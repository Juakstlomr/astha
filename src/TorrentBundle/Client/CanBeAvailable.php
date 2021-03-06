<?php

declare(strict_types=1);

namespace TorrentBundle\Client;

interface CanBeAvailable
{
    public function isAvailable(): bool;
}
