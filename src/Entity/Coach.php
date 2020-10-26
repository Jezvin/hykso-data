<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoachRepository")
 */
class Coach
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Session", mappedBy="coaches")
     */
    private $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->addCoach($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeCoach($this);
        }

        return $this;
    }


    public function getPunchesCount() {
        return array_reduce($this->sessions->toArray(), function($carry, Session $item) {
            return $carry + $item->getPunchesCount();
        });
    }

    public function getAverageVelocity() {
        $velocities = array_reduce($this->sessions->toArray(), function($carry, Session $item) {
            return $carry + $item->getAverageVelocity();
        });
        return $velocities / $this->sessions->count();
    }

    public function getAverageIntensity() {
        $intensities = array_reduce($this->sessions->toArray(), function($carry, Session $item) {
            return $carry + $item->getIntensity();
        });
        return $intensities / $this->sessions->count();
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
        /** @var Session $session */
        foreach ($this->sessions as $session) {
            $countDetail = $session->getCountDetail();

            foreach ($countDetail as $hand => $types) {
                foreach ($types as $type => $nb) {
                    $count[$hand][$type] += $nb;
                }
            }
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
        if ($this->sessions->isEmpty())
            return $velocities;

        /** @var Session $session */
        foreach ($this->sessions as $session) {
            $detailSession = $session->getAverageVelocityDetail();
            foreach ($detailSession as $hand => $types) {
                foreach ($types as $type => $velo) {
                    $velocities[$hand][$type] += $velo;
                }
            }
        }
        foreach ($velocities as $hand => $types) {
            foreach ($types as $type => $velo) {
                $velocities[$hand][$type] /= $this->sessions->count();
            }
        }
        return $velocities;
    }
}
