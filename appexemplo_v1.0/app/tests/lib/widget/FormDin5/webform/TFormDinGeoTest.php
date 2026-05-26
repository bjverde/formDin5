<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinGeoTest extends TestCase
{
    private $geo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geo = new TFormDinGeo();
    }

    protected function tearDown(): void
    {
        $this->geo = null;
        parent::tearDown();
    }

    public function test_verticesSetAndGet()
    {
        $this->geo->setVertice(-15.123, -47.456);
        $vertices = $this->geo->getVertice();
        $this->assertCount(1, $vertices);
        $this->assertEquals(-15.123, $vertices[0]['latitude']);
        $this->assertEquals(-47.456, $vertices[0]['longitude']);

        $this->geo->zerarVertice();
        $this->assertCount(0, $this->geo->getVertice());
    }

    public function test_setVerticeDf()
    {
        $this->geo->setVerticeDf();
        $this->assertCount(4, $this->geo->getVertice());
    }

    public function test_isPointInQuadrilateral_Inside()
    {
        $this->geo->setVerticeDf();
        // Point is mathematically inside the square (-15.7801 to -15.8801, -47.9292 to -47.8292)
        $inside = $this->geo->isPointInQuadrilateral(-15.8301, -47.8792);
        $this->assertTrue($inside);
    }

    public function test_isPointInQuadrilateral_Outside()
    {
        $this->geo->setVerticeDf();
        // Point is far outside
        $inside = $this->geo->isPointInQuadrilateral(-16.0000, -48.0000);
        $this->assertFalse($inside);
    }

    public function test_isPointInQuadrilateral_ZeroVerticesException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertice zerado');
        $this->geo->isPointInQuadrilateral(-15.8301, -47.8792);
    }

    public function test_isPointInQuadrilateral_IncompleteException()
    {
        $this->geo->setVertice(-15.0, -47.0);
        $this->geo->setVertice(-15.1, -47.1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quadrilatero não está completo');
        $this->geo->isPointInQuadrilateral(-15.8301, -47.8792);
    }

    public function test_isPointInQuadrilateral_TooManyVerticesException()
    {
        $this->geo->setVertice(-15.0, -47.0);
        $this->geo->setVertice(-15.1, -47.1);
        $this->geo->setVertice(-15.2, -47.2);
        $this->geo->setVertice(-15.3, -47.3);
        $this->geo->setVertice(-15.4, -47.4);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('mais de 4 pontos');
        $this->geo->isPointInQuadrilateral(-15.8301, -47.8792);
    }

    public function test_isPointWithinRadius()
    {
        $refLat = -15.793889;
        $refLon = -47.882778; // Brasília
        $radius = 5000;       // 5 km

        // Very close point (~300m)
        $this->assertTrue($this->geo->isPointWithinRadius($refLat, $refLon, $radius, -15.795, -47.885));

        // Point extremely far away (Sao Paulo)
        $this->assertFalse($this->geo->isPointWithinRadius($refLat, $refLon, $radius, -23.55052, -46.633308));
    }
}
