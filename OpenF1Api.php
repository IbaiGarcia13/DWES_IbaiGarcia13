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
        $curl = curl_init($url);
        if ($curl === false) {
            throw new RuntimeException('No se pudo iniciar la conexión con OpenF1.');
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);

        $respuesta = curl_exec($curl);
        if ($respuesta === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new RuntimeException('Error al consultar OpenF1: ' . $error);
        }

        $estado = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($estado < 200 || $estado >= 300) {
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
