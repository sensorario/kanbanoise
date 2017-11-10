<?php

namespace AppBundle\Responses;

use Symfony\Component\HttpFoundation\JsonResponse;

final class KResponse
{
    public static function createSuccess()
    {
        return self::withSuccess(true);
    }

    public static function createFailure()
    {
        return self::withSuccess(false);
    }

    public static function withSuccess(bool $success)
    {
        return new JsonResponse([
            'success' => $success,
        ]);
    }
}
