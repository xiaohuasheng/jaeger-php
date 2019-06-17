<?php

require_once '../vendor/autoload.php';

use Jaeger\Sampler\ConstSampler;
use Jaeger\Sampler\ProbabilisticSampler;

class TestSampler extends PHPUnit_Framework_TestCase
{

    public function testConstSampler(){
        $sample = new ConstSampler(true);
        $this->assertTrue($sample->IsSampled()  == true);
    }


    public function testProbabilisticSampler(){
        $sample = new ProbabilisticSampler(0.0001);
        $this->assertTrue($sample->IsSampled() !== null);
    }
}