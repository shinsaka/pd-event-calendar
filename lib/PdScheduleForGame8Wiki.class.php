<?php
require_once dirname(__FILE__) . '/../lib/phpquery/phpQuery-onefile.php';

class PdScheduleForGame8Wiki extends PdSchedule
{
    /**
     * @see PdSchedule::getPdClasses()
     */
    public function getPdClasses()
    {
        // get sites page
        $html = file_get_contents('https://game8.jp/archives/26028');
        $pqObj = phpQuery::newDocument($html);

        // parse target date
        $date_text = $pqObj['h2#hl_1']->text();
        $matches = array();
        if (preg_match('/(?P<month>\d{1,2})\/(?P<day>\d{1,2})/', $date_text, $matches) === 1) {
            $date_str = date('Y') . '-' . $matches['month'] . '-' . $matches['day'];
            $this->setDate($date_str);
        }
        // echo $date_str;

        $table = $pqObj['#article table tr'];
        foreach ($table as $tr) {
           $items = pq($tr)->find('th,td');
           if (count($items) != 6) continue;

//           echo pq(pq($items)->find('th'))->html();
           $col_num = 0;
           foreach ($items as $item) {

               // line header
               if ($col_num == 0) {
                   $title = pq($item)->html();
                   if ($title == '') {
                       break;
                   }
                   $title = pq($item)->find('img')->attr('alt') ?: $title = pq($item)->html();
 //                  echo $title . "\n";
               } else {
                   $event_time_html = pq($item)->html();
                   if ($title != '' && $event_time_html != '') {
                       $event_times = preg_split('/<[^>]*>/', $event_time_html);

                       $pd_class = $this->pd_classes[$this->indexToClassKey($col_num)]; /* @var $pd_class PdClass */
 //                      echo "class " . $this->indexToClassKey($col_num) ."\n";
                       foreach ($event_times as $event_time) {
                           $pd_class->addEvent($this->titleNameConvert($title), sprintf('%s %s', $this->getDate(), $event_time));
//                           echo "time:" . sprintf('%s %s', $this->getDate(), $event_time) . "\n";
                       }
                   }
               }
               $col_num++;
           }

//           echo "===============\n";
        }

        foreach ($this->pd_classes as $pd_class) {
            $pd_class->mergeEvent();
        }

        return $this->pd_classes;
    }

    private function indexToClassKey($index)
    {
        return substr('ABCDE', ($index - 1) % 5, 1);
    }

    private function titleNameConvert($title)
    {
        $title_conv_patterns = array(
            '火デーモン' => '星宝の魔窟',
        );

        return array_key_exists($title, $title_conv_patterns) ? $title_conv_patterns[$title] : $title;
    }
}
