<?php

declare(strict_types=1);

require_once __DIR__ . '/Monoplaza.php';
require_once __DIR__ . '/F1.php';

class F2 extends Monoplaza
{
    protected const NUMERO_PILOTOS = 24;
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

    private bool $tienePuntosSuperlicencia;

    public function __construct(string $piloto, string $nacionalidad, int $numero, string $escuderia, int $puntos, bool $tienePuntosSuperlicencia) {
        parent::__construct($piloto, $nacionalidad, $numero, $escuderia, $puntos);
        $this->tienePuntosSuperlicencia = $tienePuntosSuperlicencia;
    }

    public function tienePuntosSuperlicencia(): bool
    {
        return $this->tienePuntosSuperlicencia;
    }

    public function subirCategoria(string $patrocinadorPrincipal): F1
    {
        if (!$this->tienePuntosSuperlicencia) {
            throw new LogicException('El piloto todavía no tiene los puntos necesarios para la superlicencia :(');
        }

        return new F1(
            $this->piloto,
            $this->nacionalidad,
            $this->numero,
            $this->escuderia,
            $this->puntos,
            $patrocinadorPrincipal
        );
    }
}
