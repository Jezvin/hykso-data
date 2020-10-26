<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PunchRepository")
 */
class Punch
{

    public const HAND_LEFT = "Left";
    public const HAND_RIGHT = "Right";

    public const TYPE_DIRECT = "Straight";
    public const TYPE_POWER = "Power";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $hand;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $velocity;

    /**
     * @ORM\Column(type="float")
     */
    private $intensity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Round", inversedBy="punches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $round;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setTime(float $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getHand(): ?string
    {
        return $this->hand;
    }

    public function setHand(string $hand): self
    {
        $this->hand = $hand;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getVelocity(): ?float
    {
        return $this->velocity * 3.6;
    }

    public function setVelocity(float $velocity): self
    {
        $this->velocity = $velocity;

        return $this;
    }

    public function getIntensity(): ?float
    {
        return $this->intensity;
    }

    public function setIntensity(float $intensity): self
    {
        $this->intensity = $intensity;

        return $this;
    }

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): self
    {
        $this->round = $round;

        return $this;
    }
}
