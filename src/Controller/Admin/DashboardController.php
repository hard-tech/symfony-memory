<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Games;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ){
    }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl();
        return $this->redirect($url);
        //  return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony Memory');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::subMenu('Manage user', 'fas fa-shield')->setSubItems([
            MenuItem::linkToCrud('User', 'fas fa-users', User::class),
        ]);
        
        yield MenuItem::subMenu('Manag Games', 'fas fa-gamepad')->setSubItems([
            MenuItem::linkToCrud('Games', 'fas fa-console', Games::class)
        ]);
        yield MenuItem::linkToLogout('Logout', 'fas fa-user');
        // yield MenuItem::linkToCrud('Stats', 'fas fa-list', Games::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
