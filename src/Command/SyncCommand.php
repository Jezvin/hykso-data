<?php

namespace App\Command;


use App\Entity\Coach;
use App\Entity\Punch;
use App\Entity\Round;
use App\Entity\Session;
use App\Entity\Sport;
use App\Repository\PunchRepository;
use App\Repository\RoundRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class SyncCommand extends Command
{

    protected static $defaultName = 'app:sync';

    /** @var EntityManager */
    private $em;

    /** @var SessionRepository */
    private $sessionRepo;

    /** @var RoundRepository */
    private $roundRepo;

    /** @var PunchRepository */
    private $punchRepo;

    /** @var \App\Repository\CoachRepository|\Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository  */
    private $coachRepo;

    /** @var \App\Repository\SportRepository|\Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository  */
    private $sportRepo;

    /** @var Coach[] */
    private $coaches;

    /** @var Sport[] */
    private $sports;

    /**
     * SyncCommand constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
        $this->sessionRepo = $this->em->getRepository(Session::class);
        $this->roundRepo = $this->em->getRepository(Round::class);
        $this->punchRepo = $this->em->getRepository(Punch::class);
        $this->coachRepo = $this->em->getRepository(Coach::class);
        $this->sportRepo = $this->em->getRepository(Sport::class);

    }


    protected function configure()
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'The folder containing the files');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $folder = $input->getArgument('path');
        $finder = new Finder();
        $finder->files()->in($folder)->name("*.csv");
        $progressBar = new ProgressBar($output, $finder->count());

        $coaches = $this->coachRepo->findAll();
        foreach ($coaches as $coach) {
            $this->coaches[strtoupper(substr($coach->getName(),0,1))] = $coach;
        }

        $sports = $this->sportRepo->findAll();
        foreach ($sports as $sport) {
            $this->sports[$sport->getInitials()] = $sport;
        }

        foreach ($finder as $file) {
            $this->importCsv($file->getRealPath());
            $progressBar->advance();
            $this->em->flush();

        }
        return 0;
    }

    const DESC_DATE = 0;
    const DESC_DURATION = 1;
    const DESC_ROUNDS = 2;
    const DESC_TYPE = 3;
    const DESC_NAME = 4;
    const DESC_DESCRIPTION = 5;

    const PUNCH_DATE = 0;
    const PUNCH_EVENT = 1;
    const PUNCH_TIME = 2;
    const PUNCH_ROUND = 3;
    const PUNCH_ROUNDTIME = 4;
    const PUNCH_HAND = 5;
    const PUNCH_TYPE = 6;
    const PUNCH_VELOCITY = 7;
    const PUNCH_INTENSITY = 8;

    /**
     * @param string $file
     */
    private function importCsv($file)
    {
        if (($fp = fopen($file, "r")) !== FALSE) {
            // ignore first line
            $row = fgetcsv($fp, 1000, ",");
            $description = fgetcsv($fp, 1000, ",");
            $date = \DateTime::createFromFormat("d/m/Y H:i:s", $description[self::DESC_DATE]);

            $session = $this->sessionRepo->findOneByDate($date);
            if (!$session) {
                $session = new Session();
                $session->setDate($date);
                $this->em->persist($session);
            }
            $session->setName($description[self::DESC_NAME]);
            $session->setDescription($description[self::DESC_DESCRIPTION]);
            $session->setDuration($description[self::DESC_DURATION]);

            $sport = null;
            $coaches = [];

            $name = strtolower($session->getName());

            // liaison au sport et au coach
            if (strpos($name,"session") === false) {
                // cas spécifiques
                if (strpos($name,"mma") !== false) {
                    $sport = $this->sports["M"];
                } elseif (strpos($name,"speed bag") !== false) {
                    $sport = $this->sports["SB"];
                } elseif (strpos($name,"bag") !== false) {
                    $sport = $this->sports["B"];
                } elseif (strpos($name,"rounds") !== false) {
                    $sport = $this->sports["S"];
                } elseif (strpos($name,"strike") !== false) {
                    $sport = $this->sports["S"];
                } elseif (strpos($name,"fast") !== false) {
                    $sport = $this->sports["B"];
                } elseif (strpos($name,"30 min") !== false) {
                    $sport = $this->sports["B"];
                }

                // coaches
                if (strpos($name,"lily") !== false) {
                    $coaches[] = $this->coaches["L"];
                }
                if (strpos($name,"coco") !== false) {
                    $coaches[] = $this->coaches["C"];
                }
                if (strpos($name,"yovan") !== false) {
                    $coaches[] = $this->coaches["Y"];
                }
                if (strpos($name,"arnaud") !== false) {
                    $coaches[] = $this->coaches["A"];
                }
            } else {
                // analyse de la description
                $data = explode("-",$session->getDescription());
                if (isset($this->sports[strtoupper(trim($data[0]))])) {
                    $session->setSport($this->sports[strtoupper(trim($data[0]))]);
                }
                if (count($data)>1) {
                    if ($data[1]=="all") {
                        $coaches[] = $this->coaches["L"];
                        $coaches[] = $this->coaches["C"];
                        $coaches[] = $this->coaches["Y"];
                        $coaches[] = $this->coaches["A"];
                    } else {
                        $initial = strtoupper(substr($data[1],0,1));
                        if (isset($this->coaches[$initial])) {
                            $coaches[] = $this->coaches[$initial];
                        }
                    }
                }
            }

            if (!!$sport) {
                $session->setSport($sport);
            }
            if (count($coaches)>0) {
                foreach($coaches as $coach) {
                    $session->addCoach($coach);
                }
            }

            // liste des coups
            while (($row = fgetcsv($fp, 1000, ",")) !== FALSE) {
                if (count($row) > 3) {
                    switch ($row[self::PUNCH_EVENT]) {
                        case "Round start":
                            // nouveau round :
                            $number = $row[self::PUNCH_ROUND];
                            $round = $this->roundRepo->findOneBy(["session" => $session, "number" => $number]);
                            if (!$round) {
                                $round = new Round();
                                $round->setSession($session);
                                $round->setNumber($number);
                                $this->em->persist($round);
                            } else {
                                // on supprime tous ses punches
                                foreach ($round->getPunches() as $punch) {
                                    $this->em->remove($punch);
                                }
                            }
                            break;
                        case "Punch":
                            $roundTime = str_ireplace(",",".",$row[self::PUNCH_ROUNDTIME]);
                            $velocity = str_ireplace(",",".",$row[self::PUNCH_VELOCITY]);
                            $intensity = str_ireplace(",",".",$row[self::PUNCH_INTENSITY]);

//                            $punch = $this->punchRepo->findOneBy(["round" => $round, "time" => $roundTime]);
//                            if (!$punch) {
                                $punch = new Punch();
                                $punch->setRound($round);
                                $punch->setTime($roundTime);
                                $this->em->persist($punch);
//                            }
                            $punch->setHand($row[self::PUNCH_HAND]);
                            $punch->setType($row[self::PUNCH_TYPE]);
                            $punch->setVelocity($velocity);
                            $punch->setIntensity($intensity);
                            break;
                    }
                }
            }


            fclose($fp);
        }
    }


}