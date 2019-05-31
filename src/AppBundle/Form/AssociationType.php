<?php
/**
 * Created by PhpStorm.
 * User: bycrea
 * Date: 2019-05-02
 * Time: 15:22
 */

namespace AppBundle\Form;


use AppBundle\Entity\Assos;
use AppBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nom de l\'association'
            ))
            ->add('description', TextareaType::class)
            ->add('urlAssos', TextType::class, array(
                'label' => 'Lien http:// de l\'association'
            ))
            ->add('image', FileType::class, array(
                'required' => false
            ))
            ->add('contactInfo', TextType::class, array(
                'label' => 'Contact de l\'association',
                'required' => false
            ))
            ->add('categories', EntityType::class, array(
                'label' => 'Choisissez une ou plusieurs catégories',
                'class' => Category::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Assos::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_association';
    }
}
