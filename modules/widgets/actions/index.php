<?php

$app_title = app_set_title(TEXT_USERS_ALERTS);

switch($app_module_action)
{
  case 'save':
    $sql_data = array(
    	'is_active'	=> (isset($_POST['is_active']) ? 1:0),
        'group_id'	=> (int)($_POST['group_id']??0),    	    	
    	'type'	=> $_POST['type'],    	    	
    	'name'	=> $_POST['name'],    	
        'title'	=> $_POST['title'],        
        'subtitle'	=> $_POST['subtitle'],        
    	'description'	=> $_POST['description'],        
        'text_color'	=> $_POST['text_color'],
        'bg_color' => $_POST['bg_color'],  
        
        'display_chart'	=> $_POST['display_chart'],
        'chart_type'	=> $_POST['chart_type'],
        'chart_height'	=> (int)$_POST['chart_height'],
        'is_hide_labels'	=> (isset($_POST['is_hide_labels']) ? 1:0),        
        
    	'users_groups' => (isset($_POST['users_groups']) ? implode(',',$_POST['users_groups']):''),        
        'notes'	=> $_POST['notes'],
    	'created_by' => $app_user['id'],    	    	
    );
        
    if(isset($_GET['id']))
    {                  
      db_perform('app_widgets',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
    }
    else
    {                     
      db_perform('app_widgets',$sql_data);                             
    }
        
    if($app_redirect_to=='calc')
    {
        redirect_to('widgets/calc', 'widget_id=' . _GET('id'));
    }
    else
    {
        redirect_to('widgets/index');      
    }
  break;
  case 'delete':
      if(isset($_GET['id']))
      {  
        $widget_id = _get::int('id');
        
        db_query("delete from app_widgets where id='" . $widget_id . "'"); 
        
        //delete calc filters
        $rows_query = db_query("select * from app_widgets_calc where widget_id={$widget_id} order by sort_order, name");
        while($row = db_fetch($rows_query))
        {
            reports::delete_reports_by_type('widget_calc' . $row->id);
        }
        
        db_query("delete from app_widgets_calc where widget_id='" . $widget_id . "'"); 
                     
        redirect_to('widgets/index');  
      }
    break; 

}