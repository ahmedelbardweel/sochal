<!-- EDIT POST MODAL -->
<div id="editPostModal" class="fixed inset-0 z-[105] bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-bg-secondary rounded-3xl w-full max-w-lg shadow-2xl border border-border-light anim-scale-in">
        <div class="flex items-center justify-between p-4 border-b border-border-light">
            <h3 class="text-lg font-bold text-text-primary">Edit Post</h3>
            <button onclick="closeEditPostModal()" class="p-2 hover:bg-bg-tertiary rounded-full transition-colors text-text-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 space-y-4">
            <input type="hidden" id="editPostId">
            <div>
                <label class="block text-text-secondary text-xs font-bold uppercase mb-2">Caption</label>
                <textarea id="editPostCaption" rows="4" class="w-full bg-bg-tertiary border border-border-light rounded-xl px-4 py-3 text-text-primary placeholder-text-tertiary focus:border-primary-500 outline-none transition-colors resize-none"></textarea>
            </div>
            <div>
                <label class="block text-text-secondary text-xs font-bold uppercase mb-2">Privacy</label>
                <select id="editPostPrivacy" class="w-full bg-bg-tertiary border border-border-light rounded-xl px-4 py-3 text-text-primary focus:border-primary-500 outline-none transition-colors appearance-none">
                    <option value="public">Public - Visible to everyone</option>
                    <option value="followers">Followers - Only your network</option>
                    <option value="private">Private - Only you</option>
                </select>
            </div>
        </div>
        <div class="p-4 border-t border-border-light flex justify-end space-x-3">
            <button onclick="closeEditPostModal()" class="px-5 py-2 rounded-xl font-bold text-text-secondary hover:bg-bg-tertiary transition-colors">Cancel</button>
            <button onclick="saveEditedPost()" class="px-5 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl font-bold transition-transform active:scale-95 shadow-lg shadow-primary-500/20">Save Changes</button>
        </div>
    </div>
</div>

<!-- REPORT MODAL -->
<div id="reportModal" class="fixed inset-0 z-[105] bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-bg-secondary rounded-3xl w-full max-w-md shadow-2xl border border-border-light anim-scale-in">
        <div class="flex items-center justify-between p-4 border-b border-border-light">
            <h3 class="text-lg font-bold text-red-500 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Report Content</span>
            </h3>
            <button onclick="closeReportModal()" class="p-2 hover:bg-bg-tertiary rounded-full transition-colors text-text-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 space-y-4">
            <input type="hidden" id="reportTargetId">
            <input type="hidden" id="reportTargetType">
            
            <p class="text-text-secondary text-sm">Help us keep the neural network safe. Why are you reporting this?</p>
            
            <div class="space-y-2">
                <label class="flex items-center space-x-3 p-3 rounded-xl border border-border-light hover:bg-bg-tertiary cursor-pointer transition-colors">
                    <input type="radio" name="reportReason" value="spam" class="text-primary-500 focus:ring-primary-500">
                    <span class="text-text-primary text-sm font-bold">Spam or Misleading</span>
                </label>
                <label class="flex items-center space-x-3 p-3 rounded-xl border border-border-light hover:bg-bg-tertiary cursor-pointer transition-colors">
                    <input type="radio" name="reportReason" value="harassment" class="text-primary-500 focus:ring-primary-500">
                    <span class="text-text-primary text-sm font-bold">Harassment or Hate Speech</span>
                </label>
                <label class="flex items-center space-x-3 p-3 rounded-xl border border-border-light hover:bg-bg-tertiary cursor-pointer transition-colors">
                    <input type="radio" name="reportReason" value="inappropriate" class="text-primary-500 focus:ring-primary-500">
                    <span class="text-text-primary text-sm font-bold">Inappropriate Content</span>
                </label>
                <label class="flex items-center space-x-3 p-3 rounded-xl border border-border-light hover:bg-bg-tertiary cursor-pointer transition-colors">
                    <input type="radio" name="reportReason" value="other" class="text-primary-500 focus:ring-primary-500">
                    <span class="text-text-primary text-sm font-bold">Other</span>
                </label>
            </div>
            
            <textarea id="reportDetails" rows="3" placeholder="Additional details (optional)..." class="w-full bg-bg-tertiary border border-border-light rounded-xl px-4 py-3 text-text-primary placeholder-text-tertiary focus:border-primary-500 outline-none transition-colors resize-none hidden"></textarea>
        </div>
        <div class="p-4 border-t border-border-light grid grid-cols-2 gap-3">
             <button onclick="closeReportModal()" class="px-4 py-2 rounded-xl font-bold text-text-secondary hover:bg-bg-tertiary transition-colors">Cancel</button>
             <button onclick="submitReport()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition-transform active:scale-95 shadow-lg shadow-red-500/20">Submit Report</button>
        </div>
    </div>
</div>
