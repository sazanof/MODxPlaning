<?php
/**
 * A newest module for MODx Evolution.
 * It's my first class, I'vs ever make
 * Author: sazanof
 * Date: 13.10.2014
 * Time: 16:59
 * So, let's begin
 */

class MODxPlaning {
    public $tbl_events;
    public $tbl_categories;
    public $tbl_calendars;
    public $delCheck;
    public $manager_email;

    public function getDefaultCalendar()
    {
        global $modx;
        return $modx->db->getRow($modx->db->select("id,created,title,description,doc_id,def",$this->tbl_calendars,'def=1'));
    }
    public function getCalendar($id)
    {
        global $modx;
        return $modx->db->getRow($modx->db->select("id,created,title,description,doc_id,def",$this->tbl_calendars.'id='.$id));
    }
    public function getCalendars ()
    {
        global $modx;
        return $modx->db->makeArray($modx->db->select("id,created,title,description,doc_id,def",$this->tbl_calendars));
    }
    public function editCalendar ($post,$tbl,$id)
    {
        global $modx;
        return $modx->db->update($post,$tbl,'id = "' . $id . '"');
    }
    public function deleteCalendar ($id)
    {
        global $modx;
        $c = $this->getCalendar($id);
        if ($c['def'] == 1) {
            $this->delCheck=1;
            echo "Нельзя удалить календарь по умолчанию!";
        } else {
            if ($modx->db->delete($this->tbl_calendars, 'id = "' . $id . '"')) {
                $modx->db->delete($this->tbl_events, 'cal_id = "' . $id . '"');
                $modx->db->delete($this->tbl_categories, 'cal_id = "' . $id . '"');
            }
            $this->delCheck=0;
        }
    }
    public function getCategories ($id)
    {
        global $modx;
        return $modx->db->makeArray($modx->db->select("id,title,description",$this->tbl_categories,'cal_id='.$id));
    }
    public function getCategory ($id)
    {
        global $modx;
        return $modx->db->getRow($modx->db->select("id,title,description",$this->tbl_categories,'='.$id));
    }
    public function addCategory ($post)
    {
        global $modx;
        return $modx->db->insert($post,$this->tbl_categories);
    }
    public function deleteCategory ($id){
        global $modx;
        return $modx->db->delete($this->tbl_categories,'id = "' . $id . '"');
    }
    public function updateCategory ($post,$id){
        global $modx;
        return $modx->db->update($post,$this->tbl_categories,'id = "' . $id . '"');
    }
    public function getJsonEvents($fields,$where){
        global $modx;
        if ($fields=='')
        {
            $fields = '*';
        }
        $this->fields = $fields;
        $sql = $modx->db->select($fields,$this->tbl_events,$where);
        $ar = $modx->db->makeArray($sql);
        $i = 0;
        $data = array();
        foreach($ar as $a) {
            $data[$i]['id'] = $a['id'];
            $data[$i]['start'] = date('Y-m-d H:i',$a['event_start']);
            $data[$i]['end'] = date('Y-m-d H:i',$a['event_end']);
            $data[$i]['title'] = $a['title'];
            $data[$i]['description'] = $a['description'];
            $data[$i]['color'] = $a['color'];
            $data[$i]['cat_id'] = $a['cat_id'];
            $data[$i]['notify'] = $a['notify'];
            if (!empty($a['alarm_start']))
            {
                $data[$i]['alarm_start'] = date('Y-m-d H:i',$a['alarm_start']);
            }
            $i++;
        }
        $json = json_encode($data);
        return $json;
    }
    public function insertEvent($post,$tbl){
        global $modx;
        return $modx->db->insert($post,$tbl);
    }
    public function updateEvent ($post,$id){
        global $modx;
        return $modx->db->update($post,$this->tbl_events,"id='" . $id . "'");
    }
    public function deleteEvent ($id){
        global $modx;
        return $modx->db->delete($this->tbl_events,'id = "' . $id . '"');
    }
    public function colors(){
        // добавить русификацию
        $colors = array(
            'Зеленый'=>'#7bd148',
            'Темно-синий'=>'#5484ed',
            'Синий'=>'#a4bdfc',
            'Бирюзовый'=>'#46d6db',
            'Светло-зелёный'=>'#7ae7bf',
            'Тёмно-зеленый'=>'#51b749',
            'Жёлтый'=>'#fbd75b',
            'Оранжевый'=>'#f37328',
            'Красный'=>'#ff887c',
            'Темно-красный'=>'#dc2127',
            'Пурпурный'=>'#dbadff',
            'Серый'=>'#e1e1e1',
        );
        return $colors;
    }
}
