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

$pd_calendar->deleteEvents();
