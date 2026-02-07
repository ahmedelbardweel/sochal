
// --- POST ACTIONS (Edit, Delete, Hide, Report) ---

// Ensure we have a global post registry if not already defined
if (typeof window.feedPosts === 'undefined') {
    window.feedPosts = {}; // Maps postId -> postObject
}

window.togglePostMenu = function (postId) {
    const menu = document.getElementById(`menu-${postId}`);
    const allMenus = document.querySelectorAll('[id^="menu-"]');

    allMenus.forEach(m => {
        if (m.id !== `menu-${postId}`) m.classList.add('hidden');
    });

    if (menu) {
        menu.classList.toggle('hidden');

        // Auto-close on click outside
        if (!menu.classList.contains('hidden')) {
            const closeHandler = (e) => {
                const btn = e.target.closest(`button[onclick*="togglePostMenu(${postId})"]`);
                if (!menu.contains(e.target) && !btn) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', closeHandler);
                }
            };
            setTimeout(() => document.addEventListener('click', closeHandler), 10);
        }
    } else {
        console.warn(`Menu #menu-${postId} not found`);
    }
};

window.openEditPostModal = function (postId) {
    const post = window.feedPosts[postId];
    if (!post) {
        console.error('Post data not found for ID:', postId);
        return;
    }

    const modal = document.getElementById('editPostModal');
    if (!modal) return;

    document.getElementById('editPostId').value = postId;
    document.getElementById('editPostCaption').value = post.caption || '';
    document.getElementById('editPostPrivacy').value = post.privacy || 'public';

    window.toggleModal('editPostModal', true);

    // Close menu
    document.getElementById(`menu-${postId}`)?.classList.add('hidden');
};

window.closeEditPostModal = function () {
    window.toggleModal('editPostModal', false);
};

window.saveEditedPost = async function () {
    const postId = document.getElementById('editPostId').value;
    const caption = document.getElementById('editPostCaption').value;
    const privacy = document.getElementById('editPostPrivacy').value;

    const btn = document.querySelector('#editPostModal button[onclick="saveEditedPost()"]');
    const originalText = btn ? btn.innerText : 'Save';
    if (btn) {
        btn.innerText = 'Saving...';
        btn.disabled = true;
    }

    try {
        await window.bridge.request(`/posts/${postId}`, {
            method: 'PATCH',
            body: JSON.stringify({ caption, privacy })
        });

        window.toast('Post updated successfully', 'success');
        closeEditPostModal();

        // Refresh Feed or Profile if available
        if (typeof window.loadFeed === 'function') window.loadFeed(true);
        if (typeof window.loadUserPosts === 'function' && window.currentUser) window.loadUserPosts(window.currentUser.id);

    } catch (err) {
        console.error(err);
        window.toast('Failed to update post', 'error');
    } finally {
        if (btn) {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }
};

window.togglePostVisibility = async function (postId, currentStatus) {
    // Toggle Hide/Unhide
    const action = currentStatus === 'hidden' ? 'unhide' : 'hide';
    try {
        await window.bridge.request(`/posts/${postId}/${action}`, { method: 'POST' });
        window.toast(`Post ${action === 'hide' ? 'hidden' : 'visible'}`, 'success');

        // Refresh Feed or Profile
        if (typeof window.loadFeed === 'function') window.loadFeed(true);
        if (typeof window.loadUserPosts === 'function' && window.currentUser) window.loadUserPosts(window.currentUser.id);

    } catch (err) {
        console.error(err);
        window.toast('Action failed', 'error');
    }
};

window.hidePostClient = function (postId) {
    const card = document.getElementById(`post-${postId}`);
    if (card) {
        card.style.transition = 'all 0.5s ease';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';
        setTimeout(() => card.remove(), 500);
    }
    window.toast('Post hidden for this session', 'info');
};

window.deletePost = async function (postId) {
    if (!confirm('Are you sure you want to delete this post? This cannot be undone.')) return;

    try {
        await window.bridge.request(`/posts/${postId}`, { method: 'DELETE' });
        window.toast('Post deleted', 'success');

        // Remove from UI immediately
        const card = document.getElementById(`post-${postId}`);
        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
        }

    } catch (err) {
        console.error(err);
        window.toast('Failed to delete post', 'error');
    }
};

window.openReportModal = function (targetId, type) {
    const modal = document.getElementById('reportModal');
    if (!modal) return;

    document.getElementById('reportTargetId').value = targetId;
    document.getElementById('reportTargetType').value = type;

    // Reset form
    document.querySelectorAll('input[name="reportReason"]').forEach(el => el.checked = false);
    document.getElementById('reportDetails').value = '';
    document.getElementById('reportDetails').classList.add('hidden');

    window.toggleModal('reportModal', true);

    // Close menu if post
    if (type === 'post') document.getElementById(`menu-${targetId}`)?.classList.add('hidden');
};

window.closeReportModal = function () {
    window.toggleModal('reportModal', false);
};

// Listen for "Other" reason to toggle details input
document.addEventListener('change', (e) => {
    if (e.target.name === 'reportReason') {
        const details = document.getElementById('reportDetails');
        if (e.target.value === 'other') {
            details.classList.remove('hidden');
        } else {
            details.classList.add('hidden');
        }
    }
});

window.submitReport = async function () {
    const targetId = document.getElementById('reportTargetId').value;
    const type = document.getElementById('reportTargetType').value;
    const reasonRadio = document.querySelector('input[name="reportReason"]:checked');
    const details = document.getElementById('reportDetails').value;

    if (!reasonRadio) {
        window.toast('Please select a reason', 'error');
        return;
    }

    const btn = document.querySelector('#reportModal button[onclick="submitReport()"]');
    const originalText = btn ? btn.innerText : 'Submit';
    if (btn) {
        btn.innerText = 'Submitting...';
        btn.disabled = true;
    }

    try {
        await window.bridge.request(`/report`, {
            method: 'POST',
            body: JSON.stringify({
                type: type,
                target_id: parseInt(targetId),
                reason: reasonRadio.value,
                details: details || null
            })
        });

        window.toast('Report submitted. Thank you for making our community safer.', 'success');
        closeReportModal();

        if (type === 'post') window.hidePostClient(targetId); // Auto-hide reported content

    } catch (err) {
        console.error('Report submission failed:', err);
        window.toast(`Failed to submit report: ${err.message || 'Unknown error'}`, 'error');
    } finally {
        if (btn) {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }
};
