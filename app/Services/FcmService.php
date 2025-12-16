<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FcmService
{
    private string $projectId;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
    }

    /**
     * Send FCM Notification
     */
    public function sendNotification(string $fcmToken, string $title, string $body, array $data = [])
    {
        $accessToken = $this->getAccessToken();

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            "message" => [
                "token" => $fcmToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                "data" => $data
            ]
        ];

        return Http::withToken($accessToken)->post($url, $payload)->json();
    }

    /**
     * Get Access Token from Firebase Service Account
     */
    private function getAccessToken(): string
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $jsonKeyFile = storage_path('app/firebase/firebase-key.json');

        $credentials = new ServiceAccountCredentials($scopes, $jsonKeyFile);
        $token = $credentials->fetchAuthToken();

        return $token['access_token'];
    }
}
