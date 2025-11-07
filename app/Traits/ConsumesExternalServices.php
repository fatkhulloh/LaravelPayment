<?php
namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumesExternalServices
{
    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [], $isJsonRequest = false)
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        $options = [
            'headers' => $headers,
            'query' => $queryParams,
        ];

        if ($isJsonRequest) {
            $options['json'] = $formParams;
        } else {
            $options['form_params'] = $formParams;
        }

        $response = $client->request($method, $requestUrl, $options);
        $response = $response->getBody()->getContents();

        if (method_exists($this, 'decodeResponse')) {
            $response = $this->decodeResponse($response);
        }

        return $response;
    }
}
