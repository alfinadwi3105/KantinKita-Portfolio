<?php

namespace App\Console\Commands;

use App\Pesan\PemrosesPesan;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitConsume extends Command
{
    protected $signature   = 'rabbit:consume';
    protected $description = 'Membaca pesan pesanan dari RabbitMQ';

    public function handle(PemrosesPesan $pemroses): int
    {
        $koneksi = new AMQPStreamConnection(
            config('broker.host'), config('broker.port'),
            config('broker.user'), config('broker.pass'),
            config('broker.vhost'));

        $saluran = $koneksi->channel();
        $saluran->exchange_declare(
            config('broker.dengar'), 'fanout', false, true, false);
        $saluran->queue_declare(
            config('broker.queue'), false, true, false, false);
        $saluran->queue_bind(
            config('broker.queue'), config('broker.dengar'));

        $this->info('Dapur menunggu pesanan. Ctrl+C untuk berhenti.');

        $saluran->basic_consume(
            config('broker.queue'), '', false, false, false, false,
            function (AMQPMessage $pesan) use ($pemroses) {
                $ok = $pemroses->proses($pesan->body);
                $this->line(($ok ? ' masuk    ' : ' dilewati ')
                    . $pesan->body);
                $pesan->ack();
            });

        while ($saluran->is_consuming()) {
            $saluran->wait();
        }

        return self::SUCCESS;
    }
}
