<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * PHP extension checker task
 * @package project-verifier
 */
class CheckPhpExtensionsTask extends Task
{
    /**
     * @var array
     */
    protected $extensions;

    /**
     * Constructor
     * @param array $extensions
     */
    public function __construct($extensions)
    {
        $this->extensions = $extensions;
    }

    public function getLabel()
    {
        return 'Check PHP extensions';
    }

    public function run()
    {
        $this->showTaskInfo($this->getLabel(), false);

        $subId = 1;
        natcasesort($this->extensions);

        foreach ($this->extensions as $ext) {
            $this->showTaskInfo("$ext", true, $subId++);

            if (extension_loaded($ext)) {
                $this->setStatusOk();
            } else {
                $this->setStatusFail('MISSING');
            }
        }
    }
}