<h3 class="page-title"><?php echo TEXT_WIDGETS ?></h3>

<p><?php echo TEXT_WIDGETS_INFO ?></p>

<?php echo button_tag(TEXT_WIDGETS_ADD, url_for('widgets/form'))
        . ' ' . button_tag( TEXT_WIDGETS_GROUPS, url_for('widgets/groups'), false, array('class' => 'btn btn-default'))?>

<div class="table-scrollable">
    <table class="tree-table table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th><?php echo TEXT_ACTION ?></th> 
                <th><?php echo TEXT_IS_ACTIVE ?></th>
                <th><?php echo TEXT_TYPE ?></th> 
                <th width="100%"><?php echo TEXT_NAME ?></th>                                  
                <th><?php echo TEXT_CALCULATIONS ?></th> 
                <th><?php echo TEXT_COLOR ?></th> 
                
                <th><?php echo TEXT_ASSIGNED_TO ?></th>            
            </tr>
        </thead>
        <tbody>
            <?php
            
            $current_folder = '';
            $access_groups_cache = access_groups::get_cache();

            $rows_query = db_query("select w.*, g.name as folder_name, g.id as folder_id  from app_widgets w left join app_widgets_groups g on w.group_id=g.id order by g.sort_order, g.name,  w.name");
            while($row = db_fetch_array($rows_query)):
                
                if($current_folder!=$row['folder_name'])
                {
                    $current_folder = $row['folder_name'];
                    if(strlen($current_folder??''))
                    {
                        echo '
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="5">
                                    <div class="tt" data-tt-id="folder_row_' . $row['folder_id']. '" ></div> <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                    <b>' . $current_folder . '</b></td>
                            </tr>
                            ';
                    }
                }
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('widgets/delete', 'id=' . $row['id'])) . ' ' . button_icon_edit(url_for('widgets/form', 'id=' . $row['id'])); ?></td>        
                    <td><?php echo render_bool_value($row['is_active']) ?></td>  
                    <td><?php echo widgets::get_type_by_name($row['type']) ?></td>
                    <td>
                        <?php echo ($row['folder_id'] >0 ? '<div class="tt" data-tt-id="row_' . $row['id']. '" data-tt-parent="folder_row_' . $row['folder_id'] . '"></div>' : '') ?>
                        <?php echo link_to($row['name'], url_for('widgets/calc','widget_id=' . $row['id'])) ?>
                        
                    </td>
                    <td><?= link_to(TEXT_CONFIGURE, url_for('widgets/calc','widget_id=' . $row['id'])) ?></td>
                    <td><?= render_bg_color_block($row['bg_color'], $row['bg_color'], $row['text_color']) ?></td>                    
                    <td>
                        <?php
                        if(strlen($row['users_groups']) > 0)
                        {
                            $users_groups = array();
                            foreach(explode(',', $row['users_groups']) as $id)
                            {
                                $users_groups[] = strip_tags(access_groups::get_name_by_id($id));
                            }

                            if(count($users_groups) > 0)
                            {
                                echo '<span style="display:block" data-html="true" data-toggle="tooltip" data-placement="left" title="' . htmlspecialchars(implode('<br>', $users_groups)) . '">' . TEXT_USERS_GROUPS . ' (' . count($users_groups) . ')</span>';
                            }
                        }
                        ?>    
                    </td>                                        
                </tr>
<?php endwhile ?>
            <?php if(db_num_rows($rows_query) == 0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>'; ?>  
        </tbody>
    </table>
</div>


