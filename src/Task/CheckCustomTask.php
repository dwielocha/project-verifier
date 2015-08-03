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
     * @var array 
     */ 
    protected $parameters;

    /**
     * Constructor
     * 
     * @param string $label
     * @param callable $callable
     * @param array $params
     */
    public function __construct($label, callable $callable, array $params = [])
    {
        $this->label = $label;
        $this->callable = $callable;
        $this->parameters = $params;
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
        $params = $this->parameters;

        if (call_user_func_array($callable, $params)) {
            $this->setStatusOk();
        } else {
            $this->setStatusFail();
        }
    }
}
