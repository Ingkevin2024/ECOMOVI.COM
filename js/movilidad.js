function loadPlates() {
    const form = document.querySelector('form');
    const pointsInfo = document.getElementById('pointsInfo');
    const fotofin = document.getElementById('fotofin');
    const fotoini = document.getElementById('fotoini');

    // Function to set current date and time
    function setCurrentDateTime() {
        const now = new Date();
        
        // Format for initial and final date/time
        const currentDate = now.toISOString().split('T')[0];
        const currentTime = now.toTimeString().slice(0,5);
        
        document.getElementById('fecha_inicial').value = currentDate;
        document.getElementById('hora_inicial').value = currentTime;
        
        document.getElementById('fecha_final').value = currentDate;
        document.getElementById('hora_final').value = currentTime;
        
        document.getElementById('fotofin').disabled = false;
    }

    // Event: Disable initial photo field after upload
    fotoini.addEventListener('change', function() {
        if (this.files.length > 0) {
            this.disabled = true;
        }
    });

    // Event: Handle final photo upload
    fotofin.addEventListener('change', function() {
        if (this.files.length > 0) {
            this.disabled = true;
            
            // Disable all inputs except submit button
            const formInputs = form.querySelectorAll('input:not([type="submit"]), select');
            formInputs.forEach(input => {
                input.disabled = true;
            });

            // Keep submit button enabled
            const submitButton = form.querySelector('.boton-guardar1');
            if (submitButton) {
                submitButton.disabled = false;
            }

            calculatePoints();
        }
    });

    // Update time every second
    setInterval(setCurrentDateTime, 1000);
    setCurrentDateTime();

    function checkFinalInputs() {
        const fechaFinal = document.getElementById('fechaf').value;
        const horaFinal = document.getElementById('hora_final').value;
        const fotoFinal = document.getElementById('fotofin');
        
        if (fechaFinal && horaFinal) {
            fotoFinal.disabled = false;
        }
    }

    // Remove readonly from final date/time inputs
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('fechaf').removeAttribute('readonly');
        document.getElementById('hora_final').removeAttribute('readonly');
    });

    // Llama a la función para establecer la fecha y hora inicial al cargar la página
    setInitialDateTime();

    // Evento: Llama a la función de cálculo de puntos cuando se sube la foto final
    fotofin.addEventListener('change', calculatePoints);

    // Función: Calcula los puntos según la restricción de placas y días hábiles
    function calculatePoints() {
        if (!fotofin.files || fotofin.files.length === 0) return; // Si no hay foto final, no hace nada

        const placa = document.getElementById('placa').value; // Obtiene el número de placa
        const fechaInicial = document.getElementById('fecha').value; // Obtiene la fecha inicial
        const fechaFinal = document.getElementById('fechaf').value; // Obtiene la fecha final

        if (!placa || !fechaInicial || !fechaFinal) return; // Si falta algún dato, no hace nada

        const startDate = new Date(fechaInicial);
        const endDate = new Date(fechaFinal);
        let totalPoints = 0;

        // Extrae el último dígito de la placa (si tiene guion, lo toma después del guion)
        const plateNumber = placa.includes('-') ? 
            placa.split('-')[1].charAt(1) : 
            placa.charAt(0);

        // Días restringidos según el último dígito de la placa
        const restrictedNumbers = {
            1: ['3', '4'], // Lunes
            2: ['2', '8'], // Martes
            3: ['5', '9'], // Miércoles
            4: ['1', '7'], // Jueves
            5: ['0', '6']  // Viernes
        };

        // Itera sobre cada día dentro del rango de fechas
        for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
            if (date.getDay() === 0 || date.getDay() === 6) continue; // Ignora fines de semana

            const dayOfWeek = date.getDay(); // Obtiene el día de la semana (1 = Lunes, 5 = Viernes)

            // Verifica si el día de la semana tiene restricción para el número de placa
            if (restrictedNumbers[dayOfWeek] && restrictedNumbers[dayOfWeek].includes(plateNumber)) {
                totalPoints += 100; // Día restringido: gana 100 puntos
            } else {
                totalPoints += 200; // Día no restringido: gana 200 puntos
            }
        }

        // Muestra los puntos acumulados en la página
        pointsInfo.textContent = `Puntos ganados: ${totalPoints}`;

        // Guarda los puntos en localStorage para acceder desde otra página
        localStorage.setItem('totalPoints', totalPoints);
    }
}
