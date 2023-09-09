<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly ManagerRegistry   $managerRegistry,
                                private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

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

//        yield MenuItem::section('Секции на страницах', 'fa-sharp fa-solid fa-puzzle-piece');
//        foreach (self::getSectionMenu() as [$label, $icon, $url]) {
//            yield MenuItem::linkToUrl($label, $icon, $url);
//        }
    }

//    private function getSectionMenu(): array
//    {
//        $dataSectionMenu = [];
//
//        $dataSectionMenu['Добавить секцию'] = [
//            'Добавить секцию',
//            'fa-solid fa-plus',
//            $this->adminUrlGenerator
//                ->unsetAll()
//                ->setController(PageSectionCrudController::class)
//                ->setAction(Crud::PAGE_INDEX)
//                ->generateUrl()
//        ];
//
//        foreach ($this->managerRegistry->getRepository(PageSection::class)->findAll() as $section) {
//            $sectionType = array_search($section->getType(), PageSection::getAvailableSectionType());
//            if (!array_key_exists($sectionType, $dataSectionMenu)) {
//                $url = $this->adminUrlGenerator
//                    ->unsetAll()
//                    ->setController(PageSectionCrudController::class)
//                    ->setAction(Crud::PAGE_INDEX)
//                    ->set('type', $section->getType())
//                    ->generateUrl();
//
//                $dataSectionMenu[$sectionType] = [$sectionType, false, $url];
//            }
//        }
//
//        return $dataSectionMenu;
//    }
}
