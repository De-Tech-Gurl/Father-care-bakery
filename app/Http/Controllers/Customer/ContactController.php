<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $whatsappUrl = 'https://wa.me/234'.ltrim((string) config('bakery.phone'), '0').'?text='.rawurlencode('Hello Father Care Bakery, I would like to place an order or ask a question.');
        $mapUrl = 'https://www.google.com/maps?q='.urlencode(config('bakery.address')).'&output=embed';

        return view('customer.contact.index', [
            'whatsappUrl' => $whatsappUrl,
            'mapUrl' => $mapUrl,
        ]);
    }
}
