<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $leather = $this->getDoctrine()->getRepository(Product::class)->findBy([], [], 3);

        return $this->render('app/index.html.twig', [
            'leather' => $leather
        ]);
    }

    /**
     * @Route("/{_locale}/ueber-us", name="about-us_de")
     * @Route("/{_locale}/about-us", name="about-us_en")
     */
    public function aboutUs()
    {
        return $this->render('app/about-us.html.twig');
    }

    /**
     * @Route("/{_locale}/impressum", name="imprint_de")
     * @Route("/{_locale}/imprint", name="imprint_en")
     */
    public function imprint()
    {
        return $this->render('app/imprint.html.twig');
    }

    /**
     * @Route("/{_locale}/kontakt", name="contact_de")
     * @Route("/{_locale}/contact", name="contact_en")
     */
    public function contact()
    {
        return $this->render('app/contact.html.twig');
    }

    /**
     * @Route("/{_locale}/{id}/{alias}", name="home_category")
     *
     * @param Category $category
     * @return Response
     */
    public function category(Category $category)
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->fetchByCategories(CategoryRepository::getRelatedCategories($category));

        return $this->render('app/products.html.twig', [
            'products' => $products
        ]);
    }

}
