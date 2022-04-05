<?php

declare(strict_types=1);

namespace Rudashi\Optima\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Rudashi\Optima\Services\DatabaseHealthCheckService;
use Symfony\Component\HttpFoundation\Response as ResponseBase;

class HealthCheckController extends Controller
{

    public function __invoke(DatabaseHealthCheckService $check): Response
    {
        $health = $check->status();

        $status = $health['status'] === DatabaseHealthCheckService::OK
            ? ResponseBase::HTTP_OK
            : ResponseBase::HTTP_INTERNAL_SERVER_ERROR;

        $body = $status === ResponseBase::HTTP_OK
            ? ['message' => 'pong']
            : [
                'status' => $health['status'],
                'message' => $health['message'],
            ];

        return new Response($body, $status);
    }

}
