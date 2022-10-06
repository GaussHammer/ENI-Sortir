<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    private EntityManagerInterface $em;

    public  function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }

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
                'placeholder' => 'VILLES :'
            ]);

        $formModifier = function (FormInterface $form, Ville $ville = null){
            $form->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'placeholder' => 'VILLES :',
                'label' => 'ville',
                'data'=>$ville
            ]);

            $lieux = (null === $ville) ? [] : $ville->getLieux();

            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choices' => $lieux,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un lieu',
                'label' => 'Lieu'
            ]);
        };

       $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                $sortie = $event->getData();
                $lieu = $sortie->getLieu() ? $sortie->getLieu() : null;
                $ville = $lieu ? $lieu->getVille() : null;

                $formModifier($event->getForm(), $ville);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $ville = $this->em->getRepository(Ville::class)->find($event->getData()['ville']);
                $formModifier($event->getForm(), $ville);
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
