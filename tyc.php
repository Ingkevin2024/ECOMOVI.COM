<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - Ecomovi</title>
    <style>

        @keyframes parpadeoVerde {
    0%, 100% {
        box-shadow: 0 0 10px #00ff00, 0 0 20px #05FF05FF, 0 0 15px #00ff00;
        border-color: #05FF05FF;
    }
    50% {
        box-shadow: 0 0 5px #00aa00, 0 0 10px #00aa00;
        border-color: #00aa00;
    }
}

.borde-verde {
    border: 3px solid #05FF05FF;
    box-shadow: 0 0 5px #05FF05FF;
    animation: parpadeoVerde 2.5s infinite;
}


    body {
        height: 100vh;
        margin: 0;
        justify-content: center;
        align-items: center;
        background: url(images/fonfi.jpeg) no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
    }

    header {
        padding: 2rem;
        text-align: center;
        font-size: 1.8rem;
        font-weight: bold;
        color: #08291c;
    }

    .container {
        display: flex;
        max-width: 1200px;
        margin: auto;
        padding: 2rem;
        gap: 2rem;
    }

    .sidebar {
        width: 250px;
        background-color: #e6f3d97b;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .sidebar h3 {
        font-size: 1.1rem;
        color: #000000;
        margin-bottom: 1rem;
    }

    .sidebar a {
        display: block;
        color: #000000;
        text-decoration: none;
        margin-bottom: 0.5rem;
    }

    .sidebar a:hover {
        text-decoration: underline;
    }

    .content {
        flex: 1;
        background-color: #e6f3d97b;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .content h2 {
        color: #000000;
        margin-top: 2rem;
    }

    .content p,
    .content ul {
        line-height: 1.6;
    }

    @media screen and (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            position: static;
        }
    }
    .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .back-button:hover {
            background-color: #45a049;
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }

        .back-button img {
            width: 20px;
            height: 20px;
        }
    </style>
</head>

<body> 
    <a href="Registro de usuario.html" class="back-button">
        <i class="fas fa-arrow-left"></i> Regresar
    </a>

    <header>
        Términos y Condiciones - EcomoviStop
    </header>   
    <div class="container">
        <div class="sidebar borde-verde">
            <h3>Tabla de Contenido</h3>
            <b><a href="#aspectos">1. Aspectos Generales</a> </b>
            <b><a href="#Objetivo">1.2. Objetivo</a></b>
            <b><a href="#condiciones">2. Condiciones de Participación</a> </b>
            <b><a href="#busqueda-usuario">3. Búsqueda de Usuario</a> </b>
            <b><a href="#funcionalidad-consulta">3.1 Funcionalidad de Consulta</a> </b>
            <b><a href="#privacidad">3.2 Privacidad y Seguridad</a> </b>
            <b><a href="#uso-datos">3.3 Uso Autorizado de Datos</a> </b>
            <b><a href="#terminos-consulta">3.4 Términos y Condiciones de Consulta</a> </b>
            <b><a href="#premios">4. Puntos y Premios</a> </b>
            <b><a href="#datos">5. Uso de Datos</a> </b>
            <b><a href="#responsabilidad">6. Responsabilidad</a> </b>
            <b><a href="#modificaciones">7. Modificaciones</a> </b>
            <b><a href="#aceptacion">8. Aceptación</a> </b>
            <b><a href="#acceso">9. Acceso y Registro</a> </b>
            <b><a href="#uso">10. Uso Permitido</a> </b>
            <b><a href="#propiedad">11. Propiedad Intelectual</a> </b>
            <b><a href="#contacto">12. Contacto</a> </b>
            <b><a href="#registro">13. Registro de Usuario</a> </b>
            <b><a href="#registro-supervisor">14. Registro de Supervisor</a> </b>
            <b><a href="#terminos-vehiculos">15. Visualización de Vehículos</a> </b>
            <b><a href="#seguimiento-movilidad">16. seguimiento de movilidad</a></b>

        </div>

        <div class="content borde-verde">
            <h2 id="aspectos">1. Aspectos Generales</h2>
            <p>
                Desarrollar la página web ECO-MOVI, cumpliendo de manera integral con todos los requerimientos
                funcionales y técnicos establecidos, con el fin de ofrecer una plataforma eficiente, intuitiva y
                alineada con los objetivos del proyecto.
            </p>

            <h2 id="Objetivo">1.2 Objetivo</h2>
            <p>
                Desarrollar la página web ECO-MOVI, cumpliendo de manera integral con todos los requerimientos
                funcionales y técnicos establecidos, con el fin de ofrecer una plataforma eficiente, intuitiva y
                alineada con los objetivos del proyecto.
            </p>

            <h2 id="condiciones">2. Condiciones de Participación</h2>
            <ul>
                <li>Registro del vehículo y usuario en la plataforma.</li>
                <li>Cumplir con dejar el vehículo guardado en los días asignados o voluntarios.</li>
                <li>La información debe ser verídica y comprobable.</li>
                <li>Mantener tus papeles al día.</li>
                <li>Se puede inhabilitar la cuenta entre una semana y un mes si se detecta fraude.</li>
                <li>El usuario es responsable de su cuenta y contraseña. Hay recuperación por correo en caso de olvido.
                </li>
                <li>No nos hacemos responsables por accesos no autorizados si el usuario maneja mal sus credenciales.
                </li>
            </ul>

            <h2 id="busqueda-usuario">3. Búsqueda de Usuario</h2>

            <h3 id="funcionalidad-consulta">3.1 Funcionalidad de Consulta</h3>
            <li>La herramienta de búsqueda permite consultar si un usuario está registrado mediante el número de cédula.
                Al ingresar un número válido, el sistema mostrará el nombre completo y los vehículos asociados, siempre
                que exista una coincidencia previa en la base de datos.</li>

            <h3 id="privacidad">3.2 Privacidad y Seguridad</h3>
            <li>ECO-MOVI aplica medidas de seguridad para proteger la privacidad de los datos desplegados. Esta
                funcionalidad es interna y está protegida contra accesos indebidos. No se recomienda compartir el número
                de cédula en espacios públicos o no autorizados.</li>

            <h3 id="uso-datos">3.3 Uso Autorizado de Datos</h3>
            <li>El acceso a esta herramienta debe realizarse exclusivamente con fines administrativos y de consulta
                autorizada. Cualquier intento de uso indebido, como la extracción masiva o publicación de datos
                personales sin consentimiento, será motivo de suspensión del acceso y posibles acciones legales.</li>

            <h3 id="terminos-consulta">3.4 Términos y Condiciones de Consulta</h3>
            <ul>
                <li>El uso de esta funcionalidad implica la aceptación de no divulgar, copiar ni almacenar la
                    información obtenida con fines no permitidos.</li>
                <li>La información que aparece es de uso exclusivo de ECO-MOVI y no debe ser manipulada fuera del
                    entorno autorizado.</li>
                <li>Está prohibido tomar capturas, transcribir o reenviar los datos a terceros.</li>
                <li>El incumplimiento de estos términos puede conllevar la inhabilitación del acceso y sanciones legales
                    conforme a la ley.</li>
            </ul>

            <h2 id="premios">4. Puntos y Premios</h2>
            <li>Los usuarios recibirán puntos por cada día que mantengan su vehículo guardado. Estos puntos podrán ser
                redimidos por premios ofrecidos dentro de la plataforma, según disponibilidad.</li>

            <h2 id="datos">5. Uso de Datos</h2>
            <li>La plataforma podrá recopilar datos para mejorar la experiencia del usuario y validar el cumplimiento
                del programa. Todos los datos serán tratados bajo las políticas de privacidad vigentes.</li>

            <h2 id="responsabilidad">6. Responsabilidad</h2>
            <li>ECO-MOVI no se hace responsable por el mal uso de la plataforma ni por consecuencias derivadas del
                ingreso de información incorrecta o engañosa por parte del usuario.</li>

            <h2 id="modificaciones">7. Modificaciones</h2>
            <li>Nos reservamos el derecho de modificar estos términos y condiciones en cualquier momento. Las
                modificaciones serán comunicadas a los usuarios registrados.</li>

            <h2 id="aceptacion">8. Aceptación</h2>
            <li>El uso de la plataforma implica la aceptación total de estos términos y condiciones.</li>

            <h2 id="acceso">9. Acceso y Registro</h2>
            <li>Para participar es necesario registrarse con información personal y del vehículo. El acceso está
                restringido a usuarios registrados y autorizados.</li>

            <h2 id="uso">10. Uso Permitido</h2>
            <li>Está permitido usar la plataforma únicamente para los fines establecidos en este documento. Cualquier
                otro uso será considerado indebido.</li>

            <h2 id="propiedad">11. Propiedad Intelectual</h2>
            <li>Todos los contenidos, diseños y funcionalidades de ECO-MOVI son propiedad intelectual protegida y no
                pueden ser copiados o reutilizados sin autorización expresa.</li>

            <h2 id="contacto">12. Contacto</h2>
            <li>Para dudas o aclaraciones puedes contactarnos a través de los canales oficiales de atención al usuario.
            </li>

            <h2 id="registro">13. Registro de Usuario</h2>
            <li>El proceso de registro requerirá completar un formulario con información verificada. Solo los usuarios
                validados podrán acceder a los beneficios.</li>

            <h2 id="registro-supervisor">14. Registro de Supervisor</h2>
            <li>Los supervisores podrán acceder a funciones especiales para monitorear y validar la participación de los
                usuarios registrados bajo su cargo.</li>

            <h2 id="terminos-vehiculos">15. Visualización de Vehículos</h2>
            <p>La interfaz de consulta de vehículos asociada a un usuario está destinada exclusivamente a tareas de
                verificación interna. Al hacer uso de esta visualización, los administradores y supervisores deben tener
                en cuenta los siguientes términos:</p>
            <ul>
                <li>No está permitido tomar capturas de pantalla, copiar o difundir la información presentada en esta
                    sección.</li>
                <li>Los datos como placa, marca, modelo y recompensas obtenidas son de uso confidencial y sólo pueden
                    ser utilizados para validación dentro del sistema ECO-MOVI.</li>
                <li>El acceso no autorizado, así como la manipulación o distribución de esta información, será
                    sancionado con la pérdida de acceso y posibles acciones legales.</li>
                <li>Al utilizar esta funcionalidad, el usuario acepta plenamente estos términos de uso específicos.</li>
                
            </ul>

            <h2 id="seguimiento-movilidad">16. Seguimiento de movilidad</h2>
            <ul>
            <li>16.1. Aceptación del uso Al utilizar este formulario de Registro de Movilidad, usted acepta estos términos
                 y condiciones. Si no está de acuerdo con alguno de ellos, por favor absténgase de usar este sistema.</li>

        <li> 16.2.Finalidad del formulario El propósito del formulario es registrar de manera voluntaria la movilidad 
        del usuario como parte del programa de seguimiento ambiental y promoción de alternativas sostenibles, en
         particular el pico y placa voluntario.</li>

        <li>16.3. Registro diario</li>

        <li>Cada vehículo solo podrá realizar un (1) registro por día.</li>

        <li>Si se intenta registrar una placa más de una vez en el mismo día, el sistema bloqueará 
        la opción de ingresar nuevamente la primera parte.</li>

        <li>16.4. Proceso del registro El proceso de registro se divide en dos momentos:</li>

        <li>Primera parte: Se solicita información como el departamento, municipio, placa del 
            vehículo, fecha y hora inicial, junto con una foto del kilometraje al inicio del día.</li>

        <li>Segunda parte (posterior): Se solicitará la foto final del kilometraje y se finalizará 
            el proceso para validar y otorgar puntos.</li>

        <li>16.5. Datos requeridos Todos los campos del formulario deben completarse de forma veraz
             y responsable. Los datos ingresados serán almacenados y utilizados únicamente para fines
              de control, monitoreo y asignación de incentivos del programa.</li>

        <li>16.6. Asignación de puntos Los puntos se asignan automáticamente según el cumplimiento 
            del pico y placa voluntario:</li>

        <li>Si el vehículo tiene restricción ese día, se asignarán 100 puntos.</li>

        <li>Si el vehículo no tiene restricción, se otorgarán 200 puntos. Estos puntos se acumulan y 
            pueden ser canjeados o reconocidos conforme a los lineamientos del programa.</li>

        <li>16.7. Protección de datos Las imágenes y datos recolectados serán tratados bajo los 
            principios de confidencialidad y seguridad de la información, conforme a la normativa 
            de protección de datos personales.</li>

        <li>16.8. Uso indebido Está prohibido el uso fraudulento del sistema, como el ingreso de 
            placas falsas, imágenes manipuladas o datos erróneos. La detección de dichas conductas 
            podrá conllevar la suspensión del usuario en el programa.</li>

        <li>16.9. Modificaciones Estos términos pueden ser actualizados en cualquier momento.
             Se recomienda revisarlos periódicamente.</li>
            </ul>
     
    </div>

</body>

</html>
