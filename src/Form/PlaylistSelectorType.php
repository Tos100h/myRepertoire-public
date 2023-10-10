<?php

namespace App\Form;

use App\Entity\Song;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaylistSelectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $options['user']->getId();
        $builder
            ->add('playlists', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (PlaylistRepository $pr) use ($userId) {
                    return $pr->createQueryBuilder('p')
                        ->andWhere('p.user = :user')
                        ->setParameter('user', $userId);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
            'user' => null,
        ]);
    }
}
