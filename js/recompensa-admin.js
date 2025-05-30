function mostrarModalEditar(nomReco) {
    const modal = document.getElementById('modalEditar' + nomReco);
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function cerrarModalEditar(nomReco) {
    const modal = document.getElementById('modalEditar' + nomReco);
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.opacity = '1';
        document.body.style.overflow = 'auto';
    }, 300);
}

function mostrarModalAgregar() {
    const modal = document.getElementById('modalAgregar');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function cerrarModalAgregar() {
    const modal = document.getElementById('modalAgregar');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.opacity = '1';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                const preview = this.parentElement.querySelector('img') || document.createElement('img');
                preview.className = 'preview-image';
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    if (!preview.parentElement) {
                        input.parentElement.insertBefore(preview, input.nextSibling);
                    }
                }
                
                reader.readAsDataURL(file);
            }
        });
    });
});