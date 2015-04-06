<?php
require_once dirname(__FILE__) . '/simple_html_dom.php';

abstract class PdSchedule
{
    protected $pd_classes;
    protected $date_timestamp;

    public function __construct()
    {
        $this->pd_classes = array(
            'A' => new PdClass('A'),
            'B' => new PdClass('B'),
            'C' => new PdClass('C'),
            'D' => new PdClass('D'),
            'E' => new PdClass('E'),
        );

        $this->setDate();
    }

    /**
     * return pdclass array
     *
     * @return array(PdClass,...)
     */
    public function getPdClasses()
    {
        return $this->pd_classes;
    }

    /**
     * @param unknown_type $value
     */
    protected function setDate($value = null)
    {
        if (is_int($value)) {
            $this->date_timestamp = $value;
        } else if (is_null($value)) {
            $this->date_timestamp = strtotime(date('Y-m-d'));
        } else {
            $this->date_timestamp = strtotime($value);
        }
    }

    public function getDate($format = 'Y-m-d')
    {
        return is_null($format) ? $this->date_timestamp : date($format, $this->date_timestamp);
    }
}
