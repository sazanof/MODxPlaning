<?php
include_once MODX_BASE_PATH.'assets/modules/modxplaning/inc/config.inc.php';
include_once $mp_location.'classes/modxplaning.class.php';
$cal = new MODxPlaning;
$out = '';
$titl='Управление календарями';
$btn_add_event='';

$cal->tbl_calendars = $tbl_calendars;
$cal->tbl_categories = $tbl_categories;
if (!$lng) {
    $lng = 'ru';
}
include $mp_location.'lang/'.$lng.'.php';
$defaultCalendar = $cal->getDefaultCalendar();
if (!$_GET['op']) {
    if (count($cal->getCalendars())>0){

        if (!$defaultCalendar['id'])
		{
			//header ('Location:'.$mp_header.'&cal_id='.$defaultCalendar['id']);
		}
		else
		{
			if ($_GET['cal_id']==''){
				header ('Location:'.$mp_header.'&cal_id='.$defaultCalendar['id']);
			}
			else{
				foreach ($cal->colors() as $key=>$val){
					$options.=' <option value="'.$val.'">'.$key.'</option>';
				}
				$cals = $cal->getCalendars();
				foreach ($cals as $citem)
				{
					if ($_GET['cal_id']==$citem['id']) {
						$activeCal ='active';
					}
					else {
						$activeCal='';
					}
					$calendars .= '<li><a class="fc-state-default '.$activeCal.'" href="'.$mp_header.'&cal_id='.$citem['id'].'">'.$citem['title'].'</a></li>';
					$calendars_o .= '<option value="'.$citem['id'].'">'.$citem['title'].'</option>';
				}
				foreach ($cal->getCategories($_GET['cal_id']) as $item){
					$categories_opt.='<option value="'.$item['id'].'">'.$item['title'].'</option>';
					$categories_li.='<li><a href="'.$mp_header.'&cal_id='.$_GET['cal_id'].'&cat_id='.$item['id'].'">'.$item['title'].'</a></li>';
				}
				$c = $cal->getCalendar($_GET['cal_id']);
				//если есть календарь по умолчанию, то приводим весь основной скрипт в работу
				$out.='<div id="dialog-form" title="Событие" style="display:none">
				<div id="result"></div>
						<form>
							<input type="hidden" name="cal_id" id="cal_id" value="'.(int)$_GET['cal_id'].'">
							<input type="hidden" name="cat_id" id="cat_id" value="'.(int)$_GET['cat_id'].'">
							<input type="hidden" name="event_id" id="event_id" value="">
							<p><label for="event_type">'.$lang['e_title'].'</label><br>
							<input type="text" id="event_type" name="event_type" value=""></p>
							<p><label for="event_cat">'.$lang['e_cat'].'</label><br>
							<select id="event_cat" name="event_cat" >
								<option value="0">'.$lang['e_cat_opt'].'</option>
								'.$categories_opt.'
							</select></p>
							<p><label for="event_color">'.$lang['e_color'].'</label><br>
							<select name="event_color" id="event_color">
							  '.$options.'
							</select></p>
							<p><label for="event_start">'.$lang['e_start'].'</label><br>
							<input type="text" name="event_start" id="event_start"/></p>
							<p><label for="event_end">'.$lang['e_end'].'</label><br>
							<input type="text" name="event_end" id="event_end"/></p>
							<p><label for="event_text">'.$lang['e_text'].'</label><br>
							<textarea name="event_text" id="event_text"></textarea></p>
							<p class="noty_p"><input type="checkbox" name="event_noty" id="event_noty" value="1">
							<label for="event_noty" style="display:inline-block;margin:5px 0"> '.$lang['e_noty'].'</label>
							<span class="add_alarm"> '.$lang['v'].' <input type="text" name="alarm_start" id="alarm_start" value=""></span></p>
							</form>
							<form method="post" class="form_hide">
							<p>'.$lang['e_moveto'].' <select name="ch_calendar" id="ch_calendar">
							<option value="'.$_GET['cal_id'].'">'.$lang['e_nochange'].'</option>
							'.$calendars_o.'</select>
							<button class="button fc-button fc-state-default" type="button" id="change_parent" name="change_parent" value="1">OK!</button></p>
						</form>
				</div>
				<div class="calendarLeft" title="Сменить календарь">
						<ul class="calendarList">
						'.$calendars.'
						</ul>
					</div>
				<div class="calendarWrapper">
					<div class="leftCat">
						<h2>'.$lang['e_filterH1'].'</h2>
						<ul>
							<li><a href="'.$mp_header.'&cal_id='.$_GET['cal_id'].'">'.$lang['e_all'].'</a></li>
							'.$categories_li.'
						</ul>
					</div>
					<div class="calendarInner">
						<a id="edit_calendar" href="#"><img src="'.$mp_imgs_dir.'edit.png"></a><h1>'.$c['title'].' (ID'.$c['id'].') <small></small></h1>
						<input type="hidden" name="cal_h1_id" id="cal_h1_id" value="'.$c['id'].'">
						<input type="hidden" name="cal_h1_def" id="cal_h1_def" value="'.$c['def'].'">
						<input type="hidden" name="cal_h1_title" id="cal_h1_title" value="'.$c['title'].'">
						<input type="hidden" name="cal_h1_description" id="cal_h1_description" value="'.$c['description'].'">
						<div id="calendar"></div>
					</div>
					<br class="clear">
				</div>';

				$btn_add_event ='<li id="add_event_button"><a href="#"><img src="'.$mp_imgs_dir.'folder_page_add.png"> '.$lang['but_addEvent'].'</a></li>';
				$btn_add_event .='<li id="add_cat_button"><a href="'.$mp_header.'&cal_id='.$_GET['cal_id'].'&op=cat"><img src="'.$mp_imgs_dir.'folder_add.png"> '.$lang['but_category'].'</a></li>';
				$btn_add_event .='<li id="change_cal"><a href="#"><img src="'.$mp_imgs_dir.'cal.gif"> </a></li>';
			}
		}
    }
    else{
        $out.='<div class="ui-state-highlight"><p>'.$lang['text_nocal'].'</p></div>';
    }
}
else {
    switch ((int)$_GET['op']) {
        case 'cat' :
            $cats = $cal->getCategories($_GET['cal_id']);
            $cal_one = $cal->getCalendar($_GET['cal_id']);
            $titl = 'Управление категориями';
            $out .= '<h1>Список категорий для календаря <a href="'.$mp_header.'&cal_id='.$cal_one['id'].'">"'.$cal_one['title'].'"</a></h1>';
            if (count($cats)==0) {
                $out.='Список категорий пуст. Добавьте первую категорию.';
            }
            else {
                $out.='<fieldset><legend>Список категорий</legend>
                <div class="categoryList"><ul>';
                foreach ($cats as $cat){
                    $out.='<li><a href="'.$mp_header.'&cal_id='.$cal_one['id'].'&op=cat&cat_opt=edit&cid='.$cat['id'].'">'.$cat['title'].'</a></li>';
                }
                $out.='</ul></div></fieldset>';
            }
            if ($_GET['cat_opt']){
                switch ($_GET['cat_opt']){
                    case 'add' :
                        $catalog['id']='NULL';
                        $catalog['title']='';
                        $catalog['description']='';
                        $catalog_but='Создать';
                        $del_cat = '';
                        if ($_POST['add_category']==1){
                            $post['title']=$_POST['title'];
                            $post['cal_id']=$cal_one['id'];
                            $post['description']=$_POST['description'];
                            $post['created']=time();
                            $cat_sovp=$modx->db->select('id,title',$cal->tbl_categories,"title='".$post['title']."'");
                            if ($modx->db->getRecordCount($cat_sovp)==1)
                            {
                                $out.='<p class="error">Нельзя добавить одинаковые категории</p>';
                            }
                            else {
                                $cal->addCategory($post);
                                header('Location:'.$_SERVER['REQUEST_URI']);
                            }
                        }
                        break;
                    case 'edit' :
                        if ((int)$_GET['cid']){
                            $c=$cal->getCategory($_GET['cid']);
                            $catalog['id']=$c['id'];
                            $catalog['title']=$c['title'];
                            $catalog['description']=$c['description'];
                            $catalog_but='Редактировать';
                            $del_cat = '<a href="'.$mp_header.'&cal_id='.$cal_one['id'].'&op=cat&cat_opt=delete&cid='.$cat['id'].'"><img src="'.$mp_imgs_dir.'event3.png"></a>';
                            if ($_POST['add_category']==1) {
                                $post['id'] = $_POST['id'];
                                $post['title'] = $_POST['title'];
                                $post['cal_id'] = $cal_one['id'];
                                $post['description'] = $_POST['description'];
                                $post['created'] = time();
                                $cat_sovp = $modx->db->select('id,title', $cal->tbl_categories, "title='" . $post['title'] . "'");
                                if ($modx->db->getRecordCount($cat_sovp) == 1) {
                                    $out .= '<p class="error">Нельзя добавить одинаковые категории</p>';
                                } else {
                                    $cal->updateCategory($post, $post['id']);
                                    header('Location:' . $_SERVER['REQUEST_URI']);
                                }
                            }
                        }
                        break;
                    case 'delete':
                        if ((int)$_GET['cid']){
                            $cal->deleteCategory($_GET['cid']);
                            header('Location:' . $mp_header . '&cal_id=' . $cal_one['id'] . '&op=cat');
                        }
                }
                $out.='<fieldset><legend>'.$catalog_but.' категорию '.$del_cat.'</legend>
                        <form method="post">
                            <input type="hidden" name="id" value="'.$catalog['id'].'">
                            <b>Название категории</b><br>
                            <input type="text" name="title" value="'.$catalog['title'].'"><br>
                            <b>Описание категории</b><br>
                            <textarea name="description">'.$catalog['description'].'</textarea><br>
                            <button type="submit" name="add_category" value="1" class="button fc-button fc-state-default">'.$catalog_but.'</button>
                        </form>
                        </fieldset>';

            }
            $btn_add_event ='<li id="add_cat_button"><a href="'.$mp_header.'&cal_id='.$cal_one['id'].'&op=cat&cat_opt=add"><img src="'.$mp_imgs_dir.'folder_page_add.png"> Добавить категорию</a></li>';
            break;
    }
}
switch ($_GET['act'])
{
    case 'add_calendar' :
        $titl = 'Добавление календаря';
        $title = $_POST['title'];
        $description = $_POST['description'];
        if (count($cal->getCalendars())>0)
        {
            $default = 1;
        }
        $default = $_POST['def'];
        break;

}

$out.='<div id="actions">
<ul class="actionButtons">
    <li><a href="'.$mp_header.'" id="home"><img src="'.$mp_imgs_dir.'home.gif"> Домой</a></a></li>
    <li><a href="#" id="add_calendar"><img src="'.$mp_imgs_dir.'add.png"> Добавить календарь</a></a></li>
    <li><a href="" id="refresh"><img src="'.$mp_imgs_dir.'refresh.png"> Обновить</a></a></li>
    '.$btn_add_event.'
</ul>
</div>';
$html = '<html>
<head>
<title>'.$lang['module_name'].'</title>
<meta http-equiv="Content-Type" content="text/html; charset='.$modx->config['modx_charset'].'" />
<link rel="stylesheet" type="text/css" href="'.MODX_MANAGER_URL.'/media/style/'.$theme.'/style.css" />
<link rel="stylesheet" type="text/css" href="'.$mp_url.'css/jquery-ui.css" />
<link href="'.$mp_url.'fullcalendar/fullcalendar.css" rel="stylesheet" />
<link href="'.$mp_url.'css/jquery-ui-timepicker-addon.css" rel="stylesheet" />
<link href="'.$mp_url.'fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print" />
<link href="'.$mp_url.'css/jquery.simplecolorpicker.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="'.$mp_url.'css/modxplaning.css" />
<script src="'.$mp_url.'fullcalendar/lib/moment.min.js"></script>
<script src="'.$mp_url.'js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="'.$mp_url.'js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="'.$mp_url.'js/jquery-ui.min.js" type="text/javascript"></script>
<script src="'.$mp_url.'js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="'.$mp_url.'fullcalendar/fullcalendar.min.js"></script>
<script src="'.$mp_url.'fullcalendar/lang/'.$lng.'.js"></script>
<script src="'.$mp_url.'js/jquery.simplecolorpicker.js"></script>
<script src="'.$mp_url.'js/init.js" type="text/javascript"></script>
</head>
    <body>
        <h1>MODx Planing</h1>
        <div class="sectionHeader">'.$titl.'</div>
        <div class="sectionBody">
        '.$out.'</div>
        <div class="addCal" title="Управление календарём">
            <span></span>
            <input type="hidden" name="calendar_id"  id="calendar_id" value="">
            <p>Название</p>
            <input type="text" name="cal_title" id="cal_title" value="">
            <p>Описание</p>
            <textarea name="cal_description" id="cal_description"></textarea><br>
            <input type="checkbox" name="def" id="cal_def" value="1"> По умолчанию
        </div>
    </body>
</html>';
echo $html;
