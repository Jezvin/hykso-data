<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoundRepository")
 */
class Round
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Session", inversedBy="rounds")
     */
    private $session;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Punch", mappedBy="round", orphanRemoval=true)
     */
    private $punches;

    public function __construct()
    {
        $this->punches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collection|Punch[]
     */
    public function getPunches(): Collection
    {
        return $this->punches;
    }

    public function addPunch(Punch $punch): self
    {
        if (!$this->punches->contains($punch)) {
            $this->punches[] = $punch;
            $punch->setRound($this);
        }

        return $this;
    }

    public function removePunch(Punch $punch): self
    {
        if ($this->punches->contains($punch)) {
            $this->punches->removeElement($punch);
            // set the owning side to null (unless already changed)
            if ($punch->getRound() === $this) {
                $punch->setRound(null);
            }
        }

        return $this;
    }

    public function getAverageVelocity() {
        $velocities = array_reduce($this->punches->toArray(), function($carry, Punch $item) {
            return $carry + $item->getVelocity();
        });
        if ($this->punches->isEmpty())
            return 0;
        return $velocities / $this->punches->count();
    }

    public function getCountDetail() {
        $count = [
            Punch::HAND_LEFT => [
                Punch::TYPE_DIRECT => 0,
                Punch::TYPE_POWER => 0,
            ],
            Punch::HAND_RIGHT => [
                Punch::TYPE_DIRECT => 0,
                Punch::TYPE_POWER => 0,
            ]
        ];
        /** @var Punch $punch */
        foreach ($this->punches as $punch) {
            $count[$punch->getHand()][$punch->getType()]++;
        }
        return $count;
    }

    public function getAverageVelocityDetail() {
        $velocities = [
            Punch::HAND_LEFT => [
                Punch::TYPE_DIRECT => 0,
                Punch::TYPE_POWER => 0,
            ],
            Punch::HAND_RIGHT => [
                Punch::TYPE_DIRECT => 0,
                Punch::TYPE_POWER => 0,
            ]
        ];
        /** @var Punch $punch */
        foreach ($this->punches as $punch) {
            $velocities[$punch->getHand()][$punch->getType()] += $punch->getVelocity();
        }
        $count = $this->getCountDetail();

        $average=[];
        foreach ($velocities as $hand => $types) {
            $average[$hand] = [];
            foreach ($types as $type => $velocity) {
                $average[$hand][$type] = 0;
                if ($count[$hand][$type] > 0) {
                    $average[$hand][$type] = $velocities[$hand][$type] / $count[$hand][$type] ;
                }
            }
        }
        return $average;
    }

    public function getIntensity() {
        return array_reduce($this->punches->toArray(), function($carry, Punch $item) {
            return $carry + $item->getIntensity();
        });
    }
}
