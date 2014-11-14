<?php
require($_SERVER['DOCUMENT_ROOT'].'/manager/includes/protect.inc.php');
include ($_SERVER['DOCUMENT_ROOT'].'/manager/includes/config.inc.php');
include(MODX_BASE_PATH."manager/includes/document.parser.class.inc.php");
$modx = new DocumentParser;
function normJsonStr($str){
    $str = preg_replace_callback('/\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
    return iconv('cp1251', 'utf-8', $str);
}
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    include_once MODX_BASE_PATH.'assets/modules/modxplaning/inc/config.inc.php';
    include_once $mp_location.'classes/modxplaning.class.php';
    $cal = new MODxPlaning;
    $cal->tbl_events = $tbl_events;
    $cal->tbl_calendars = $tbl_calendars;
    switch ($_POST['op'])
    {
        case 'source' :
            if ((int)$_POST['cat_id']>0)
            {
                    $and = " AND cat_id='".$_POST['cat_id']."'";
            }
            elseif((int)$_POST['cat_id']==0){
                $and = "";
            }
            echo $cal->getJsonEvents('*',"cal_id='" . $_POST['cal_id'] . "'".$and);
            break;
        case 'add' :
            $err = array();
            $err_ar = array(
                'title'=>$_POST['title'],
                'description'=>$_POST['description'],
                'event_start'=>$_POST['event_start'],
                'event_end'=>$_POST['event_end']
            );
            if (in_array('',$err_ar))
            {
                if (empty($_POST['title'])) { $err['no_title']='Не указан заголовок';}
                if (empty($_POST['description'])) { $err['no_description']='Не указано описание';}
                if (empty($_POST['event_start'])) { $err['no_event_start']='Не указана дата начала события';}
                if (empty($_POST['event_end'])) { $err['no_event_end']='Не указана дата окончания события';}
                $er_out.='<b>В процессе выполнения обнаружены следующие ошибки:</b><ul class="errors_event">';
                foreach ($err as $li) {
                    $er_out.="<li>".$li."</li>";
                }
                $er_out.='</ul>';
                echo $er_out;
                die();
            }
            else{
                $err['error']=0;
                $data = array();
                $data['id']='NULL';
                $data['cal_id']=(int)$_POST['cal_id'];
                $data['cat_id']=(int)$_POST['cat_id'];
                $data['created']=time();
                $data['event_start']=strtotime($_POST['event_start']);
                $data['event_end']=strtotime($_POST['event_end']);
                $data['alarm_start']='';
                $data['title']=$_POST['title'];
                $data['description']=$_POST['description'];
                $data['color']=$_POST['color'];
                $data['notify']=(int)$_POST['notify'];
                $data['alarm_start']=strtotime($_POST['alarm_start']);
                $data['sticky']=(int)$_POST['sticky'];
                $data['status']=(int)$_POST['status'];
                $cal->insertEvent($data,$cal->tbl_events);
            }
            break;
        case 'edit' :
            $data = array();
            $data['id']=$_POST['event_id'];
            $data['cal_id']=$_POST['cal_id'];
            $data['cat_id']=(int)$_POST['cat_id'];
            $data['event_start']=strtotime($_POST['event_start']);
            $data['event_end']=strtotime($_POST['event_end']);
            $data['alarm_start']='';
            $data['title']=$_POST['title'];
            $data['description']=$_POST['description'];
            $data['color']=$_POST['color'];
            $data['notify']=(int)$_POST['notify'];
            $data['alarm_start']=strtotime($_POST['alarm_start']);
            $data['sticky']=(int)$_POST['sticky'];
            $data['status']=(int)$_POST['status'];
            $cal->updateEvent($data,$data['id']);
            break;
        case 'delta' :
            $data = array();
            $data['id']=$_POST['event_id'];
            $data['event_start']=strtotime($_POST['event_start']);
            $data['event_end']=strtotime($_POST['event_end']);
            $cal->updateEvent($data,$data['id']);
            break;
        case 'delete' :
            $cal->deleteEvent($_POST['event_id']);
            break;
        case 'add_calendar' :
            $data=array();
            $data['title'] = $_POST['cal_title'];
            $data['created']=time();
            $data['description'] = $_POST['cal_description'];
            if ($_POST['cal_def']==1) {
                $data['def'] = 1;
                $modx->db->query("UPDATE ".$cal->tbl_calendars." SET def='0' WHERE def='1'");
            }
            else{
                $data['def'] = 0;
            }

            if (in_array('',$data) or in_array(' ',$data))
            {
                echo 'Поле не может быть пустым!';
            }
            else{
                if ($modx->db->insert($data,$cal->tbl_calendars));
                {
                    echo '<script>document.location.reload()</script>';
                }
            }
            break;
        case 'delete_calendar' :
            $cal->deleteCalendar((int)$_POST['calendar_id']);
            break;
        case 'edit_calendar' :
            $data=array();
            $data['id'] = $_POST['calendar_id'];
            $data['title'] = $_POST['cal_title'];
            $data['description'] = $_POST['cal_description'];
            if ($_POST['cal_def']==1) {
                $data['def'] = 1;
                $modx->db->query("UPDATE ".$cal->tbl_calendars." SET def='0' WHERE def='1'");
            }
            else{
                $data['def'] = 0;
            }
            if (in_array('',$data) or in_array(' ',$data))
            {
                echo 'Поле не может быть пустым!';
            }
            else{
                if ($cal->editCalendar($data,$cal->tbl_calendars,$data['id']))
                {
                    echo '<script>document.location.reload()</script>';
                }
            }

            break;
        case 'change_parent' :
                $data=array();
                $data['id']=$_POST['event_id'];
                $data['cal_id']=$_POST['ch_cal'];
               $cal->updateEvent($data,$data['id']);
    }
}