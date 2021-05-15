<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class qui utiliser le service Slugger
 */
class MySlugger
{
    /**
    * @var SluggerInterface $slugger Le slugger de Symfony
    */
    private $slugger;
    /**
     * @var bool $toLower Paramètre de configuration pour passer en minuscule
     */
    private $toLower;

    public function __construct(SluggerInterface $slugger, $toLower)
    {
        $this->slugger = $slugger;
        $this->toLower = $toLower;
    }

    public function toSlug($toSlug)
    {
        $slug = $this->slugger->slug($toSlug);

         // On lower ou pas ?
         if ($this->toLower) {
            return $slug->lower();
        }

        return $slug;
    }


    /**
     * Get $toLower Paramètre de configuration pour passer en minuscule
     *
     * @return  bool
     */ 
    public function getToLower()
    {
        return $this->toLower;
    }

    /**
     * Set $toLower Paramètre de configuration pour passer en minuscule
     *
     * @param  bool  $toLower  $toLower Paramètre de configuration pour passer en minuscule
     *
     * @return  self
     */ 
    public function setToLower(bool $toLower)
    {
        $this->toLower = $toLower;

        return $this;
    }
}