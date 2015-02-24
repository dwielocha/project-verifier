<?php namespace MrGrygson\ProjectVerifier;

/**
 * Project Verifier class
 * @package project-verifier
 */
class ProjectVerifier {

    const VERSION = 0.2;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var string
     */
    protected $env;

    /**
     * @var string
     */
    protected $projectPath;

    /**
     * @var array
     */
    protected $options = [
        'CONSOLE_WIDTH' => 100,
        'CONSOLE_LABEL_WIDTH' => 74,
    ];

    /**
     * @var \mysqli
     */
    protected $dbh;

    /**
     * @var array
     */
    protected $stats = [
        'ok' => 0,
        'fail' => 0,
        'warn' => 0,
        'total' => 0
    ];

    /**
     * @var array
     */
    protected $tasks;

    /**
     * @var integer
     */
    protected $lastTaskId;

    /**
     * Constructor
     * @param string $path Project path
     * @param string $env Environment/domain name
     * @param array $options array of options
     */
    public function __construct($path, $env, $options = [])
    {
        $this->projectPath = $path;
        $this->env = $env;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Runs project verifier
     */
    public function run()
    {   
        $this->showStartInfo();
        $this->runTasks();
        $this->showStats();
    }

    /**
     * Adds new task to do
     * @param Task $task
     * @return self
     */
    public function addTask(Task $task)
    {
        $task->setVerifier($this);
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Sets database configuration
     * $config = [
     *      'host'      => 'localhost',
     *      'database'  => 'dbname',
     *      'username'  => 'user1',
     *      'password'  => 'password1',
     *      'charset'   => 'utf8',
     * ]
     * @param array $config 
     * @return self
     */
    public function setDbConfiguration($config)
    {
        if (!isset($config['host'], $config['username'], $config['password'], $config['database'], $config['charset'])) {
            die('Invalid database config given!'."\n");
        }

        $this->dbh = new \mysqli($config['host'], $config['username'], $config['password'], $config['database']);

        if (mysqli_connect_errno()) {
            die('Connection to database failed! '.mysqli_connect_error()."\n");
        }

        if (isset($config['charset'])) {
            if (!$this->dbh->set_charset($config['charset'])) {
                die("Error loading charset `{$config['charset']}` !".$this->dbh->error);
            }
        }

        return $this;
    }

    /**
     * Returns DB handler
     * @return \mysqli
     */
    public function getDbh()
    {
        return $this->dbh;
    }

    /**
     * Returns new task ID
     * @return interger
     */
    public function getNewTaskId()
    {
        $this->lastTaskId += 1;

        return $this->lastTaskId;
    }

    /**
     * Returns project path
     * @return string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * Displays start info
     * @return void
     */
    protected function showStartInfo()
    {
        echo "\n";
        echo str_pad('', $this->options['CONSOLE_WIDTH'], '='), "\n";
        echo 'Project Verifier v'.self::VERSION.' - starting at ', date('Y-m-d H:i:s'), "\n";
        echo "Project path: ".$this->projectPath."\n";
        echo 'Use environment: '.$this->env."\n";
        echo str_pad('', $this->options['CONSOLE_WIDTH'], '='), "\n";
    }

    protected function showSeparatingLine()
    {
        echo str_pad('', $this->options['CONSOLE_WIDTH'], '-'), "\n";
    }

    /**
     * Displays statistics
     * @return void
     */
    protected function showStats()
    {
        $stats = $this->stats;

        echo str_pad('', $this->options['CONSOLE_WIDTH'], '='), "\n";
        echo "OK: {$stats['ok']}/{$stats['total']}   FAILED: {$stats['fail']}/{$stats['total']}   WARNINGS: {$stats['warn']}/{$stats['total']} ", "\n";
        echo str_pad('', $this->options['CONSOLE_WIDTH'], '='), "\n\n";
    }

    /**
     * Displays given text
     * @param string $text 
     * @param boolean $closeLine 
     * @return self
     */
    public function showText($text, $closeLine = false)
    {
        if ($closeLine) {
            echo str_pad($text, $this->options['CONSOLE_WIDTH']), "\n";
            
        } else {
            echo str_pad($text, $this->options['CONSOLE_LABEL_WIDTH']);
        }

        return $this;
    }

    /**
     * Sets status 
     * @param string $label
     * @param int $statusType
     * @return self
     */
    public function showStatus($label, $statusType = null)
    {
        $color = '';

        switch ($statusType) {
            case Task::STATUS_OK:
                $color = "\033[1;32m";
                $this->stats['ok']++;
                $this->stats['total']++;
                break;

            case Task::STATUS_FAIL: 
                $color = "\033[1;31m";
                $this->stats['fail']++;
                $this->stats['total']++;
                break;

            case Task::STATUS_WARN:
                $color = "\033[1;33m";
                $this->stats['warn']++;
                $this->stats['total']++;
                break;
        }

        // @TODO pass to ProjectVerifier class
        $len = $this->options['CONSOLE_WIDTH'] -  $this->options['CONSOLE_LABEL_WIDTH'] - strlen($label) - 3;
        echo str_pad('', $len, ' ', STR_PAD_LEFT), '[', $color, $label, "\033[0m", ']', "\n";

        return $this;
    }
    /**
     * Runs tasks
     * @return void
     */
    protected function runTasks()
    {
        $cnt = (count($this->tasks) -1);
        foreach ($this->tasks as $key => $task) {
            $task->start();

            if ($key < $cnt) {
                $this->showSeparatingLine();
            }
        }
    }
}
