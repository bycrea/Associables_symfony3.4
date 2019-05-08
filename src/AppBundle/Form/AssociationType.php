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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociationType extends AbstractType // On hérite des méthodes de la class abstraite
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('urlAssos', TextType::class)
            ->add('image', FileType::class, ['required' => false])
            ->add('contactInfo', TextType::class, array(
                'required' => false
            ))
            ->add('categories', EntityType::class, array(
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