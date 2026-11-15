<?php

namespace App\Broker;

class PenerbitUji implements Penerbit
{
    public static array $terkirim = [];

    public function terbitkan(array $pesan): void
    {
        static::$terkirim[] = $pesan;
    }
}