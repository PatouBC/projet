<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/", name="accueilClass")
 */
Class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }
    /**
     * @Route("/admin", name="indexAdmin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAdmin()
    {
        return $this->render('default/index.html.twig');
    }
}