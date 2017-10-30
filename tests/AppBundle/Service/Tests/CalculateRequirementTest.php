<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\CalculateRequirement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Boat;

class CalculateRequirementTest extends TestCase
{
    private $boat;

    public function setup()
    {
        $this->boat = $this->getMockBuilder(Boat::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $this->boat->method('getHullLength')->willReturn(26);
        $this->boat->method('getButtockAngle')->willReturn(2);
        $this->boat->method('getDisplacement')->willReturn(2000);
    }

    public function testCalculateHorsePower()
    {
        $calc = new CalculateRequirement($this->boat);
        $hp = $calc->calculateHorsePower(10);
        $this->assertEquals(8.21,round($hp,2));
    }

    public function testCalculateSLRatio()
    {
        $calc = new CalculateRequirement($this->boat);
        $slRatio = $calc->calculateSlRatio();
        $this->assertEquals(2.5, round($slRatio, 2));
    }

    public function testCalculateHullSpeed()
    {
        $calc = new CalculateRequirement($this->boat);
        $hullSpeed = $calc->calculateHullSpeed();
        $this->assertEquals(12.75, round($hullSpeed, 2));
    }

    public function testCalculateWymanCoefficient()
    {
        $calc = new CalculateRequirement($this->boat);
        $slRatio = 2.5;
        $expected = 0.8 + (0.17 * $slRatio);
        $wymanCoefficient = $calc->calculateWymanCoefficient($slRatio);
        $this->assertEquals($expected, round($wymanCoefficient, 3));
    }

    public function testCalculateDisplacementInLBSfromKGS()
    {
        $kgBoat = $this->getMockBuilder(Boat::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $kgBoat->method('getDisplacement')->willReturn(1000);
        $kgBoat->method('getDispUnit')->willReturn('kilograms');
        $calc = new CalculateRequirement($kgBoat);
        $lbValue = $calc->calculateDisplacementInLBS();

        $this->assertEquals(2204.62, round($lbValue, 2));
    }

    public function testCalculateDisplacementInLBSfromGrams()
    {
        $gBoat = $this->getMockBuilder(Boat::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $gBoat->method('getDisplacement')->willReturn(1000000);
        $gBoat->method('getDispUnit')->willReturn('grams');
        $calc = new CalculateRequirement($gBoat);
        $lbValue = $calc->calculateDisplacementInLBS();
        $this->assertEquals(2204.62, round($lbValue, 2));
    }

    public function testConvertToFeetFromMetres()
    {
        $boat10m = $this->getMockBuilder(Boat::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $boat10m->method('getHullLength')->willReturn(10);
        $boat10m->method('getLengthUnit')->willReturn('metre');
        $calc = new CalculateRequirement($boat10m);
        $feetValue = $calc->convertToFeet();
        $this->assertEquals(32.81, round($feetValue, 2));
    }

    public function testConvertToFeetFromInches()
    {
        $boat312ft = $this->getMockBuilder(Boat::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $boat312ft->method('getHullLength')->willReturn(312);
        $boat312ft->method('getLengthUnit')->willReturn('inch');
        $calc = new CalculateRequirement($boat312ft);
        $inchesValue = $calc->convertToFeet($boat312ft);
        $this->assertEquals(26, round($inchesValue, 2));
    }

    public function testSetBoat()
    {
        $calc = new CalculateRequirement();
        $calcObj = $calc->setBoat($this->boat);
        $this->assertInstanceOf(CalculateRequirement::class, $calcObj);
    }

}
