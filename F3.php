<?php

declare(strict_types=1);

require_once __DIR__ . '/Monoplaza.php';
require_once __DIR__ . '/F2.php';

class F3 extends Monoplaza
{
    protected const NUMERO_PILOTOS = 30;
    protected const PUNTUA_VUELTA_RAPIDA = false;
    protected const PUNTOS_POR_POSICION = [
        1 => 10,
        2 => 8,
        3 => 7,
        4 => 6,
        5 => 5,
        6 => 4,
        7 => 3,
        8 => 2,
        9 => 1,
    ];

    private string $academia;

    // Constructor que inicializa los datos de F3 y la academia a la que pertenece
    public function __construct(string $piloto, string $nacionalidad, int $numero, string $escuderia, int $puntos, string $academia) {
        parent::__construct($piloto, $nacionalidad, $numero, $escuderia, $puntos);
        if (trim($academia) === '') {
            throw new InvalidArgumentException('La academia del piloto es obligatoria.');
        }

        $this->academia = $academia;
    }

    // Devuelve la academia de desarrollo del piloto
    public function getAcademia(): string
    {
        return $this->academia;
    }

    // Asciende al piloto a F2 recibiendo si cuenta con puntos de superlicencia
    public function subirCategoria(bool $tienePuntosSuperlicencia): F2
    {
        return new F2(
            $this->piloto,
            $this->nacionalidad,
            $this->numero,
            $this->escuderia,
            $this->puntos,
            $tienePuntosSuperlicencia
        );
    }
}
