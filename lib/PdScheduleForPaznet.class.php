<?php
class PdScheduleForPaznet extends PdSchedule
{
    /**
     * @see PdSchedule::getPdClasses()
     */
    public function getPdClasses()
    {
        $event_patterns = array(
            'chogold' => '超ゴルドラ',
            'chometa' => '超メタドラ',
            'chorubi' => '超ルビドラ',
            'chosafa' => '超サファドラ',
            'choeme' => '超エメドラ',
            'gold' => 'ゴルドラ',
            'meta' => 'メタドラ',
            'eme' => 'エメドラ',
            'safa' => 'サファドラ',
            'rubi' => 'ルビドラ',
            'pendora' => 'ペンドラの里',
            'dorapura' => 'ドラプラ',
            'king-carnival' => 'キングカーニバル',
            'metagoru' => 'メタゴル',
            'shinka-rush' => '進化ラッシュ',
            'iseki' => '星宝の遺跡',
            'czcolm' => '超絶メタドラ',
            'hikyou' => 'たまドラの秘境',
            'konjiki' => '金色の築山',
            'rarechara' => 'レアキャラ',
            'ckcar' => '超キングカーニバル',
        );

        $result = array(
            0 => new PdClass('A'),
            1 => new PdClass('B'),
            2 => new PdClass('C'),
            3 => new PdClass('D'),
            4 => new PdClass('E'),
        );

        // 公開サイト読み込み
        $dom = file_get_html('http://paznet.net/schedules/blog_parts/270');

        // get date
        $nodes = $dom->find("table.schedule-table caption");
        if (array($nodes) && count($nodes) > 0) {
            $date_text = reset($nodes)->text();
            $matches = array();
            if (preg_match('/(?P<month>\d{1,2})[^\d]+?(?P<day>\d{1,2})/', $date_text, $matches) === 1) {
                $date_str = date('Y') . '-' . $matches['month'] . '-' . $matches['day'];
                $this->setDate($date_str);
            }
        }

        // イベント種類すべて検索・走査する
        foreach ($event_patterns as $key => $pattern) {
            $nodes = $dom->find("table.schedule-table td." . $key);
            // イベントなしor5個(AtoE)の倍数でなければ無視
            if (count($nodes) == 0 || count($nodes) % 5 != 0) {
                continue;
            }

            foreach ($nodes as $index => $item) { /* @var $item simple_html_dom_node */
                $pd_class = $this->pd_classes[$this->indexToClassKey($index)]; /* @var $pd_class PdClass */
                $event_time = sprintf('%s %s:00', $this->getDate(), $item->text());
                $pd_class->addEvent($pattern, $event_time);
            }
        }
        foreach ($this->pd_classes as $pd_class) {
            $pd_class->mergeEvent();
        }

        return $this->pd_classes;
    }

    private function indexToClassKey($index)
    {
        return substr('ABCDE', $index % 5, 1);
    }
}
