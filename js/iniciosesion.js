// Espera a que todo el DOM esté completamente cargado antes de ejecutar el código
document.addEventListener('DOMContentLoaded', function() {

    // Maneja el envío del formulario de inicio de sesión
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        // Previene que el formulario se envíe de forma tradicional
        event.preventDefault();

        // Obtiene el valor seleccionado del tipo de inicio (Usuario, Administrador, etc.)
        const tipoInicio = document.getElementById('tipo_Inicio').value;

        // Obtiene y limpia los espacios del número de documento ingresado
        const num_doc_usu = document.getElementById('num_doc_usu').value.trim();

        // Obtiene y limpia los espacios de la contraseña ingresada
        const contrasena = document.getElementById('contrasena').value.trim();

        // Credenciales fijas para el acceso del Administrador (para pruebas básicas o sin base de datos)
        const adminDocument = "admin123";
        const adminPassword = "admin123";

        // Validación: si no se selecciona un tipo de usuario válido
        if (tipoInicio === '#') {
            alert('Por favor, selecciona el tipo de usuario.');
            return; // Detiene el proceso si la validación falla
        }

        // Validación: campos vacíos
        if (!num_doc_usu || !contrasena) {
            alert('Por favor, ingresa tu número de documento y contraseña.');
            return; // Detiene el proceso si la validación falla
        }

        // Verifica si el usuario intenta iniciar sesión como Administrador con las credenciales predefinidas
        if (tipoInicio === "Administrador" && num_doc_usu === adminDocument && contrasena === adminPassword) {
            // Redirecciona a la página del administrador
            window.location.href = "paginaadministrador.html";
            return; // Termina el flujo, no continúa al submit tradicional
        }

        // Si no es Administrador o las credenciales no coinciden, continúa con el envío del formulario (PHP lo procesará)
        this.submit();
    });

    // Configura el enlace de "términos y condiciones" para que abra el modal en lugar de redirigir
    const termsLink = document.querySelector('.form-check-label a');

    // Asigna atributos para activar el modal de Bootstrap al hacer clic
    termsLink.setAttribute('data-toggle', 'modal');
    termsLink.setAttribute('data-target', '#termsModal');

    // Elimina el atributo href para que no redireccione
    termsLink.removeAttribute('href');
});
