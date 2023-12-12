<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\PageBlock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'default')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span style="color: red">Symfony template</span>');
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Пользователи', 'fa-solid fa-user', User::class);

        yield MenuItem::section('TEST', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Блоки для страниц', 'fa-solid fa-pages', PageBlock::class);
    }
}
