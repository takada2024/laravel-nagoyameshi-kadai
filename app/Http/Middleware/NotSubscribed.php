<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotSubscribed
{
    /**
     * 受信リクエストの処理
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->subscribed('premium_plan')) {
            // ユーザーを支払いページへリダイレクトし、サブスクリプションを購入するか尋ねる
            return redirect('subscription/edit');
        }

        return $next($request);
    }
}
