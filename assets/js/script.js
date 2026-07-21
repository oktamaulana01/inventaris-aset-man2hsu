// ============================================
// JavaScript - Sistem Inventarisasi Aset
// MAN 2 Hulu Sungai Utara
// ============================================

// Sidebar Toggle (Mobile & Desktop)
function toggleSidebar() {
    const appLayout = document.querySelector('.app-layout');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active');
    } else {
        if (appLayout) {
            appLayout.classList.toggle('sidebar-collapsed');
            const isCollapsed = appLayout.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed ? 'true' : 'false');
        }
    }
}

// Restore state & close sidebar when clicking overlay
document.addEventListener('DOMContentLoaded', function() {
    // Restore state desktop collapsed sidebar
    if (window.innerWidth > 768) {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            document.querySelector('.app-layout')?.classList.add('sidebar-collapsed');
        }
    }

    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            toggleSidebar();
        });
    }
    
    // ── Restore posisi scroll sidebar ──
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        const savedScroll = sessionStorage.getItem('sidebarScrollPos');
        if (savedScroll !== null) {
            sidebar.scrollTop = parseInt(savedScroll, 10);
        }

        // Simpan posisi scroll saat klik link sidebar
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });
        });
    }

    // Auto-hide alerts after 4 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // Animate numbers on stat cards
    animateCounters();
});

// Number counter animation
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        if (isNaN(target)) return;
        const duration = 1000;
        const step = target / (duration / 16);
        let current = 0;
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target.toLocaleString('id-ID');
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current).toLocaleString('id-ID');
            }
        }, 16);
    });
}

// Confirm Delete — menggunakan POST method dengan CSRF token
function confirmDelete(message, actionUrl, itemId) {
    if (confirm(message || 'Apakah Anda yakin ingin menghapus data ini?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;
        form.style.display = 'none';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = itemId;
        form.appendChild(idInput);

        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'csrf_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }

        document.body.appendChild(form);
        form.submit();
    }
    return false;
}

// Confirm Action — POST method untuk aksi non-hapus (kembali, dll)
function confirmAction(message, actionUrl, itemId) {
    if (confirm(message || 'Apakah Anda yakin?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;
        form.style.display = 'none';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = itemId;
        form.appendChild(idInput);

        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'csrf_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }

        document.body.appendChild(form);
        form.submit();
    }
    return false;
}

// Image Preview on File Input
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Print QR Code
function printQRCode(kodeAset) {
    const printContent = document.getElementById('qr-print-area');
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Code - ${kodeAset}</title>
            <style>
                body { text-align: center; font-family: Arial, sans-serif; padding: 20px; }
                img { max-width: 300px; }
                h3 { margin-top: 10px; }
                @media print {
                    body { padding: 0; }
                }
            </style>
        </head>
        <body>
            ${printContent.innerHTML}
            <script>window.print(); window.close();<\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// Format Rupiah in inputs
function formatRupiahInput(el) {
    let val = el.value.replace(/[^0-9]/g, '');
    el.value = new Intl.NumberFormat('id-ID').format(val);
}

// Search / Filter table
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let match = false;
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().includes(filter)) {
                match = true;
                break;
            }
        }
        rows[i].style.display = match ? '' : 'none';
    }
}
