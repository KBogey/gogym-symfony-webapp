<?php

namespace App\Form;

use App\Entity\Category;
use App\Model\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
               'attr' =>  [
                   'placeholder' => 'Rechercher...'
               ]
            ])
            ->add('categories', EntityType::class, [
                'class' =>Category::class,
                'attr' => ['class' => 'd-inline-flex'],
                'choice_label' => 'name',
                'label_attr' => ['class' => 'mx-1'],
                'expanded' => true,
                'multiple' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}