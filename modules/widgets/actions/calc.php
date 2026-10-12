<?php

$widget_info = db_find('app_widgets', _GET('widget_id'));
if($widget_info['id']=='')
{
    redirect_to('widgets/index');
}

switch($app_module_action)
{
    case 'save':
        $sql_data = array(
            'widget_id' => _GET('widget_id'), 
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'entities_id' => $_POST['entities_id'],
            'formula' => $_POST['functions_formula'],
            'is_hidden' => isset($_POST['is_hidden']) ? 1 : 0,
            'number_format' => $_POST['number_format'],
            'number_prefix' => $_POST['number_prefix'],
            'number_suffix' => $_POST['number_suffix'],            
            'color' => $_POST['color']??'',            
            'sort_order' => $_POST['sort_order']);

        if(isset($_GET['id']))
        {
            $obj = db_find('app_widgets_calc',$_GET['id']);
            
            //if entity is changed remove report filters
            if($obj['entities_id']!=$sql_data['entities_id'])
            {
                reports::delete_reports_by_type('widget_calc' . _GET('id'));
            }
            
            db_perform('app_widgets_calc', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        }
        else
        {
            db_perform('app_widgets_calc', $sql_data);            
        }

        redirect_to('widgets/calc', 'widget_id=' . _GET('widget_id'));
        break;
    case 'delete':
        if(isset($_GET['id']))
        {
                        
            db_delete_row('app_widgets_calc', _GET('id'));
            
            reports::delete_reports_by_type('widget_calc' . _GET('id'));
                        
            redirect_to('widgets/calc', 'widget_id=' . _GET('widget_id'));
        }
        break;
    case 'sort':
        $choices_sorted = $_POST['choices_sorted'];

        if(strlen($choices_sorted) > 0)
        {
            $choices_sorted = json_decode(stripslashes($choices_sorted), true);            

            $sort_order = 0;
            foreach($choices_sorted as $v)
            {
                db_query("update app_widgets_calc set sort_order={$sort_order} where id={$v['id']}");
                $sort_order++;
            }
        }

        redirect_to('widgets/calc', 'widget_id=' . _GET('widget_id'));
        break;
}
