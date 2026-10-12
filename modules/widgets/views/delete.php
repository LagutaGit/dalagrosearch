<?php echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php echo form_tag('login', url_for('widgets/index','action=delete&id=' . $_GET['id'])) ?>
<div class="modal-body">    
<?php
    $row = db_find('app_widgets',_get::int('id'));
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION,$row['name'])
?>
</div> 
<?php echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>   
    