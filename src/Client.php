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
     * The stats API allows you to retrieve some account-wide information. Don't buy a nice car to overcompensate;
     * instead, show off how many hours of your video have been played! Or, celebrate when you reach a certain landmark.
     *
     * @return mixed
     */
    public function getStatsAccount()
    {
        return $this->get('stats/account.json');
    }

    /**
     * The stats API allows you to retrieve information about all of the videos in a particular project. We know you
     * like it when we let you get specific.
     *
     * @param int $projectId
     *
     * @return mixed
     */
    public function getStatsProjects($projectId)
    {
        return $this->get("stats/projects/{$projectId}.json");
    }

    /**
     * The Wistia stats API can be used to retrieve stats for any given video. Ever wanted to entice that special
     * someone (or those hundreds of special someones viewing your page) to watch your video? Win their heart by
     * displaying impressive data like your engagement rate. Or, give away a puppy to the thousandth viewer of your
     * video. We heard you can 3D print those now.
     *
     * @param int $mediaId
     *
     * @return mixed
     */
    public function getStatsMedias($mediaId)
    {
        return $this->get("stats/medias/{$mediaId}.json");
    }

    /**
     * Using the stats API, you can retrieve the data used to construct the engagement graphs at the top of the stats
     * page for any video in Wistia.
     *
     * @param int $mediaId
     *
     * @return mixed
     */
    public function getStatsMediasEngagement($mediaId)
    {
        return $this->get("stats/medias/{$mediaId}/engagement.json");
    }

    /**
     * This method allows you to retrieve a list of visitors that have watched videos in your account.
     *
     * @param int $page
     * @param int $perPage
     * @param string $filter
     * @param string $search
     *
     * @return mixed
     */
    public function getStatsVisitors($page = 1, $perPage = 100, $filter = null, $search = null)
    {
        $query = ['page' => $page, 'per_page' => $perPage];

        if (!empty($filter)) {
            $query['filter'] = $filter;
        }

        if (!empty($search)) {
            $query['search'] = $search;
        }

        return $this->get('stats/visitors.json', ['query' => $query]);
    }

    /**
     * This method allows you to retrieve the information for a single visitor.
     *
     * @param string $visitorKey
     *
     * @return mixed
     */
    public function getStatsVisitor($visitorKey)
    {
        return $this->get("stats/visitors/{$visitorKey}.json");
    }

    /**
     * This method allows you to retrieve a list of events (viewing sessions) from your account.
     *
     * @param int $page
     * @param int $perPage
     * @param string $mediaId
     * @param string $visitorKey
     * @param string $startDate
     * @param string $endDate
     *
     * @return mixed
     */
    public function getStatsEvents($page = 1, $perPage = 100, $mediaId = null, $visitorKey = null, $startDate = null, $endDate = null)
    {
        $query = ['page' => $page, 'per_page' => $perPage];

        if (!empty($mediaId)) {
            $query['media_id'] = $mediaId;
        }

        if (!empty($visitorKey)) {
            $query['visitor_key'] = $visitorKey;
        }

        if (!empty($startDate)) {
            $query['start_date'] = date('Y-m-d', strtotime($startDate));
        }

        if (!empty($endDate)) {
            $query['end_date'] = date('Y-m-d', strtotime($endDate));
        }

        return $this->get('stats/events.json', ['query' => $query]);
    }

    /**
     * This method gives you the information about a single event from your account.
     *
     * @param string $eventKey
     *
     * @return mixed
     */
    public function getStatsEvent($eventKey)
    {
        return $this->get("stats/events/{$eventKey}.json");
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

        return ($response->getStatusCode() === 200) ? $response->json() : false;
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
