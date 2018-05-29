<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Unirest;
use App\Utils\ProductHelper;

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

        $cart[$id] = [
            'quantity'  => (int) $request->request->get('quantity'),
            'color'      => $request->request->get('color'),
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
            return $this->render('cart/index.html.twig', [
                'cart'  => []
            ]);
        }

        $cart = $session->get('cart');
        $products = $this->getProductsFromSession();

        //$mobileDetector = $this->get('mobile_detect.mobile_detector');
        //$view = $mobileDetector->isMobile() ? '_mobile' : '';

        return $this->render('cart/index.html.twig', [
            'cart'  => ProductHelper::computeCard($products, $cart, $request->getLocale())
        ]);
    }

    /**
     * @Route("/init-payment")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function initPayment(Request $request)
    {
        $products = $this->getProductsFromSession();

        if (empty($products)) {
            return $this->redirectToRoute('app_home');
        }

        $products = ProductHelper::computeCard($products, $this->get('session')->get('cart'), $request->getLocale());

        $params = [
            'METHOD'	=> 'SetExpressCheckout',
            'VERSION'	=> $this->container->getParameter('paypal.version'),
            'USER'		=> $this->container->getParameter('paypal.sandbox.username'),
            'PWD'		=> $this->container->getParameter('paypal.sandbox.password'),
            'SIGNATURE'	=> $this->container->getParameter('paypal.sandbox.signature'),
            'RETURNURL'	=> $this->generateUrl('app_cart_confirmpayment', [], 0),
            'CANCELURL'	=> $this->generateUrl('app_cart_index', [], 0),
            'PAYMENTREQUEST_0_CURRENCYCODE' => $this->container->getParameter('paypal.currency_code'),
            'PAYMENTREQUEST_0_AMT' => $products['totals']['total']
        ];

        $params = http_build_query($params);
        $url = $this->container->getParameter('paypal.sandbox.endpoint_url') . '?' . $params;

        $response = Unirest\Request::get($url);

        $response = $response->body;
        $response_params = [];
        parse_str($response, $response_params);

        if ($response_params['ACK'] == 'Success') {
            return $this->redirect($this->container->getParameter('paypal.sandbox.payment_url') . $response_params['TOKEN']);
        }

        throw new \Exception('Error');
    }

    /**
     * @Route("/confirm-payment")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function confirmPayment(Request $request)
    {
        $token = $request->query->get('token');
        $payerId = $request->query->get('PayerID');

        if ($token && $payerId) {
            $products = $this->getProductsFromSession();
            $cart = $this->get('session')->get('cart');
            $productsData = ProductHelper::computeCard($products, $cart, $request->getLocale());

            $params = [
                'METHOD'	=> 'DoExpressCheckoutPayment',
                'VERSION'	=> $this->container->getParameter('paypal.version'),
                'PAYMENTACTION'					=> 'Sale',

                'USER'		=> $this->container->getParameter('paypal.sandbox.username'),
                'PWD'		=> $this->container->getParameter('paypal.sandbox.password'),
                'SIGNATURE'	=> $this->container->getParameter('paypal.sandbox.signature'),

                'PAYMENTREQUEST_0_CURRENCYCODE' => $this->container->getParameter('paypal.currency_code'),
                'PAYMENTREQUEST_0_AMT' => $productsData['totals']['total'],

                'TOKEN'							=> $token,
                'PAYERID'						=> $payerId,
            ];

            $params = http_build_query($params);
            $url = $this->container->getParameter('paypal.sandbox.endpoint_url') . '?' . $params;

            $response = Unirest\Request::get($url);

            $response = $response->body;
            $response_params = [];
            parse_str($response, $response_params);

            if ($response_params['ACK'] == 'Success') {
                $this->get('session')->set('cart', []); //Empty session cart

                $em = $this->getDoctrine()->getManager();

                $ordering = new Ordering;
                $ordering->setUser($this->getUser());
                $ordering->setTotals($productsData['totals']);
                $ordering->setPaymentPayerId($payerId);
                $ordering->setPaymentToken($token);
                $ordering->setPaymentStatus($response_params['PAYMENTINFO_0_PAYMENTSTATUS']);
                $ordering->setPaymentType($response_params['PAYMENTINFO_0_PAYMENTTYPE']);
                $ordering->setPaymentTransactionId($response_params['PAYMENTINFO_0_TRANSACTIONID']);
                $ordering->setPaymentTransactionType($response_params['PAYMENTINFO_0_TRANSACTIONTYPE']);
                $ordering->setPaymentCorrelationId($response_params['CORRELATIONID']);
                $ordering->setPaymentAmount($response_params['PAYMENTINFO_0_AMT']);
                $ordering->setPaymentFee($response_params['PAYMENTINFO_0_FEEAMT']);
                $ordering->setPaymentTax($response_params['PAYMENTINFO_0_TAXAMT']);
                $ordering->setPaymentCurrencyCode($response_params['PAYMENTINFO_0_CURRENCYCODE']);

                foreach ($products as $product) {
                    $orderingProduct = new OrderingProduct;
                    $orderingProduct->setProduct($product);
                    $orderingProduct->setOrdering($ordering);

                    unset($productsData[$product->getId()]['products']['slug']); //Not necessary
                    $orderingProduct->setAttributes($productsData['products'][$product->getId()]);

                    $ordering->addOrderingProduct($orderingProduct);
                }

                $em->persist($ordering);
                $em->flush();

                $orderingUrl = $this->generateUrl('app_user_orderings', [], 0);

                $mailSubject = $this->get('translator')->trans('payment.mail.subject');
                $mailBody = $this->get('translator')->trans('payment.mail.body', [
                    '%ordering_url%' => '<a href="'.$orderingUrl.'">'.$orderingUrl.'</a>',
                    '%buyer_name%' => $ordering->getUser()->getName(),
                ]);

                $message = \Swift_Message::newInstance($mailSubject)
                    ->setFrom($this->container->getParameter('mailer_user'))
                    ->setTo($ordering->getUser()->getEmail())
                    ->setBody(nl2br($mailBody), 'text/html');

                try {
                    $this->get('mailer')->send($message);
                } catch (\Exception $e) {
                    //do nothing
                }

                return $this->render('cart/confirmPayment.html.twig', [
                    'payment_url' => $this->container->getParameter('paypal.sandbox.payment_url') . $response_params['TOKEN']
                ]);
            }

            throw new \Exception('PayPal Error');
        }
    }


    /**
     * @return Product[]
     */
    private function getProductsFromSession()
    {
        $session = $this->get('session');

        if (!$session->has('cart') || empty($session->get('cart'))) {
            return [];
        }

        $cart = $session->get('cart');

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findByIds(array_keys($cart));

        return $products;
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