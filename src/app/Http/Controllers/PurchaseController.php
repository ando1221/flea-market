<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Stripe\Stripe;

class PurchaseController extends Controller
{
    // 購入画面・支払い方法更新・購入実行はログイン済みユーザーのみ許可
    // webhook は Stripe サーバーから来るため認証不要
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profile.set'])
            ->except(['webhook', 'success']);
    }

    // 購入画面を表示する
    public function show(Request $request, Item $item)
    {
        // 売り切れ商品の場合は表示しない
        if ($item->status === 'sold') {
            abort(404);
        }

        $paymentMethods = PaymentMethod::query()
            ->orderBy('id')
            ->get();

        $shippingAddress = session("purchase_address.{$item->id}", [
            'zip' => $request->user()->zip,
            'address' => $request->user()->address,
            'building' => $request->user()->building,
        ]);

        $selectedPaymentMethodId = session("purchase_payment_method.{$item->id}");
        $selectedPaymentMethodLabel = null;

        // 選択済みの支払い方法がある場合
        if ($selectedPaymentMethodId) {
            $selectedPaymentMethod = $paymentMethods->firstWhere('id', (int) $selectedPaymentMethodId);
            $selectedPaymentMethodLabel = optional($selectedPaymentMethod)->label;
        }

        return view('purchase.show', compact(
            'item',
            'paymentMethods',
            'shippingAddress',
            'selectedPaymentMethodId',
            'selectedPaymentMethodLabel'
        ));
    }

    // 支払い方法を更新する
    public function update(PurchaseRequest $request, Item $item)
    {
        $validated = $request->validated();

        session([
            "purchase_payment_method.{$item->id}" => $validated['payment_method_id'],
        ]);

        return redirect()->route('purchase.show', $item);
    }

    // 購入ボタン押下時の処理
    public function store(Request $request, Item $item)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = $request->user();

        $shippingAddress = session("purchase_address.{$item->id}", [
            'zip' => $user->zip,
            'address' => $user->address,
            'building' => $user->building,
        ]);

        $paymentMethodId = session("purchase_payment_method.{$item->id}") ?? $request->input('payment_method_id');

        // 支払い方法が未選択の場合
        if (!$paymentMethodId) {
            return redirect()
                ->route('purchase.show', $item)
                ->withErrors(['payment_method_id' => '支払い方法を選択してください。']);
        }

        $session = $this->createCheckoutSession($item, $user, $shippingAddress, $paymentMethodId);

        return redirect($session->url);
    }

    // Stripe の決済セッションを作成する
    protected function createCheckoutSession(Item $item, $user, array $shippingAddress, $paymentMethodId)
    {
        return CheckoutSession::create([
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.show', ['item' => $item->id]),
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'item_id' => $item->id,
                'buyer_id' => $user->id,
                'payment_method_id' => (string) $paymentMethodId,
                'shipping_zip' => $shippingAddress['zip'],
                'shipping_address' => $shippingAddress['address'],
                'shipping_building' => $shippingAddress['building'] ?? '',
            ],
        ]);
    }

    // Stripe 決済完了後の戻り先
    public function success(Request $request, Item $item)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->query('session_id');

        // session_id がない場合は購入画面へ戻す
        if (!$sessionId) {
            return redirect()->route('purchase.show', $item->id);
        }

        $session = CheckoutSession::retrieve($sessionId);

        session()->forget("purchase_payment_method.{$item->id}");
        session()->forget("purchase_address.{$item->id}");

        return view('purchase.success', compact('item', 'session'));
    }

    // Stripe Webhook を受け取る
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            return response('Invalid webhook', 400);
        }

        // 決済完了イベントの場合のみ購入確定処理を行う
        if ($event->type === 'checkout.session.completed') {
            try {
                $session = $event->data->object;

                Purchase::firstOrCreate(
                    [
                        'item_id' => $session->metadata->item_id,
                    ],
                    [
                        'buyer_id' => $session->metadata->buyer_id,
                        'payment_method_id' => (int) $session->metadata->payment_method_id,
                        'shipping_zip' => $session->metadata->shipping_zip,
                        'shipping_address' => $session->metadata->shipping_address,
                        'shipping_building' => $session->metadata->shipping_building,
                        'purchased_at' => now(),
                    ]
                );

                Item::where('id', $session->metadata->item_id)
                    ->update(['status' => 'sold']);
            } catch (\Throwable $e) {
                return response('Webhook failed', 500);
            }
        }

        return response('OK', 200);
    }
}