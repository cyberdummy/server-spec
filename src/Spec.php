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
     * Get the OS
     *
     */
    public function os()
    {
        $process = new Process('cat /etc/*release');
        $output  = $this->runner->run($process);
        $lines   = explode("\n", trim($output));

        $os = '';
        foreach ($lines as $line) {
            // CentOS
            if (strpos($line, "CentOS") !== false) {
                $os = trim($line);
                break;
            }
            // Ubuntu / Arch
            if (strpos($line, "PRETTY_NAME=") !== false) {
                $os = trim(explode("=", trim($line))[1], '"');
                break;
            }
        }

        return $os;
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
        $output =  $this->runner->run($process);

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
}
