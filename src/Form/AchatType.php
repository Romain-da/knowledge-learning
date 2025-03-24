<?php

namespace App\Form;

use App\Entity\Achat;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // Affiche l'email dans la liste déroulante
                'label' => 'Utilisateur',
            ])
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant de l\'achat',
                'divisor' => 100, // Facultatif, utile si la BDD stocke en centimes
            ])
            ->add('dateAchat', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'achat',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer l\'achat'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
        ]);
    }
}