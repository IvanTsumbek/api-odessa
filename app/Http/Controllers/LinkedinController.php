<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class LinkedinController extends Controller
{
    public string $url = 'https://www.linkedin.com/oauth/v2/';

    public function redirect()
    {
        $params = [
            'response_type' => 'code',
            'client_id' => env('LINKEDIN_ID'),
            'redirect_uri' => env('LINKEDIN_REDIRECT_URL'),
            'scope' => 'openid profile w_member_social email'
        ];
        $query = http_build_query($params);

        return redirect($this->url . 'authorization?' . $query);
    }

    public function callback(Request $request)
    {
        try {
            $code = $request->get('code');

            $response = Http::asForm()->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->post($this->url . 'accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' =>  env('LINKEDIN_ID'),
                'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
                'redirect_uri' => env('LINKEDIN_REDIRECT_URL'),
            ]);

            $responseData = $response->json();

            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            $user->json = json_encode($responseData);
            $user->save();

            return redirect()->route('linkedin.post')->with('success', 'LinkedIn токен сохранён!');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Ошибка при обращении к LinkedIn: ' . $e->getMessage());
        }
    }
}
