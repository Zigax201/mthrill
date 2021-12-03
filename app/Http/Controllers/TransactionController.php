<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use App\Models\Cart;
use App\Models\user;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Midtrans\Transaction as MidtransTransaction;

class TransactionController extends Controller
{
    public function snapPage(Request $request)
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = 'SB-Mid-server-jHiRIe0iXX-6GM6owv1hXRYi';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $user = Auth::user();

        $order_id = rand();

        $params = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => $request->total_price,
            ),
            'customer_details' => array(
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'phone' => $user->noHP,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $transaction = transaction::create([
            'id_user' => $request->id_user,
            'number' => strval($order_id),
            'total_price' => $request->total_price,
            'payment_status' => 1,
            'snap_token' => $snapToken
        ]);

        $transaction->save();

        return response([
            'Message' => 'Order Received',
            'transaction' => $transaction,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken
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
            if ($response['transaction_status'] == 'capture' || $response['transaction_status'] == 'settlement') {

                transaction::where('number', $request->order_id)
                    ->update(['payment_status' => 2]);


                $cart = Cart::where('id_user', $request->id_user);
                $cart->delete();
            }
            return $response;
        }
    }

    public function get_transaction(Request $request)
    {
        $transaction = transaction::where('id_user', $request->id_user)->get();
        return response([
            'message' => 'Succes get all transaction for user',
            'Transactions' =>  $transaction
        ]);
    }

    public function get_transaction_by_id(Request $request)
    {
        $transaction = transaction::where('id_user', $request->id_user)
            ->where('id', $request->id_transaksi);
        return response([
            'message' => 'Succes get transaction',
            'Transactions' =>  $transaction
        ]);
    }
}
