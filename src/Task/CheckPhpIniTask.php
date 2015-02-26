<?php namespace MrGrygson\ProjectVerifier\Task;

use MrGrygson\ProjectVerifier\Task;

/**
 * PHP extension checker task
 * @package project-verifier
 */
class CheckPhpIniTask extends Task
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    private $validComparators = [
        '>', '>=', '<', '<=', '='
    ];

    /**
     * Constructor
     * @param array $settings
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Returns label
     * @return string
     */
    public function getLabel()
    {
        return 'Check PHP ini settings';
    }

    /**
     * Runs task
     */
    public function run()
    {
        $this->showTaskInfo($this->getLabel(), false);
        $this->validateSettings();
        $this->sortSettings();
        $subId = 1;

        foreach ($this->settings as $option) {
            $currentValue = ini_get($option['option']);

            $label = "Is {$option['option']} ({$currentValue}) {$option['comparator']} {$option['value']} ?";
            $this->showTaskInfo($label, true, $subId++);

            $value1 = $currentValue;
            $value2 = $option['value'];

            if ($option['to_bytes']) {
                $value1 = $this->convertToBytes($value1);
                $value2 = $this->convertToBytes($value2);
            }

            if ($this->compareValues($value1, $value2, $option['comparator'])) {
                $this->setStatusOk();
            } else {
                $this->setStatusFail();
            }
        }
    }

    /**
     * Checks settings
     */
    protected function validateSettings()
    {
        foreach ($this->settings as $key => $option) {
            if (!isset($option['option'], $option['value'])) {
                die("Invalid settings format! Please use e.g. ['option' => 'timezone', 'value' => 'UTC', 'comparator' => '='] \n");
            }

            if (!isset($option['comparator'])) {
                $this->settings[$key]['comparator'] = '=';
            }

            if (!in_array($option['comparator'], $this->validComparators)) {
                die("Invalid comparator for option `{$option['option']}` !");
            }

            if (!isset($option['to_bytes'])) {
                $this->settings[$key]['to_bytes'] = false;
            }
        }
    }

    /**
     * Sorts settings by name (option key)
     */
    protected function sortSettings()
    {
        $sortArray = array(); 

        foreach($this->settings as $option){ 
            foreach($option as $key => $value){ 
                if(!isset($sortArray[$key])){ 
                    $sortArray[$key] = array(); 
                } 
                $sortArray[$key][] = $value; 
            } 
        } 
 
        array_multisort($sortArray['option'],SORT_DESC, $this->settings);
    }

    /**
     * Convert value to bytes
     * @param string $value
     * @return integer
     */
    protected function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);

        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * Compare 2 values with comparator
     * @param string $value1
     * @param string $value2
     * @param string $comparator
     * @return boolean
     */
    protected function compareValues($value1, $value2, $comparator)
    {
        switch ($comparator) {
            case '>':
                $result = $value1 > $value2;
                break;

            case '>=':
                $result = $value1 >= $value2;
                break;

            case '<':
                $result = $value1 < $value2;
                break;

            case '<=':
                $result = $value1 <= $value2;
                break;

            case '=':
            default:
                $result = $value1 == $value2;
        }

        return $result;
    }
}