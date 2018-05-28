<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/{_locale}/category/{id}/{alias}/{subCatAlias}/{itemId}",
     *     name="categories",
     *     defaults={"subCatAlias" = null, "itemId" = null}
     *     )
     *
     * @param Request $request
     * @return Response
     */
    public function category(Request $request)
    {
        $catRepo = $this->getDoctrine()->getRepository(Category::class);
        $productRepo = $this->getDoctrine()->getRepository(Product::class);

        if (null === $itemId = $request->get('itemId')) {
            if (null === $subCatAlias = $request->get('subCatAlias')) { //Main categories

                /** @var Category $category */
                $category = $catRepo->find($request->get('id'));
                $catIds = CategoryRepository::getChildrenIds($category);
                $products = $productRepo->fetchByCategories($catIds);
            } else {
                $alias = 'alias' . ucfirst($request->getLocale());
                $category = $catRepo->findOneBy([$alias => $subCatAlias]);
                $products = $productRepo->findBy(['category' => $category]);
            }

            return $this->render('app/products.html.twig', [
                'category'  => $category,
                'products'  => $products
            ]);
        } else {

            return $this->render('app/product.html.twig', [
                'product'  => $productRepo->find($itemId)
            ]);
        }


    }

}
