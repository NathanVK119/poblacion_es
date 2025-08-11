function loadSchoolYears(selectId, defaultSY = null) {
    let select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = "<option value=''>Select School Year</option>";

    let currentYear = new Date().getFullYear();
    let startYear = currentYear - 25;
    let endYear = currentYear + 24;
    let currentSY = currentYear + "-" + (currentYear + 1);

    for (let year = startYear; year <= endYear; year++) {
        let option = document.createElement("option");
        let schoolYear = year + "-" + (year + 1);
        option.value = schoolYear;
        option.textContent = schoolYear;

        // Sa `input.php` lang may default na current SY
        if (selectId === "sy" && defaultSY === null && schoolYear === currentSY) {
            option.selected = true;
        }

        // Para sa `edit.php`, ise-select ang school year mula sa database
        if (defaultSY !== null && schoolYear === defaultSY) {
            option.selected = true;
        }

        select.appendChild(option);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let syDropdown = document.getElementById("sy");

    // Para sa `edit.php`, kunin ang value ng `sy` input field
    let selectedSY = syDropdown ? syDropdown.getAttribute("data-selected") : null;

    loadSchoolYears("sy", selectedSY); // Gumagana sa input.php at edit.php
    loadSchoolYears("syFilter"); // Para sa view.php, walang default
});
