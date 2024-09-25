<?php

namespace App\Form;

use App\Entity\TypeAbsence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class MainFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code_type_absence', TextType::class, [
                'label' => 'Code Type Absence',
                'required' => true,
            ])
            ->add('absence_color', TextType::class, [
                'label' => 'Couleur absence',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
            ]);
//            ->add('Denomination')
//            ->add('Active')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeAbsence::class,
        ]);
    }
}
