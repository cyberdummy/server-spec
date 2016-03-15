<?php

namespace Cyberdummy\ServerSpec;

use Symfony\Component\Process\Process;

interface RunnerInterface
{
    public function run(Process $command);
}
