<?php
/* HEADERS */
$theme = $modx->config['manager_theme'];
$mp_mod_id = $_GET['id'];  //id модуля
$mp_folder = 'assets/modules/modxplaning/';     //папка с модулем
$mp_url = $modx->config['site_url'].$mp_folder;
$mp_header = $modx->config['site_url'].'manager/index.php?a=112&id='.$mp_mod_id;
$mp_location = MODX_BASE_PATH.$mp_folder;   //путь до папки на сервере
$mp_imgs_dir = $mp_url.'images/';
/* TABLES */
$tbl_calendars = $modx->getFullTableName('planing_calendars');
$tbl_events = $modx->getFullTableName('planing_events');
$tbl_categories = $modx->getFullTableName('planing_categories');

$install = "
CREATE TABLE IF NOT EXISTS ".$tbl_calendars." (
  `id` int(10) NOT NULL auto_increment,
  `created` varchar(100) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` TEXT NOT NULL default '',
  `doc_id` int(10) NOT NULL default '0',
  `def` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)";
$modx->db->query($install);
$install = "
CREATE TABLE IF NOT EXISTS ".$tbl_categories." (
  `id` int(10) NOT NULL auto_increment,
  `cal_id` int(10) NOT NULL default '1',
  `created` varchar(100) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` TEXT NOT NULL default '',
  PRIMARY KEY  (`id`)
)";
$modx->db->query($install);
$install = "
CREATE TABLE IF NOT EXISTS ".$tbl_events." (
  `id` int(10) NOT NULL auto_increment,
  `cal_id` int(10) NOT NULL default '0',
  `cat_id` int(10) NOT NULL default '0',
  `created` varchar(100) NOT NULL default '',
  `event_start` varchar(100) NOT NULL default '',
  `event_end` varchar(100) NOT NULL default '',
  `alarm_start` varchar(100) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` TEXT NOT NULL default '',
  `color` varchar(100) NOT NULL default '',
  `notify` int(1) NOT NULL default '0',
  `sticky` int(1) NOT NULL default '0',
  `status` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)";
$modx->db->query($install);