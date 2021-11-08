<?php

namespace AcMarche\Mercredi\Utils;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * not use
 */
class ProcessUtils
{
    public static function lunchSend()
    {
        $process = new Process(['bin/console', 'mercredi:send-facture', '10-2021']);
        $process->setWorkingDirectory(getcwd()."/../");
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            var_dump($process);
            throw new ProcessFailedException($process);
        }

        var_dump($process->getOutput());
    }
}
