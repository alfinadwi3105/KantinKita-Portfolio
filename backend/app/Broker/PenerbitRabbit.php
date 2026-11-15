<?php

namespace App\Broker;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PenerbitRabbit implements Penerbit
{
    public function terbitkan(array $pesan): void
    {
        $koneksi = new AMQPStreamConnection(
            config('broker.host'), config('broker.port'),
            config('broker.user'), config('broker.pass'),
            config('broker.vhost'));

        $saluran = $koneksi->channel();
        $saluran->exchange_declare(
            config('broker.terbit'), 'fanout', false, true, false);

        $saluran->basic_publish(
            new AMQPMessage(json_encode($pesan), [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]),
            config('broker.terbit'));

        $saluran->close();
        $koneksi->close();
    }
}