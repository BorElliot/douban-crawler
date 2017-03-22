<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as HttpRequest;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
	const BASE_URI = "https://www.douban.com/group/shanghaizufang/discussion?start=";

    public function index()
    {
    	$client = new Client();

		$requests = function ($total) use ($client) {
		    $uri = self::BASE_URI;
		    for ($i = 0; $i < $total; $i++) {
		        yield function() use ($client, $uri, $i) {
		        	Log::info("请求测试", ['index' => $i]);
		            return $client->getAsync($uri);
		        };
		    }
		};

		$pool = new Pool($client, $requests(3), [
			'concurrency' => 5,
			'fulfilled' => function ($response, $index) {
				$response = (string)$response->getBody();
				Log::info("响应成功", ['response' => "", 'index' => $index]);
			},
			'rejected' => function($reason, $index) {
				Log::info('失败', compact($reason, $index));
			}
		]);
		$promise = $pool->promise();
		$promise->wait();
		dd($pool);
    }
}
