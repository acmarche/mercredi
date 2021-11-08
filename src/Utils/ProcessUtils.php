<?php

namespace AcMarche\Mercredi\Utils;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessUtils
{
    public static function lunchSend()
    {
        $process = new Process(['~/tools/symfony', 'console mercredi:test-mail jf@marche.be jf@marche.be']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}
