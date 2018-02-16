<?php

namespace App\Controller;

use App\Entity\Leather;
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
        $leather = $this->getDoctrine()->getRepository(Leather::class)->findBy([], [], 3);


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
     * @Route("/{_locale}/lederboersen", name="leather-wallets_de")
     * @Route("/{_locale}/leather-wallets", name="leather-wallets_en")
     */
    public function leatherWallets()
    {
        return $this->render('app/leather-wallets.html.twig');
    }

    /**
     * @Route("/{_locale}/ledertaschen", name="leather-bags_de")
     * @Route("/{_locale}/leather-bags", name="leather-bags_en")
     */
    public function leatherBags()
    {
        return $this->render('app/leather-bags.html.twig');
    }

    /**
     * @Route("/{_locale}/Lederhandschuhe", name="leather-gloves_de")
     * @Route("/{_locale}/leather-gloves", name="leather-gloves_en")
     */
    public function leatherGloves()
    {
        return $this->render('app/leather-gloves.html.twig');
    }
}
