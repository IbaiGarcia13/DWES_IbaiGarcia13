<?php

declare(strict_types=1);

abstract class Monoplaza
{
    protected const NUMERO_PILOTOS = 0;
    protected const PUNTOS_POR_POSICION = [];
    protected const PUNTUA_VUELTA_RAPIDA = true;

    protected string $piloto;
    protected string $nacionalidad;
    protected int $numero;
    protected string $escuderia;
    protected int $puntos;

    // Constructor que inicializa y valida los datos base del monoplaza
    public function __construct(string $piloto, string $nacionalidad, int $numero, string $escuderia, int $puntos) {
        if (trim($piloto) === '' || trim($nacionalidad) === '' || trim($escuderia) === '') {
            throw new InvalidArgumentException('El piloto, la nacionalidad y la escudería son obligatorios.');
        }
        if ($numero < 1) {
            throw new InvalidArgumentException('El número del monoplaza debe ser positivo.');
        }
        if ($puntos < 0) {
            throw new InvalidArgumentException('Los puntos no pueden ser negativos.');
        }

        $this->piloto = $piloto;
        $this->nacionalidad = $nacionalidad;
        $this->numero = $numero;
        $this->escuderia = $escuderia;
        $this->puntos = $puntos;
    }

    // Comprueba si una posición está dentro del rango de pilotos de la categoría
    public function posicionValida(int $posicion): bool
    {
        return $posicion >= 1 && $posicion <= static::NUMERO_PILOTOS;
    }

    // Suma puntos al piloto según su posición final y si logró vuelta rápida en el top 10
    public function otorgarPuntos(int $posicion, bool $vueltaRapida = false): void
    {
        if (!$this->posicionValida($posicion)) {
            throw new InvalidArgumentException(
                'La posición debe estar entre 1 y ' . static::NUMERO_PILOTOS . ' para esta categoría.'
            );
        }

        $puntosObtenidos = static::PUNTOS_POR_POSICION[$posicion] ?? 0;
        if (static::PUNTUA_VUELTA_RAPIDA && $vueltaRapida && $posicion <= 10) {
            $puntosObtenidos++;
        }

        $this->puntos += $puntosObtenidos;
    }

    // Devuelve el nombre del piloto
    public function getPiloto(): string
    {
        return $this->piloto;
    }

    // Devuelve la nacionalidad del piloto
    public function getNacionalidad(): string
    {
        return $this->nacionalidad;
    }

    // Devuelve el número del monoplaza
    public function getNumero(): int
    {
        return $this->numero;
    }

    // Devuelve la escudería del piloto
    public function getEscuderia(): string
    {
        return $this->escuderia;
    }

    // Devuelve la cantidad de puntos acumulados
    public function getPuntos(): int
    {
        return $this->puntos;
    }
}
