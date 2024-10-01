<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $reversed = null;

    #[ORM\Column]
    private ?bool $find = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    /**
     * @var Collection<int, Games>
     */
    #[ORM\ManyToMany(targetEntity: Games::class, inversedBy: 'cards')]
    private Collection $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isReversed(): ?bool
    {
        return $this->reversed;
    }

    public function setReversed(bool $reversed): static
    {
        $this->reversed = $reversed;

        return $this;
    }

    public function isFind(): ?bool
    {
        return $this->find;
    }

    public function setFind(bool $find): static
    {
        $this->find = $find;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection<int, Games>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Games $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
        }

        return $this;
    }

    public function removeGame(Games $game): static
    {
        $this->games->removeElement($game);

        return $this;
    }
}
