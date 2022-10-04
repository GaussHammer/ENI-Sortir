<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('infoSortie')
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'placeholder' => 'VILLES :',
                'label' => 'ville'
            ])
            ->add('lieu', ChoiceType::class, [
                'placeholder' => 'Choisir un lieu'
            ])

        ;

        $formModifier = function (FormInterface $form, Ville $ville = null){
            $lieu = (null === $ville) ? [] : $ville->getLieux();

            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choices' => $lieu,
                'choice_label' => 'nom',
                'placeholder' => 'ICI',
                'label' => 'Lieu'
            ]);
        };

        $builder->get('ville')->addEventListener(
          FormEvents::PRE_SUBMIT,
          function (FormEvent $event) use ($formModifier) {
              $ville = $event->getForm()->getData();
              $formModifier($event->getForm()->getParent(), $ville);
          }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
