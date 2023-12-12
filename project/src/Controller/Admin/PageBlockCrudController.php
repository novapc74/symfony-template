<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\Gallery;
use App\Enum\MediaCache;
use App\Entity\PageBlock;
use App\Enum\PageBlockType;
use App\Form\Admin\MediaType;
use Doctrine\ORM\QueryBuilder;
use App\Form\Admin\GalleryType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Count;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PageBlockCrudController extends AbstractCrudController
{
    public function __construct(private readonly RequestStack $requestStack,
                                private readonly CacheManager $imagineCacheManager)
    {
    }

    public static function getEntityFqcn(): string
    {
        return PageBlock::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Блок')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавить блок')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать блок')
            ->setEntityLabelInPlural(' Блоки');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        $type = $this->getContext()->getEntity()->getInstance()?->getLayout();

        if (Crud::PAGE_EDIT === $pageName && $type === PageBlockType::REQUIRED->value) {
            return [
                FormField::addPanel('Постер для статьи')
                    ->setProperty('newImage')
                    ->setFormType(MediaType::class)
                    ->setFormTypeOptions([
                        'by_reference' => false,
                        'error_bubbling' => true,
                        'mapped' => true,
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
            ];
        }

        if (Crud::PAGE_EDIT === $pageName && $type === PageBlockType::DESCRIPTION_IMAGE->value) {

            return [
                FormField::addPanel('Изображение Описание'),
                IntegerField::new('sort', 'Очередность на странице')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addPanel('Изображение')
                    ->setProperty('newImage')
                    ->setFormType(MediaType::class)
                    ->setFormTypeOptions([
                        'by_reference' => false,
                        'error_bubbling' => true,
                        'mapped' => true,
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addRow(),
                TextareaField::new('description', 'Текст')
                    ->setTextAlign('center')
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ])
            ];
        }

        if (Crud::PAGE_EDIT === $pageName && $type === PageBlockType::TITLE_DESCRIPTION_IMAGE->value) {

            return [
                FormField::addPanel('Заголовок Описание Картинка'),
                IntegerField::new('sort', 'Очерёдность')
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                    ->setTextAlign('center')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addRow(),
                TextField::new('title', 'Заголовок')
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                    ->setTextAlign('center')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addRow(),
                TextareaField::new('description', 'Описание')
                    ->setTextAlign('center')
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addRow(),
                FormField::addPanel('Изображение')
                    ->setProperty('newImage')
                    ->setFormType(MediaType::class)
                    ->setFormTypeOptions([
                        'by_reference' => false,
                        'error_bubbling' => true,
                        'mapped' => true,
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
            ];
        }

        if (Crud::PAGE_EDIT === $pageName && $type === PageBlockType::TITLE_HTML_GALLERY->value) {

            return [
                FormField::addTab('Заголовок HTML Галерея'),
                IntegerField::new('sort', 'Очередность на странице')
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addRow(),
                TextField::new('title', 'Заголовок')
                    ->setTextAlign('center')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addRow(),
                TextareaField::new('html', 'HTML')
                    ->setFormType(CKEditorType::class)
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addTab('Галерея'),
                CollectionField::new('gallery', 'Галерея')
                    ->setEntryType(GalleryType::class)
                    ->setFormTypeOptions([
                        'by_reference' => false,
                        'error_bubbling' => false,
                        'constraints' => [
                            new NotBlank(),
                            new Count([
                                'min' => 2,
                                'max' => 2,
                            ])
                        ]
                    ])
                    ->renderExpanded()
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3'),
            ];
        }

        if (Crud::PAGE_EDIT === $pageName && $type === PageBlockType::QUOTE->value) {
            return [
                IntegerField::new('sort', 'Очередность на странице')
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]),
                FormField::addRow(),
                TextareaField::new('description', 'Текст')
                    ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new NotBlank()
                        ]
                    ])

            ];
        }

        return [
            FormField::addTab('Описание'),
            IntegerField::new('sort', 'Очерёдность')
                ->setTextAlign('center')
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
            ,
            FormField::addRow(),
            TextField::new('title', 'Заголовок')
                ->setTextAlign('center')
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
            ,
            FormField::addRow(),
            TextareaField::new('description', 'Текст')
                ->setTextAlign('center')
                ->setTemplatePath('admin/crud/assoc_description.html.twig')
                ->setTextAlign('center')
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
            ,
            FormField::addRow(),
            TextareaField::new('html', 'HTML')
                ->setFormType(CKEditorType::class)
                ->setTextAlign('center')
                ->setTemplatePath('admin/crud/assoc_description.html.twig')
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
            ,
            FormField::addRow(),
            ChoiceField::new('layout', 'Тип блока')
                ->setChoices(PageBlock::getAvailableType())
                ->setTextAlign('center')
                ->formatValue(fn($entity, $value) => $value)
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
            ,
            FormField::addTab('Медиа'),
            TextField::new('newImage', 'Изображение')
                ->setTextAlign('center')
                ->setTemplatePath('admin/crud/assoc_gallery.html.twig')
                ->onlyOnIndex(),
            FormField::addPanel('Постер для статьи')
                ->setProperty('newImage')
                ->setFormType(MediaType::class)
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'error_bubbling' => true,
                    'mapped' => true,
                ])
                ->onlyOnForms()
            ,
            CollectionField::new('gallery', 'Галерея')
                ->setTemplatePath('admin/crud/assoc_gallery.html.twig')
                ->setTextAlign('center')
                ->onlyOnIndex()
            ,
            CollectionField::new('gallery', 'Галерея')
                ->setEntryType(GalleryType::class)
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'error_bubbling' => false,
                    'constraints' => [
                        new Count([
                            'min' => 2,
                            'max' => 2,
                        ])
                    ]
                ])
                ->renderExpanded()
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                ->onlyOnForms()
            ,
        ];
    }

    public static function getPageBlockUrl(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): string
    {
        $idCollection = $context->getEntity()->getInstance()->getPageBlocks()->map(fn(PageBlock $pageBlock) => $pageBlock->getId());
        $stringifyIdCollection = implode(',', $idCollection->toArray());

        return $adminUrlGenerator->unsetAll()
            ->setController(PageBlockCrudController::class)
            ->set('entity_id', $stringifyIdCollection)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();
    }

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        /**@var PageBlock $pageBlock */
        $pageBlock = $context->getEntity()->getInstance();

        if ($image = $pageBlock->getImage()) {
            $this->createWarmImageCacheLink($image);
        }

        $pageBlock
            ->getGallery()
            ->map(fn(Gallery $gallery) => $this->createWarmImageCacheLink($gallery->getImage()));

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    private function createWarmImageCacheLink(Media $image): void
    {
        $imagePath = MediaCache::UploadMediaFolder->value . $image->getImageName();
        $filter = MediaCache::MediumImageFilter->value;

        $warmUrl = $this->imagineCacheManager->getBrowserPath($imagePath, $filter);
        $message = "<a href=\"$warmUrl\" target=\"_blank\" >Давай прогреем эту картинку!</a>";

        $this->addFlash('success', $message);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->requestStack->getCurrentRequest()->query->has('entity_id')) {
            $idCollection = explode(',', $this->requestStack->getCurrentRequest()->query->get('entity_id'));

            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->where('entity.id IN (:value)')
                ->setParameter('value', $idCollection);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
    }
}
