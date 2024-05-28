<?php

namespace app\tariff;

class PlanObject
{
    public function __construct(
        public string $identifier,
        public string $name,
        public int $cost,
        public int $duration,
        public bool $canRenew = true
    )
    {}
}