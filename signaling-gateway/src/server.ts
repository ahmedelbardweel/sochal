import { WebSocketServer, WebSocket } from 'ws';
import * as jwt from 'jsonwebtoken';
import { IncomingMessage } from 'http';
import * as dotenv from 'dotenv';
import * as path from 'path';

// Load .env from root directory
dotenv.config({ path: path.resolve(__dirname, '../../.env') });

const PORT = process.env.PORT || 8080;
const wss = new WebSocketServer({ port: Number(PORT), host: '0.0.0.0' });

const APP_KEY = process.env.APP_KEY || 'default_key';

interface Peer {
    ws: WebSocket;
    userId: string;
    liveId: string;
    role: 'host' | 'audience' | 'moderator';
}

const rooms = new Map<string, Set<Peer>>();

function broadcastPopulation(liveId: string) {
    const room = rooms.get(String(liveId));
    if (!room) return;

    let total = room.size;
    let audienceCount = 0;
    let roles: string[] = [];

    room.forEach(p => {
        roles.push(p.role);
        // Relaxed counting: Anyone who is NOT the host is audience
        if (p.role !== 'host') audienceCount++;
    });

    console.log(`[ROOM ${liveId}] Sync: ${audienceCount} viewers. Raw Roles: [${roles.join(', ')}]`);

    const msg = JSON.stringify({
        type: 'ROOM_POPULATION',
        payload: { liveId: String(liveId), count: audienceCount, total: total }
    });

    console.log(`[ROOM ${liveId}] Broadcasting to ${room.size} peers. (Audience: ${audienceCount})`);

    room.forEach(p => {
        try {
            if (p.ws.readyState === WebSocket.OPEN) {
                p.ws.send(msg, (err) => {
                    if (err) console.error(`[SEND ERROR] Failed to send auth to ${p.userId}:`, err);
                });
            } else {
                console.warn(`[SKIP] User ${p.userId} socket state: ${p.ws.readyState}`);
            }
        } catch (err) {
            console.error(`[BROADCAST ERR] User ${p.userId}:`, err);
        }
    });
}

// Global heartbeat to keep everyone synced
setInterval(() => {
    rooms.forEach((_, liveId) => broadcastPopulation(liveId));
}, 3000);

console.log(`Signaling Gateway started on port ${PORT}`);

wss.on('connection', (ws: WebSocket, req: IncomingMessage) => {
    let peer: Peer | null = null;

    ws.on('message', (message: string) => {
        try {
            const data = JSON.parse(message);
            const { type, payload } = data;

            switch (type) {
                case 'AUTH':
                    handleAuth(ws, payload);
                    break;
                case 'CHAT_MESSAGE':
                case 'REACTION':
                case 'ICE_CANDIDATE':
                case 'SDP_OFFER':
                case 'SDP_ANSWER':
                    handleRelay(ws, type, payload);
                    break;
                case 'HOST_END_LIVE':
                    handleHostEnd(ws);
                    break;
                case 'PING':
                    ws.send(JSON.stringify({ type: 'PONG', payload: { t: Date.now() } }));
                    break;
                case 'TRIGGER_POPULATION_REFRESH':
                    if (peer) broadcastPopulation(peer.liveId);
                    break;
                default:
                    console.warn(`[UNKNOWN] ${type}`, payload);
            }
        } catch (e) {
            console.error('Error handling message:', e);
        }
    });

    function handleAuth(ws: WebSocket, payload: any) {
        const { token } = payload;
        try {
            const decoded = jwt.verify(token, APP_KEY) as any;
            let { liveId, userId, role } = decoded;

            // Strict normalization
            liveId = String(liveId).trim();
            userId = String(userId).trim();
            role = String(role || 'audience').trim().toLowerCase();

            peer = { ws, userId, liveId, role };

            if (!rooms.has(peer.liveId)) {
                rooms.set(peer.liveId, new Set());
            }
            rooms.get(peer.liveId)!.add(peer);

            ws.send(JSON.stringify({ type: 'AUTH_OK', payload: { userId, role, roomSize: rooms.get(peer.liveId)!.size } }));
            console.log(`[JOIN] Room ${liveId} | User ${userId} | ${role}`);

            setTimeout(() => {
                if (peer) broadcastPopulation(peer.liveId);
            }, 500);
        } catch (e) {
            console.error('[AUTH FAIL]', e);
            ws.send(JSON.stringify({ type: 'ERROR', payload: { code: 'AUTH_FAILED', message: 'Invalid token' } }));
            ws.close();
        }
    }

    function handleHostEnd(ws: WebSocket) {
        if (!peer || peer.role !== 'host') return;

        const room = rooms.get(peer.liveId);
        if (room) {
            console.log(`[END] Host ${peer.userId} ended live ${peer.liveId}.`);
            room.forEach((p) => {
                if (p.ws !== ws) {
                    p.ws.send(JSON.stringify({ type: 'LIVE_ENDED', payload: { liveId: peer!.liveId } }));
                }
            });
        }
    }

    function handleRelay(ws: WebSocket, type: string, payload: any) {
        if (!peer) return;

        const { liveId, targetId } = payload;
        const targetRoomId = String(liveId || peer.liveId);
        const room = rooms.get(targetRoomId);

        if (!room) return;

        console.log(`[RELAY] ${type} in ${targetRoomId} from ${peer.userId}`);

        if (targetId) {
            room.forEach((p) => {
                if (p.userId === String(targetId)) {
                    p.ws.send(JSON.stringify({ type, payload: { ...payload, fromId: peer!.userId } }));
                }
            });
        } else {
            room.forEach((p) => {
                if (p.ws !== ws) {
                    p.ws.send(JSON.stringify({ type, payload: { ...payload, fromId: peer!.userId } }));
                }
            });
        }
    }

    ws.on('close', () => {
        if (peer) {
            const room = rooms.get(peer.liveId);
            if (room) {
                if (peer.role === 'host') {
                    room.forEach((p) => {
                        if (p.ws !== ws) {
                            p.ws.send(JSON.stringify({ type: 'LIVE_ENDED', payload: { liveId: peer!.liveId } }));
                        }
                    });
                }

                room.delete(peer);
                if (room.size === 0) {
                    rooms.delete(peer.liveId);
                } else {
                    broadcastPopulation(peer.liveId);
                }
            }
            console.log(`[LEAVE] User ${peer.userId} disconnected`);
        }
    });
});
