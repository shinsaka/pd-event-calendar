<?php
require_once dirname(__FILE__) . '/lib/autoloader.php';

// config parse
$pd_config = new PdConfig();
if ($pd_config->hasErrors()) {
    echo $pd_config->getErrorMessageText();
    exit(1);
}

$pd_calendar = new PdCalendar();
$pd_calendar->setAuthEmail($pd_config->getGcalAuthEmail());
$pd_calendar->setP12Key(file_get_contents($pd_config->getGcalP12KeyfilePath()));

//$pd_schedule = new PdScheduleForPaznet();
//$pd_schedule = new PdScheduleForGame8Wiki();
$pd_schedule = new PdScheduleForYahooGames();
$pd_classes = $pd_schedule->getPdClasses();

// 該当日の予定を削除
$pd_calendar->deleteEvents($pd_schedule->getDate(null));


// A組 to E組
foreach ($pd_classes as $class_letter => $pd_class) {
    $event_times = $pd_class->getEventsByTime();
    // 当日のイベント（時間ごと）
    foreach ($event_times as $start_time => $events) {
        $subjects = array();
        // 時間に発生するイベント
        foreach ($events as $event) {  /* @var $event PdEvent */
            $subjects[] = $event->getName();
        }
        $subject = $class_letter . "組:" . implode('/', $subjects);
        $start_timestamp = strtotime($start_time);
        $pd_calendar->addEvent($class_letter, $subject, $start_timestamp, $start_timestamp + 60 * 60);
    }
}

