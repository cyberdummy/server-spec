<?php
use \Cyberdummy\ServerSpec\RunnerInterface;
use \Cyberdummy\ServerSpec\SshRunner;
use \Cyberdummy\ServerSpec\LocalRunner;
use phpseclib\Net\SSH2;
use \Mockery as m;
use Symfony\Component\Process\Process;

class RunnerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function ssh()
    {
        $ssh2 = m::mock(SSH2::class)
            ->shouldReceive('exec')
            ->once()
            ->with('ls -lah')
            ->andReturn("files")
            ->shouldReceive('disconnect')
            ->getMock();

        $runner = new SshRunner($ssh2);
        $ret = $runner->run(new Process('ls -lah'));
        $this->assertSame("files", $ret);
    }

    /** @test */
    public function local()
    {
        $process = m::mock(Process::class)
            ->shouldReceive('mustRun')
            ->once()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('files')
            ->shouldReceive('stop')
            ->getMock();

        $runner = new LocalRunner();
        $ret = $runner->run($process);
        $this->assertSame("files", $ret);
    }
}
