<?php
require_once 'google-api-php-client/src/Google/autoload.php';

class PdCalendar
{
    protected $auth_email;
    protected $p12_key;
    protected $pd_calendar_ids;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->initPdCalendarIds();
    }

    /**
     * set auth mail
     * @param string $value
     */
    public function setAuthEmail($value)
    {
        $this->auth_email = $value;
    }

    /**
     * set p12key
     * @param string $value
     */
    public function setP12Key($value)
    {
        $this->p12_key = $value;
    }

    /**
     * auth and get service instance
     */
    public function getServiceCalendar()
    {
        $scopes = array('https://www.googleapis.com/auth/calendar');
        $credential = new Google_Auth_AssertionCredentials($this->auth_email, $scopes, $this->p12_key);

        $client = new Google_Client();
        $client->setAssertionCredentials($credential);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($credential);
        }

        return new Google_Service_Calendar($client);
    }

    /**
     * initialize google calendar ids
     */
    protected function initPdCalendarIds()
    {
        $this->pd_calendar_ids = array(
            'A' => '2pig0qflik15708oe983ma08v4@group.calendar.google.com',
            'B' => 'm6o3rgmm58rq9461qcb27sbtl8@group.calendar.google.com',
            'C' => 'q1435u36geer0aq4nlhsdo1pog@group.calendar.google.com',
            'D' => 'tsant1g9vi7ra9tfmlgmhr8l1c@group.calendar.google.com',
            'E' => 'q840245lkh41jdtha1au1ml6no@group.calendar.google.com',
        );
    }

    /**
     * calendar id by pd class
     *
     * @param string $class class string(A,B,C,D,E)
     * @return string
     */
    public function getPdCalendarId($class)
    {
        return (array_key_exists($class, $this->pd_calendar_ids)) ? $this->pd_calendar_ids[$class] : '';
    }


    /**
     * add event
     * @param string $class          class string(A,B,C,D,E)
     * @param string $subject        event title
     * @param int    $start_timestamp event start timestamp value
     * @param int    $end_timestamp  event end timestamp value
     */
    public function addEvent($class, $subject, $start_timestamp, $end_timestamp)
    {
        $pd_calendar_id = $this->getPdCalendarId($class);

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
        $reminder->setMinutes(10);
        $reminders = new Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);
        $reminders->setOverrides(array($reminder));
        $event->setReminders($reminders);

        echo "invent insert" . $class . ":" . date('c', $start_timestamp) . ":" . $subject . "\n";
        $this->getServiceCalendar()->events->insert($pd_calendar_id, $event);
    }

    /**
     *
     */
    public function deleteEvents($target_date_timestamp = null)
    {
        // 省略時は当日
        if (is_null($target_date_timestamp)) {
            $target_date_timestamp = strtotime(date('Y-m-d'));
        }

        echo 'deleteEvents start for:' . date('Y-m-d' , $target_date_timestamp) . "\n";

        $service_calendar = $this->getServiceCalendar();

        $cal_list = $service_calendar->calendarList->listCalendarList();
        foreach ($cal_list['items'] as $calendar) { /* @var $calendar Google_Service_Calendar_CalendarListEntry */
            $calendar_id = $calendar->getId();
            $calendar_name = $calendar->getSummary();
            echo $calendar_name."\t" . $calendar_id . "\n";

            $opt = array(
                'timeMin' => date('c', $target_date_timestamp),
                'timeMax' => date('c', $target_date_timestamp + 86400),
            );
            $events = $service_calendar->events->listEvents($calendar_id, $opt);
            foreach ($events as $event) { /* @var $event Google_Service_Calendar_Event */
                echo $event->getStart()->getDateTime() . ":" . $event->getSummary() . "\n";
                $service_calendar->events->delete($calendar_id, $event->getId());
            }
        }
    }
}
