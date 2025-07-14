<?php
namespace App\Http\Controllers;

use App\Http\Requests\LinkedinPostStoreRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LinkedinPostStoreController extends Controller
{
    public function store(LinkedinPostStoreRequest $request)
    {
        $user = Auth::user();
        $accessToken = $user->json ? json_decode($user->json, true)['access_token'] ?? null : null;

        if (!$accessToken) {
            return redirect()->back()->with('error', 'Нет токена LinkedIn для публикации.');
        }

        $message = $request->input('message');

        // Пример запроса на публикацию в LinkedIn (упрощённо)
        $response = Http::withToken($accessToken)
            ->post('https://api.linkedin.com/v2/ugcPosts', [
                'author' => 'urn:li:person:' . $user->linkedin_id, // linkedin_id нужно хранить отдельно
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $message,
                        ],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Пост опубликован в LinkedIn!');
        }

        return redirect()->back()->with('error', 'Ошибка при публикации поста.');
    }
}
