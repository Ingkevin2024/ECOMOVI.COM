


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Restricciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .vehicle-section {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .day-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .day-label {
            width: 100px;
            font-weight: bold;
        }
        input {
            margin: 0 5px;
            width: 50px;
            padding: 5px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Formulario de Restricciones Vehiculares</h1>
    <form action="continuar_formulario.php" method="post">
        <div class="vehicle-section">
            <h2>Carro</h2>
            <?php for ($day = 1; $day <= 5; $day++): ?>
                <div class="day-row">
                    <div class="day-label">Día <?php echo $day; ?>:</div>
                    <input type="number" name="carro[<?php echo $day; ?>][]" min="0" max="9" placeholder="Primer dígito" required>
                    <input type="number" name="carro[<?php echo $day; ?>][]" min="0" max="9" placeholder="Segundo dígito" required>
                </div>
            <?php endfor; ?>
        </div>

        <div class="vehicle-section">
            <h2>Moto</h2>
            <?php for ($day = 1; $day <= 5; $day++): ?>
                <div class="day-row">
                    <div class="day-label">Día <?php echo $day; ?>:</div>
                    <input type="number" name="moto[<?php echo $day; ?>][]" min="0" max="9" placeholder="Primer dígito" required>
                    <input type="number" name="moto[<?php echo $day; ?>][]" min="0" max="9" placeholder="Segundo dígito" required>
                </div>
            <?php endfor; ?>
        </div>

        <button type="submit">Guardar Restricciones</button>
    </form>
</body>
</html>

