<?php echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php echo form_tag('delete', url_for('widgets/calc','action=delete&widget_id=' . _GET('widget_id') . '&id=' . $_GET['id'])) ?>
<div class="modal-body">    
<?php echo TEXT_ARE_YOU_SURE ?>
</div> 
<?php echo ajax_modal_template_footer(TEXT_DELETE) ?>

</form>  