<?php
declare(strict_types=1);

namespace App\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function index()
    {
        return new JsonResponse(['hello' => 'world']);
    }

    public function sayPhrase(Request $request): Response
    {
        $client = new Client();

        $response = $client->post('https://westus.api.cognitive.microsoft.com/sts/v1.0/issueToken', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Ocp-Apim-Subscription-Key' => getenv('TEXT_TO_SPEECH_KEY')
            ]
        ]);

        $token = (string) $response->getBody();

        $body = '<speak version=\'1.0\' xmlns="http://www.w3.org/2001/10/synthesis" xml:lang=\'en-US\'><voice  name=\'Microsoft Server Speech Text to Speech Voice (en-US, BenjaminRUS)\'>'.$request->getContent().'</voice> </speak>';

        $response = $client->post('https://westus.tts.speech.microsoft.com/cognitiveservices/v1?language=en-US', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/ssml+xml',
                'X-Microsoft-OutputFormat' => 'audio-16khz-32kbitrate-mono-mp3'
            ],
            'body' => $body
        ]);

        $response = new Response((string) $response->getBody());

        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    public function getTweets(): Response
    {
        $client = new Client();

        $response = $response = $client->post('https://api.twitter.com/oauth2/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization' => 'Basic '.getenv('TWITTER_KEY')
            ],
            'body' => 'grant_type=client_credentials'
        ]);

        $token = json_decode((string) $response->getBody(), true)['access_token'];

        $response = $client->get('https://api.twitter.com/1.1/statuses/user_timeline.json?count=100&screen_name=twitterapi', [
            'headers' => [
                'Authorization' => 'Bearer '.$token
            ]
        ]);

        $tweets = json_decode((string) $response->getBody(), true);

        return new JsonResponse($tweets[0]);
    }
}
