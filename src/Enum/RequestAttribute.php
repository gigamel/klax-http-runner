<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Enum;

final readonly class RequestAttribute
{
    public const string STARTUP_TIME = '_startup_microtime';
    public const string ACTION = '_action';
}
