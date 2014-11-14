<?php
chdir('public_html');// измените на свою корневую директорию
include('./manager/includes/protect.inc.php');
include ('./manager/includes/config.inc.php');
include("./manager/includes/document.parser.class.inc.php");
$modx = new DocumentParser;

include_once './assets/modules/modxplaning/inc/config.inc.php';
include_once './assets/modules/modxplaning/classes/modxplaning.class.php';
$cal = new MODxPlaning;
$cal->tbl_events = $tbl_events;
$cal->tbl_calendars = $tbl_calendars;

$ar = $cal->getJsonEvents('*','WHERE event_start <= alarm_start');
$ar = json_decode($ar,true);
$line = $modx->db->select('*',$modx->getFullTableName('system_settings'),"setting_name='emailsender'");
$line = $modx->db->getRow($line);

foreach ($ar as $a){
    $start=strtotime($a['alarm_start']);
    $email = $line['setting_value'];
    $message = '<h2 style="color:'.$a['color'].'">Напонимание о событии "'.$a['title'].$a['event_start'].'".</h2>
    <b>Описание события:</b> <br>'.$a['description'].'
    <hr>
    <b>Событие состоится:</b> '.date("d.m.Y H:i",strtotime($a['start'])).'
    <br><b>Конец события:</b> '.date("d.m.Y H:i",strtotime($a['end'])).'
    ';
    //echo $message;
    if(date("d.m.Y H:i",$start)==date("d.m.Y H:i",time()))
    {
        $to      = $email;
        $subject = 'Напоминание календаря';
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset: utf8\r\n";
        $headers .= 'From: '.$email.'' . "\r\n" .
            'Reply-To: '.$email.'' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }
}
