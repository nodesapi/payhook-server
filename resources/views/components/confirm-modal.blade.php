<div id="confirmModal" class="hidden fixed inset-0 z-[9999] transition-all duration-300">
    <div class="fixed inset-0 transition-opacity"></div>
    
    <div class="fixed inset-0 flex items-center justify-center p-8">
        <div id="modalContent" class="relative bg-supabase-surface border border-supabase-border rounded-3xl shadow-2xl w-full max-w-lg transform transition-all scale-95 opacity-0 overflow-hidden" style="border-radius: 24px;">
            
            <button 
                type="button"
                onclick="closeConfirmModal()"
                class="absolute top-5 right-5 text-supabase-muted hover:text-white hover:bg-supabase-dark/50 p-2.5 rounded-full transition-all duration-200 hover:scale-110 z-10 group"
            >
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="p-10 text-center">
                <div class="flex justify-center mb-6">
                    <div id="modalIconContainer" class="w-20 h-20 rounded-full flex items-center justify-center relative">
                        <div id="modalIconRing" class="absolute inset-0 rounded-full animate-ping opacity-20"></div>
                        <div id="modalIcon" class="relative z-10"></div>
                    </div>
                </div>

                <div class="space-y-3 mb-8">
                    <h3 id="modalTitle" class="text-2xl font-bold text-white"></h3>
                    <p id="modalMessage" class="text-supabase-muted text-base leading-relaxed px-2"></p>
                </div>

                <div class="flex gap-3">
                    <button 
                        type="button"
                        onclick="closeConfirmModal()"
                        class="flex-1 px-6 py-3.5 text-white font-semibold bg-supabase-surface hover:bg-supabase-dark/50 border border-supabase-border rounded-xl transition-all"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button"
                        id="modalConfirmBtn"
                        class="flex-1 px-6 py-3.5 font-semibold text-white rounded-xl transition-all shadow-lg whitespace-nowrap"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="successToast" class="hidden fixed bottom-6 right-6 z-[9999] animate-bounce-in">
    <div class="bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center border border-slate-700/50">
        <div class="w-7 h-7 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mr-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <p id="successToastMessage" class="text-sm font-medium"></p>
    </div>
</div>

<style>
    @keyframes bounce-in {
        0% { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    .animate-bounce-in { animation: bounce-in 0.3s ease-out forwards; }
</style>

<script>
let modalCallback = null;

const modalConfig = {
    danger: {
        ring: 'bg-rose-500',
        container: 'bg-rose-500/10 text-rose-500',
        btn: 'bg-rose-600 hover:bg-rose-700 shadow-lg shadow-rose-600/20',
        svg: `<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`
    },
    warning: {
        ring: 'bg-amber-500',
        container: 'bg-amber-500/10 text-amber-500',
        btn: 'bg-amber-600 hover:bg-amber-700 shadow-lg shadow-amber-600/20',
        svg: `<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`
    },
    info: {
        ring: 'bg-blue-500',
        container: 'bg-blue-500/10 text-blue-500',
        btn: 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20',
        svg: `<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
    }
};

function showConfirmModal(options) {
    const modal = document.getElementById('confirmModal');
    const content = document.getElementById('modalContent');
    const iconContainer = document.getElementById('modalIconContainer');
    const iconRing = document.getElementById('modalIconRing');
    const icon = document.getElementById('modalIcon');
    const confirmBtn = document.getElementById('modalConfirmBtn');

    const type = options.type || 'warning';
    const config = modalConfig[type];

    // Update Content
    document.getElementById('modalTitle').textContent = options.title;
    document.getElementById('modalMessage').textContent = options.message;
    confirmBtn.textContent = options.confirmText || 'Confirm';
    
    // Update Styles
    iconContainer.className = `w-20 h-20 rounded-full flex items-center justify-center relative ${config.container}`;
    iconRing.className = `absolute inset-0 rounded-full animate-ping opacity-20 ${config.ring}`;
    icon.innerHTML = config.svg;
    confirmBtn.className = `flex-1 px-6 py-3 font-semibold text-white rounded-xl transition-all shadow-lg active:scale-95 whitespace-nowrap ${config.btn}`;

    modalCallback = options.onConfirm;

    // Show with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    document.body.style.overflow = 'hidden';
}

function closeConfirmModal() {
    const modal = document.getElementById('confirmModal');
    const content = document.getElementById('modalContent');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        modalCallback = null;
    }, 200);
}

// Logic initialization
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('modalConfirmBtn').onclick = () => {
        if (modalCallback) modalCallback();
        closeConfirmModal();
    };

    // Close on backdrop click
    document.getElementById('confirmModal').onclick = (e) => {
        if (e.target === e.currentTarget) closeConfirmModal();
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeConfirmModal();
    });
});

function showSuccessToast(message) {
    const toast = document.getElementById('successToast');
    document.getElementById('successToastMessage').textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}
</script>
