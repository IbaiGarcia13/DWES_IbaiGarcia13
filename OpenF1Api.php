<?php

declare(strict_types=1);

class OpenF1Api
{
    // URL de la API de OpenF1
    private const BASE_URL = 'https://api.openf1.org/v1';

    // Obtiene la última sesión de la temporada actual y retorna los datos de los pilotos 16 y 44
    public function obtenerPilotosTemporadaActual(): array
    {
        // 1. Obtiene el año actual en curso
        $temporada = (int) gmdate('Y');

        // 2. Solicita todas las sesiones del calendario de dicho año
        $sesiones = $this->solicitar('/sessions', ['year' => $temporada]);
        $ultimaSesion = null;
        $ultimaFecha = 0;

        // 3. Recorre las sesiones para encontrar la más reciente que ya se haya disputado
        foreach ($sesiones as $sesion) {
            if (!isset($sesion['session_key'], $sesion['date_start']) || !is_string($sesion['date_start'])) {
                continue;
            }

            // Convierte la fecha de inicio a timestamp
            $fecha = strtotime($sesion['date_start']);

            // Comprueba que la sesión ya ocurrió (fecha <= time()) y sea posterior a la última encontrada
            if ($fecha !== false && $fecha <= time() && $fecha > $ultimaFecha) {
                $ultimaFecha = $fecha;
                $ultimaSesion = $sesion['session_key'];
            }
        }

        // Si no hay ninguna sesión finalizada este año, lanza excepción
        if ($ultimaSesion === null) {
            throw new RuntimeException('OpenF1 no tiene sesiones disputadas de la temporada ' . $temporada . '.');
        }

        // 4. Solicita el listado de pilotos que corrieron en esa última sesión
        $pilotos = $this->solicitar('/drivers', ['session_key' => $ultimaSesion]);
        $pilotosSolicitados = [];

        // 5. Filtra únicamente los pilotos con dorsal 16 o 44
        foreach ($pilotos as $piloto) {
            if (!isset($piloto['driver_number'])) {
                continue;
            }

            $numero = filter_var($piloto['driver_number'], FILTER_VALIDATE_INT);
            if ($numero === 16 || $numero === 44) {
                $pilotosSolicitados[$numero] = $piloto;
            }
        }

        // 6. Valida que se hayan encontrado ambos pilotos
        $faltantes = array_values(array_diff([16, 44], array_keys($pilotosSolicitados)));
        if ($faltantes) {
            throw new RuntimeException(
                'OpenF1 no devolvió datos para los pilotos: ' . implode(' y ', $faltantes) . '.'
            );
        }

        // 7. Ordena por número de monoplaza y devuelve el array de pilotos
        ksort($pilotosSolicitados);
        return array_values($pilotosSolicitados);
    }

    // Realiza peticiones HTTP GET a OpenF1 y decodifica la respuesta en formato JSON
    private function solicitar(string $recurso, array $parametros): array
    {
        // Construye la URL completa con los parámetros de la consulta
        $url = self::BASE_URL . $recurso . '?' . http_build_query($parametros);

        // Configura el contexto de la petición HTTP (método, cabeceras, timeout y certificados SSL)
        $contexto = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Accept: application/json',
                'timeout' => 15,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        // Ejecuta la petición GET a la API
        $respuesta = @file_get_contents($url, false, $contexto);

        // Controla si hubo fallo en la conexión de red
        if ($respuesta === false) {
            throw new RuntimeException('Error al consultar OpenF1: no se pudo conectar con la API.');
        }

        // Lee las cabeceras devueltas por el servidor para verificar el código HTTP
        $cabeceras = function_exists('http_get_last_response_headers')
            ? http_get_last_response_headers()
            : [];

        $estado = 0;
        foreach ($cabeceras as $cabecera) {
            if (preg_match('#^HTTP/\S+\s+(\d+)#', $cabecera, $coincidencias)) {
                $estado = (int) $coincidencias[1];
                break;
            }
        }

        // Si el servidor respondió con un error (código distinto a 2xx), lanza excepción
        if ($estado > 0 && ($estado < 200 || $estado >= 300)) {
            throw new RuntimeException('OpenF1 respondió con el código HTTP ' . $estado . '.');
        }

        // Decodifica la respuesta en formato JSON a un array de PHP
        try {
            $datos = json_decode($respuesta, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('OpenF1 devolvió una respuesta JSON no válida.', 0, $exception);
        }

        // Valida que el formato del JSON recibido sea un array
        if (!is_array($datos)) {
            throw new RuntimeException('OpenF1 devolvió un formato de respuesta inesperado.');
        }

        return $datos;
    }
}
