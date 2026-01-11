<link rel="stylesheet" href="css/pages.css">

<h1>
    <i class='bx bx-log-in'></i>My Attendance History
</h1>

<style>
    .profile-card {
        max-width: 100vw !important;
        margin-top: 0px;
        padding: 2%;
        padding-top: 1%;
    }
        
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;   /* 🔥 KEY FIX */
    }

    .filter-bar input {
        width: 180px;
    }

    #employeeFilter {
        width: 160px;
    }

    .filter-item label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .filter-item input {
        min-width: 220px;
    }

    .attendance-filter {
    display: grid;
    grid-template-columns: 1fr 1fr auto auto;
    gap: 12px;                 /* controls spacing */
    align-items: end;
}

    .btn-item button {
        min-width: 110px;
        height: 38px;
        border-radius: 20px;
    }


    @media (max-width: 768px) {
        .attendance-filter {
            grid-template-columns: 1fr;
        }
    }
</style>    


<div class="profile-card">
    <div class="attendance-filter">

        <div class="filter-item">
            <label>From</label>
            <input type="date" class="form-control">
        </div>

        <div class="filter-item">
            <label>To</label>
            <input type="date" class="form-control">
        </div>

        <div class="filter-item btn-item">
            <label>&nbsp;</label>
            <button class="btn btn-primary">Search</button>
        </div>

        <div class="filter-item btn-item">
            <label>&nbsp;</label>
            <button class="btn btn-outline-dark">Reset</button>
        </div>

    </div>
</div>


<div class="table-responsive">
    <table class="custom-table" id="punchTable">
        <thead>
            <tr>
                <th>Sr No</th>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Punch In Time</th>
                <th>Punch Out Time</th>
                <th>Total Hours</th>
            </tr>
        </thead>

         <!-- 10 sample rows (page 1) -->
        <tbody id="punchBody">
            <tr>
                <td>1</td>
                <td>2025-11-10</td>
                <td>EMP001</td>
                <td>09:12 AM</td>
                <td>06:05 PM</td>
                <td>8h 53m</td>
            </tr>
            <tr>
                <td>2</td>
                <td>2025-11-09</td>
                <td>EMP002</td>
                <td>09:20 AM</td>
                <td>06:10 PM</td>
                <td>8h 50m</td>
            </tr>
            <tr>
                <td>3</td>
                <td>2025-11-08</td>
                <td>EMP001</td>
                <td>09:05 AM</td>
                <td>06:00 PM</td>
                <td>8h 55m</td>
            </tr>
            <tr>
                <td>4</td>
                <td>2025-11-07</td>
                <td>EMP003</td>
                <td>09:18 AM</td>
                <td>06:08 PM</td>
                <td>8h 50m</td>
            </tr>
            <tr>
                <td>5</td>
                <td>2025-11-06</td>
                <td>EMP001</td>
                <td>09:10 AM</td>
                <td>06:02 PM</td>
                <td>8h 52m</td>
            </tr>
            <tr>
                <td>6</td>
                <td>2025-11-05</td>
                <td>EMP002</td>
                <td>09:25 AM</td>
                <td>06:15 PM</td>
                <td>8h 50m</td>
            </tr>
            <tr>
                <td>7</td>
                <td>2025-11-04</td>
                <td>EMP001</td>
                <td>09:00 AM</td>
                <td>06:00 PM</td>
                <td>9h 00m</td>
            </tr>
            <tr>
                <td>8</td>
                <td>2025-11-03</td>
                <td>EMP003</td>
                <td>09:22 AM</td>
                <td>06:12 PM</td>
                <td>8h 50m</td>
            </tr>
            <tr>
                <td>9</td>
                <td>2025-11-02</td>
                <td>EMP002</td>
                <td>09:15 AM</td>
                <td>06:05 PM</td>
                <td>8h 50m</td>
            </tr>
            <tr>
                <td>10</td>
                <td>2025-11-01</td>
                <td>EMP001</td>
                <td>09:08 AM</td>
                <td>06:00 PM</td>
                <td>8h 52m</td>
            </tr>
        </tbody>

    </table>
</div>

    <!-- Pagination -->
<div class="d-flex justify-content-end mt-2" id="pagination">   
</div>

<script>
    const allRows = Array.from(document.querySelectorAll("#punchBody tr"));
</script>

<script>
    function applyFilters() {
        const fromDate = document.getElementById("fromDate").value;
        const toDate = document.getElementById("toDate").value;
        const empId = document.getElementById("employeeFilter").value.toUpperCase();

        allRows.forEach(row => {
            const rowDate = row.children[1].innerText; // Date column
            const rowEmp  = row.children[2].innerText.toUpperCase(); // Employee ID

            let show = true;

            if (fromDate && rowDate < fromDate) show = false;
            if (toDate && rowDate > toDate) show = false;
            if (empId && !rowEmp.includes(empId)) show = false;

            row.style.display = show ? "" : "none";
        });

        applyHourColors(); // reapply colors after filtering
    }
</script>

<script>
    function resetFilters() {
        document.getElementById("fromDate").value = "";
        document.getElementById("toDate").value = "";
        document.getElementById("employeeFilter").value = "";

        allRows.forEach(row => row.style.display = "");
    }
</script>

<script>
    function applyHourColors() {
        const rows = document.querySelectorAll("#punchBody tr");

        rows.forEach(row => {
            const totalHoursCell = row.children[5]; // Total Hours column
            const timeText = totalHoursCell.innerText.trim();

            // Extract hours (before 'h')
            const hours = parseInt(timeText.split("h")[0]);

            if (!isNaN(hours)) {
                if (hours < 8) {
                    totalHoursCell.style.color = "red";
                    totalHoursCell.style.fontWeight = "600";
                } else {
                    totalHoursCell.style.color = "green";
                    totalHoursCell.style.fontWeight = "600";
                }
            }
        });
    }

    // Run once on page load
    applyHourColors();
</script>
