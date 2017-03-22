<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class DoubanHouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'douabn:house';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬取豆瓣小组租房信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();

        $requests = function($total)  use ($client) {
            $base_uri = "https://www.douban.com/group/shanghaizufang/discussion?start=";
            for ($i = 0; $i < $total; $i = $i + 25) {
                $request_url = $base_uri.$i;
                yield function() use ($client, $request_url) {
                    $promise = $client->getAsync($request_url);
                    $promise->then(
                        function (ResponseInterface $res) {
                            echo $res->getStatusCode() . "\n";
                        },
                        function (RequestException $e) {
                            echo $e->getMessage() . "\n";
                            echo $e->getRequest()->getMethod();
                        }
                    );
                };
            }
        };

        $pool = new Pool($client, $requests(1000));
    }
}
