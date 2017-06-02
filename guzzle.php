<?php
 require './vendor/autoload.php';

 use GuzzleHttp\Client;
$client = new Client([
 'base_uri' => 'http://localhost/',
 'timeout'  => 5.0,
]);

   # Request / or root
$response = $client->request('GET', '/', [
    'json' => ['foo' => 'href']
]);
    echo $request->getBody();

 

?>
