<?php

namespace Cyberdummy\ServerSpec;

use Symfony\Component\Process\Process;
use phpseclib\Net\SSH2;

class SshRunner implements RunnerInterface
{
    /**
     * @var mixed
     */
    protected $sshConnection;

    public function __construct(SSH2 $sshConnection)
    {
        $this->sshConnection = $sshConnection;
    }

    public function run(Process $command)
    {
        return $this->sshConnection->exec($command->getCommandLine());
    }
}
