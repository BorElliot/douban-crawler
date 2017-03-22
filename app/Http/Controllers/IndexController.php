<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
	const BASE_URI = "https://jsonplaceholder.typicode.com/comments?postId=1";

    public function index()
    {
    	$client = new Client();

		$requests = function ($total) use ($client) {
		    $uri = self::BASE_URI;
		    for ($i = 0; $i < $total; $i++) {
		    	sleep(2);

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
                $response_arr = json_decode($response, true);
				Log::info("响应成功", ['response' => $response_arr, 'index' => $index]);
			},
			'rejected' => function($reason, $index) {
				Log::info('失败', compact('reason', 'index'));
			}
		]);
		$promise = $pool->promise();
		$promise->wait();
		dd($pool);
    }
}
