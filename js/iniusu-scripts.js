// Date display functionality
function mostrarFecha() {
    const fecha = new Date();
    const opciones = { 
        weekday: 'long',
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    };
    const fechaFormateada = fecha.toLocaleDateString('es-ES', opciones);
    const fechaCapitalizada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
    const periodo = fecha.getHours() >= 12 ? '' : '';
    
    document.getElementById('fecha').innerHTML = `
        <span style="color: #4CAF50">📅</span> 
        ${fechaCapitalizada}
        <span style="color: #000000">${periodo}</span> <span style="color: #4CAF50">⌚</span>
    `;
}

// Update date every minute
window.onload = function() {
    mostrarFecha();
    setInterval(mostrarFecha, 60000);
};

// Profile image functionality
const profileImg = document.getElementById('profileImg');
const profileTooltip = document.getElementById('profileTooltip');

if (profileImg && profileTooltip) {
    profileImg.addEventListener('mouseover', () => {
        profileTooltip.style.opacity = '1';
    });

    profileImg.addEventListener('mouseout', () => {
        profileTooltip.style.opacity = '0';
    });

    profileImg.addEventListener('click', () => {
        window.location.href = 'perfilusuario.html';
    });
}


// Add button animations
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.card-button');
    
    buttons.forEach(button => {
        button.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
            this.style.boxShadow = '0 10px 20px rgba(76, 175, 80, 0.4)';
            this.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });

        button.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 15px rgba(76, 175, 80, 0.3)';
        });

        button.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
    });
});