<?php

namespace Cyberdummy\ServerSpec;

use Symfony\Component\Process\Process;

/**
 * Public API get the server specs
 *
 */
class Spec
{
    /**
     * Command runner instance
     */
    protected $runner;

    public function __construct(RunnerInterface $runner)
    {
        $this->runner = $runner;
    }

    /**
     * Get the distro
     *
     */
    public function distro()
    {
        $process = new Process('cat /etc/*release');
        $output  = $this->runner->run($process);
        $lines   = explode("\n", trim($output));

        $os = '';
        foreach ($lines as $line) {
            // Ubuntu / Arch / CentOS 7
            if (preg_match("/^NAME=/", $line) == 1) {
                $os = trim(explode("=", trim($line))[1], '"');
                break;
            }
            // CentOS
            if (strpos($line, "CentOS") !== false) {
                $os = "CentOS";
                break;
            }
        }

        return $os;
    }

    /**
     * Get Version of installed distro
     *
     */
    public function distroVersion()
    {
        $process = new Process('cat /etc/*release');
        $output  = $this->runner->run($process);
        $lines   = explode("\n", trim($output));

        $version = '';
        foreach ($lines as $line) {
            // Ubuntu / Arch / CentOS 7
            if (preg_match("/^VERSION=/", $line) == 1) {
                $version = trim(explode("=", trim($line))[1], '"');
                break;
            }
            // CentOS
            if (strpos($line, "CentOS") !== false) {
                $split = preg_split('/\s+/', $line);

                if (isset($split[2])) {
                    $version = $split[2];
                    break;
                }
            }
        }

        return $version;
    }

    /**
     * Kernel version number eg 3.4.12
     *
     */
    public function kernelVersion()
    {
        $process = new Process('uname -r');
        $output =  $this->runner->run($process);

        return trim(explode('-', $output)[0]);
    }

    /**
     * Get the values from the cpu freq files
     *
     */
    protected function cpuFreq()
    {
        $process = new Process('cat /sys/devices/system/cpu/cpu*/cpufreq/scaling_max_freq');
        return $this->runner->run($process);
    }

    /**
     * Get the CPU clock speed
     *
     */
    public function cpuClock()
    {
        $line = (int)trim(explode("\n", $this->cpuFreq())[0]);
        return ($line/1000);
    }

    /**
     * Count the number of cores on server
     *
     */
    public function cpuCores()
    {
        return count(explode("\n", trim($this->cpuFreq())));
    }

    /**
     * Get the amount of RAM in GB
     *
     */
    public function ram()
    {
        $process = new Process('cat /proc/meminfo');
        $output  = $this->runner->run($process);

        $values = [];
        $lines  = explode("\n", $output);

        array_walk($lines, function(&$value) use (&$values) {
            $split = preg_split('/\s+/', $value);

            if (count($split) == 3) {
                $values[trim($split[0], ':')] = (int)$split[1];
            }
        });

        if (isset($values['MemTotal'])) {
            return ceil($values['MemTotal']/1048576);
        } else {
            return 0;
        }
    }

    /**
     * report file system disk space usage
     *
     */
    public function df()
    {
        $process = new Process('df -BM');
        $output  = $this->runner->run($process);

        $lines  = explode("\n", trim($output));

        // first line is headers
        array_shift($lines);

        $return = [];
        foreach ($lines as $line) {
            $split = preg_split('/\s+/', $line);

            if (count($split) != 6) {
                continue;
            }

            $fs = array_combine([
                'filesystem',
                'size',
                'used',
                'available',
                'use',
                'mount'
            ], $split);

            // clean up
            $fs['size']      = (int)str_replace('M', '', $fs['size']);
            $fs['used']      = (int)str_replace('M', '', $fs['used']);
            $fs['available'] = (int)str_replace('M', '', $fs['available']);
            $fs['use']       = (int)str_replace('%', '', $fs['use']);

            $return[$fs['mount']] = $fs;
        }

        return $return;
    }
}
