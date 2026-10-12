<?php

class widgets
{
    static function render(int $widget_id, $is_preview = false)
    {
        global $app_user;
        
        $where_sql = $app_user['group_id']>0 ? "and (find_in_set({$app_user['group_id']},users_groups))" : "";                
        
        if(!$is_preview)
        {
            $where_sql .= " and is_active=1 ";
        }
        
        $widget_query = db_query("select * from app_widgets where id={$widget_id} {$where_sql}", false);
        if($widget = db_fetch($widget_query))
        {
            $html = '';
            switch($widget->type)
            {
                case 'single_counter':
                    $html = self::render_single_counter($widget);
                    break;
                case 'summary':
                    $html = self::render_summary($widget);
                    break;
            }
            
            return $html;
        }
        
        return '';
    }
    
    static function render_summary($widget)
    {
        $bg_color  = 'style="' . (strlen($widget->bg_color) ? ' background-color: ' . $widget->bg_color . ';' : '')  . (strlen($widget->text_color) ? 'color: ' . $widget->text_color .';' : ''). '"';
                
        $content = '';
        
        $content .= strlen($widget->title) ? '<div class="widget-title">' . $widget->title . '</div>' : '';
        $content .= strlen($widget->description) ? '<div class="widget-description">' . $widget->description . '</div>' : '';
                
        //print_rr(self::get_calc_values($widget->id));
        
        $values = self::get_calc_values($widget->id);
        $content_values = strlen($widget->subtitle) ? '<div class="widget-subtitle">' . $widget->subtitle . '</div>' : '';
        $content_values .= '<table class="widget-values">';
        foreach($values as $value)
        {
            if($value->is_hidden)
            {
                continue;
            }
            
            $content_values .= '
                <tr>
                    <td>' . (strlen($value->color) ? '<div class="widget-value-color">' . app_render_icon('fa-square','',$value->color)  . '</div>': '') . '</td>
                    <td class="widget-value-name">' . $value->name . '</td>
                    <td class="widget-value-text">' . $value->text . '</td>
                </tr>
                ';
        }
        $content_values .= '</table>';
        
        $chart_content = self::get_chart_content($widget, $values);
        
        if($widget->display_chart==1)
        {
            $content .= '
                <div class="row">
                    <div class="col-md-6">' . $content_values . '</div>
                    <div class="col-md-6">' . $chart_content . '</div>
                </div>
                ';
        }
        elseif($widget->display_chart=='chart_only')
        {
            $content .= $chart_content;
        }
        else
        {
            $content .= $content_values;
        }
                        
        return '
            <div class="widget widget-' . $widget->id . ' widget-summary" ' . $bg_color  . '>
                ' . $content . '
            </div>
            ';
    }
    
    static function render_single_counter($widget)
    {
        $values = self::get_calc_values($widget->id);
        $value = count($values) ? end($values)->text : 0;
        
        $bg_color  = strlen($widget->bg_color) ? 'style="background-color: ' . $widget->bg_color . '"' : '';
        $text_color = strlen($widget->text_color) ? 'style="color: ' . $widget->text_color . '"' : '';
        
        $heading = $widget->name;
                        
        return '
            <div class="widget widget-' . $widget->id . ' widget-single-counter" ' . $bg_color . '>
                <div class="single-count" '  . $text_color . '>' . $value . '</div>
                <div class="single-heading" ' . $text_color . '>' . $heading . '</div>
            </div>
            ';
    }
    
    static function get_chart_content($widget, $values)
    {
        if($widget->display_chart==0)
        {
            return '';
        }
        
        $chart_id = $widget->id . '_' . rand(100,300);
        
        $series = [];
        $categories = [];
        $data_values = [];
        $data_css_colors = [];        
        $row = 0;
        foreach($values as $value)
        {
            if($value->is_hidden)
            {
                continue;
            }
            
            $categories[] = "'" . addslashes($value->name) . "'";
            
            $data_values[] =  '{y: ' . $value->value . ', name: "' . addslashes($value->name). '", color: "#00ff00"}';
            
            if(strlen($value->color))
            {
                $data_css_colors[] = '
                    #widget_chart_' .  $chart_id . ' .highcharts-color-' . $row . ' {
                        fill: ' . $value->color . ';
                        stroke: ' . $value->color . ';
                    }
                    ';
            }
            
            $row++;                                      
        }
        
        $series[] = '{                      
                name: "",        
                data: [' . implode(',', $data_values) . '],                
              }'; 
        
        
        
        
        
$html = "
    <div id=\"widget_chart_{$chart_id}\" style=\"height: " . ($widget->chart_height>0 ? $widget->chart_height : 300). "px;\"></div>
<script>  
$(function () {
    Highcharts.chart('widget_chart_{$chart_id}', {
        chart: {
            type: '" . $widget->chart_type . "',
            styledMode: true, 
            
        },
        title: {
            text: null
        },
        legend:{ enabled:false },

        xAxis: {
                categories: [" .  implode(',',$categories) . "],
                labels: {
                    rotation: " . ($widget->chart_type=='bar' ? 0 : -90) . "                     
                },                
            },
        yAxis: {
                title: {
                    text: ''
                },                
                labels: {
                    formatter: function () {
                        return this.axis.defaultLabelFormatter.call(this);
                    },
                    
                }
            },
        tooltip: {
         
                formatter: function () {
                    //console.log(this.point)
                    if(this.x)
                    {
                        return '<span style=\"font-size: 10px;\">' + this.x + '</span>: ' + this.point.y + (this.point.percentage ? ' '+Math.round(this.point.percentage)+'%' : '');
                    }
                    else
                    {
                        return '<b>' + this.point.name + ': </b>' + this.point.y;
                    }
                }
            },
            
        " . self::get_cahrt_plot_options($widget)  . "
                    
        series: [
            " . implode(',', $series) . "
        ]
    });
})
</script>        
    ";   

//bg color
if(strlen($widget->bg_color))
{
$html .= '
<style>    
    #widget_chart_' . $chart_id . ' .highcharts-background {
        fill: ' . $widget->bg_color . ';
    }    
</style>    
    ';
}

if(count($data_css_colors))
{
    echo '<style>' . implode('', $data_css_colors). '</style>';
}              
        return $html;
        
    }
    
    static function get_cahrt_plot_options($widget)
    {
        $html = '';
        
        if($widget->chart_type=='stacked_column')
        {
            $html = "
                plotOptions: {
                    column: {
                      stacking: 'normal'
                    }
                  },
                ";
            
        }
        elseif($widget->chart_type=='stacked_percent')
        {
            $html = "
                plotOptions: {
                    column: {
                      stacking: 'percent'
                    }
                  },
                ";
        }     
        elseif($widget->chart_type=='stacked_area')
        {
            $html = "
                plotOptions: {
                    area: {
                        stacking: 'normal',
                        lineColor: '#666666',
                        lineWidth: 1,
                        marker: {
                            lineWidth: 1,
                            lineColor: '#666666'
                        }
                    }
                },
                ";
            
        }
        else
        {
            $hide_labels = $widget->is_hide_labels==1 ? 0 : 1;
            $html = "
                plotOptions: {
                    column: {
                      colorByPoint: true                      
                    },
                    bar: {
                      colorByPoint: true,
                      dataLabels: {
                            enabled: true,                            
                            align: 'left',                            
                            formatter: function () {
                                return Number.isInteger(this.y) ? this.y : Highcharts.numberFormat(this.y,2);
                            }                        
                        }
                    },
                    line: {
                      colorByPoint: true
                    },
                    spline: {
                      colorByPoint: true
                    },
                    area: {
                      colorByPoint: true
                    },
                    pie:{
                        dataLabels: {
                            enabled: {$hide_labels},
                        }
                    },
                    pyramid:{
                        dataLabels: {
                            enabled: {$hide_labels},
                        }
                    },
                    funnel:{
                        dataLabels: {
                            enabled: {$hide_labels},
                        }
                    }
                    
                  },
                ";
        }
        
        
        return $html;
    }
                
    static function get_calc_values(int $widget_id)
    {
        $values = [];
        
        //get sql values
        $rows_query = db_query("select * from app_widgets_calc where widget_id={$widget_id}  and type!='PHP' order by sort_order, name");
        while($row = db_fetch($rows_query))
        {
            $fiters_reports_id = reports::auto_create_report_by_type($row->entities_id,'widget_calc' . $row->id);
                        
            if($row->type=='COUNT')
            {
                $listing_sql_query = reports::add_filters_query($fiters_reports_id);
                
                $count_query = db_query("select count(*) as total from app_entity_{$row->entities_id} e where e.id>0 " . $listing_sql_query);
                $count = db_fetch($count_query);
                $value = $count->total??0;
            }
            elseif(in_array($row->type, ['SUM','MIN','MAX']))
            {
                $listing_sql_query = reports::add_filters_query($fiters_reports_id, '');
                
                $calc_query = db_query("select {$row->type}(" . functions::prepare_formula_in_function_query($row->entities_id, $row->formula, 'e') . "+0) as total from app_entity_{$row->entities_id} e where e.id>0 " . $listing_sql_query, false);                
                $calc = db_fetch($calc_query);
                $value = $calc->total??0;                
            }
            
            $values[$row->id] = $value;
        }
        
        //print_rr($values);
        
        $rows_query = db_query("select * from app_widgets_calc where widget_id={$widget_id}  and type='PHP' order by sort_order, name");
        while($row = db_fetch($rows_query))
        {
            $formula  = $row->formula;
            
            foreach($values as $k=>$v)
            {
                $formula = str_replace('${' . $k . '}', $v, $formula);
            }
            
            $php_code = '
                $calc = function()
                {                
                    ' . $formula . '
                };                               
                ';                        
            
            try
            {                        
                eval($php_code);
                $values[$row->id] = $calc();
            }
            catch (Error $e)
            {
                echo alert_error(TEXT_ERROR . ' ' . TEXT_CALCULATIONS . ' "' . $row->name . ' #' . $row->id . '": ' . $e->getMessage() . ' on line ' . $e->getLine());
            }                                    
        }
        
        //print_rr($values);
        
        $calcs = [];
        
        $rows_query = db_query("select * from app_widgets_calc where widget_id={$widget_id} order by sort_order, name");
        while($row = db_fetch($rows_query))
        {
            $number_format = strlen($row->number_format) ? explode('/',$row->number_format) : [];
        
            $nf = (object)[
                'decimals' => $number_format[0]??0,
                'decimal_separator' => $number_format[1]??'.',
                'thousands_separator' => $number_format[2]??' ',                
            ];
            
            $value = $values[$row->id]??0;
            $calcs[$row->id] = (object)[
                'name' => $row->name,
                'value' => number_format($value, $nf->decimals, '.', ''),
                'color' => $row->color,
                'is_hidden'=> $row->is_hidden,
                'text' => $row->number_prefix . number_format($value, $nf->decimals, $nf->decimal_separator, $nf->thousands_separator ) . $row->number_suffix
            ];                                    
        }
        
        //print_rr($calcs);
        
        return $calcs;
    }
    
    
    static function get_types_choices()
    {
        $choices = array(
            'single_counter' => TEXT_SINGLE_COUNTER,
            'summary' => TEXT_SUMMARY,            
        );

        return $choices;
    }

    static function get_type_by_name($name)
    {
        $types = self::get_types_choices();

        return (isset($types[$name]) ? $types[$name] : '');
    }
    
    static function get_group_choices()
    {
        $choices = [];
        $rows_query = db_query("select * from app_widgets_groups order by sort_order, name");        
        while($row = db_fetch($rows_query))
        {
            $choices[$row->id] = $row->name; 
        }
        
        return $choices;
    }
    
    static function get_calc_types_choices()
    {
        $choices = array(
            'COUNT' => TEXT_FUNCTION_COUNT,
            'SUM' => TEXT_FUNCTION_SUM, 
            'MIN' => TEXT_FUNCTION_MIN,
            'MAX' => TEXT_FUNCTION_MAX,
            'PHP' => TEXT_PHP_CODE,
        );
        
        return $choices;
    }
    
    static function get_calcs(int $widget_id)
    {
        $calcs = [];
        $rows_query = db_query("select * from app_widgets_calc where widget_id={$widget_id} order by sort_order, name");
        while($row = db_fetch($rows_query))
        {
            $calcs[$row->id] = $row->name;
        }
        
        return $calcs;
    }
}
