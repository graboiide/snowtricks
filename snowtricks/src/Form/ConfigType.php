<?php

namespace App\Form;

use App\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbTricksDisplay',null,['label'=>'Nombre de figures affichable en page d\'accueil'])
            ->add('emailAdmin',EmailType::class,['label'=>'Email administrateur'])
            ->add('nbMessagesDisplay',null,['label'=>'Nombre de messages affichable sur la page d\'une figure'])
            ->add('protectLevel',ChoiceType::class,['label'=>'Niveau de protection','choices'=>['Public'=>0,'Protected'=>1]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }
}
