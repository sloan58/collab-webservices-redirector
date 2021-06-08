<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function() {
    $baseUri = 'https://hq-ucm-pub.karmatek.io/ucmuser';

    $client = new Client([
        'timeout'  => 20.0,
        'verify' => false,
        'proxy' => '127.0.0.1:8866'
    ]);

    $response = $client->get($baseUri);

    $cookies = array_values(array_filter($response->getHeaders(), function($header) {
        return $header === 'Set-Cookie';
    }, ARRAY_FILTER_USE_KEY));

    $jSessionId = explode(';', $cookies[0][0])[0];

    $response = $client->post("{$baseUri}/j_security_check", [
        'form_params' => [
            'j_username' => 'marty',
            'j_password' => 'A$h8urn!',
            'login' => ''
        ],
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Referer' => 'https://hq-ucm-pub.karmatek.io/ucmuser',
            'Origin' => 'https://hq-ucm-pub.karmatek.io',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Cookie' => $jSessionId,
//                'Connection' => 'keep-alive',
//                'Content-Length' => '47',
//                'Pragma' => 'no-cache',
//                'Cache-Control' => 'no-cache',
//                'sec-ch-ua' => '" Not;A Brand";v="99", "Google Chrome";v="91", "Chromium";v="91"',
//                'sec-ch-ua-mobile' => '?0',
//                'Upgrade-Insecure-Requests' => '1',
//                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
//                'Sec-Fetch-Site' => 'same-origin',
//                'Sec-Fetch-Mode' => 'navigate',
//                'Sec-Fetch-User' => '?1',
//                'Sec-Fetch-Dest' => 'document',
//                'Accept-Encoding' => 'gzip, deflate, br',
//                'Accept-Language' => 'en-US,en;q=0.9,la;q=0.8',
        ]
    ]);

    info('body', $response->getBody());
    info('status', $response->getStatusCode());

    return redirect($baseUri, 302, [
        'Set-Cookie' => "$jSessionId; Domain=.karmatek.io; Secure; HttpOnly; path=/"
    ])->withCookie(cookie('Cookie', $jSessionId));
});
