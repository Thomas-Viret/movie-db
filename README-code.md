mainController:



```php
<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        // 1. Directement dans le contrôleur
        // On crée un formulaire de login
        $form = $this->createForm(LoginType::class,
            // Valeurs par défaut des champs du form
            ['user_email' => 'toto@toto.com']
            // Options du formulaire
            // ['action' => 'http://toto.com/login']
        );

        // 2. Demande au formulaire d'inspecter la requête
        // et de faire ce qu'il y a à faire
        $form->handleRequest($request);

        // Le form est-il soumis et valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données du form
            $loginData = $form->getData();

            // Fait quelque chose => se connecter
            dd($loginData);

            // On redirige vers...
            // return $this->redirectToRoute(...)
        }

        return $this->render('main/index.html.twig', [
            // On envoie au template "une vue de formulaire" via createView()
            'form' => $form->createView(),
        ]);
    }
}

```


FormController:

```php
<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_email', EmailType::class, [
                // Supprime le required HTML5
                'required' => false,
                'label' => 'Email',
                'constraints' => [
                    // Non vide
                    new NotBlank(),
                    // Format E-mail valide
                    new Email(),
                ]
            ])
            ->add('user_password', PasswordType::class, [
                // Supprime le required HTML5
                'required' => false,
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'max' => 16,
                    ])
                ]
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

```