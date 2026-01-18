/* ===============================
   SIDEBAR + NAVBAR
================================ */

// Toggle sidebar
const menuBar = document.querySelector("#content nav .bx.bx-menu");
const sidebar = document.getElementById("sidebar");

if (menuBar && sidebar) {
  menuBar.addEventListener("click", () => {
    sidebar.classList.toggle("hide");
  });
}

// Search toggle (mobile)
const searchButton = document.querySelector(
  "#content nav form .form-input button"
);
const searchButtonIcon = document.querySelector(
  "#content nav form .form-input button .bx"
);
const searchForm = document.querySelector("#content nav form");

if (searchButton && searchForm && searchButtonIcon) {
  searchButton.addEventListener("click", (e) => {
    if (window.innerWidth < 576) {
      e.preventDefault();
      searchForm.classList.toggle("show");
      searchButtonIcon.classList.toggle("bx-x");
      searchButtonIcon.classList.toggle("bx-search");
    }
  });
}

window.addEventListener("resize", () => {
  if (window.innerWidth > 576 && searchForm && searchButtonIcon) {
    searchForm.classList.remove("show");
    searchButtonIcon.classList.replace("bx-x", "bx-search");
  }
});

// Dark mode
const switchMode = document.getElementById("switch-mode");
if (switchMode) {
  switchMode.addEventListener("change", () => {
    document.body.classList.toggle("dark", switchMode.checked);
  });
}

/* ===============================
   CALENDAR
================================ */

document.addEventListener("DOMContentLoaded", () => {
  const calendar = document.getElementById("calendar");
  if (!calendar) return;

  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
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

    ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"].forEach((d) => {
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
      generateCalendar(
        month === 0 ? year - 1 : year,
        month === 0 ? 11 : month - 1
      );
    };

    nextBtn.onclick = () => {
      generateCalendar(
        month === 11 ? year + 1 : year,
        month === 11 ? 0 : month + 1
      );
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
    year: "numeric",
  });
}

document.addEventListener("DOMContentLoaded", () => {
  updateDateTime();
  setInterval(updateDateTime, 1000);
});

function initAttendance() {
  const punchInBtn = document.getElementById("punchInBtn");
  const punchOutBtn = document.getElementById("punchOutBtn");

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

        let addressText = "";
        let addressHtml = "";

        try {
          // 🔁 Use same logic as index.html (Nominatim style response via your PHP)
          const res = await fetch("pages/reverse_geocode.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ lat, lng }),
          });

          if (!res.ok) throw new Error("Reverse geocode failed");

          const data = await res.json();
          const a = data.address || {};

          const landmark = a.neighbourhood || a.suburb || "Not available";
          const area = a.road || "";
          const city = a.city || a.town || a.village || "";
          const state = a.state || "";
          const country = a.country || "";
          const postcode = a.postcode || "";

          addressHtml = `
            <strong>Landmark:</strong> ${landmark}<br>
            <strong>Area:</strong> ${area}<br>
            <strong>City:</strong> ${city}<br>
            <strong>State:</strong> ${state}<br>
            <strong>Country:</strong> ${country}<br>
            ${postcode ? `<strong>Pincode:</strong> ${postcode}<br>` : ""}
          `;

          addressText = [
            landmark !== "Not available" ? landmark : null,
            area,
            city,
            state,
            postcode,
            country,
          ].filter(Boolean).join(", ");

        } catch (err) {
          Swal.fire("Location Error", "Unable to fetch your live address.", "error");
          return;
        }

        Swal.fire({
          title: action === "punch_in" ? "Confirm Punch In" : "Confirm Punch Out",
          html: `
            <div style="text-align:left;background:#f8f9fb;padding:14px 18px;border-radius:8px;border-left:4px solid #6c63ff;">
              <div style="font-weight:600;margin-bottom:6px;color:#6c63ff;">
                📍 Current Location
              </div>
              ${addressHtml}
            </div>
          `,
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Yes",
          cancelButtonText: "No",
        }).then((result) => {
          if (!result.isConfirmed) return;

          fetch("pages/attendance_action.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
              action,
              location: addressText,
              lat,
              lng,
            }),
          })
            .then((res) => res.json())
            .then((data) => {
              if (data.status === "success") {
                Swal.fire("Success", data.message, "success");

                punchInBtn.disabled = true;
                punchInBtn.classList.add("disabled");

                punchOutBtn.disabled = true;
                punchOutBtn.classList.add("disabled");

                const punchInTime = Date.now();
                document.getElementById("punchInTime").textContent =
                  "" + new Date(punchInTime).toLocaleTimeString();

                startDuration(punchInTime);
              } else {
                Swal.fire("Error", data.message, "error");
              }
            })
            .catch((err) => {
              console.log(err);
              Swal.fire("Error", "Server error occurred", "error");
            });
        });
      },
      (error) => {
        let msg = "Unable to get location.";
        if (error.code === 1) msg = "Location permission denied.";
        else if (error.code === 2) msg = "Location unavailable.";
        else if (error.code === 3) msg = "Location request timed out.";
        Swal.fire("Location Error", msg, "error");
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
      }
    );

  }

  punchInBtn.onclick = () => getAddressAndSend("punch_in");
  punchOutBtn.onclick = () => getAddressAndSend("punch_out");
}

// 🔥 Call it AFTER everything is loaded
window.addEventListener("load", initAttendance);

let durationInterval = null;

function startDuration(punchInTime) {
  const durationEl = document.getElementById("workDuration");
  const punchOutBtn = document.getElementById("punchOutBtn");

  if (!durationEl || !punchOutBtn) return;

  // ✅ FIX: stop previous timer (THIS WAS MISSING)
  if (durationInterval) {
    clearInterval(durationInterval);
    durationInterval = null;
  }

  function tick() {
    const diff = Math.floor((Date.now() - punchInTime) / 1000);

    const h = Math.floor(diff / 3600);
    const m = Math.floor((diff % 3600) / 60);
    const s = diff % 60;

    durationEl.textContent =
      `Working Duration: ${h}h ${m}m ${s}s`;

    // ✅ FIX: enable after 2 minutes
    if (diff >= 120) {
      punchOutBtn.disabled = false;
      punchOutBtn.classList.remove("disabled");
    }
  }

  tick();
  durationInterval = setInterval(tick, 1000);
}

async function restoreAttendanceState() {
  const punchInBtn = document.getElementById("punchInBtn");
  const punchOutBtn = document.getElementById("punchOutBtn");

  updateCurrentLocationBox();

  const res = await fetch("pages/attendance_action.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "action=status",
  });

  const data = await res.json();
  if (data.status !== "found") return;

  punchInBtn.disabled = true;
  punchInBtn.classList.add("disabled");

  document.getElementById("punchInTime").textContent =
    "" + new Date(data.punch_in).toLocaleTimeString();

  document.getElementById("location-data").textContent =
    "" + data.location;

  if (data.punch_out) {
    punchOutBtn.disabled = true;
    document.getElementById("punchOutTime").textContent =
      "" + new Date(data.punch_out).toLocaleTimeString();
    return;
  }

  punchOutBtn.disabled = true;
  startDuration(new Date(data.punch_in).getTime());
}

function updateCurrentLocationBox() {
    const locationEl = document.getElementById("currentLocation");

    if (!navigator.geolocation) {
        locationEl.textContent = "N/A";
        return;
    }

    navigator.geolocation.getCurrentPosition(
        async (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            try {
                const res = await fetch("pages/reverse_geocode.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ lat, lng })
                });
                const data = await res.json();
                const address = data.address;
                const city = address.city || address.town || address.village || '';
                const state = address.state || '';
                locationEl.textContent = city ? `${city}, ${state}` : 'Location not found';
            } catch (err) {
                locationEl.textContent = "Unable to fetch";
            }
        },
        (err) => {
            locationEl.textContent = "Permission denied";
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

window.addEventListener("load", restoreAttendanceState);

function loadMonthStatus(year, month) {

    fetch(`pages/get_month_attendance.php?month=${year}-${month}`)
        .then(res => res.json())
        .then(data => {

            document.querySelectorAll('.calendar-day').forEach(dayEl => {

                const date = dayEl.dataset.date; // YYYY-MM-DD

                if (!date) return;

                dayEl.classList.remove('red','green','yellow','leave');

                if (data[date]) {
                    dayEl.classList.add(data[date]);
                } else {
                    dayEl.classList.add('red'); // ❌ not logged in
                }
            });
        });
}

