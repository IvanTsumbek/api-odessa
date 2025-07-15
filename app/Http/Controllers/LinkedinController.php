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

            $accessToken = $responseData['access_token'] ?? null;

            if (!$accessToken) {
                return redirect()->route('home')->with('error', 'Access token не получен.');
            }

            // 🔹 Получаем ID пользователя из LinkedIn
            $profileResponse = Http::withToken($accessToken)
                ->get('https://api.linkedin.com/v2/me');

            $profileData = $profileResponse->json();
            $linkedinId = $profileData['id'] ?? null;

            if (!$linkedinId) {
                return redirect()->route('home')->with('error', 'Не удалось получить LinkedIn ID.');
            }

            // 🔹 Сохраняем и токен, и ID
            $user = Auth::user();
            $user->json = json_encode($responseData);
            $user->linkedin_id = $linkedinId;
            $user->save();

            return redirect()->route('linkedin.post')->with('success', 'Токен и ID сохранены!');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Ошибка при обращении к LinkedIn: ' . $e->getMessage());
        }
    }
}
