<?php
defined('BASEPATH') OR die('Access denied!');
class Report extends MY_Controller {
    public function __construct() {
        parent::__construct();

        $this->app_language->load(array('sample','report'));
        $this->load->model(array('report_model' => 'rModel'));

        $this->template->plugins->add(['DataTable','MomentJS','BootstrapDateTimePicker']);
        $this->template->stylesheet->add('assets/camlis/css/camlis_admin_style.css');
        $this->template->stylesheet->add('assets/camlis/css/camlis_sample_style.css');
        $this->template->content_title = _t('report.report');


    }
    public function index() {
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => '']);
        $this->template->content->view('template/pages/report/index', $this->data);
        $this->template->publish();
    }


    /* lookup data sample code
     *
     */
    public function lookup_patient_id(){
        $obj		= new stdClass();
        $obj->val = $this->input->post('filter_val');
        $rows   = $this->rModel->lookup_patient_id($obj);


        $json_array = array();
        foreach ($rows as $row){
            array_push($json_array, $row->pid.' - '.$row->sample_number);
        }
 
        echo json_encode($json_array);

    }

    /* lookup data patient name
     *
     */
    public function lookup_patient_name(){

        $obj		= new stdClass();
        $obj->val = $this->input->post('filter_val');
        $rows   = $this->rModel->lookup_patient_name($obj);

        $json_array = array();
        foreach ($rows as $row){
            array_push($json_array, $row->patient_name.' - '.$row->pid.' - '.$row->sample_number);
        }
 
        echo json_encode($json_array);

    }

    /* lookup data sample code
     *
     */
    public function lookup_sample_code(){
        $obj		= new stdClass();
        $obj->val = $this->input->post('filter_val');
        $rows   = $this->rModel->lookup_sample_code($obj);


        $json_array = array();
        foreach ($rows as $row){
            array_push($json_array, $row->sample_number);
        }
 
        echo json_encode($json_array);

    }
    /* lookup data lookup labo_number
     *
     */
    public function lookup_labo_number(){
        $obj		= new stdClass();
        $obj->val = $this->input->post('filter_val');
        $rows   = $this->rModel->lookup_labo_number($obj);


        $json_array = array();
        foreach ($rows as $row){
            array_push($json_array, $row->sample_number);
        }
 
        echo json_encode($json_array);

    }

    public function aggregated() {
        $this->aauth->control('generate_aggregated_report');
        $this->template->plugins->add(['DataTableFileExport','Progress', 'BootstrapTimePicker']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'aggregated']);
        $this->template->content->view('template/pages/report/aggregated', $this->data);
        $this->template->publish();
    }

    public function individual() {
        $this->aauth->control('generate_individual_report');
        $this->template->plugins->add(['AutoComplete', 'AsyncJS', 'AutoNumeric']);
        $this->template->javascript->add('assets/camlis/js/report/individual_report.js');
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'individual']);
        $this->template->modal->view('template/modal/modal_patient_sample_preview_result');
        $this->template->content->view('template/pages/report/individual', $this->data);
        $this->template->publish();
    }

    public function bacteriology() {
        $this->aauth->control('generate_bacteriology_report');
        $this->data['labo_type']   = $this->rModel->load_labo();
        $this->data['department']   = $this->rModel->load_dept();
        $this->data['sample']   = $this->rModel->load_sample();
        $this->data['test']   = $this->rModel->load_test();

        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'bacteriology']);

        $this->template->stylesheet->add('assets/plugins/autocomplete/jquery-ui.css');
        $this->template->javascript->add('assets/plugins/autocomplete/jquery-ui.js');
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');

        $this->template->modal->view('template/modal/modal_bacteriology_preview_result');
        $this->template->content->view('template/pages/report/bacteriology', $this->data);
        $this->template->publish();
    }

    public function ward() {
        $this->aauth->control('generate_ward_report');
        $this->template->plugins->add(['BootstrapTimePicker']);
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'ward']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');
        $this->template->content->view('template/pages/report/ward', $this->data);
        $this->template->publish();
    }

    // format date time Y-m-d
    function format_date($date){
        $arr = explode('/',$date);
        return $arr[2].'-'.$arr[1].'-'.$arr[0];

    }

    /**
     * View All view_all
     */
    public function aggregate_list() {
        $obj		= new stdClass();
        $obj->start	= $this->format_date($this->input->post('start'));
        $obj->end	= $this->format_date($this->input->post('end'));
        $obj->start_time = $this->input->post('start_time');
        $obj->end_time   = $this->input->post('end_time');

        $result_list = $this->rModel->aggregate_table($obj);


        $grade_name = '';
        $table_body ='';

        foreach($result_list as $row){
            if($grade_name != $row["Title"] && $row["Title"]!=''){
                $table_body.= '<tr style=" height: 30px;"> 
                                    <td style="font-weight:bold">&nbsp;'.$row["Title"].'</td>  
                                    <td>'._t('global.male').'</th>
                                    <td>'._t('global.female').'</th>
                                    <td>'._t('report.total').'</th>
                                </tr>';
            }

            if($row["distribute"]=="Sub total"){
                $css = 'style="text-align:right; "';
            }else if($row["distribute"]=="Grand total"){
                $css = 'style="text-align:right; "';
            }else{
                $css = "";
            }


             $table_body.='<tr >  
                            <td '.$css.'>&nbsp;'.$row["distribute"].'</td> 
                            <td>&nbsp;'.($row["male"]==''?'':$row["male"]).'</td>
                            <td>&nbsp;'.($row["female"]==''?'':$row["female"]).'</td>
                            <td>&nbsp;'.($row["total"]==''?'':$row["total"]).'</td>   
                        </tr>';


            $grade_name = $row["Title"];

        }

        echo  $table_body;

    }

    /**
     * View All award_list
     */
    public function ward_list() {
        $obj		= new stdClass();
        $obj->start	= $this->format_date($this->input->post('start'));
        $obj->end	= $this->format_date($this->input->post('end'));
        $obj->start_time = $this->input->post('start_time');
        $obj->end_time   = $this->input->post('end_time');

        $result_list = $this->rModel->ward_table($obj);


        $grade_name = '';
        $table_body ='';

        if($result_list){
            foreach($result_list as $row){
                if($grade_name != $row["Title"] && $row["Title"]!=''){
                    $table_body.= '<tr style=" height: 30px;"> 
                                        <td style="font-weight:bold">&nbsp;'.$row["Title"].'</td>  
                                        <td>'._t('global.male').'</th>
                                        <td>'._t('global.female').'</th>
                                        <td>'._t('report.total').'</th>
                                    </tr>';
                }

                if($row["distribute"]=="Sub total"){
                    $css = 'style="text-align:right; "';
                }else if($row["distribute"]=="Grand total"){
                    $css = 'style="text-align:right; "';
                }else{
                    $css = "";
                }

                 $table_body.='<tr >  
                                <td '.$css.'>&nbsp;'.$row["distribute"].'</td> 
                                <td>&nbsp;'.($row["male"]==''?'':$row["male"]).'</td>
                                <td>&nbsp;'.($row["female"]==''?'':$row["female"]).'</td>
                                <td>&nbsp;'.($row["total"]==''?'':$row["total"]).'</td>   
                            </tr>';

                $grade_name = $row["Title"];

            }
        }else{
            $table_body = "<tr><td align='center'>No data</td></tr>";
        }

        echo  $table_body;
    }

    /**
     * Print result Patient's sample
     */
    public function preview_pbacteriology_result($type = 'preview') {

        $obj		= new stdClass();
        $con = '';
        //
        if($this->input->post('_labo_type')!=''){
            $labid = $this->rModel->normalize_critial($this->input->post('_labo_type'));
			if($labid=='0'){
                $con .= '';
            }else{
                $con .= 'and stv."labID" in(' . $labid . ')';
            }
        } else {
			$con .= 'and stv."labID"='.$this->session->userdata('laboratory')->labID;
		}
        //
        if($this->input->post('_department')!=''){
            $dept 	= $this->rModel->normalize_critial($this->input->post('_department'));
            $con .= 'and stv.department_id in(' . $dept . ')';
        }
        //
        if($this->input->post('_sample_type')!=''){
            $sample_id 	= $this->rModel->normalize_critial($this->input->post('_sample_type'));
            if ($sample_id=='0'){
				$con.='';
			}else{
				$con.= 'and ds.sample_id in(' . $sample_id . ')';
			}
        }
        //
        if($this->input->post('_testing')!=''){
            $testing_id= $this->rModel->normalize_critial($this->input->post('_testing'));
            $con .= 'and stv.test_id in('.$testing_id.')';
        }
        //
        if($this->input->post('_result')!=''){
            $result_id 	= $this->rModel->normalize_critial($this->input->post('_result'));
            $con .= 'and pr.result_id in(' . $result_id . ')';
        }
        //
		if($this->input->get('_labo_number')!=''){
			 $obj->labo_number = $this->input->get('_labo_number');
			 $conExp .= "and stv.sample_number = '".$obj->labo_number."'";
		}
        if($this->input->post('_start')!=''){
            $_start 	= date('Y-m-d',strtotime(str_replace('/', '-',$this->input->post('_start'))));
            $con .= 'and date_format(stv.test_date,"%Y-%m-%d")>="'.$_start.'"';
        }
        if($this->input->post('_end')!=''){
            $_end 	= date('Y-m-d',strtotime(str_replace('/', '-',$this->input->post('_end'))));
            $con .= 'and date_format(stv.test_date,"%Y-%m-%d")<="'.$_end.'"';
        }
        
        // get result list array
        $result_list   = $this->rModel->load_result($obj,$con);

        // export to excel
         if($type=='print'){
             $this->load->library('phptoexcel');
            // file name
            $obj->filename = "Bacteriology Report";

            $this->excel = PHPExcel_IOFactory::createReader('Excel2007');
            $this->excel = $this->excel->load('./assets/report/export_report.xlsx');
            //activate worksheet number 1
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle($obj->filename);

            // looping
            $r=1;
            $i=5;

              $conExp = '';
             if($this->input->get('_labo_type')!=''){
                 $data = $this->input->get('_labo_type');
                 $labid = $this->rModel->normalize_critial($data);
                 if($labid=='0'){
                     $conExp .= '';
                 }else{
                     $conExp .= 'and stv."labID" in(' . $labid . ')';
                 }
             }
             //
             if($this->input->get('_department')!=''){
                 $data = $this->input->get('_department');
                 $dept 	= $this->rModel->normalize_critial($data);
                 $conExp .= 'and stv.department_id in(' . $dept . ')';
             }
             //
             if($this->input->get('_sample_type')!=''){
                 $data = $this->input->get('_sample_type');
                 $sample_id 	= $this->rModel->normalize_critial($data);
                 if($sample_id=='0'){
                     $conExp .= '';
                 }else {
                     $conExp .= 'and ds.sample_id in(' . $sample_id . ')';
                 }
             }
             //
             if($this->input->get('_testing')!=''){
                 $data = $this->input->get('_testing');
                 $testing_id 	= $this->rModel->normalize_critial($data);
                 $conExp .= 'and stv.test_id in('.$testing_id.')';
             }
             //
             if($this->input->get('_result')!=''){
                 $data = $this->input->get('_result');
                 $result_id 	= $this->rModel->normalize_critial($data);
                 $conExp .= 'and pr.result_id in(' . $result_id . ')';
             }
             //
             if($this->input->get('_labo_number')!=''){
                 $obj->labo_number = $this->input->get('_labo_number');
                 $conExp .= "and stv.sample_number = '".$obj->labo_number."'";
             }
             if($this->input->get('_start')!=''){
                 $date=$this->input->get('_start');
                 $_start 	= date('Y-m-d',strtotime(str_replace('/', '-',$date)));
                 $conExp .= 'and date_format(stv.received_date,"%Y-%m-%d")>="'.$_start.'"';
             }
             if($this->input->get('_end')!=''){
                 $date=$this->input->get('_end');
                 $_end 	= date('Y-m-d',strtotime(str_replace('/', '-',$date)));
                 $conExp .= 'and date_format(stv.received_date,"%Y-%m-%d")<="'.$_end.'"';
             }
             // get result list array
             $export_list   = $this->rModel->load_result($obj,$conExp);
			 
			foreach($export_list as $row){
                $this->excel->getActiveSheet()->setCellValue('A'.$i, $r); // no
                $this->excel->getActiveSheet()->setCellValue('B'.$i, $row["lab_code"]); // name_en
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $row["patient_id"]); // patient_id
                $this->excel->getActiveSheet()->setCellValue('D'.$i, $row["sample_number"]);  //  sample_number
                $this->excel->getActiveSheet()->setCellValue('E'.$i, $row["sex"]); // sex
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $row["dob"]); // dob
                $this->excel->getActiveSheet()->setCellValue('G'.$i, $row["patient_age"]); // age
                $this->excel->getActiveSheet()->setCellValue('H'.$i, $row["sample_name"]); // sample_name
                $this->excel->getActiveSheet()->setCellValue('I'.$i, $row["description"]); // description
                $this->excel->getActiveSheet()->setCellValue('J'.$i, $row["sample_volume1"]); // First Blood culture bottle 
                $this->excel->getActiveSheet()->setCellValue('K'.$i, $row["sample_volume2"]); // Second Blood culture bottle 
                $this->excel->getActiveSheet()->setCellValue('L'.$i, $row["collected_date"]); // collected_date
                $this->excel->getActiveSheet()->setCellValue('M'.$i, $row["test_date"]); // test_date
				$this->excel->getActiveSheet()->setCellValue('N'.$i, $row["diagnosis"]); // Diagnosis
                $this->excel->getActiveSheet()->setCellValue('O'.$i, $row["results"]); // results
//

                $this->excel->getActiveSheet()->setCellValue('P'.$i, $row["Amoxi_Clav"]); // Amoxi_Clav
                $this->excel->getActiveSheet()->setCellValue('Q'.$i, $row["Ceftriaxone"]); // Ceftriaxone
                $this->excel->getActiveSheet()->setCellValue('R'.$i, $row["Cephalothin"]); // Cephalothin
                $this->excel->getActiveSheet()->setCellValue('S'.$i, $row["Chloramphenicol"]); // Chloramphenicol
                $this->excel->getActiveSheet()->setCellValue('T'.$i, $row["Clindamycin"]); // Clindamycin
                $this->excel->getActiveSheet()->setCellValue('U'.$i, $row["Cloxacillin"]); // Cloxacillin
                $this->excel->getActiveSheet()->setCellValue('V'.$i, $row["Erythromycin"]); // Erythromycin
                $this->excel->getActiveSheet()->setCellValue('W'.$i, $row["Nitrofurantoin"]); // Nitrofurantoin
                $this->excel->getActiveSheet()->setCellValue('X'.$i, $row["Norfloxacin"]); // Norfloxacin
                $this->excel->getActiveSheet()->setCellValue('Y'.$i, $row["Oxacillin"]); // Oxacillin
                $this->excel->getActiveSheet()->setCellValue('Z'.$i, $row["Penicillin"]); // Penicillin
                $this->excel->getActiveSheet()->setCellValue('AA'.$i, $row["Tetracycline"]); // Tetracycline
                $this->excel->getActiveSheet()->setCellValue('AB'.$i, $row["Trimeth_Sulfa"]); // Trimeth_Sulfa
                $this->excel->getActiveSheet()->setCellValue('AC'.$i, $row["Vancomycin"]); // Vancomycin
                $this->excel->getActiveSheet()->setCellValue('AD'.$i, $row["Cefoxitin"]); // Cefoxitin
                //
                $this->excel->getActiveSheet()->setCellValue('AE'.$i, $row["Ampicillin"]); // Ampicillin
                $this->excel->getActiveSheet()->setCellValue('AF'.$i, $row["Amikacin"]); // Amikacin
                $this->excel->getActiveSheet()->setCellValue('AG'.$i, $row["Azithromycin"]); // Azithromycin
                $this->excel->getActiveSheet()->setCellValue('AH'.$i, $row["Cefazolin"]); // Cefazolin
                $this->excel->getActiveSheet()->setCellValue('AI'.$i, $row["Cefepime"]); // Cefepime
                $this->excel->getActiveSheet()->setCellValue('AJ'.$i, $row["Ceftazidime"]); // Ceftazidime
                $this->excel->getActiveSheet()->setCellValue('AK'.$i, $row["Ceftriaxone_30_GNB"]); // Ceftriaxone_30_GNB
                $this->excel->getActiveSheet()->setCellValue('AL'.$i, $row["Chloramphenicol_30"]); // Chloramphenicol_30
                $this->excel->getActiveSheet()->setCellValue('AM'.$i, $row["Ciprofloxacin"]); // Ciprofloxacin
                $this->excel->getActiveSheet()->setCellValue('AN'.$i, $row["Fosfomycin"]); // Fosfomycin
                $this->excel->getActiveSheet()->setCellValue('AO'.$i, $row["Gentamicin"]); // Gentamicin
                $this->excel->getActiveSheet()->setCellValue('AP'.$i, $row["Imipenem"]); // Imipenem
                $this->excel->getActiveSheet()->setCellValue('AQ'.$i, $row["Levofloxacin"]); // Imipenem
                $this->excel->getActiveSheet()->setCellValue('AR'.$i, $row["Meropenem"]); // Meropenem
                $this->excel->getActiveSheet()->setCellValue('AS'.$i, $row["Minocycline"]); // Minocycline
                $this->excel->getActiveSheet()->setCellValue('AT'.$i, $row["Nalidixic_acid"]); // Nalidixic_acid
                $this->excel->getActiveSheet()->setCellValue('AU'.$i, $row["Norfloxacin_10_GNB"]); // Norfloxacin_10_GNB
                $this->excel->getActiveSheet()->setCellValue('AV'.$i, $row["Trimeth_Sulfa_1_25"]); // Trimeth_Sulfa_1_25

                $this->excel->getActiveSheet()->setCellValue('AW'.$i, $row["result_comment"]); // result_comment

                $r++;
                $i++;

            }

            $this->excel->setActiveSheetIndex(1);
            $this->excel->getActiveSheet()->setTitle('Criteria');
            $this->excel->getActiveSheet()->setCellValue('A1', 'Criteria');
            $this->excel->getActiveSheet()->setCellValue('B2', $labid);
            $this->excel->getActiveSheet()->setCellValue('B3', $_start);
            $this->excel->getActiveSheet()->setCellValue('B4', $_end);
            $this->excel->getActiveSheet()->setCellValue('B5', $this->input->get('_labo_number'));
            $this->excel->getActiveSheet()->setCellValue('B6', $sample_id);
            $this->excel->getActiveSheet()->setCellValue('B7', $result_id);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$obj->filename.'.xlsx"');
            header('Cache-Control: max-age=0'); //no cache

            ob_end_clean();
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
            $objWriter->save('php://output');
            exit;

         }
        $this->data['bact_list'] =   $result_list;
        $this->load->view('template/print/pbacteriology_result.php', $this->data);
    }

    /* get patient info
     * funciton
     */
     public function patient_info($param1 = '',$param2 = '',$param3 = ''){
        $obj			= new stdClass();
        $obj->filter_val= $this->input->post('filter_val');
        $obj->type		= $this->input->post('type');

        echo json_encode($this->rModel->get_patient_info($obj));

     }

     /*get sample by department */
     public function sample_by_dept(){
        $obj			= new stdClass();
        $obj->value= $this->input->post('department');
        $dept_id =  4;//$this->rModel->normalize_critial($obj->value);

         $sql = "select  
                    ds.id as dept_sam_id,
                    ds.sample_id,
                    s.sample_name
                from camlis_std_department_sample ds
                inner join camlis_std_sample s on s.\"ID\" = ds.sample_id
                
                
                where ds.department_id in(".$dept_id.")";
        $result = $this->db->query($sql)->result();

        $json_array = array();
        foreach ($result as $row){
            array_push($json_array, $row);
        }
        echo json_encode($json_array);

     }

    /*get test by department sample id */
     public function test_by_dept_sample(){
        $obj			= new stdClass();
        $obj->value= $this->input->post('sample_type');
        $dept_id =  $this->rModel->normalize_critial($obj->value);

         $sql = "select  
                    s.\"ID\" as sample_test_id,
                    t.test_name
                from camlis_std_sample_test s
                inner join camlis_std_test t on t.\"ID\" = s.test_id
                
                
                where s.department_sample_id in(".$dept_id.")";
        $result = $this->db->query($sql)->result();

        $json_array = array();
        foreach ($result as $row){
            array_push($json_array, $row);
        }
        echo json_encode($json_array);

     }
    /*get test by result by sample test */
     public function result_by_sample_test(){
        $obj			= new stdClass();
         $obj->sample_type= $this->input->post('sample_type');
         $obj->value= $this->input->post('testing');
         $samp_test_id = $this->rModel->normalize_critial($obj->value);
         $samp_id = $this->rModel->normalize_critial($obj->sample_type);
		 if ($samp_id=='0'){
			 $samp_id=6;
		 }
         $sql = "select  
                    t.\"ID\" as organism_id,
                    t.organism_name
                from camlis_std_department_sample ds 
                inner join camlis_std_sample_test st on st.department_sample_id = ds.\"ID\"
                inner join camlis_std_test_organism s on s.sample_test_id = st.\"ID\"
                inner join camlis_std_organism t on t.\"ID\" = s.organism_id 
                where ds.sample_id in(".$samp_id.") 
                and st.test_id in(".$samp_test_id.") 
                ";

        $result = $this->db->query($sql)->result();

        $json_array = array();
        foreach ($result as $row){
            array_push($json_array, $row);
        }
        echo json_encode($json_array);

     }

    /**
     * Query extractor page
     */
    public function query_extractor() {
        $this->aauth->control('generate_query_extractor');
        $this->app_language->load('pages/report_data_query');
        $this->load->model(['gazetteer_model', 'department_model', 'sample_source_model', 'requester_model', 'organism_model', 'antibiotic_model', 'sample_model', 'test_model','laboratory_model', 'payment_type_model']);

        $this->data['provinces']      = $this->gazetteer_model->get_province();
        $this->data['districts']      = $this->gazetteer_model->get_district();
        $this->data['departments']    = $this->department_model->get_std_department();
        $this->data['samples']        = $this->sample_model->get_std_sample();
        $this->data['sample_sources'] = $this->sample_source_model->get_lab_sample_source();
        $this->data['requesters']     = collect($this->requester_model->get_lab_requester(FALSE))->unique('requester_id')->toArray();
        $this->data['organisms']      = $this->organism_model->get_std_organism();
        $this->data['antibiotics']    = $this->antibiotic_model->get_std_antibiotic();
        $this->data['tests']          = $this->test_model->get_std_test();
        $this->data['group_results']  = $this->test_model->get_sample_test_group_result();
        $this->data['payment_types']  = $this->aauth->is_admin() ? $this->payment_type_model->get_std_payment_type() : $this->payment_type_model->get_lab_payment_type();
        $this->data['sample_descriptions']    = $this->sample_model->get_std_sample_descriptions();
        $this->data['laboratories'] = $this->aauth->is_admin() ? $this->laboratory_model->get_laboratory() : [CamlisSession::getLabSession()];
        $this->template->plugins->add(['DataTable', 'DataTableFileExport', 'MomentJS', 'BootstrapDateTimePicker', 'BootstrapMultiselect']);
        $this->template->stylesheet->add('assets/camlis/css/pages/camlis_report_data_query.css');
        $this->template->stylesheet->add('assets/plugins/select2/css/select2-bootstrap-flat.css');
        $this->template->javascript->add('assets/camlis/js/report/camlis_data_query.js');
        $this->template->content->view('template/pages/report/data_query', $this->data);
        $this->template->content_title = _t('nav.query_extractor');
        $this->template->publish();
    }

    /**
     * TAT report page
     */
    public function tat() {
        $this->template->plugins->add(['MomentJS', 'BootstrapDateTimePicker']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_tat_report.js');
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'tat']);
        $this->template->content->view('template/pages/report/tat_report', $this->data);
        $this->template->modal->view('template/modal/modal_patient_sample_preview_result');
        $this->template->content_title = _t('report.report');
        $this->template->publish();
    }

    /**
     * Graph report page
     */
    public function graph() {
        $this->app_language->load('pages/report_graph');
        $this->load->model(['sample_model', 'test_model', 'laboratory_model']);

        $this->data['sample_types'] = $this->sample_model->get_std_sample();
        $this->data['tests'] = $this->test_model->get_std_test();
        $this->data['laboratories'] = $this->aauth->is_admin() ? $this->laboratory_model->get_laboratory() : [CamlisSession::getLabSession()];
        $this->data['group_results']  = $this->test_model->get_sample_test_group_result();

        $this->template->plugins->add(['MomentJS', 'BootstrapDateTimePicker', 'AmCharts', 'BootstrapMultiselect']);
        $this->template->stylesheet->add('assets/camlis/css/pages/camlis_graph_report.css');
        $this->template->javascript->add('assets/plugins/ammap/themes/light.js');
        $this->template->javascript->add('assets/camlis/js/report/graph_report.js');
        $this->template->content->view('template/pages/report/graph_report', $this->data);
        $this->template->content_title = _t('nav.graph');
        $this->template->publish();
    }

    /**
     * Map report page
     */
    public function map_generation() {
        $this->app_language->load('pages/report_map_generation');
        $this->load->model(['sample_model', 'laboratory_model', 'sample_model', 'test_model', 'organism_model']);

        $this->data['sample_types'] = $this->sample_model->get_std_sample();
        $this->data['department_samples'] = $this->sample_model->get_std_department_sample();
        $this->data['sample_tests'] = $this->test_model->get_std_sample_test();
        $this->data['possible_results'] = $this->organism_model->get_sample_test_organism();
        $this->data['laboratories'] = $this->aauth->is_admin() ? $this->laboratory_model->get_laboratory() : [CamlisSession::getLabSession()];

        $this->template->plugins->add(['MomentJS', 'BootstrapDateTimePicker', 'AmMap', 'BootstrapMultiselect']);
        $this->template->stylesheet->add('assets/camlis/css/pages/camlis_graph_report.css');
        $this->template->javascript->add('assets/camlis/js/map/cambodia.province.js');
        $this->template->javascript->add('assets/camlis/js/map/laboratory.latlong.js');
        $this->template->javascript->add('assets/plugins/ammap/themes/light.js');
        $this->template->javascript->add('assets/camlis/js/report/map_generation.js');
        $this->template->content->view('template/pages/report/map_generation', $this->data);
        $this->template->content_title = _t('nav.map_generation');
        $this->template->publish();
    }

    /**
     * Financial report page
     */
    public function financial() {
        $this->template->plugins->add(['AutoNumeric', 'BootstrapTimePicker']);
        $this->template->javascript->add('assets/camlis/js/report/financial_report.js');
        $this->template->content->view('template/pages/report/financial_report', $this->data);
        $this->template->content_title = _t('nav.financial_report');
        $this->template->modal->view('template/modal/modal_patient_sample_preview_result');
        $this->template->publish();
    }

    /**
     * Get Query extractor data
     */
    public function get_raw_data() {
        $data   = $this->input->post();

        if (!$this->aauth->is_admin()) $data['laboratory']['value'] = [CamlisSession::getLabSession('labID')];

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 600);
        $result = $this->rModel->get_raw_data($data);
        echo json_encode($result);
    }

     function template(){
        $this->load->model('email_model');
        $obj		= new stdClass();
        $obj->patient_id = 22222;
        $obj->sex = 'Male';
        $obj->_year = 66;
        $obj->diseases = 'test';
        // insert tracking email
        $this->db->set('mail', json_encode($obj));
        $this->db->set('date', date('Y-m-d'));
        $this->db->set('status', TRUE);
        $this->db->insert('daily_mail');
        $obj->inscress_id = $this->db->insert_id();
        echo $this->email_model->email_urgent_test($obj);
     }

     /**
      *
      */
     public function lookup_patient_code() {
         $patient_code  = $this->input->post('patient_code');
         $result        = $this->rModel->lookup_patient_code($patient_code);

         $json_array = array();
         foreach ($result as $row){
             array_push($json_array, $row['patient_code'].' - '.$row['sample_number']);
         }

         echo json_encode($json_array);
     }

    /**
     * Generate TAT report
     * @param $start_date
     * @param $end_date
     * @param $testing_type
     * @param $action
     */
     public function generate_tat_report($start_date, $end_date, $testing_type, $action = "preview") {
         $report_data = [];
         $startDate   = DateTime::createFromFormat('Y-m-d', $start_date);
         $endDate     = DateTime::createFromFormat('Y-m-d', $end_date);

         if ($startDate && $endDate) {
             $report_data = $this->rModel->get_tat_report_data($start_date, $end_date, $testing_type);
             $report_data = collect($report_data)->map(function ($item) {
                 $item['diff_col_rec'] = floor((strtotime($item['received_date']) - strtotime($item['collected_date'])) / 60);
                 $item['diff_rec_print'] = floor((strtotime($item['printedDate']) - strtotime($item['received_date'])) / 60);
                 $item['diff_col_print'] = floor((strtotime($item['printedDate']) - strtotime($item['collected_date'])) / 60);
                 return $item;
             });
             $report_data = $report_data->groupBy(function ($item) {
                 return $item['department_id'] . '#' . $item['department_name'];
             }); //Group by department
             $report_data = $report_data->map(function ($items) {
                 return $items->groupBy('group_result')->map(function ($d) {
                     $t = $d->groupBy('type');
                     $t["URGENT"] = $t->get("URGENT", collect([]));
                     $t["ROUTINE"] = $t->get("ROUTINE", collect([]));
                     return $t;
                 });
             });
         }

         $this->data['report_data'] = $report_data;
         $this->data['startDate']   = $startDate ? $startDate->format('d/m/Y') : '';
         $this->data['endDate']     = $endDate ? $endDate->format('d/m/Y') : '';
         $this->data['action']      = $action;

         if ($this->input->server('REQUEST_METHOD') == 'POST') {
             $template = $this->load->view('template/print/tat_report.php', $this->data, TRUE);
             echo json_encode(['template' => $template]);
         }
         else {
             $this->load->view('template/print/tat_report.php', $this->data);
         }
     }

    /**
     * Get Patient number by age group
     */
     public function get_patient_by_age_group() {
         $start_date = $this->input->post('start_date');
         $end_date   = $this->input->post('end_date');
         $laboratory = $this->input->post('laboratory_id');
         $result = [
             ['age_group' => '0 - 29 days', 'male' => 0, 'female' => 0],
             ['age_group' => '1 - 11 months', 'male' => 0, 'female' => 0],
             ['age_group' => '1 - 4 years', 'male' => 0, 'female' => 0],
             ['age_group' => '5 - 14 years', 'male' => 0, 'female' => 0],
             ['age_group' => '15 - 24 years', 'male' => 0, 'female' => 0],
             ['age_group' => '25 - 49 years', 'male' => 0, 'female' => 0],
             ['age_group' => '50 - 64 years', 'male' => 0, 'female' => 0],
             ['age_group' => '>= 65 years', 'male' => 0, 'female' => 0],
         ];
         $data = collect($this->rModel->patient_by_age_group($start_date, $end_date, $laboratory))->keyBy('age_group');
         $result = collect($result)->keyBy('age_group')->merge($data)->toArray();
         echo json_encode(['data' => array_values($result)]);
     }

    /**
     * Get Patient number by sample source
     */
     public function get_patient_by_sample_source() {
         $start_date = $this->input->post('start_date');
         $end_date   = $this->input->post('end_date');
         $laboratory = $this->input->post('laboratory_id');
         $result = $this->rModel->patient_by_sample_source($start_date, $end_date, $laboratory);
         echo json_encode(['data' => $result]);
     }

    /**
     * Get Patient number by sample type
     */
    public function get_patient_by_sample_type() {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $laboratory = $this->input->post('laboratory_id');
        $result = $this->rModel->patient_by_sample_type($start_date, $end_date, $laboratory);
        echo json_encode(['data' => $result]);
    }

    /**
     * Get Patient number by department
     */
    public function get_patient_by_department() {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $laboratory = $this->input->post('laboratory_id');
        $result = $this->rModel->patient_by_department($start_date, $end_date, $laboratory);
        echo json_encode(['data' => $result]);
    }

    /**
     * Get Patient number by department
     */
    public function get_patient_by_month() {
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $laboratory = $this->input->post('laboratory_id');
        $result     = $this->rModel->patient_by_month($start_date, $end_date, $laboratory);

        $startDate  = new DateTime($start_date);
        $endDate    = new DateTime($end_date);
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);
        $data     = [];
        foreach ($period as $dt) {
            $data[$dt->format('F').'-'.$dt->format('Y')] = [
                'month' => $dt->format('F'),
                'year' => $dt->format('Y'),
                'male' => 0,
                'female' => 0
            ];
        }
        $result = collect($result)->keyBy(function($item) { return $item['month'].'-'.$item['year']; });
        $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = $item['month'].' '.$item['year'];  return $item; });
        echo json_encode(['data' => array_values($result->toArray())]);
    }

    /**
     * Get sample type by month
     */
    public function get_sample_type_by_month() {
        $start_date  = $this->input->post('start_date');
        $end_date    = $this->input->post('end_date');
        $laboratory  = $this->input->post('laboratory_id');
        $sample_type = $this->input->post('sample_type_id');
        $result      = $this->rModel->sample_type_by_month($sample_type, $start_date, $end_date, $laboratory);

        $startDate  = new DateTime($start_date);
        $endDate    = new DateTime($end_date);
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);
        $data     = [];
        foreach ($period as $dt) {
            $data[$dt->format('F').'-'.$dt->format('Y')] = [
                'month' => $dt->format('F'),
                'year' => $dt->format('Y'),
                'count' => 0,
            ];
        }
        $result = collect($result)->keyBy(function($item) { return $item['month'].'-'.$item['year']; });
        $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = $item['month'].' '.$item['year'];  return $item; });
        echo json_encode(['data' => array_values($result->toArray())]);
    }

    /**
     * Get sample type by month
     */
    public function get_test_by_month() {
        $start_date  = $this->input->post('start_date');
        $end_date    = $this->input->post('end_date');
        $test_id     = $this->input->post('test_id');
        $laboratory  = $this->input->post('laboratory_id');
        $result      = $this->rModel->test_by_month($test_id, $start_date, $end_date, $laboratory);

        $startDate  = new DateTime($start_date);
        $endDate    = new DateTime($end_date);
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);
        $data     = [];
        foreach ($period as $dt) {
            $data[$dt->format('F').'-'.$dt->format('Y')] = [
                'month' => $dt->format('F'),
                'year' => $dt->format('Y'),
                'count' => 0,
            ];
        }
        $result = collect($result)->keyBy(function($item) { return $item['month'].'-'.$item['year']; });
        $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = $item['month'].' '.$item['year'];  return $item; });
        echo json_encode(['data' => array_values($result->toArray())]);
    }

    /**
     * Get number of patient by address
     */
    public function get_patient_by_address() {
        $start_date  = $this->input->post('start_date');
        $end_date    = $this->input->post('end_date');
        $laboratory  = $this->input->post('laboratory_id');

        $result['province'] = collect($this->rModel->patient_by_province($start_date, $end_date, $laboratory))->map(function($item) {
            $d['id'] = $item['province_code'];
            $d['value'] = $item['patient_count'];
            $d['balloonText'] = "[[title]]<br/>[[value]]";
            if (!empty($item['province_name_'.$this->app_language->app_lang()])) $d['title'] = $item['province_name_'.$this->app_language->app_lang()];
            return $d;
        });
        echo json_encode(['data' => $result]);
    }

    /**
     * Get number of test by address
     */
    public function get_test_by_address() {
        $start_date  = $this->input->post('start_date');
        $end_date    = $this->input->post('end_date');
        $laboratory  = $this->input->post('laboratory_id');
        $department_sample = $this->input->post('department_sample');
        $sample_test_id = $this->input->post('sample_test_id');
        $possible_result_id = $this->input->post('possible_result_id');

        $result['province'] = collect($this->rModel->test_by_province($start_date, $end_date, $department_sample, $sample_test_id, $possible_result_id, $laboratory))->map(function($item) {
            $d['id'] = $item['province_code'];
            $d['value'] = $item['sample_test_count'];
            $d['balloonText'] = "[[title]]<br/>[[value]]";
            if (!empty($item['province_name_'.$this->app_language->app_lang()])) $d['title'] = $item['province_name_'.$this->app_language->app_lang()];
            return $d;
        });
        echo json_encode(['data' => $result]);
    }

    /**
     * Get number of test by address
     */
    public function get_test_by_laboratory() {
        $start_date  = $this->input->post('start_date');
        $end_date    = $this->input->post('end_date');
        $laboratory  = $this->input->post('laboratory_id');
        $department_sample = $this->input->post('department_sample');
        $sample_test_id = $this->input->post('sample_test_id');
        $possible_result_id = $this->input->post('possible_result_id');

        $result['laboratory'] = collect($this->rModel->test_by_laboratory($start_date, $end_date, $department_sample, $sample_test_id, $possible_result_id, $laboratory))->map(function($item) {
            $d['code']  = $item['lab_code'];
            $d['value'] = $item['sample_test_count'];
            if (!empty($item['name_'.$this->app_language->app_lang()])) $d['name'] = $item['name_'.$this->app_language->app_lang()];
            return $d;
        });
        echo json_encode(['data' => $result]);
    }

    /**
     * Get Financial report
     * @param $start_date
     * @param $start_time
     * @param $end_date
     * @param $end_time
     * @param string $type
     */
    public function get_financial_report($start_date, $start_time, $end_date, $end_time, $type = 'preview') {
        $this->load->model(['payment_type_model']);
        $this->app_language->load(['pages/report_financial']);

        $start_date_time = DateTime::createFromFormat('Y-m-d H:i', $start_date.' '.$start_time);
        $end_date_time = DateTime::createFromFormat('Y-m-d H:i', $end_date.' '.$end_time);

        $report_data = $this->rModel->get_financial_report($start_date.' '.$start_time, $end_date.' '.$end_time);
        $payment_types = collect($this->payment_type_model->get_lab_payment_type())->sortBy('name')->toArray();
        $formatted_data = collect($report_data)->groupBy(function($item) { return $item['department_id'].'#'.$item['department_name']; });
        $formatted_data = $formatted_data->map(function($item) use($payment_types) {
            return $item->groupBy('group_result')->map(function($test_payments) use($payment_types) {
                $value = [];
                foreach ($payment_types as $payment_type) {
                    $by_payment_type = $test_payments->where('payment_type_id', $payment_type['id']);
                    $value[$payment_type['id'].'#'.$payment_type['name']] = ['count' => $by_payment_type->count(), 'cost' => $by_payment_type->sum('price')];
                }
                return $value;
            });
        });
        $total_by_departments = collect($report_data)->groupBy(function($item) { return $item['department_id'].'#'.$item['department_name']; });
        $total_by_departments = $total_by_departments->map(function($test_payments) use($payment_types) {
            $value = [];
            foreach ($payment_types as $payment_type) {
                $by_payment_type = $test_payments->where('payment_type_id', $payment_type['id']);
                $value[$payment_type['id'].'#'.$payment_type['name']] = ['total_count' => $by_payment_type->count(), 'total_cost' => $by_payment_type->sum('price')];
            }
            return $value;
        });

        $this->data['payment_types'] = $payment_types;
        $this->data['report_data'] = $formatted_data;
        $this->data['total_by_departments'] = $total_by_departments;
        $this->data['start_date'] = $start_date_time;
        $this->data['end_date']   = $end_date_time;
        $this->data['type'] = $type;

        if ($type != "excel") {
            $result = [];
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                $result[] = ['template' => $this->load->view('template/print/financial_report.php', $this->data, TRUE)];
            } else {
                $this->load->view('template/print/financial_report.php', $this->data);
            }
            if ($this->input->server('REQUEST_METHOD') == 'POST') echo json_encode($result);
        } else {
            $start_date = $start_date_time ? $start_date_time->format('d/m/Y H:i') : '';
            $end_date   = $end_date_time ? $end_date_time->format('d/m/Y H:i') : '';

            try {
                ob_start();
                $this->load->library('phptoexcel');
                $objPHPExcel = new PHPExcel();
                $sheet = $objPHPExcel->setActiveSheetIndex(0);
                $lastColumn = PHPExcel_Cell::stringFromColumnIndex(count($payment_types) * 2 + 1);
                $initRow = $startRow = 6;
                $startCol = 2;

                //Set Header
                $sheet->setCellValue('A1', CamlisSession::getLabSession("name_".$this->data['app_lang']));
                $sheet->mergeCells('A3:'.$lastColumn.'3')->setCellValue('A3', _t('financial_report'));
                $sheet->mergeCells('A4:'.$lastColumn.'4')->setCellValue('A4', $start_date.' '._t('to').' '.$end_date);
                $sheet->getStyle('A3:'.$lastColumn.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A'.$startRow.':A'.($startRow + 1))->setCellValue('A'.$startRow, _t('department'));
                $sheet->mergeCells('B'.$startRow.':B'.($startRow + 1))->setCellValue('B'.$startRow, _t('test_name'));
                $sheet->getStyle('A'.$startRow.':B'.($startRow + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                foreach ($payment_types as $index => $payment_type) {
                    $cell1 = $cell2 = PHPExcel_Cell::stringFromColumnIndex($startCol * ($index + 1));
                    $cell2++;
                    $rangeCell = $cell1.$startRow.':'.$cell2.$startRow;
                    $sheet->mergeCells($rangeCell)->setCellValue($cell1.$startRow, $payment_type['name']);
                    $sheet->getStyle($rangeCell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue($cell1.($startRow + 1), _t('test_count'));
                    $sheet->setCellValue($cell2.($startRow + 1), _t('cost'));
                }
                $sheet->getStyle('A1'.':'.$lastColumn.($startRow + 1))->getFont()->setBold(true);

                //Set result
                $startRow += 2;
                foreach ($formatted_data as $department => $group_results) {
                    $rangeCell = 'A'.$startRow.':A'.($startRow + $group_results->count() - 1);
                    $sheet->mergeCells($rangeCell)->setCellValue('A'.$startRow, preg_replace('/^(\d#)(.*)$/', '${2}', $department));
                    foreach ($group_results as $group_result => $data) {
                        $sheet->setCellValue('B'.$startRow, $group_result);

                        $i = 0;
                        foreach($data as $d) {
                            $cell1 = $cell2 = PHPExcel_Cell::stringFromColumnIndex($startCol * ($i + 1));
                            $cell2++;
                            $sheet->setCellValue($cell1.$startRow, $d['count']);
                            $sheet->setCellValue($cell2.$startRow, $d['cost']);
                            $i++;
                        }

                        $startRow++;
                    }

                    //Total by department
                    $sheet->mergeCells('A'.$startRow.':B'.$startRow)->setCellValue('A'.$startRow, _t('total'));
                    foreach ($payment_types as $index => $payment_type) {
                        $cell1 = $cell2 = PHPExcel_Cell::stringFromColumnIndex($startCol * ($index + 1));
                        $cell2++;
                        $rangeCell1 = $cell1.($startRow - $group_results->count()).':'.$cell1.($startRow - 1);
                        $rangeCell2 = $cell2.($startRow - $group_results->count()).':'.$cell2.($startRow - 1);
                        $sheet->setCellValue($cell1.$startRow, '=SUM('.$rangeCell1.')');
                        $sheet->setCellValue($cell2.$startRow, '=SUM('.$rangeCell2.')');
                    }
                    $sheet->getStyle('A'.$startRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$startRow.':'.$lastColumn.$startRow)->applyFromArray([
                        'fill' => [
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'E7E7E7')
                        ]
                    ]);
                    $startRow++;
                }

                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle('A'.$initRow.':'.$lastColumn.($startRow - 1))->applyFromArray($styleArray);
                $sheet->getStyle('A'.$initRow.':'.$lastColumn.($startRow - 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($startCol).($initRow + 2).":".$lastColumn.($startRow - 1))
                      ->getNumberFormat()
                      ->setFormatCode('#,##0');
                foreach (['A', 'B'] as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $filename = "Financial report from $start_date to $end_date";
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
                header('Cache-Control: max-age=0'); //no cache
                ob_end_clean();

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

                exit;
            }
            catch (PHPExcel_Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}