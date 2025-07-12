<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Participation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formularz do obsługi tworzenia i edycji uczestnictwa w wydarzeniu.
 */
class ParticipationType extends AbstractType
{
    /**
     * Buduje formularz uczestnictwa.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('joinedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('participant', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'id',
            ])
        ;
    }

    /**
     * Konfiguruje domyślne opcje formularza.
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
