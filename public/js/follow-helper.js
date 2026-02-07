/**
 * Global Follow Button Helper
 * Handles 3 states: Follow, Requested, Following
 */

// Get button state based on user data
function getFollowButtonState(user) {
    if (user.is_following) {
        return {
            text: 'Following',
            classes: 'bg-bg-tertiary text-text-primary border border-border-light',
            linkClasses: 'text-white/40',
            action: 'unfollow'
        };
    } else if (user.has_pending_request || user.follow_status === 'pending') {
        return {
            text: 'Requested',
            classes: 'bg-yellow-500/10 text-yellow-600 border border-yellow-500/30',
            linkClasses: 'text-yellow-500/60',
            action: 'cancel'
        };
    } else {
        return {
            text: 'Follow',
            classes: 'bg-primary-500 text-white shadow-lg shadow-primary-500/20',
            linkClasses: 'text-primary-500',
            action: 'follow'
        };
    }
}

// Universal follow toggle function
async function toggleFollowUniversal(userId, btn, onSuccess, mode = 'button') {
    try {
        const currentText = btn.innerText.trim().toUpperCase();
        let endpoint = '';
        let newState = {};

        // Detect mode if not explicitly passed
        if (mode === 'button' && (btn.classList.contains('text-primary-500') || btn.classList.contains('text-[11px]'))) {
            mode = 'link';
        }

        if (currentText === 'FOLLOWING' || currentText === 'FOLLOWING') {
            endpoint = `/users/${userId}/unfollow`;
            newState = {
                text: 'Follow',
                classes: mode === 'link' ? 'text-primary-500' : 'bg-primary-500 text-white shadow-lg shadow-primary-500/20'
            };
        } else if (currentText === 'REQUESTED') {
            endpoint = `/users/${userId}/unfollow`;
            newState = {
                text: 'Follow',
                classes: mode === 'link' ? 'text-primary-500' : 'bg-primary-500 text-white shadow-lg shadow-primary-500/20'
            };
        } else {
            endpoint = `/users/${userId}/follow`;
            btn.disabled = true;
            btn.innerText = '...';
        }

        const response = await window.bridge.request(endpoint, { method: 'POST' });

        if (response.data && response.data.status) {
            if (response.data.status === 'pending') {
                btn.innerText = 'Requested';
                if (mode === 'link') {
                    btn.className = btn.className.replace(/text-\S+/g, '') + ' text-yellow-500/60';
                } else {
                    btn.className = btn.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '') + ' bg-yellow-500/10 text-yellow-600 border border-yellow-500/30';
                }
                window.toast?.('Follow request sent! 📩', 'success');
            } else if (response.data.status === 'accepted') {
                btn.innerText = 'Following';
                if (mode === 'link') {
                    btn.className = btn.className.replace(/text-\S+/g, '') + ' text-white/40';
                } else {
                    btn.className = btn.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '').replace(/shadow-\S+/g, '') + ' bg-bg-tertiary text-text-primary border border-border-light';
                }
                window.toast?.('Following! ✅', 'success');
            } else {
                btn.innerText = 'Follow';
                if (mode === 'link') {
                    btn.className = btn.className.replace(/text-\S+/g, '') + ' text-primary-500';
                } else {
                    btn.className = btn.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '').replace(/shadow-\S+/g, '') + ' bg-primary-500 text-white';
                }
            }
        } else {
            btn.innerText = newState.text;
            if (mode === 'link') {
                btn.className = btn.className.replace(/text-\S+/g, '') + ' ' + newState.classes;
            } else {
                btn.className = btn.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '').replace(/shadow-\S+/g, '') + ' ' + newState.classes;
            }
        }

        btn.disabled = false;

        if (onSuccess) onSuccess(response);

        // Broadcast follow state change globally so all pages can update
        const followStateEvent = new CustomEvent('followStateChanged', {
            detail: {
                userId: userId,
                status: response.data?.status || (currentText === 'FOLLOWING' ? 'unfollowed' : 'following'),
                isFollowing: response.data?.status === 'accepted',
                isPending: response.data?.status === 'pending',
                buttonText: btn.innerText,
                buttonClasses: btn.className
            }
        });
        window.dispatchEvent(followStateEvent);

        // Refresh follow requests badge
        if (window.refreshFollowRequestsBadge) {
            window.refreshFollowRequestsBadge();
        }

    } catch (err) {
        console.error('Follow toggle error:', err);
        btn.disabled = false;
        btn.innerText = currentText;
        window.toast?.('Action failed', 'error');
    }
}
