<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Card;
use App\Entity\Games;
use App\Entity\User;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Math;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Uid\Uuid;

class GameController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('game/game.html.twig', [
            'controllerName' => 'GameController',
        ]);
    }

    #[Route('/playground/', name: 'PlaygroundRoute')]
    #[Route('/playground/', name: 'PlaygroundRoute')]
    public function play(Request $request, EntityManagerInterface $em, TokenInterface $token): Response
    {
        $themeName = $request->query->get('theme');

        $themes = $em->getRepository(Theme::class)->findBy(['name' => $themeName]);

        $pathsPicturesTheme = [];

        foreach ($themes as $themeEntity) {
            $pathsPicturesTheme[] = $themeEntity->getUrl();
        }

        $difficulty = $request->query->get('difficulty', '');

        // Convert difficulty string to an integer
        $difficultyLevel = $this->convertDifficultyToInt($difficulty);

        $pairCount = $this->getPairCountByDifficulty($difficulty);

        $cardsData = $this->generateCards($pairCount);

        $gridSize = ceil(sqrt(count($cardsData)));

        $user = $this->getUser();

        if ($user instanceof User) {
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();

            // Initialize the game in BDD with entityManager (Games entity, Card entity)

            $game = new Games();
            $game->setEndGame(false);
            $game->setScore(0);
            $game->setFirstName($firstName);
            $game->setLastName($lastName);
            $game->setDifficulty($difficultyLevel);
            $game->setUserEmail($token->getUser()->getUserIdentifier());

            $em->persist($game);
            $em->flush();
        }

        return $this->render('game/game.html.twig', [
            'pathsPicturesTheme' => $pathsPicturesTheme,
            'theme' => $themeName,
            'difficulty' => $difficulty,
            'cards' => $cardsData,
            'gridSize' => $gridSize,
            'game' => $game,
            'controllerName' => 'GameController',
        ]);
    }

    private function convertDifficultyToInt(string $difficulty): int
    {
        return match ($difficulty) {
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            default => 0,
        };
    }

    private function getPairCountByDifficulty(string $difficulty): int
    {
        return match ($difficulty) {
            'easy' => 8,
            'medium' => 10,
            'hard' => 15,
            default => 8,
        };
    }

    private function generateCards(int $pairCount): array
    {
        $cards = [];
        for ($i = 1; $i <= $pairCount; $i++) {
            $cards[] = ['number' => $i, 'id' => $i];
            $cards[] = ['number' => $i, 'id' => $i];
        }
        shuffle($cards);
        return $cards;
    }

    #[Route('/flipCard', name: 'flip_card', methods: ['POST'])]
    public function flipCard(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $cardId = $data['cardId'];

        $card = $em->getRepository(Card::class)->find($cardId);

        if ($card) {
            $card->setFlipped(true);
            $em->persist($card);
            $em->flush();

            return new JsonResponse(['status' => 'success', 'cardId' => $cardId]);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Card not found'], Response::HTTP_NOT_FOUND);
    }

    // #[Route('/api/game/update', name: 'win_game', methods: ['POST'])]
    // public function updateGameStats(Request $request, EntityManagerInterface $em): Response
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $gameId = $data['gameId'];

    //     $game = $em->getRepository(Games::class)->findOneBy(['gameId' => $gameId]);

    //     if ($game) {
    //         $game->setEndGame(true);
    //         $em->persist($game);
    //         $em->flush();

    //         return new JsonResponse(['status' => 'success', 'gameId' => $game->getId()]);
    //     }

    //     return new JsonResponse(['status' => 'error', 'message' => 'Game not found'], Response::HTTP_NOT_FOUND);
    // }

    // Ajouter cette méthode dans GameController
    #[Route('/api/game/win', name: 'update_game_stats', methods: ['POST'])]
    public function winGame(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Assurez-vous que l'ID du jeu est bien transmis
        $gameId = $data['gameId'] ?? null;
        $totalTime = $data['totalTime'] ?? null;
        $difficulty = $data['difficulty'] ?? null;

        if (!$gameId || !$totalTime || !$difficulty) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid data'], 400);
        }

        // Récupérer le jeu en question depuis la base de données
        $game = $em->getRepository(Games::class)->find($gameId);
        $score = (1 / $totalTime) * $difficulty * 100000;
        $score = (int) $score;

        if (!$game) {
            return new JsonResponse(['status' => 'error', 'message' => 'Game not found'], 404);
        }

        // Mettre à jour les valeurs du jeu
        $game->setScore($score);
        $game->setFinishedAt(new \DateTime()); // Par exemple, on enregistre le moment de fin
        $game->setEndGame(true);
        // Sauvegarder dans la base de données
        $em->persist($game);
        $em->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Game updated successfully']);
    }
}
