<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    private Client $http;

    public function __construct()
    {
        $base = rtrim((string) config('services.api.base_url'), '/') . '/';

        // Retry middleware: up to 3 times for connection-related issues
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry(function ($retries, $request, $response, $exception) {
            return $retries < 3 && $exception instanceof ConnectException;
        }));

        $this->http = new Client([
            'base_uri'        => $base,
            'timeout'         => 10, // fail faster
            'connect_timeout' => 5,
            'handler'         => $stack,
        ]);
    }

    public function post(string $uri, array $payload = [], ?string $bearer = null, bool $asJson = false): array
    {
        $options = $asJson ? ['json' => $payload] : ['form_params' => $payload];

        if ($bearer) {
            $options['headers']['Authorization'] = 'Bearer ' . $bearer;
        }

        try {
            $res = $this->http->post(ltrim($uri, '/'), $options);

            $body = (string) $res->getBody();
            $data = $body !== '' ? json_decode($body, true) : null;

            return [
                'ok'     => $res->getStatusCode() >= 200 && $res->getStatusCode() < 300,
                'status' => $res->getStatusCode(),
                'data'   => $data,
            ];
        } catch (RequestException | ConnectException | TransferException $e) {
            return $this->handleException($e, $uri);
        }
    }

    private function handleException($e, string $uri): array
    {
        $ctx = method_exists($e, 'getHandlerContext') ? $e->getHandlerContext() : [];
        $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;

        $payload = null;
        if ($response) {
            $raw = (string) $response->getBody();
            $payload = $raw !== '' ? json_decode($raw, true) : null;
        }

        $curlErrno   = $ctx['errno'] ?? null;
        $curlError   = $ctx['error'] ?? null;
        $timedOut    = $ctx['timed_out'] ?? null;
        $connectTime = $ctx['connect_time'] ?? null;

        $message = $payload['message'] ?? $curlError ?? $e->getMessage();

        $code = match (true) {
            $timedOut === true,
            $curlErrno === 28 => 'ETIMEOUT',
            $e instanceof ConnectException => 'ECONNECT',
            default => $response?->getStatusCode() ?? 0,
        };

        // Optional: log the error for debugging
        Log::error('API Request Failed', [
            'uri'          => $uri,
            'error'        => $message,
            'curl_errno'   => $curlErrno,
            'curl_error'   => $curlError,
            'timed_out'    => $timedOut,
            'connect_time' => $connectTime,
            'status'       => $response?->getStatusCode(),
        ]);

        return [
            'ok'       => false,
            'status'   => is_int($code) ? $code : 0,
            'code'     => is_string($code) ? $code : null,
            'error'    => $message,
            'errors'   => $payload['errors'] ?? null,
            'context'  => [
                'uri'          => (string) ($e->getRequest()?->getUri() ?? $this->http->getConfig('base_uri') . ltrim($uri, '/')),
                'curl_errno'   => $curlErrno,
                'curl_error'   => $curlError,
                'timed_out'    => $timedOut,
                'connect_time' => $connectTime,
            ],
        ];
    }
}
