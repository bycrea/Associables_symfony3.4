<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Category;
use AppBundle\Form\AssociationType;
use AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends Controller
{
    /**
     * @Route("/categories", name="admin_categories")
     */
    public function indexAction(Request $request)
    {

        return $this->render('admin/categories/index.html.twig', [
            'title' => 'categories'
        ]);
    }

    /**
     * @Route("/categories/create", name="admin_categories_create")
     */
    public function createAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
        }

        return $this->render('admin/categories/create.html.twig', [
            'title' => 'create category',
            'form' => $form->createView()
        ]);
    }
}
