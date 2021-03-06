<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Http\Controllers\ErrorController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * Middleware to handle and render errors.
 */
class ExceptionHandler implements MiddlewareInterface
{
    /** @var ErrorController */
    private $error_controller;

    /**
     * ExceptionHandler constructor.
     *
     * @param ErrorController $error_controller
     */
    public function __construct(ErrorController $error_controller)
    {
        $this->error_controller = $error_controller;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $exception) {
            if ($request->getHeaderLine('X-Requested-With') !== '') {
                return $this->error_controller->ajaxErrorResponse($exception);
            }

            return $this->error_controller->errorResponse($exception);
        } catch (Throwable $exception) {
            return $this->error_controller->unhandledExceptionResponse($request, $exception);
        }
    }
}
