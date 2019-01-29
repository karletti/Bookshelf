<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use AppBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends Controller
{
    /**
     * @Route("/authors", name="author_list")
     */
    public function listAction()
    {
        $authors = $this->getDoctrine()
            ->getRepository('AppBundle:Author')
            ->findAll();

        return $this->render('author/list.html.twig', array('authors' => $authors));
    }

    /**
     * @Route("/authors/create", name="author_create")
     */
    public function createAction(Request $request)
    {
        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($author);

            $em->flush();
            return $this->redirectToRoute('author_list');
        }

        return $this->render('create.html.twig', array(
            'form' => $form->createView(),
            'object_class' => 'Author'
        ));
    }

    /**
     * @Route("/authors/{id}", name="author_edit")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $author = $em->getRepository('AppBundle:Author')->find($id);

        if(!$author){
            throw $this->createNotFoundException(
                'No author found for id '.$id
            );
        }

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            return $this->redirectToRoute('author_list');
        }

        return $this->render('edit.html.twig', array(
            'form' => $form->createView(),
            'object_class' => 'Author'
        ));
    }

    /**
     * @Route("/authors/delete/{id}", name="author_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $author = $em->getRepository('AppBundle:Author')->find($id);

        if(!$author){
            throw $this->createNotFoundException(
                'No Author found for id '.$id
            );
        }

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('delete')->isClicked()){

                $em->remove($author);
                $em->flush();
            }

            return $this->redirectToRoute('book_list');
        }

        return $this->render('delete.html.twig', array(
            'form' => $form->createView(),
            'object_id' => $id,
            'object_class' => 'Author'
        ));
    }
}