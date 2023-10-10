<?php

namespace App\Form;

use App\Form\SongType;
use App\Model\SongModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SongModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('song', SongType::class, [
                
            ])
            ->add('newArtist', TextType::class, [
                'label' => 'Create Artist',
                'attr' => ['placeholder' => "if artist doesn't exist in the list above, you can create one or more here (for more than one artist just separate them with a ; (like: Artit1; Artist2)"],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SongModel::class,
        ]);
    }
}
