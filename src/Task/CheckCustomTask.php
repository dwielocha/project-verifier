<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * Custom task checker
 * @package project-verifier
 */
class CheckCustomTask extends Task
{
    /**
     * @var string
     */
    protected $label;

    protected $callable;

    /**
     * Constructor
     * @param string $label
     * @param callable $callable
     */
    public function __construct($label, callable $callable)
    {
        $this->label = $label;
        $this->callable = $callable;
    }

    /**
     * Returns label
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Runs task
     */
    public function run()
    {
        $this->showTaskInfo($this->getLabel());
        $callable = $this->callable;

        if ($callable()) {
            $this->setStatusOk();
        } else {
            $this->setStatusFail();
        }
    }
}