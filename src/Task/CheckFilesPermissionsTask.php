<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * File permission checker task
 * @package project-verifier
 */
class CheckFilesPermissionsTask extends Task
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
        return 'Check files/directories permissions';
    }

    public function run()
    {
        $this->showTaskInfo($this->getLabel(), false);

        $projectPath = $this->verifier->getProjectPath();

        $subId = 1;
        asort($this->files);

        foreach ($this->files as $file => $perm) {
            if (strlen($perm) == 3) {
                $perm = '0'.$perm;
            }

            $this->showTaskInfo("{$perm} - /{$file}", true, $subId++);

            $file = $projectPath.'/'.$file;
            $currentPerm = substr(decoct(@fileperms($file)), -4);

            if ($perm == $currentPerm) {
                $this->setStatusOk();

            } else {
                if (@chmod($file, octdec($perm))) {
                    $this->setStatusOk('FIXED');
                } else {
                    $this->setStatusFail();
                }
            }
        }
    }
}