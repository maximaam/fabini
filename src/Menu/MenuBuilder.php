<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 15.02.18
 * Time: 16:13
 */

namespace App\Menu;

use App\Entity\Category;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{

    private $factory;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface $factory
     * @param RequestStack $request
     * @param TranslatorInterface $translator
     * @param EntityManager $em
     */
    public function __construct(
        FactoryInterface $factory,
        RequestStack $request,
        TranslatorInterface $translator,
        EntityManager $em)
    {
        $this->factory = $factory;
        $this->request = $request;
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

        $categories = $this->em
            ->getRepository(Category::class)
            ->fetchParents()
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $locale = $this->request->getCurrentRequest()->getLocale();

        /** @var Category $category */
        foreach ($categories as $category) {
            $menu->addChild($category->getName($locale), [
                'route' => 'home_category',
                'attributes' => ['class' => 'nav-item'],
                'linkAttributes' => ['class' => 'nav-link'],
                'routeParameters' => [
                    'id'    => $category->getId(),
                    'alias' => $category->getAlias($locale)
                ]
            ]);
        }

        /*
        foreach ($items[$locale] as $key => $val) {
            $menu->addChild($val['label'], [
                'route' => $val['route'],
                'attributes' => ['class' => 'nav-item'],
                'linkAttributes' => ['class' => 'nav-link']
            ]);
        }
        */

        return $menu;
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'footer-nav');

        $locale = $this->request->getCurrentRequest()->getLocale();

        $items = [
            'de' => [
                ['label' => 'Über uns', 'route' => 'about-us_de'],
                ['label' => 'Impressum', 'route' => 'imprint_de'],
                ['label' => 'Contact', 'route' => 'contact_de'],
            ],
            'en' => [
                ['label' => 'About us', 'route' => 'about-us_en'],
                ['label' => 'Imprint', 'route' => 'imprint_en'],
                ['label' => 'Contact', 'route' => 'contact_en'],
            ]
        ];

        foreach ($items[$locale] as $key => $val) {
            $menu->addChild($val['label'], ['route' => $val['route']]);
        }

        return $menu;
    }
}