<?php

namespace Chrometoaster\SiteHost\API;

class Client
{
    /**
     * API endpoint URL
     *
     * @var string
     */
    private $api_url = 'https://mysth.safeserver.net.nz';

    /**
     * API key for the client (whitelisted to a set of IPs)
     *
     * @var string
     */
    private $api_key;

    /**
     * Client ID
     *
     * @var string
     */
    private $client_id;


    /**
     * Client constructor
     *
     * @param string $api_key
     * @param string $client_id
     */
    public function __construct(string $api_key, string $client_id)
    {
        $this->api_key   = $api_key;
        $this->client_id = $client_id;
    }


    /**
     * Provide an API URL should it change from the default
     *
     * @param string $api_url
     * @return $this
     */
    public function setApiUrl(string $api_url): self
    {
        $this->api_url = rtrim($api_url, '/');

        return $this;
    }


    /**
     * Get a cURL resource and set the URL
     *
     * @param string $url
     * @return false|resource
     */
    private function getCURL(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        return $ch;
    }


    /**
     * Run a CURL request and return a valid or an empty reponse
     *
     * @param $ch
     * @return Response
     */
    private function executeCURL($ch): Response
    {
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 200) {
            $res = json_decode($response);

            if (json_last_error() === JSON_ERROR_NONE) {
                return new Response($res);
            }
        }

        return new Response();
    }


    /**
     * Compose an array of query/post data to pass to the API
     *
     * @param array $data
     * @return array
     */
    private function prepareQueryData(array $data): array
    {
        $query = [
            'apikey'    => $this->api_key,
            'client_id' => $this->client_id,
        ];
        if (count($data)) {
            unset($data['apikey']);
            unset($data['client_id']);

            $query = array_merge($query, $data);
        }

        return $query;
    }


    /**
     * Run a GET type of API request
     *
     * @param string $endpoint
     * @param array $data
     * @return Response
     */
    private function requestGET(string $endpoint, array $data = [])
    {
        $url = $this->api_url . $endpoint . '?' . http_build_query($this->prepareQueryData($data));
        $ch  = $this->getCURL($url);

        return $this->executeCURL($ch);
    }


    /**
     * Run a POST type of API request
     *
     * @param string $endpoint
     * @param array $data
     * @return Response
     */
    private function requestPOST(string $endpoint, array $data)
    {
        $url = $this->api_url . $endpoint;
        $ch  = $this->getCURL($url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->prepareQueryData($data));

        return $this->executeCURL($ch);
    }


    /**
     * Get an information about an asynchronous job processed by the API
     *
     * @param string $jobID
     * @param string $jobType
     * @return Job
     */
    public function getJobInfo(string $jobID, string $jobType = Constants::JOB_TYPE_SCHEDULER)
    {
        $response = $this->requestGET(Constants::ENDPOINT_JOB_GET, ['job_id' => $jobID, 'type' => $jobType]);

        return Job::createFromResponse($response);
    }


    /**
     * Get information based on the API key
     *
     * This provides e.g. the client_id needed for other calls and a list of roles for the user.
     * The client ID can also be found through the admin web interface.
     *
     * @return Response
     */
    public function getAPIInfo()
    {
        return $this->requestGET(Constants::ENDPOINT_API_GETINFO);
    }


    /**
     * Get information for a particular cloud container (cloud stack) sitting on a given server
     *
     * When looking at a particular container through the web interface, the URL is something like
     * https://cp.sitehost.nz/cloud/manage-container/server/ex-myserver/stack/abc123def456ab12
     * so ex-myserver is the server name and abc123def456ab12 is the container identification.
     *
     * @param string $server
     * @param string $stack
     * @return Response
     */
    public function getStackInfo(string $server, string $stack)
    {
        return $this->requestGET(Constants::ENDPOINT_CLOUD_STACK_GET, ['server' => $server, 'name' => $stack]);
    }


    /**
     * Update information for a particular cloud container (cloud stack) sitting on a giver server
     *
     * Items that can be updated:
     * - label
     * - docker-compose.yml file
     * - environments
     *
     * @param string $server
     * @param string $stack
     * @param string $label
     * @param string $dockerComposeFile
     * @param string $environments
     * @return Response
     */
    public function updateStackInfo(string $server, string $stack, string $label = '', string $dockerComposeFile = '', string $environments = '')
    {
        $data = [
            'server' => $server,
            'name'   => $stack,
        ];

        if ($label) {
            $data['params[label]'] = $label;
        }
        if ($dockerComposeFile) {
            $data['params[docker_compose]'] = $dockerComposeFile;
        }
        if ($environments) {
            $data['params[environments]'] = $environments;
        }

        return $this->requestPOST(Constants::ENDPOINT_CLOUD_STACK_UPDATE, $data);
    }


    /**
     * Update label for a particular cloud container (cloud stack) sitting on a giver server
     *
     * @param string $server
     * @param string $stack
     * @param string $label
     * @return Response
     */
    public function updateStackLabel(string $server, string $stack, string $label)
    {
        return $this->updateStackInfo($server, $stack, $label);
    }


    /**
     * Update docker-compose.yml for a particular cloud container (cloud stack) sitting on a giver server
     *
     * @param string $server
     * @param string $stack
     * @param string $dockerComposeFile
     * @return Response
     */
    public function updateStackDockerComposeFile(string $server, string $stack, string $dockerComposeFile)
    {
        return $this->updateStackInfo($server, $stack, '', $dockerComposeFile);
    }


    /**
     * Update environments for a particular cloud container (cloud stack) sitting on a giver server
     *
     * @param string $server
     * @param string $stack
     * @param string $environments
     * @return Response
     */
    public function updateStackEnvironments(string $server, string $stack, string $environments)
    {
        return $this->updateStackInfo($server, $stack, '', '', $environments);
    }


    /**
     * Restart a stack
     *
     * @param string $server
     * @param string $stack
     * @param string $label
     * @param string $dockerComposeFile
     * @param string $environments
     * @return Response
     */
    public function restartStack(string $server, string $stack, string $container = '')
    {
        $data = [
            'server' => $server,
            'name'   => $stack,
        ];

        if ($container) {
            $data['containers[]'] = $container;
        }

        return $this->requestPOST(Constants::ENDPOINT_CLOUD_STACK_RESTART, $data);
    }
}
