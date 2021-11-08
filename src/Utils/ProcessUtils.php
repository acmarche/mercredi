<?php

namespace AcMarche\Mercredi\Utils;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessUtils
{
    public static function lunchSend()
    {
        $process = new Process(['/homez.76/atlhotq/www/atl/tools/symfony']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}
