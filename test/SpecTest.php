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
    public function distro()
    {
        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("CentOS release 6.7 (Final)\n CentOS release 6.7 (Final)\n CentOS release 6.7 (Final)")
            ->getMock();

        $spec = new Spec($runner);
        $distro = $spec->distro();

        $this->assertEquals("CentOS", $distro);


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
PRETTY_NAME=\"Ubuntu 14.04.3 LTS\"\n")
            ->getMock();

        $spec = new Spec($runner);
        $distro = $spec->distro();

        $this->assertEquals("Ubuntu", $distro);
    }

    /** @test */
    public function distroVersion()
    {
        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn("CentOS release 6.7 (Final)\n CentOS release 6.7 (Final)\n CentOS release 6.7 (Final)")
            ->getMock();

        $spec = new Spec($runner);
        $distro = $spec->distroVersion();

        $this->assertEquals("6.7", $distro);


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
PRETTY_NAME=\"Ubuntu 14.04.3 LTS\"\n")
            ->getMock();

        $spec = new Spec($runner);
        $distro = $spec->distroVersion();

        $this->assertEquals("14.04.3 LTS, Trusty Tahr", $distro);
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

    /** @test */
    public function df()
    {
        $return = "Filesystem     1M-blocks  Used Available Use% Mounted on
/dev/sda          48065M 4380M    43181M  10% /
none                  1M    0M        1M   0% /sys/fs/cgroup
devtmpfs            998M    1M      998M   1% /dev
none                200M    1M      200M   1% /run
none                  5M    0M        5M   0% /run/lock
none               1000M    1M     1000M   1% /run/shm
none                100M    0M      100M   0% /run/user"; 


        $runner = m::mock(RunnerInterface::class)
            ->shouldReceive('run')
            ->once()
            ->andReturn($return)
            ->getMock();

        $spec = new Spec($runner);
        $df = $spec->df();

        $this->assertCount(7, $df);

        $this->assertArrayHasKey('/', $df);
        $this->assertSame($df['/']['filesystem'], '/dev/sda');
        $this->assertSame($df['/']['size'], 48065);
        $this->assertSame($df['/']['used'], 4380);
        $this->assertSame($df['/']['available'], 43181);
        $this->assertSame($df['/']['use'], 10);
    }
}
