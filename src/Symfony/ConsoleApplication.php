<?php
declare(strict_types=1);

namespace App\Symfony;

use Symfony\Bundle\FrameworkBundle\Console\Application;

class ConsoleApplication extends Application
{
    public function getLongVersion(): string
    {
        return
            $this->getSymfonyLongVersion()
            . sprintf(
                ' (env: <comment>%s</>, debug: <comment>%s</>)',
                $this->getKernel()->getEnvironment(),
                $this->getKernel()->isDebug() ? 'true' : 'false'
            )
        ;
    }

    private function getSymfonyLongVersion(): string
    {
        if ('UNKNOWN' !== $this->getName()) {
            if ('UNKNOWN' !== $this->getVersion()) {
                return sprintf('%s <info>%s</info>', $this->getName(), $this->getVersion());
            }

            return $this->getName();
        }

        return 'Console Tool';
    }
}
