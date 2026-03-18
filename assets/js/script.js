// ============================================
// JavaScript - Sistem Inventarisasi Aset
// MAN 2 Hulu Sungai Utara
// ============================================

// Sidebar Toggle (Mobile)
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('active');
}

// Close sidebar when clicking overlay
document.addEventListener('DOMContentLoaded', function() {
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

// Confirm Delete
function confirmDelete(message, url) {
    if (confirm(message || 'Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = url;
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
