// === Global Page Loader for Both Admin & User Dashboards ===
// document.addEventListener("DOMContentLoaded", () => {
//     const allLinks = document.querySelectorAll('#sidebar .side-menu.top li a');
//     const pageContent = document.getElementById("pageContent");
//     const dashboardContent = document.getElementById("dashboardContent");

//     if (!pageContent || !dashboardContent) {
//         console.error("Required content containers not found!");
//         return;
//     }

//     allLinks.forEach(link => {
//         link.addEventListener("click", (e) => {
//             e.preventDefault();
//             const page = link.getAttribute("data-page");

//             // Remove active class from all menu items
//             allLinks.forEach(i => i.parentElement.classList.remove("active"));
//             link.parentElement.classList.add("active");

//             // --- Handle Admin Dashboard ---
//             if (window.location.pathname.includes("dashboard.html") && page === "dashboard.html") {
//                 dashboardContent.style.display = "block";
//                 pageContent.style.display = "none";
//                 pageContent.innerHTML = "";
//                 return;
//             }

//             // --- Handle User Dashboard ---
//             if (window.location.pathname.includes("userDash.html") && page === "userDash.html") {
//                 dashboardContent.style.display = "block";
//                 pageContent.style.display = "none";
//                 pageContent.innerHTML = "";
//                 return;
//             }

//             // --- Load any other page dynamically ---
//             dashboardContent.style.display = "none";
//             pageContent.style.display = "block";
//             pageContent.innerHTML = `<p style="text-align:center;padding:30px;">Loading...</p>`;

//             fetch(page)
//                 .then(res => {
//                     if (!res.ok) throw new Error(`Page not found: ${page}`);
//                     return res.text();
//                 })
//                 .then(html => {
//                     pageContent.innerHTML = html;
//                 })
//                 .catch(err => {
//                     console.error(err);
//                     pageContent.innerHTML = `<p style="color:red;text-align:center;">Failed to load page: ${page}</p>`;
//                 });
//         });
//     });
// });

// === Global Page Loader for Both Admin & User Dashboards ===
document.addEventListener("DOMContentLoaded", () => {
    const allLinks = document.querySelectorAll('#sidebar .side-menu.top li a');
    const pageContent = document.getElementById("pageContent");
    const dashboardContent = document.getElementById("dashboardContent");

    if (!pageContent || !dashboardContent) {
        console.error("Required content containers not found!");
        return;
    }

    allLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            const page = link.getAttribute("data-page");

            // Remove active class
            allLinks.forEach(i => i.parentElement.classList.remove("active"));
            link.parentElement.classList.add("active");

            // Admin dashboard
            if (window.location.pathname.includes("dashboard.html") && page === "dashboard.html") {
                dashboardContent.style.display = "block";
                pageContent.style.display = "none";
                pageContent.innerHTML = "";
                return;
            }

            // User dashboard
            if (window.location.pathname.includes("userDash.html") && page === "userDash.html") {
                dashboardContent.style.display = "block";
                pageContent.style.display = "none";
                pageContent.innerHTML = "";
                return;
            }

            // Load other pages
            dashboardContent.style.display = "none";
            pageContent.style.display = "block";
            pageContent.innerHTML = `<p style="text-align:center;padding:30px;">Loading...</p>`;

            fetch(page)
                .then(res => {
                    if (!res.ok) throw new Error(`Page not found: ${page}`);
                    return res.text();
                })
                .then(html => {
                    pageContent.innerHTML = html;

                    // -------------------------------------------------------
                    // RUN SCRIPTS inside dynamically loaded page
                    // -------------------------------------------------------
                    const tempDiv = document.createElement("div");
                    tempDiv.innerHTML = html;

                    tempDiv.querySelectorAll("script").forEach(oldScript => {
                        const newScript = document.createElement("script");

                        if (oldScript.src) {
                            newScript.src = oldScript.src;   // external JS
                        } else {
                            newScript.innerHTML = oldScript.innerHTML; // inline JS
                        }

                        document.body.appendChild(newScript); // execute it
                    });
                    // -------------------------------------------------------
                })
                .catch(err => {
                    console.error(err);
                    pageContent.innerHTML = `<p style="color:red;text-align:center;">Failed to load page: ${page}</p>`;
                });
        });
    });
});


// logout
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn =
        document.getElementById('logoutBtn') ||
        document.getElementById('logoutUser');

    if (!logoutBtn) {
        console.warn('⚠️ Logout button not found on this page.');
        return;
    }

    logoutBtn.addEventListener('click', (e) => {
        e.preventDefault(); // avoid accidental anchor redirects

        // check if SweetAlert is loaded
        if (typeof Swal === 'undefined') {
            console.error('❌ SweetAlert2 not loaded.');
            alert('Logout confirmation failed. Please try again.');
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
                localStorage.removeItem('userRole');

                Swal.fire({
                    title: 'Logged Out!',
                    text: 'You have been successfully logged out.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1200,
                    didClose: () => {
                        window.location.href = 'index.html';
                    }
                });
            }
        });
    });
});

