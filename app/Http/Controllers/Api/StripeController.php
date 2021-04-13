<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\Stripe;
use Illuminate\Support\Facades\Redirect;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    //
    public function createCompanyCheckoutSession(Request $request) {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
            $payment_intent = PaymentIntent::create([
                'amount' => $request['amount'],
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'integration_check' => 'accept_a_payment'
                ]
            ]);
            // $session = Session::create([
            //     'payment_method_types' => ['card'],
            //     'mode' => 'payment',
            //     'line_items' => [
            //         [
            //             'price' => env('STRIPE_COMPANY_PRICE'),
            //             'quantity' => 1
            //         ]
            //     ],
            //     'cancel_url' => $request['cancel_url'],
            //     'success_url' => $request['success_url']
            // ]);
            // dd($payment_intent);
            return response()->json(['client_secret' => $payment_intent->client_secret]);
        } catch (CardException $e) {
            return response()->json(['message' => $e->getError()->message],$e->getHttpStatus());
        } catch (ApiErrorException $e) {
            return response()->json(['message' => $e->getError()->message],$e->getHttpStatus());
        }   
    }

    public function createLeadCheckoutSession(Request $request) {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
            $payment_intent = PaymentIntent::create([
                'amount' => $request['amount'],
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'integration_check' => 'accept_a_payment'
                ]
            ]);
            return response()->json(['client_secret' => $payment_intent->client_secret]);
            // $session = Session::create([
            //     'payment_method_types' => ['card'],
            //     'mode' => 'payment',
            //     'line_items' => [
            //         [
            //             'price' => env('STRIPE_LEAD_PRICE'),
            //             'quantity' => $request['quantity']
            //         ]
            //     ],
            //     'cancel_url' => $request['cancel_url'],
            //     'success_url' => $request['success_url'] . '?quantity=' . $request['quantity']
            // ]);
            // return response()->json(['id' => $session->id]);
        } catch (CardException $e) {
            return response()->json(['message' => $e->getError()->message],$e->getHttpStatus());
        } catch (ApiErrorException $e) {
            return response()->json(['message' => $e->getError()->message],$e->getHttpStatus());
        }   
    }

    public function success(Request $request) {
        return Redirect::to('http://localhost/#/payment/success?product=company');
    }

    public function failed() {
        return Redirect::to('http://localhost/#/payment/failed');
    }
}
