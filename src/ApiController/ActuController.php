<?php

namespace App\ApiController;

use App\Entity\Actu;
use App\Form\ActuType;
use App\Repository\ActuRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route(path="/actu", host="api.projet.do")
 */
class ActuController extends AbstractFOSRestController
{
    /**
     * Retrieves a collection of Actu resource
     * @Rest\Get(path="/", name="actulist_api")
     * @Rest\View()
     */
    public function index(ActuRepository $actuRepository): View
    {
        $actus = $actuRepository->findAll();
        return View::create($actus, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     *     path="/{id}",
     *     name="actushow_api"
     * )
     */
    public function show(Actu $actu): View
    {
        return View::create($actu,  Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     *     path="/new",
     *     name="actucreate_api"
     * )
     */
    public function create(Request $request): View
    {
        $actu = new Actu();
        $actu->setTitle($request->get('title'));
        $actu->setDescription($request->get('description'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($actu);
        $em->flush();
        return View::create($actu,Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete(
     *     path="/{id}",
     *     name="actudelete_api"
     * )
     */
    public function delete(Actu $actu): View
    {
        if($actu)
        {
            $em=$this->getDoctrine()->getManager();
            $em->remove($actu);
            $em->flush();
        }
        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Put(
     *     path="/{id}",
     *     name="actuedit_api"
     * )
     */
    public function edit(Request $request, Actu $actu)
    {
        if($actu){
            $actu->setTitle($request->get('title'));
            $actu->setDescription($request->get('description'));
            $em=$this->getDoctrine()->getManager();
            $em->persist($actu);
            $em->flush();
        }
        return View::create($actu, Response::HTTP_OK);
    }
    /**
     * @Rest\Patch(
     *     path="/{id}",
     *     name="actupatch_api"
     * )
     */
    public function patch(Request $request, Actu $actu)
    {
        if($actu){
            $form = $this->createForm(ActuType::class, $actu);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($actu);
            $em->flush();
        }
        return View::create($actu, Response::HTTP_OK);
    }
}