<?php
defined('BASEPATH') OR die('Access denied!');
if (!function_exists('getAge')) {
	function getAge($dob, $type = 'day') {
		$tmp = date_diff(date_create(date('Y-m-d')), date_create($dob));
		$days = $tmp->format("%a");

		$result = 0;
		switch(strtolower($type)) {
			case 'month' : 
				$result = floor($days/30);
				break;
			case 'year' :
				$result = floor($days/365);
				break;
			default :
				$result = $days;
		}
		
		return $result;
	}
}
function getAging($dob)
{
    $from = new DateTime($dob);
    $to = new DateTime('today');
    return $from->diff($to)->y.'&nbsp;'._t('global.year').'&nbsp;'. $from->diff($to)->m.'&nbsp;'._t('global.month').'&nbsp;'.($from->diff($to)->d+1).'&nbsp;'._t('global.day');

}

if (!function_exists('convertToHierarchy')) {
    function convertToHierarchy($results, $idField='id', $parentIdField='parent', $childrenField='children', $depthField='depth') {
        $hierarchy = array(); // -- Stores the final data
        $itemReferences = array(); // -- temporary array, storing references to all items in a single-dimention
        foreach ( $results as $item ) {
            $item[$depthField] = 0;
            $id         = $item[$idField];
            $parentId   = $item[$parentIdField];
            if (isset($itemReferences[$parentId])) { // parent exists
                $item[$depthField] = $itemReferences[$parentId][$depthField] + 1;
                $itemReferences[$parentId][$childrenField][$id] = $item; // assign item to parent
                $itemReferences[$id] =& $itemReferences[$parentId][$childrenField][$id]; // reference parent's item in single-dimentional array
            } elseif (!$parentId || !isset($hierarchy[$parentId])) { // -- parent Id empty or does not exist. Add it to the root
                $hierarchy[$id] = $item;
                $itemReferences[$id] =& $hierarchy[$id];
            }
        }
        unset($results, $item, $id, $parentId);
        // -- Run through the root one more time. If any child got added before it's parent, fix it.
        foreach ( $hierarchy as $id => &$item ) {
            $parentId = $item[$parentIdField];
            if ( isset($itemReferences[$parentId] ) ) { // -- parent DOES exist
                $item[$depthField] = $itemReferences[$parentId][$depthField] + 1;
                $itemReferences[$parentId][$childrenField][$id] = $item; // -- assign it to the parent's list of children
                unset($hierarchy[$id]); // -- remove it from the root of the hierarchy
            }
        }
        unset($itemReferences, $id, $item, $parentId);
        return $hierarchy;
    }
}

if (!function_exists('array_values_recursive')) {
    function reindexArray($arr, $childField = 'children')
    {
        foreach ($arr as $key => $value)
        {
            if (is_array($value))
            {
                $arr[$key] = reindexArray($value, $childField);
            }
        }

        if (isset($arr[$childField]))
        {
            $arr[$childField] = array_values($arr[$childField]);
        }

        return $arr;
    }
}

if (!function_exists('calculateAge')) {
    function calculateAge($dob, $current_date = NULL, $convertTo = NULL) {
        $current_date = $current_date ? DateTime::createFromFormat('Y-m-d', $current_date) : new DateTime();
        $dob          = DateTime::createFromFormat('Y-m-d', $dob);
        $diff         = $current_date->diff($dob);

        switch ($convertTo) {
            case 'days':
                $diff = (int)$diff->format('%a');
                break;
            case 'months':
                $diff = (int)$diff->format('%m') + ((int)$diff->format('%y') * 12);
                break;
            case 'years':
                $diff = (int)$diff->format('%y');
                break;
        }

        return $diff;
    }
}

/**
 * Language Helper
 */
if (!function_exists('_t')) {
	function _t($key, $param = '') {
		$CI		= & get_instance();
		$value	= $CI->lang->line(strtolower(trim($key)));
		
		if (empty($value) || $value == FALSE) {
			$value = strtolower($key);
		}
		
		return $value;
	}
}

if (!function_exists('isPMRSPatientID')) {
    function isPMRSPatientID($id)
    {
        return preg_match('/^[A-Za-z]{2}\-[0-9]{6}$/', $id) ||
        preg_match('/^[0-9]{3}\-[0-9]{3}\-[0-9]{3}\-[0-9]{1}$/', $id) ||
        preg_match('/^[0-9]{3}\-[0-9]{3}\-[0-9]{3}\-[0-9]{1}\-$/', $id) ? true : false;
    }
}
// added 26 Feb 2021
if (!function_exists('khNumberToLatinNumber')) {
    function khNumberToLatinNumber($kh_number)
    {
        $result     = '';
        $khnum      = array('០','១','២','៣','៤','៥','៦','៧','៨','៩');
        //$latinNum   = array('0','1','2','3','4','5','6','7','8','9');    
        $num_arr = preg_split('/(?<!^)(?!$)/u', $kh_number );  //function for split uft8 character   
        foreach($num_arr as $key => $value){
            if(in_array($value,$khnum)){        
                $value = array_search($value,$khnum); // 
                $result .= $value;
            }
        }
        return $result;
    }
}

if (!function_exists('dd')) {
    function dd($data = null) {
        if(!empty($data))
        {
            var_dump($data);
        }
        else
        {
            var_dump(func_get_args());
        }
        die();
    }
}

if (!function_exists('Jsondd')) {
    function Jsondd($data = null)
    {
        if(!empty($data))
        {
            die(json_encode($data));
        }
        
        die(json_encode(func_get_args()));
    }
}
