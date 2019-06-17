<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/autoload.php';

use Jaeger\Config;
use OpenTracing\Formats;

$http = new swoole_http_server("0.0.0.0", 8002);
$http->on('request', function ($request, $response) {
    unset($_SERVER['argv']);
    $config = Config::getInstance();
    $config::$propagator = \Jaeger\Constants\PROPAGATOR_ZIPKIN;

    //init server span start
    $tracer = $config->initTrace('Istio', 'jaeger-agent.istio-system:6831');

    $spanContext = $tracer->extract(Formats\TEXT_MAP, $request->header);

    $serverSpan = $tracer->startSpan('Istio3', array('child_of' => $spanContext));
    $tracer->inject($serverSpan->getContext(), Formats\TEXT_MAP, $_SERVER);

    //client span1 start
    $clientTrace = $config->initTrace('Istio3 Bus');
    $spanContext = $clientTrace->extract(Formats\TEXT_MAP, $_SERVER);
    $clientSapn = $clientTrace->startSpan('Istio3', array('child_of' => $spanContext));

    $sum = 0;
    for($i = 0; $i < 10; $i++){
        $sum += $i;
    }
    $clientSapn->log(array('message' => 'result:'.$sum));
    $clientSapn->finish();

    //client span1 end

    //server span end
    $serverSpan->finish();
    //trace flush
    $config->flush();

    $response->end("Hello Istio3");
});
$http->start();

?>