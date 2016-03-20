<?php
require_once dirname(__FILE__) . '/../lib/phpquery/phpQuery-onefile.php';

class PdScheduleForYahooGames extends PdSchedule
{
    /**
     * @see PdSchedule::getPdClasses()
     */
    public function getPdClasses()
    {
        // get sites page
        $html = file_get_contents('http://gamelog.games.yahoo.co.jp/timetable/pad/');
        $pqObj = phpQuery::newDocument($html);

        // parse target date
        $date_text = $pqObj['h1']->text();
        $matches = array();
        if (preg_match('/(?P<month>\d{1,2})\/(?P<day>\d{1,2})/', $date_text, $matches) === 1) {
            $date_str = date('Y') . '-' . $matches['month'] . '-' . $matches['day'];
            $this->setDate($date_str);
        }
        echo $date_str;

        $table_json = $pqObj['script#timetable_data']->text();
        $table = json_decode($table_json);

        if (!is_null($table)) {
            // group a-e
            foreach ($table->group->a as $item) {
                $this->pd_classes['A']->addEvent($item->name, sprintf('%s %s', $this->getDate(), $item->start));
            }
            foreach ($table->group->b as $item) {
                $this->pd_classes['B']->addEvent($item->name, sprintf('%s %s', $this->getDate(), $item->start));
            }
            foreach ($table->group->c as $item) {
                $this->pd_classes['C']->addEvent($item->name, sprintf('%s %s', $this->getDate(), $item->start));
            }
            foreach ($table->group->d as $item) {
                $this->pd_classes['D']->addEvent($item->name, sprintf('%s %s', $this->getDate(), $item->start));
            }
            foreach ($table->group->e as $item) {
                $this->pd_classes['E']->addEvent($item->name, sprintf('%s %s', $this->getDate(), $item->start));
            }

            foreach ($this->pd_classes as $pd_class) {
                $pd_class->mergeEvent();
            }
        }

        return $this->pd_classes;
    }
}
