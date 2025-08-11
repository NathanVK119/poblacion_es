document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const gradeFilter = document.getElementById("gradeFilter");
    const syFilter = document.getElementById("syFilter");
    const statusFilter = document.getElementById("statusFilter");
    const genderFilter = document.getElementById("genderFilter");
    const searchButton = document.getElementById("searchButton");

    function performSearch() {
        const search = searchInput.value;
        const grade = gradeFilter.value;
        const sy = syFilter.value;
        const status = statusFilter.value;
        const gender = genderFilter.value;

        // Build the URL with search parameters
        let url = 'view.php?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (grade) url += `grade=${encodeURIComponent(grade)}&`;
        if (sy) url += `sy=${encodeURIComponent(sy)}&`;
        if (status) url += `status=${encodeURIComponent(status)}&`;
        if (gender) url += `gender=${encodeURIComponent(gender)}`;

        // Remove trailing & if exists
        url = url.replace(/&$/, '');

        // Navigate to the search URL
        window.location.href = url;
    }

    // Add event listeners
    searchButton.addEventListener("click", performSearch);
    searchInput.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            performSearch();
        }
    });
});

// Modal functionality
function showStudentDetails(lrn) {
    // Get the row data
    const row = document.querySelector(`tr[data-lrn="${lrn}"]`);
    if (!row) return;

    // Get all cells from the row
    const cells = row.cells;

    // Update modal content
    document.getElementById('modal-lrn').textContent = cells[0].textContent;
    document.getElementById('modal-name').textContent = cells[1].textContent;
    document.getElementById('modal-sex').textContent = cells[2].textContent;
    document.getElementById('modal-birthday').textContent = cells[3].textContent;
    document.getElementById('modal-age').textContent = cells[4].textContent;
    document.getElementById('modal-mother-tongue').textContent = cells[5].textContent;
    document.getElementById('modal-ip').textContent = cells[6].textContent;
    document.getElementById('modal-religion').textContent = cells[7].textContent;
    document.getElementById('modal-house-number').textContent = cells[8].textContent.split(',')[0].trim();
    document.getElementById('modal-barangay').textContent = cells[8].textContent.split(',')[1].trim();
    document.getElementById('modal-municipality').textContent = cells[8].textContent.split(',')[2].trim();
    document.getElementById('modal-province').textContent = cells[8].textContent.split('(')[1].split(')')[0].trim();
    document.getElementById('modal-region').textContent = cells[8].textContent.split('(')[1].split(')')[1].trim();
    document.getElementById('modal-father').textContent = cells[9].textContent;
    document.getElementById('modal-mother').textContent = cells[10].textContent;
    document.getElementById('modal-guardian').textContent = cells[11].textContent;
    document.getElementById('modal-relationship').textContent = cells[12].textContent;
    document.getElementById('modal-contact').textContent = cells[13].textContent;
    document.getElementById('modal-learning-modality').textContent = cells[14].textContent;
    document.getElementById('modal-sy').textContent = cells[15].textContent;
    document.getElementById('modal-grade').textContent = cells[16].textContent;
    document.getElementById('modal-section').textContent = cells[17].textContent;
    document.getElementById('modal-adviser').textContent = cells[18].textContent;
    document.getElementById('modal-status').textContent = cells[19].textContent;
    document.getElementById('modal-remarks').textContent = cells[20].textContent;

    // Show the modal
    document.getElementById('studentModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('studentModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('studentModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
