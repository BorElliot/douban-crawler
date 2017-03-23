<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise;

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

    const CRAWLER_URI = "https://jsonplaceholder.typicode.com/comments?postId=1";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->multipleRequest();
    }

    public function multipleRequest()
    {
        $client = new Client();

        for ($i = 0; $i < 10; $i++) {
            $request = $client->postAsync('http://crawler.local/blogs', ['form_params' => ['title' => 'Hello', 'content' => 'just test!!!']]);
            $promises[] = $request;
        }

        $results = Promise\unwrap($promises);

        // Wait for the requests to complete, even if some of them fail
        $results = Promise\settle($promises);

        $this->info("并发请求完成");
    }

    public function syncRequest()
    {
        $client = new Client();
        $response = $client->get('http://httpbin.org/get');
    }
}
