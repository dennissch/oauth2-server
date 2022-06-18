<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server\Middleware;

use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ResourceServerMiddleware implements Middleware {
    /**
     * @var ResourceServer
     */
    private $server;

    /**
     * @param ResourceServer $server
     */
    public function __construct(ResourceServer $server) {
        $this->server = $server;
    }

    /**
     * @param Request           $request
     * @param RequestHandler    $handler
     *
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response {
        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
            // @codeCoverageIgnoreEnd
        }

        return $handler->handle($request);
    }
}
