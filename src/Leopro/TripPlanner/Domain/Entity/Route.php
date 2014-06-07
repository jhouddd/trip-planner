<?php

namespace Leopro\TripPlanner\Domain\Entity;

use Leopro\TripPlanner\Domain\Adapter\ArrayCollection;
use Leopro\TripPlanner\Domain\Exception\DateAlreadyUsedException;
use Leopro\TripPlanner\Domain\ValueObject\InternalIdentity;

class Route
{
    private $internalIdentity;
    private $name;
    private $legs;

    private function __construct(InternalIdentity $internalIdentity,
                                 $name)
    {
        $this->internalIdentity = $internalIdentity;
        $this->name = $name;
        $this->legs = new ArrayCollection();
    }

    public static function create($tripName)
    {
        return new self(
            new InternalIdentity,
            'first route for trip: ' . $tripName
        );
    }

    public function addLeg($date, $latitude, $longitude)
    {
        $leg = Leg::create($date, 'd-m-Y', $latitude, $longitude);

        $dateAlreadyUsed = function($key, $element) use($leg) {
            return $element->getDate() == $leg->getDate();
        };

        if ($this->legs->exists($dateAlreadyUsed)) {
            throw new DateAlreadyUsedException($date . ' already used');
        }

        $this->legs->add($leg);
    }

    public function getLegs()
    {
        return $this->legs;
    }

    public function getName()
    {
        return $this->name;
    }
} 