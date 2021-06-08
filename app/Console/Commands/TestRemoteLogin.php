<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class TestRemoteLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle()
    {
        $baseUri = 'https://10.175.200.10/ucmuser';

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
                'Referer' => 'https://10.175.200.10/ucmuser',
//                'Connection' => 'keep-alive',
//                'Content-Length' => '47',
//                'Pragma' => 'no-cache',
//                'Cache-Control' => 'no-cache',
//                'sec-ch-ua' => '" Not;A Brand";v="99", "Google Chrome";v="91", "Chromium";v="91"',
//                'sec-ch-ua-mobile' => '?0',
//                'Upgrade-Insecure-Requests' => '1',
                'Origin' => 'https://10.175.200.10',
//                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
//                'Sec-Fetch-Site' => 'same-origin',
//                'Sec-Fetch-Mode' => 'navigate',
//                'Sec-Fetch-User' => '?1',
//                'Sec-Fetch-Dest' => 'document',
//                'Accept-Encoding' => 'gzip, deflate, br',
//                'Accept-Language' => 'en-US,en;q=0.9,la;q=0.8',
                'Cookie' => $jSessionId,
            ]
        ]);

        var_dump('body', $response->getBody());
        var_dump('status', $response->getStatusCode());

    }
}
