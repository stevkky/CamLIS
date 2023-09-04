<?php
defined('BASEPATH') OR die('Access denied!');
if (!function_exists('sample_test_hierarchy')) {
	/**
	 * List Sample Test By Department and Sample
	 * @param $data
	 * @param bool $group_sample
	 * @return array
	 */
	function sample_test_hierarchy($data, $group_sample = TRUE) {
		$sample_tests = array();
		foreach ($data as $row) {
			$row = (object)$row;
			if (!isset($sample_tests[$row->department_id])) {
				$val = array(
					'department_id'   => $row->department_id,
					'department_name' => $row->department_name,
					'entry_by' => isset($row->entry_by)?$row->entry_by:'',
					'entry_date' => isset($row->entry_date)?$row->entry_date:'',
					'modified_by' => isset($row->modified_by)?$row->modified_by:'',
					'modified_date' => isset($row->modified_date)?$row->modified_date:''
				);
				if ($group_sample === TRUE) $val['samples'] = array();
				else $val['tests'] = array();
				$sample_tests[$row->department_id] = (object)$val;
			}
			if ($group_sample === TRUE) {
				if (!isset($sample_tests[$row->department_id]->samples[$row->dep_sample_id])) {
					$sample_tests[$row->department_id]->samples[$row->dep_sample_id] = (object)array(
						'department_sample_id' => $row->dep_sample_id,
						'sample_id'            => $row->sample_id,
                        'sample_name'          => $row->sample_name,
						//'show_weight'          => isset($row->show_weight) && $row->show_weight == 1 ? TRUE : FALSE,
						'show_weight'          => isset($row->show_weight) && $row->show_weight == 't' ? TRUE : FALSE,
						'tests'                => array(),
                        'result_comment'       => isset($row->result_comment)?$row->result_comment:''
					);
				}

				$sample_tests[$row->department_id]->samples[$row->dep_sample_id]->tests[] = $row;
			} else {
				$sample_tests[$row->department_id]->tests[] = $row;
			}
		}
		return $sample_tests;
	}
}

if (!function_exists('sample_test_hierarchy_row')) {
	/**
	 * List Sample Test Header-Child to normal row
	 * @param $data
	 * @param int $parent
	 * @param int $level
	 * @return string
	 */
	function sample_test_hierarchy_row($data, $parent = 0, $level = 0) {
		$formatted = array();

		if (count($data) > 0) {
			foreach ($data as $item) {
				if (isset($item['childs'])) {
					$item['level'] = $level;
					$childs = $item['childs'];
					unset($item['childs']);
					$formatted[] = $item;
					$formatted = array_merge($formatted, sample_test_hierarchy_row($childs, $item['sample_test_id'], $level + 1));
				} else {
					$item['level'] = $level;
					$formatted[] = $item;
				}
			}
		}

		return $formatted;
	}
}

if (!function_exists('sample_test_hierarchy_html')) {
	/**
	 * List Sample Test as HTML
	 * @param $data
	 * @param int $parent
	 * @param int $level
	 * @return string
	 */
	function sample_test_hierarchy_html($data, $parent = 0, $level = 0) {
		$html = "";

		if (count($data) > 0) {
			$html = str_repeat("\t", $level)."<ul class='list-unstyled'>".PHP_EOL;
			foreach ($data as $item) {
				$item = (object)$item;
				if ((int)$item->child_count > 0) {
					$html .= str_repeat("\t\t", $level + 1)."<li is_heading='" . $item->is_heading . "'><label style='cursor:pointer;'><input type='checkbox' class='sample-test' id='st-" . $item->sample_test_id . "' is_heading='" . $item->is_heading . "' parent='" . $parent . "' testID='" . $item->test_id . "' value='" . $item->sample_test_id . "' test-name='" . $item->test_name . "'>&nbsp;&nbsp;<span class='t-name'>" . $item->test_name . "</span></label>";
					$html .= sample_test_hierarchy_html($item->childs, $item->sample_test_id, $level + 1);
					$html .= "</li>".PHP_EOL;
				} else {
					$html .= str_repeat("\t\t", $level + 1)."<li is_heading='" . $item->is_heading . "'><label style='font-weight:100; cursor:pointer;'><input type='checkbox' class='sample-test' name='sample_tests[]' id='st-" . $item->sample_test_id . "' is_heading='" . $item->is_heading . "' parent='" . $parent . "' value='" . $item->sample_test_id . "' test-name='" . $item->test_name . "'>&nbsp;&nbsp;<span class='t-name'>" . $item->test_name . "</span></label></li>".PHP_EOL;
				}
			}
			$html .= str_repeat("\t", $level)."</ul>";
		}

		return $html;
	}
}