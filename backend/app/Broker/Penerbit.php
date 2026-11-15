<?php

namespace App\Broker;

interface Penerbit
{
    public function terbitkan(array $pesan): void;
}