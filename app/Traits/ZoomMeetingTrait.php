<?php

namespace App\Traits;

use GuzzleHttp\Client;
use App\Models\Utility;
use Log;
use \Firebase\JWT\JWT;

/**
 * trait ZoomMeetingTrait
 */
trait ZoomMeetingTrait
{
    public $client;
    public $jwt;
    public $headers;
    public $meeting_url = "https://api.zoom.us/v2/";
    public function __construct()
    {
        $this->client = new Client();
    }


    private function retrieveZoomUrl()
    {
        return $this->meeting_url;
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        // dd($dateTime);
        try {
            $date = new \DateTime($dateTime);
            // dd($date);
            return $date->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());
            // dd($e);
            return '';
        }
    }

    public function createmitting($data)
    {
        $path = 'users/me/meetings';
        $url = $this->retrieveZoomUrl();
        // $data['start_time'] = '23-11-15 12:10:00';
        // dd($data);
        $body = [
            'headers' => $this->getHeader(),
            'verify' => false,
            'body'    => json_encode([
                'topic'      => $data['topic'],
                'type'       => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat($data['start_time']),
                'duration'   => $data['duration'],
                'password' => $data['password'],
                'agenda'     => (!empty($data['agenda'])) ? $data['agenda'] : null,
                'timezone'     => 'Asia/Kolkata',
                'settings'   => [
                    'host_video'        => ($data['host_video'] == "1") ? true : false,
                    'participant_video' => ($data['participant_video'] == "1") ? true : false,
                    'waiting_room'      => true,
                ],
            ]),
        ];

        $response =  $this->client->post($url . $path, $body);
        // dd($response);
        return [
            'success' => $response->getStatusCode() === 201,
            'data'    => json_decode($response->getBody(), true),
        ];
    }

    public function meetingUpdate($id, $data)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();

        $body = [
            'headers' => $this->getHeader(),
            'body'    => json_encode([
                'topic'      => $data['topic'],
                'type'       => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat($data['start_time']),
                'duration'   => $data['duration'],
                'agenda'     => (!empty($data['agenda'])) ? $data['agenda'] : null,
                'timezone'     => config('app.timezone'),
                'settings'   => [
                    'host_video'        => ($data['host_video'] == "1") ? true : false,
                    'participant_video' => ($data['participant_video'] == "1") ? true : false,
                    'waiting_room'      => true,
                ],
            ]),
        ];
        $response =  $this->client->patch($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
            'data'    => json_decode($response->getBody(), true),
        ];
    }

    public function get($id)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();

        $body = [
            'headers' => $this->getHeader(),
            'body'    => json_encode([]),
        ];

        $response =  $this->client->get($url . $path, $body);
        return [
            'success' => $response->getStatusCode() === 204,
            'data'    => json_decode($response->getBody(), true),
        ];
    }

    /**
     * @param string $id
     * 
     * @return bool[]
     */
    public function delete($id)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();
        $body = [
            'headers' => $this->headers,
            'body'    => json_encode([]),
        ];

        $response =  $this->client->delete($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
        ];
    }

    public function getHeader()
    {
        $token = $this->getToken();
        // dd($token);
        Log::info('Generated JWT for headers: ' . $token);

        return [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }


    public function getToken()
    {

        $settings  = Utility::settings(\Auth::user()->id);

        if ((isset($settings['zoom_apikey']) && !empty($settings['zoom_apikey'])) && (isset($settings['zoom_secret_key']) && !empty($settings['zoom_secret_key'])) && (isset($settings['zoom_account_id']) && !empty($settings['zoom_account_id']))) {
            try {
                $key = $settings['zoom_apikey'];
                $secret = $settings['zoom_secret_key'];
                $url = 'https://zoom.us/oauth/token';

                $credentials = base64_encode($key . ':' . $secret);

                $headers = array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Basic ' . $credentials,
                );

                $body = array(
                    'grant_type' => 'account_credentials',
                    'account_id' => $settings['zoom_account_id'],
                );

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);
                // dd($response);
                return $response['access_token'];

            } catch (\Exception $e) {
                Log::error('Error generating Zoom token: ' . $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }
}
