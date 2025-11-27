document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('sidebarToggle');
    const menuItems = document.querySelectorAll('.menu-item');
    const sections = document.querySelectorAll('.content-section');

    // Modal elements
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const modalTitle = document.getElementById('modalTitle');

    // Toggle Sidebar
    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });

    // Navigation Logic
    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default anchor behavior

            const targetId = this.getAttribute('data-target');

            // Update Menu Active State
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Show Target Section
            sections.forEach(section => {
                if (section.id === targetId) {
                    section.classList.remove('hidden');
                    section.classList.add('active');
                } else {
                    section.classList.add('hidden');
                    section.classList.remove('active');
                }
            });
        });
    });

    // Modal Logic
    // Open Modal (Delegation for dynamic elements if needed, but direct for now)
    const openModalBtns = document.querySelectorAll('.btn-primary, .btn-icon.edit');

    // We use event delegation for buttons inside tables since they might be dynamic later
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.btn-primary') || e.target.closest('.btn-icon.edit');

        if (target) {
            // Determine if it's Add or Edit
            if (target.classList.contains('btn-primary')) {
                modalTitle.textContent = "Thêm mới";
            } else {
                modalTitle.textContent = "Chỉnh sửa";
            }

            modalOverlay.classList.remove('hidden');
        }
    });

    // Close Modal
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            modalOverlay.classList.add('hidden');
        });
    });

    // Close on click outside
    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) {
            modalOverlay.classList.add('hidden');
        }
    });
});
