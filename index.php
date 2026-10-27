<?php

declare(strict_types=1);

$categorias = [
    ['nombre' => 'F1', 'pilotos' => 22, 'rapida' => true],
    ['nombre' => 'F2', 'pilotos' => 24, 'rapida' => true],
    ['nombre' => 'F3', 'pilotos' => 30, 'rapida' => false],
    ['nombre' => 'F4', 'pilotos' => 30, 'rapida' => false],
    ['nombre' => 'F Academy', 'pilotos' => 18, 'rapida' => true],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fórmula | Gestión de monoplazas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="contenedor">
        <header class="cabecera">
            <p class="etiqueta">FIA · Gestión de categorías</p>
            <h1>Monoplazas</h1>
            <p class="subtitulo">Puntos, pilotos y ascensos en las categorías de Fórmula.</p>
        </header>

        <section class="panel">
            <h2>Categorías</h2>
            <div class="categorias">
                <?php foreach ($categorias as $categoria): ?>
                    <article class="categoria">
                        <h3><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= $categoria['pilotos'] ?> pilotos</p>
                        <p><?= $categoria['rapida'] ? 'La vuelta rápida puede sumar un punto.' : 'La vuelta rápida no suma puntos :(' ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="nota">Las clases de cada categoría gestionan sus propias tablas de puntuación y validan las posiciones.</p>
        </section>

        <section class="panel">
            <h2>F1 actual</h2>
            <p>Consulta en OpenF1 los datos más recientes disponibles de los pilotos con los números 16 y 44 de la temporada actual.</p>
            <a class="boton" href="pilotos.php">Consultar pilotos 16 y 44</a>
        </section>
    </main>
</body>
</html>