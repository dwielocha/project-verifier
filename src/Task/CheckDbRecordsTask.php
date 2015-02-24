<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * Database record checker task
 * @package project-verifier
 */
class CheckDbRecordsTask extends Task
{

    /**
     * @var array
     */
    protected $queries;

    /**
     * Constructor
     * @param array $queries
     */
    public function __construct($queries)
    {
        $this->queries = $queries;
    }

    public function getLabel()
    {
        return 'Check DB records';
    }

    public function run()
    {
        $this->showTaskInfo($this->getLabel(), false);
        
        $subId = 1;
        $dbh = $this->verifier->getDbh();

        foreach ($this->queries as $label => $query) {
            if (is_int($label)) {
                $label = $query;
            }

            $this->showTaskInfo($label, true, $subId++);
            $result = $dbh->query($query);

            if ($result) {
                if ($result->num_rows) {
                    $this->setStatusOk();
                } else {
                    $this->setStatusFail('MISSING');
                }

                $result->close();

            } else {
                $this->setStatusFail('BAD QUERY');
            }
        }
    }
}