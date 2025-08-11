document.addEventListener("DOMContentLoaded", function() {
    loadRegions();

    document.getElementById('region').addEventListener('change', function() {
        loadProvinces(this.value);
    });

    document.getElementById('province').addEventListener('change', function() {
        loadMunicipalities(this.value, document.getElementById('region').value);
    });

    document.getElementById('municipality').addEventListener('change', function() {
        loadBarangays(this.value, document.getElementById('province').value, document.getElementById('region').value);
    });

    document.getElementById("sy").addEventListener("change", function() {
        console.log("School Year Selected:", this.value);
    });

    setTimeout(restorePreviousSelections, 100); // Mas malinis at madaling intindihin
});
