<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assos;
use AppBundle\Form\AssociationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssociationsController extends Controller
{
    /**
     * @Route("/associations", name="admin_associations")
     */
    public function indexAction(Request $request)
    {

        return $this->render('admin/associations/index.html.twig', [
            'title' => 'Bonjour les geeks'
        ]);
    }

    /**
     * @Route("/associations/create", name="admin_associations_create")
     */
    public function createAction(Request $request)
    {
        $association = new Assos();
        $form = $this->createForm(AssociationType::class, $association);

        return $this->render('admin/associations/create.html.twig', [
            'title' => 'create asso',
            'form' => $form->createView()
        ]);
    }
}
