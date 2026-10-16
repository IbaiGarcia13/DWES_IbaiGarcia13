<?php

declare(strict_types=1);

require_once __DIR__ . '/OpenF1Api.php';

$pilotos = [];
$error = null;

try {
    $pilotos = (new OpenF1Api())->obtenerPilotosTemporadaActual();
} catch (RuntimeException $exception) {
    $error = $exception->getMessage();
}

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilotos F1 | OpenF1</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="contenedor">
        <header class="cabecera">
            <p class="etiqueta">OpenF1 · <?= (int) gmdate('Y') ?></p>
            <h1>Pilotos 16 y 44</h1>
            <p class="subtitulo">Datos de la última sesión disputada de la temporada actual.</p>
        </header>

        <section class="panel">
            <?php if ($error !== null): ?>
                <p class="error"><?= escapar($error) ?></p>
            <?php else: ?>
                <div class="tabla-contenedor">
                    <table>
                        <thead>
                        <tr>
                            <th>Número</th>
                            <th>Piloto</th>
                            <th>Equipo</th>
                            <th>País</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pilotos as $piloto): ?>
                            <tr>
                                <td><?= escapar((string) ($piloto['driver_number'] ?? '')) ?></td>
                                <td><?= escapar((string) ($piloto['full_name'] ?? 'Nombre no disponible')) ?></td>
                                <td><?= escapar((string) ($piloto['team_name'] ?? 'Equipo no disponible')) ?></td>
                                <td><?= escapar((string) ($piloto['country_code'] ?? 'No disponible')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <a class="volver" href="index.php">Volver a categorías</a>
        </section>
    </main>
</body>
</html>
