<?php

$widget_calc = explode('_', str_replace('widget_calc','',$app_redirect_to));
$widget_id = $widget_calc[0];
$widget_calc_id = $widget_calc[1];

$widget_info = db_find('app_widgets', $widget_id);
$widget_calc_info = db_find('app_widgets_calc', $widget_calc_id);

$breadcrumb = array();

$breadcrumb[] = '<li>' . TEXT_WIDGETS . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to($widget_info['name'], url_for('widgets/calc', 'widget_id=' . $widget_id)) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $widget_calc_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

