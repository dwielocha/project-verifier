<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * File checker task
 * @package project-verifier
 */
class CheckRequiredFilesTask extends Task
{

    /**
     * @var array
     */
    protected $files;

    /**
     * Constructor
     * @param array $files
     */
    public function __construct($files)
    {
        $this->files = $files;
    }

    /**
     * Return label
     * @return string
     */
    public function getLabel()
    {
        return 'Check required files';
    }

    public function run()
    {
        $this->showTaskInfo($this->getLabel(), false);

        $projectPath = $this->verifier->getProjectPath();

        $subId = 1;
        asort($this->files);

        foreach ($this->files as $file) {
            $this->showTaskInfo("/{$file}", true, $subId++);

            $file = $projectPath.'/'.$file;
            if (file_exists($file)) {
                $this->setStatusOk();
            } else {
                $this->setStatusFail();
            }
        }
    }
}