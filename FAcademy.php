<?php

declare(strict_types=1);

require_once __DIR__ . '/Monoplaza.php';
require_once __DIR__ . '/F4.php';

class FAcademy extends Monoplaza
{
    protected const NUMERO_PILOTOS = 18;
    protected const PUNTOS_POR_POSICION = [
        1 => 18,
        2 => 15,
        3 => 12,
        4 => 10,
        5 => 8,
        6 => 6,
        7 => 4,
        8 => 2,
        9 => 1,
    ];

    private int $potenciaMaxima;

    public function __construct(
        string $piloto,
        string $nacionalidad,
        int $numero,
        string $escuderia,
        int $puntos,
        int $potenciaMaxima
    ) {
        parent::__construct($piloto, $nacionalidad, $numero, $escuderia, $puntos);
        if ($potenciaMaxima < 1) {
            throw new InvalidArgumentException('La potencia máxima del motor debe ser positiva.');
        }

        $this->potenciaMaxima = $potenciaMaxima;
    }

    public function getPotenciaMaxima(): int
    {
        return $this->potenciaMaxima;
    }

    public function subirCategoria(string $paisCategoria): F4
    {
        return new F4(
            $this->piloto,
            $this->nacionalidad,
            $this->numero,
            $this->escuderia,
            $this->puntos,
            $paisCategoria
        );
    }
}
