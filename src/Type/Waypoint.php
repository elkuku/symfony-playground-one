<?php

namespace App\Type;

class Waypoint
{
    public function __construct(
        public string $name,
        public float $lat,
        public float $lon,
    )
    {
    }
}
