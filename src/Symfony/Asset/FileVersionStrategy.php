<?php
declare(strict_types=1);

namespace App\Symfony\Asset;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class FileVersionStrategy implements VersionStrategyInterface
{
    public function getVersion(string $path): string
    {
        return (string)filemtime($path);
    }

    public function applyVersion(string $path): string
    {
        return $path . '?v=' . $this->getVersion($path);
    }
}
