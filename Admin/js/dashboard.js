// dashboard.js
// Handles Accept/Reject actions on the Pending User Registrations table using AJAX,
// removing the row and updating the pending count without reloading the page.

document.addEventListener("DOMContentLoaded", function () {
    const pendingTable = document.getElementById("pendingTable");
    const pendingBadge = document.getElementById("pendingBadge");
    const pendingEmptyNote = document.getElementById("pendingEmptyNote");

    function updatePendingCount() {
        const rows = pendingTable.querySelectorAll("tr[data-id]");
        pendingBadge.textContent = rows.length + " pending";
        pendingEmptyNote.style.display = rows.length === 0 ? "block" : "none";
    }

    pendingTable.addEventListener("click", function (e) {
        const btn = e.target.closest(".action-btn");
        if (!btn) return;

        const row = btn.closest("tr");
        const userId = row.getAttribute("data-id");
        const action = btn.getAttribute("data-action"); // "approved" or "rejected"

        row.querySelectorAll("button").forEach(function (b) { b.disabled = true; });

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/UpdateUserStatus.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    row.remove();
                    updatePendingCount();
                } else {
                    alert(response.message);
                    row.querySelectorAll("button").forEach(function (b) { b.disabled = false; });
                }
            }
        };
        xhr.send("id=" + encodeURIComponent(userId) + "&status=" + encodeURIComponent(action));
    });
});
