<?php

namespace App\Form;

use App\Entity\Achat;
use App\Entity\Cursus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur'
            ])
            ->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'nom', // Assurez-vous que "nom" existe dans votre entité Cursus
                'label' => 'Cursus'
            ])
            ->add('dateAchat', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'achat'
            ])
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
        ]);
    }
}
