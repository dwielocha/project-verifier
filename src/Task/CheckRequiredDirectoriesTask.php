<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * Directory checker task
 * @package project-verifier
 */
class CheckRequiredDirectoriesTask extends Task
{

    /**
     * @var array
     */
    protected $directories;

    /**
     * Constructor
     * @param array $directories
     */
    public function __construct($directories)
    {
        $this->directories = $directories;
    }

    /**
     * Return label
     * @return string
     */
    public function getLabel()
    {
        return 'Check required directories';
    }

    public function run()
    {
        $this->showTaskInfo($this->getLabel(), false);

        $projectPath = $this->verifier->getProjectPath();

        $subId = 1;
        asort($this->directories);

        foreach ($this->directories as $directory) {
            $this->showTaskInfo("/{$directory}", true, $subId++);

            $directory = $projectPath.'/'.$directory;
            if (file_exists($directory)) {
                $this->setStatusOk();
            } else {
                if (@mkdir($directory, 0777, true)) {
                    $this->setStatusOk('CREATED');
                } else {
                    $this->setStatusFail();
                }
            }
        }

        clearstatcache();
    }
}
