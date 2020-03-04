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

    public function providerInvoke(): array
    {
        return [
            'passes record up' => [
                ['foo' => 'bar'],
                ['foo' => 'bar'],
            ],
            'escalates default from context' => [
                ['foo' => 'bar', 'context' => ['quote_id' => 1, 'contract_id' => 2]],
                [
                    'foo'         => 'bar',
                    'quote_id'    => 1,
                    'contract_id' => 2,
                    'context'     => [],
                ],
            ],
            'overwrites preserve' => [
                ['foo' => 'bar', 'quote_id' => 2, 'context' => ['quote_id' => 1]],
                [
                    'foo'         => 'bar',
                    'quote_id'    => 1,
                    'context'     => [ '_quote_id' => 2 ],
                ],
            ],
        ];
    }


    /**
     * @dataProvider providerInvoke
     */
    public function testInvoke(array $record, array $with_record)
    {
        $processor = $this->createMock(Processor::class);
        $sut       = new Sut($processor);
        $return    = ['foo', 'baz'];

        $processor
            ->expects($this->once())
            ->method('__invoke')
            ->with($with_record)
            ->willReturn($return);

        $this->assertEquals($return, $sut($record));
    }


    public function testFormatEscalatesCustomValuesFromSubArray()
    {
        $processor = $this->createMock(Processor::class);
        $sut       = new Sut($processor, ['foo']);
        $record    = ['context' => ['quote_id' => 1, 'foo' => 'bar']];
        $return    = ['foo', 'baz'];

        $processor
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                [
                    'foo' => 'bar',
                    'context' => ['quote_id' => 1],
                ]
            )
            ->willReturn($return);

        $this->assertEquals($return, $sut($record));
    }
}
