/* ===============================
   SIDEBAR + NAVBAR
================================ */

// Toggle sidebar
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

if (menuBar && sidebar) {
    menuBar.addEventListener('click', () => {
        sidebar.classList.toggle('hide');
    });
}

// Search toggle (mobile)
const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

if (searchButton && searchForm && searchButtonIcon) {
    searchButton.addEventListener('click', (e) => {
        if (window.innerWidth < 576) {
            e.preventDefault();
            searchForm.classList.toggle('show');
            searchButtonIcon.classList.toggle('bx-x');
            searchButtonIcon.classList.toggle('bx-search');
        }
    });
}

window.addEventListener('resize', () => {
    if (window.innerWidth > 576 && searchForm && searchButtonIcon) {
        searchForm.classList.remove('show');
        searchButtonIcon.classList.replace('bx-x', 'bx-search');
    }
});

// Dark mode
const switchMode = document.getElementById('switch-mode');
if (switchMode) {
    switchMode.addEventListener('change', () => {
        document.body.classList.toggle('dark', switchMode.checked);
    });
}

/* ===============================
   CALENDAR
================================ */

document.addEventListener("DOMContentLoaded", () => {
    const calendar = document.getElementById("calendar");
    if (!calendar) return;

    const monthNames = [
        "January","February","March","April","May","June",
        "July","August","September","October","November","December"
    ];

    function generateCalendar(year, month) {
        calendar.innerHTML = "";

        const header = document.createElement("div");
        header.className = "calendar-header";

        const title = document.createElement("h3");
        title.textContent = `${monthNames[month]} ${year}`;

        const prevBtn = document.createElement("button");
        prevBtn.textContent = "◀";

        const nextBtn = document.createElement("button");
        nextBtn.textContent = "▶";

        header.append(prevBtn, title, nextBtn);
        calendar.append(header);

        const grid = document.createElement("div");
        grid.className = "calendar";

        ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].forEach(d => {
            const el = document.createElement("div");
            el.textContent = d;
            el.style.fontWeight = "bold";
            grid.append(el);
        });

        const firstDay = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            grid.append(document.createElement("div"));
        }

        const today = new Date();

        for (let d = 1; d <= totalDays; d++) {
            const el = document.createElement("div");
            el.className = "calendar-day";
            el.textContent = d;

            const thisDate = new Date(year, month, d);

            if (
                thisDate.getDate() === today.getDate() &&
                thisDate.getMonth() === today.getMonth() &&
                thisDate.getFullYear() === today.getFullYear()
            ) {
                el.classList.add("today");
            }

            grid.append(el);
        }

        calendar.append(grid);

        prevBtn.onclick = () => {
            generateCalendar(month === 0 ? year - 1 : year, month === 0 ? 11 : month - 1);
        };

        nextBtn.onclick = () => {
            generateCalendar(month === 11 ? year + 1 : year, month === 11 ? 0 : month + 1);
        };
    }

    const now = new Date();
    generateCalendar(now.getFullYear(), now.getMonth());
});

/* ===============================
   DATE & TIME
================================ */

function updateDateTime() {
    const now = new Date();
    const timeEl = document.getElementById("currentTime");
    const dateEl = document.getElementById("currentDate");

    if (!timeEl || !dateEl) return;

    timeEl.textContent = now.toLocaleTimeString();
    dateEl.textContent = now.toLocaleDateString("en-IN", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric"
    });
}

document.addEventListener("DOMContentLoaded", () => {
    updateDateTime();
    setInterval(updateDateTime, 1000);
});

/* ===============================
   ATTENDANCE (PUNCH IN / OUT)
================================ */

/* ===============================
   ATTENDANCE (PUNCH IN / OUT)
================================ */

// document.addEventListener("DOMContentLoaded", () => {

//     const punchInBtn = document.getElementById("punchInBtn");
//     const punchOutBtn = document.getElementById("punchOutBtn");
//     const punchInTimeEl = document.getElementById("punchInTime");
//     const punchOutTimeEl = document.getElementById("punchOutTime");

//     if (!punchInBtn || !punchOutBtn) {
//         console.warn("Punch buttons not found");
//         return;
//     }

//     document.addEventListener("DOMContentLoaded", () => {

//     const punchInBtn = document.getElementById("punchInBtn");
//     const punchOutBtn = document.getElementById("punchOutBtn");
//     const punchInTimeEl = document.getElementById("punchInTime");
//     const punchOutTimeEl = document.getElementById("punchOutTime");

//     function getAddressAndSend(action) {

//         if (!navigator.geolocation) {
//             alert("Geolocation not supported");
//             return;
//         }

//         navigator.geolocation.getCurrentPosition(async (pos) => {

//             const lat = pos.coords.latitude;
//             const lng = pos.coords.longitude;

//             let address = `Lat:${lat}, Lng:${lng}`;

//             // 🔹 Convert GPS → Address (simple, free)
//             try {
//                 const res = await fetch(
//                     `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`
//                 );
//                 const data = await res.json();
//                 if (data.display_name) {
//                     address = data.display_name;
//                 }
//             } catch (e) {
//                 console.warn("Address fetch failed, using lat/lng");
//             }

//             // 🔹 Show popup with LIVE ADDRESS
//             Swal.fire({
//                 title: action === "punch_in" ? "Confirm Punch In" : "Confirm Punch Out",
//                 html: `<small>${address}</small>`,
//                 icon: "question",
//                 showCancelButton: true,
//                 confirmButtonText: "Yes",
//                 cancelButtonText: "No"
//             }).then((result) => {

//                 if (!result.isConfirmed) return;

//                 // 🔹 Store SAME address in DB
//                 fetch("pages/attendance_action.php", {
//                     method: "POST",
//                     headers: {
//                         "Content-Type": "application/x-www-form-urlencoded"
//                     },
//                     body: new URLSearchParams({
//                         action: action,
//                         location: address
//                     })
//                 })
//                 .then(res => res.json())
//                 .then(data => {
//                     if (data.status === "success") {

//                         if (action === "punch_in") {
//                             punchInTimeEl.textContent =
//                                 "Punch In Time: " + data.time;
//                             punchInBtn.disabled = true;
//                             punchOutBtn.disabled = false;
//                         } else {
//                             punchOutTimeEl.textContent =
//                                 "Punch Out Time: " + data.time;
//                             punchOutBtn.disabled = true;
//                         }

//                         Swal.fire("Success", data.message, "success");
//                     } else {
//                         Swal.fire("Error", data.message, "error");
//                     }
//                 });

//             });

//         }, () => {
//             alert("Location permission denied");
//         });
//     }

//     punchInBtn.addEventListener("click", () => {
//         getAddressAndSend("punch_in");
//     });

//     punchOutBtn.addEventListener("click", () => {
//         getAddressAndSend("punch_out");
//     });

// });


// });
function initAttendance() {

    const punchInBtn = document.getElementById("punchInBtn");
    const punchOutBtn = document.getElementById("punchOutBtn");
    const punchInTimeEl = document.getElementById("punchInTime");
    const punchOutTimeEl = document.getElementById("punchOutTime");

    if (!punchInBtn || !punchOutBtn) {
        console.warn("Punch buttons not ready yet");
        return;
    }

    function getAddressAndSend(action) {

    if (!navigator.geolocation) {
        Swal.fire("Error", "Geolocation not supported", "error");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        async (pos) => {

            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            let address = null;

            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`,
                    {
                        headers: { "Accept": "application/json" }
                    }
                );

                if (!res.ok) throw new Error("Reverse geocode failed");

                const data = await res.json();

                if (!data || !data.display_name) {
                    throw new Error("No address found");
                }

                address = data.display_name;

            } catch (err) {
                console.error(err);

                Swal.fire(
                    "Location Error",
                    "Unable to fetch your live address. Please try again.",
                    "error"
                );
                return; // ⛔ STOP HERE (no popup)
            }

            // ✅ Popup only when address is available
            Swal.fire({
                title: action === "punch_in" ? "Confirm Punch In" : "Confirm Punch Out",
                html: `<small>${address}</small>`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            }).then(result => {

                if (!result.isConfirmed) return;

                fetch("pages/attendance_action.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({
                        action,
                        location: address,
                        lat,
                        lng
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire("Success", data.message, "success");
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                });
            });

        },
        // ❌ Permission denied / GPS error
        (error) => {
            Swal.fire(
                "Location Error",
                "Please allow location access to punch attendance.",
                "error"
            );
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}



    punchInBtn.onclick = () => getAddressAndSend("punch_in");
    punchOutBtn.onclick = () => getAddressAndSend("punch_out");
}

// 🔥 Call it AFTER everything is loaded
window.addEventListener("load", initAttendance);
