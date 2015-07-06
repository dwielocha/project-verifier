<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * Installed commands/software checker task
 * @package project-verifier
 */
class CheckInstalledCommandsTask extends Task
{
    /**
     * @var array
     */
    protected $commands;

    /**
     * Constructor
     * @param array $commands
     */
    public function __construct($commands)
    {
        $this->commands = $commands;
    }

    public function getLabel()
    {
        return 'Check installed commands/software';
    }

    public function run()
    {
        $this->showTaskInfo($this->getLabel(), false);
        if (!function_exists('exec')) {
            die('PHP `exec` function is not available!');
        }

        $subId = 1;
        natcasesort($this->commands);

        foreach ($this->commands as $command) {
            $this->showTaskInfo("$command", true, $subId++);
            $output = [];

            exec("command -v {$command} > /dev/null 2>&1 && echo 'Found' || echo 'Not Found'", $output);

            if (isset($output[0]) && $output[0] == 'Found') {
                $this->setStatusOk();
            } else {
                $this->setStatusFail('MISSING');
            }
        }
    }
}
