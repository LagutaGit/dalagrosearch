<?php

$widget_info = db_find('app_widgets', _GET('widget_id'));
if($widget_info['id']=='')
{
    redirect_to('widgets/index');
}

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_widgets_calc',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_widgets_calc');
  $obj['sort_order']=0;
  $obj['number_format'] = '0/./ ';
  
  $row_query = db_query("select max(sort_order)+1 as sort_order from app_widgets_calc where widget_id=" . _GET('widget_id'));
  $row = db_fetch($row_query);
  $obj['sort_order'] =  $row->sort_order??0; 
  
      
  
}