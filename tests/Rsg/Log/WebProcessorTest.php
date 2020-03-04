<?php

namespace Test\Rsg\Log;

use Rsg\Log\Processor;
use Rsg\Log\WebProcessor as Sut;

class WebProcessorTest extends \PHPUnit\Framework\TestCase
{
    private function getMockProcessor()
    {
        return $this->createMock(Processor::class);
    }


    public function testConstructor()
    {
        $processor = $this->getMockProcessor();

        $sut = new Sut($processor);
        $this->assertInstanceOf(\Monolog\Processor\WebProcessor::class, $sut);
        $this->assertInstanceOf(Processor::class, $sut);
        $this->assertInstanceOf(\Rsg\Log\WebProcessor::class, $sut);
    }


    public function testCallsDecoratedProcessor()
    {
        $record = [ 'foo' => 'bar', 'extra' => [] ];
        $processor = $this->getMockProcessor();
        $processor->expects($this->once())
            ->method('__invoke')
            ->with($record);


        $sut = new Sut($processor);
        $sut($record);
    }
}
