</main><!-- /.main-content -->
</div><!-- /.admin-container -->

<!-- Toast Notification -->
<div id="toast">
    <i class="fas fa-circle-check" id="toastIcon"></i>
    <span id="toastMsg">Action completed.</span>
</div>

<!-- Global JS -->
<script>
// ── Page loader ──────────────────────────────────
window.addEventListener('load', () => {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => loader.style.display = 'none', 300);
    }
});

// ── Sidebar toggle (mobile) ───────────────────────
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
}

function closeSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// ── Toast utility ─────────────────────────────────
function showToast(message, type = 'default') {
    const toast    = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    const toastIco = document.getElementById('toastIcon');

    toastMsg.textContent = message;
    toast.className = '';   // reset classes
    if (type === 'success') {
        toast.classList.add('show', 'toast-success');
        toastIco.className = 'fas fa-circle-check';
    } else if (type === 'error') {
        toast.classList.add('show', 'toast-error');
        toastIco.className = 'fas fa-circle-xmark';
    } else {
        toast.classList.add('show');
        toastIco.className = 'fas fa-circle-info';
    }

    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => { toast.classList.remove('show'); }, 3500);
}

// ── Chart.js global defaults ──────────────────────
if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748b';
    Chart.defaults.plugins.legend.labels.boxWidth  = 12;
    Chart.defaults.plugins.legend.labels.padding   = 16;
    Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
    Chart.defaults.plugins.tooltip.padding         = 10;
    Chart.defaults.plugins.tooltip.cornerRadius    = 8;
    Chart.defaults.plugins.tooltip.titleFont       = { weight: '700' };
    Chart.defaults.scale.grid.color                = 'rgba(0,0,0,0.05)';
    Chart.defaults.scale.border.display            = false;
}
</script>

</body>
</html>
