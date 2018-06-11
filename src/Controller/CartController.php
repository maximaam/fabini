<?php

namespace App\Controller;


use App\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\{Request, Response};
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Utils\ProductHelper;
use App\Service\Paypal;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Validator;

use App\Entity\Product;

/**
 * Class CartController
 * @package AppBundle\Controller
 *
 * @Route("{_locale}/cart")
 */
class CartController extends Controller
{
    /**
     * @Route("/add/{id}")
     * @Method("POST")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add($id, Request $request)
    {
        $session = $this->get('session');

        if (!$session->has('cart')) {
            $session->set('cart', []);
        }

        $cart = $session->get('cart');

        $quantity = (int) $request->request->get('quantity');
        $color = $request->request->get('color');
        $size = $request->request->get('size');

        $cart[$id] = [
            'quantity'  => $quantity,
            'color'     => $color,
            'size'      => $size
        ];

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * @Route("/remove/{id}")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remove($id)
    {
        $session = $this->get('session');
        $cart = $session->get('cart');
        unset($cart[$id]);
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * @Route("/", name="app_cart_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $session = $this->get('session');

        if (!$session->has('cart') || empty($session->get('cart'))) {
            return $this->render('app/cart/index.html.twig', [
                'cart'  => []
            ]);
        }

        $cart = $session->get('cart');
        $products = $this->getProductsFromSession();

        //$mobileDetector = $this->get('mobile_detect.mobile_detector');
        //$view = $mobileDetector->isMobile() ? '_mobile' : '';

        return $this->render('app/cart/index.html.twig', [
            'cart'  => ProductHelper::computeCard($products, $cart, $request->getLocale())
        ]);
    }

    /**
     * @Route("/create-payment", name="app_cart_create-payment")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createPayment(Request $request): Response
    {
        $paymentDetails = $request->request->get('paymentDetails');

        if (!$paymentDetails) {
            $this->redirectToRoute('app_index_index');
        }

        $payment = new Payment();
        $payment->setStatus(Payment::STATUS_INIT)
            ->setPaymentId($paymentDetails['id'])
            ->setPaypalPaymentDetails($paymentDetails)
            ->setProductsIds($this->getProductsIds());

        $em = $this->getDoctrine()->getManager();

        $em->persist($payment);
        $em->flush();

        return new Response('success');
    }

    /**
     * @Route("/confirm-payment", name="app_cart_confirm-payment")
     * @param Request $request
     *
     * @return Response
     */
    public function confirmPayment(Request $request)
    {
        $paymentId = $request->query->get('paymentId');
        $cart = $this->get('session')->get('cart');

        if (!$paymentId || empty($cart)) {
            return $this->redirectToRoute('app_index_index');
        }

        /** @var Payment $payment */
        $payment = $this->getDoctrine()->getRepository(Payment::class)
            ->findOneBy(['paymentId' => $paymentId]);

        $payment->setStatus(Payment::STATUS_CONFIRM);

        $this->getDoctrine()->getManager()->flush();

        $products = $this->getProductsFromSession();
        $this->get('session')->set('cart', []); //Empty session cart

        return $this->render('app/cart/confirmPayment.html.twig', [
            'cart'  => ProductHelper::computeCard($products, $cart, $request->getLocale()),
            'payment'   => $payment
        ]);
    }

    /**
     * @Route("/cancel-payment", name="app_cart_cancel-payment")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cancelPayment(Request $request): Response
    {
        return new Response('success');
    }


    /**
     * @return Product[]
     */
    private function getProductsFromSession()
    {
        return $this->getDoctrine()
            ->getManager()
            ->getRepository(Product::class)
            ->findByIds($this->getProductsIds());
    }

    /**
     * @return array
     */
    private function getProductsIds()
    {
        $session = $this->get('session');

        if (!$session->has('cart') || empty($session->get('cart'))) {
            return [];
        }

        $cart = $session->get('cart');

        return array_keys($cart);
    }

}


/*
 *
 * array(25) {
  ["TOKEN"]=>
  string(20) "EC-8DX31524G38521809"
  ["SUCCESSPAGEREDIRECTREQUESTED"]=>
  string(5) "false"
  ["TIMESTAMP"]=>
  string(20) "2017-05-10T14:23:22Z"
  ["CORRELATIONID"]=>
  string(12) "6bcee1d32242"
  ["ACK"]=>
  string(7) "Success"
  ["VERSION"]=>
  string(4) "74.0"
  ["BUILD"]=>
  string(8) "33490117"
  ["INSURANCEOPTIONSELECTED"]=>
  string(5) "false"
  ["SHIPPINGOPTIONISDEFAULT"]=>
  string(5) "false"
  ["PAYMENTINFO_0_TRANSACTIONID"]=>
  string(17) "4XR48274NH982190U"
  ["PAYMENTINFO_0_TRANSACTIONTYPE"]=>
  string(15) "expresscheckout"
  ["PAYMENTINFO_0_PAYMENTTYPE"]=>
  string(7) "instant"
  ["PAYMENTINFO_0_ORDERTIME"]=>
  string(20) "2017-05-10T14:23:21Z"
  ["PAYMENTINFO_0_AMT"]=>
  string(5) "85.00"
  ["PAYMENTINFO_0_FEEAMT"]=>
  string(4) "1.97"
  ["PAYMENTINFO_0_TAXAMT"]=>
  string(4) "0.00"
  ["PAYMENTINFO_0_CURRENCYCODE"]=>
  string(3) "EUR"
  ["PAYMENTINFO_0_PAYMENTSTATUS"]=>
  string(9) "Completed"
  ["PAYMENTINFO_0_PENDINGREASON"]=>
  string(4) "None"
  ["PAYMENTINFO_0_REASONCODE"]=>
  string(4) "None"
  ["PAYMENTINFO_0_PROTECTIONELIGIBILITY"]=>
  string(8) "Eligible"
  ["PAYMENTINFO_0_PROTECTIONELIGIBILITYTYPE"]=>
  string(51) "ItemNotReceivedEligible,UnauthorizedPaymentEligible"
  ["PAYMENTINFO_0_SECUREMERCHANTACCOUNTID"]=>
  string(13) "AYU5HA5V7X8XA"
  ["PAYMENTINFO_0_ERRORCODE"]=>
  string(1) "0"
  ["PAYMENTINFO_0_ACK"]=>
  string(7) "Success"
}
 *
 */