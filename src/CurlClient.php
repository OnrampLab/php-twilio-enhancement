<?php

namespace Onramplab\TwilioEnhancement;

use Psr\Log\LoggerInterface;
use Twilio\Exceptions\EnvironmentException;
use Twilio\Http\CurlClient as TwilioCurlClient;
use Twilio\Http\Response;

class CurlClient extends TwilioCurlClient
{
    protected ?LoggerInterface $logger;

    public function __construct(array $options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options);

        $this->logger = $logger;
    }

    public function request(
        string $method,
        string $url,
        array $params = [],
        array $data = [],
        array $headers = [],
        ?string $user = null,
        ?string $password = null,
        ?int $timeout = null
    ): Response {
        $options = $this->options(
            $method,
            $url,
            $params,
            $data,
            $headers,
            $user,
            $password,
            $timeout
        );

        $this->lastRequest = $options;
        $this->lastResponse = null;

        try {
            $curl = \curl_init();

            /** @phpstan-ignore-next-line  */
            if (! $curl) {
                throw new EnvironmentException('Unable to initialize cURL');
            }

            if (! \curl_setopt_array($curl, $options)) {
                throw new EnvironmentException(\curl_error($curl));
            }

            $response = \curl_exec($curl);

            if (! $response) {
                throw new EnvironmentException(\curl_error($curl));
            }

            $statusCode = $this->getStatusCode($curl);

            \curl_close($curl);

            if (isset($options[CURLOPT_INFILE]) && \is_resource($options[CURLOPT_INFILE])) {
                \fclose($options[CURLOPT_INFILE]);
            }

            $this->lastResponse = $this->getResponse($response, $statusCode);

            return $this->lastResponse;
        } catch (\ErrorException $e) {
            /** @phpstan-ignore-next-line  */
            if (isset($curl) && \is_resource($curl)) {
                \curl_close($curl);
            }

            if (isset($options[CURLOPT_INFILE]) && \is_resource($options[CURLOPT_INFILE])) {
                \fclose($options[CURLOPT_INFILE]);
            }

            throw $e;
        }
    }

    protected function getHeaders(string $response): array
    {
        $parts = \explode("\r\n\r\n", $response, 3);

        $head = $this->isSpecialStatus($parts[0]) ? $parts[1] : $parts[0];

        $responseHeaders = [];
        $headerLines = \explode("\r\n", $head);
        \array_shift($headerLines);
        foreach ($headerLines as $line) {
            [$key, $value] = \explode(':', $line, 2);
            $responseHeaders[$key] = $value;
        }

        return $responseHeaders;
    }

    protected function getBody(string $response): string
    {
        $parts = \explode("\r\n\r\n", $response, 3);

        return $this->isSpecialStatus($parts[0]) ? $parts[2] : $parts[1];
    }

    /**
     * @param resource $curl
     */
    protected function getStatusCode($curl): int
    {
        return \curl_getinfo($curl, CURLINFO_HTTP_CODE);
    }

    /**
     * @param $response string|bool
     */
    /** @phpstan-ignore-next-line  */
    private function getResponse($response, int $statusCode): Response
    {
        $responseHeaders = $this->getHeaders($response);
        $body = $this->getBody($response);

        if (isset($this->logger)) {
            $this->logger->debug('Twilio response', [
                'headers' => $responseHeaders,
                'body' => $body,
            ]);
        }

        return new Response($statusCode, $body, $responseHeaders);
    }

    private function isSpecialStatus(string $part1): bool
    {
        return \preg_match('/\AHTTP\/1.\d 100 Continue\Z/', $part1)
            || \preg_match('/\AHTTP\/1.\d 200 Connection established\Z/', $part1)
            || \preg_match('/\AHTTP\/1.\d 200 Tunnel established\Z/', $part1);
    }
}
