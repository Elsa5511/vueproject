<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\Redis;
use WebSocket\Client;

class RedisSubscribe extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to redis channel';
    protected $url;
    protected $listen;
    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /* public function __construct() {
      parent::__construct();
      }
     */

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['listen', null, InputOption::VALUE_OPTIONAL, 'Redis subscribe channel example.*', '*'],
            ['url', null, InputOption::VALUE_REQUIRED, 'Websocket Server url', null],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->url = $this->option('url');
        $this->listen = $this->option('listen');
        $this->start();
    }

    public function start() {
        $this->client = new Client($this->url);
        $this->info('Connected to websocket: ' . $this->url);
        $redis = Redis::connection('pubsub');
        $this->info('Listening: ' . $this->listen);
        $redis->psubscribe(explode(',', $this->listen), function ($message, $channel) {
            $this->info('Message received: ', $message);
            $this->client->send(json_encode(['payload' => json_decode($message), 'channel' => $channel]));
        });
    }

}
