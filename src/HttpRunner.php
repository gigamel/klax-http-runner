<?php

declare(strict_types=1);

namespace Klax\Http\Runner;

use Klax\Http\Runner\Enum\RequestAttribute;
use Klax\Http\Skeleton\Runner\AbstractHttpRunner;
use Psr\Http\Message\ServerRequestInterface;

readonly class HttpRunner extends AbstractHttpRunner
{
    public function run(ServerRequestInterface $request): void
    {
        parent::run($request->withAttribute(
            RequestAttribute::STARTUP_TIME,
            (float) microtime(true),
        ));
    }
}
