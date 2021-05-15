<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use function PHPSTORM_META\type;
use App\Repository\GenreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre *',
            ])
            ->add('releaseDate', DateType::class, [
                'widget' => 'choice',
                'label' => 'Release Date * :',
                'years' => range(date('Y'), date('Y') - 50),
                ])
            // Pas besoin de ces deux-là dans le formulaire
            // ->add('createdAt')
            // ->add('updatedAt')

            // Si on le laisse deviner, via "null" en second argument
            // Symfony règle toutes les options pour nous,
            // et notamment le 'multiple' => true
            // car genres = ToMany = ArrayCollection
            // Il nous reste juste le 'choice_label' à régler

            // Si on configure tout à la main,
            // on doit alors tout préciser nous-même
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'query_builder' => function (GenreRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },  
                // LE PLUS IMPORTANT POUR UNE COLLECTION (genres)
                'multiple' => true,
                // En checkboxes, meilleure UX
                'expanded' => true,
            ])
                

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
             'data_class' => Movie::class,
        ]);
    }
}
