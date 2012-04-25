<?php

function smarty_function_gtime($params, $template)
{

	$format=$params['format'];
	$timestamp=$params['timestamp'];
	$convert = 1;
	if(!empty($params['timestamp'])){
	

	$now=time();
	$lang =array('前','天','昨天','前天','小时','半','分钟','秒','刚才');
	$s = gmdate($format, $timestamp);
	if($convert) {
		
		if(!isset($GLOBALS['todaytimestamp'])) {
			$GLOBALS['todaytimestamp'] = $now - $now % 86400 ;
		}

		$time = $now - $timestamp;
		if($timestamp >= $GLOBALS['todaytimestamp']) {
			if($time > 3600) {
				return '<span class="time" title="'.$s.'">'.intval($time / 3600).'&nbsp;'.$lang[4].$lang[0].'</span>';
			} elseif($time > 1800) {
				return '<span class="time" title="'.$s.'">'.$lang[5].$lang[4].$lang[0].'</span>';
			} elseif($time > 60) {
				return '<span class="time" title="'.$s.'">'.intval($time / 60).'&nbsp;'.$lang[6].$lang[0].'</span>';
			} elseif($time > 0) {
				return '<span class="time" title="'.$s.'">'.$time.'&nbsp;'.$lang[7].$lang[0].'</span>';
			} elseif($time == 0) {
				return '<span class="time" class="time"title="'.$s.'">'.$lang[8].'</span>';
			} else {
				return $s;
			}
		} elseif(($days = intval(($GLOBALS['todaytimestamp'] - $timestamp) / 86400)) >= 0 && $days < 7) {
			if($days == 0) {
				return '<span class="time" title="'.$s.'">'.$lang[2].'&nbsp;'.gmdate('H:i', $timestamp).'</span>';
			} elseif($days == 1) {
				return '<span class="time" title="'.$s.'">'.$lang[3].'&nbsp;'.gmdate('H:i', $timestamp).'</span>';
			} else {
				return '<span class="time" title="'.$s.'">'.($days + 1).'&nbsp;'.$lang[1].$lang[0].'&nbsp;'.gmdate('H:i', $timestamp).'</span>';
			}
		} else {
			return $s;
		}
	} else {
		return $s;
	}
	
	}
	

}
