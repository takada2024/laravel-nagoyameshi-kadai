<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     *  create
     */
    public function create()
    {
        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    /**
     *  store
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->user()->newSubscription(
            'premium_plan',
            'price_1PgMVLGeo7j2tfrTS0pOZqj8'
        )->create($request->paymentMethodId);

        return redirect()->route('home')->with('flash_message', '有料プランの登録が完了しました。');
    }

    /**
     *  edit
     */
    public function edit(User $user)
    {
        $user = Auth::user();
        $intent = $user->createSetupIntent();

        return view('subscription.edit', compact('user', 'intent'));
    }

    /**
     *  update
     */
    public function update(Request $request, User $user)
    {
        $user = $request->user();

        $user->updateDefaultPaymentMethod($request->paymentMethodId);

        return redirect()->route('home')->with('flash_message', 'お支払方法を変更しました。');
    }

    /**
     *  cancel
     */
    public function cancel()
    {
        return view('subscription.cancel');
    }

    /**
     *  destroy
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        $user->subscription('premium_plan')->cancelNow();

        return redirect()->route('home')->with('flash_message', '有料プランを解約しました。');
    }
}
