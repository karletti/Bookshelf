<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Form\SearchType;
use AppBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class BookController extends Controller
{

    /**
     * @Route("/books", name="book_list")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()){

            $data = $form->getData();


            $books = $em->getRepository('AppBundle:Book')->findByFilter($data);
        } else {
            $books = $em->getRepository('AppBundle:Book')->findAll();
        }

        return $this->render('book/list.html.twig', array(
            'books' => $books, 'form' => $form->createView()));
    }

    /**
     * @Route("/books/create", name="book_create")
     */
    public function createAction(Request $request)
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($book);

            $em->flush();
            return $this->redirectToRoute('book_list');
        }

        return $this->render('create.html.twig', array(
            'form' => $form->createView(),
            'object_class' => 'Book'
        ));
    }

    /**
     * @Route("/books/{id}", name="book_edit")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository('AppBundle:Book')->find($id);

        if(!$book){
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }


        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            return $this->redirectToRoute('book_list');
        }

        return $this->render('edit.html.twig', array(
            'form' => $form->createView(),
            'object_class' => 'Book'
        ));
    }

    /**
     * @Route("/books/delete/{id}", name="book_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository('AppBundle:Book')->find($id);

        if(!$book){
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('delete')->isClicked()){

                $em->remove($book);
                $em->flush();
            }

            return $this->redirectToRoute('book_list');
        }

        return $this->render('delete.html.twig', array(
            'form' => $form->createView(),
            'object_id' => $id,
            'object_class' => 'Book'
        ));
    }
}