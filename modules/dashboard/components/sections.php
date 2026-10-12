<?php
/**
 * Этот файл является частью программы "CRM Руководитель" - конструктор CRM систем для бизнеса
 * https://www.rukovoditel.net.ru/
 * 
 * CRM Руководитель - это свободное программное обеспечение, 
 * распространяемое на условиях GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * Автор и правообладатель программы: Харчишина Ольга Александровна (RU), Харчишин Сергей Васильевич (RU).
 * Государственная регистрация программы для ЭВМ: 2023664624
 * https://fips.ru/EGD/3b18c104-1db7-4f2d-83fb-2d38e1474ca3
 */

$reports_groups_id  = (isset($_GET['id']) ? _get::int('id') :0);
$sections_query = db_query("select * from app_reports_sections where reports_groups_id='" . db_input($reports_groups_id) . "' " .  ($reports_groups_id==0 ? " and created_by='" . $app_user['id']. "'":'') . " order by sort_order");
while($sections = db_fetch_array($sections_query))
{    
    echo '<div class="row">';
    switch($sections['count_columns'])
    {
        case '1':
            $section_report = $sections['report_left'];
            echo '<div class="col-md-12">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            break;
        
        case '2':
            $section_report = $sections['report_left'];
            echo '<div class="col-md-6">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right'];
            echo '<div class="col-md-6">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            break;
        
        case '3':
            $section_report = $sections['report_left'];
            echo '<div class="col-md-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right'];
            echo '<div class="col-md-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right3'];
            echo '<div class="col-md-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            break;
        
        case '4':
            $section_report = $sections['report_left'];
            echo '<div class="col-md-3">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right'];
            echo '<div class="col-md-3">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right3'];
            echo '<div class="col-md-3">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right4'];
            echo '<div class="col-md-3">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            break;
        
        case '5':
            $section_report = $sections['report_left'];
            echo '<div class="col-md-20 col-sm-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right'];
            echo '<div class="col-md-20 col-sm-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right3'];
            echo '<div class="col-md-20 col-sm-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right4'];
            echo '<div class="col-md-20 col-sm-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right5'];
            echo '<div class="col-md-20 col-sm-4">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            break;
        
        case '6':
            
            $section_report = $sections['report_left'];
            echo '<div class="col-md-2">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right'];
            echo '<div class="col-md-2">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right3'];
            echo '<div class="col-md-2">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right4'];
            echo '<div class="col-md-2">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right5'];
            echo '<div class="col-md-2">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            
            $section_report = $sections['report_right6'];
            echo '<div class="col-md-2">';
                require(component_path('dashboard/sections_reports'));
            echo '</div>';
            break;
    }
    
    echo '</div>';
    
            
    $has_reports_on_dashboard = true;
}