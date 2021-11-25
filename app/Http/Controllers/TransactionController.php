<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function snapPage()
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = 'SB-Mid-server-jHiRIe0iXX-6GM6owv1hXRYi';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $order_id = rand();

        $params = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => 10000,
            ),
            'customer_details' => array(
                'first_name' => 'budi',
                'last_name' => 'pratama',
                'email' => 'budi.pra@example.com',
                'phone' => '08111222333',
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response([
            'Message' => 'Order Received',
            'order_id' => $order_id,
            'snap_token' => $snapToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/'.$snapToken
        ]);
        // $snapToken = $order->snap_token;
        // if (is_null($snapToken)) {
        //     // If snap token is still NULL, generate snap token and save it to database

        //     $midtrans = new CreateSnapTokenService($order);
        //     $snapToken = $midtrans->getSnapToken();

        //     $order->snap_token = $snapToken;
        //     $order->save();
        // }

        // return view('orders.show', compact('order', 'snapToken'));
    }

    public function status(Request $request)
    {
        $authz = base64_encode("SB-Mid-server-jHiRIe0iXX-6GM6owv1hXRYi:");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/$request->order_id/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . $authz
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            return $response;
        }
    }
}
