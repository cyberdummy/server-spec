<?php

namespace Cyberdummy\ServerSpec;

use Symfony\Component\Process\Process;

class LocalRunner implements RunnerInterface
{
    public function run(Process $command)
    {
        $command->mustRun();
        return $command->getOutput();
    }
}
