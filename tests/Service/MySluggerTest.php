<?php


namespace App\Tests\Util;

use App\Service\MySlugger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Si on on veut et que l'on peut tester un service sans passer le conteneur
 */
class MySluggerTest extends TestCase
{
    
    // public function testMySlugger()
    // {
    //     self::bootKernel();

    //     $container = self::$container;
    //     $mySlugger = $container->get(MySlugger::class);
    //     $result = $mySlugger->toSlug('Jambon Beurre');

    //     // assert that your calculator added the numbers correctly!
    //     $this->assertEquals('jambon-beurre', $result);
    // }

    public function testMySlugger()
    {
        // Instancier notre MySlugger
        // à la mano (sans passer par le conteneur de Service)

        // On a donc besoin de la classe AsciiSlugger
        $asciiSlugger = new AsciiSlugger();

        // UPPER
        // On peut donner les arguments au constructeur
        $mySlugger = new MySlugger($asciiSlugger, false);

        // Slugifier une chaine
        $slug = $mySlugger->toSlug('Hello World');

        // Vérifier qu'elle est correcte
        $this->assertEquals('Hello-World', $slug);
    }

    public function testMySluggerToLower()
    {
        // Instancier notre MySlugger
        // à la mano (sans passer par le conteneur de Service)

        // On a donc besoin de la classe AsciiSlugger
        $asciiSlugger = new AsciiSlugger();

        // LOWER
        // On peut donner les arguments au constructeur
        $mySlugger = new MySlugger($asciiSlugger, true);

        // Slugifier une chaine
        $slug = $mySlugger->toSlug('Hello World');

        // Vérifier qu'elle est correcte
        $this->assertEquals('hello-world', $slug);

    }
}