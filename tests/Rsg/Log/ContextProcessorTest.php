<?php

namespace Test\Rsg\Log;

use Rsg\Log\ContextProcessor as Sut;
use Rsg\Log\Processor;

class ContextProcessorTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $processor = $this->createMock(Processor::class);
        $sut = new Sut($processor);
        $this->assertInstanceOf(Processor::class, $sut);
        $this->assertInstanceOf(\Rsg\Log\ContextProcessor::class, $sut);
    }


    public function testFormatPassesRecordUp()
    {
        $processor = $this->createMock(Processor::class);
        $sut       = new Sut($processor);
        $record    = [ 'foo' => 'bar ' ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects($this->once())
            ->method('__invoke')
            ->with($record)
            ->willReturn($return);

        $this->assertEquals($return, $sut($record));
    }


    public function testFormatEscalatesDefaultValuesFromSubArray()
    {
        $processor = $this->createMock(Processor::class);
        $sut       = new Sut($processor);
        $record    = [ 'foo' => 'bar ', 'context' => [ 'quote_id' => 1, 'contract_id' => 2 ], ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $record + [
                    'quote_id'    => $record[ 'context' ][ 'quote_id' ],
                    'contract_id' => $record[ 'context' ][ 'contract_id' ],
                ]
            )
            ->willReturn($return);

        $this->assertEquals($return, $sut($record));
    }


    public function testFormatEscalatesCustomValuesFromSubArray()
    {
        $processor = $this->createMock(Processor::class);
        $sut       = new Sut($processor, [ 'foo' ]);
        $record    = [ 'context' => [ 'quote_id' => 1, 'foo' => 'bar' ] ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $record + [
                    'foo' => 'bar',
                ]
            )
            ->willReturn($return);

        $this->assertEquals($return, $sut($record));
    }


    public function testFormatEscalationDoesNotOverwrite()
    {
        $processor = $this->createMock(Processor::class);
        $sut       = new Sut($processor);
        $record    = [ 'foo' => 'bar ', 'quote_id' => 2, 'context' => [ 'quote_id' => 1 ] ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects($this->once())
            ->method('__invoke')
            ->with($record)
            ->willReturn($return);

        $this->assertEquals($return, $sut($record));
    }
}
