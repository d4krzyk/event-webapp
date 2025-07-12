<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formularz do obsługi tworzenia i edycji kategorii wydarzeń.
 */
class CategoryType extends AbstractType
{
    /**
     * Buduje formularz kategorii.
     *
     * @param FormBuilderInterface $builder Budowniczy formularza
     * @param array $options Opcje formularza
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('slug')
        ;
    }

    /**
     * Konfiguruje domyślne opcje formularza.
     *
     * @param OptionsResolver $resolver Rozwiązywacz opcji
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
