<?php
defined('BASEPATH') OR die('Access denied!');
if (!function_exists('preparePsampleDetails')) {
	function preparePsampleDetails($data, $level) {
		$tmp	= array();

		foreach ($data as $row) {
			$row->level	= $level;
			
			if (isset($row->children) && count($row->children) > 0) {
				$childs		= $row->children;
				unset($row->children);
				
				$tmp[]		= $row;

				$t = preparePsampleDetails($childs, $level + 1);
				foreach($t as $ch) {
					$tmp[]			= $ch;
				}
			} else {
				$tmp[]		= $row;
			}
		}

		return $tmp;
	}
}

if (!function_exists('listChildPSampleDetails')) {
	function listChildPSampleDetails($data, $parent, $level) {
		$testIDs	= array();
		$new_data	= array();
		
		foreach ($data as $row) {
			$testIDs[]	= $row->testID;
		}
		
		foreach ($data as $key => $row) {
			if (!in_array($row->testPID, $testIDs)) $row->testPID = 0;
			if ($row->testPID == $parent) {
				$child	= listChildPSampleDetails($data, $row->testID, $level + 1);
				if ($child) {
					$row->children	= $child;
				}
				
				$row->level	= $level;
				$new_data[]	= $row;
			}
		}
		
		$tmp	= [];
		foreach($new_data as $row) {
			$tmp[] = $row;
		}
		return $tmp;
	}
}