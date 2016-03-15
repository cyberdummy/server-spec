<?php
use \Cyberdummy\ServerSpec\Spec;
use \Cyberdummy\ServerSpec\RunnerInterface;
use \Mockery as m;

class SpecTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function cpuClock()
    {
        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("3400000\n3400000\n3400000\n3400000")
            ->getMock();

        $spec = new Spec($runner);
        $clock = $spec->cpuClock();

        $this->assertEquals(3400, $clock);
    }

    /** @test */
    public function cpuCores()
    {
        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("3400000\n3400000\n3400000\n3400000\n")
            ->getMock();

        $spec = new Spec($runner);
        $cores = $spec->cpuCores();

        $this->assertEquals(4, $cores);
    }

    /** @test */
    public function ram()
    {
        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("MemTotal:        7840460 kB\nMemFree:         1956100 kB")
            ->getMock();

        $spec = new Spec($runner);
        $ram = $spec->ram();

        $this->assertEquals(8, $ram);
    }

    /** @test */
    public function os()
    {
        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("CentOS release 6.7 (Final)\n CentOS release 6.7 (Final)\n CentOS release 6.7 (Final)")
            ->getMock();

        $spec = new Spec($runner);
        $os = $spec->os();

        $this->assertEquals("CentOS release 6.7 (Final)", $os);


        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("DISTRIB_ID=Ubuntu
            DISTRIB_RELEASE=14.04
            DISTRIB_CODENAME=trusty
            DISTRIB_DESCRIPTION=\"Ubuntu 14.04.3 LTS\"
            NAME=\"Ubuntu\"
            VERSION=\"14.04.3 LTS, Trusty Tahr\"
            ID=ubuntu
            ID_LIKE=debian
            PRETTY_NAME=\"Ubuntu 14.04.3 LTS\"
            ")
            ->getMock();

        $spec = new Spec($runner);
        $os = $spec->os();

        $this->assertEquals("Ubuntu 14.04.3 LTS", $os);
    }

    /** @test */
    public function kernelVersion()
    {
        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("4.1.5-x86_64-linode61")
            ->getMock();

        $spec = new Spec($runner);
        $kv = $spec->kernelVersion();

        $this->assertEquals("4.1.5", $kv);
    }
}
