
<?php echo ajax_modal_template_header(TEXT_INFO) ?>

<?php echo form_tag('groups_form', url_for('widgets/calc', 'action=save&widget_id=' . _GET('widget_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')), array('class' => 'form-horizontal')) ?>
<div class="modal-body">
    <div class="form-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_tab"  data-toggle="tab"><?php echo TEXT_GENERAL_INFO ?></a></li>   
            <li><a href="#value_tab"  data-toggle="tab" ><?php echo TEXT_VALUE ?></a></li>             
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_tab">  


                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
                    <div class="col-md-9">	
                        <?php echo input_tag('name', $obj['name'], array('class' => 'form-control  required')) ?>
                    </div>			
                </div>  

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php echo TEXT_TYPE ?></label>
                    <div class="col-md-9">	
                        <?php echo select_tag('type', widgets::get_calc_types_choices(), $obj['type'], array('class' => 'form-control input-xlarge')) ?>                
                    </div>			
                </div>

                <div class="form-group" form_display_rules="type:!PHP">
                    <label class="col-md-3 control-label" for="entities_id"><?php echo TEXT_REPORT_ENTITY ?></label>
                    <div class="col-md-9"><?php echo select_tag('entities_id', entities::get_choices(), $obj['entities_id'], array('class' => 'form-control input-xlarge chosen-select')) ?>
                    </div>			
                </div> 

                <div class="form-group function-prop" form_display_rules="type:!COUNT">
                    <label class="col-md-3 control-label" for="functions_formula"><?php echo TEXT_FORMULA ?> 
                        <div id="available_fields" form_display_rules="type:!PHP"></div>
                    </label>
                    <div class="col-md-9">	
                        <?php echo textarea_tag('functions_formula', $obj['formula'], array('class' => 'form-control code')) ?>
                        <div form_display_rules="type:!PHP"><?php echo tooltip_text(TEXT_FUNCTIONS_FORMULA_INFO); ?></div>      
                        <div form_display_rules="type:PHP"><?php echo tooltip_text(TEXT_WIDGET_CALCULATIONS_PHP_TIP); ?></div>
                    </div>			
                </div>
                
                <?php if($widget_info['type']=='summary'):  ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="is_hidden"><?php echo tooltip_icon(TEXT_HIDDEN_CALC_TIP) . TEXT_HIDDEN_CALC ?></label>
                    <div class="col-md-9">	
                        <p class="form-control-static"><?php echo input_checkbox_tag('is_hidden', $obj['is_hidden'], array('checked' => ($obj['is_hidden'] == 1 ? 'checked' : ''))) ?></p>
                    </div>			
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php echo TEXT_COLOR ?></label>
                    <div class="col-md-9">	
                        <?php echo input_color('color', $obj['color']) ?>
                    </div>			
                </div>
                
                <?php endif ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="sort_order"><?php echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-9">	
                        <?php echo input_tag('sort_order', $obj['sort_order'], array('class' => 'form-control input-small required number')) ?>
                    </div>			
                </div>

            </div>
            
            <div class="tab-pane fade" id="value_tab">
                <div class="form-group">
                    <label class="col-md-4 control-label" for="cfg_numer_format"><?php echo tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT ?></label>
                    <div class="col-md-8">	
                        <?php echo input_tag('number_format', $obj['number_format'], array('class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~')) ?>	      
                    </div>			
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="cfg_numer_format"><?php echo TEXT_PREFIX ?></label>
                    <div class="col-md-8">	
                        <?php echo input_tag('number_prefix', $obj['number_prefix'], array('class' => 'form-control input-small ')) ?>	      
                    </div>			
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="cfg_numer_format"><?php echo TEXT_SUFFIX ?></label>
                    <div class="col-md-8">	
                        <?php echo input_tag('number_suffix', $obj['number_suffix'], array('class' => 'form-control input-small ')) ?>	      
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
        $('#groups_form').validate({
            submitHandler: function (form)
            {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

        load_available_fields();


        $('#entities_id').change(function ()
        {
            load_available_fields();
        })
        
        $(".input-masked").each(function(){
            $.mask.definitions["~"]="[,. ]";
            $(this).mask($(this).attr("data-mask"));
        }) 
    });

    function load_available_fields()
    {
        $('#available_fields').html('');
        $('#available_fields').addClass('ajax-loading');
        $('#available_fields').load('<?php echo url_for("ext/functions/functions", "action=get_available_fields") ?>', {entities_id: $('#entities_id').val()}, function ()
        {
            $('#available_fields').removeClass('ajax-loading');
        })
    }

</script>  


