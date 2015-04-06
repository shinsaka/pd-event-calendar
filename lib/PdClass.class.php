<?php
class PdClass
{
    protected $name;
    protected $pd_events;
    protected $pd_events_by_time;

    public function __construct($name)
    {
        $this->name = $name;
        $pd_schedules = array();
        $this->pd_events = array();
        $this->pd_events_by_time = array();
    }

    public function addEvent($name, $time)
    {
        $this->pd_events[] = new PdEvent($name, $time);
    }

    public function getEventsByTime()
    {
        return $this->pd_events_by_time;
    }

    /**
     * 時間ごとにスケジュールをまとめる
     */
    public function mergeEvent()
    {
        $result = array();
        foreach ($this->pd_events as $pd_event) {    /* @var $pd_event PdEvent */
            $time = $pd_event->getTime();
            if (!array_key_exists($time, $result)) {
                $result[$time] = array($pd_event);
            } else {
                $result[$time] = array_merge($result[$time], array($pd_event));
            }
        }
        $this->pd_events_by_time = $result;
    }
}
