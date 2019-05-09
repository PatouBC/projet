<?php

namespace App\ApiController;

use App\Form\EventType;
use App\Repository\EventRepository;
use App\Entity\Event;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class EventController extends AbstractFOSRestController
{
    /**
     * Retrieves a collection of Event resource
     * @Rest\Get("/", name="eventlist_api")
     * @Rest\View()
     */
     public function index(EventRepository $eventRepository): View
     {
         $events = $eventRepository->findAll();
         return View::create($events, Response::HTTP_OK);
     }

      /**
          * @Rest\Post("/new", name="eventcreate_api")
          */
         public function create(Request $request): View
         {
             $event = new Event();
             $event->setTitle($request->get('title'));
             $event->setDescription($request->get('description'));
             $em = $this->getDoctrine()->getManager();
             $em->persist($event);
         $em->flush();
         return View::create($event, Response::HTTP_CREATED);
     }

     /**
      * @Rest\Delete("/{id}", name="eventdelete_api")
      */
     public function delete(Event $event): View
     {
         if($event)
         {
             $em=$this->getDoctrine()->getManager();
             $em->remove($event);
                         $em->flush();
                     }
                     return View::create([], Response::HTTP_NO_CONTENT);
                 }

                 /**
                  * @Rest\Put("/{id}", name="eventedit_api")
                  */
                 public function edit(Request $request, Event $event)
     {
         if($event){
             $event->setTitle($request->get('title'));
             $event->setDescription($request->get('description'));
             $em = $this->getDoctrine()->getManager();
             $em->persist($event);
             $em->flush();
         }
         return View::create($event, Response::HTTP_OK);
     }

         /**
          * @Rest\Patch("/{id}", name="eventpatch_api")
          */
         public function patch(Request $request, Event $event)
         {
             if($event){
                 $form = $this->createForm(EventType::class, $event);
                 $form->submit($request->request->all(), false);
                 $em = $this->getDoctrine()->getManager();
                 $em->persist($event);
                 $em->flush();
             }
         return View::create($event, Response::HTTP_OK);
     }
}