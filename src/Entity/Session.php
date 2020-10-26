<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="session")
     */
    private $rounds;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Coach", inversedBy="sessions")
     */
    private $coaches;

    /**
     * @ORM\ManyToOne(targetEntity=Sport::class, inversedBy="sessions")
     */
    private $sport;

    public function __construct()
    {
        $this->rounds = new ArrayCollection();
        $this->coaches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds[] = $round;
            $round->setSession($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->contains($round)) {
            $this->rounds->removeElement($round);
            // set the owning side to null (unless already changed)
            if ($round->getSession() === $this) {
                $round->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Coach[]
     */
    public function getCoaches(): Collection
    {
        return $this->coaches;
    }

    public function addCoach(Coach $coach): self
    {
        if (!$this->coaches->contains($coach)) {
            $this->coaches[] = $coach;
        }

        return $this;
    }

    public function removeCoach(Coach $coach): self
    {
        if ($this->coaches->contains($coach)) {
            $this->coaches->removeElement($coach);
        }

        return $this;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(?Sport $sport): self
    {
        $this->sport = $sport;

        return $this;
    }

    public function getPunchesCount() {
        return array_reduce($this->rounds->toArray(), function($carry, Round $item) {
            return $carry + $item->getPunches()->count();
        });
    }

    public function getAverageVelocity() {
        $velocities = array_reduce($this->rounds->toArray(), function($carry, Round $item) {
            return $carry + $item->getAverageVelocity();
        });
        return $velocities / $this->rounds->count();
    }

    public function getIntensity() {
        return array_reduce($this->rounds->toArray(), function($carry, Round $item) {
            return $carry + $item->getIntensity();
        });
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
        /** @var Round $round */
        foreach ($this->rounds as $round) {
            $countDetail = $round->getCountDetail();

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
        /** @var Round $round */
        foreach ($this->rounds as $round) {
            $roundDetail = $round->getAverageVelocityDetail();
            foreach ($roundDetail as $hand => $types) {
                foreach ($types as $type => $velo) {
                    $velocities[$hand][$type] += $velo;
                }
            }
        }
        foreach ($roundDetail as $hand => $types) {
            foreach ($types as $type => $velo) {
                $velocities[$hand][$type] /= $this->rounds->count();
            }
        }
        return $velocities;
    }


}
