<?php

namespace Test\Rsg\Log;

use Rsg\Log\Processor;
use Rsg\Log\StandardProcessor as Sut;

class StandardProcessorTest
    extends \PHPUnit\Framework\TestCase
{
    private $_env = 'test';
    private $_service = 'logging-test';


    public function testConstructor()
    {
        $sut = new Sut( $this->_env, $this->_service );
        $this->assertInstanceOf( Processor::class, $sut );
        $this->assertInstanceOf( \Rsg\Log\StandardProcessor::class, $sut );
        return $sut;
    }


    /**
     * @depends testConstructor
     */
    public function testRecordHasEnvironment( Sut $sut )
    {
        $record = $sut( [] );
        $this->assertArrayHasKey( 'env', $record );
        $this->assertEquals( $this->_env, $record[ 'env' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordCanOverrideEnvironment( Sut $sut )
    {
        $env    = 'dev';
        $record = $sut( [ 'env' => $env ] );
        $this->assertArrayHasKey( 'env', $record );
        $this->assertEquals( $env, $record[ 'env' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordHasService( Sut $sut )
    {
        $record = $sut( [] );
        $this->assertArrayHasKey( 'service', $record );
        $this->assertEquals( $this->_service, $record[ 'service' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordCanOverrideService( Sut $sut )
    {
        $service = 'some-service';
        $record  = $sut( [ 'service' => $service ] );
        $this->assertArrayHasKey( 'service', $record );
        $this->assertEquals( $service, $record[ 'service' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordHasContext( Sut $sut )
    {
        $record  = $sut( [ 'foo' => 'bar' ] );
        $this->assertArrayHasKey( 'foo', $record );
        $this->assertEquals( 'bar', $record[ 'foo' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordHandlesSeverity( Sut $sut )
    {
        $severity = 'DEBUG';
        $record   = $sut( [ 'level_name' => $severity ] );
        $this->assertArrayNotHasKey( 'level_name', $record );
        $this->assertArrayHasKey( 'severity', $record );
        $this->assertEquals( $severity, $record[ 'severity' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordCannotOverridSeverity( Sut $sut )
    {
        $severity = 'DEBUG';
        $record   = $sut( [ 'level_name' => $severity, 'severity' => 'foo' ] );
        $this->assertArrayNotHasKey( 'level_name', $record );
        $this->assertArrayHasKey( 'severity', $record );
        $this->assertEquals( $severity, $record[ 'severity' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordHandlesTimestamp( Sut $sut )
    {
        $datetime = new \DateTimeImmutable( '2019-01-01', new \DateTimeZone( "EDT" ) );
        $record   = $sut( [ 'datetime' => $datetime ] );
        $this->assertArrayNotHasKey( 'datetime', $record );
        $this->assertArrayHasKey( 'timestamp', $record );
        $this->assertIsString( $record[ 'timestamp' ] );
        $this->assertEquals( '2019-01-01T00:00:00.000000-0500', $record[ 'timestamp' ] );
    }


    /**
     * @depends testConstructor
     */
    public function testRecordHandlesTimestampWhenNotDateTime( Sut $sut )
    {
        $datetime = '2019-08-16';
        $record   = $sut( [ 'datetime' => $datetime ] );
        $this->assertArrayNotHasKey( 'datetime', $record );
        $this->assertArrayHasKey( 'timestamp', $record );
        $this->assertIsString( $record[ 'timestamp' ] );
        $this->assertEquals( $datetime, $record[ 'timestamp' ] );
    }


    public function testCustomDatetimeFormatter()
    {
        $sut = new Sut( $this->_env, $this->_service, 'c' );
        $datetime = new \DateTimeImmutable( '2019-01-01', new \DateTimeZone( "EDT" ) );
        $record   = $sut( [ 'datetime' => $datetime ] );
        $this->assertArrayNotHasKey( 'datetime', $record );
        $this->assertArrayHasKey( 'timestamp', $record );
        $this->assertIsString( $record[ 'timestamp' ] );
        $this->assertEquals( '2019-01-01T00:00:00-05:00', $record[ 'timestamp' ] );
    }
}
