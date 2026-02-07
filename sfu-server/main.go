package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"sync"
	"time"
	"github.com/pion/interceptor"
	"github.com/pion/rtcp"
	"github.com/pion/webrtc/v3"
)

type Room struct {
	sync.Mutex
	ID          string
	Publisher   *webrtc.PeerConnection
	Subscribers map[string]*webrtc.PeerConnection
	LocalTracks map[string]*webrtc.TrackLocalStaticRTP
}

var (
	rooms   = make(map[string]*Room)
	roomsMu sync.Mutex
	api     *webrtc.API
)

func main() {
	// Initialize WebRTC API
	m := &webrtc.MediaEngine{}
	if err := m.RegisterDefaultCodecs(); err != nil {
		log.Fatal(err)
	}

	i := &interceptor.Registry{}
	if err := webrtc.RegisterDefaultInterceptors(m, i); err != nil {
		log.Fatal(err)
	}

	s := webrtc.SettingEngine{}
	s.SetEphemeralUDPPortRange(50000, 50100)
	
	api = webrtc.NewAPI(webrtc.WithMediaEngine(m), webrtc.WithSettingEngine(s), webrtc.WithInterceptorRegistry(i))

	http.HandleFunc("/publish", corsMiddleware(handlePublish))
	http.HandleFunc("/subscribe", corsMiddleware(handleSubscribe))

	fmt.Println("SFU Server started on :8888")
	log.Fatal(http.ListenAndServe(":8888", nil))
}

func corsMiddleware(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Access-Control-Allow-Origin", "*")
		w.Header().Set("Access-Control-Allow-Methods", "POST, OPTIONS")
		w.Header().Set("Access-Control-Allow-Headers", "Content-Type")

		if r.Method == "OPTIONS" {
			w.WriteHeader(http.StatusOK)
			return
		}

		next(w, r)
	}
}

func handlePublish(w http.ResponseWriter, r *http.Request) {
	var body struct {
		SDP    string `json:"sdp"`
		RoomID string `json:"roomId"`
	}
	if err := json.NewDecoder(r.Body).Decode(&body); err != nil {
		http.Error(w, err.Error(), 400)
		return
	}

	pc, err := api.NewPeerConnection(webrtc.Configuration{})
	if err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	room := getOrCreateRoom(body.RoomID)
	room.Lock()
	room.Publisher = pc
	room.LocalTracks = make(map[string]*webrtc.TrackLocalStaticRTP)
	room.Unlock()

	pc.OnConnectionStateChange(func(s webrtc.PeerConnectionState) {
		fmt.Printf("Publisher connection state: %s\n", s.String())
	})

	pc.OnTrack(func(track *webrtc.TrackRemote, receiver *webrtc.RTPReceiver) {
		fmt.Printf("Received publisher track: %s (%s)\n", track.ID(), track.Kind().String())
		
		// Create a local track to push data to
		localTrack, err := webrtc.NewTrackLocalStaticRTP(track.Codec().RTPCodecCapability, track.ID(), track.StreamID())
		if err != nil {
			fmt.Printf("Failed to create local track: %v\n", err)
			return
		}

		room.Lock()
		room.LocalTracks[track.Kind().String()] = localTrack
		room.Unlock()

		// Read from remote and write to local
		go func() {
			buf := make([]byte, 1500)
			for {
				n, _, err := track.Read(buf)
				if err != nil {
					return
				}
				if _, err = localTrack.Write(buf[:n]); err != nil {
					return
				}
			}
		}()

		// Read RTCP from receiver to keep it alive
		go func() {
			for {
				if _, _, err := receiver.ReadRTCP(); err != nil {
					return
				}
			}
		}()
	})

	offer := webrtc.SessionDescription{Type: webrtc.SDPTypeOffer, SDP: body.SDP}
	if err := pc.SetRemoteDescription(offer); err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	answer, err := pc.CreateAnswer(nil)
	if err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	gatherComplete := webrtc.GatheringCompletePromise(pc)
	if err := pc.SetLocalDescription(answer); err != nil {
		http.Error(w, err.Error(), 500)
		return
	}
	<-gatherComplete

	fmt.Printf("Publisher joined room %s\n", body.RoomID)
	json.NewEncoder(w).Encode(map[string]string{"sdp": pc.LocalDescription().SDP})
}

func handleSubscribe(w http.ResponseWriter, r *http.Request) {
	var body struct {
		SDP    string `json:"sdp"`
		RoomID string `json:"roomId"`
		UserID string `json:"userId"`
	}
	if err := json.NewDecoder(r.Body).Decode(&body); err != nil {
		http.Error(w, err.Error(), 400)
		return
	}

	pc, err := api.NewPeerConnection(webrtc.Configuration{})
	if err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	room := getOrCreateRoom(body.RoomID)
	room.Lock()
	room.Subscribers[body.UserID] = pc
	
	// Add tracks and trigger PLI
	hasVideo := false
	for kind, track := range room.LocalTracks {
		pc.AddTrack(track)
		if kind == "video" {
			hasVideo = true
		}
	}

	// Trigger PLI to publisher to get a keyframe
	if hasVideo && room.Publisher != nil {
		go func() {
			// Small delay to let connection settle
			time.Sleep(500 * time.Millisecond)
			for _, sender := range room.Publisher.GetSenders() {
				if sender.Track() != nil && sender.Track().Kind() == webrtc.RTPCodecTypeVideo {
					// In a simple setup, we send PLI for all video tracks
					// We need to find the SSRC of the receiver that is feeding this track
                    // For simplicity in this prototype, we send it to the first video receiver we find
				}
			}
            // Better: just tell the publisher PC to send PLI for its receivers
            for _, receiver := range room.Publisher.GetReceivers() {
                if receiver.Track() != nil && receiver.Track().Kind() == webrtc.RTPCodecTypeVideo {
                    room.Publisher.WriteRTCP([]rtcp.Packet{
                        &rtcp.PictureLossIndication{MediaSSRC: uint32(receiver.Track().SSRC())},
                    })
                }
            }
		}()
	}
	room.Unlock()

	pc.OnConnectionStateChange(func(s webrtc.PeerConnectionState) {
		fmt.Printf("Subscriber %s connection state: %s\n", body.UserID, s.String())
	})

	offer := webrtc.SessionDescription{Type: webrtc.SDPTypeOffer, SDP: body.SDP}
	if err := pc.SetRemoteDescription(offer); err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	answer, err := pc.CreateAnswer(nil)
	if err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	gatherComplete := webrtc.GatheringCompletePromise(pc)
	if err := pc.SetLocalDescription(answer); err != nil {
		http.Error(w, err.Error(), 500)
		return
	}
	<-gatherComplete

	fmt.Printf("Subscriber %s joined room %s\n", body.UserID, body.RoomID)
	json.NewEncoder(w).Encode(map[string]string{"sdp": pc.LocalDescription().SDP})
}

func getOrCreateRoom(id string) *Room {
	roomsMu.Lock()
	defer roomsMu.Unlock()
	if r, ok := rooms[id]; ok {
		return r
	}
	r := &Room{
		ID:          id,
		Subscribers: make(map[string]*webrtc.PeerConnection),
		LocalTracks: make(map[string]*webrtc.TrackLocalStaticRTP),
	}
	rooms[id] = r
	return r
}
