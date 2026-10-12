
<ul class="page-breadcrumb breadcrumb">
    <?php
    echo '
        <li>' . link_to(TEXT_WIDGETS, url_for('widgets/index')) . '<i class="fa fa-angle-right"></i></li>
        <li>' . $widget_info['name']. '<i class="fa fa-angle-right"></i></li></li>    
        <li>' . TEXT_CALCULATIONS . '</li>';
    ?>
</ul>

<p><?php echo $widget_info['type']=='single_counter' ? TEXT_WIDGETS_CALC_SINGLE_COUNTER_INFO : TEXT_WIDGETS_CALC_INFO ?></p>


<?php echo button_tag(TEXT_BUTTON_ADD, url_for('widgets/calc_form', 'widget_id=' . _GET('widget_id'))) ?> 
<?php echo button_tag(TEXT_BUTTON_SORT, url_for('widgets/calc_sort', 'widget_id=' . _GET('widget_id')),true,['class'=>'btn btn-default']) ?> 
<?php echo button_tag(TEXT_EDIT_WIDGET, url_for('widgets/form', 'id=' . _GET('widget_id') . '&redirect_to=calc'),true,['class'=>'btn btn-default']) ?> 


<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th><?php echo TEXT_ACTION ?></th>    
                <th><?php echo TEXT_ID ?></th>                
                <th><?php echo TEXT_TYPE ?></th>
                <th><?php echo TEXT_ENTITY ?></th>
                <th><?php echo TEXT_FILTERS ?></th>
                <th width="100%"><?php echo TEXT_NAME ?></th>                                                
                <th><?php echo TEXT_SORT_ORDER ?></th>            
            </tr>
        </thead>
        <tbody>

            <?php


            $rows_query = db_query("select c.*, e.name as entity_name from app_widgets_calc c left join app_entities e on e.id=c.entities_id where c.widget_id=" . _GET('widget_id'). " order by c.sort_order, c.name");

            if(db_num_rows($rows_query) == 0)
                echo '<tr><td colspan="8">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';

            while($row = db_fetch($rows_query)):                                
                ?>
                <tr>
                    <td style="white-space: nowrap;">
                        <?php echo button_icon_delete(url_for('widgets/calc_delete', 'id=' . $row->id . '&widget_id=' . $row->widget_id)) . ' ' . button_icon_edit(url_for('widgets/calc_form', 'id=' . $row->id . '&widget_id=' . $row->widget_id)); ?>
                    </td>        
                    <td><?php echo input_tag('calc_id', '${' . $row->id . '}', ['class'=>'form-control input-small select-all code']) ?></td>   
                    <td><?php echo $row->type; ?></td>
                    <td><?php echo $row->type!=='PHP' ? $row->entity_name : ''; ?></td>
                    <td><?php 
                        if($row->type!=='PHP')
                        {
                            $calc_report_id = reports::auto_create_report_by_type($row->entities_id,'widget_calc' . $row->id);
                            echo link_to(TEXT_FILTERS . ' (' . reports::count_filters_by_reports_id($calc_report_id) . ')', url_for('default_filters/filters','reports_id=' . $calc_report_id . '&redirect_to=widget_calc' . $row->widget_id . '_' . $row->id));
                        } 
                        ?></td>                    
                    <td><?php echo (strlen($row->color) ? app_render_icon('fa-square','',$row->color) . ' ' : '') 
                            . $row->name
                            . ($row->is_hidden ? '<br><small>' . TEXT_HIDDEN_CALC . '</small>':''); ?></td>
                    
                    <td><?php echo $row->sort_order ?></td>
                </tr>
            <?php endwhile ?>  
        </tbody>
    </table>
</div>

<?php echo '<a class="btn btn-default" href="' . url_for('widgets/index') . '">' . TEXT_BUTTON_BACK . '</a>'; ?>

<h3 class="form-section"><?= TEXT_PREVIEW ?></h3>   

<?php
    if($widget_info['type']=='single_counter')
    {
        echo '<div style="max-width: 350px;" id="widget_preview"><div class="ajax-loading-small"></div></div>';
    }
    else
    {
        echo '<div style="max-width: 800px;"  id="widget_preview"><div class="ajax-loading-small"></div></div>';
    }
?>

<script>
    $(function () {
        $('#widget_preview').load("<?= url_for('widgets/preview','id=' . $widget_info['id']) ?>")
    })
</script>
