document.addEventListener("DOMContentLoaded", () => {

    const dashboardContent = document.getElementById("dashboardContent");
    const pageContent = document.getElementById("pageContent");

    // DASHBOARD CLICK
    document.getElementById("dashboardLink").addEventListener("click", (e) => {
        e.preventDefault();

        // Show dashboard
        dashboardContent.style.display = "block";
        pageContent.style.display = "none";

        // Update active class
        document.querySelectorAll(".side-menu li").forEach(li => li.classList.remove("active"));
        document.querySelector("#dashboardLink").closest("li").classList.add("active");
    });

    // OTHER SIDEBAR LINKS
    document.querySelectorAll("[data-page]").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            const page = this.getAttribute("data-page");

            fetch(page)
                .then(res => res.text())
                .then(html => {
                    pageContent.innerHTML = html;

                    // Hide dashboard
                    dashboardContent.style.display = "none";
                    pageContent.style.display = "block";

                    // Update active class
                    document.querySelectorAll(".side-menu li").forEach(li => li.classList.remove("active"));
                    this.closest("li").classList.add("active");
                })
                .catch(err => console.error(err));
        });
    });
});


// ================= LEAVE STATUS FILTER (NO REDIRECT) =================
document.addEventListener("submit", function (e) {

    const form = e.target;

    if (form.id === "leaveFilterForm") {
        e.preventDefault(); // 🚫 STOP PAGE REDIRECT

        const params = new URLSearchParams(new FormData(form)).toString();

        // Load same page with filters
        loadPage("pages/leaves_status.php?" + params);
    }
});
// ================= PAGE LOADER SYSTEM =================
function loadPage(page) {
    fetch(page)
        .then(response => response.text())
        .then(html => {
            document.getElementById("pageContent").innerHTML = html;
        })
        .catch(err => {
            console.error("Page load failed:", err);
        });
}

