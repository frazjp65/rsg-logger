<?php

namespace Test\Rsg\Log;

use Monolog\Processor\ProcessorInterface;
use Rsg\Log\ContextProcessor as Sut;

class ContextProcessorTest
    extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $processor = $this->createMock( ProcessorInterface::class );
        $sut = new Sut( $processor );
        $this->assertInstanceOf( ProcessorInterface::class, $sut );
        $this->assertInstanceOf( \Rsg\Log\ContextProcessor::class, $sut );
    }


    public function testFormatPassesRecordUp()
    {
        $processor = $this->createMock( ProcessorInterface::class );
        $sut       = new Sut( $processor );
        $record    = [ 'foo' => 'bar ' ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects( $this->once() )
            ->method( '__invoke' )
            ->with( $record )
            ->willReturn( $return );

        $this->assertEquals( $return, $sut( $record ) );
    }


    public function testFormatEscalatesDefaultValuesFromSubArray()
    {
        $processor = $this->createMock( ProcessorInterface::class );
        $sut       = new Sut( $processor );
        $record    = [ 'foo' => 'bar ', 'context' => [ 'quote_id' => 1, 'contract_id' => 2 ], ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects( $this->once() )
            ->method( '__invoke' )
            ->with(
                $record + [
                    'quote_id'    => $record[ 'context' ][ 'quote_id' ],
                    'contract_id' => $record[ 'context' ][ 'contract_id' ],
                ]
            )
            ->willReturn( $return );

        $this->assertEquals( $return, $sut( $record ) );
    }


    public function testFormatEscalatesCustomValuesFromSubArray()
    {
        $processor = $this->createMock( ProcessorInterface::class );
        $sut       = new Sut( $processor, [ 'foo' ] );
        $record    = [ 'context' => [ 'quote_id' => 1, 'foo' => 'bar' ] ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects( $this->once() )
            ->method( '__invoke' )
            ->with(
                $record + [
                    'foo' => 'bar',
                ]
            )
            ->willReturn( $return );

        $this->assertEquals( $return, $sut( $record ) );
    }


    public function testFormatEscalationDoesNotOverwrite()
    {
        $processor = $this->createMock( ProcessorInterface::class );
        $sut       = new Sut( $processor );
        $record    = [ 'foo' => 'bar ', 'quote_id' => 2, 'context' => [ 'quote_id' => 1 ] ];
        $return    = [ 'foo', 'baz' ];

        $processor
            ->expects( $this->once() )
            ->method( '__invoke' )
            ->with( $record )
            ->willReturn( $return );

        $this->assertEquals( $return, $sut( $record ) );
    }
}
