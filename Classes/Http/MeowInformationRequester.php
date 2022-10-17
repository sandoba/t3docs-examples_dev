<?php

declare(strict_types=1);

namespace T3docs\Examples\Http;

use TYPO3\CMS\Core\Http\RequestFactory;

final class MeowInformationRequester
{
    private const API_URL = 'https://catfact.ninja/fact';

    // We need the RequestFactory for creating and sending a request,
    // so we inject it into the class using constructor injection.
    public function __construct(
        private readonly RequestFactory $requestFactory,
    ) {
    }

    public function request(): string
    {
        // Additional headers for this specific request
        // See: https://docs.guzzlephp.org/en/stable/request-options.html
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'allow_redirects' => false,
        ];

        // Get a PSR-7-compliant response object
        $response = $this->requestFactory->request(
            self::API_URL,
            'GET',
            $additionalOptions
        );

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(
                'Returned status code is ' . $response->getStatusCode()
            );
        }

        if ($response->getHeaderLine('Content-Type') !== 'application/json') {
            throw new \RuntimeException(
                'The request did not return JSON data'
            );
        }

        // Get the content as a string on a successful request
        return json_decode($response->getBody()->getContents(), true)['fact']
            ?? throw new \RuntimeException('No information available');
    }
}