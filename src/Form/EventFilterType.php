<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class EventFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Tytuł (fragment)',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Wszystkie',
                'label' => 'Kategoria',
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => function (Location $location) {
                    return $location->getName() . ' | ' . $location->getCity();
                },
                'required' => false,
                'placeholder' => 'Wszystkie',
                'label' => 'Lokalizacja',
            ])

            ->add('startDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Data od',
            ])
            ->add('endDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Data do',
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'Status',
                'choices' => [
                    'Trwające' => 'ongoing',
                    'Przyszłe' => 'upcoming',
                    'Zakończone' => 'finished',
                ],
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Wszystkie',
            ])
            ->add('sortBy', ChoiceType::class, [
                'required' => false,
                'label' => 'Sortuj po',
                'choices' => [
                    'Popularność' => 'popularity',
                    'Data rozpoczęcia' => 'startDate',
                    'Nazwa' => 'title',
                ],
                'placeholder' => 'Domyślnie',
            ])
            ->add('sortOrder', ChoiceType::class, [
                'required' => false,
                'label' => 'Kierunek',
                'choices' => [
                    'Rosnąco' => 'ASC',
                    'Malejąco' => 'DESC',
                ],
                'placeholder' => 'Domyślnie (malejąco)',
            ]);;


    }
}
