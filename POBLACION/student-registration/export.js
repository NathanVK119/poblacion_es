document.getElementById('exportBtn').addEventListener('click', async function() {
    const btn = this;
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Exporting...';
    try {
        // Get current search parameters from URL
        const urlParams = new URLSearchParams(window.location.search);
        const searchParams = new URLSearchParams();
        
        // Copy relevant search parameters
        ['search', 'grade', 'sy', 'status', 'gender'].forEach(param => {
            if (urlParams.has(param)) {
                searchParams.append(param, urlParams.get(param));
            }
        });

        // Make the export request with search parameters
        const response = await fetch('export-all-students.php?' + searchParams.toString());
        const data = await response.json();
        const headers = [
            'lrn', 'name', 'sex', 'birthday', 'age', 'mother_tongue', 'ip', 'religion',
            'house_number', 'barangay', 'municipality', 'province', 'region',
            'father', 'mother', 'guardian_name', 'relationship', 'contact',
            'learning_modality', 'sy', 'grade', 'section', 'adviser', 'status', 'remarks'
        ];
        const excelHeaders = headers.map(h => h.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
        const ws = XLSX.utils.json_to_sheet(data, {header: headers});
        XLSX.utils.sheet_add_aoa(ws, [excelHeaders], {origin: 'A1'});
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Student Records');
        XLSX.writeFile(wb, 'Student_Records.xlsx');
    } catch (e) {
        alert('Export failed. Please try again.');
        console.error(e);
    }
    btn.disabled = false;
    btn.textContent = originalText;
});
