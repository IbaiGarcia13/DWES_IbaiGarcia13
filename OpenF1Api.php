<?php

declare(strict_types=1);

class OpenF1Api
{
    private const BASE_URL = 'https://api.openf1.org/v1';

    public function obtenerPilotosTemporadaActual(): array
    {
        $temporada = (int) gmdate('Y');
        $sesiones = $this->solicitar('/sessions', ['year' => $temporada]);
        $ultimaSesion = null;
        $ultimaFecha = 0;

        foreach ($sesiones as $sesion) {
            if (!isset($sesion['session_key'], $sesion['date_start']) || !is_string($sesion['date_start'])) {
                continue;
            }

            $fecha = strtotime($sesion['date_start']);
            if ($fecha !== false && $fecha <= time() && $fecha > $ultimaFecha) {
                $ultimaFecha = $fecha;
                $ultimaSesion = $sesion['session_key'];
            }
        }

        if ($ultimaSesion === null) {
            throw new RuntimeException('OpenF1 no tiene sesiones disputadas de la temporada ' . $temporada . '.');
        }

        $pilotos = $this->solicitar('/drivers', ['session_key' => $ultimaSesion]);
        $pilotosSolicitados = [];
        foreach ($pilotos as $piloto) {
            if (!isset($piloto['driver_number'])) {
                continue;
            }

            $numero = filter_var($piloto['driver_number'], FILTER_VALIDATE_INT);
            if ($numero === 16 || $numero === 44) {
                $pilotosSolicitados[$numero] = $piloto;
            }
        }

        $faltantes = array_values(array_diff([16, 44], array_keys($pilotosSolicitados)));
        if ($faltantes) {
            throw new RuntimeException(
                'OpenF1 no devolvió datos para los pilotos: ' . implode(' y ', $faltantes) . '.'
            );
        }

        ksort($pilotosSolicitados);
        return array_values($pilotosSolicitados);
    }

    private function solicitar(string $recurso, array $parametros): array
    {
        $url = self::BASE_URL . $recurso . '?' . http_build_query($parametros);

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

        $respuesta = @file_get_contents($url, false, $contexto);

        if ($respuesta === false) {
            throw new RuntimeException('Error al consultar OpenF1: no se pudo conectar con la API.');
        }

        // Obtener el código HTTP de la respuesta (Correción error php con el nuevo XAMPP)
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

        if ($estado > 0 && ($estado < 200 || $estado >= 300)) {
            throw new RuntimeException('OpenF1 respondió con el código HTTP ' . $estado . '.');
        }

        try {
            $datos = json_decode($respuesta, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('OpenF1 devolvió una respuesta JSON no válida.', 0, $exception);
        }

        if (!is_array($datos)) {
            throw new RuntimeException('OpenF1 devolvió un formato de respuesta inesperado.');
        }

        return $datos;
    }
}
