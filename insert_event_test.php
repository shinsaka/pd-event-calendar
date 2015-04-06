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

$subject = 'イベント登録テスト';
$start_timestamp = strtotime('2015-04-09 07:00');
$end_timestamp = strtotime('2015-04-09 08:00');

$event = new Google_Service_Calendar_Event();
$event->setSummary($subject);

$start_datetime = new Google_Service_Calendar_EventDateTime();
$start_datetime->setDateTime(date('c', $start_timestamp));
$event->setStart($start_datetime);

$end_datetime = new Google_Service_Calendar_EventDateTime();
$end_datetime->setDateTime(date('c', $end_timestamp));
$event->setEnd($end_datetime);

$reminder = new Google_Service_Calendar_EventReminder();
$reminder->setMethod('popup');
$reminder->setMinutes(13);

$reminders = new Google_Service_Calendar_EventReminders();
$reminders->setUseDefault(false);
$reminders->setOverrides(array($reminder));
$event->setReminders($reminders);

echo "invent insert" . ":" . date('c', $start_timestamp) . ":" . $subject . "\n";
$pd_calendar->getServiceCalendar()->events->insert($pd_calendar->getPdCalendarId('C'), $event);

