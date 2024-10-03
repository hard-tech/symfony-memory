<?php

namespace App\Controller;

use App\Entity\Theme;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\ArrayItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/playground/settings', name: 'PlaygroundSettingsRoute')]
    public function index(EntityManagerInterface $em): Response
    {

        $themes = $em->getRepository(Theme::class)->findAll();
        $themesNames = [];
        foreach ($themes as $theme) {
            if(in_array($theme->getName(), $themesNames)) {
            }else{
                $themesNames[] = $theme->getName();
            }
        }

        return $this->render('settings/settings.html.twig', [
            'themesNames' => $themesNames,
            'controller_name' => 'SettingsController',
        ]);
    }
}
