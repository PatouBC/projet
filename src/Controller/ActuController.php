<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Actu;
use App\Form\ActuType;
use App\Repository\ActuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actu", host="projet.do")
 */
class ActuController extends AbstractController
{
    /**
     * @Route("/", name="actu_index", methods={"GET"})
     */
    public function index(ActuRepository $actuRepository): Response
    {
        return $this->render('actu/index.html.twig', [
            'actus' => $actuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="actu_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $actu = new Actu();
        $form = $this->createForm(ActuType::class, $actu);
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
                $actu->setImage($image);
            }else{
                $actu->setImage(null);
            }

            $entityManager->persist($actu);
            $entityManager->flush();

            return $this->redirectToRoute('actu_index');
        }

        return $this->render('actu/new.html.twig', [
            'actu' => $actu,
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
     * @Route("/{id}", name="actu_show", methods={"GET"})
     */
    public function show(Actu $actu): Response
    {
        return $this->render('actu/show.html.twig', [
            'actu' => $actu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="actu_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Actu $actu): Response
    {
        $form = $this->createForm(ActuType::class, $actu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $image = $actu->getImage();
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
                $actu->setImage($image);
            }
            if(empty($image->getId())&& !$file)
            {
                $actu->setImage(null);
            }
            $entityManager->flush();

            return $this->redirectToRoute('actu_index', [
                'id' => $actu->getId(),
            ]);
        }

        return $this->render('actu/edit.html.twig', [
            'actu' => $actu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="actu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Actu $actu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actu->getId(), $request->request->get('_token'))) {
            $image = $actu->getImage();
            if($image){
                $this->removeFile($image->getPath());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('actu_index');
    }
    /**
     * @Route("/{id}", name="actu_image_delete", methods={"POST"})
     */
    public function deleteImg(Request $request, Actu $actu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $actu->getImage();
            $this->removeFile($image->getPath());
            $actu->setImage(null);
            $entityManager->remove($image);
            $entityManager->persist($actu);
            $entityManager->flush();
        }
        return $this->redirectToRoute('actu_edit', array('id'=>$actu->getId()));
    }
    private function removeFile($path)
    {
        if(file_exists($path))
        {
            unlink($path);
        }
    }

}
