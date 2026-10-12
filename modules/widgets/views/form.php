
<?php echo ajax_modal_template_header(TEXT_INFO) ?>

<?php echo form_tag('users_alerts_form', url_for('widgets/index', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')), array('class' => 'form-horizontal')) ?>
<?= input_hidden_tag('redirect_to',  $app_redirect_to) ?>
<div class="modal-body">
    <div class="form-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_tab"  data-toggle="tab"><?php echo TEXT_GENERAL_INFO ?></a></li>   
            <li><a href="#chart_tab"  data-toggle="tab" form_display_rules="type:summary"><?php echo TEXT_CHART ?></a></li>            
            <li><a href="#access_tab"  data-toggle="tab"><?php echo TEXT_ACCESS ?></a></li>            
            <li><a href="#note_tab"  data-toggle="tab"><?php echo TEXT_NOTE ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_tab">  

                <div class="form-group">
                    <label class="col-md-3 control-label" for="is_active"><?php echo TEXT_IS_ACTIVE ?></label>
                    <div class="col-md-9">	
                        <p class="form-control-static"><?php echo input_checkbox_tag('is_active', $obj['is_active'], array('checked' => ($obj['is_active'] == 1 ? 'checked' : ''))) ?></p>
                    </div>			
                </div>
                
                <?php if(count(widgets::get_group_choices())) { ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name"><?php echo TEXT_GROUP ?></label>
                        <div class="col-md-9">	
                            <?php echo select_tag('group_id', [''=>'']+widgets::get_group_choices(), $obj['group_id'], array('class' => 'form-control input-large')) ?>
                        </div>			
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
                    <div class="col-md-9">	
                        <?php echo input_tag('name', $obj['name'], array('class' => 'form-control input-xlarge required')) ?>
                    </div>			
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php echo TEXT_TYPE ?></label>
                    <div class="col-md-9">	
                        <?php echo select_tag('type', widgets::get_types_choices(), $obj['type'], array('class' => 'form-control input-medium')) ?>
                        <div form_display_rules="type:single_counter"><?= tooltip_text(TEXT_WIDGET_SINGLE_COUNTER_TIP) ?></div>
                        <div form_display_rules="type:summary"><?= tooltip_text(TEXT_WIDGET_SUMMARY_TIP) ?></div>
                    </div>			
                </div>
                
                
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php echo TEXT_TEXT_COLOR ?></label>
                    <div class="col-md-9">	
                        <?php echo input_color('text_color', $obj['text_color']) ?>
                    </div>			
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php echo TEXT_BACKGROUND_COLOR ?></label>
                    <div class="col-md-9">	
                        <?php echo input_color('bg_color', $obj['bg_color']) ?>
                    </div>			
                </div>
                
                
                <div form_display_rules="type:summary">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name"><?php echo TEXT_HEADING ?></label>
                        <div class="col-md-9">	
                            <?php echo input_tag('title', $obj['title'], array('class' => 'form-control input-xlarge')) ?>
                        </div>			
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="description"><?php echo TEXT_TEXT ?></label>
                        <div class="col-md-9">	
                            <?php echo textarea_tag('description', $obj['description'], array('class' => 'form-control input-xlarge textarea-small')) ?>
                        </div>			
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name"><?php echo TEXT_SUBTITLE ?></label>
                        <div class="col-md-9">	
                            <?php echo input_tag('subtitle', $obj['subtitle'], array('class' => 'form-control input-xlarge')) ?>
                        </div>			
                    </div> 
                                                            
                    
                </div>

            </div>
            
            <div class="tab-pane fade" id="chart_tab">
                <?php
                    $choices = [
                        '0' => TEXT_NO,
                        '1' => TEXT_YES,
                        'chart_only' => TEXT_CHART_ONLY,
                        
                    ];
                    ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name"><?php echo TEXT_DISPLAY_CHART ?></label>
                        <div class="col-md-9">	
                            <?php echo select_tag('display_chart', $choices, $obj['display_chart'], array('class' => 'form-control input-medium')) ?>
                        </div>			
                    </div>
                
                <?php
                $choices_chart_type = [                    
                    'pie' => TEXT_PIE_CHART,
                    'column' => TEXT_COLUMN_CHART,
                    'bar' => TEXT_BAR_CHART,
                    'line' => TEXT_LINE_CHART,
                    'spline'=>TEXT_SPLINE_CHART,
                    'funnel' => TEXT_FUNNEL_CHART,
                    'pyramid' => TEXT_PYRAMID_CHART,
                    'area' => TEXT_AREA_CHART,                   
                ];
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php echo TEXT_TYPE ?></label>
                    <div class="col-md-9">	
                        <?php echo select_tag('chart_type', $choices_chart_type, $obj['chart_type'], array('class' => 'form-control input-large')) ?>        
                    </div>			
                </div>
                
                <div class="form-group" form_display_rules="chart_type:pie,pyramid,funnel">
                    <label class="col-md-3 control-label" for="is_active"><?php echo TEXT_HIDE_LABELS ?></label>
                    <div class="col-md-9">	
                        <p class="form-control-static"><?php echo input_checkbox_tag('is_hide_labels', $obj['is_hide_labels'], array('checked' => ($obj['is_hide_labels'] == 1 ? 'checked' : ''))) ?></p>
                    </div>			
                </div>
                
                
                <div class="form-group">
                    <label class="col-md-3 control-label" for="chart_height"><?php echo tooltip_icon(TEXT_DEFAULT . ': 300') . TEXT_HEIGHT ?></label>
                    <div class="col-md-9">	
                        <?php echo input_tag('chart_height', $obj['chart_height'], array('class' => 'form-control input-small', 'type' => 'number')) ?>        
                    </div>			
                </div>
            </div>    

            <div class="tab-pane fade" id="access_tab">        
                
                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">	
                        <div class="input-group dropup">                                
                            <?php echo select_tag('users_groups[]', access_groups::get_choices(), (strlen($obj['users_groups']) ? $obj['users_groups'] : -1), array('class' => 'form-control  chosen-select', 'multiple' => 'multiple')) ?>		                                      
                            <?= input_group_btn_select_all('users_groups') ?>                                                                
                        </div>

                    </div>			
                </div>


            </div>

            <div class="tab-pane fade" id="note_tab">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php echo TEXT_ADMINISTRATOR_NOTE ?></label>
                    <div class="col-md-9">	
                        <?php echo textarea_tag('notes', $obj['notes'], array('class' => 'form-control')) ?>
                    </div>			
                </div> 
            </div>
        </div>    

    </div>
</div> 

<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
    $(function ()
    {
        $('#users_alerts_form').validate({
            submitHandler: function (form)
            {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });
    });

</script>   


