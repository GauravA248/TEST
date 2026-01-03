document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn =
        document.getElementById('logoutBtn') ||
        document.getElementById('logoutUser');

    if (!logoutBtn) {
        console.warn('⚠️ Logout button not found on this page.');
        return;
    }

    logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();

        if (typeof Swal === 'undefined') {
            // fallback (no SweetAlert)
            window.location.href = "/TEST/logout.php";
            return;
        }

        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout'
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 900,
                    didClose: () => {
                        // 🔑 MONOLITHIC LOGOUT
                        window.location.href = "/TEST/logout.php";
                    }
                });

            }
        });
    });
});
