<?php

namespace App\Entity;

use App\Repository\GamesRepository;
use App\Controller\Admin\StatsController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
class Games
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $difficulty = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?bool $endGame = null;

    /**
     * @var Collection<int, Card>
     */
    #[ORM\ManyToMany(targetEntity: Card::class, mappedBy: 'games')]
    private Collection $cards;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'games')]
    private Collection $users;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $gameId = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $UserEmail = null;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }
    
    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;
    
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function isEndGame(): ?bool
    {
        return $this->endGame;
    }

    public function setEndGame(bool $endGame): static
    {
        $this->endGame = $endGame;

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->addGame($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            $card->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addGame($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeGame($this);
        }

        return $this;
    }

    public function getGameId(): ?Uuid
    {
        return $this->gameId;
    }

    public function setGameId(Uuid $gameId): static
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->UserEmail;
    }

    public function setUserEmail(string $UserEmail): static
    {
        $this->UserEmail = $UserEmail;

        return $this;
    }
}
