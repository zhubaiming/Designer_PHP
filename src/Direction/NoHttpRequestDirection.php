<?php

declare(strict_types=1);

namespace Hongyi\Designer\Direction;

use Hongyi\Designer\Contracts\DirectionInterface;
use Hongyi\Designer\Contracts\PackerInterface;
use Psr\Http\Message\ResponseInterface;

class NoHttpRequestDirection implements DirectionInterface
{
    public function guide(PackerInterface $packer, ?ResponseInterface $response, array $params = [])
    {
        return $response;
    }
}