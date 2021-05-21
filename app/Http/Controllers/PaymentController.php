<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentControllerRequest;
use Mail;

use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardNumber;
use LVR\CreditCard\CardExpirationDate;

class PaymentController extends Controller
{
    public function payment(CheckoutRequest $request)
    {
        $data = $request->only('client_name','client_address','shipping_id','total_product_value');
        if ($data['shipping_id'] == 0) {
            $data['total_shipping_value'] = $data['total_product_value'];
        } elseif ($data['shipping_id'] == 1) {
            $data['total_shipping_value'] = $data['total_product_value'] + 10;
        }
        $item = new Order($data);
        if ($item) {
                return view('merchant',compact('item'));
        } else {
            return back()
                ->withErrors(['msg' => 'Error'])
                ->withInput();
        }
    }

    public function merchant(PaymentControllerRequest $request)
    {
        $data = $request->only('client_name','client_address','total_shipping_value','total_product_value');
        $item = new Order($data);
        $item->save();
        if ($item) {
            return redirect()
                ->route('home', [$item->id])
                ->with(['success' => 'Successful!']);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }
    }
}