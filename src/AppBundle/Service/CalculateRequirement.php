<?php
/**
 *
 * Simon Brown <uptoeleven@gmail.com>
 */

namespace AppBundle\Service;

use AppBundle\Entity\Boat;

/**
 * Class CalculateRequirement
 * @package AppBundle\Service
 */
class CalculateRequirement
{
    /**
     * @var Boat Boat
     */
    public $boat;

    /**
     * CalculateRequirement constructor.
     * @param Boat $boat
     */
    public function __construct(Boat $boat = null)
    {
        $this->boat = $boat;
    }

    public function setBoat(Boat $boat)
    {
        $this->boat = $boat;
        return $this;
    }

    public function generateSpeedPowerCurve()
    {
        $maxSpeed = 30;
        // plane speed
        $nominalHullSpeed = $this->calculateHullSpeed();
        $speedPowerCurve = [];

        //generate first section of curve
        for ($i=1; $i<=$nominalHullSpeed; $i++) {
            $speedPowerCurve[] = [
                'speed' => $i,
                'power' => round($this->calculateHorsePower($i), 2)
            ];
        }

        //add in the nominal hull speed at the appropriate point
        $speedPowerCurve[] = [
            'speed' => round($nominalHullSpeed, 2) . ' (Nominal Hull Speed)',
            'power' => round($this->calculateHorsePower($nominalHullSpeed), 2)
        ];

        //complete the curve
        for ($i=(intval($nominalHullSpeed) + 1); $i<=$maxSpeed; $i++) {
            $speedPowerCurve[] = [
                'speed' => $i,
                'power' => round($this->calculateHorsePower($i), 2)
            ];
        }
        return $speedPowerCurve;
    }

    /**
     * Calculate HP
     *
     * @param $kt
     * @return float|int
     */
    public function calculateHorsePower($kt)
    {
        return ($this->calculateDisplacementInLBS() / 1000) *
            pow($kt / ($this->calculateWymanCoefficient() * sqrt($this->convertToFeet())), 3);
    }

    /**
     * Calculate SL Ratio
     *
     * @return float|mixed
     */
    public function calculateSlRatio()
    {
        return ($this->boat->getButtockAngle() * -0.2) + 2.9;
    }

    /**
     * Calculate Hull Speed
     *
     * @return float|mixed
     */
    public function calculateHullSpeed()
    {
        return $this->calculateSlRatio() * (sqrt($this->convertToFeet()));
    }

    /**
     * Convert to Feet
     *
     * @return mixed
     */
    public function convertToFeet()
    {
        switch ($this->boat->getLengthUnit()) {
            case 'inch' :
                return $this->boat->getHullLength() * 0.0833333;
                break;
            case 'metre' :
                return $this->boat->getHullLength() * 3.28084;
                break;
            default:
                break;
        }
        return $this->boat->getHullLength();
    }

    /**
     * Calculate Wyman Coefficient
     *
     * @param null $slRatio
     * @return float|mixed|null
     */
     public function calculateWymanCoefficient($slRatio = null)
    {
        $slRatio = (is_null($slRatio)) ? $this->calculateSlRatio() : $slRatio;
        return 0.8 + (0.17 * $slRatio);
    }

    /**
     * Get Displacement
     *
     * @return float|mixed
     */
    public function calculateDisplacementInLBS()
    {
        if ($this->boat->getDispUnit() != 'lbs') {
            switch ($this->boat->getDispUnit()) {
                case 'kilograms' :
                    return $this->boat->getDisplacement() * 2.20462;
                    break;
                case 'grams' :
                    return $this->boat->getDisplacement() * 0.00220462;
                    break;
                default:
                    break;
            }
        }
        return $this->boat->getDisplacement();
    }
}