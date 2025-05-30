document.addEventListener('DOMContentLoaded', function() {
    // Handle checkbox changes
    const checkboxes = document.querySelectorAll('.check-devolver');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            const devolverBtn = row.querySelector('.boton-devolver');
            const redimirBtn = row.querySelector('.boton-redimir');
            
            if (this.checked) {
                devolverBtn.disabled = false;
                redimirBtn.disabled = false;
            } else {
                devolverBtn.disabled = true;
                redimirBtn.disabled = true;
            }
        });
    });

    // Add search functionality
    const searchInput = document.querySelector('input[name="search_placa"]');
    searchInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const placa = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            if (placa.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Add confirmation before submitting forms
    const forms = document.querySelectorAll('.form-devolver');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const action = this.querySelector('button[type="submit"]:not([disabled])').value;
            const placa = this.querySelector('input[name$="plac_veh"]').value;
            
            const message = action === 'devolver' 
                ? `¿Está seguro que desea devolver los puntos al vehículo ${placa}?`
                : `¿Está seguro que desea descontar los puntos del vehículo ${placa}?`;
                
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});