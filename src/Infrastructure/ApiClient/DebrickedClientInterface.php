<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiClient;

use Doctrine\Common\Collections\Collection;

interface DebrickedClientInterface
{
    public function checkStatusFileScanning(string $ciUploadId): array;

    public function scanFiles(Collection $files, string $repositoryName, string $commitName): string;
}
