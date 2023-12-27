<?php

namespace App\Services;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Ypareo service
 */
class Ypareo
{
    /**
     * @var string
     */
    protected $apiKey = null;

    /**
     * @var string
     */
    protected $baseUrl = null;

    /**
     * Construct Ypareo service
     *
     * @param string $apiKey
     * @param string $baseUrl
     */
    public function __construct($apiKey, $baseUrl)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Retrieve CSRF token from login page
     *
     * @param  string $userAgent
     * @return string
     */
    protected function getLoginCsrf($userAgent, CookieJar $cookieJar): string
    {
        return '**CSRF**';
    }

    /**
     * Authenticate a user against Ypareo
     *
     * @param  string $username
     * @param  string $password
     * @param  string $userAgent
     * @return bool
     */
    public function auth($username, $password, $userAgent): bool
    {
        return true;
    }

    /**
     * Get active employee users
     *
     * @return \Illuminate\Support\Collection
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function getEmployeeUsers()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Auth-Token' => $this->apiKey,
        ])->get($this->baseUrl . '/r/v1/utilisateur/personnels');

        $response->throw();

        return $response->collect();
    }

    /**
     * Get active student users
     *
     * @return \Illuminate\Support\Collection
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function getStudentUsers()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Auth-Token' => $this->apiKey,
        ])->get($this->baseUrl . '/r/v1/utilisateur/apprenants');

        $response->throw();

        return $response->collect();
    }

    /**
     * Get active users
     *
     * @return \Illuminate\Support\Collection
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getUsers()
    {
        $users = [];

        $this->getEmployeeUsers()->each(function ($u) use (&$users) {
            $users[] = [
                'is_student' => false,
                'ypareo_id' => $u['codePersonnel'],
                'ypareo_login' => $u['login'],
                'lastname' => $u['nom'],
                'firstname' => $u['prenom'],
                'email' => $u['email'],
            ];
        });

        $this->getStudentUsers()->each(function ($u) use (&$users) {
            $users[] = [
                'is_student' => true,
                'ypareo_id' => $u['codeApprenant'],
                'ypareo_login' => $u['login'],
                'lastname' => $u['nom'],
                'firstname' => $u['prenom'],
                'email' => $u['email'],
            ];
        });

        return collect($users);
    }
}
