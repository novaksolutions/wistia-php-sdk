<?php

namespace Ipalaus\Wistia;

use GuzzleHttp\Client as HttpClient;

class Client
{
    /**
     * @const VERSION Current version of the SDK.
     */
    const VERSION = '0.0.1';

    /**
     * Wistia's base URL.
     *
     * @var string
     */
    protected $baseUrl = 'https://api.wistia.com/{version}/';

    /**
     * Wistia's API version.
     *
     * @var string
     */
    protected $version = 'v1';

    /**
     * Wistia token.
     *
     * @var null|string
     */
    protected $token;

    /**
     * Create a new Client instance.
     *
     * @param null|string $token
     */
    public function __construct($token = null)
    {
        $this->setToken($token);
    }

    /**
     * Obtain a list of all the media in your account. You can
     * page the returned list.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return mixed
     */
    public function getMedias($page = 1, $perPage = 100)
    {
        return $this->get('medias.json', ['query' => ['page' => $page, 'per_page' => $perPage]]);
    }

    /**
     * Get information about a specific piece of media that you
     * have uploaded to your account.
     *
     * @param string $hashedId
     *
     * @return array
     */
    public function getMedia($hashedId)
    {
        return $this->get("medias/{$hashedId}.json");
    }

    /**
     * Send a GET request.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed
     */
    protected function get($uri, $options = [])
    {
        $response = $this->getHttpClient()->get($uri, $options);

        if ($response->getStatusCode() === 200) {
            return $response->json();
        }
    }

    /**
     * Get an HTTP client to deal with.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        $client = new HttpClient([
            'base_url' => [$this->getBaseUrl(), ['version' => $this->getVersion()]],
            'defaults' => [
                'headers' => [
                    'User-Agent' => 'Ipalaus-Wistia/' . self::VERSION
                ],
                'auth'    => ['api', $this->getToken()]
            ],
        ]);

        return $client;
    }

    /**
     * Get Wistia's base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set Wistia's base URL.
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get Wistia's API version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set Wistia's API version.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get the token being used by the client.
     *
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the token to be used by the client.
     *
     * @param null|string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
