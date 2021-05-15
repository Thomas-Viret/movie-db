<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    // Format E-mail valide
                    new Email(),
                ]])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                        'user'=>"ROLE_USER",
                        'manager'=>"ROLE_MANAGER",
                        'admin'=>"ROLE_ADMIN",
                ],
                // LE PLUS IMPORTANT POUR UNE COLLECTION (genres)
                'multiple' => true,
                // Checkboxes
                'expanded' => true,
                
            ])
            // ->add('password', PasswordType::class, [
            //     // Si donnée vide (null), remplacer par chaine vide
            //     // @see https://symfony.com/doc/current/reference/forms/types/password.html#empty-data
            //     'empty_data' => '',
            //     'constraints' => [
            //         new NotBlank(),
            //         new Length([
            //             'min' => 4,
            //         ])
            //     ]
            // ])

            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                $id = $user->getId();
                
                
                //dump($user);
                
                if ($id !== null) {
                    
                    $form->add('password', PasswordType::class, [
                        // Si donnée vide (null), remplacer par chaine vide
                        // @see https://symfony.com/doc/current/reference/forms/types/password.html#empty-data
                        'empty_data' => '',
                        'attr' => [
                            'placeholder' => 'Laissez vide si inchangé',
                        ],
                         // @see https://symfony.com/doc/current/reference/forms/types/email.html#mapped
                        // Ce champ ne sera présent que dans la requête et dans le form
                        // mais PAS dans l'entité !
                        'mapped' => false,
                        
                    ]);
                }else{
                    
                    $form->add('password', PasswordType::class,[
                        // Si donnée vide (null), remplacer par chaine vide
                        // @see https://symfony.com/doc/current/reference/forms/types/password.html#empty-data
                        'empty_data' => '',
                        'constraints' => [
                            new NotBlank(),
                            new Length([
                                'min' => 4,
                            ])
                        ]
                    ]);
                }
            })
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
