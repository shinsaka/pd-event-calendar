<?php
require_once dirname(__FILE__) . '/lib/autoloader.php';

$pd_schedule = new PdScheduleForPaznet();
$pd_classes = $pd_schedule->getPdClasses();

// A組 to E組
foreach ($pd_classes as $class_letter => $pd_class) {
    echo $class_letter . " class ------------------------------------------\n";

    $event_times = $pd_class->getEventsByTime();
    // 当日のイベント（時間ごと）
    foreach ($event_times as $start_time => $events) {
        $subjects = array();
        // 時間に発生するイベント
        foreach ($events as $event) {  /* @var $event PdEvent */
            $subjects[] = $event->getName();
        }
        $subject = implode('/', $subjects);
        $start_timestamp = strtotime($start_time);

        echo $start_time . ":" . $subject . "\n";
    }
}
