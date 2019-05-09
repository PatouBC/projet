<?php


namespace App\ApiController;

use App\Repository\ImageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;



class ImageController extends AbstractFOSRestController
{
    /**
     * Retrieves a collection of Image resource
     * @Route("/image", name="imagelist_api", methods={ "GET" })
     * @Rest\View()
     */
    public function index(ImageRepository $imageRepository): View
    {
        $images = $imageRepository->findAll();
        return View::create($images, Response::HTTP_OK);
    }
}