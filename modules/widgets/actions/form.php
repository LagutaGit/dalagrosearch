<?php

$obj = array();

if(isset($_GET['id']))
{
	$obj = db_find('app_widgets',$_GET['id']);
}
else
{
	$obj = db_show_columns('app_widgets');		
	$obj['is_active'] = 1;
}
