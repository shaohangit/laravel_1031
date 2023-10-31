<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telnet\TelnetCommandConfig;

class TelnetSupport extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telnet-support {--ip=} {--port=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Support telnet communication';

    /**
     * Execute the console command.
     */
    public function handle() {
        $ip = $this->option('ip');
        $port = $this->option('port');
        if (empty($ip) || empty($port)) {
            $this->error('Please specify both ip and port');
            return;
        }
        if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            $this->error("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
            return;
        }
        if (socket_bind($sock, $ip, $port) === false) {
            $this->error("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)));
            return;
        }
        if (socket_listen($sock, 5) === false) {
            $this->error("socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)));
            return;
        }
        do {
            if (($msgsock = socket_accept($sock)) === false) {
                $this->error("socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)));
                break;
            }
            do {
                if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
                    $this->error("socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)));
                    break 2;
                }
                $args = explode(' ', trim($buf));
                if (empty($args) || empty($command = TelnetCommandConfig::getCommand($args[0]))) {
                    break;
                }
                $commandObj = new $command[0](array_splice($args, 1));
                $response = $commandObj->{$command[1]}();
                socket_write($msgsock, $response, strlen($response));
            } while (true);
            socket_close($msgsock);
        } while (true);
        socket_close($sock);
    }
}
