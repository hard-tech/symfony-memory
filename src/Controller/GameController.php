<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    // #[Route('/game/select', name: 'memory_select')]
    public function index(): Response
    {
        return $this->render('game/game.html.twig', [
            'controllerName' => 'GameController',
        ]);
    }

    #[Route('/playground/', name: 'PlaygroundRoute')]
    public function play(Request $request): Response
    {
        $theme = $request->query->get('theme');

        $difficulty = $request->query->get('difficulty', '');

        $pairCount = $this->getPairCountByDifficulty($difficulty);

        $cards = $this->generateCards($pairCount);
        
        $gridSize = ceil(sqrt(count($cards))); 

        return $this->render('game/game.html.twig', [
            'theme' => $theme,
            'difficulty' => $difficulty,
            'cards' => $cards,
            'gridSize' => $gridSize,
            'controllerName' => 'GameController',
        ]);
    }

    private function getPairCountByDifficulty(string $difficulty): int
    {
        return match($difficulty) {
            'easy' => 8,   
            'medium' => 10, 
            'hard' => 12,   
            default => 10,  
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

}
