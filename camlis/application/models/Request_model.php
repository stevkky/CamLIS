<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('DataTable');
	}

	public function view_all_requests($data)
	{
		$table = "camlis_patient_sample";
		$primaryKey	= "ID";
		// Permission
        $can_print_psample_result = $this->aauth->is_allowed('print_psample_result');
        $can_edit_psample = $this->aauth->is_allowed('edit_psample');
        // columns
        $columns = array(
            array(
                'db'		=> 'psample.ID',
                'dt'		=> 'psample_id',
                'field'		=> 'ID'
            ),
            array(
                'db'		=> 'patient.patient_code',
                'dt'		=> 'patient_code',
                'field'		=> 'patient_code'
            ),
            array(
                'db'		=> 'patient.patient_name',
                'dt'		=> 'patient_name',
                'field'		=> 'patient_name'
            ),
            array(
                'db'		=> 'psample.sample_number',
                'dt'		=> 'sample_number',
                'field'		=> 'sample_number'
            ),
            array(
                'db'		=> 'psample.collected_time',
                'dt'		=> 'collected_time',
                'field'		=> 'collected_time'
            ),
            array(
                'db'		=> 'psample.received_time',
                'dt'		=> 'received_time',
                'field'		=> 'received_time'
            ),
            array(
                'db'		=> 'psample.verify',
                'dt'		=> 'verify',
                'field'		=> 'verify'
            ),
            array(
                'db'		=> 'sample_source.source_name',
                'dt'		=> 'sample_source',
                'field'		=> 'source_name'
            ),
			array(
                'db'		=> 'psample.micro',
                'dt'		=> 'micro',
                'field'		=> 'micro',
				'formatter'	=> function($d, $row) {
                    return $d >=1 ? "<b class='text-red'>M</b>" : "";
				}
            ),
            array(
                'db'		=> 'psample.collected_date',
                'dt'		=> 'collected_date',
                'field'		=> 'collected_date'
            ),
            array(
                'db'		=> 'psample.received_date',
                'dt'		=> 'received_date',
                'field'		=> 'received_date'
            ),
            array(
                'db'		=> 'psample.progress_status',
                'dt'		=> 'psample_status',
                'field'		=> 'progress_status',
                'formatter'	=> function($d, $row) {
                    $colors	= [PSAMPLE_PENDING => PSAMPLE_PENDING_COLOR, PSAMPLE_PROGRESSING => PSAMPLE_PROGRESSING_COLOR, PSAMPLE_COMPLETE => PSAMPLE_COMPLETE_COLOR, PSAMPLE_PRINTED => PSAMPLE_PRINTED_COLOR, PSAMPLE_REJECTED => PSAMPLE_REJECTED_COLOR, PSAMPLE_REQUESTED => PSAMPLE_REQUESTED_COLOR, PSAMPLE_COLLECTED => PSAMPLE_COLLECTED_COLOR];
                    $format = "<span style='display:none;'>".$d."</span><div style='width:100%; height:20px; border:1px solid #e3e3e3; background:".(isset($colors[$d]) ? $colors[$d] : '')."'></div>";
                    return $format;
                }
            ),
            array(
                'db'		=> 'psample.ID',
                'dt'		=> 'action',
                'field'		=> 'ID',
                'formatter'	=> function($d, $row) use($can_edit_psample, $can_print_psample_result) {
                    $formatted  = "";
                    //$formatted.= "<a href='#' class='text-green hint--left hint--success preview pointer' data-hint='"._t('global.requestform')."'><i class='fa fa-list'></i></a>";
                    // collected date
                    if (empty($row[9]) && $this->aauth->is_allowed('collect_sample')) $formatted .= "&nbsp;<a href='".$this->app_language->site_url("collect/sample/".$d)."' class='text-red hint--left hint--success preview-sample pointer' data-hint='"._t('global.collect')."'><i class='fa fa-flask'></i></a>&nbsp;";
                    if ($this->aauth->is_allowed('edit_request_sample')) $formatted .= "|&nbsp;<a href='".$this->app_language->site_url("request/edit/".$d)."' class='hint--left hint--info pointer' data-hint='"._t('global.edit')."'><i class='fa fa-edit'></i></a>";
                    return $formatted;
                }
            )
        );
		// Join
		$join =" FROM (
						SELECT pmrs_patient.pid AS pid,psample.\"labID\" AS lab_id,pmrs_patient.pid AS patient_id,pmrs_patient.pid AS patient_code,pmrs_patient.name AS patient_name,(CASE pmrs_patient.sex WHEN 'M' THEN 1 WHEN 'F' THEN 2 ELSE 0 END) AS sex,pmrs_patient.dob AS dob,pmrs_patient.phone AS phone,pmrs_patient.province AS province,pmrs_patient.district AS district,pmrs_patient.commune AS commune,pmrs_patient.village AS village
						FROM (camlis_patient_sample psample
						JOIN camlis_pmrs_patient pmrs_patient ON(((psample.patient_id = pmrs_patient.pid) AND (pmrs_patient.status = 1))))
						WHERE (psample.status = 1 and psample.\"labID\"=$this->laboratory_id)
						GROUP BY psample.patient_id,psample.\"labID\" , pmrs_patient.pid 
                        UNION ALL
						SELECT CAST(outside_patient.pid AS VARCHAR) AS pid, outside_patient.lab_id AS lab_id, outside_patient.patient_id AS patient_id,outside_patient.patient_code AS patient_code,outside_patient.patient_name AS patient_name,outside_patient.sex AS sex,outside_patient.dob AS dob,outside_patient.phone AS phone,outside_patient.province AS province,outside_patient.district AS district,outside_patient.commune AS commune,outside_patient.village AS village
						FROM camlis_outside_patient outside_patient
						WHERE (outside_patient.status = 1 and outside_patient.lab_id=$this->laboratory_id)
					) as patient
					INNER JOIN camlis_patient_sample AS psample ON psample.patient_id=patient.pid
					INNER JOIN camlis_lab_sample_source AS sample_source ON psample.sample_source_id = sample_source.\"ID\"";
		// Where 
		$where	= "psample.status = 1 AND psample.progress_status IN (6, 7) AND psample.\"labID\" = $this->laboratory_id";
        // IN(1,6,7) 6=request, 7=collect, 1=pending 
		if (isset($data['reqData']['is_urgent']) && $data['reqData']['is_urgent'] == 1) {
            $where .= " AND psample.is_urgent = 1 AND psample.progress_status IN (1, 2)";
        } else if (isset($data['reqData']['sample_progress']) && $data['reqData']['sample_progress'] > 0) {
            $where .= " AND psample.progress_status = ".$data['reqData']['sample_progress'];
        }
        // Database configuration
        $db_config = $this->load->database('default', TRUE);
        $sql_details = array(
            'user'	=> $db_config->username,
            'pass'	=> $db_config->password,
            'db'	=> $db_config->database,
            'port'	=> $db_config->port,
            'host'	=> $db_config->hostname
        );
        $result = DataTable::simple($data['reqData'], $sql_details, $table, $primaryKey, $columns, $join, $where);
        for ($i=0; $i < count($result['data']); $i++) {
            $result['data'][$i]['number'] = $i + 1;
        }
        return $result;
	}
}