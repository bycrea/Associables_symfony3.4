<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assos;
use AppBundle\Form\AssociationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
            'title' => 'Associations'
        ]);
    }

    /**
     * @Route("/associations/edit/{id}", name="admin_associations_edit")
     */
    public function editAction($id)
    {

        return $this->render('admin/associations/edit.html.twig', [
            'title' => 'Associations'
        ]);
    }

    /**
     * @Route("/associations/create", name="admin_associations_create")
     */
    public function createAction(Request $request)
    {
        $association = new Assos();
        $form = $this->createForm(AssociationType::class, $association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $image */
            $image = $association->getImage();
            if($image) {
                $imageName = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_dir'),
                        $imageName
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur de téléchargement');
                    return $this->redirectToRoute('admin_associations_create');
                }

                $association->setImage($imageName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($association);
            $entityManager->flush();

            $this->addFlash('success', $association->getName().' à bien été ajouté.');
            return $this->redirectToRoute('admin_associations');
        }

        return $this->render('admin/associations/create.html.twig', [
            'title' => 'Creation assos',
            'form' => $form->createView()
        ]);
    }
}
