// document.addEventListener("DOMContentLoaded", () => {
//     const allLinks = document.querySelectorAll('#sidebar .side-menu.top li a');
//     const pageContent = document.getElementById("pageContent");
//     const dashboardContent = document.getElementById("dashboardContent");

//     if (!allLinks.length) return;

//     allLinks.forEach(link => {
//         link.addEventListener("click", (e) => {

//             // FULL PAGE navigation
//             if (link.dataset.nav === "true") {
//                 return;
//             }

//             // AJAX navigation
//             const page = link.dataset.page;
//             if (!page) return;

//             e.preventDefault();

//             allLinks.forEach(i => i.parentElement.classList.remove("active"));
//             link.parentElement.classList.add("active");

//             dashboardContent.style.display = "none";
//             pageContent.style.display = "block";
//             pageContent.innerHTML = `<p style="padding:30px;text-align:center;">Loading...</p>`;

//             fetch(page)
//                 .then(res => {
//                     if (!res.ok) throw new Error("Page not found");
//                     return res.text();
//                 })
//                 .then(html => {
//                     pageContent.innerHTML = html;

//                     // Run scripts inside loaded page
//                     const temp = document.createElement("div");
//                     temp.innerHTML = html;
//                     temp.querySelectorAll("script").forEach(old => {
//                         const s = document.createElement("script");
//                         if (old.src) s.src = old.src;
//                         else s.textContent = old.textContent;
//                         document.body.appendChild(s);
//                     });
//                 })
//                 .catch(() => {
//                     pageContent.innerHTML =
//                         `<p style="color:red;text-align:center;">Failed to load page</p>`;
//                 });
//         });
//     });
// });
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
