<?php

namespace App\Form;

use App\Entity\Lecon;
use App\Entity\Cursus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeconType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la leçon'
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false
            ])
            ->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'nom',
                'label' => 'Cursus associé'
            ])
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de création',
                'required' => false, // Permet d'éviter une erreur si la date est vide
            ])
            ->add('isValidated', CheckboxType::class, [
                'label' => 'Validation de la leçon',
                'required' => false, // Permet de ne pas forcer la validation
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lecon::class,
        ]);
    }
}
