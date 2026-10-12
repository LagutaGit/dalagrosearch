<?php

switch($app_module_action)
{
    case 'save':
        $sql_data = array(
            'name' => $_POST['name'],
            'sort_order' => $_POST['sort_order']);

        if(isset($_GET['id']))
        {
            db_perform('app_widgets_groups', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        }
        else
        {
            db_perform('app_widgets_groups', $sql_data);
            $id = db_insert_id();
        }

        redirect_to('widgets/groups');
        break;
    case 'delete':
        if(isset($_GET['id']))
        {
                        
            db_delete_row('app_widgets_groups', _GET('id'));
            
            db_query("update app_widgets set group_id=0 where group_id=" . _GET('id'));


            redirect_to('widgets/groups');
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
                db_query("update app_widgets_groups set sort_order={$sort_order} where id={$v['id']}");
                $sort_order++;
            }
        }

        redirect_to('widgets/groups');
        break;
}
