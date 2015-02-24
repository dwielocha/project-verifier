<?php namespace MrGrygson\ProjectVerifier;

/**
 * Task abstract class
 * @package project-verifier
 */
abstract class Task
{
    // status types
    const STATUS_OK = 1;
    const STATUS_FAIL = 2;
    const STATUS_WARN = 3;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var ProjectVerifier
     */
    protected $verifier;

    /**
     * @var array
     */
    protected $stats = [
        'ok' => 0,
        'fail' => 0,
        'warn' => 0,
        'total' => 0
    ];

    abstract public function run();

    abstract public function getLabel();


    /**
     * Starts task
     * @throws \Exception when Project Verifier is not set
     * @return void
     */
    final public function start()
    {
        if (!$this->verifier) {
            die('Project Verifier not set for task '.__CLASS__."\n");
        }

        $this->run();
    }

    /**
     * Sets verifier
     * @param ProjectVerifier $verifier
     * @return self
     */
    final public function setVerifier(ProjectVerifier $verifier)
    {
        $this->id = $verifier->getNewTaskId();
        $this->verifier = $verifier;

        return $this;
    }

    /**
     * Shows task info
     * @param string $label
     * @param boolean $withStatus 
     * @param int|null $subId
     * @return self
     */
    public function showTaskInfo($label, $withStatus = true, $subId = null)
    {
        $taskId = $subId ? $this->id.'.'.$subId.'.' : $this->id.'.';
        $this->verifier->showText($taskId.' '.$label, !$withStatus);

        return $this;
    }

    /**
     * Sets OK status
     * @param string $label
     * @return self
     */
    public function setStatusOk($label = 'OK')
    {
        $this->verifier->showStatus($label, self::STATUS_OK);

        return $this;
    }

    /**
     * Sets FAIL status
     * @param string $label
     * @return self
     */
    public function setStatusFail($label = 'FAIL')
    {
        $this->verifier->showStatus($label, self::STATUS_FAIL);

        return $this;
    }

    /**
     * Sets WARN status
     * @param string $label
     * @return self
     */
    public function setStatusWarn($label)
    {
        $this->verifier->showStatus($label, self::STATUS_WARN);

        return $this;
    }
}