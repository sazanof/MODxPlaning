<?php
/**
 * snippet MODxPlaning.
 * Author: sazanof
 * Date: 27.10.2014
 * Email: sazanof@gmail.com
 *
 * PARAMS
 * &cal_id - The calendar ID from DBASE
 */
if(!defined('MODX_BASE_PATH')){die('What are you doing? Get out of here!');}
include_once MODX_BASE_PATH.'assets/modules/modxplaning/inc/config.inc.php';
include_once $mp_location.'classes/modxplaning.class.php';
$cal = new MODxPlaning;
$cal->tbl_calendars = $tbl_calendars;
$cal->tbl_categories = $tbl_categories;

$defCal = $cal->getDefaultCalendar();
$out = '';
$jQ = isset($jQ) ? $jQ : 'n'; // default NO jQuery
$calID = isset($calID) ? (int)$calID : $defCal['id']; // if empty echo def cal ID
$isClick = isset($isClick) ? (int)$isClick : 0; // use if jquery ui dialog included
$dayClick = isset($dayClick) ? (int)$dayClick : 0; // use if jquery ui dialog included
$calHeight = isset($calHeight) ? (int)$calHeight : "'auto'"; // calendar Height
$container = isset($container) ? $container : "#calendar"; // <div id="calendar">
$result = isset($result) ? $result : "#result"; // <div id="result">
$lang = isset($lang) ? $lang : 'ru';
$successMsg = isset($successMsg) ? $modx->getChunk($successMsg) : '';
if ($jQ=='y'){$modx->regClientStartupScript($mp_url.'js/jquery-1.11.1.min.js');}
elseif ($jQ=='n'){$out.='';}
else{die();}
$js.='<script src="'.$mp_url.'fullcalendar/lib/moment.min.js"></script>
<script src="'.$mp_url.'fullcalendar/fullcalendar.js"></script>
<script src="'.$mp_url.'fullcalendar/lang-all.js"></script>';
$js .= "<script>
	$(document).ready(function() {
	    var result = $('".$result."');
		$('".$container."').fullCalendar({
		firstDay: 1,
		axisFormat:'HH:mm',
		eventLimit: true,
		height: ".$calHeight.",
		header: {
        left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			lang: '".$lang."',";
            if ($isClick==1)
            {
                $js.="
                    eventClick: function(calEvent, jsEvent, view){
                        $('#event_modal').dialog({
                            modal:true,
                            title:calEvent.title,
                              open: function( event, ui ) {
                                $('#event_modal').html('<div class=event_in_modal><span><b>Время проведения</b>' + moment(calEvent.start).format('DD.MM.YYYY HH:mm') + ' - '+ moment(calEvent.end).format('DD.MM.YYYY HH:mm') + '</span></div>');
                              }
                        });
                    },
                ";
            }
        if ($dayClick==1)
            {
                $js.="
                    dayClick: function(date, allDay, jsEvent, view,start) {
                        var newDate = moment(date).format('DD.MM.YYYY 08:mm');
                        $('#s').val(newDate);
                        $('#po').val(newDate);
                        $('#dialog-form').dialog({title:'Заявка на фотосъемку',width:500,modal:true});
                    },
                ";
            }
			$js.="
			/* источник записей */
            eventSources: [{
                url: '/assets/modules/modxplaning/inc/ajax.inc.php',
                type: 'POST',
                data: {
                    cal_id:".$calID.",
                    op: 'source'
                },
                success: function() {
                    result.html('".$successMsg."');
                },

                error: function() {
                    result.html('Пусто!');
                }
            }]
		});
	});
</script>";
$modx->regClientCSS('<link href="'.$mp_url.'fullcalendar/fullcalendar.css" rel="stylesheet" />');
$modx->regClientStartupScript($js);
$container = str_replace ("#",'',$container);
$result = str_replace ("#",'',$result);
$out.='<div id="event_modal"></div>
<div id="'.$result.'"></div>
<div id="'.$container.'"></div>';
echo $out;