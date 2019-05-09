<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Image;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('image')->get('file')->getData();
            if($file){
                $image = new Image();
                $fileName=$this->generateUniqueFileName().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('image_abs_path'),
                        $fileName
                    );
                }catch(FileException $e){

                }

                $image->setPath($this->getParameter('image_abs_path').'/'.$fileName);
                $image->setImgPath($this->getParameter('image_path').'/'.$fileName);
                $entityManager->persist($image);
                $event->setImage($image);
            }else{
                $event->setImage(null);
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $image = $event->getImage();
            $file = $form->get('image')->get('file')->getData();

            if($file)
            {

                $fileName=$this->generateUniqueFileName().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('image_abs_path'),
                        $fileName
                    );
                }catch(FileException $e){

                }
                $this->removeFile($image->getPath());

                $image->setPath($this->getParameter('image_abs_path').'/'.$fileName);
                $image->setImgPath($this->getParameter('image_path').'/'.$fileName);
                $entityManager->persist($image);
                $event->setImage($image);
            }
            if(empty($image->getId())&& !$file)
            {
                $event->setImage(null);
            }
            $entityManager->flush();

            return $this->redirectToRoute('event_index', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $image = $event->getImage();
            if($image){
                $this->removeFile($image->getPath());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index');
    }
    /**
     * @Route("/{id}", name="product_image_delete", methods={"POST"})
     */
    public function deleteImg(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $event->getImage();
            $this->removeFile($image->getPath());
            $event->setImage(null);

            $entityManager->remove($image);
            $entityManager->persist($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_edit', array('id'=>$event->getId()));
    }
    private function removeFile($path)
    {
        if(file_exists($path))
        {
            unlink($path);
        }
    }
}
