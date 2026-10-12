<?php

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_widgets_groups',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_widgets_groups');
  $obj['sort_order']=0;
}