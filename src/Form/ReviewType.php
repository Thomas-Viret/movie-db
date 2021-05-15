<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom *',
                'constraints' => new NotBlank(),
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                // Sera écrasé si on le renseigne aussi dans le Twig
                'help' => 'Ceci est un message d\'aide'
            ])
            ->add('Message', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 10]),
                ],
                'label' => 'Message *'
                 
            ])
            ->add('user_email', EmailType::class, [
                'label' => 'Email *',
                'constraints' => [
                    // Non vide
                    new NotBlank(),
                    // Format E-mail valide
                    new Email(),
                ]
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Age *',
                'constraints' => [
                    // Non vide
                    new NotBlank(),
                    new Range(['min' => 7, 'max' => 77]),
                    
                ]
            ])
            ->add('user_password', PasswordType::class, [
                'label' => 'Mot de passe *',
                'constraints' => [
                    new NotBlank(),
                    new Regex('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,16})$/'),
                    ],
                    'help' => 'Entre 8 et 16 caractères, une majuscule, une minuscule, un chiffre, $@%*+-_!',
                    // - de 8 à 16 caractères
                    // - au moins une lettre minuscule
                    // - au moins une lettre majuscule
                    // - au moins un chiffre
                    // - au moins un de ces caractères spéciaux: $ @ % * + - _ !
            ])
            ->add('url', UrlType::class, [
                // Protocole à ajouter à la saisie si protocole non précisé
                // @see https://symfony.com/doc/current/reference/forms/types/url.html#default-protocol
                'default_protocol' => 'https',
                'constraints' => [
                    // Si protocole saisi par l'utilisateur,
                    // quels sont les protocoles autorisés
                    // @see https://symfony.com/doc/current/reference/constraints/Url.html#protocols
                    new Url([
                        'protocols' => ['http', 'https'],
                    ]),
                    new NotBlank(),
                ],
                'label' => 'Site web'
            ])
            ->add('avis', ChoiceType::class, [
                'label' => 'avis',
                'placeholder' => 'Vous avez trouvé ce film...',
                'choices' => [
                        // Clé = libellé, Valeur = value de l'option
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('emotion', ChoiceType::class, [
                'label' => 'Ce film vous a fait',
                'choices' => [
                        'Rire'=>1,
                        'Pleurer'=>2,
                        'Réfléchir'=>3,
                        'Dormir'=>4,
                        'Rêver'=>5,
                    
                ],
                // Plusieurs choix possibles
                'multiple' => true,
                // Plusieurs éléments de form
                'expanded' => true,
            ])
            ->add('date', DateType::class, [
                'widget' => 'choice',
                // 'widget' => 'single_text',
                'label' => 'Vous avez vu ce film le :',
                'years' => range(date('Y'), date('Y') - 50),
            ])
            ->add('attachment', FileType::class,[
                'label' => 'Photo du cinéma ou de la salle ',
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Le fichier n\'est pas au bon format (formats acceptés: .png, .jpg, .jpeg)',
                    ]),
                ]
                ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            // 'data' => ['date' => new \DateTime()]
        ]);
    }
}
