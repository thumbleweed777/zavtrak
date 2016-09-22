<?php
/*
=====================================================
 DataLife Engine Nulled by M.I.D-Team
-----------------------------------------------------
 http://www.mid-team.ws/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: calendar.php
-----------------------------------------------------
 Назначение: AJAX для вывода календаря
=====================================================
*/
@session_start();

error_reporting( 7 );
@ini_set( 'display_errors', true );
@ini_set( 'html_errors', false );

define( 'DATALIFEENGINE', true );
define( 'ROOT_DIR', '../..' );
define( 'ENGINE_DIR', '..' );

include ENGINE_DIR . '/data/config.php';

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( "engine/ajax/calendar.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';

if( $_COOKIE['dle_skin'] ) {
	if( @is_dir( ROOT_DIR . '/templates/' . $_COOKIE['dle_skin'] ) ) {
		$config['skin'] = $_COOKIE['dle_skin'];
	}
}

if( $config["lang_" . $config['skin']] ) {
	
	include_once ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/website.lng';

} else {
	
	include_once ROOT_DIR . '/language/' . $config['langs'] . '/website.lng';

}

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR . '/modules/functions.php';

# Генерируем календарь
function cal($cal_month, $cal_year, $events) {
	global $f, $r, $year, $month, $day, $config, $lang, $langdateshortweekdays;
	
	$next = true;
	
	if( intval( $cal_year . $cal_month ) >= date( 'Ym' ) ) $next = false;
	
	$cal_month = intval( $cal_month );
	$cal_year = intval( $cal_year );
	
	if( $cal_month < 0 ) $cal_month = 1;
	if( $cal_year < 0 ) $cal_year = 2008;
	
	$first_of_month = mktime( 0, 0, 0, intval( $cal_month ), 7, intval( $cal_year ) );
	$maxdays = date( 't', $first_of_month ) + 1; // 28-31
	$prev_of_month = mktime( 0, 0, 0, ($cal_month - 1), 7, $cal_year );
	$next_of_month = mktime( 0, 0, 0, ($cal_month + 1), 7, $cal_year );
	$cal_day = 1;
	$weekday = date( 'w', $first_of_month ); // 0-6
	

	if( $config['allow_alt_url'] == "yes" ) {

		$date_link['prev'] = '<a class="monthlink" onClick="doCalendar(' . date( "'m','Y'", $prev_of_month ) . '); return false;" href="' . $config['http_home_url'] . date( 'Y/m/', $prev_of_month ) . '" title="' . $lang['prev_moth'] . '">&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
		$date_link['next'] = '&nbsp;&nbsp;&nbsp;&nbsp;<a class="monthlink" onClick="doCalendar(' . date( "'m','Y'", $next_of_month ) . '); return false;" href="' . $config['http_home_url'] . date( 'Y/m/', $next_of_month ) . '" title="' . $lang['next_moth'] . '">&raquo;</a>';

	} else {

		$date_link['prev'] = '<a class="monthlink" onClick="doCalendar(' . date( "'m','Y'", $prev_of_month ) . '); return false;" href="' . $PHP_SELF . '?year=' . date( "Y", $prev_of_month ) . '&month=' . date( "m", $prev_of_month ) . '" title="' . $lang['prev_moth'] . '">&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
		$date_link['next'] = '&nbsp;&nbsp;&nbsp;&nbsp;<a class="monthlink" onClick="doCalendar(' . date( "'m','Y'", $next_of_month ) . '); return false;" href="' . $PHP_SELF . '?year=' . date( "Y", $next_of_month ) . '&month=' . date( "m", $next_of_month ) . '" title="' . $lang['next_moth'] . '">&raquo;</a>';

	}
	
	if( ! $next ) $date_link['next'] = "&nbsp;&nbsp;&nbsp;&nbsp;&raquo;";
	
	$buffer = '<table id="calendar" cellpadding="3" class="calendar"><tr><th colspan="7"><center><b>' . $date_link['prev'] . langdate( 'F', $first_of_month ) . ' ' . $cal_year . $date_link['next'] . '</b></center></th></tr><tr>';
	
	$buffer = str_replace( $f, $r, $buffer );
	
	# Дни недели: рабочая неделя
	for($it = 1; $it < 6; $it ++)
		$buffer .= '<th>' . $langdateshortweekdays[$it] . '</th>';
		
	# Дни недели: субботний и воскресный дни
	$buffer .= '<th class="weekday">' . $langdateshortweekdays[6] . '</th>';
	$buffer .= '<th class="weekday">' . $langdateshortweekdays[0] . '</th>';
	
	$buffer .= '</tr><tr>';
	
	if( $weekday > 0 ) {
		$buffer .= '<td colspan="' . $weekday . '">&nbsp;</td>';
	}
	
	while ( $maxdays > $cal_day ) {
		if( $weekday == 7 ) {
			$buffer .= '</tr><tr>';
			$weekday = 0;
		}
		
		# В данный день есть новость
		if( isset( $events[$cal_day] ) ) {
			$date['title'] = langdate( 'd F Y', $events[$cal_day] );
			
			# Если суббота и воскресенье.
			if( $weekday == '5' or $weekday == '6' ) {
				
				# Активный день
				if( $day == $cal_day ) {
					
					$go_page = ($config['ajax']) ? "onclick=\"DlePage('year=" . date( "Y", $events[$cal_day] ) . "&month=" . date( "m", $events[$cal_day] ) . "&day=" . date( "d", $events[$cal_day] ) . "'); return false;\" " : "";
					
					if( $config['allow_alt_url'] == "yes" ) $buffer .= '<td class="weekday-active"><center><a class="weekday-active" ' . $go_page . 'href="' . $config['http_home_url'] . '' . date( "Y/m/d", $events[$cal_day] ) . '/" title="' . $lang['cal_post'] . ' ' . $date['title'] . '"><b>' . $cal_day . '</b></a></center></td>';
					else $buffer .= '<td class="weekday-active"><center><a class="weekday-active" ' . $go_page . 'href="' . $PHP_SELF . '?year=' . date( "Y", $events[$cal_day] ) . '&month=' . date( "m", $events[$cal_day] ) . '&day=' . date( "d", $events[$cal_day] ) . '" title="' . $lang['cal_post'] . ' ' . $date['title'] . '"><b>' . $cal_day . '</b></a></center></td>';
				
				} 

				# Не активный
				else {
					
					$go_page = ($config['ajax']) ? "onclick=\"DlePage('year=" . date( "Y", $events[$cal_day] ) . "&month=" . date( "m", $events[$cal_day] ) . "&day=" . date( "d", $events[$cal_day] ) . "'); return false;\" " : "";
					
					if( $config['allow_alt_url'] == "yes" ) $buffer .= '<td class="day-active"><center><a class="day-active" ' . $go_page . 'href="' . $config['http_home_url'] . '' . date( "Y/m/d", $events[$cal_day] ) . '/" title="' . $lang['cal_post'] . ' ' . $date['title'] . '">' . $cal_day . '</a></center></td>';
					else $buffer .= '<td class="day-active"><center><a class="day-active" ' . $go_page . 'href="' . $PHP_SELF . '?year=' . date( "Y", $events[$cal_day] ) . '&month=' . date( "m", $events[$cal_day] ) . '&day=' . date( "d", $events[$cal_day] ) . '" title="' . $lang['cal_post'] . ' ' . $date[title] . '">' . $cal_day . '</a></center></td>';
				}
			} 

			# Рабочии дни.
			else {
				
				# Активный
				if( $day == $cal_day ) {
					
					$go_page = ($config['ajax']) ? "onclick=\"DlePage('year=" . date( "Y", $events[$cal_day] ) . "&month=" . date( "m", $events[$cal_day] ) . "&day=" . date( "d", $events[$cal_day] ) . "'); return false;\" " : "";
					
					if( $config['allow_alt_url'] == "yes" ) $buffer .= '<td class="weekday-active-v"><center><a class="weekday-active-v" ' . $go_page . 'href="' . $config['http_home_url'] . '' . date( "Y/m/d", $events[$cal_day] ) . '/" title="' . $lang['cal_post'] . ' ' . $date[title] . '"><b>' . $cal_day . '</b></a></center></td>';
					else $buffer .= '<td class="weekday-active-v"><center><a class="weekday-active-v" ' . $go_page . 'href="' . $PHP_SELF . '?year=' . date( "Y", $events[$cal_day] ) . '&month=' . date( "m", $events[$cal_day] ) . '&day=' . date( "d", $events[$cal_day] ) . '" title="' . $lang['cal_post'] . ' ' . $date[title] . '"><b>' . $cal_day . '</b></a></center></td>';
				} 

				# Не активный
				else {
					
					$go_page = ($config['ajax']) ? "onclick=\"DlePage('year=" . date( "Y", $events[$cal_day] ) . "&month=" . date( "m", $events[$cal_day] ) . "&day=" . date( "d", $events[$cal_day] ) . "'); return false;\" " : "";
					
					if( $config['allow_alt_url'] == "yes" ) $buffer .= '<td class="day-active-v"><center><a class="day-active-v" ' . $go_page . 'href="' . $config['http_home_url'] . '' . date( "Y/m/d", $events[$cal_day] ) . '/" title="' . $lang['cal_post'] . ' ' . $date[title] . '">' . $cal_day . '</a></center></td>';
					else $buffer .= '<td class="day-active-v"><center><a class="day-active-v" ' . $go_page . 'href="' . $PHP_SELF . '?year=' . date( "Y", $events[$cal_day] ) . '&month=' . date( "m", $events[$cal_day] ) . '&day=' . date( "d", $events[$cal_day] ) . '" title="' . $lang['cal_post'] . ' ' . $date[title] . '">' . $cal_day . '</a></center></td>';
				}
			}
		} 

		# В данный день новостей нет.
		else {
			
			# Если суббота воскресенье
			if( $weekday == "5" or $weekday == "6" ) {
				$buffer .= '<td class="weekday"><center>' . $cal_day . '</center></td>';
			} 

			# Дни, когда ничего нет
			else {
				$buffer .= '<td class="day"><center>' . $cal_day . '</center></td>';
			}
		}
		
		$cal_day ++;
		$weekday ++;
	}
	
	if( $weekday != 7 ) {
		$buffer .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>';
	}
	
	return $buffer . '</tr></table>';
}

$buffer = false;
$time = time() + ($config['date_adjust'] * 60);
$thisdate = date( "Y-m-d H:i:s", $time );
if( intval( $config['no_date'] ) ) $where_date = " AND date < '" . $thisdate . "'"; else $where_date = "";

$this_month = date( 'm', $time );
$this_year = date( 'Y', $time );

$month = $db->safesql( $_GET['month'] );
$year = intval( $_GET['year'] );

if( $year != '' and $month != '' ) {
	
	if( ($year == $this_year and $month < $this_month) or ($year < $this_year) ) {

		$where_date = "";
		$approve = "";

	} else {
		$approve = " AND approve";
	}
	
	if( ($year == $this_year and $month > $this_month) or ($year > $this_year) ) {
		
		$sql = "";
	
	} else {
		
		$sql = "SELECT DISTINCT DAYOFMONTH(date) as day FROM " . PREFIX . "_post WHERE date >= '{$year}-{$month}-01' AND date < '{$year}-{$month}-01' + INTERVAL 1 MONTH" . $approve . $where_date;
	
	}
	
	$this_month = $month;
	$this_year = $year;

} else {
	
	$sql = "SELECT DISTINCT DAYOFMONTH(date) as day FROM " . PREFIX . "_post WHERE date >= '{$this_year}-{$this_month}-01' AND date < '{$this_year}-{$this_month}-01' + INTERVAL 1 MONTH AND approve" . $where_date;

}

if( $sql != "" ) {
	
	$db->query( $sql );
	
	while ( $row = $db->get_row() ) {
		$events[$row['day']] = strtotime( $this_year . "-" . $this_month . "-" . $row['day'] );
	}
	
	$db->free();
}
$db->close();

$buffer = cal( $this_month, $this_year, $events );

header( "Content-type: text/css; charset=" . $config['charset'] );
echo $buffer;

?>