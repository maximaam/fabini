<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 15.02.18
 * Time: 16:13
 */

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{

    private $factory;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface $factory
     * @param RequestStack $request
     */
    public function __construct(FactoryInterface $factory, RequestStack $request)
    {
        $this->factory = $factory;
        $this->request = $request;
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

        $locale = $this->request->getCurrentRequest()->getLocale();

        $items = [
            'de' => [
                ['label' => 'LEDERBÖRSEN', 'route' => 'leather-wallets_de'],
                ['label' => 'LEDERTASCHEN', 'route' => 'leather-bags_de'],
                ['label' => 'LEDERHANDSCHUHE', 'route' => 'leather-gloves_de'],
            ],
            'en' => [
                ['label' => 'LEDERBÖRSEN', 'route' => 'leather-wallets_de'],
                ['label' => 'LEDERTASCHEN', 'route' => 'leather-bags_de'],
                ['label' => 'LEDERHANDSCHUHE', 'route' => 'leather-gloves_de'],
            ]
        ];

        foreach ($items[$locale] as $key => $val) {
            $menu->addChild($val['label'], [
                'route' => $val['route'],
                'attributes' => ['class' => 'nav-item'],
                'linkAttributes' => ['class' => 'nav-link']
            ]);
        }

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