<?php

class fieldtype_entity_value
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_ENTITY_VALUE_TITLE);
    }

    function get_configuration()
    {
        $entities_id = _POST('entities_id');
        
        $cfg = [];                                                
        $cfg[] = array('title' => TEXT_RELATED_ENTITY_BY_FIELD,
            'name' => 'related_entity',
            'tooltip' => TEXT_RELATED_ENTITY_BY_FIELD_TIP,
            'type' => 'dropdown',
            'choices' => self::get_related_entity_choices($entities_id),
            'params' => array('class' => 'form-control', 'onChange'=>'fields_types_ajax_configuration(\'fields_in_form\',this.value)'));

        $cfg[] = array('name'=>'fields_in_form','type'=>'ajax','html'=>'<script>fields_types_ajax_configuration(\'fields_in_form\',$("#fields_configuration_related_entity").val())</script>');
        
        return $cfg;
    }
    
    function get_ajax_configuration($name, $value)
    {
  	global $app_entities_cache;
        
        $cfg = array();
  	  	  	
  	switch($name)
  	{
  		case 'fields_in_form':
                    $field_id = $value;
                    
                    $choices = [];
                    
                    $field = db_find('app_fields', $field_id);
                    
                    $entity_id = self::get_related_entity_id($field);
                                                                                                   
                    $cfg[] = array(
                        'title'=>TEXT_FIELD, 
                        'name'=>'related_entity_field',
                        'type'=>'dropdown',
                        'tooltip' => TEXT_FIELDTYPE_ENTITY_VALUE_SUPPORTED_TYPES_TIP,
                        'choices'=> self::get_related_entity_fields_choices($entity_id),
                        'params'=>array('class'=>'form-control'));
                    
                    break;
        }
        
        return $cfg;
  	
    }
    
    static function get_related_entity_fields_choices($entity_id)
    {
        $allowed_types = [
            'fieldtype_input',
            'fieldtype_input_date',
            'fieldtype_input_date_extra',
            'fieldtype_input_datetime',
            'fieldtype_input_dynamic_mask',
            'fieldtype_input_email',
            'fieldtype_input_encrypted',
            'fieldtype_input_file',
            'fieldtype_input_masked',
            'fieldtype_input_numeric',
            'fieldtype_input_protected',
            'fieldtype_input_url',
            'fieldtype_phone',
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_radioboxes',
            'fieldtype_stages',
            'fieldtype_tags',
            'fieldtype_checkboxes',
            'fieldtype_color',            
        ];

        $choices = [];
        $fields_query = fields::get_query($entity_id, " and type in ('" . implode('\',\'', $allowed_types). "')");
        while($fields = db_fetch_array($fields_query))
        {
            $choices[$fields['id']] = fields::get_name($fields);
        }
        
        return $choices;
    }
    
    static function get_related_entity_choices($entities_id)
    {
        $chlices = [];
        $fields_query = fields::get_query($entities_id, " and type in ('fieldtype_entity','fieldtype_entity_ajax', 'fieldtype_entity_multilevel', 'fieldtype_users', 'fieldtype_users_ajax', 'fieldtype_users_approve', 'fieldtype_created_by', 'fieldtype_parent_item_id')");
        while($fields = db_fetch_array($fields_query))
        {
            if($fields['type']=='fieldtype_parent_item_id' and !entities::has_parent($entities_id))
            {
                continue;
            }
            
            $chlices[$fields['id']] = fields::get_name($fields) . ' (' . entities::get_name_by_id(self::get_related_entity_id($fields)) . ')';
        }    
        
        return $chlices;
    }
          
    static function get_related_entity_id($fields)
    {
        global $app_entities_cache;
        
        $cfg = new settings($fields['configuration']);
        
        switch($fields['type'])
        {
            case 'fieldtype_entity':
            case 'fieldtype_entity_ajax':
            case 'fieldtype_entity_multilevel':   
                $entity_id = $cfg->get('entity_id');
                break;
            case 'fieldtype_parent_item_id':
                $entity_id = $app_entities_cache[$fields['entities_id']]['parent_id']??0;
                break;
            default:
                $entity_id = 1;
                break;
        }
                
        return $entity_id;
    }
    
    static function get_related_field_name($fields)
    {    
        switch($fields['type'])
        {
            case 'fieldtype_parent_item_id':
                $name = 'parent_item_id';
                break;
            case 'fieldtype_created_by':
                $name = 'created_by';
                break;
            default:
                $name = 'field_' . $fields['id'];
                break;
        }
                
        return $name;
    }
        
    function render($field, $obj, $params = array())
    {        
        return '';
    }

    function process($options)
    {
        return db_prepare_input($options['value']);
    }

    function output($options)
    {
        $cfg = new settings($options['field']['configuration']);       
                
        $field = db_find('app_fields', $cfg->get('related_entity'));
        $related_entity_field = db_find('app_fields', $cfg->get('related_entity_field'));
        
        
        if($field and $related_entity_field and $field['id']>0 and $related_entity_field['id']>0)
        {
            return self::get_field_value($options, $field, $related_entity_field) ;
        }
        
        return '';
    }
    
    static function get_field_value($options, $field, $related_entity_field)
    {
        $field_name = self::get_related_field_name($field);
        $field_values = ($options['item'][$field_name]??0);
        
        //echo $field_name . '=' . $field_value;
        
        $entity_id = self::get_related_entity_id($field);
        $field_id = $related_entity_field['id'];
        
        if(!isset_field($entity_id, $field_id) or !strlen($field_values))
        {
            return '';
        }
        
        //echo $field_name . '=' . $field_values . ' ';
        //print_rr($options);
        
        $output_values = [];
        foreach(explode(',', $field_values) as $field_value)
        {        
            $row_query = db_query("select * from app_entity_{$entity_id} where id={$field_value}");
            if($row = db_fetch_array($row_query))
            {
                $output_options = array(                            
                        'class' => $related_entity_field['type'],
                        'value' => $row['field_' . $field_id],
                        'field' => $related_entity_field,
                        'path' => $entity_id . '-' . $field_value,
                        'item' => $row,                        
                    );
                
                $output_options = array_merge($options, $output_options);
                
                $row_value = fields_types::output($output_options);
                
                $output_values[] = $row_value;
            }                        
        }
                        
        return implode('<br>', $output_values);
    }
    
    function reports_query($options)
    {
        global $app_fields_cache;
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');
                
        //print_rr($options);                

        $entity_id = $options['entities_id'];
        $field_id = $filters['fields_id'];   
        
        //echo $entity_id;
                        
        if(($app_fields_cache[$entity_id][$field_id]['type']??'') == 'fieldtype_entity_value')
        {
            $field = $app_fields_cache[$entity_id][$field_id];
            $cfg = new settings($field['configuration']);
            
            $related_field_id = $cfg->get('related_entity');
            $related_field = $app_fields_cache[$entity_id][$related_field_id]??false;
            $related_entity_id = self::get_related_entity_id($related_field);
            $related_entity_field_id = $cfg->get('related_entity_field');
            $related_entity_field = $app_fields_cache[$related_entity_id][$related_entity_field_id]??false;
                        
            if(!$related_entity_field or !strlen($filters['filters_values']))
            {
                return $sql_query;
            }
                                                           
            $filters['type'] = $related_entity_field['type'];
            $filters['fields_id'] = $related_entity_field['id'];

            if(in_array($filters['filters_condition'],['empty_value', 'not_empty_value']))
            {
                $is_empty = $filters['filters_condition']=='empty_value';
                $filters_values_sql =  [fields::db_is_empty_sql($related_entity_field['type'], $related_entity_field_id, $is_empty)];
            }
            elseif (in_array($filters['type'], fields_types::get_types_for_search()) and !in_array($filters['type'],['fieldtype_tags']))
            {
                $filters_values_sql = reports::add_search_qeury($filters, $related_entity_id, []);
            }
            else
            {
                $options = array('class' => $related_entity_field['type'], 'filters' => $filters, 'entities_id' => $related_entity_id, 'sql_query' => [], 'prefix' => 'e');

                $filters_values_sql = fields_types::reports_query($options);
            }

            $filters_values_sql = current($filters_values_sql);

            switch($related_field['type'])
            { 
                case 'fieldtype_parent_item_id':
                    $sql_query[] = $prefix . '.parent_item_id::int' .  ($filters['filters_condition']=='include' ? ' in ': ' not in ') .'(' . $filters_values_sql . ') ';
                    break;
                case 'fieldtype_created_by':
                    $sql_query[] = $prefix . '.created_by::int' .  ($filters['filters_condition']=='include' ? ' in ': ' not in ') .'(' . $filters_values_sql . ') ';
                    break;
                default:                                                
                    $sql = "EXISTS(select cv.id from app_entity_" . $entity_id . "_values as cv where cv.items_id=" . $prefix . ".id and cv.fields_id='" . db_input($related_field['id'])  . "' and cv.value in (select e.id from app_entity_{$related_entity_id} e where {$filters_values_sql}))";
                    break;
            }

            //echo $sql;
            //exit();

            $sql_query[] = $sql;
                                                          
        }

        return $sql_query;
    }

}
