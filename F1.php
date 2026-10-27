<?php

declare(strict_types=1);

require_once __DIR__ . '/Monoplaza.php';

class F1 extends Monoplaza
{
    protected const NUMERO_PILOTOS = 22;
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

    private string $patrocinadorPrincipal;

    public function __construct(string $piloto, string $nacionalidad, int $numero, string $escuderia, int $puntos, string $patrocinadorPrincipal) {
        parent::__construct($piloto, $nacionalidad, $numero, $escuderia, $puntos);
        if (trim($patrocinadorPrincipal) === '') {
            throw new InvalidArgumentException('El patrocinador principal es obligatorio.');
        }

        $this->patrocinadorPrincipal = $patrocinadorPrincipal;
    }

    public function getPatrocinadorPrincipal(): string
    {
        return $this->patrocinadorPrincipal;
    }

    public function subirCategoria(): ?Monoplaza
    {
        return null;
    }
}
