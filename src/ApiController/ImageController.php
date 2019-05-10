<?php

namespace App\ApiController;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\Route(path="/image", host="api.projet.do")
 */
class ImageController extends AbstractFOSRestController
{
    /**
     * Retrieves a collection of Image resource
     * @Rest\Get(path="/", name="imagelist_api")
     * @Rest\View()
     */
    public function index(ImageRepository $imageRepository): View
    {
        $images = $imageRepository->findAll();
        return View::create($images, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     *     path="/{id}",
     *     name="actushow_api"
     * )
     */
    public function show(Image $image): View
    {
        return View::create($image, Response::HTTP_OK);

    }

    /**
     * @Rest\Post(
     *     path="/new",
     *     name="imagecreate_api"
     * )
     */
    public function create(Request $request): View
    {
        $image = new Image();
        $image->setPath($request->get('path'));
        $image->setImgpath($request->get('imgpath'));
        $image->setAlt($request->get('alt'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();
        return View::create($image, Response::HTTP_CREATED);
    }
    /**
     * @Rest\Delete(
     *     path="/{id}",
     *     name="imagedelete_api"
     * )
     */
    public function delete(Image $image): View
    {
        if($image)
        {
            $em=$this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }
        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Put(
     *     path="/{id}",
     *     name="imageedit_api"
     * )
     */
    public function edit(Request $request, Image $image): View
    {
        if($image){
            $image->setImgpath($request->get('imgpath'));
            $image->setPath($request->get('path'));
            $image->setAlt($request->get('alt'));
            $em=$this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
        }
        return View::create($image, Response::HTTP_OK);
    }

    /**
     * @Rest\Patch(
     *     path="/{id}",
     *     name="imagepatch_api"
     * )
     */
    public function patch(Request $request, Image $image)
    {
        if($image){
            $form=$this->createForm(ImageType::class, $image);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
        }
        return View::create($image, Response::HTTP_OK);
    }
}
