<?php
namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, array(
                'required' => false,
                'label' => 'Id'
            ))
            ->add('name', TextType::class, array(
                'required' => false,
                'label' => 'Name contains'
            ))
            ->add('authors', EntityType::class, array(
                'class' => 'AppBundle:Author',
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => true,
                'required' => false
            ))
            ->add('description', TextType::class, array(
                'required' => false,
                'label' => 'Description contains'
            ))
            ->add('year', IntegerType::class, array(
                'required' => false,
                'label' => 'Year'

            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Search'
            ))
        ;
    }
}