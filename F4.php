<?php

declare(strict_types=1);

require_once __DIR__ . '/Monoplaza.php';
require_once __DIR__ . '/F3.php';

class F4 extends Monoplaza
{
    protected const NUMERO_PILOTOS = 30;
    protected const PUNTUA_VUELTA_RAPIDA = false;
    protected const PUNTOS_POR_POSICION = [
        1 => 25,
        2 => 18,
        3 => 15,
        4 => 12,
        5 => 10,
        6 => 8,
        7 => 6,
        8 => 4,
        9 => 2,
        10 => 1,
    ];

    private string $paisCategoria;

    public function __construct(string $piloto, string $nacionalidad, int $numero, string $escuderia, int $puntos, string $paisCategoria) {
        parent::__construct($piloto, $nacionalidad, $numero, $escuderia, $puntos);
        if (trim($paisCategoria) === '') {
            throw new InvalidArgumentException('El país de la categoría F4 es obligatorio.');
        }

        $this->paisCategoria = $paisCategoria;
    }

    public function getPaisCategoria(): string
    {
        return $this->paisCategoria;
    }

    public function subirCategoria(string $academia): F3
    {
        return new F3(
            $this->piloto,
            $this->nacionalidad,
            $this->numero,
            $this->escuderia,
            $this->puntos,
            $academia
        );
    }
}
