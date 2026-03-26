<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\VerifyEmailResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // メール認証完了後のリダイレクト先を変更
        $this->app->singleton(VerifyEmailResponse::class, function () {
            return new class implements VerifyEmailResponse {
                public function toResponse($request)
                {
                    return redirect('/mypage/profile');
                }
            };
        });
    }

    public function boot(): void
    {
        // ログイン画面表示
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // 会員登録画面表示
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // 会員登録処理
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::createUsersUsing(CreateNewUser::class);

        // 認証処理本体は Fortify を使う
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });
    }
}