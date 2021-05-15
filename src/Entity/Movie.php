<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 * @UniqueEntity("title")
 * @see https://symfony.com/doc/current/reference/constraints/UniqueEntity.html
 * 
 * @ORM\HasLifecycleCallbacks()
 * @see https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-callbacks
 */
class Movie
{
    /**
     * Primary key
     * Auto-Increment
     * type INT
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("movies_read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank
     * @Groups("movies_read")
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * @Groups("movies_read")
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("movies_read")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("movies_read")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="movie" , cascade={"remove"})
     * @Groups("movies_read_item")
     */
    private $reviews;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, cascade={"persist"})
     * @ORM\OrderBy({"name"="ASC"})
     * @Assert\Count(min=1)
     * @Groups("movies_read")
     * 
     * Validation des objets en cascade
     * @see https://symfony.com/doc/current/reference/constraints/Valid.html
     * @Assert\Valid
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity=Casting::class, mappedBy="movie", cascade={"remove"})
     * @ORM\OrderBy({"creditOrder"="ASC"})
     * @Groups("movies_read_item")
     */
    private $castings;

    /**
     * @ORM\OneToMany(targetEntity=Team::class, mappedBy="movie")
     */
    private $teams;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("movies_read")
     */
    private $slug;


    public function __construct()
    {
        
        $this->createdAt = new \DateTime();
        $this->reviews = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->castings = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    // /!\ Pas de setter pour l'id !

    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of releaseDate
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * On peut envoyer une donnée directement transformée
     * 
     * Get the year of releaseDate
     * @Groups("movies_read")
     */ 
    public function getReleaseDateYear()
    {
        return $this->releaseDate->format('Y');
    }

    /**
     * Set the value of releaseDate
     *
     * @return  self
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }


    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            // On informe le côté qui détient
            $review->setMovie($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getMovie() === $this) {
                $review->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        // On vérifie que le genre qu'on veut associer ici
        // n'existe pas déjà dans la Collectuib des genres de ce film
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    /**
     * @return Collection|Casting[]
     */
    public function getCastings(): Collection
    {
        return $this->castings;
    }

    public function addCasting(Casting $casting): self
    {
        if (!$this->castings->contains($casting)) {
            $this->castings[] = $casting;
            $casting->setMovie($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->removeElement($casting)) {
            // set the owning side to null (unless already changed)
            if ($casting->getMovie() === $this) {
                $casting->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setMovie($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getMovie() === $this) {
                $team->setMovie(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    
}
