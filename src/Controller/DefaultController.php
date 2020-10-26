<?php

namespace App\Controller;

use App\Repository\CoachRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /** @var SessionRepository */
    private $sessionRepository;

    /** @var CoachRepository  */
    private $coachRepository;

    /**
     * DefaultController constructor.
     * @param SessionRepository $sessionRepository
     */
    public function __construct(SessionRepository $sessionRepository, CoachRepository $coachRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->coachRepository = $coachRepository;
    }


    /**
     * @Route("/default", name="default")
     */
    public function index()
    {

        $sessions = $this->sessionRepository->findAll();
        $coaches = $this->coachRepository->findAll();
        return $this->render('default/index.html.twig', [
            "sessions" => $sessions,
            "coaches" => $coaches
        ]);
    }
}
