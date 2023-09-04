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
        $obj->val   = $this->input->post('filter_val');
        $rows       = $this->rModel->lookup_patient_id($obj);

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
        $this->aauth->control('generate_aggregate_report'); 
        $this->template->plugins->add(['DataTableFileExport','Progress', 'BootstrapTimePicker']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'aggregated']);
        $this->template->content->view('template/pages/report/aggregated', $this->data);
        $this->template->publish();
    }

    public function individual() {
        $this->aauth->control('generate_individual_report');
        $this->app_language->load('pages/report_individual');
        $this->template->plugins->add(['AutoComplete', 'AsyncJS', 'AutoNumeric']);
        $this->template->javascript->add('assets/camlis/js/report/individual_report.js');
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'individual']);
        $this->template->modal->view('template/modal/modal_patient_sample_preview_result');
        $this->template->content->view('template/pages/report/individual', $this->data);
        $this->template->publish();
    }

    public function bacteriology() {
        $this->aauth->control('generate_bacteriology_report');
        $this->data['labo_type']    = $this->rModel->load_labo();
        $this->data['department']   = $this->rModel->load_dept();
        $this->data['sample']       = $this->rModel->load_sample();
        $this->data['test']         = $this->rModel->load_test();

        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'bacteriology']);

        $this->template->stylesheet->add('assets/plugins/autocomplete/jquery-ui.css');
        $this->template->javascript->add('assets/plugins/autocomplete/jquery-ui.js');
        $this->template->javascript->add('https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js');
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');

        $this->template->modal->view('template/modal/modal_bacteriology_preview_result');
        $this->template->content->view('template/pages/report/bacteriology', $this->data);
        $this->template->publish();
    }

    public function culture()
    {
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'culture']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');

        $this->template->modal->view('template/modal/modal_bacteriology_preview_result');
        $this->template->content->view('template/pages/report/culture', $this->data);
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
        $obj		        = new stdClass();
        $obj->start	        = $this->format_date($this->input->post('start'));
        $obj->end	        = $this->format_date($this->input->post('end'));
        $obj->start_time    = $this->input->post('start_time');
        $obj->end_time      = $this->input->post('end_time');

        $result_list        = $this->rModel->aggregate_table($obj);

        //echo $result_list;
        $grade_name = '';
        $table_body ='';
        $title = '';
        $sub_total_male = 0;
        $sub_total_female = 0;
        $grand_total_male = 0;
        $grand_total_female = 0;
        $count = 0;
        foreach($result_list as $row){
            $count++;
            if( $title == ''){
                $table_body.= '<tr style=" height: 30px;"> 
                                    <td style="font-weight:bold">&nbsp;'.$row["title"].'</td>  
                                    <td>'._t('global.male').'</th>
                                    <td>'._t('global.female').'</th>
                                    <td>'._t('report.total').'</th>
                                </tr>';
                $table_body .= '<tr >  
                                <td>&nbsp;'.$row["name_kh"].'</td> 
                                <td>&nbsp;'.($row["male"]==''?'':$row["male"]).'</td>
                                <td>&nbsp;'.($row["female"]==''?'':$row["female"]).'</td>
                                <td>&nbsp;'.($row["total"]==''?'':$row["total"]).'</td>   
                            </tr>';
                $sub_total_male += $row["male"];
                $sub_total_female += $row["female"];
                $title = $row['title'];
            }else if($row["title"] == $title){
                $table_body .= '<tr >  
                                <td>&nbsp;'.$row["name_kh"].'</td> 
                                <td>&nbsp;'.($row["male"]==''?'':$row["male"]).'</td>
                                <td>&nbsp;'.($row["female"]==''?'':$row["female"]).'</td>
                                <td>&nbsp;'.($row["total"]==''?'':$row["total"]).'</td>   
                            </tr>';
                $sub_total_male += $row["male"];
                $sub_total_female += $row["female"];
                // if end of the loop
                if (count($result_list) == $count){
                    $table_body.='<tr >  
                            <td style="text-align:right; ">&nbsp; Sub total</td> 
                            <td>&nbsp;'.$sub_total_male.'</td>
                            <td>&nbsp;'.$sub_total_female.'</td>
                            <td>&nbsp;'.($sub_total_male + $sub_total_female).'</td>   
                        </tr>';
                }
            }else{
                $table_body.='<tr >  
                            <td style="text-align:right; ">&nbsp; Sub total</td> 
                            <td>&nbsp;'.$sub_total_male.'</td>
                            <td>&nbsp;'.$sub_total_female.'</td>
                            <td>&nbsp;'.($sub_total_male + $sub_total_female).'</td>   
                        </tr>';
                $grand_total_male +=$sub_total_male;
                $grand_total_female +=$sub_total_female;

                $title = $row['title'];
                $sub_total_male = 0;
                $sub_total_female = 0;
                $table_body.= '<tr style=" height: 30px;"> 
                                    <td style="font-weight:bold">&nbsp;'.$row["title"].'</td>  
                                    <td>'._t('global.male').'</th>
                                    <td>'._t('global.female').'</th>
                                    <td>'._t('report.total').'</th>
                                </tr>';
                $table_body .= '<tr >  
                                <td>&nbsp;'.$row["name_kh"].'</td> 
                                <td>&nbsp;'.($row["male"]==''?'':$row["male"]).'</td>
                                <td>&nbsp;'.($row["female"]==''?'':$row["female"]).'</td>
                                <td>&nbsp;'.($row["total"]==''?'':$row["total"]).'</td>   
                            </tr>';
                $sub_total_male += $row["male"];
                $sub_total_female += $row["female"];
            }
            /*
            if($grade_name != $row["title"] && $row["title"]!=''){
                $table_body.= '<tr style=" height: 30px;"> 
                                    <td style="font-weight:bold">&nbsp;'.$row["title"].'</td>  
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


            $grade_name = $row["title"];
            */
        }
        
        $table_body .= '<tr >  
                            <td style="text-align:right;">&nbsp;Grand total</td> 
                            <td>&nbsp;'.$grand_total_male.'</td>
                            <td>&nbsp;'.$grand_total_female.'</td>
                            <td>&nbsp;'.($grand_total_male + $grand_total_female).'</td>   
                        </tr>';
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
                if($grade_name != $row["title"] && $row["title"]!=''){
                    $table_body.= '<tr style=" height: 30px;"> 
                                        <td style="font-weight:bold">&nbsp;'.$row["title"].'</td>  
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

                $grade_name = $row["title"];

            }
        }else{
            $table_body = "<tr><td align='center'>No data</td></tr>";
        }

        echo  $table_body;
    }

    /**
     * View All Sample Rejection
     */
    public function rejection_sample() {
        $obj		= new stdClass();
        $obj->start	= $this->format_date($this->input->post('start'));
        $obj->end	= $this->format_date($this->input->post('end'));
        $obj->start_time = $this->input->post('start_time');
        $obj->end_time   = $this->input->post('end_time');

        $result_list = $this->rModel->Rejection_Sample($obj);


        $grade_name = '';
        $table_body ='';

        if($result_list){
			$i=0;
			$sumtot=0;
			$grandtot=0;
            foreach($result_list as $row){
				$grandtot=$grandtot+$row["total"];
                if($grade_name != $row["title"] && $row["title"]!=''){
					if ($i>0) {
					$table_body.='<tr style="height: 30px;">
									<td style="text-align:right;" colspan=2>Sub Total</td>
									<td>'.$sumtot.'<td>
								</tr>';
					$i=0;
					}
                    $table_body.= '<tr style=" height: 30px;"> 
                                        <td style="font-weight:bold;background:#c0c0c0" colspan=3>&nbsp;'._t('global.sample_type').': '.$row["title"].'</td></tr>
                                  <tr style=" height: 30px;">
										<td style="font-weight:bold;background:lightgray">'._t('global.sample_source').'</th>
                                        <td style="font-weight:bold;background:lightgray">'._t('global.comment').'</th>
                                        <td style="font-weight:bold;background:lightgray">'._t('report.total').'</th>
                                   </tr>
								   <tr >  
										<td>&nbsp;'.$row["source_name"].'</td>
										<td>&nbsp;'.$row["reject_comment"].'</td>
										<td>&nbsp;'.$row["total"].'</td>   
									</tr>';
					$sumtot=$row["total"];
					$i++;
                } else {

                $table_body.='<tr >  
                                <td>&nbsp;'.$row["source_name"].'</td>
                                <td>&nbsp;'.$row["reject_comment"].'</td>
                                <td>&nbsp;'.$row["total"].'</td>   
                            </tr>';
				$sumtot=$sumtot+$row["total"];

                
				}
				$grade_name = $row["title"];

            }$table_body.='<tr style="height: 30px;">
									<td style="text-align:right;" colspan=2>'._t('report.subtotal').'</td>
									<td>'.$sumtot.'<td>
						   </tr>
						   <tr style="height: 30px;">
									<td style="text-align:right;" colspan=2>'._t('report.grandtotal').'</td>
									<td>'.$grandtot.'<td>
						   </tr>';
        }else{
            $table_body = "<tr><td align='center'>No data</td></tr>";
        }

        echo  $table_body;
    }

    public function amr_report() {
        $obj		        = new stdClass();
        $obj->start	        = $this->format_date($this->input->post('start'));
        $obj->end	        = $this->format_date($this->input->post('end'));
        $obj->start_time    = $this->input->post('start_time');
        $obj->end_time      = $this->input->post('end_time');

        $result_list        = $this->rModel->get_patient_by_culture($obj);
        print_r($result_list);
        $grade_name         = '';
        $table_body         ='';
		
        if($result_list){
			$i=0;
            foreach($result_list as $row){
			
			if($grade_name != $row["title"] && $row["title"]!=''){
					if ($i>0) {
					$table_body.='<tr style="height: 30px;">
									<td style="text-align:right;">Sub Total</td>
									<td>'.$subtot1m.'</td><td>'.$subtot1f.'</td><td>'.$subtot2m.'</td><td>'.$subtot2f.'</td><td>'.$subtot3m.'</td><td>'.$subtot3f.'</td><td>'.$subtot4m.'</td><td>'.$subtot4f.'</td><td>'.$subtot5m.'</td><td>'.$subtot5f.'</td><td>'.$subtot6m.'</td><td>'.$subtot6f.'</td><td>'.$subtot7m.'</td><td>'.$subtot7f.'</td><td>'.$subtot8m.'</td><td>'.$subtot8f.'</td><td>'.$grandtot.'</td>
								</tr>';
					$i=0;
					}
					$table_body.='<tr><tr style=" height: 30px;"><td style="font-weight:bold;background:#c0c0c0" colspan=18>'.$row["title"].'</td></tr>
							<tr style=" height: 30px;"> <td style="font-weight:bold" rowspan=2>&nbsp;'._t('global.sample_type').'</td>  
								<td colspan=2>'._t('report.028d').' </td><td colspan=2>'._t('report.2911m').' </td><td colspan=2>'._t('report.14y').' </td><td colspan=2>'._t('report.514y').' </td><td colspan=2>'._t('report.1524y').' </td><td colspan=2>'._t('report.2549y').' </td><td colspan=2>'._t('report.5064y').' </td><td colspan=2>'._t('report.65y').' </td><td rowspan=2>'._t('report.total').'</td></tr>
							<tr><td>'._t('report.m').'</td><td>'._t('report.f').'</td><td>'._t('report.m').'</td><td>'._t('report.f').'</td><td>'._t('report.m').'</td><td>'._t('report.f').'</td><td>'._t('report.m').'</td><td>'._t('report.f').'</td><td>'._t('report.m').'</td><td>'._t('report.f').'</td><td>'._t('report.m').'</td><td>'._t('report.f').'</td><td>'._t('report.m').'</td><td>'._t('report.f').'</td><td>'._t('report.m').'</td><td>'._t('report.f').'</td></tr>
							<tr><td>'.$row["sampletype"].'</td><td>'.$row["sex1m"].'</td><td>'.$row["sex1f"].'</td><td>'.$row["sex2m"].'</td><td>'.$row["sex2f"].'</td><td>'.$row["sex3m"].'</td><td>'.$row["sex3f"].'</td><td>'.$row["sex4m"].'</td><td>'.$row["sex4f"].'</td><td>'.$row["sex5m"].'</td><td>'.$row["sex5f"].'</td><td>'.$row["sex6m"].'</td><td>'.$row["sex6f"].'</td><td>'.$row["sex7m"].'</td><td>'.$row["sex7f"].'</td><td>'.$row["sex8m"].'</td><td>'.$row["sex8f"].'</td><td>'.$row["total"].'</td></tr>';
					
                    $subtot1m=$row["sex1m"];
                    $subtot1f=$row["sex1f"];
                    $subtot2m=$row["sex2m"];
                    $subtot2f=$row["sex2f"];
                    $subtot3m=$row["sex3m"];
                    $subtot3f=$row["sex3f"];
                    $subtot4m=$row["sex4m"];
                    $subtot4f=$row["sex4f"];
                    $subtot5m=$row["sex5m"];
                    $subtot5f=$row["sex5f"];
                    $subtot6m=$row["sex6m"];
                    $subtot6f=$row["sex6f"];
                    $subtot7m=$row["sex7m"];
                    $subtot7f=$row["sex7f"];
                    $subtot8m=$row["sex8m"];
                    $subtot8f=$row["sex8f"];
                    $grandtot=$row["total"];
					$i++;
			} else {				
                $table_body.='<tr><td>'.$row["sampletype"].'</td><td>'.$row["sex1m"].'</td><td>'.$row["sex1f"].'</td><td>'.$row["sex2m"].'</td><td>'.$row["sex2f"].'</td><td>'.$row["sex3m"].'</td><td>'.$row["sex3f"].'</td><td>'.$row["sex4m"].'</td><td>'.$row["sex4f"].'</td><td>'.$row["sex5m"].'</td><td>'.$row["sex5f"].'</td><td>'.$row["sex6m"].'</td><td>'.$row["sex6f"].'</td><td>'.$row["sex7m"].'</td><td>'.$row["sex7f"].'</td><td>'.$row["sex8m"].'</td><td>'.$row["sex8f"].'</td><td>'.$row["total"].'</td></tr>';
				
                $subtot1m=$subtot1m+$row["sex1m"];
                $subtot1f=$subtot1f+$row["sex1f"];
                $subtot2m=$subtot2m+$row["sex2m"];
                $subtot2f=$subtot2f+$row["sex2f"];
                $subtot3m=$subtot3m+$row["sex3m"];
                $subtot3f=$subtot3f+$row["sex3f"];
                $subtot4m=$subtot4m+$row["sex4m"];
                $subtot4f=$subtot4f+$row["sex4f"];
                $subtot5m=$subtot5m+$row["sex5m"];
                $subtot5f=$subtot5f+$row["sex5f"];
                $subtot6m=$subtot6m+$row["sex6m"];
                $subtot6f=$subtot6f+$row["sex6f"];
                $subtot7m=$subtot7m+$row["sex7m"];
                $subtot7f=$subtot7f+$row["sex7f"];
                $subtot8m=$subtot8m+$row["sex8m"];
                $subtot8f=$subtot8f+$row["sex8f"];
                $grandtot=$grandtot+$row["total"];
				
				}
				$grade_name = $row["title"];
			}
                $table_body.='<tr style="height: 30px;">
									<td style="text-align:right;">Sub Total</td>
									<td>'.$subtot1m.'</td><td>'.$subtot1f.'</td><td>'.$subtot2m.'</td><td>'.$subtot2f.'</td><td>'.$subtot3m.'</td><td>'.$subtot3f.'</td><td>'.$subtot4m.'</td><td>'.$subtot4f.'</td><td>'.$subtot5m.'</td><td>'.$subtot5f.'</td><td>'.$subtot6m.'</td><td>'.$subtot6f.'</td><td>'.$subtot7m.'</td><td>'.$subtot7f.'</td><td>'.$subtot8m.'</td><td>'.$subtot8f.'</td><td>'.$grandtot.'</td>
								</tr>';                            
        }else{
            $table_body = "<tr><td align='center'>No data</td></tr>";
        }
        echo  $table_body;
    }
    /**
     * Print result Patient's sample
     */
    public function preview_pbacteriology_result($type = 'preview') {

		ini_set('max_execution_time', 0);
		
        $obj		= new stdClass();
        $con = '';
        //
        if($this->input->post('_labo_type')!=''){
            $labid = $this->rModel->normalize_critial($this->input->post('_labo_type'));
			if($labid=='0'){
                $con .= '';
            }else{
                $con .= 'and psample."labID" in (' . $labid . ')';
            }

        } else {
            $con .= 'and psample."labID" ='.$this->session->userdata('laboratory')->labID;
            $labid = $this->session->userdata('laboratory')->labID;
		}
        $obj->lab_id = $labid;
        //
        /*if($this->input->post('_department')!=''){
            $dept 	= $this->rModel->normalize_critial($this->input->post('_department'));
            $con .= 'and stv.department_id in(' . $dept . ')';
            $obj->dept = $dept;
        }*/
        //
        if($this->input->post('_sample_type')!=''){
            $sample_id 	= $this->rModel->normalize_critial($this->input->post('_sample_type'));
            if ($sample_id=='0'){
				$con.='';
			}else{
				//$con.= 'and ds.sample_id in(' . $sample_id . ')';
			}
            $obj->sample_id = $sample_id;
        }
        //
        /*if($this->input->post('_testing')!=''){
            $testing_id= $this->rModel->normalize_critial($this->input->post('_testing'));
            $con .= 'and stv.test_id in('.$testing_id.')';
            $obj->testing_id = $testing_id;
        }*/
        //
        if($this->input->post('_result')!=''){
            $result_id 	= $this->rModel->normalize_critial($this->input->post('_result'));
            //$con .= 'and pr.result_id in(' . $result_id . ')';
            $obj->result_id = $result_id;
        }
        //
		if($this->input->post('_labo_number')!=''){
			 $obj->labo_number = $this->input->post('_labo_number');
			 $con .= "and psample.sample_number = '".$obj->labo_number."'";
		}
        if($this->input->post('_start')!=''){
            $_start 	= date('Y-m-d',strtotime(str_replace('/', '-',$this->input->post('_start'))));            
            $con .= ' and psample.received_date >= '. "'".$_start."'"."::date";
            $obj->start_date = $_start;
        }
        if($this->input->post('_end')!=''){
            $_end 	= date('Y-m-d',strtotime(str_replace('/', '-',$this->input->post('_end'))));            
            $con .= ' and psample.received_date <= '. "'".$_end."'"."::date";
            $obj->end_date = $_end;
        }

        //echo $con;
        //exit();
        
        // get result list array
        //$result_list   = $this->rModel->load_result($obj,$con);
        $result_list   = $this->rModel->load_result_v2($obj, $con);
        if($type=='print'){
            
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
        //Get User Assign Laboratory
        $assign_lab	= $this->session->userdata('user_laboratories');
        $this->data['provinces']                = $this->gazetteer_model->get_province();
        $this->data['districts']                = $this->gazetteer_model->get_district();
        $this->data['departments']              = $this->department_model->get_std_department();
        $this->data['samples']                  = $this->sample_model->get_std_sample();
        $this->data['sample_sources']           = $this->sample_source_model->get_lab_sample_source();
        $this->data['requesters']               = collect($this->requester_model->get_lab_requester(FALSE))->unique('requester_id')->toArray();
        $this->data['organisms']                = $this->organism_model->get_std_organism();
        $this->data['antibiotics']              = $this->antibiotic_model->get_std_antibiotic();
        $this->data['tests']                    = $this->test_model->get_std_test();
        $this->data['group_results']            = $this->test_model->get_sample_test_group_result();
        $this->data['payment_types']            = $this->aauth->is_admin() ? $this->payment_type_model->get_std_payment_type() : $this->payment_type_model->get_lab_payment_type();
        $this->data['sample_descriptions']      = $this->sample_model->get_std_sample_descriptions();        
        //Get User Assign Laboratory
		$assign_lab	= $this->session->userdata('user_laboratories');
        if ($this->aauth->is_admin()) {
			$this->data['laboratories'] = $this->laboratory_model->get_laboratory();
		} else {
			$this->data['laboratories'] = $assign_lab && count($assign_lab) > 0 ? $this->laboratory_model->get_laboratory($assign_lab) : array();		
		} 

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
        $this->template->plugins->add(['MomentJS', 'BootstrapDateTimePicker', 'BootstrapMultiselect']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_tat_report.js');
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'tat']);
        $this->data['group_results'] = $this->rModel->group_result();
        $this->template->content->view('template/pages/report/tat_report', $this->data);
        $this->template->modal->view('template/modal/modal_patient_sample_preview_result');
        $this->template->content_title = _t('report.report');
        $this->template->publish();
    }
	/**
     * Sample Rejection report
     */
    public function sample_rejection() {
        //$this->aauth->control('generate_sample_rejection_report');
        $this->template->plugins->add(['BootstrapTimePicker']);
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'rejection']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');
        $this->template->content->view('template/pages/report/sample_rejection', $this->data);
        $this->template->publish();
    }

    /**
     * AMR report page
     */
    
    public function amr() {
        $this->aauth->control('generate_amr_report');
        $this->template->plugins->add(['BootstrapTimePicker']);
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'amr']);
        $this->template->javascript->add('assets/camlis/js/report/camlis_report.js');
        $this->template->content->view('template/pages/report/amr', $this->data);
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
     * User report page
     */
    public function audit() {
        $this->template->plugins->add(['AutoNumeric', 'BootstrapTimePicker', 'BootstrapMultiselect']);
        $this->template->javascript->add('assets/camlis/js/report/audit_report.js');
        $this->load->model(['audit_user_model', 'laboratory_model']);
		$this->data['laboratories'] = ($this->aauth->is_admin()) ? $this->laboratory_model->get_laboratory() : [CamlisSession::getLabSession()];
        $this->template->content->view('template/pages/report/audit_report', $this->data);
        $this->template->content_title = 'Audit';
        $this->template->modal->view('template/modal/modal_patient_sample_preview_result');
        $this->template->publish();
    }
    
    /**
     * Get Query extractor data
     */
    public function get_raw_data() {
        $data   = $this->input->post();
        if(empty($data['laboratory']['value'])){
            $data['laboratory']['value'] = [CamlisSession::getLabSession('labID')];
        }
        ini_set('memory_limit', '4092M');
        ini_set('max_execution_time', 0);
        if($this->app_language->app_lang() == 'en'){
            $result = $this->rModel->get_raw_data($data);
        }else{
            $result = $this->rModel->get_raw_data_kh($data);
        }
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
        $result = collect($result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = trim($item['month']).' '.trim($item['year']);  return $item; });
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
        $result = collect($result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = trim($item['month']).' '.trim($item['year']);  return $item; });
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
        $result = collect($result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = trim($item['month']).' '.trim($item['year']);  return $item; });
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

        $start_date_time        = DateTime::createFromFormat('Y-m-d H:i', $start_date.' '.$start_time);
        $end_date_time          = DateTime::createFromFormat('Y-m-d H:i', $end_date.' '.$end_time);

        $report_data            = $this->rModel->get_financial_report($start_date.' '.$start_time, $end_date.' '.$end_time);
        $payment_types          = collect($this->payment_type_model->get_lab_payment_type())->sortBy('name')->toArray();
        $formatted_data         = collect($report_data)->groupBy(function($item) { return $item['department_id'].'#'.$item['department_name']; });
        $formatted_data         = $formatted_data->map(function($item) use($payment_types) {
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


    /**
     * COVID report page
     */
    public function covid() {
        $this->aauth->control('generate_covid_report');
        $this->load->model('sample_source_model');
        $this->app_language->load(array('patient'));
        //$this->load->model('laboratory_model');
        $this->load->model('laboratory_model');
        
        $this->data['sample_source']        = $this->sample_source_model->get_lab_sample_source(); // added 27-04-2021
        //$this->data['labo_type']            = $this->rModel->load_labo();
        
        //Get User Assign Laboratory
		$assign_lab	= $this->session->userdata('user_laboratories');
        if ($this->aauth->is_admin()) {
			$this->data['laboratories'] = $this->laboratory_model->get_laboratory();
		} else {
			$this->data['laboratories'] = $assign_lab && count($assign_lab) > 0 ? $this->laboratory_model->get_laboratory($assign_lab) : array();
		
		}

        $this->template->plugins->add(['DataTable', 'DataTableFileExport','BootstrapTimePicker','BootstrapMultiselect']);
        $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'covid']);
        $this->template->javascript->add('assets/camlis/js/report/covid_report.js');
        $this->template->content->view('template/pages/report/covid', $this->data);
        $this->template->publish();
    }    

    public function generate_covid_report(){
        $this->load->model('report_model');
        $this->load->model('patient_model');
        
        $obj		        = new stdClass();

        if($this->input->post('start') !== ""){
            $obj->start	= $this->format_date($this->input->post('start'));
        }
        if($this->input->post('end') !== ""){
            $obj->end	    = $this->format_date($this->input->post('end'));
        }

        //$obj->start	        = $this->format_date($this->input->post('start'));
        //$obj->end	        = $this->format_date($this->input->post('end'));
        $lang               = $this->input->post('lang');
        $obj->start_time    = $this->input->post('start_time');
        $obj->end_time      = $this->input->post('end_time');
        $for_research       = $this->input->post('for_research');
        $obj->test_start    = "";
        $obj->test_end      = "";
        $obj->start_sn       = "";
        $obj->end_sn        = "";
        //$obj->test_start	= $this->format_date($this->input->post('test_start'));
        //$obj->test_end	    = $this->format_date($this->input->post('test_end'));

        // added 01 - April 2021

        if($this->input->post('test_start') !== ""){
            $obj->test_start	= $this->format_date($this->input->post('test_start'));
        }
        if($this->input->post('test_end') !== ""){
            $obj->test_end	    = $this->format_date($this->input->post('test_end'));
        }
        if($this->input->post('start_sample_number') !== "" || $this->input->post('end_sample_number') !== ""){
            $obj->start_sn = $this->input->post('start_sample_number');
            $obj->end_sn  = $this->input->post('end_sample_number');
        }
        // added 27-04-2021
        
        if($this->input->post('sample_source') !== 0){
            $sample_source = $this->input->post('sample_source');
            $ss_string = '';       
            if (is_array($sample_source) && count($sample_source) > 0) {
                foreach ($sample_source as $cs) {
                    $ss_string.= $cs.",";
                }
            }
            $ss_string = substr($ss_string,0,strlen($ss_string)-1);
            $obj->sample_source  = $ss_string;
        }
        if($this->input->post('test_name') !== 0){
            $test_name  = $this->input->post('test_name');
            $str_testname = '';
            if (is_array($test_name) && count($test_name) > 0) {
                foreach ($test_name as $cs) {
                    $str_testname.= " test.\"ID\" = ".$cs." OR";
                }
            }
            $str_testname = substr($str_testname,0,strlen($str_testname)-2);
            $obj->test_name  = $str_testname;
        }
        $obj->lab_id    = $this->input->post('lab_id');
        //end
        if($this->input->post('test_result') !== ""){            
            $obj->test_result	    = $this->input->post('test_result');
        }

        if($this->input->post('number_of_sample') !== 0){
            $obj->number_of_sample	    = $this->input->post('number_of_sample');
        }

        //04102021
        $lab_ids = $this->input->post('lab_id');
        $result_lists = array();
        for($i = 0 ; $i < count($lab_ids) ; $i++ ){
            $res    = $this->report_model->covid_table($obj , $for_research, $lab_ids[$i]);
            if($res) $result_lists[] = $res;
        }

        $data               = array();
        $table_body = '';
        
        $total = 0;
        $n = 1;
        $index = 0;
     //   $table_body .= '<tbody>';
        if(count($result_lists) == 0){
            $table_body .= '<tr>';
            $table_body .= '<td colspan="16" class="text-center">'._t('global.no_result').'</td>';
            $table_body .= '</tr>';
        }
        for($j = 0 ; $j < count($result_lists) ; $j++){
            $result_list = $result_lists[$j];
            foreach($result_list as $key => $row){
                $table_body .= '<tr>';
                $table_body .= '<td>'.$n.'</td>';
                $table_body .= '<td>'.$row['patient_code'].'</td>';
                $table_body .= '<td>'.$row['patient_name'].'</td>';
                $table_body .= '<td>'.$row['patient_age'].'</td>';
                $table_body .= '<td>'.$row['patient_gender'].'</td>';
                $nationality = '';
                $passport_number = '';
                $flight_number = "";
                $date_arrival = "";
                
                $patient     = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                if($patient){
                    $nationality = $patient['nationality_en'];
                    $passport_number = $patient['passport_number'];
                    $flight_number = $patient['flight_number'];
                    $date_arrival = $patient['date_arrival'];
                }
               
                $table_body .= '<td>'.$nationality.'</td>';
                $table_body .= '<td>'.$passport_number.'</td>';
                $table_body .= '<td>'.$flight_number.'</td>';
                $table_body .= '<td>'.$date_arrival.'</td>';
    
                $table_body .= '<td>'.$row['sample_source'].'</td>';
                if(isset($row['reason_for_testing'])){
                    $reason = $row['reason_for_testing'];
                }else{
                    $reason = $row['diagnosis'];
                    $reason = str_replace('{','',$reason);
                    $reason = str_replace('}','',$reason);
                    $reason = explode(",", $reason);
                    $reason = ($reason[0] == "NULL") ? '' : str_replace('"','',$reason[0]);
                }
    
                $table_body .= '<td>'.$row['reason_for_testing'].'</td>';
                $table_body .= '<td>'.$row['diagnosis'].'</td>';
                $table_body .= '<td>'.$row['collected_date'].'</td>';
                $table_body .= '<td>'.$row['received_date'].'</td>';
                $table_body .= '<td>'.$row['test_date'].'</td>';
                $table_body .= '<td>'.$row['result_organism'].'</td>';
                $table_body .= '<td>'.$row['sample_number'].'</td>';
                $nSample     = $row['number_of_sample'] == 0 ? "" : $row['number_of_sample'];
                $table_body .= '<td>'.$nSample.'</td>';
                $table_body .= '</tr>';
    
                $result_list[$key]['passport_number'] =  $passport_number;
                $result_list[$key]['nationality'] = $nationality;
                $n++;
    
                
            }
        }
        
    //    $table_body .= '</tbody>';
    //    echo $table_body;
        
        echo json_encode(array('data' => $result_lists, 'htmlstring' => $table_body));
        
    }
    
    public function culture_list()
    {
        $start_date = date("Y-m-d", strtotime($this->input->post('start_date')));
        $end_date   = date("Y-m-d", strtotime($this->input->post('end_date')));
        
        ini_set('max_execution_time', 2400);
        $collect = collect($this->rModel->culture_result($start_date, $end_date));
        $years = $collect->map(function($item){
            return $item->years;
        })->unique();

        $results = [];
        foreach ($years as $year) {
            $data = $collect->where('years', $year);
            $months = $data->map(function($item){
                return $item->months; 
            })->unique();

            $sample_volume = [];
            foreach ($months as $month) {
                $total = 0;
                $data->where('months', $month)->map(function($item) use(&$total, &$sample_volume){
                    $total+= (!empty($item->sample_volume1)) ? 1: 0;
                    $total+= (!empty($item->sample_volume2)) ? 1: 0;
                });
                array_push($sample_volume, [$month => $total]);
            }
            array_push($results, [$year => $sample_volume]);
        }
        echo json_encode(['culture' => $results]);
    }

    public function get_sample_source(){
        $this->load->model('sample_source_model');
        $lab_id = $this->input->post("lab_id");
        $lab = $this->sample_source_model->get_sample_source($lab_id);
        echo json_encode($lab );
    }

    public function export_covid_report_backup(){
        
        $this->app_language->load(array('global','patient','sample'));
        $this->load->model('report_model');
        $this->load->model('patient_model');
        $this->load->model('user_model');
        $this->load->model('laboratory_model');
        
       
        $obj		        = new stdClass();
       // $obj->start	        = $this->format_date($this->input->get('start'));
        //$obj->end	        = $this->format_date($this->input->get('end'));
        $obj->start_time    = $this->input->get('start_time');
        $obj->end_time      = $this->input->get('end_time');
        $for_research       = $this->input->get('for_research');
        $obj->test_start    = "";
        $obj->test_end      = "";
        $obj->start_sn      = "";
        $obj->end_sn        = "";
        $lang       = $this->app_language->app_lang();

        if($this->input->get('start') !== ""){
            $obj->start	= $this->format_date($this->input->get('start'));
        }
        if($this->input->get('end') !== ""){
            $obj->end	    = $this->format_date($this->input->get('end'));
        }
        if($this->input->get('test_start') !== ""){
            $obj->test_start	= $this->format_date($this->input->get('test_start'));
        }
        if($this->input->get('test_end') !== ""){
            $obj->test_end	    = $this->format_date($this->input->get('test_end'));
        }
        if($this->input->get('start_sample_number') !== "" || $this->input->get('end_sample_number') !== ""){
            $obj->start_sn = $this->input->get('start_sample_number');
            $obj->end_sn  = $this->input->get('end_sample_number');
        }
        if($this->input->get('sample_source') !== ""){
            $obj->sample_source	= $this->input->get('sample_source');
        }
        if($this->input->get('test_name') !== ""){
            $test_name_str     = $this->input->get('test_name');
            $r              = (explode(",",$test_name_str));
            $str_testname   = '';
            for($i = 0; $i < count($r); $i++){
                $str_testname.= " test.\"ID\" = ".$r[$i]." OR";
            }
            $str_testname = substr($str_testname,0,strlen($str_testname)-2);
            $obj->test_name  = $str_testname;
        }
        if($this->input->get('lab_id') !== ""){
            $obj->lab_id	    = $this->input->get('lab_id');
            $lab_code = $obj->lab_id;
            $lab = $this->laboratory_model->get_laboratory($lab_code, FALSE);
            $lab_name = ($lang == 'kh') ? $lab[0]->name_kh :  strtoupper($lab[0]->name_en);
        }else{
            $lab_code = $this->session->userdata('laboratory')->labID;
            $lab_name = CamlisSession::getLabSession("name_".$lang);
        }

        if($this->input->get('test_result') !== ""){            
            $obj->test_result	    = $this->input->get('test_result');
        }

        //$result_list        = $this->report_model->covid_table($obj,$for_research);
        
        
        $user       = $this->user_model-> get_user($this->session->userdata("username"));
        $report_by  = $user[0]['fullname'];
        

        if($lang == 'en'){
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
        }else{
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
        }
        try {
            ob_start();
            $this->load->library('phptoexcel');
            $objPHPExcel    = new PHPExcel();


            $n_sheet = 1;
            $SHEET_ARR = array();
            $table_columns  = array(_t('sample.order_number'), _t('patient.patient_id'), _t('patient.name'), _t('global.patient_age'), _t('global.patient_gender'), _t('patient.nationality'), _t('patient.passport_no') , _t('sample.sample_number') , _t('sample.reason_for_testing') , _t('global.sample_source'), _t('sample.collect_dt') , _t('sample.receive_dt'), _t('sample.test_date') ,_t('sample.result')." SARS-CoV-2", _t('sample.number_of_sample'));
            
            $border_style = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $headerStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 13,
                    'name'  => 'Khmer OS Muol Light'
                ));
            $header1StyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Muol Light'
            ));
            $subheaderStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Bokor'
            ));
            $columnStyleArray = array(
                'font'  => array(
                    'color' => array('rgb' => '000000'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Siemreap'
            ));
            
            $bodyStyleArray = array(
                'font'  => array(
                        'color' => array('rgb' => '000000'),
                        'size'  => 11,
                        'name'  => 'Khmer OS Siemreap'
                    ));
            
            $current_date           = date('d/M/Y');
            $total_arr              = array();
            $total_by_sample_source = array();
            
            $reason_for_testing_total   = array(); //05052021
            $data_by_sample_source      = array(); //10052021
            if($for_research == "all"){
                $file_title = "បង្ហាញទាំងអស់";
                foreach($FOR_RESEARCH_ARR as $index => $value){
                    $result_list    = $this->report_model->covid_table($obj,$index);
                    if($result_list){
                        $objPHPExcel->createSheet();
                        $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                        $objDrawing     = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('MOH');
                        $objDrawing->setDescription('ក្រសួងសុខាភិបាល');           
                        $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                        $objDrawing->setOffsetX(5);  // setOffsetX works properly
                        $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                        $objDrawing->setCoordinates('B2');
                        $objDrawing->setHeight(36); // logo height
                        $objDrawing->setWorksheet($sheet);
                        $title = ($index == 0) ? _t('sample.none_reason_selected') : $value;
                        $sheet->setTitle($title);
                        $column         = 0;
                        
                        //Set Header
                        $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                        $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                        $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                        $sheet->mergeCells('C4:N4')->setCellValue('C4', _t('report.covid19_laboratory_result')."(".$title.")");
                        $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                               
                        $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                        $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                        $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                        $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                        $sheet->mergeCells('A5:O5')->setCellValue('A5', _t('report.date')." " .$current_date);
                        $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                        foreach($table_columns as $field)
                        {
                            $sheet->setCellValueByColumnAndRow($column, 6, $field);
                            $column++;
                        }
                        $sheet->getStyle("A6:O6")->applyFromArray($columnStyleArray);
                        $sheet->getStyle('A6:O6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A6:O6')->getFont()->setBold(true);
                        $sheet->getStyle('A5')->getFont()->setBold(true);
                        $num_row            = 7;
                        $number             = 1;
                        $number_male        = 0;
                        $number_female      = 0;
                        $number_positive    = 0;
                        $number_negative    = 0;
                        $number_invalid     = 0;
                        $number_pending     = 0;

                        $reason_for_testing_total[$value] = array();

                        foreach($result_list as $key => $row){
                            $nationality        = '';
                            $passport_number    = '';
                            $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $title;
                            
                            $patient            = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                            if($patient){
                                $nationality    = $patient['nationality_en'];
                                $passport_number = $patient['passport_number'];
                                // save it for group of sample source
                                $row['nationality'] = $nationality;
                                $row['passport_number'] = $passport_number;
                            }
                            // Request to REMOVE "SRP-" from siem reap covid lab
                            // 18-03-2021
                            $patient_code = $row['patient_code'];
                            if($lab_code == 61){
                                $patient_code = str_replace("SRP-","",$row['patient_code']);
                            }
                            $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                            $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                            $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                            $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                            $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                            $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                            $sheet->setCellValueByColumnAndRow(7, $num_row, $row['sample_number']);
                            $sheet->setCellValueByColumnAndRow(8, $num_row, $reason);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_source']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $row['collected_date']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $row['received_date']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $row['test_date']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $row['result_organism']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $number_of_sample);
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('D'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('E'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('H'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('K'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('L'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('M'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('N'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('O'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            $number++;
                            
                            if($row['patient_gender'] == 'M'){
                                $number_male++;    
                            }
                            if($row['patient_gender'] == 'F'){
                                $number_female++;    
                            }
                            if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                $number_positive++;    
                            }
                            if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                $number_negative++;
                            }
                            if($row['result_organism'] == 'Invalid'){
                                $number_invalid++;
                            }
                            if($row['result_organism'] == ''){
                                $number_pending++;
                            }
                            // number of sample by sample source
                            // added 28-06-2021
                            $exist = false;
                            foreach($total_by_sample_source as $key => $source){
                                if($key == $row['sample_source']){
                                    $exist = true;
                                    break;
                                }
                            }
                            
                            // if sample source does not exit
                            if(!$exist){
                                $total_by_sample_source[$row['sample_source']] = array(
                                    "number_male"       => 0 ,
                                    "number_female"     => 0 ,
                                    "number_positive"   => 0 ,
                                    "number_negative"   => 0 ,
                                    "number_invalid"    => 0 ,
                                    "number_pending"    => 0
                                );
                                if($row['patient_gender'] == 'M'){
                                    $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                                }
                                if($row['patient_gender'] == 'F'){
                                    $total_by_sample_source[$row['sample_source']]["number_female"]++;
                                }
                                if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                                }
                                if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                                }
                                if($row['result_organism'] == 'Invalid'){
                                    $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                                }
                                if($row['result_organism'] == ''){
                                    $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                                }
                            }else{
                                if($row['patient_gender'] == 'M'){
                                    $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                                }
                                if($row['patient_gender'] == 'F'){
                                    $total_by_sample_source[$row['sample_source']]["number_female"]++;
                                }
                                if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                                }
                                if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                                }
                                if($row['result_organism'] == 'Invalid'){
                                    $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                                }
                                if($row['result_organism'] == ''){
                                    $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                                }
                            }
                            // end
                            //05052021
                            /**
                             * 05052021
                             * Table sample source group by reason for testing
                             */
                            $is_sample_source_exist = false;
                            foreach($reason_for_testing_total[$value] as $key => $source){
                                if($key == $row['sample_source']){
                                    $is_sample_source_exist = true;
                                    break;
                                }
                            }
                            if(!$is_sample_source_exist){
                                $reason_for_testing_total[$value][$row['sample_source']] = array(
                                    "number_male"       => 0 ,
                                    "number_female"     => 0 ,
                                    "number_positive"   => 0 ,
                                    "number_negative"   => 0 ,
                                    "number_invalid"    => 0 ,  
                                    "number_pending"    => 0
                                );
                                if(trim($row['patient_gender']) == 'M'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                                }
                                if(trim($row['patient_gender']) == 'F'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                                }
                                if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                                }
                                if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                                }

                                if(trim($row['result_organism']) == 'Invalid'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                                }
                                if(trim($row['result_organism']) == ''){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                                }

                            }else{
                                if(trim($row['patient_gender']) == 'M'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                                }
                                if(trim($row['patient_gender']) == 'F'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                                }
                                if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                                }
                                if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                                }

                                if(trim($row['result_organism']) == 'Invalid'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                                }
                                if(trim($row['result_organism']) == ''){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                                }
                            }
                            // End

                            /**
                             * Added: 10-05-2021
                             * Data by sample source
                             */
                            $sample_source_existent = false;
                            
                            foreach($data_by_sample_source as $key => $source){
                                if($key == trim($row['sample_source'])){
                                    $sample_source_existent = true;
                                    break;
                                }
                            }                            
                            if(!$sample_source_existent){
                                $data_by_sample_source[$row['sample_source']] = array();
                                $data_by_sample_source[$row['sample_source']][] = $row;
                            }else{
                                $data_by_sample_source[$row['sample_source']][] = $row;
                            }
                            
                            /** End */
                            
                        }
                        $total    = array(
                            'title'             => $title,
                            'total'             => count($result_list),
                            'number_male'       => $number_male,
                            'number_female'     => $number_female,
                            'number_positive'   => $number_positive,
                            'number_negative'   => $number_negative,
                            'number_invalid'    => $number_invalid,
                            'number_pending'    => $number_pending
                        );
                        $total_arr[] = $total;
                        $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                        $sheet->getStyle("A6:O".($num_row - 1))->applyFromArray($border_style);
                        $sheet->mergeCells('K'.($num_row + 1).':O'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                        $sheet->mergeCells('K'.($num_row + 2).':O'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                        
                        $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                        
                        $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                        $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                        $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
        
                        $SHEET_ARR[$value] = $sheet;
                        $n_sheet++;
                    }
                    
                    // Auto size columns for each worksheet
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                        $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

                        $sheet = $objPHPExcel->getActiveSheet();
                        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        /** @var PHPExcel_Cell $cell */
                        foreach ($cellIterator as $cell) {
                            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                        }
                    }
                }


                /**
                 * Worksheet data by Sample Source
                 */
                //$n_sheet++;
                
                if(count($data_by_sample_source) > 0){
                
                    //if($this->session->userdata('roleid') == 1){
                        foreach($data_by_sample_source as $ind => $result){
                            $sample_source_name = $ind;
                            $objPHPExcel->createSheet();
                            $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                            $objDrawing     = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setName('MOH');
                            $objDrawing->setDescription('ក្រសួងសុខាភិបាល');           
                            $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                            $objDrawing->setOffsetX(5);  // setOffsetX works properly
                            $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                            $objDrawing->setCoordinates('B2');
                            $objDrawing->setHeight(36); // logo height
                            $objDrawing->setWorksheet($sheet);
                            $title = $sample_source_name;
                            
                            // sample source in Khmer Unicode
                            if(mb_strlen($title) > 31){
                                $title = mb_substr($title,0,15,"UTF-8");
                               // $title = mb_substr($title, 0 , 30);
                             // $invalidCharacters = $sheet->getInvalidCharacters();
                               // $title = str_replace($invalidCharacters, '', $title);
                            }
                            
                            $sheet->setTitle(strval($n_sheet));
                            //$sheet->setTitle($title);
                            $column         = 0;
                            
                            //Set Header
                            $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                            $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                            $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                            $sheet->mergeCells('C4:N4')->setCellValue('C4', _t('report.covid19_laboratory_result')." "._t('report.by_sample_source')." (".$sample_source_name.")");
                            $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                            $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                                    
                            $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                            $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                            $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                            $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                            $sheet->mergeCells('A5:O5')->setCellValue('A5', _t('report.date')." " .$current_date);
                            $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                            foreach($table_columns as $field)
                            {
                                $sheet->setCellValueByColumnAndRow($column, 6, $field);
                                $column++;
                            }
                            $sheet->getStyle("A6:O6")->applyFromArray($columnStyleArray);
                            $sheet->getStyle('A6:O6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A6:O6')->getFont()->setBold(true);
                            $sheet->getStyle('A5')->getFont()->setBold(true);
                            $num_row = 7;
                            $number = 1;
                            
                            foreach($result as $key => $row){
                                $nationality        = '';
                                $passport_number    = '';
                                $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $row['reason_for_testing'];
                                                               
                                // Request to REMOVE "SRP-" from siem reap covid lab
                                // 18-03-2021
                                $patient_code = $row['patient_code'];
                                if($lab_code == 61){
                                    $patient_code = str_replace("SRP-","",$row['patient_code']);
                                }
                                $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                                $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                                $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                                $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                                $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                                $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                                $sheet->setCellValueByColumnAndRow(5, $num_row, $row['nationality']);
                                $sheet->setCellValueByColumnAndRow(6, $num_row, $row['passport_number']);
                                $sheet->setCellValueByColumnAndRow(7, $num_row, $row['sample_number']);
                                $sheet->setCellValueByColumnAndRow(8, $num_row, $reason);
                                $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_source']);
                                $sheet->setCellValueByColumnAndRow(10, $num_row, $row['collected_date']);
                                $sheet->setCellValueByColumnAndRow(11, $num_row, $row['received_date']);
                                $sheet->setCellValueByColumnAndRow(12, $num_row, $row['test_date']);
                                $sheet->setCellValueByColumnAndRow(13, $num_row, $row['result_organism']);
                                $sheet->setCellValueByColumnAndRow(14, $num_row, $number_of_sample);
                                $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('D'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('E'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('H'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('K'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('L'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('M'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('N'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('O'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $column++;
                                $num_row++;
                                $number++;
                            }
                            $n_sheet++;
                            $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                            $sheet->getStyle("A6:O".($num_row - 1))->applyFromArray($border_style);
                            $sheet->mergeCells('K'.($num_row + 1).':O'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                            $sheet->mergeCells('K'.($num_row + 2).':O'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                            
                            $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            
                            $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                            $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                            $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            }
                            // Auto size columns for each worksheet
                            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                                $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

                                $sheet = $objPHPExcel->getActiveSheet();
                                $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(true);
                               
                                foreach ($cellIterator as $cell) {
                                    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                                }
                            }
                    //    }
                    }
                   
                    //End 

                

                // Added 07 April 2021
                // create total sheet 
                if(count($total_arr) > 0){
                    $table_columns = array( _t('sample.order_number'), _t('sample.reason_for_testing'), _t('sample.total'));
                    $objPHPExcel->createSheet();
                    $sheet          = $objPHPExcel->setActiveSheetIndex(0);
                    $objDrawing     = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('MOH');
                    $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                    $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                    $objDrawing->setOffsetX(5);    // setOffsetX works properly
                    $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('B2');
                    $objDrawing->setHeight(36); // logo height
                    $objDrawing->setWorksheet($sheet);
                    $title = _t('sample.overall_result');
                    $sheet->setTitle($title);
                    $column         = 0;
                    
                    //Set Header
                    $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                    $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                    $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                    $sheet->mergeCells('C4:N4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$title.")");
                    $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                    $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                    $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                    $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                    $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                    $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                    $sheet->mergeCells('A5:P5')->setCellValue('A5',_t('report.date')." ".$current_date);
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A5")->applyFromArray($columnStyleArray);

                    // header 
                    $sheet->setCellValueByColumnAndRow(0, 6, _t('sample.order_number'));
                    $sheet->mergeCells('B6:I6')->setCellValue('B6',_t('sample.reason_for_testing'));
                    $sheet->setCellValueByColumnAndRow(9, 6, _t('global.female') );
                    $sheet->setCellValueByColumnAndRow(10, 6, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, 6, _t('sample.number_of_negative'));
                    $sheet->setCellValueByColumnAndRow(12, 6, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, 6, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, 6, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, 6, _t('sample.total'));
                    

                    $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                    $sheet->getStyle('A5')->getFont()->setBold(true);

                    $num_row = 7;
                    $number = 1;
                    
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    foreach($total_arr as $key => $row){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,$row['title']);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $row['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $row['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $row['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $row['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $row['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $row['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, $row['total']);
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;
                        $total_nb_female    += $row['number_female'];
                        $total_nb_male      += $row['number_male'];
                        $total_nb_negative  += $row['number_negative'];
                        $total_nb_positive  +=  $row['number_positive'];
                        $total_nb_invalid   +=  $row['number_invalid'];
                        $total_nb_pending   +=  $row['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    // End Grand total
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A6:O".($num_row - 1))->applyFromArray($border_style);

                    $num_row++;
                    $number = 1;
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $tbl2_row = $num_row; 
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,_t('sample.sample_source'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;

                    foreach($total_by_sample_source as $key => $source){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'. $num_row,$key);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;

                        $total_nb_female    += $source['number_female'];
                        $total_nb_male      += $source['number_male'];
                        $total_nb_negative  += $source['number_negative'];
                        $total_nb_positive  +=  $source['number_positive'];
                        $total_nb_invalid   +=  $source['number_invalid'];
                        $total_nb_pending   +=  $source['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    // End Grand total
                    
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);


                    /**
                     * 05052021
                     */
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $num_row++;
                    $tbl2_row           = $num_row;
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'.$num_row, _t('sample.reason_for_testing'));
                    $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'.$num_row, _t('sample.sample_source') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive') );
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid') );
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending') );
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;
                    $number = 1;
                    foreach($reason_for_testing_total as $label_reason => $res){
                        $mg = $num_row;
                        $len = 0;
                        foreach($res as $key => $source){
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'. $num_row,$label_reason);
                            $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'. $num_row,$key);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            //$number++;
    
                            $total_nb_female    += $source['number_female'];
                            $total_nb_male      += $source['number_male'];
                            $total_nb_negative  += $source['number_negative'];
                            $total_nb_positive  += $source['number_positive'];
                            $total_nb_invalid   += $source['number_invalid'];
                            $total_nb_pending   += $source['number_pending'];
                            $len++;
                        }
                        //$sheet->mergeCells('A'.$mg.':A'. ($mg + $len))->setCellValue('A'. $mg ,$number);
                        $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);
                        /*
                        if($len > 1){
                            $sheet->mergeCells('A'.$mg.':A'. ($mg + $len))->setCellValue('A'. $mg ,$number);
                            $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);
                            $sheet->getStyle('A'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );

                            $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        }else{
                            $sheet->setCellValueByColumnAndRow(0, ($num_row-1), $number);
                            $sheet->mergeCells('B'.($num_row-1).':E'.($num_row-1))->setCellValue('B'. ($num_row-1),$label_reason);
                        }
                        */
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        $number++;
                    }
                    

                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    // End Grand total
                    /**End */
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);

                    $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                    $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                    
                    $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    
                    $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                    $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                    $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                }
                
                //Remove the black worksheet
                /*
                $in = $objPHPExcel->getSheetByName('Worksheet 1');
                $sheetIndex = $objPHPExcel->getIndex($in);
                if($sheetIndex){
                    $objPHPExcel->removeSheetByIndex($wsIndex);
                }
                */
                $sheetIndex = $objPHPExcel->getIndex(
                    $objPHPExcel->getSheetByName('Worksheet 1')
                );
                $objPHPExcel->removeSheetByIndex($sheetIndex);
                $wsIndexStr = "".$sheetIndex;
                
            }else{
                /**
                 * 
                 * Save to only sheet
                 */

                $reason_for_testing = $FOR_RESEARCH_ARR[$for_research];
                $file_title = $reason_for_testing;
                $result_list    = $this->report_model->covid_table($obj,$for_research);
                $sheet          = $objPHPExcel->setActiveSheetIndex(0);
                $objDrawing     = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('MOH');
                $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                $objDrawing->setOffsetX(5);    // setOffsetX works properly
                $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                $objDrawing->setCoordinates('B2');
                $objDrawing->setHeight(36); // logo height
                $objDrawing->setWorksheet($sheet);
                $column         = 0;
                
                //Set Header
               
               $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
               $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
               
               $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
               
               $sheet->mergeCells('C4:O4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$reason_for_testing.")");
               $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
               
               $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
               $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
               $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
               $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
               $sheet->mergeCells('A5:O5')->setCellValue('A5',"កាលបរិច្ឆេទ៖ ".$current_date);
               $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
               $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                foreach($table_columns as $field)
                {
                    $sheet->setCellValueByColumnAndRow($column, 6, $field);
                    $column++;
                }                    
                $sheet->getStyle("A6:O6")->applyFromArray($columnStyleArray);
                $sheet->getStyle('A6:O6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A6:O6')->getFont()->setBold(true);
                $sheet->getStyle('A5')->getFont()->setBold(true);
                $num_row = 7;
                $number = 1;
                
                foreach($result_list as $key => $row){
                    $nationality = '';
                    $passport_number = '';
                    $reason     = $FOR_RESEARCH_ARR[$row['for_research']];
                    $patient     = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                    if($patient){
                        $nationality = $patient['nationality_en'];
                        $passport_number = $patient['passport_number'];
                    }
                    // Request to REMOVE "SRP-" from siem reap covid lab
                    // 18-03-2021
                    $patient_code = $row['patient_code'];
                    if($lab_code == 61){
                        $patient_code = str_replace("SRP-","",$row['patient_code']);
                    }
                    $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                    $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                    $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                    $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                    $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                    $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                    $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                    $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                    $sheet->setCellValueByColumnAndRow(7, $num_row, $row['sample_number']);
                    $sheet->setCellValueByColumnAndRow(8, $num_row, $reason);
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_source']);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $row['collected_date']);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $row['received_date']);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $row['test_date']);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $row['result_organism']);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $number_of_sample);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('H'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('L'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('M'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('N'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('0'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $num_row++;
                    $number++;
                }                        
                $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                $sheet->getStyle("A6:O".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':O'.($num_row + 1))->setCellValue('K'.($num_row + 1),_t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':O'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                
                              
                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1),_t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                
                
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                /** */
            }
            
            
            $filename = "Sars_CoV2_".$lab_code."-".$file_title."-".$current_date;

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

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

    public function export_covid_report_(){
        
        $this->app_language->load(array('global','patient','sample'));
        $this->load->model('report_model');
        $this->load->model('patient_model');
        $this->load->model('user_model');
        $this->load->model('laboratory_model');
        
        $obj		        = new stdClass();
       // $obj->start	        = $this->format_date($this->input->get('start'));
        //$obj->end	        = $this->format_date($this->input->get('end'));
        $obj->start_time    = $this->input->get('start_time');
        $obj->end_time      = $this->input->get('end_time');
        $for_research       = $this->input->get('for_research');
        $obj->test_start    = "";
        $obj->test_end      = "";
        $obj->start_sn      = "";
        $obj->end_sn        = "";
        $lang       = $this->app_language->app_lang();

        if($this->input->get('start') !== ""){
            $obj->start	= $this->format_date($this->input->get('start'));
        }
        if($this->input->get('end') !== ""){
            $obj->end	    = $this->format_date($this->input->get('end'));
        }
        if($this->input->get('test_start') !== ""){
            $obj->test_start	= $this->format_date($this->input->get('test_start'));
        }
        if($this->input->get('test_end') !== ""){
            $obj->test_end	    = $this->format_date($this->input->get('test_end'));
        }
        if($this->input->get('start_sample_number') !== "" || $this->input->get('end_sample_number') !== ""){
            $obj->start_sn = $this->input->get('start_sample_number');
            $obj->end_sn  = $this->input->get('end_sample_number');
        }
        if($this->input->get('sample_source') !== ""){
            $obj->sample_source	= $this->input->get('sample_source');
        }
        if($this->input->get('test_name') !== ""){
            $test_name_str     = $this->input->get('test_name');
            $r              = (explode(",",$test_name_str));
            $str_testname   = '';
            for($i = 0; $i < count($r); $i++){
                $str_testname.= " test.\"ID\" = ".$r[$i]." OR";
            }
            $str_testname = substr($str_testname,0,strlen($str_testname)-2);
            $obj->test_name  = $str_testname;
        }
        if($this->input->get('lab_id') !== ""){
            $obj->lab_id	    = $this->input->get('lab_id');
            $lab_code = $obj->lab_id;
            $lab = $this->laboratory_model->get_laboratory($lab_code, FALSE);
            $lab_name = ($lang == 'kh') ? $lab[0]->name_kh :  strtoupper($lab[0]->name_en);
        }else{
            $lab_code = $this->session->userdata('laboratory')->labID;
            $lab_name = CamlisSession::getLabSession("name_".$lang);
        }

        if($this->input->get('test_result') !== ""){            
            $obj->test_result	    = $this->input->get('test_result');
        }

        //$result_list        = $this->report_model->covid_table($obj,$for_research);
                
        $user       = $this->user_model-> get_user($this->session->userdata("username"));
        $report_by  = $user[0]['fullname'];
        

        if($lang == 'en'){
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
        }else{
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
        }
        try {
            ob_start();
            $this->load->library('phptoexcel');
            $objPHPExcel    = new PHPExcel();


            $n_sheet = 1;
            $SHEET_ARR = array();
            $table_columns  = array(_t('sample.order_number'), _t('patient.patient_id'), _t('patient.name'), _t('global.patient_age'), _t('global.patient_gender'), _t('patient.nationality'),_t('patient.passport_no') , _t('patient.flight_number') , _t('sample.sample_number') , _t('sample.reason_for_testing') , _t('global.sample_source'), _t('sample.collect_dt') , _t('sample.receive_dt'), _t('sample.test_date') ,_t('sample.result')." SARS-CoV-2", _t('sample.number_of_sample'));
            
            $border_style = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $headerStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 13,
                    'name'  => 'Khmer OS Muol Light'
                ));
            $header1StyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Muol Light'
            ));
            $subheaderStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Bokor'
            ));
            $columnStyleArray = array(
                'font'  => array(
                    'color' => array('rgb' => '000000'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Siemreap'
            ));
            
            $bodyStyleArray = array(
                'font'  => array(
                        'color' => array('rgb' => '000000'),
                        'size'  => 11,
                        'name'  => 'Khmer OS Siemreap'
                    ));
            
            $current_date           = date('d/M/Y');
            $total_arr              = array();
            $total_by_sample_source = array();
            
            $reason_for_testing_total   = array(); //05052021
            $data_by_sample_source      = array(); //10052021
            if($for_research == "all"){
                $file_title = "បង្ហាញទាំងអស់";
                foreach($FOR_RESEARCH_ARR as $index => $value){
                    $result_list    = $this->report_model->covid_table($obj,$index);
                    if($result_list){
                        $objPHPExcel->createSheet();
                        $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                        $objDrawing     = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('MOH');
                        $objDrawing->setDescription('ក្រសួងសុខាភិបាល');           
                        $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                        //$objDrawing->setPath(site_url().'assets/camlis/images/moh-logo.png');
                        $objDrawing->setOffsetX(5);  // setOffsetX works properly
                        $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                        $objDrawing->setCoordinates('B2');
                        $objDrawing->setHeight(36); // logo height
                        $objDrawing->setWorksheet($sheet);
                        $title = ($index == 0) ? _t('sample.none_reason_selected') : $value;
                        $sheet->setTitle($title);
                        $column         = 0;
                        
                        //Set Header
                        $sheet->mergeCells('A1:P1')->setCellValue('A1',_t('global.kingdom'));
                        $sheet->mergeCells('A2:P2')->setCellValue('A2',_t('global.nation'));
                        $sheet->mergeCells('A3:P3')->setCellValue('A3',$lab_name);
                        $sheet->mergeCells('C4:O4')->setCellValue('C4', _t('report.covid19_laboratory_result')."(".$title.")");
                        $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                               
                        $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                        $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                        $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                        $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                        $sheet->mergeCells('A5:P5')->setCellValue('A5', _t('report.date')." " .$current_date);
                        $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                        foreach($table_columns as $field)
                        {
                            $sheet->setCellValueByColumnAndRow($column, 6, $field);
                            $column++;
                        }
                        $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                        $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                        $sheet->getStyle('A5')->getFont()->setBold(true);
                        $num_row            = 7;
                        $number             = 1;
                        $number_male        = 0;
                        $number_female      = 0;
                        $number_positive    = 0;
                        $number_negative    = 0;
                        $number_invalid     = 0;
                        $number_pending     = 0;

                        $reason_for_testing_total[$value] = array();

                        foreach($result_list as $key => $row){
                            $nationality        = '';
                            $passport_number    = '';
                            $flight_number      = "";
                            $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $title;
                            
                            $patient            = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                            if($patient){
                                $nationality    = $patient['nationality_en'];
                                $passport_number = $patient['passport_number'];
                                $flight_number  = $patient['flight_number'];
                                // save it for group of sample source
                                $row['nationality'] = $nationality;
                                $row['passport_number'] = $passport_number;
                                $row['flight_number'] = $flight_number;
                            }
                            // Request to REMOVE "SRP-" from siem reap covid lab
                            // 18-03-2021
                            $patient_code = $row['patient_code'];
                            if($lab_code == 61){
                                $patient_code = str_replace("SRP-","",$row['patient_code']);
                            }
                            $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                            $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                            $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                            $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                            $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                            $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                            $sheet->setCellValueByColumnAndRow(7, $num_row, $flight_number);
                            $sheet->setCellValueByColumnAndRow(8, $num_row, $row['sample_number']);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $reason);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $row['sample_source']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $row['collected_date']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $row['received_date']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $row['test_date']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $row['result_organism']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, $number_of_sample);
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('D'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('E'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('H'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('K'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('L'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('M'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('N'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('O'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('P'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            $number++;
                            
                            if($row['patient_gender'] == 'M'){
                                $number_male++;    
                            }
                            if($row['patient_gender'] == 'F'){
                                $number_female++;    
                            }
                            if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                $number_positive++;    
                            }
                            if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                $number_negative++;
                            }
                            if($row['result_organism'] == 'Invalid'){
                                $number_invalid++;
                            }
                            if($row['result_organism'] == ''){
                                $number_pending++;
                            }
                            // number of sample by sample source
                            // added 28-06-2021
                            $exist = false;
                            foreach($total_by_sample_source as $key => $source){
                                if($key == $row['sample_source']){
                                    $exist = true;
                                    break;
                                }
                            }
                            
                            // if sample source does not exit
                            if(!$exist){
                                $total_by_sample_source[$row['sample_source']] = array(
                                    "number_male"       => 0 ,
                                    "number_female"     => 0 ,
                                    "number_positive"   => 0 ,
                                    "number_negative"   => 0 ,
                                    "number_invalid"    => 0 ,
                                    "number_pending"    => 0
                                );
                                if($row['patient_gender'] == 'M'){
                                    $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                                }
                                if($row['patient_gender'] == 'F'){
                                    $total_by_sample_source[$row['sample_source']]["number_female"]++;
                                }
                                if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                                }
                                if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                                }
                                if($row['result_organism'] == 'Invalid'){
                                    $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                                }
                                if($row['result_organism'] == ''){
                                    $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                                }
                            }else{
                                if($row['patient_gender'] == 'M'){
                                    $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                                }
                                if($row['patient_gender'] == 'F'){
                                    $total_by_sample_source[$row['sample_source']]["number_female"]++;
                                }
                                if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                                }
                                if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                                }
                                if($row['result_organism'] == 'Invalid'){
                                    $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                                }
                                if($row['result_organism'] == ''){
                                    $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                                }
                            }
                            // end
                            //05052021
                            /**
                             * 05052021
                             * Table sample source group by reason for testing
                             */
                            $is_sample_source_exist = false;
                            foreach($reason_for_testing_total[$value] as $key => $source){
                                if($key == $row['sample_source']){
                                    $is_sample_source_exist = true;
                                    break;
                                }
                            }
                            if(!$is_sample_source_exist){
                                $reason_for_testing_total[$value][$row['sample_source']] = array(
                                    "number_male"       => 0 ,
                                    "number_female"     => 0 ,
                                    "number_positive"   => 0 ,
                                    "number_negative"   => 0 ,
                                    "number_invalid"    => 0 ,  
                                    "number_pending"    => 0
                                );
                                if(trim($row['patient_gender']) == 'M'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                                }
                                if(trim($row['patient_gender']) == 'F'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                                }
                                if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                                }
                                if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                                }

                                if(trim($row['result_organism']) == 'Invalid'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                                }
                                if(trim($row['result_organism']) == ''){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                                }

                            }else{
                                if(trim($row['patient_gender']) == 'M'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                                }
                                if(trim($row['patient_gender']) == 'F'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                                }
                                if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                                }
                                if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                                }

                                if(trim($row['result_organism']) == 'Invalid'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                                }
                                if(trim($row['result_organism']) == ''){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                                }
                            }
                            // End

                            /**
                             * Added: 10-05-2021
                             * Data by sample source
                             */
                            $sample_source_existent = false;
                            
                            foreach($data_by_sample_source as $key => $source){
                                if($key == trim($row['sample_source'])){
                                    $sample_source_existent = true;
                                    break;
                                }
                            }                            
                            if(!$sample_source_existent){
                                $data_by_sample_source[$row['sample_source']] = array();
                                $data_by_sample_source[$row['sample_source']][] = $row;
                            }else{
                                $data_by_sample_source[$row['sample_source']][] = $row;
                            }
                            
                            /** End */
                            
                        }
                        $total    = array(
                            'title'             => $title,
                            'total'             => count($result_list),
                            'number_male'       => $number_male,
                            'number_female'     => $number_female,
                            'number_positive'   => $number_positive,
                            'number_negative'   => $number_negative,
                            'number_invalid'    => $number_invalid,
                            'number_pending'    => $number_pending
                        );
                        $total_arr[] = $total;
                        //$sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                        $sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                        $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                        $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                        
                        $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                        
                        $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                        $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                        $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
        
                        $SHEET_ARR[$value] = $sheet;
                        $n_sheet++;
                    }
                    
                    // Auto size columns for each worksheet
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                        $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

                        $sheet = $objPHPExcel->getActiveSheet();
                        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        /** @var PHPExcel_Cell $cell */
                        foreach ($cellIterator as $cell) {
                            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                        }
                    }
                }


                /**
                 * Worksheet data by Sample Source
                 */
                //$n_sheet++;
                
                if(count($data_by_sample_source) > 0){
                
                    //if($this->session->userdata('roleid') == 1){
                        foreach($data_by_sample_source as $ind => $result){
                            $sample_source_name = $ind;
                            $objPHPExcel->createSheet();
                            $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                            $objDrawing     = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setName('MOH');
                            $objDrawing->setDescription('ក្រសួងសុខាភិបាល');           
                            $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                            $objDrawing->setOffsetX(5);  // setOffsetX works properly
                            $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                            $objDrawing->setCoordinates('B2');
                            $objDrawing->setHeight(36); // logo height
                            $objDrawing->setWorksheet($sheet);
                            $title = $sample_source_name;
                            
                            // sample source in Khmer Unicode
                            if(mb_strlen($title) > 31){
                                $title = mb_substr($title,0,15,"UTF-8");
                               // $title = mb_substr($title, 0 , 30);
                             // $invalidCharacters = $sheet->getInvalidCharacters();
                               // $title = str_replace($invalidCharacters, '', $title);
                            }
                            
                            $sheet->setTitle(strval($n_sheet));
                            //$sheet->setTitle($title);
                            $column         = 0;
                            
                            //Set Header
                            $sheet->mergeCells('A1:P1')->setCellValue('A1',_t('global.kingdom'));
                            $sheet->mergeCells('A2:P2')->setCellValue('A2',_t('global.nation'));
                            $sheet->mergeCells('A3:P3')->setCellValue('A3',$lab_name);
                            $sheet->mergeCells('C4:O4')->setCellValue('C4', _t('report.covid19_laboratory_result')." "._t('report.by_sample_source')." (".$sample_source_name.")");
                            $sheet->mergeCells('A4:O4')->setCellValue('A4', _t('global.moh'));
                            $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                                    
                            $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                            $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                            $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                            $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                            $sheet->mergeCells('A5:P5')->setCellValue('A5', _t('report.date')." " .$current_date);
                            $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                            foreach($table_columns as $field)
                            {
                                $sheet->setCellValueByColumnAndRow($column, 6, $field);
                                $column++;
                            }
                            $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                            $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                            $sheet->getStyle('A5')->getFont()->setBold(true);
                            $num_row = 7;
                            $number = 1;
                            
                            foreach($result as $key => $row){
                                $nationality        = '';
                                $passport_number    = '';
                                $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $row['reason_for_testing'];
                                                               
                                // Request to REMOVE "SRP-" from siem reap covid lab
                                // 18-03-2021
                                $patient_code = $row['patient_code'];
                                if($lab_code == 61){
                                    $patient_code = str_replace("SRP-","",$row['patient_code']);
                                }
                                $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                                $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                                $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                                $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                                $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                                $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                                $sheet->setCellValueByColumnAndRow(5, $num_row, $row['nationality']);
                                $sheet->setCellValueByColumnAndRow(6, $num_row, $row['passport_number']);
                                $sheet->setCellValueByColumnAndRow(7, $num_row, $row['flight_number']);
                                $sheet->setCellValueByColumnAndRow(8, $num_row, $row['sample_number']);
                                $sheet->setCellValueByColumnAndRow(9, $num_row, $reason);
                                $sheet->setCellValueByColumnAndRow(10, $num_row, $row['sample_source']);
                                $sheet->setCellValueByColumnAndRow(11, $num_row, $row['collected_date']);
                                $sheet->setCellValueByColumnAndRow(12, $num_row, $row['received_date']);
                                $sheet->setCellValueByColumnAndRow(13, $num_row, $row['test_date']);
                                $sheet->setCellValueByColumnAndRow(14, $num_row, $row['result_organism']);
                                $sheet->setCellValueByColumnAndRow(15, $num_row, $number_of_sample);
                                $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('D'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('E'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('H'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('K'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('L'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('M'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('N'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('O'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('P'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $column++;
                                $num_row++;
                                $number++;
                            }
                            $n_sheet++;
                            $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                            $sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                            $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                            $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                            
                            $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            
                            $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                            $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                            $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            }
                            // Auto size columns for each worksheet
                            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                                $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

                                $sheet = $objPHPExcel->getActiveSheet();
                                $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(true);
                               
                                foreach ($cellIterator as $cell) {
                                    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                                }
                            }
                    //    }
                    }
                   
                    //End 
            
                // Added 07 April 2021
                // create total sheet 
                if(count($total_arr) > 0){
                    $table_columns = array( _t('sample.order_number'), _t('sample.reason_for_testing'), _t('sample.total'));
                    $objPHPExcel->createSheet();
                    $sheet          = $objPHPExcel->setActiveSheetIndex(0);
                    $objDrawing     = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('MOH');
                    $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                    $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                    $objDrawing->setOffsetX(5);    // setOffsetX works properly
                    $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('B2');
                    $objDrawing->setHeight(36); // logo height
                    $objDrawing->setWorksheet($sheet);
                    $title = _t('sample.overall_result');
                    $sheet->setTitle($title);
                    $column         = 0;
                    
                    //Set Header
                    $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                    $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                    $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                    $sheet->mergeCells('C4:N4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$title.")");
                    $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                    $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                    $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                    $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                    $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                    $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                    $sheet->mergeCells('A5:P5')->setCellValue('A5',_t('report.date')." ".$current_date);
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A5")->applyFromArray($columnStyleArray);

                    // header 
                    $sheet->setCellValueByColumnAndRow(0, 6, _t('sample.order_number'));
                    $sheet->mergeCells('B6:I6')->setCellValue('B6',_t('sample.reason_for_testing'));
                    $sheet->setCellValueByColumnAndRow(9, 6, _t('global.female') );
                    $sheet->setCellValueByColumnAndRow(10, 6, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, 6, _t('sample.number_of_negative'));
                    $sheet->setCellValueByColumnAndRow(12, 6, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, 6, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, 6, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, 6, _t('sample.total'));
                    
                    $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                    $sheet->getStyle('A5')->getFont()->setBold(true);

                    $num_row = 7;
                    $number = 1;
                    
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    foreach($total_arr as $key => $row){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,$row['title']);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $row['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $row['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $row['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $row['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $row['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $row['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, $row['total']);
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;
                        $total_nb_female    += $row['number_female'];
                        $total_nb_male      += $row['number_male'];
                        $total_nb_negative  += $row['number_negative'];
                        $total_nb_positive  +=  $row['number_positive'];
                        $total_nb_invalid   +=  $row['number_invalid'];
                        $total_nb_pending   +=  $row['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    // End Grand total
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A6:O".($num_row - 1))->applyFromArray($border_style);

                    $num_row++;
                    $number = 1;
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $tbl2_row = $num_row; 
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,_t('sample.sample_source'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;

                    foreach($total_by_sample_source as $key => $source){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'. $num_row,$key);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;

                        $total_nb_female    += $source['number_female'];
                        $total_nb_male      += $source['number_male'];
                        $total_nb_negative  += $source['number_negative'];
                        $total_nb_positive  +=  $source['number_positive'];
                        $total_nb_invalid   +=  $source['number_invalid'];
                        $total_nb_pending   +=  $source['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    // End Grand total
                    
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);


                    /**
                     * 05052021
                     */
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $num_row++;
                    $tbl2_row           = $num_row;
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'.$num_row, _t('sample.reason_for_testing'));
                    $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'.$num_row, _t('sample.sample_source') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive') );
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid') );
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending') );
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;
                    $number = 1;
                    foreach($reason_for_testing_total as $label_reason => $res){
                        $mg = $num_row;
                        $len = 0;
                        foreach($res as $key => $source){
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'. $num_row,$label_reason);
                            $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'. $num_row,$key);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            //$number++;
    
                            $total_nb_female    += $source['number_female'];
                            $total_nb_male      += $source['number_male'];
                            $total_nb_negative  += $source['number_negative'];
                            $total_nb_positive  += $source['number_positive'];
                            $total_nb_invalid   += $source['number_invalid'];
                            $total_nb_pending   += $source['number_pending'];
                            $len++;
                        }
                        //$sheet->mergeCells('A'.$mg.':A'. ($mg + $len))->setCellValue('A'. $mg ,$number);
                        $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);
                        /*
                        if($len > 1){
                            $sheet->mergeCells('A'.$mg.':A'. ($mg + $len))->setCellValue('A'. $mg ,$number);
                            $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);
                            $sheet->getStyle('A'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );

                            $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        }else{
                            $sheet->setCellValueByColumnAndRow(0, ($num_row-1), $number);
                            $sheet->mergeCells('B'.($num_row-1).':E'.($num_row-1))->setCellValue('B'. ($num_row-1),$label_reason);
                        }
                        */
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        $number++;
                    }
                    

                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    // End Grand total
                    /**End */
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);

                    $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                    $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                    
                    $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    
                    $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                    $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                    $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                }
                
                //Remove the black worksheet
                /*
                $in = $objPHPExcel->getSheetByName('Worksheet 1');
                $sheetIndex = $objPHPExcel->getIndex($in);
                if($sheetIndex){
                    $objPHPExcel->removeSheetByIndex($wsIndex);
                }
                */
                $sheetIndex = $objPHPExcel->getIndex(
                    $objPHPExcel->getSheetByName('Worksheet 1')
                );
                $objPHPExcel->removeSheetByIndex($sheetIndex);
                $wsIndexStr = "".$sheetIndex;
                
            }else{
                /**
                 * 
                 * Save to only sheet
                 */

                $reason_for_testing = $FOR_RESEARCH_ARR[$for_research];
                $file_title         = $reason_for_testing;
                $result_list        = $this->report_model->covid_table($obj,$for_research);
                $sheet              = $objPHPExcel->setActiveSheetIndex(0);
                $sheet->setTitle($reason_for_testing);
                $objDrawing         = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('MOH');
                $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                $objDrawing->setOffsetX(5);    // setOffsetX works properly
                $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                $objDrawing->setCoordinates('B2');
                $objDrawing->setHeight(36); // logo height
                $objDrawing->setWorksheet($sheet);
                $column             = 0;
                $number             = 1;
                $number_male        = 0;
                $number_female      = 0;
                $number_positive    = 0;
                $number_negative    = 0;
                $number_invalid     = 0;
                $number_pending     = 0;

                $reason_for_testing_total[$for_research] = array();
                //Set Header
               $sheet->mergeCells('A1:P1')->setCellValue('A1',_t('global.kingdom'));
               $sheet->mergeCells('A2:P2')->setCellValue('A2',_t('global.nation'));
               $sheet->mergeCells('A3:P3')->setCellValue('A3',$lab_name);
               $sheet->mergeCells('C4:P4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$reason_for_testing.")");
               $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
               
               $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
               $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
               $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
               $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
               $sheet->mergeCells('A5:P5')->setCellValue('A5',"កាលបរិច្ឆេទ៖ ".$current_date);
               $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
               $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
               foreach($table_columns as $field)
               {
                    $sheet->setCellValueByColumnAndRow($column, 6, $field);
                    $column++;
               }
               $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
               $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle('A6:P6')->getFont()->setBold(true);
               $sheet->getStyle('A5')->getFont()->setBold(true);
               $num_row = 7;
               $number = 1;
                
                foreach($result_list as $key => $row){
                    $nationality        = '';
                    $passport_number    = '';
                    $reason             = $FOR_RESEARCH_ARR[$row['for_research']];
                    $value              = $reason; //05-07-2021
                    $patient            = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                    $flight_number      = "";
                    if($patient){
                        $nationality        = $patient['nationality_en'];
                        $passport_number    = $patient['passport_number'];
                        $flight_number      = $patient['flight_number'];
                    }
                    // Request to REMOVE "SRP-" from siem reap covid lab
                    // 18-03-2021
                    $patient_code = $row['patient_code'];
                    if($lab_code == 61){
                        $patient_code = str_replace("SRP-","",$row['patient_code']);
                    }
                    $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                    $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                    $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                    $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                    $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                    $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                    $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                    $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                    $sheet->setCellValueByColumnAndRow(7, $num_row, $flight_number);
                    $sheet->setCellValueByColumnAndRow(8, $num_row, $row['sample_number']);
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $reason);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $row['sample_source']);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $row['collected_date']);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $row['received_date']);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $row['test_date']);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $row['result_organism']);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, $number_of_sample);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('H'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('L'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('M'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('N'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('0'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('P'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $num_row++;
                    $number++;

                    if($row['patient_gender'] == 'M'){
                        $number_male++;    
                    }
                    if($row['patient_gender'] == 'F'){
                        $number_female++;    
                    }
                    if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                        $number_positive++;    
                    }
                    if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                        $number_negative++;
                    }
                    if($row['result_organism'] == 'Invalid'){
                        $number_invalid++;
                    }
                    if($row['result_organism'] == ''){
                        $number_pending++;
                    }
                    // number of sample by sample source
                    // added 28-06-2021
                    $exist = false;
                    foreach($total_by_sample_source as $key => $source){
                        if($key == $row['sample_source']){
                            $exist = true;
                            break;
                        }
                    }
                    
                    // if sample source does not exit
                    if(!$exist){
                        $total_by_sample_source[$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,
                            "number_pending"    => 0
                        );
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }else{
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // end
                    //05052021
                    /**
                     * 05052021
                     * Table sample source group by reason for testing
                     */
                    $is_sample_source_exist = false;
                    foreach($reason_for_testing_total[$value] as $key => $source){
                        if($key == $row['sample_source']){
                            $is_sample_source_exist = true;
                            break;
                        }
                    }
                    if(!$is_sample_source_exist){
                        $reason_for_testing_total[$value][$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,  
                            "number_pending"    => 0
                        );
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                        }

                    }else{
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // End

                    /**
                     * Added: 10-05-2021
                     * Data by sample source
                     */
                    $sample_source_existent = false;
                    
                    foreach($data_by_sample_source as $key => $source){
                        if($key == trim($row['sample_source'])){
                            $sample_source_existent = true;
                            break;
                        }
                    }                            
                    if(!$sample_source_existent){
                        $data_by_sample_source[$row['sample_source']] = array();
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }else{
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }
                    /** End */
                }                    
                $sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1),_t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);

                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1),_t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                /** Overal Result Sheet */
                $total    = array(
                    'title'             => $value,
                    'total'             => count($result_list),
                    'number_male'       => $number_male,
                    'number_female'     => $number_female,
                    'number_positive'   => $number_positive,
                    'number_negative'   => $number_negative,
                    'number_invalid'    => $number_invalid,
                    'number_pending'    => $number_pending
                );
                $total_arr[] = $total;

                // Added 07 April 2021
                // create total sheet 
                if(count($total_arr) > 0){
                    $table_columns = array( _t('sample.order_number'), _t('sample.reason_for_testing'), _t('sample.total'));
                    $objPHPExcel->createSheet();
                    $sheet          = $objPHPExcel->setActiveSheetIndex(1);
                    $objDrawing     = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('MOH');
                    $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                    $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                    $objDrawing->setOffsetX(5);    // setOffsetX works properly
                    $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('B2');
                    $objDrawing->setHeight(36); // logo height
                    $objDrawing->setWorksheet($sheet);
                    $title = _t('sample.overall_result');
                    $sheet->setTitle($title);
                    $column         = 0;
                    
                    //Set Header
                    $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                    $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                    $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                    $sheet->mergeCells('C4:N4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$title.")");
                    $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                    $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                    $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                    $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                    $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                    $sheet->mergeCells('A5:P5')->setCellValue('A5',_t('report.date')." ".$current_date);
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A5")->applyFromArray($columnStyleArray);

                    // header 
                    $sheet->setCellValueByColumnAndRow(0, 6, _t('sample.order_number'));
                    $sheet->mergeCells('B6:I6')->setCellValue('B6',_t('sample.reason_for_testing'));
                    $sheet->setCellValueByColumnAndRow(9, 6, _t('global.female') );
                    $sheet->setCellValueByColumnAndRow(10, 6, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, 6, _t('sample.number_of_negative'));
                    $sheet->setCellValueByColumnAndRow(12, 6, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, 6, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, 6, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, 6, _t('sample.total'));
                    
                    $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                    $sheet->getStyle('A5')->getFont()->setBold(true);

                    $num_row = 7;
                    $number  = 1;
                    
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    foreach($total_arr as $key => $row){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,$row['title']);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $row['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $row['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $row['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $row['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $row['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $row['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, $row['total']);
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;
                        $total_nb_female    += $row['number_female'];
                        $total_nb_male      += $row['number_male'];
                        $total_nb_negative  += $row['number_negative'];
                        $total_nb_positive  +=  $row['number_positive'];
                        $total_nb_invalid   +=  $row['number_invalid'];
                        $total_nb_pending   +=  $row['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A6:P".($num_row))->applyFromArray($border_style);
                    
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");
                    // End Grand total
                   
                    $num_row++;
                    $number = 1;
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $tbl2_row           = $num_row; 
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,_t('sample.sample_source'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));
                    
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;

                    foreach($total_by_sample_source as $key => $source){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'. $num_row,$key);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;

                        $total_nb_female    += $source['number_female'];
                        $total_nb_male      += $source['number_male'];
                        $total_nb_negative  += $source['number_negative'];
                        $total_nb_positive  +=  $source['number_positive'];
                        $total_nb_invalid   +=  $source['number_invalid'];
                        $total_nb_pending   +=  $source['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    $column++;
                    //$num_row++;
                    $number++;
                    // End Grand total                                        
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");
                    /**
                     * 05052021
                     */
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $num_row++;
                    $tbl2_row           = $num_row;
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'.$num_row, _t('sample.reason_for_testing'));
                    $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'.$num_row, _t('sample.sample_source') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive') );
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid') );
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending') );
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;
                    $number = 1;
                    foreach($reason_for_testing_total as $label_reason => $res){
                        $mg = $num_row;
                        $len = 0;
                        foreach($res as $key => $source){
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'. $num_row,$label_reason);
                            $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'. $num_row,$key);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            //$number++;
    
                            $total_nb_female    += $source['number_female'];
                            $total_nb_male      += $source['number_male'];
                            $total_nb_negative  += $source['number_negative'];
                            $total_nb_positive  += $source['number_positive'];
                            $total_nb_invalid   += $source['number_invalid'];
                            $total_nb_pending   += $source['number_pending'];
                            $len++;
                        }                        
                        $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        $number++;
                    }
                    
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");

                    // End Grand total
                    /**End */
                   // $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);
                    $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                    $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                    $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                    $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                    $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                }

                //$sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1),_t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);

                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1),_t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                /** */
            }                        
            $filename = "Sars_CoV2_".$lab_code."-".$file_title."-".$current_date;

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

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

    public function export_covid_report13102021(){        
        $this->app_language->load(array('global','patient','sample'));
        $this->load->model('report_model');
        $this->load->model('patient_model');
        $this->load->model('user_model');
        $this->load->model('laboratory_model');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 1200); // Zero means the script can run forever

        $obj		        = new stdClass();       
        $obj->start_time    = $this->input->get('start_time');
        $obj->end_time      = $this->input->get('end_time');
        $for_research       = $this->input->get('for_research');
        $obj->test_start    = "";
        $obj->test_end      = "";
        $obj->start_sn      = "";
        $obj->end_sn        = "";
        $lang       = $this->app_language->app_lang();

        if($this->input->get('start') !== ""){
            $obj->start	= $this->format_date($this->input->get('start'));
        }
        if($this->input->get('end') !== ""){
            $obj->end	    = $this->format_date($this->input->get('end'));
        }
        if($this->input->get('test_start') !== ""){
            $obj->test_start	= $this->format_date($this->input->get('test_start'));
        }
        if($this->input->get('test_end') !== ""){
            $obj->test_end	    = $this->format_date($this->input->get('test_end'));
        }
        if($this->input->get('start_sample_number') !== "" || $this->input->get('end_sample_number') !== ""){
            $obj->start_sn = $this->input->get('start_sample_number');
            $obj->end_sn  = $this->input->get('end_sample_number');
        }
        if($this->input->get('sample_source') !== ""){
            $obj->sample_source	= $this->input->get('sample_source');
        }
        if($this->input->get('test_name') !== ""){
            $test_name_str     = $this->input->get('test_name');
            $r              = (explode(",",$test_name_str));
            $str_testname   = '';
            for($i = 0; $i < count($r); $i++){
                $str_testname.= " test.\"ID\" = ".$r[$i]." OR";
            }
            $str_testname = substr($str_testname,0,strlen($str_testname)-2);
            $obj->test_name  = $str_testname;
        }

        if($this->input->get('lab_id') !== ""){
            $obj->lab_id    = $this->input->get('lab_id');
            //$lab_code       = $obj->lab_id;
            //$lab_code       = $obj->lab_id;
            $lab_code       = $this->session->userdata('laboratory')->labID;
            $lab            = $this->laboratory_model->get_laboratory($lab_code, FALSE);
            $lab_name       = ($lang == 'kh') ? $lab[0]->name_kh :  strtoupper($lab[0]->name_en);
            $labID          = $obj->lab_id;
        }else{
            $lab_code = $this->session->userdata('laboratory')->labID;
            $lab_name = CamlisSession::getLabSession("name_".$lang);
            $labID  = $obj->lab_id;
        }

        if($this->input->get('test_result') !== ""){            
            $obj->test_result	    = $this->input->get('test_result');
        }
        if($this->input->get('number_of_sample') !== 0){
            $obj->number_of_sample	    = $this->input->get('number_of_sample');
        }
        //$result_list        = $this->report_model->covid_table($obj,$for_research);
                
        $user       = $this->user_model-> get_user($this->session->userdata("username"));
        $report_by  = $user[0]['fullname'];
        
        if($lang == 'en'){
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
        }else{
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
        }
        try {
            ob_start();
            $this->load->library('phptoexcel');
            $objPHPExcel    = new PHPExcel();

            $n_sheet = 1;
            $SHEET_ARR = array();
            $table_columns  = array(_t('sample.order_number'), _t('patient.patient_id'), _t('patient.name'), _t('global.patient_age'), _t('global.patient_gender'), _t('patient.nationality'),_t('patient.passport_no') , _t('patient.flight_number'), _t('patient.date_of_arrival') , _t('sample.sample_number') , _t('sample.reason_for_testing') , _t('global.sample_source'), _t('sample.collect_dt') , _t('sample.receive_dt'), _t('sample.test_date') ,_t('sample.result')." SARS-CoV-2", _t('sample.number_of_sample'));
            
            $border_style = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $headerStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 13,
                    'name'  => 'Khmer OS Muol Light'
                ));
            $header1StyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Muol Light'
            ));
            $subheaderStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Bokor'
            ));
            $columnStyleArray = array(
                'font'  => array(
                    'color' => array('rgb' => '000000'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Siemreap'
            ));
            
            $bodyStyleArray = array(
                'font'  => array(
                        'color' => array('rgb' => '000000'),
                        'size'  => 11,
                        'name'  => 'Khmer OS Siemreap'
                    ));
            
            $current_date           = date('d/M/Y');
            $total_arr              = array();
            $total_by_sample_source = array();
            
            $reason_for_testing_total   = array(); //05052021
            $data_by_sample_source      = array(); //10052021
            if($for_research == "all"){
                $file_title = "បង្ហាញទាំងអស់";
                foreach($FOR_RESEARCH_ARR as $index => $value){
                    $result_list    = $this->report_model->covid_table($obj,$index,$labID);
                    if($result_list){
                        $objPHPExcel->createSheet();
                        $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                        $objDrawing     = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('MOH');
                        $objDrawing->setDescription('ក្រសួងសុខាភិបាល');           
                        $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                        //$objDrawing->setPath(site_url().'assets/camlis/images/moh-logo.png');
                        $objDrawing->setOffsetX(5);  // setOffsetX works properly
                        $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                        $objDrawing->setCoordinates('B2');
                        $objDrawing->setHeight(36); // logo height
                        $objDrawing->setWorksheet($sheet);
                        $title = ($index == 0) ? _t('sample.none_reason_selected') : $value;
                        $sheet->setTitle($title);
                        $column         = 0;
                        
                        //Set Header
                        $sheet->mergeCells('A1:Q1')->setCellValue('A1',_t('global.kingdom'));
                        $sheet->mergeCells('A2:Q2')->setCellValue('A2',_t('global.nation'));
                        $sheet->mergeCells('A3:Q3')->setCellValue('A3',$lab_name);
                        $sheet->mergeCells('C4:O4')->setCellValue('C4', _t('report.covid19_laboratory_result')."(".$title.")");
                        $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                               
                        $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                        $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                        $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                        $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                        $sheet->mergeCells('A5:Q5')->setCellValue('A5', _t('report.date')." " .$current_date);
                        $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                        foreach($table_columns as $field)
                        {
                            $sheet->setCellValueByColumnAndRow($column, 6, $field);
                            $column++;
                        }
                        $sheet->getStyle("A6:Q6")->applyFromArray($columnStyleArray);
                        $sheet->getStyle('A6:Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A6:Q6')->getFont()->setBold(true);
                        $sheet->getStyle('A5')->getFont()->setBold(true);
                        $num_row            = 7;
                        $number             = 1;
                        $number_male        = 0;
                        $number_female      = 0;
                        $number_positive    = 0;
                        $number_negative    = 0;
                        $number_invalid     = 0;
                        $number_pending     = 0;

                        $reason_for_testing_total[$value] = array();

                        foreach($result_list as $key => $row){
                            $nationality        = '';
                            $passport_number    = '';
                            $flight_number      = "";
                            $date_arrival       = "";
                            $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $title;
                            
                            $patient            = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                            if($patient){
                                $nationality    = $patient['nationality_en'];
                                $passport_number = $patient['passport_number'];
                                $flight_number  = $patient['flight_number'];
                                $date_arrival = $patient['date_arrival'];
                                // save it for group of sample source
                                $row['nationality'] = $nationality;
                                $row['passport_number'] = $passport_number;
                                $row['flight_number'] = $flight_number;
                                $row['date_arrival'] = $date_arrival;
                            }
                            // Request to REMOVE "SRP-" from siem reap covid lab
                            // 18-03-2021
                            $patient_code = $row['patient_code'];
                            if($lab_code == 61){
                                $patient_code = str_replace("SRP-","",$row['patient_code']);
                            }
                            $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                            $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                            $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                            $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                            $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                            $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                            $sheet->setCellValueByColumnAndRow(7, $num_row, $flight_number);
                            $sheet->setCellValueByColumnAndRow(8, $num_row, $date_arrival);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_number']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $reason);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $row['sample_source']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $row['collected_date']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $row['received_date']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $row['test_date']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, $row['result_organism']);
                            $sheet->setCellValueByColumnAndRow(16, $num_row, $number_of_sample);
                            $sheet->getStyle('A'.$num_row.':Q'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('B'.$num_row.':C'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $column++;
                            $num_row++;
                            $number++;
                            
                            if($row['patient_gender'] == 'M'){
                                $number_male++;    
                            }
                            if($row['patient_gender'] == 'F'){
                                $number_female++;    
                            }
                            if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                $number_positive++;    
                            }
                            if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                $number_negative++;
                            }
                            if($row['result_organism'] == 'Invalid'){
                                $number_invalid++;
                            }
                            if($row['result_organism'] == ''){
                                $number_pending++;
                            }
                            // number of sample by sample source
                            // added 28-06-2021
                            $exist = false;
                            foreach($total_by_sample_source as $key => $source){
                                if($key == $row['sample_source']){
                                    $exist = true;
                                    break;
                                }
                            }
                            
                            // if sample source does not exit
                            if(!$exist){
                                $total_by_sample_source[$row['sample_source']] = array(
                                    "number_male"       => 0 ,
                                    "number_female"     => 0 ,
                                    "number_positive"   => 0 ,
                                    "number_negative"   => 0 ,
                                    "number_invalid"    => 0 ,
                                    "number_pending"    => 0
                                );
                                if($row['patient_gender'] == 'M'){
                                    $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                                }
                                if($row['patient_gender'] == 'F'){
                                    $total_by_sample_source[$row['sample_source']]["number_female"]++;
                                }
                                if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                                }
                                if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                                }
                                if($row['result_organism'] == 'Invalid'){
                                    $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                                }
                                if($row['result_organism'] == ''){
                                    $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                                }
                            }else{
                                if($row['patient_gender'] == 'M'){
                                    $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                                }
                                if($row['patient_gender'] == 'F'){
                                    $total_by_sample_source[$row['sample_source']]["number_female"]++;
                                }
                                if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                                }
                                if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                                }
                                if($row['result_organism'] == 'Invalid'){
                                    $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                                }
                                if($row['result_organism'] == ''){
                                    $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                                }
                            }
                            // end
                            //05052021
                            /**
                             * 05052021
                             * Table sample source group by reason for testing
                             */
                            $is_sample_source_exist = false;
                            foreach($reason_for_testing_total[$value] as $key => $source){
                                if($key == $row['sample_source']){
                                    $is_sample_source_exist = true;
                                    break;
                                }
                            }
                            if(!$is_sample_source_exist){
                                $reason_for_testing_total[$value][$row['sample_source']] = array(
                                    "number_male"       => 0 ,
                                    "number_female"     => 0 ,
                                    "number_positive"   => 0 ,
                                    "number_negative"   => 0 ,
                                    "number_invalid"    => 0 ,  
                                    "number_pending"    => 0
                                );
                                if(trim($row['patient_gender']) == 'M'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                                }
                                if(trim($row['patient_gender']) == 'F'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                                }
                                if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                                }
                                if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                                }

                                if(trim($row['result_organism']) == 'Invalid'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                                }
                                if(trim($row['result_organism']) == ''){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                                }

                            }else{
                                if(trim($row['patient_gender']) == 'M'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                                }
                                if(trim($row['patient_gender']) == 'F'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                                }
                                if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                                }
                                if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                                }

                                if(trim($row['result_organism']) == 'Invalid'){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                                }
                                if(trim($row['result_organism']) == ''){
                                    $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                                }
                            }
                            // End

                            /**
                             * Added: 10-05-2021
                             * Data by sample source
                             */
                            $sample_source_existent = false;
                            
                            foreach($data_by_sample_source as $key => $source){
                                if($key == trim($row['sample_source'])){
                                    $sample_source_existent = true;
                                    break;
                                }
                            }                            
                            if(!$sample_source_existent){
                                $data_by_sample_source[$row['sample_source']] = array();
                                $data_by_sample_source[$row['sample_source']][] = $row;
                            }else{
                                $data_by_sample_source[$row['sample_source']][] = $row;
                            }
                            /** End */
                        }
                        $total    = array(
                            'title'             => $title,
                            'total'             => count($result_list),
                            'number_male'       => $number_male,
                            'number_female'     => $number_female,
                            'number_positive'   => $number_positive,
                            'number_negative'   => $number_negative,
                            'number_invalid'    => $number_invalid,
                            'number_pending'    => $number_pending
                        );
                        $total_arr[] = $total;
                        //$sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                        $sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                        $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                        $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                        
                        $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                        
                        $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                        $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                        $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
        
                        $SHEET_ARR[$value] = $sheet;
                        $n_sheet++;
                    }
                    // Auto size columns for each worksheet
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                        $sheet = $objPHPExcel->getActiveSheet();
                        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        /** @var PHPExcel_Cell $cell */
                        foreach ($cellIterator as $cell) {
                            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                        }
                    }
                }
                /**
                 * Worksheet data by Sample Source
                 */
                //$n_sheet++;
                if(count($data_by_sample_source) > 0){
                
                    //if($this->session->userdata('roleid') == 1){
                        foreach($data_by_sample_source as $ind => $result){
                            $sample_source_name = $ind;
                            $objPHPExcel->createSheet();
                            $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                            $objDrawing     = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setName('MOH');
                            $objDrawing->setDescription('ក្រសួងសុខាភិបាល');           
                            $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                            $objDrawing->setOffsetX(5);  // setOffsetX works properly
                            $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                            $objDrawing->setCoordinates('B2');
                            $objDrawing->setHeight(36); // logo height
                            $objDrawing->setWorksheet($sheet);
                            $title = $sample_source_name;
                            
                            // sample source in Khmer Unicode
                            if(mb_strlen($title) > 31){
                                $title = mb_substr($title,0,15,"UTF-8");
                               // $title = mb_substr($title, 0 , 30);
                             // $invalidCharacters = $sheet->getInvalidCharacters();
                               // $title = str_replace($invalidCharacters, '', $title);
                            }
                            
                            $sheet->setTitle(strval($n_sheet));
                            //$sheet->setTitle($title);
                            $column         = 0;
                            
                            //Set Header
                            $sheet->mergeCells('A1:P1')->setCellValue('A1',_t('global.kingdom'));
                            $sheet->mergeCells('A2:P2')->setCellValue('A2',_t('global.nation'));
                            $sheet->mergeCells('A3:P3')->setCellValue('A3',$lab_name);
                            $sheet->mergeCells('C4:O4')->setCellValue('C4', _t('report.covid19_laboratory_result')." "._t('report.by_sample_source')." (".$sample_source_name.")");
                            $sheet->mergeCells('A4:O4')->setCellValue('A4', _t('global.moh'));
                            $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                            $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                            $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                            $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                            $sheet->mergeCells('A5:P5')->setCellValue('A5', _t('report.date')." " .$current_date);
                            $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                            foreach($table_columns as $field)
                            {
                                $sheet->setCellValueByColumnAndRow($column, 6, $field);
                                $column++;
                            }
                            $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                            $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                            $sheet->getStyle('A5')->getFont()->setBold(true);
                            $num_row = 7;
                            $number = 1;
                            
                            foreach($result as $key => $row){
                                $nationality        = '';
                                $passport_number    = '';
                                $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $row['reason_for_testing'];
                                                               
                                // Request to REMOVE "SRP-" from siem reap covid lab
                                // 18-03-2021
                                $patient_code = $row['patient_code'];
                                if($lab_code == 61){
                                    $patient_code = str_replace("SRP-","",$row['patient_code']);
                                }
                                $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                                $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                                $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                                $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                                $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                                $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                                $sheet->setCellValueByColumnAndRow(5, $num_row, $row['nationality']);
                                $sheet->setCellValueByColumnAndRow(6, $num_row, $row['passport_number']);
                                $sheet->setCellValueByColumnAndRow(7, $num_row, $row['flight_number']);
                                $sheet->setCellValueByColumnAndRow(8, $num_row, $row['date_arrival']);
                                $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_number']);
                                $sheet->setCellValueByColumnAndRow(10, $num_row, $reason);
                                $sheet->setCellValueByColumnAndRow(11, $num_row, $row['sample_source']);
                                $sheet->setCellValueByColumnAndRow(12, $num_row, $row['collected_date']);
                                $sheet->setCellValueByColumnAndRow(13, $num_row, $row['received_date']);
                                $sheet->setCellValueByColumnAndRow(14, $num_row, $row['test_date']);
                                $sheet->setCellValueByColumnAndRow(15, $num_row, $row['result_organism']);
                                $sheet->setCellValueByColumnAndRow(16, $num_row, $number_of_sample);
                                $sheet->getStyle('A'.$num_row.':Q'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle('B'.$num_row.':C'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);                                
                                $column++;
                                $num_row++;
                                $number++;
                            }
                            $n_sheet++;
                            
                            $sheet->mergeCells('K'.($num_row + 1).':Q'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                            $sheet->mergeCells('K'.($num_row + 2).':Q'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                            
                            $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            
                            $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                            $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                            $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            
                            }
                            // Auto size columns for each worksheet
                            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                                $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                                $sheet = $objPHPExcel->getActiveSheet();
                                $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(true);
                                foreach ($cellIterator as $cell) {
                                    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                                }
                            }
                    //    }
                    }
                    //End 
                // Added 06-07-2021
                // create total sheet 
                if(count($total_arr) > 0){
                    $table_columns = array( _t('sample.order_number'), _t('sample.reason_for_testing'), _t('sample.total'));
                    $objPHPExcel->createSheet();
                    $sheet          = $objPHPExcel->setActiveSheetIndex(0);
                    $objDrawing     = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('MOH');
                    $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                    $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                    $objDrawing->setOffsetX(5);    // setOffsetX works properly
                    $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('B2');
                    $objDrawing->setHeight(36); // logo height
                    $objDrawing->setWorksheet($sheet);
                    $title = _t('sample.overall_result');
                    $sheet->setTitle($title);
                    $column         = 0;
                    
                    //Set Header
                    $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                    $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                    $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                    $sheet->mergeCells('C4:N4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$title.")");
                    $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                    $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                    $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                    $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                    $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                    $sheet->mergeCells('A5:Q5')->setCellValue('A5',_t('report.date')." ".$current_date);
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A5")->applyFromArray($columnStyleArray);

                    // header 
                    $sheet->setCellValueByColumnAndRow(0, 6, _t('sample.order_number'));
                    $sheet->mergeCells('B6:I6')->setCellValue('B6',_t('sample.reason_for_testing'));
                    $sheet->setCellValueByColumnAndRow(9, 6, _t('global.female') );
                    $sheet->setCellValueByColumnAndRow(10, 6, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, 6, _t('sample.number_of_negative'));
                    $sheet->setCellValueByColumnAndRow(12, 6, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, 6, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, 6, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, 6, _t('sample.total'));
                    
                    $sheet->getStyle("A6:Q6")->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A6:Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:Q6')->getFont()->setBold(true);
                    $sheet->getStyle('A5')->getFont()->setBold(true);

                    $num_row = 7;
                    $number = 1;
                    
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    foreach($total_arr as $key => $row){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,$row['title']);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $row['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $row['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $row['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $row['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $row['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $row['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, $row['total']);
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;
                        $total_nb_female    += $row['number_female'];
                        $total_nb_male      += $row['number_male'];
                        $total_nb_negative  += $row['number_negative'];
                        $total_nb_positive  +=  $row['number_positive'];
                        $total_nb_invalid   +=  $row['number_invalid'];
                        $total_nb_pending   +=  $row['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    // End Grand total
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A6:O".($num_row - 1))->applyFromArray($border_style);

                    $num_row++;
                    $number = 1;
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $tbl2_row = $num_row; 
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,_t('sample.sample_source'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;

                    foreach($total_by_sample_source as $key => $source){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'. $num_row,$key);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;

                        $total_nb_female    += $source['number_female'];
                        $total_nb_male      += $source['number_male'];
                        $total_nb_negative  += $source['number_negative'];
                        $total_nb_positive  +=  $source['number_positive'];
                        $total_nb_invalid   +=  $source['number_invalid'];
                        $total_nb_pending   +=  $source['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    // End Grand total
                    
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);

                    /**
                     * 05052021
                     */
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $num_row++;
                    $tbl2_row           = $num_row;
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'.$num_row, _t('sample.reason_for_testing'));
                    $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'.$num_row, _t('sample.sample_source') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive') );
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid') );
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending') );
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;
                    $number = 1;
                    foreach($reason_for_testing_total as $label_reason => $res){
                        $mg = $num_row;
                        $len = 0;
                        foreach($res as $key => $source){
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'. $num_row,$label_reason);
                            $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'. $num_row,$key);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            //$number++;
    
                            $total_nb_female    += $source['number_female'];
                            $total_nb_male      += $source['number_male'];
                            $total_nb_negative  += $source['number_negative'];
                            $total_nb_positive  += $source['number_positive'];
                            $total_nb_invalid   += $source['number_invalid'];
                            $total_nb_pending   += $source['number_pending'];
                            $len++;
                        }
                        //$sheet->mergeCells('A'.$mg.':A'. ($mg + $len))->setCellValue('A'. $mg ,$number);
                        $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);                        
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        $number++;
                    }
                    
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    // End Grand total
                    /**End */
                    $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);

                    $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                    $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                    
                    $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    
                    $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                    $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                    $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                }
                
                //Remove the black worksheet                
                $sheetIndex = $objPHPExcel->getIndex(
                    $objPHPExcel->getSheetByName('Worksheet 1')
                );
                $objPHPExcel->removeSheetByIndex($sheetIndex);
                $wsIndexStr = "".$sheetIndex;
                
            }else{
                /**
                 * 
                 * Save One sheet
                 */

                $reason_for_testing = $FOR_RESEARCH_ARR[$for_research];
                $file_title         = $reason_for_testing;
                $result_list        = $this->report_model->covid_table($obj,$for_research);
                $sheet              = $objPHPExcel->setActiveSheetIndex(0);
                $sheet->setTitle($reason_for_testing);
                $objDrawing         = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('MOH');
                $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                $objDrawing->setOffsetX(5);    // setOffsetX works properly
                $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                $objDrawing->setCoordinates('B2');
                $objDrawing->setHeight(36); // logo height
                $objDrawing->setWorksheet($sheet);
                $column             = 0;
                $number             = 1;
                $number_male        = 0;
                $number_female      = 0;
                $number_positive    = 0;
                $number_negative    = 0;
                $number_invalid     = 0;
                $number_pending     = 0;

                $reason_for_testing_total[$for_research] = array();
                //Set Header
               $sheet->mergeCells('A1:Q1')->setCellValue('A1',_t('global.kingdom'));
               $sheet->mergeCells('A2:Q2')->setCellValue('A2',_t('global.nation'));
               $sheet->mergeCells('A3:Q3')->setCellValue('A3',$lab_name);
               $sheet->mergeCells('C4:Q4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$reason_for_testing.")");
               $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
               
               $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
               $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
               $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
               $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
               $sheet->mergeCells('A5:Q5')->setCellValue('A5',"កាលបរិច្ឆេទ៖ ".$current_date);
               $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
               $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
               foreach($table_columns as $field)
               {
                    $sheet->setCellValueByColumnAndRow($column, 6, $field);
                    $column++;
               }
               $sheet->getStyle("A6:Q6")->applyFromArray($columnStyleArray);
               $sheet->getStyle('A6:Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle('A6:Q6')->getFont()->setBold(true);
               $sheet->getStyle('A5')->getFont()->setBold(true);
               $num_row = 7;
               $number = 1;
                
                foreach($result_list as $key => $row){
                    $nationality        = '';
                    $passport_number    = '';
                    $reason             = $FOR_RESEARCH_ARR[$row['for_research']];
                    $value              = $reason; //05-07-2021
                    $patient            = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                    $flight_number      = "";
                    $date_arrival       = "";
                    if($patient){
                        $nationality        = $patient['nationality_en'];
                        $passport_number    = $patient['passport_number'];
                        $flight_number      = $patient['flight_number'];
                        $date_arrival       = $patient['date_arrival'];
                    }
                    // Request to REMOVE "SRP-" from siem reap covid lab
                    // 18-03-2021
                    $patient_code = $row['patient_code'];
                    if($lab_code == 61){
                        $patient_code = str_replace("SRP-","",$row['patient_code']);
                    }
                    $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                    $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                    $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                    $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                    $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                    $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                    $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                    $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                    $sheet->setCellValueByColumnAndRow(7, $num_row, $flight_number);
                    $sheet->setCellValueByColumnAndRow(8, $num_row, $date_arrival);
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_number']);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $reason);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $row['sample_source']);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $row['collected_date']);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $row['received_date']);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $row['test_date']);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, $row['result_organism']);
                    $sheet->setCellValueByColumnAndRow(16, $num_row, $number_of_sample);
                    $sheet->getStyle('A'.$num_row.':Q'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B'.$num_row.':C'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $num_row++;
                    $number++;

                    if($row['patient_gender'] == 'M'){
                        $number_male++;    
                    }
                    if($row['patient_gender'] == 'F'){
                        $number_female++;    
                    }
                    if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                        $number_positive++;    
                    }
                    if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                        $number_negative++;
                    }
                    if($row['result_organism'] == 'Invalid'){
                        $number_invalid++;
                    }
                    if($row['result_organism'] == ''){
                        $number_pending++;
                    }
                    // number of sample by sample source
                    // added 28-06-2021
                    $exist = false;
                    foreach($total_by_sample_source as $key => $source){
                        if($key == $row['sample_source']){
                            $exist = true;
                            break;
                        }
                    }
                    
                    // if sample source does not exit
                    if(!$exist){
                        $total_by_sample_source[$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,
                            "number_pending"    => 0
                        );
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }else{
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // end
                    //05052021
                    /**
                     * 05052021
                     * Table sample source group by reason for testing
                     */
                    $is_sample_source_exist = false;
                    foreach($reason_for_testing_total[$value] as $key => $source){
                        if($key == $row['sample_source']){
                            $is_sample_source_exist = true;
                            break;
                        }
                    }
                    if(!$is_sample_source_exist){
                        $reason_for_testing_total[$value][$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,  
                            "number_pending"    => 0
                        );
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                        }

                    }else{
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // End

                    /**
                     * Added: 10-05-2021
                     * Data by sample source
                     */
                    $sample_source_existent = false;
                    
                    foreach($data_by_sample_source as $key => $source){
                        if($key == trim($row['sample_source'])){
                            $sample_source_existent = true;
                            break;
                        }
                    }                            
                    if(!$sample_source_existent){
                        $data_by_sample_source[$row['sample_source']] = array();
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }else{
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }
                    /** End */
                }                    
                $sheet->getStyle("A6:Q".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':Q'.($num_row + 1))->setCellValue('K'.($num_row + 1),_t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':Q'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);

                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1),_t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                /** Overal Result Sheet */
                $total    = array(
                    'title'             => $value,
                    'total'             => count($result_list),
                    'number_male'       => $number_male,
                    'number_female'     => $number_female,
                    'number_positive'   => $number_positive,
                    'number_negative'   => $number_negative,
                    'number_invalid'    => $number_invalid,
                    'number_pending'    => $number_pending
                );
                $total_arr[] = $total;

                // Added 07 April 2021
                // create total sheet 
                if(count($total_arr) > 0){
                    $table_columns = array( _t('sample.order_number'), _t('sample.reason_for_testing'), _t('sample.total'));
                    $objPHPExcel->createSheet();
                    $sheet          = $objPHPExcel->setActiveSheetIndex(1);
                    $objDrawing     = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('MOH');
                    $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                    $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png');
                    $objDrawing->setOffsetX(5);    // setOffsetX works properly
                    $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('B2');
                    $objDrawing->setHeight(36); // logo height
                    $objDrawing->setWorksheet($sheet);
                    $title = _t('sample.overall_result');
                    $sheet->setTitle($title);
                    $column         = 0;
                    
                    //Set Header
                    $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                    $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                    $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                    $sheet->mergeCells('C4:N4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$title.")");
                    $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                    $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                    $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                    $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                    $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                    $sheet->mergeCells('A5:P5')->setCellValue('A5',_t('report.date')." ".$current_date);
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A5")->applyFromArray($columnStyleArray);

                    // header 
                    $sheet->setCellValueByColumnAndRow(0, 6, _t('sample.order_number'));
                    $sheet->mergeCells('B6:I6')->setCellValue('B6',_t('sample.reason_for_testing'));
                    $sheet->setCellValueByColumnAndRow(9, 6, _t('global.female') );
                    $sheet->setCellValueByColumnAndRow(10, 6, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, 6, _t('sample.number_of_negative'));
                    $sheet->setCellValueByColumnAndRow(12, 6, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, 6, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, 6, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, 6, _t('sample.total'));
                    
                    $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                    $sheet->getStyle('A5')->getFont()->setBold(true);

                    $num_row = 7;
                    $number  = 1;
                    
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    foreach($total_arr as $key => $row){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,$row['title']);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $row['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $row['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $row['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $row['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $row['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $row['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, $row['total']);
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;
                        $total_nb_female    += $row['number_female'];
                        $total_nb_male      += $row['number_male'];
                        $total_nb_negative  += $row['number_negative'];
                        $total_nb_positive  +=  $row['number_positive'];
                        $total_nb_invalid   +=  $row['number_invalid'];
                        $total_nb_pending   +=  $row['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A6:P".($num_row))->applyFromArray($border_style);
                    
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");
                    // End Grand total
                   
                    $num_row++;
                    $number = 1;
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $tbl2_row           = $num_row; 
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,_t('sample.sample_source'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));
                    
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;

                    foreach($total_by_sample_source as $key => $source){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'. $num_row,$key);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;

                        $total_nb_female    += $source['number_female'];
                        $total_nb_male      += $source['number_male'];
                        $total_nb_negative  += $source['number_negative'];
                        $total_nb_positive  +=  $source['number_positive'];
                        $total_nb_invalid   +=  $source['number_invalid'];
                        $total_nb_pending   +=  $source['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    $column++;
                    //$num_row++;
                    $number++;
                    // End Grand total                                        
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");
                    /**
                     * 05052021
                     */
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $num_row++;
                    $tbl2_row           = $num_row;
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'.$num_row, _t('sample.reason_for_testing'));
                    $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'.$num_row, _t('sample.sample_source') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive') );
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid') );
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending') );
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;
                    $number = 1;
                    foreach($reason_for_testing_total as $label_reason => $res){
                        $mg = $num_row;
                        $len = 0;
                        foreach($res as $key => $source){
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'. $num_row,$label_reason);
                            $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'. $num_row,$key);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            //$number++;
    
                            $total_nb_female    += $source['number_female'];
                            $total_nb_male      += $source['number_male'];
                            $total_nb_negative  += $source['number_negative'];
                            $total_nb_positive  += $source['number_positive'];
                            $total_nb_invalid   += $source['number_invalid'];
                            $total_nb_pending   += $source['number_pending'];
                            $len++;
                        }                        
                        $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        $number++;
                    }
                    
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");

                    // End Grand total
                    /**End */
                   // $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);
                    $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                    $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                    $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                    $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                    $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                }

                //$sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':Q'.($num_row + 1))->setCellValue('K'.($num_row + 1),_t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':Q'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1),_t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                /** */
            }
            $filename = "Sars_CoV2_".$lab_code."-".$file_title."-".$current_date;
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
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

    public function export_covid_report(){        
        $this->app_language->load(array('global','patient','sample'));
        $this->load->model('report_model');
        $this->load->model('patient_model');
        $this->load->model('user_model');
        $this->load->model('laboratory_model');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 1200); // Zero means the script can run forever

        $obj		        = new stdClass();
        
        $obj->start_time    = $this->input->get('start_time');
        $obj->end_time      = $this->input->get('end_time');
        $for_research       = $this->input->get('for_research');
        $obj->test_start    = "";
        $obj->test_end      = "";
        $obj->start_sn      = "";
        $obj->end_sn        = "";
        $lang               = $this->app_language->app_lang();

        if($this->input->get('start') !== ""){
            $obj->start	= $this->format_date($this->input->get('start'));
        }
        if($this->input->get('end') !== ""){
            $obj->end	    = $this->format_date($this->input->get('end'));
        }
        if($this->input->get('test_start') !== ""){
            $obj->test_start	= $this->format_date($this->input->get('test_start'));
        }
        if($this->input->get('test_end') !== ""){
            $obj->test_end	    = $this->format_date($this->input->get('test_end'));
        }
        if($this->input->get('start_sample_number') !== "" || $this->input->get('end_sample_number') !== ""){
            $obj->start_sn = $this->input->get('start_sample_number');
            $obj->end_sn  = $this->input->get('end_sample_number');
        }
        if($this->input->get('sample_source') !== ""){
            $obj->sample_source	= $this->input->get('sample_source');
        }
        if($this->input->get('test_name') !== ""){
            $test_name_str  = $this->input->get('test_name');
            $r              = (explode(",",$test_name_str));
            $str_testname   = '';
            for($i = 0; $i < count($r); $i++){
                $str_testname.= " test.\"ID\" = ".$r[$i]." OR";
            }
            $str_testname = substr($str_testname,0,strlen($str_testname)-2);
            $obj->test_name  = $str_testname;
        }


        if($this->input->get('lab_id') !== ""){
            $obj->lab_id    = $this->input->get('lab_id');
            //$lab_code       = $obj->lab_id;
            //$lab_code       = $obj->lab_id;
            $lab_code       = $this->session->userdata('laboratory')->labID;
            $lab            = $this->laboratory_model->get_laboratory($lab_code, FALSE);
            $lab_name       = ($lang == 'kh') ? $lab[0]->name_kh :  strtoupper($lab[0]->name_en);
            $labID          = $obj->lab_id;
        }else{
            $lab_code = $this->session->userdata('laboratory')->labID;
            $lab_name = CamlisSession::getLabSession("name_".$lang);
            $labID  = $obj->lab_id;
        }
       

        if($this->input->get('test_result') !== ""){            
            $obj->test_result	    = $this->input->get('test_result');
        }
        if($this->input->get('number_of_sample') !== 0){
            $obj->number_of_sample	    = $this->input->get('number_of_sample');
        }
        //$result_list        = $this->report_model->covid_table($obj,$for_research);

        $user       = $this->user_model-> get_user($this->session->userdata("username"));
        $report_by  = $user[0]['fullname'];
        
        if($lang == 'en'){
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
        }else{
            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
        }
        try {
            ob_start();
            $this->load->library('phptoexcel');
            $objPHPExcel    = new PHPExcel();

            $n_sheet = 1;
            $SHEET_ARR = array();
            $table_columns  = array(_t('sample.order_number'), _t('patient.patient_id'), _t('patient.name'), _t('global.patient_age'), _t('global.patient_gender'), _t('patient.nationality'),_t('patient.passport_no') , _t('patient.flight_number'), _t('patient.date_of_arrival') , _t('sample.sample_number') , _t('sample.reason_for_testing') , _t('global.sample_source'), _t('sample.collect_dt') , _t('sample.receive_dt'), _t('sample.test_date') ,_t('sample.result')." SARS-CoV-2", _t('sample.number_of_sample'));
            
            $border_style = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $headerStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 13,
                    'name'  => 'Khmer OS Muol Light'
                ));
            $header1StyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Muol Light'
            ));
            $subheaderStyleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '1f01ff'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Bokor'
            ));
            $columnStyleArray = array(
                'font'  => array(
                    'color' => array('rgb' => '000000'),
                    'size'  => 11,
                    'name'  => 'Khmer OS Siemreap'
            ));
            
            $bodyStyleArray = array(
                'font'  => array(
                        'color' => array('rgb' => '000000'),
                        'size'  => 11,
                        'name'  => 'Khmer OS Siemreap'
                    ));
            
            $current_date           = date('d/M/Y');
            $total_arr              = array();
            $total_by_sample_source = array();            

            $reason_for_testing_total   = array(); //05052021
            $data_by_sample_source      = array(); //10052021
            $moh_logo_path = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/moh-logo.png';
            if($for_research == "all"){
                $file_title = "បង្ហាញទាំងអស់";
                $result_list    = $this->report_model->covid_table($obj, null, $labID, true);
                
                $prev_reason = null;
                $cur_reason = null;
                $count_reason = 0;
                $k = 0;
                $number_male        = 0;
                $number_female      = 0;
                $number_positive    = 0;
                $number_negative    = 0;
                $number_invalid     = 0;
                $number_pending     = 0;
                $tt = 0;
                
                foreach($result_list as $key => $row){
                    $k++;
                    $value = $row["reason_for_testing"];
                    $ind_reason = $row["for_research"];
                    $title = ($row["for_research"] == 0) ? _t('sample.none_reason_selected') :$FOR_RESEARCH_ARR[$row["for_research"]];
                    
                    //Create new sheet 
                    if($prev_reason === null){
                        $prev_reason = $row["for_research"];
                    }
                    if($cur_reason !== $row["for_research"]){
                        
                        if($cur_reason !== null){
                            // Add footer of the sheet
                            $sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                            $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                            $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                            
                            $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            
                            $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                            $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                            $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                            //End
                        }

                        //Create new sheet
                        $cur_reason = $row["for_research"];
                        $objPHPExcel->createSheet();
                        $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                        $objDrawing     = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('MOH');
                        $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                        $objDrawing->setPath($moh_logo_path);
                        //$objDrawing->setPath(site_url().'assets/camlis/images/moh-logo.png');
                        $objDrawing->setOffsetX(5);  // setOffsetX works properly
                        $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                        $objDrawing->setCoordinates('B2');
                        $objDrawing->setHeight(36); // logo height
                        $objDrawing->setWorksheet($sheet);

                        $sheet->setTitle($title);
                        $column         = 0;

                        //Set Header
                        $sheet->mergeCells('A1:Q1')->setCellValue('A1',_t('global.kingdom'));
                        $sheet->mergeCells('A2:Q2')->setCellValue('A2',_t('global.nation'));
                        $sheet->mergeCells('A3:Q3')->setCellValue('A3',$lab_name);
                        $sheet->mergeCells('C4:O4')->setCellValue('C4', _t('report.covid19_laboratory_result')."(".$title.")");
                        $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                        $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                        $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                        $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                        $sheet->mergeCells('A5:Q5')->setCellValue('A5', _t('report.date')." " .$current_date);
                        $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                        foreach($table_columns as $field)
                        {
                            $sheet->setCellValueByColumnAndRow($column, 6, $field);
                            $column++;
                        }
                        $sheet->getStyle("A6:Q6")->applyFromArray($columnStyleArray);
                        $sheet->getStyle('A6:Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A6:Q6')->getFont()->setBold(true);
                        $sheet->getStyle('A5')->getFont()->setBold(true);
                        $num_row            = 7;
                        $number             = 1;                        

                        $reason_for_testing_total[$row["for_research"]] = array();
                        $n_sheet++;
                    }

                    $nationality        = '';
                    $passport_number    = '';
                    $flight_number      = "";
                    $date_arrival       = "";
                    $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $title;

                    $patient            = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                    if($patient){
                        $nationality    = $patient['nationality_en'];
                        $passport_number = $patient['passport_number'];
                        $flight_number  = $patient['flight_number'];
                        $date_arrival = $patient['date_arrival'];
                        // save it for group of sample source
                        $row['nationality'] = $nationality;
                        $row['passport_number'] = $passport_number;
                        $row['flight_number'] = $flight_number;
                        $row['date_arrival'] = $date_arrival;
                    }
                    // Request to REMOVE "SRP-" from siem reap covid lab
                    // 18-03-2021
                    $patient_code = $row['patient_code'];
                    if($lab_code == 61){
                        $patient_code = str_replace("SRP-","",$row['patient_code']);
                    }
                    $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                    $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                    $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                    $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                    $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                    $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                    $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                    $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                    $sheet->setCellValueByColumnAndRow(7, $num_row, $flight_number);
                    $sheet->setCellValueByColumnAndRow(8, $num_row, $date_arrival);
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_number']);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $reason);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $row['sample_source']);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $row['collected_date']);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $row['received_date']);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $row['test_date']);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, $row['result_organism']);
                    $sheet->setCellValueByColumnAndRow(16, $num_row, $number_of_sample);
                    $sheet->getStyle('A'.$num_row.':Q'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B'.$num_row.':C'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $column++;
                    $num_row++;
                    $number++;
                    
                    // Overal Result sheet
                    if($cur_reason == $prev_reason){
                        if($row['patient_gender'] == 'M'){
                            $number_male++;
                        }
                        if($row['patient_gender'] == 'F'){
                            $number_female++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $number_positive++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $number_negative++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $number_invalid++;
                        }
                        if($row['result_organism'] == ''){
                            $number_pending++;
                        }
                        $count_reason++;                        
                    }else{
                        $total    = array(
                            'title'             => ($prev_reason == 0) ? _t('sample.none_reason_selected') :$FOR_RESEARCH_ARR[$prev_reason],
                            'total'             => $count_reason,
                            'number_male'       => $number_male,
                            'number_female'     => $number_female,
                            'number_positive'   => $number_positive,
                            'number_negative'   => $number_negative,
                            'number_invalid'    => $number_invalid,
                            'number_pending'    => $number_pending
                        );                                              
                        $total_arr[]        = $total;
                        $prev_reason        = $cur_reason;
                        $number_male        = 0;
                        $number_female      = 0;
                        $number_positive    = 0;
                        $number_negative    = 0;
                        $number_invalid     = 0;
                        $number_pending     = 0;
                        $count_reason       = 1;
                        if($row['patient_gender'] == 'M'){
                            $number_male++;
                        }
                        if($row['patient_gender'] == 'F'){
                            $number_female++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $number_positive++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $number_negative++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $number_invalid++;
                        }
                        if($row['result_organism'] == ''){
                            $number_pending++;
                        }                     
                    }                     
                    // Check end of the list
                    if(count($result_list) == $k){                        
                        $total    = array(
                            'title'             => $FOR_RESEARCH_ARR[$prev_reason],
                            'total'             => $count_reason,
                            'number_male'       => $number_male,
                            'number_female'     => $number_female,
                            'number_positive'   => $number_positive,
                            'number_negative'   => $number_negative,
                            'number_invalid'    => $number_invalid,
                            'number_pending'    => $number_pending
                        );
                        $total_arr[] = $total;
                    }
                    // End Overal Result

                    // number of sample by sample source
                    // added 28-06-2021
                    $exist = false;
                    foreach($total_by_sample_source as $key => $source){
                        if($key == $row['sample_source']){
                            $exist = true;
                            break;
                        }
                    }

                    // if sample source does not exit
                    if(!$exist){
                        $total_by_sample_source[$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,
                            "number_pending"    => 0
                        );
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }else{
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // end
                    //05052021
                    /**
                     * 05052021
                     * Table sample source group by reason for testing
                     */
                    $is_sample_source_exist = false;
                    foreach($reason_for_testing_total[$ind_reason] as $key => $source){
                        if($key == $row['sample_source']){
                            $is_sample_source_exist = true;
                            break;
                        }
                    }
                    if(!$is_sample_source_exist){
                        $reason_for_testing_total[$ind_reason][$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,  
                            "number_pending"    => 0
                        );
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_pending"]++;
                        }

                    }else{
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$ind_reason][$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // End

                    /**
                     * Added: 10-05-2021
                     * Data by sample source
                     */
                    $sample_source_existent = false;
                    
                    foreach($data_by_sample_source as $key => $source){
                        if($key == trim($row['sample_source'])){
                            $sample_source_existent = true;
                            break;
                        }
                    }
                    if(!$sample_source_existent){
                        $data_by_sample_source[$row['sample_source']] = array();
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }else{
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }
                    /** End */
                } // End foreach
                                
                $sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                
                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);

                $SHEET_ARR[$value] = $sheet;
                
                /**
                 * Worksheet data by Sample Source
                 */
                //$n_sheet++;
                if(count($data_by_sample_source) > 0){
                
                //if($this->session->userdata('roleid') == 1){
                    foreach($data_by_sample_source as $ind => $result){
                        $sample_source_name = $ind;
                        $objPHPExcel->createSheet();
                        $sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
                        $objDrawing     = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('MOH');
                        $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                        $objDrawing->setPath($moh_logo_path);
                        $objDrawing->setOffsetX(5);  // setOffsetX works properly
                        $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                        $objDrawing->setCoordinates('B2');
                        $objDrawing->setHeight(36); // logo height
                        $objDrawing->setWorksheet($sheet);
                        $title = $sample_source_name;
                        
                        // sample source in Khmer Unicode
                        if(mb_strlen($title) > 31){
                            $title = mb_substr($title,0,15,"UTF-8");                            
                        }
                        
                        $sheet->setTitle(strval($n_sheet));
                        //$sheet->setTitle($title);
                        $column         = 0;
                        
                        //Set Header
                        $sheet->mergeCells('A1:P1')->setCellValue('A1',_t('global.kingdom'));
                        $sheet->mergeCells('A2:P2')->setCellValue('A2',_t('global.nation'));
                        $sheet->mergeCells('A3:P3')->setCellValue('A3',$lab_name);
                        $sheet->mergeCells('C4:O4')->setCellValue('C4', _t('report.covid19_laboratory_result')." "._t('report.by_sample_source')." (".$sample_source_name.")");
                        $sheet->mergeCells('A4:O4')->setCellValue('A4', _t('global.moh'));
                        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                        $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                        $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                        $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                        $sheet->mergeCells('A5:P5')->setCellValue('A5', _t('report.date')." " .$current_date);
                        $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
                        foreach($table_columns as $field)
                        {
                            $sheet->setCellValueByColumnAndRow($column, 6, $field);
                            $column++;
                        }
                        $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                        $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                        $sheet->getStyle('A5')->getFont()->setBold(true);
                        $num_row = 7;
                        $number = 1;
                        
                        foreach($result as $key => $row){
                            $nationality        = '';
                            $passport_number    = '';
                            $reason             = ($row['reason_for_testing'] == "") ? $row['diagnosis'] : $row['reason_for_testing'];
                                                            
                            // Request to REMOVE "SRP-" from siem reap covid lab
                            // 18-03-2021
                            $patient_code = $row['patient_code'];
                            if($lab_code == 61){
                                $patient_code = str_replace("SRP-","",$row['patient_code']);
                            }
                            $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                            $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                            $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                            $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                            $sheet->setCellValueByColumnAndRow(5, $num_row, $row['nationality']);
                            $sheet->setCellValueByColumnAndRow(6, $num_row, $row['passport_number']);
                            $sheet->setCellValueByColumnAndRow(7, $num_row, $row['flight_number']);
                            $sheet->setCellValueByColumnAndRow(8, $num_row, $row['date_arrival']);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_number']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $reason);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $row['sample_source']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $row['collected_date']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $row['received_date']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $row['test_date']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, $row['result_organism']);
                            $sheet->setCellValueByColumnAndRow(16, $num_row, $number_of_sample);
                            $sheet->getStyle('A'.$num_row.':Q'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle('B'.$num_row.':C'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);                                
                            $column++;
                            $num_row++;
                            $number++;
                        }
                        $n_sheet++;
                        
                        
                        $sheet->mergeCells('K'.($num_row + 1).':Q'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                        $sheet->mergeCells('K'.($num_row + 2).':Q'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                        
                        $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                        
                        $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                        $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                        $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                        
                        }
                        // Auto size columns for each worksheet
                        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                            $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                            $sheet = $objPHPExcel->getActiveSheet();
                            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(true);
                            foreach ($cellIterator as $cell) {
                                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                            }
                        }
                //    }
                }
                //End
                // Added 06-07-2021
                // Create overal result sheet 
                if(count($total_arr) > 0){
                    // Table Total by Reason for testing
                    $table_columns = array( _t('sample.order_number'), _t('sample.reason_for_testing'), _t('sample.total'));
                    $objPHPExcel->createSheet();
                    $sheet          = $objPHPExcel->setActiveSheetIndex(0);
                    $objDrawing     = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('MOH');
                    $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                    $objDrawing->setPath($moh_logo_path);
                    $objDrawing->setOffsetX(5);    // setOffsetX works properly
                    $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('B2');
                    $objDrawing->setHeight(36); // logo height
                    $objDrawing->setWorksheet($sheet);
                    $title = _t('sample.overall_result');
                    $sheet->setTitle($title);
                    $column         = 0;
                    
                    //Set Header
                    $sheet->mergeCells('A1:P1')->setCellValue('A1',_t('global.kingdom'));
                    $sheet->mergeCells('A2:P2')->setCellValue('A2',_t('global.nation'));
                    $sheet->mergeCells('A3:P3')->setCellValue('A3',$lab_name);
                    $sheet->mergeCells('C4:O4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$title.")");
                    $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                    $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                    $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                    $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                    $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                    $sheet->mergeCells('A5:P5')->setCellValue('A5',_t('report.date')." ".$current_date);
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A5")->applyFromArray($columnStyleArray);

                    // header 
                    $sheet->setCellValueByColumnAndRow(0, 6, _t('sample.order_number'));
                    $sheet->mergeCells('B6:I6')->setCellValue('B6',_t('sample.reason_for_testing'));
                    $sheet->setCellValueByColumnAndRow(9, 6, _t('global.female') );
                    $sheet->setCellValueByColumnAndRow(10, 6, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, 6, _t('sample.number_of_negative'));
                    $sheet->setCellValueByColumnAndRow(12, 6, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, 6, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, 6, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, 6, _t('sample.total'));
                    
                    $sheet->getStyle("A6:Q6")->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A6:Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:Q6')->getFont()->setBold(true);
                    $sheet->getStyle('A5')->getFont()->setBold(true);

                    $num_row = 7;
                    $number = 1;
                    
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    foreach($total_arr as $key => $row){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,$row['title']);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $row['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $row['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $row['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $row['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $row['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $row['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, $row['total']);
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;
                        $total_nb_female    += $row['number_female'];
                        $total_nb_male      += $row['number_male'];
                        $total_nb_negative  += $row['number_negative'];
                        $total_nb_positive  += $row['number_positive'];
                        $total_nb_invalid   += $row['number_invalid'];
                        $total_nb_pending   += $row['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,'');
                    // End Table                     
                    $sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                                        
                    $num_row++;
                    $number = 1;
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $tbl2_row = $num_row; 
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,_t('sample.sample_source'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;

                    foreach($total_by_sample_source as $key => $source){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'. $num_row,$key);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;

                        $total_nb_female    += $source['number_female'];
                        $total_nb_male      += $source['number_male'];
                        $total_nb_negative  += $source['number_negative'];
                        $total_nb_positive  +=  $source['number_positive'];
                        $total_nb_invalid   +=  $source['number_invalid'];
                        $total_nb_pending   +=  $source['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,'');
                    // End Grand total

                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);

                    /**
                     * 05052021
                     */
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $num_row++;
                    $tbl2_row           = $num_row;
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'.$num_row, _t('sample.reason_for_testing'));
                    $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'.$num_row, _t('sample.sample_source') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive') );
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid') );
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending') );
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;
                    $number = 1;
                    
                    foreach($reason_for_testing_total as $ind => $res){
                        $mg = $num_row;
                        $len = 0;
                        $label_reason = ($ind == 0) ? _t('sample.none_reason_selected') : $FOR_RESEARCH_ARR[$ind];
                        
                        foreach($res as $key => $source){
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'. $num_row, $label_reason );
                            $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'. $num_row,$key);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            //$number++;
    
                            $total_nb_female    += $source['number_female'];
                            $total_nb_male      += $source['number_male'];
                            $total_nb_negative  += $source['number_negative'];
                            $total_nb_positive  += $source['number_positive'];
                            $total_nb_invalid   += $source['number_invalid'];
                            $total_nb_pending   += $source['number_pending'];
                            $len++;
                        }
                        //$sheet->mergeCells('A'.$mg.':A'. ($mg + $len))->setCellValue('A'. $mg ,$number);
                        $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);                        
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        $number++;
                    }
                                       
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,'');
                    // End Grand total
                    /**End */
                    
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);
                    $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                    $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);                    
                    $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    
                    $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                    $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                    $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    
                }
                
                //Remove the black worksheet
                $sheetIndex = $objPHPExcel->getIndex(
                    $objPHPExcel->getSheetByName('Worksheet 1')
                );
                $objPHPExcel->removeSheetByIndex($sheetIndex);
                $wsIndexStr = "".$sheetIndex;
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0);
                
                /** */               
                $filename = "Sars_CoV2_".$lab_code."-".$file_title."-".$current_date;
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
                header('Cache-Control: max-age=0'); //no cache
                ob_end_clean();
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
            }else{
                /**
                 * 
                 * Save One sheet
                 */

                $reason_for_testing = $FOR_RESEARCH_ARR[$for_research];
                $file_title         = $reason_for_testing;
                $result_list        = $this->report_model->covid_table($obj,$for_research,$labID,null);
                //$result_list    = $this->report_model->covid_table($obj, null, $labID, true);
                $sheet              = $objPHPExcel->setActiveSheetIndex(0);
                $sheet->setTitle($reason_for_testing);
                $objDrawing         = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('MOH');
                $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                $objDrawing->setPath($moh_logo_path);
                $objDrawing->setOffsetX(5);    // setOffsetX works properly
                $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                $objDrawing->setCoordinates('B2');
                $objDrawing->setHeight(36); // logo height
                $objDrawing->setWorksheet($sheet);
                $column             = 0;
                $number             = 1;
                $number_male        = 0;
                $number_female      = 0;
                $number_positive    = 0;
                $number_negative    = 0;
                $number_invalid     = 0;
                $number_pending     = 0;

                $reason_for_testing_total[$for_research] = array();
                //Set Header
               $sheet->mergeCells('A1:Q1')->setCellValue('A1',_t('global.kingdom'));
               $sheet->mergeCells('A2:Q2')->setCellValue('A2',_t('global.nation'));
               $sheet->mergeCells('A3:Q3')->setCellValue('A3',$lab_name);
               $sheet->mergeCells('C4:Q4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$reason_for_testing.")");
               $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
               
               $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
               $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
               $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
               $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
               $sheet->mergeCells('A5:Q5')->setCellValue('A5',"កាលបរិច្ឆេទ៖ ".$current_date);
               $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
               $sheet->getStyle("A5")->applyFromArray($columnStyleArray);
               foreach($table_columns as $field)
               {
                    $sheet->setCellValueByColumnAndRow($column, 6, $field);
                    $column++;
               }
               $sheet->getStyle("A6:Q6")->applyFromArray($columnStyleArray);
               $sheet->getStyle('A6:Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle('A6:Q6')->getFont()->setBold(true);
               $sheet->getStyle('A5')->getFont()->setBold(true);
               $num_row = 7;
               $number = 1;
                
                foreach($result_list as $key => $row){
                    $nationality        = '';
                    $passport_number    = '';
                    $reason             = $FOR_RESEARCH_ARR[$row['for_research']];
                    $value              = $reason; //05-07-2021
                    $patient            = $this->patient_model->get_outside_patient(null,$row['patient_code']);
                    $flight_number      = "";
                    $date_arrival       = "";
                    if($patient){
                        $nationality        = $patient['nationality_en'];
                        $passport_number    = $patient['passport_number'];
                        $flight_number      = $patient['flight_number'];
                        $date_arrival       = $patient['date_arrival'];
                    }
                    // Request to REMOVE "SRP-" from siem reap covid lab
                    // 18-03-2021
                    $patient_code = $row['patient_code'];
                    if($lab_code == 61){
                        $patient_code = str_replace("SRP-","",$row['patient_code']);
                    }
                    $number_of_sample = ($row['number_of_sample'] == 0 || $row['number_of_sample'] == "") ? "" : $row['number_of_sample'];
                    $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                    $sheet->setCellValueByColumnAndRow(1, $num_row, $patient_code);
                    $sheet->setCellValueByColumnAndRow(2, $num_row, $row['patient_name']);
                    $sheet->setCellValueByColumnAndRow(3, $num_row, $row['patient_age']);
                    $sheet->setCellValueByColumnAndRow(4, $num_row, $row['patient_gender']);
                    $sheet->setCellValueByColumnAndRow(5, $num_row, $nationality);
                    $sheet->setCellValueByColumnAndRow(6, $num_row, $passport_number);
                    $sheet->setCellValueByColumnAndRow(7, $num_row, $flight_number);
                    $sheet->setCellValueByColumnAndRow(8, $num_row, $date_arrival);
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $row['sample_number']);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $reason);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $row['sample_source']);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $row['collected_date']);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $row['received_date']);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $row['test_date']);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, $row['result_organism']);
                    $sheet->setCellValueByColumnAndRow(16, $num_row, $number_of_sample);
                    $sheet->getStyle('A'.$num_row.':Q'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B'.$num_row.':C'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $num_row++;
                    $number++;

                    if($row['patient_gender'] == 'M'){
                        $number_male++;    
                    }
                    if($row['patient_gender'] == 'F'){
                        $number_female++;    
                    }
                    if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                        $number_positive++;    
                    }
                    if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                        $number_negative++;
                    }
                    if($row['result_organism'] == 'Invalid'){
                        $number_invalid++;
                    }
                    if($row['result_organism'] == ''){
                        $number_pending++;
                    }
                    // number of sample by sample source
                    // added 28-06-2021
                    $exist = false;
                    foreach($total_by_sample_source as $key => $source){
                        if($key == $row['sample_source']){
                            $exist = true;
                            break;
                        }
                    }
                    
                    // if sample source does not exit
                    if(!$exist){
                        $total_by_sample_source[$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,
                            "number_pending"    => 0
                        );
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }else{
                        if($row['patient_gender'] == 'M'){
                            $total_by_sample_source[$row['sample_source']]["number_male"]++;   
                        }
                        if($row['patient_gender'] == 'F'){
                            $total_by_sample_source[$row['sample_source']]["number_female"]++;
                        }
                        if($row['result_organism'] == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $total_by_sample_source[$row['sample_source']]["number_positive"]++;    
                        }
                        if($row['result_organism'] == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $total_by_sample_source[$row['sample_source']]["number_negative"]++;
                        }
                        if($row['result_organism'] == 'Invalid'){
                            $total_by_sample_source[$row['sample_source']]["number_invalid"]++;
                        }
                        if($row['result_organism'] == ''){
                            $total_by_sample_source[$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // end
                    //05052021
                    /**
                     * 05052021
                     * Table sample source group by reason for testing
                     */
                    $is_sample_source_exist = false;
                    foreach($reason_for_testing_total[$value] as $key => $source){
                        if($key == $row['sample_source']){
                            $is_sample_source_exist = true;
                            break;
                        }
                    }
                    if(!$is_sample_source_exist){
                        $reason_for_testing_total[$value][$row['sample_source']] = array(
                            "number_male"       => 0 ,
                            "number_female"     => 0 ,
                            "number_positive"   => 0 ,
                            "number_negative"   => 0 ,
                            "number_invalid"    => 0 ,  
                            "number_pending"    => 0
                        );
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                        }

                    }else{
                        if(trim($row['patient_gender']) == 'M'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_male"]++;   
                        }
                        if(trim($row['patient_gender']) == 'F'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_female"]++;
                        }
                        if(trim($row['result_organism']) == 'Positive' || $row['result_organism'] == 'Reaction Positive'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_positive"]++;    
                        }
                        if(trim($row['result_organism']) == 'Negative' || $row['result_organism'] == 'Reaction Negative'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_negative"]++;
                        }

                        if(trim($row['result_organism']) == 'Invalid'){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_invalid"]++;
                        }
                        if(trim($row['result_organism']) == ''){
                            $reason_for_testing_total[$value][$row['sample_source']]["number_pending"]++;
                        }
                    }
                    // End

                    /**
                     * Added: 10-05-2021
                     * Data by sample source
                     */
                    $sample_source_existent = false;
                    
                    foreach($data_by_sample_source as $key => $source){
                        if($key == trim($row['sample_source'])){
                            $sample_source_existent = true;
                            break;
                        }
                    }                            
                    if(!$sample_source_existent){
                        $data_by_sample_source[$row['sample_source']] = array();
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }else{
                        $data_by_sample_source[$row['sample_source']][] = $row;
                    }
                    /** End */
                }                    
                $sheet->getStyle("A6:Q".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':Q'.($num_row + 1))->setCellValue('K'.($num_row + 1),_t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':Q'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);

                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1),_t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                /** Overal Result Sheet */
                $total    = array(
                    'title'             => $value,
                    'total'             => count($result_list),
                    'number_male'       => $number_male,
                    'number_female'     => $number_female,
                    'number_positive'   => $number_positive,
                    'number_negative'   => $number_negative,
                    'number_invalid'    => $number_invalid,
                    'number_pending'    => $number_pending
                );
                $total_arr[] = $total;

                // Added 07 April 2021
                // create total sheet 
                if(count($total_arr) > 0){
                    $table_columns = array( _t('sample.order_number'), _t('sample.reason_for_testing'), _t('sample.total'));
                    $objPHPExcel->createSheet();
                    $sheet          = $objPHPExcel->setActiveSheetIndex(1);
                    $objDrawing     = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('MOH');
                    $objDrawing->setDescription('ក្រសួងសុខាភិបាល');
                    $objDrawing->setPath($moh_logo_path);
                    $objDrawing->setOffsetX(5);    // setOffsetX works properly
                    $objDrawing->setOffsetY(0);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('B2');
                    $objDrawing->setHeight(36); // logo height
                    $objDrawing->setWorksheet($sheet);
                    $title = _t('sample.overall_result');
                    $sheet->setTitle($title);
                    $column         = 0;
                    
                    //Set Header
                    $sheet->mergeCells('A1:O1')->setCellValue('A1',_t('global.kingdom'));
                    $sheet->mergeCells('A2:O2')->setCellValue('A2',_t('global.nation'));
                    $sheet->mergeCells('A3:O3')->setCellValue('A3',$lab_name);
                    $sheet->mergeCells('C4:N4')->setCellValue('C4',_t('report.covid19_laboratory_result')." (".$title.")");
                    $sheet->mergeCells('A4:B4')->setCellValue('A4', _t('global.moh'));
                    $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("A1")->applyFromArray($headerStyleArray);
                    $sheet->getStyle("A2")->applyFromArray($header1StyleArray);
                    $sheet->getStyle("A3")->applyFromArray($subheaderStyleArray);
                    $sheet->getStyle("A4:C4")->applyFromArray($subheaderStyleArray);
                    $sheet->mergeCells('A5:P5')->setCellValue('A5',_t('report.date')." ".$current_date);
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A5")->applyFromArray($columnStyleArray);

                    // header 
                    $sheet->setCellValueByColumnAndRow(0, 6, _t('sample.order_number'));
                    $sheet->mergeCells('B6:I6')->setCellValue('B6',_t('sample.reason_for_testing'));
                    $sheet->setCellValueByColumnAndRow(9, 6, _t('global.female') );
                    $sheet->setCellValueByColumnAndRow(10, 6, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, 6, _t('sample.number_of_negative'));
                    $sheet->setCellValueByColumnAndRow(12, 6, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, 6, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, 6, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, 6, _t('sample.total'));
                    
                    $sheet->getStyle("A6:P6")->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:P6')->getFont()->setBold(true);
                    $sheet->getStyle('A5')->getFont()->setBold(true);

                    $num_row = 7;
                    $number  = 1;
                    
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    foreach($total_arr as $key => $row){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,$row['title']);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $row['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $row['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $row['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $row['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $row['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $row['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, $row['total']);
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;
                        $total_nb_female    += $row['number_female'];
                        $total_nb_male      += $row['number_male'];
                        $total_nb_negative  += $row['number_negative'];
                        $total_nb_positive  +=  $row['number_positive'];
                        $total_nb_invalid   +=  $row['number_invalid'];
                        $total_nb_pending   +=  $row['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A6:P".($num_row))->applyFromArray($border_style);
                    
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");
                    // End Grand total
                   
                    $num_row++;
                    $number = 1;
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $tbl2_row           = $num_row; 
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row,_t('sample.sample_source'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive'));
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid'));
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending'));
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));
                    
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;

                    foreach($total_by_sample_source as $key => $source){
                        $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                        $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'. $num_row,$key);
                        $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                        $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                        $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                        $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                        $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                        $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                        $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                        $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $column++;
                        $num_row++;
                        $number++;

                        $total_nb_female    += $source['number_female'];
                        $total_nb_male      += $source['number_male'];
                        $total_nb_negative  += $source['number_negative'];
                        $total_nb_positive  +=  $source['number_positive'];
                        $total_nb_invalid   +=  $source['number_invalid'];
                        $total_nb_pending   +=  $source['number_pending'];
                    }
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    $column++;
                    //$num_row++;
                    $number++;
                    // End Grand total                                        
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");
                    /**
                     * 05052021
                     */
                    // 28-04-2021
                    // add table number of sample by sample source
                    // header 
                    $num_row++;
                    $tbl2_row           = $num_row;
                    $total_nb_female    = 0;
                    $total_nb_male      = 0;
                    $total_nb_negative  = 0;
                    $total_nb_positive  = 0;
                    $total_nb_invalid   = 0;
                    $total_nb_pending   = 0;

                    $sheet->setCellValueByColumnAndRow(0, $num_row, _t('sample.order_number'));
                    $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'.$num_row, _t('sample.reason_for_testing'));
                    $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'.$num_row, _t('sample.sample_source') );
                    $sheet->setCellValueByColumnAndRow(9, $num_row, _t('global.female'));
                    $sheet->setCellValueByColumnAndRow(10, $num_row, _t('global.male') );
                    $sheet->setCellValueByColumnAndRow(11, $num_row, _t('sample.number_of_negative') );
                    $sheet->setCellValueByColumnAndRow(12, $num_row, _t('sample.number_of_positive') );
                    $sheet->setCellValueByColumnAndRow(13, $num_row, _t('sample.number_of_invalid') );
                    $sheet->setCellValueByColumnAndRow(14, $num_row, _t('sample.number_of_pending') );
                    $sheet->setCellValueByColumnAndRow(15, $num_row, _t('sample.total'));

                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $num_row++;
                    $number = 1;
                    foreach($reason_for_testing_total as $label_reason => $res){
                        $mg = $num_row;
                        $len = 0;
                        foreach($res as $key => $source){
                            $sheet->setCellValueByColumnAndRow(0, $num_row, $number);
                            $sheet->mergeCells('B'.$num_row.':E'.$num_row)->setCellValue('B'. $num_row,$label_reason);
                            $sheet->mergeCells('F'.$num_row.':I'.$num_row)->setCellValue('F'. $num_row,$key);
                            $sheet->setCellValueByColumnAndRow(9, $num_row, $source['number_female']);
                            $sheet->setCellValueByColumnAndRow(10, $num_row, $source['number_male']);
                            $sheet->setCellValueByColumnAndRow(11, $num_row, $source['number_negative']);
                            $sheet->setCellValueByColumnAndRow(12, $num_row, $source['number_positive']);
                            $sheet->setCellValueByColumnAndRow(13, $num_row, $source['number_invalid']);
                            $sheet->setCellValueByColumnAndRow(14, $num_row, $source['number_pending']);
                            $sheet->setCellValueByColumnAndRow(15, $num_row, ($source['number_male'] + $source['number_female']));
                            $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $column++;
                            $num_row++;
                            //$number++;
    
                            $total_nb_female    += $source['number_female'];
                            $total_nb_male      += $source['number_male'];
                            $total_nb_negative  += $source['number_negative'];
                            $total_nb_positive  += $source['number_positive'];
                            $total_nb_invalid   += $source['number_invalid'];
                            $total_nb_pending   += $source['number_pending'];
                            $len++;
                        }                        
                        $sheet->mergeCells('B'.$mg.':E'. ($mg + $len))->setCellValue('B'. $mg ,$label_reason);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'. $mg)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                        $number++;
                    }
                    
                    // Grand total row
                    $sheet->setCellValueByColumnAndRow(0, $num_row,"");
                    $sheet->mergeCells('B'.$num_row.':I'.$num_row)->setCellValue('B'.$num_row, _t('sample.total'));
                    $sheet->setCellValueByColumnAndRow(9, $num_row, $total_nb_female);
                    $sheet->setCellValueByColumnAndRow(10, $num_row, $total_nb_male);
                    $sheet->setCellValueByColumnAndRow(11, $num_row, $total_nb_negative);
                    $sheet->setCellValueByColumnAndRow(12, $num_row, $total_nb_positive);
                    $sheet->setCellValueByColumnAndRow(13, $num_row, $total_nb_invalid);
                    $sheet->setCellValueByColumnAndRow(14, $num_row, $total_nb_pending);
                    $sheet->setCellValueByColumnAndRow(15, $num_row, ($total_nb_female + $total_nb_male));

                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
                    $sheet->getStyle('J'.$num_row.':P'.$num_row)->getFont()->setBold(true);
                    $sheet->getStyle('B'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $column++;
                    $num_row++;
                    $number++;
                    $sheet->getStyle("A". $num_row.":P". $num_row)->applyFromArray($columnStyleArray);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'. $num_row.':P'. $num_row)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $num_row)->getFont()->setBold(true);
                    $sheet->mergeCells('B'.($num_row).':I'.($num_row))->setCellValue('B'.($num_row),"");

                    // End Grand total
                    /**End */
                   // $sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);
                    $sheet->getStyle("A".($tbl2_row).":P".($num_row - 1))->applyFromArray($border_style);
                    $sheet->mergeCells('K'.($num_row + 1).':P'.($num_row + 1))->setCellValue('K'.($num_row + 1), _t('sample.reported_by'));
                    $sheet->mergeCells('K'.($num_row + 2).':P'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                    $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                    $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1), _t('sample.head_laboratory'));
                    $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                    $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                }

                //$sheet->getStyle("A6:P".($num_row - 1))->applyFromArray($border_style);
                $sheet->mergeCells('K'.($num_row + 1).':Q'.($num_row + 1))->setCellValue('K'.($num_row + 1),_t('sample.reported_by'));
                $sheet->mergeCells('K'.($num_row + 2).':Q'.($num_row + 2))->setCellValue('K'.($num_row + 2),$report_by);
                
                $sheet->getStyle('K'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);

                $sheet->mergeCells('A'.($num_row + 1).':D'.($num_row + 1))->setCellValue('A'.($num_row + 1),_t('sample.head_laboratory'));
                $sheet->mergeCells('A'.($num_row + 2).':D'.($num_row + 2))->setCellValue('A'.($num_row + 2),"");
                $sheet->getStyle('A'.($num_row + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.($num_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A".($num_row + 1))->applyFromArray($columnStyleArray);
                
                // Auto size columns for each worksheet
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));
                    $sheet = $objPHPExcel->getActiveSheet();
                    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    /** @var PHPExcel_Cell $cell */
                    foreach ($cellIterator as $cell) {
                        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }
                /** */
            }
            $filename = "Sars_CoV2_".$lab_code."-".$file_title."-".$current_date;
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
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

    //23092021
    public function godata() {
        //if ($this->aauth->is_admin() || $_SESSION['id'] == 2327) {
            $this->load->model('sample_source_model');
            $this->app_language->load(array('patient'));
            $this->load->model('laboratory_model');
            
            $this->data['sample_source']        = $this->sample_source_model->get_lab_sample_source();
            
            //Get User Assign Laboratory
            $assign_lab	= $this->session->userdata('user_laboratories');
            
            if ($this->aauth->is_admin()) {
                $this->data['laboratories'] = $this->laboratory_model->get_laboratory();
            } else {
                $this->data['laboratories'] = $assign_lab && count($assign_lab) > 0 ? $this->laboratory_model->get_laboratory($assign_lab) : array();		
            }
            
            $this->template->plugins->add(['DataTable', 'DataTableFileExport','BootstrapTimePicker', 'BootstrapMultiselect']);
            $this->template->content->widget('CamLISReportNavigation', ['cur_page' => 'For GoData']);
            
            $this->template->stylesheet->add('assets/plugins/autocomplete/jquery-ui.css');
            $this->template->javascript->add('assets/plugins/autocomplete/jquery-ui.js');
            $this->template->javascript->add('assets/camlis/js/report/camlis_for_godata_report.js');
            
            $this->template->content->view('template/pages/report/godata', $this->data);
            $this->template->publish();
        //}else{
            //echo "You do not have right to access this page.";
        //}
    }

    public function preview_godata_result($type = 'preview') {
        $this->load->model('report_model');
        $this->load->model('patient_model');
        ini_set('memory_limit', '4092M');
        ini_set('max_execution_time', 0);
        $obj		        = new stdClass();
        $result_list        = array();

        if($this->input->post('lab_ids') !== ""){
            $lab_ids    = $this->input->post('lab_ids');                    
            //$lab_ids    = implode(",",$this->input->post('lab_ids'));
        }
        if($this->input->post('start') !== ""){
            $obj->start	= $this->format_date($this->input->post('start'));
        }
        if($this->input->post('end') !== ""){
            $obj->end	    = $this->format_date($this->input->post('end'));
        }
        $obj->start_time    = $this->input->post('start_time');
        $obj->end_time      = $this->input->post('end_time');
        $lab_string = "";
        for($i = 0; $i < count($lab_ids) ; $i++){ 
            $lab_string .= "(".$lab_ids[$i]."),"; 
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove , 
        $result_list   = $this->rModel->generate_covid_report_for_godata($lab_string, $obj);    
        echo json_encode($result_list);        
    }
    /**
    * Mircro biology laboratory Report
    * 04 Jan 2022
    */
    public function micro(){
        $this->aauth->control('generate_microbiology_report');
        $this->app_language->load(array('micro_report'));
        $this->load->model(['audit_user_model', 'laboratory_model']);
        $this->template->plugins->add(['AutoNumeric', 'Datatable', 'BootstrapTimePicker', 'AmCharts', 'BootstrapMultiselect']);                
        $this->template->stylesheet->add('assets/camlis/css/micro_report_style.css');
       // $this->template->stylesheet->add('assets/plugins/tocify/css/jquery.tocify.css');
        //$this->template->stylesheet->add('assets/plugins/tocify/css/prettify.css');
        $this->template->javascript->add('https://www.amcharts.com/lib/3/pie.js');
        $this->template->javascript->add('https://www.amcharts.com/lib/3/exporting/amexport.js');
        $this->template->javascript->add('https://www.amcharts.com/lib/3/exporting/canvg.js');
        $this->template->javascript->add('https://www.amcharts.com/lib/3/exporting/rgbcolor.js');
        
        
        //$this->template->javascript->add('assets/plugins/tocify/js/jquery-ui-1.9.1.custom.min.js');
        //$this->template->javascript->add('assets/plugins/tocify/js/jquery.tocify.min.js');
        //$this->template->javascript->add('assets/plugins/tocify/js/prettify.js');
        $this->template->javascript->add('assets/camlis/js/report/micro_report.js');
        $assign_lab	= $this->session->userdata('user_laboratories');
        if ($this->aauth->is_admin()) {
			$this->data['laboratories'] = $this->laboratory_model->get_laboratory();
		} else {
			$this->data['laboratories'] = $assign_lab && count($assign_lab) > 0 ? $this->laboratory_model->get_laboratory($assign_lab) : array();		
		}
        $this->data['samples'] = $this->rModel->load_sample();
        $this->template->content->view('template/pages/report/micro_report', $this->data);
        //$this->template->modal->view('template/print/micro_labo_report');
        //$this->template->modal->view('template/modal/modal_preview_micro_report');
        $this->template->content_title = 'Microbiology Laboratory Report';

        $this->template->modal->view('template/modal/modal_preview_result_micro_report');    
        $this->template->publish();
    }
    /**
     * Generate micro report
     * 13012022
     */
    public function get_patients_gender()
	{
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }        
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,
        
        $result = $this->rModel->get_patient_gender($start_date, $end_date, $lab_string);
        $data = [];
        foreach($result as $row){
            $gender = $row["gender"];
            $count = ($row["male"] == 0) ? $row["female"] : $row["male"];
            $color = ($row["gender"] == "Male") ? "#5ec0c5" : "#ea756c";
            $data[] = ["gender" => $gender, 'count'=>$count , 'color' => $color];
        }       
        echo json_encode(['data' => array_values($data)]);
	}
    public function get_patients_age()
	{
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,

        $result = [
             ['age_group' => '0 - 28d', 'male' => 0, 'female' => 0],
             ['age_group' => '29d - <1y', 'male' => 0, 'female' => 0],
             ['age_group' => '1y - 4y', 'male' => 0, 'female' => 0],
             ['age_group' => '5y - 14y', 'male' => 0, 'female' => 0],
             ['age_group' => '15y - 24y', 'male' => 0, 'female' => 0],             
             ['age_group' => '25y - 34y', 'male' => 0, 'female' => 0],
             ['age_group' => '35y - 44y', 'male' => 0, 'female' => 0],
             ['age_group' => '45y - 54y', 'male' => 0, 'female' => 0],
             ['age_group' => '55y - 64y', 'male' => 0, 'female' => 0],
             ['age_group' => '65y - 80y', 'male' => 0, 'female' => 0],
             ['age_group' => '>= 81y', 'male' => 0, 'female' => 0],
        ];
        $data = collect($this->rModel->get_patient_age($start_date, $end_date,$lab_string))->keyBy('age_group');
        $result = collect($result)->keyBy('age_group')->merge($data)->toArray();
        echo json_encode(['data' => array_values($result)]);
	}

    public function get_micro_specimen(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,

        // load std sample
        $std_sample = $this->rModel->load_sample();
        $result_list = [];
        $startDate  = new DateTime($this->input->post('start_date'));
        $endDate    = new DateTime($this->input->post('end_date'));
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);

        foreach($std_sample as $sample){
            $sample_id = $sample["ID"];            
            if (!in_array($sample_id, array(9,17))) {
                $result     = $this->rModel->get_micro_specimen($sample_id, $start_date, $end_date, $lab_string);
                $data     = [];
                foreach ($period as $dt) {
                    $data[$dt->format('M').'-'.$dt->format('y')] = [
                        'month' => $dt->format('M'),
                        'year' => $dt->format('y'),
                        'count' => 0,
                    ];
                }
                $result = collect($result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
                $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = trim($item['month']).'-'.trim($item['year']);  return $item; });
                $result_list[$sample["ID"]] = array_values($result->toArray());
            }
        }
        // Merge Pus
        $pus_result     = $this->rModel->get_pus_specimen($start_date, $end_date, $lab_string);        
        $data     = [];
        foreach ($period as $dt) {
            $data[$dt->format('M').'-'.$dt->format('y')] = [
                'month' => $dt->format('M'),
                'year' => $dt->format('y'),
                'count' => 0,
            ];
        }        
        $pus_result = collect($pus_result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $pus_result = collect($data)->merge($pus_result)->map(function($item) { $item['month_year'] = trim($item['month']).'-'.trim($item['year']);  return $item; });
        $result_list["917"] = array_values($pus_result->toArray());
        
        $data_by_month     = $this->rModel->get_specimen_by_month($start_date, $end_date, $lab_string);
        $output = collect($data_by_month)->groupBy('month_year')->all(); // group by month
        // Merge pus swap and pus aspirate
        
        foreach($output as $key => $month_row){
            $save_ind = array();
            foreach( $month_row as $index => $row ){
                if(in_array($row['sample_id'] , [9,17])){
                    // save index                    
                    array_push($save_ind , $index);
                }
            }
            if(count($save_ind) >0){
                $tmp_row    = $month_row[$save_ind[0]];
                $total      = 0;
                for($l = 0 ; $l < count($save_ind) ; $l++){
                    $total += $month_row[$save_ind[$l]]["value"];
                }
                $tmp_row['sample_name'] = "Pus";
                $tmp_row['value']       = $total;
                // remove both pus
                
                for($l = 0 ; $l < count($save_ind) ; $l++){                    
                    unset($output[$key][$save_ind[$l]]);
                }
                $output[$key][] = $tmp_row;
            }
            // remove data
            
        }
        //sort by value
        $res =[];
        foreach($output as $key => $row){
            $res[$key] = collect($row)->sortByDesc('value')->values()->all();
        }
        echo json_encode(['data' => $result_list , 'month_data' => $data_by_month , 'group_by'=>$output , 'ready_data'=>$res]);
    }
    
    public function get_antibiotic_organism(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');        
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string         = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,
        $organism_id_par    = $this->input->post('organism_id');
        $sample_id          = $this->input->post('sample_id');
        $patient_samples    = $this->rModel->get_patient_sample_by_pathogen($organism_id_par, $sample_id, $start_date, $end_date, $lab_string);
        // Deduplicate result
        $curr_pid           = 0;
        $current_date       = 0;
        $organism_id        = 0;
        $patient_sample_ids = ''; // save it
        $result             = array();
        foreach($patient_samples as $row){
            if($row['pid'] !== $curr_pid){
                $curr_pid               = $row['pid'];
                $current_date           = $row['collected_date']; 
                $patient_sample_ids    .= $row['psample_id'].',';
                $organism_id            = $row['organism_id'];
            }else{
                if($organism_id == $row['organism_id']){
                    $date1  = date_create($current_date);
                    $date2  = date_create($row['collected_date']);
                    $diff   = date_diff($date1,$date2);
                    $day    = $diff->format("%a");

                    if( intval($day) > 30){
                        $patient_sample_ids .= $row['psample_id'].',';
                        $curr_pid           = $row['pid'];
                        $current_date       = $row['collected_date']; 
                        $organism_id        = $row['organism_id'];
                    }
                }
            }
        }
        if($patient_sample_ids !== ''){
            $patient_sample_ids = substr($patient_sample_ids,0,strlen($patient_sample_ids)-1);
            $result = $this->rModel->get_antibiotic_organism($patient_sample_ids, $organism_id_par, $sample_id, $start_date, $end_date, $lab_string);
        }
        echo json_encode($result);
    }
    
    // Get antibiotic of bps

    public function get_antibiotic_bps(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,        
        $result = $this->rModel->get_antibiotic_bps($start_date, $end_date, $lab_string);
        echo json_encode($result);
    }

    // Notifiable and other important pathogens list
    public function get_notifiable_pathogens_list(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1); // remove ,
        $organism_tbl = array(
            '448' => 'Streptococcus suis',
            '480' => 'Vibrio cholerae',
            '495' => 'Yersinia pestis',
            '131' => 'Bacillus anthracis',
            '150' => 'Burkholderia pseudomallei',
            '181' => 'Corynebacterium diphtheriae',
            '247' => 'Francisella tularensis',
            '309' => 'Listeria monocytogenes',
            '333' => 'Neisseria gonorrhoeae',
            '334' => 'Neisseria meningitidis',
            '402' => 'Salmonella Paratyphi A',
            '405' => 'Salmonella sp.',
            '406' => 'Salmonella Typhi'
        );
        $orgasnism_ids  = '';
        $sample_ids     = '';
        foreach($organism_tbl as $org_id => $val){
            $orgasnism_ids .= $org_id.",";
        }
        $orgasnism_ids    = substr($orgasnism_ids,0,strlen($orgasnism_ids)-1);        
        $samples          = $this->rModel->load_sample();
        foreach($samples as $key =>$value){
            $sample_ids .= $value["ID"].',';
        }
        $sample_ids        = substr($sample_ids,0,strlen($sample_ids)-1);
        $patient_samples   = $this->rModel->get_patient_sample_by_pathogen($orgasnism_ids, $sample_ids, $start_date, $end_date, $lab_string);
        
        


        // Deduplicate result
        $curr_pid           = 0;
        $current_date       = 0;
        $organism_id        = 0;
        $patient_sample_ids = ''; // save it
        foreach($patient_samples as $row){
            if($row['pid'] !== $curr_pid){
                $curr_pid               = $row['pid'];
                $current_date           = $row['collected_date']; 
                $patient_sample_ids    .= $row['psample_id'].',';
                $organism_id            = $row['organism_id'];
            }else{
                if($organism_id == $row['organism_id']){
                    $date1  = date_create($current_date);
                    $date2  = date_create($row['collected_date']);
                    $diff   = date_diff($date1,$date2);
                    $day    = $diff->format("%a");

                    if( intval($day) > 30){
                        $patient_sample_ids .= $row['psample_id'].',';
                        $curr_pid           = $row['pid'];
                        $current_date       = $row['collected_date']; 
                        $organism_id        = $row['organism_id'];
                    }
                }
            }
        }

        $patient_sample_ids = substr($patient_sample_ids,0,strlen($patient_sample_ids)-1);
        
        $result = $patient_sample_ids == '' ? [] : $this->rModel->get_notifiable_pathogens_list($patient_sample_ids , $start_date, $end_date, $lab_string);

        $startDate  = new DateTime($this->input->post('start_date'));
        $endDate    = new DateTime($this->input->post('end_date'));
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);        
        $data     = [];

        $header = '<th>Pathogens</th>';
        $number_month = 0;
        $months = [];
        foreach ($period as $dt) {
            $number_month++;
            $header .= "<th>".$dt->format('M')."</th>";
            $months[$dt->format('m')] = 0;
        }
        $header .= "<th>Total<th>";

        $curr_organism_name = "";
        $group_organism_tbl = [];
                

        foreach($result as $row){
            unset($organism_tbl[$row['organism_id']]);
            if($curr_organism_name !== $row['organism_name']){
                $curr_organism_name = $row['organism_name'];
                $group_organism_tbl[$curr_organism_name] = $months;
                $group_organism_tbl[$curr_organism_name][$row['month_n']] = $row['total'];
            }else{
                $group_organism_tbl[$curr_organism_name][$row['month_n']] = $row['total'];
            }
        }

        // Add other pathogen
        foreach($result as $row){
            foreach($organism_tbl as $key => $organism){
                $group_organism_tbl[$organism] = $months;
                $group_organism_tbl[$organism][$row['month_n']] = 0;
            }
        }
                
        echo json_encode(['group_result'=> $group_organism_tbl , 'sample_ids' => $sample_ids , 'patient_samples' => $patient_samples]);
    }
    public function get_volume_blood_culture(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,

        // Create empty result
        $startDate  = new DateTime($this->input->post('start_date'));
        $endDate    = new DateTime($this->input->post('end_date'));
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);
        $data     = [];
        $total_data = [];
        $months = [];
        $counter = 0;
        $counter1 = 0;
        $counter2 = 0;
        foreach ($period as $dt) {
            $data[$dt->format('M').'-'.$dt->format('y')] = [
                'month' => $dt->format('M'),
                'year' => $dt->format('y'),
                'total' => 0,
                'low_percentage' => 0,
                'correct_percentage' => 0,
                'heigh_percentage' => 0
            ];            
        }
        $total_adult_low      = 0;
        $total_adult_correct  = 0;
        $total_adult_height   = 0;
        $total_29d1y_low      = 0;
        $total_29d1y_correct  = 0;
        $total_29d1y_height   = 0;
        $total_1y14y_low      = 0;
        $total_1y14y_correct  = 0;
        $total_1y14y_height   = 0;

        $adult_result = $this->rModel->get_adult_blood_volume($start_date, $end_date, $lab_string);
        foreach($adult_result as $val){
            $counter++;
            $total_adult_low += $val['low_percentage'];
            $total_adult_correct += $val['correct_percentage'];
            $total_adult_height += $val['heigh_percentage'];
        }
        
        $adult_result = collect($adult_result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $adult_result = collect($data)->merge($adult_result)->map(function($item) { $item['month_year'] = trim($item['month']).'-'.trim($item['year']);  return $item; });

        $pediatric_29d1y_result = $this->rModel->get_pediatric_29d1y_blood_volume($start_date, $end_date, $lab_string);        
        foreach($pediatric_29d1y_result as $val){
            $counter1++;
            $total_29d1y_low += $val['low_percentage'];
            $total_29d1y_correct += $val['correct_percentage'];
            $total_29d1y_height += $val['heigh_percentage'];
        }
        $pediatric_29d1y_result = collect($pediatric_29d1y_result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $pediatric_29d1y_result = collect($data)->merge($pediatric_29d1y_result)->map(function($item) { $item['month_year'] = trim($item['month']).'-'.trim($item['year']);  return $item; });

        $pediatric_1y14y_result = $this->rModel->get_pediatric_1y14y_blood_volume($start_date, $end_date, $lab_string);
        foreach($pediatric_1y14y_result as $val){
            $counter2++;
            $total_1y14y_low += $val['low_percentage'];
            $total_1y14y_correct += $val['correct_percentage'];
            $total_1y14y_height += $val['heigh_percentage'];
        }
        $pediatric_1y14y_result = collect($pediatric_1y14y_result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $pediatric_1y14y_result = collect($data)->merge($pediatric_1y14y_result)->map(function($item) { $item['month_year'] = trim($item['month']).'-'.trim($item['year']);  return $item; });

        $pediatric_28d          = $this->rModel->get_pediatric_under_28d_blood_volume($start_date, $end_date, $lab_string);
        $pediatric_28d          = collect($pediatric_28d)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $pediatric_28d          = collect($data)->merge($pediatric_28d)->map(function($item) { $item['month_year'] = trim($item['month']).'-'.trim($item['year']);  return $item; });

        
        $t1l = $counter1 > 0 ? ceil($total_29d1y_low/$counter1) : $total_29d1y_low;
        $t1c = $counter1 > 0 ? ceil($total_29d1y_correct/$counter1) : $total_29d1y_correct;
        $t1h = $counter1 > 0 ? ceil($total_29d1y_height/$counter1) : $total_29d1y_height;

        $t2l = $counter2 > 0 ? ceil($total_1y14y_low/$counter2) : $total_1y14y_low;
        $t2c = $counter2 > 0 ? ceil($total_1y14y_correct/$counter2) : $total_1y14y_correct;
        $t2h = $counter2 > 0 ? ceil($total_1y14y_height/$counter2) : $total_1y14y_height;

        $tl = $counter > 0 ? ceil($total_adult_low/$counter) : $total_adult_low;
        $tc = $counter > 0 ? ceil($total_adult_correct/$counter) : $total_adult_correct;
        $th = $counter > 0 ? ceil($total_adult_height/$counter) : $total_adult_height;

        $total_data = [
            ['age_group' => '29d-<1y', 'low_percentage' => $t1l, 'correct_percentage' => $t1c, 'heigh_percentage'=> $t1h],
            ['age_group' => '1y-<14y', 'low_percentage' => $t2l, 'correct_percentage' => $t2c , 'heigh_percentage'=> $t2h],
            ['age_group' => '>=14y', 'low_percentage' => $tl, 'correct_percentage' => $tc , 'heigh_percentage'=> $th]
       ];
        
        echo json_encode([
            'data_adult'            => array_values($adult_result->toArray()) ,
            'data_pediatric_29d1y'  => array_values($pediatric_29d1y_result->toArray()),
            'data_pediatric_1y14y'  => array_values($pediatric_1y14y_result->toArray()),
            'data_pediatric_28d'    => array_values($pediatric_28d->toArray()),
            'total'                 => array_values($total_data)            
        ]);
    }
    // Burkholderia pseudomallei (Bps)
    // from all specimen
    public function get_bps_from_all_specimen(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,
        //$result = $this->rModel->get_bps_from_all_specimen($start_date, $end_date, $lab_string);
        
        /**
         * Deduplicate organism
         * 
         */
        $bps_result = $this->rModel->get_bps_from_all_specimen($start_date, $end_date, $lab_string);
        $bps_result = collect($bps_result)->groupBy('sample_id')->sortBy('pid')->sortBy('collected_date')->all(); // group by month 
        
        $sample_result = $bps_result;
        
        $curr_pid           = 0;
        $current_date       = 0;
        $index_array        = array();
        // deduplicate the result Episode 30days
        foreach($bps_result as $specimen_id => $specimen_group){
            foreach($specimen_group as $ind => $row){
                if($row['pid'] !== $curr_pid){
                    $curr_pid = $row['pid'];
                    $current_date = $row['collected_date']; 
                }else{
                    $date1  = date_create($current_date);
                    $date2  = date_create($row['collected_date']);
                    $diff   = date_diff($date1,$date2);
                    $day    = $diff->format("%a");
                    if( intval($day) < 30){
                        // Save index for deleting
                        $index_array[$specimen_id][] = $ind;
                    }else{
                        $current_date   = $row['collected_date'];
                        $curr_pid       = $row['pid'];
                    }
                }
            }
        }
        // start to remove duplicate result from array
        if(count($index_array) > 0){
            $i = 0;
            foreach($index_array as $in => $row){
                foreach($row as $val){
                    unset($bps_result[$in][$val]);
                }
            }
        }

        //group by month, year, month_n
        //order by month_n, year asc
        $bps_result1 = collect($bps_result)->flatten(1)->values()->all();
        $bps_result1 = collect($bps_result1)->sortByDesc('month_n')->sortByDesc('year')->groupBy('month_n')->groupBy('year')->values()->all(); // group by month 
        
        // Lets group by month and year
        //
        
        $obj = [
            'blood_culture' => 0,
            'body_fluid' => 0,
            'csf' => 0,            
            'pus' => 0,
            'sputum' => 0,
            'swab' => 0,
            'swab_genital' => 0,
            'throat_swab' => 0,
            'tissue' => 0,
            'urine' => 0,
            'month' => 0,
            'month_n' => '',
            'month_year' => '',
            'year' => 0,
        ];
        // Save sample id as string 
        
        $blood_culture  = 0;
        $body_fluid     = 0;
        $csf            = 0;
        $pus            = 0;
        $sputum         = 0;
        $swab           = 0;
        $swab_genital   = 0;
        $tissue         = 0;
        $urine          = 0;
        $throat_swab    = 0;
        $stool          = 0;

        $final_result = array();
        $patient_sample_ids = '';
        foreach($bps_result1 as $res){
            foreach($res as $rows){
                foreach($rows as $row){
                    $obj['month']       = $row['month'];
                    $obj['month_n']     = $row['month_n'];
                    $obj['month_year']  = $row['month']."-".$row['year'];
                    $obj['year']        = $row['year'];
                    $patient_sample_ids .= $row['patient_sample_id'].",";
                    if($row['sample_id'] == 6){
                        $blood_culture += 1;
                    }else if ($row['sample_id'] == 7){
                        $body_fluid += 1;                        
                    }else if ($row['sample_id'] == 8){
                        $csf +=1 ;
                    }else if ($row['sample_id'] == 9 || $row['sample_id'] == 17){
                        $pus +=1;
                    }else if ($row['sample_id'] == 10){
                        $sputum +=1;                        
                    }else if ($row['sample_id'] == 11){
                        $stool +=1;                        
                    }else if ($row['sample_id'] == 12){
                        $swab +=1;
                    }else if ($row['sample_id'] == 13){
                        $swab_genital += 1;
                    }else if ($row['sample_id'] == 14){
                        $throat_swab +=1;
                    }else if ($row['sample_id'] == 15){
                        $tissue +=1;
                    }else if ($row['sample_id'] == 16){
                        $urine += 1;                        
                    }
                    
                }

                $obj['blood_culture']   = $blood_culture;
                $obj['body_fluid']      = $body_fluid;
                $obj['csf']             = $csf;
                $obj['pus']             = $pus;        
                $obj['sputum']          = $sputum;
                $obj['swab']            = $swab;      
                $obj['swab_genital']    = $swab_genital;
                $obj['tissue']          = $tissue;
                $obj['urine']           = $urine;
                $obj['throat_swab']     = $throat_swab;

                $final_result[] = $obj;

                $blood_culture  = 0;
                $body_fluid     = 0;
                $csf            = 0;
                $pus            = 0;
                $sputum         = 0;
                $swab           = 0;
                $swab_genital   = 0;
                $tissue         = 0;
                $urine          = 0;
                $throat_swab    = 0;
            }
        }

        $final_result = collect($final_result)->sortBy('month_n')->values()->all(); // sort by month
        
        
        $patient_sample_ids = substr($patient_sample_ids,0,strlen($patient_sample_ids)-1);
        #$antibiotic_result = $this->rModel->get_antibiotic_bps($patient_sample_ids, $start_date, $end_date, $lab_string);
        $antibiotic_result = $patient_sample_ids == ''? [] : $this->rModel->get_antibiotic_bps($patient_sample_ids, $start_date, $end_date, $lab_string);
        echo json_encode(['data' => array_values($final_result) , 'antibiotic_result' => $antibiotic_result , 'sample_result'=> array_values($sample_result)]);
    }
    // Samonella cases
    public function get_salmonella_from_all_specimen(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string         = substr($lab_string, 0, strlen($lab_string) - 1);// remove
        $orgasnism_ids      = '399, 400, 401,402,403,404,405,406,407,408,409,643,644'; // samonella
        $blood_culture_id   = 6;
        $patient_samples    = $this->rModel->get_patient_sample_by_pathogen($orgasnism_ids, $blood_culture_id, $start_date, $end_date, $lab_string);
        // Deduplicate result
        $curr_pid           = 0;
        $current_date       = 0;
        $organism_id        = 0;
        $patient_sample_ids = ''; // save it
        foreach($patient_samples as $row){
            if($row['pid'] !== $curr_pid){
                $curr_pid               = $row['pid'];
                $current_date           = $row['collected_date']; 
                $patient_sample_ids    .= $row['psample_id'].',';
                $organism_id            = $row['organism_id'];
            }else{
                if($organism_id == $row['organism_id']){
                    $date1  = date_create($current_date);
                    $date2  = date_create($row['collected_date']);
                    $diff   = date_diff($date1,$date2);
                    $day    = $diff->format("%a");

                    if( intval($day) > 30){
                        $patient_sample_ids .= $row['psample_id'].',';
                        $curr_pid           = $row['pid'];
                        $current_date       = $row['collected_date']; 
                        $organism_id        = $row['organism_id'];
                    }
                }
            }
        }
        
        $patient_sample_ids = $patient_sample_ids == '' ? 0 : substr($patient_sample_ids,0,strlen($patient_sample_ids)-1);

        $result = $this->rModel->get_salmonella_from_all_specimen($patient_sample_ids, $start_date, $end_date, $lab_string);    
        
        $startDate  = new DateTime($this->input->post('start_date'));
        $endDate    = new DateTime($this->input->post('end_date'));
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);
        $data     = [];
        $months = [];
        foreach ($period as $dt) {
            $data[$dt->format('M').'-'.$dt->format('y')] = [
                'month' => $dt->format('M'),
                'year' => $dt->format('y'),
                'total' => 0,
            ];
            //$months[$dt->format('m')];
        }
        $result = collect($result)->keyBy(function($item) { return trim($item['month']).'-'.trim($item['year']); });
        $result = collect($data)->merge($result)->map(function($item) { $item['month_year'] = trim($item['month']).'-'.trim($item['year']);  return $item; });
        echo json_encode(['data' => array_values($result->toArray()) , 'psample' => $patient_samples]);
    }
    /**
     * Get CSF + Blood pathogens isolate
     * 6: Blood culture
     * 8: CSF
     */
    public function get_isolated_pathogens(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $sample_ids = '6,8';
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,
        $result = $this->rModel->get_isolated_pathogens($sample_ids,$start_date, $end_date, $lab_string);

        $output = collect($result)->groupBy('csf')->all(); // group by month 
        $csf = collect($result)->sortByDesc('csf')->values()->all();
        $blood = collect($result)->sortByDesc('blood')->values()->all();
        echo json_encode(['csf' => $csf , 'blood'=>$blood]);
    }
    /**
     * Bloodstream pathogen isolate
     * Adult and pediatric
     */
    public function get_bloodstream_pathogens_isolated(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string     = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,
        $result         = $this->rModel->get_bloodstream_pathogens_isolated($start_date, $end_date, $lab_string);  
        
        $graph_result   = $this->rModel->get_bloodstream_pathogen_as_graph($start_date, $end_date, $lab_string);
        $startDate      = new DateTime($this->input->post('start_date'));
        $endDate        = new DateTime($this->input->post('end_date'));
        $startDate->modify('first day of this month');
        $endDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($startDate, $interval, $endDate);
        $data     = [];

        $header         = '<th>Pathogens</th>';
        $number_month   = 0;
        $months         = [];
        $columns = array();
        foreach ($period as $dt) {
            $number_month++;
            $header .= "<th>".$dt->format('M')."</th>";
            $months[$dt->format('m')] = 0;
            array_push($columns, $dt->format('M'));
        }
        $header .= "<th>Total<th>";

        $body = "";
        $body_ = "";

        $total_ = 0;
        $n_     = 0;
        $esc_coli = false;
        $group_by_organism = collect($result)->groupBy('organism_name')->all(); // group by month
        foreach( $group_by_organism as $key => $row){
            $total = 0;
            $body .= "<tr>";
            $body .= "<td><i>".$key."</i></td>";
            foreach($period as $dt){
                $month_n = $dt->format('m');
                $n = 0;
                for($k = 0 ; $k < count($row) ; $k++){
                    if($row[$k]["month_n"] == $month_n){
                        $n      = $row[$k]["total"];
                        $total += $row[$k]["total"];
                    }
                }
                $body .= "<td>".$n."</td>";
            }
            $body .= "<td>".$total."</td>";
            $body .= "</tr>";

            /*
            // Group Esch-coli
            if(in_array($key,array('Escherichia coli','Escherichia coli 1','Escherichia coli 2'))){
                
                if(!$esc_coli){
                    $body_ .= "<tr>";
                    $body_ .= "<td><i>Escherichia coli</i></td>";
                }                
                foreach($period as $dt){
                    $month_n = $dt->format('m');
                    for($k = 0 ; $k < count($row) ; $k++){
                        if($row[$k]["month_n"] == $month_n){
                            $n_     += $row[$k]["total"];
                            $total_ += $row[$k]["total"];
                        }
                    }
                }
                if(!$esc_coli){
                    $body_ .= "<td>".$n_."</td>";
                    $esc_coli = true;
                }
                
            }else{
                $total = 0;
                $body .= "<tr>";
                $body .= "<td><i>".$key."</i></td>";
                foreach($period as $dt){
                    $month_n = $dt->format('m');
                    $n = 0;
                    for($k = 0 ; $k < count($row) ; $k++){
                        if($row[$k]["month_n"] == $month_n){
                            $n      = $row[$k]["total"];
                            $total += $row[$k]["total"];
                        }
                    }
                    $body .= "<td>".$n."</td>";
                }
                $body .= "<td>".$total."</td>";
                $body .= "</tr>";
            }
            */            
        }
        /*
        $body_ .= "<td>".$total_."</td>";
        $body_ .= "</tr>";
        $body .= $body_;
        */
        echo json_encode(['graph_data' => array_values($graph_result), 'body' => $body]);
    }

    public function get_true_pathogen_by_wards(){
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }        
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,
        $request_patient = $this->rModel->get_patient_request_by_wards($start_date, $end_date, $lab_string);
        
        $true_pathogen   = $this->rModel->get_true_pathogen($start_date, $end_date, $lab_string);
        /**
         * Filter first isolate
         * Episode: 30 days
         */                
        
        $curr_pid           = 0;        
        $curr_organism_id   = 0;
        $current_date       = 0;
        $nb_true_pathogen   = 0;
        $curr_sample_source = 0;        
        $true_pathogen_result = array();
        
        $true_pathogen_by_wards = collect($true_pathogen)->groupBy('ID')->values()->all();
        foreach($true_pathogen_by_wards as $ward){
            foreach($ward as $row){
                $curr_sample_source = $row['ID'];
                if($curr_pid !== $row['pid']){
                    $nb_true_pathogen++;
                    $curr_organism_id   = $row['organism_id'];
                    $current_date       = $row['collected_date'];
                    $curr_pid           = $row['pid'];
                }else{
                    if($curr_organism_id == $row['organism_id']){
                        // Check period within 30 days
                        $date1  = date_create($current_date);
                        $date2  = date_create($row['collected_date']);
                        $diff   = date_diff($date1,$date2);
                        $day    = $diff->format("%a");
                        if( intval($day) >= 30){
                            $nb_true_pathogen++;
                            $current_date   = $row['collected_date'];
                            $curr_pid       = $row['pid'];
                        }
                    }else{                        
                        $nb_true_pathogen++;
                        $curr_organism_id   = $row['organism_id'];
                        $current_date       = $row['collected_date'];
                    }
                }
            }
            $true_pathogen_result[] = array(
                'sample_source_id'  => $curr_sample_source,                
                'number_pathogen'   => $nb_true_pathogen
            );
            $nb_true_pathogen = 0; 
        }
        
        $contaminant     = $this->rModel->get_contaminant_rate($start_date, $end_date, $lab_string);

        echo json_encode([
            'request_patient' => array_values($request_patient) , 
            'true_pathogen' => array_values($true_pathogen_result) , 
            'contaminant' => array_values($contaminant)
        ]);
    }

    public function get_rejected_sample() {
        $start_date = $this->input->post('start_date').' '.$this->input->post('start_time');
        $end_date   = $this->input->post('end_date').' '.$this->input->post('end_time');
        $lab_string = "";
        if(empty($this->input->post('laboratories'))){
            $laboratory = array([CamlisSession::getLabSession('labID')]);
        }else {
            $laboratory = $this->input->post('laboratories');
        }
        foreach($laboratory as $val){
            $lab_string .= "(".$val."),";
        }        
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,

        $result_list = $this->rModel->get_rejected_sample($start_date, $end_date, $lab_string);        
        $wards      = collect($result_list)->groupBy(function ($item, $key) {
            return $item['ward'];
        })->all();
        $specimen = collect($result_list)->groupBy(function ($item, $key) {
            return $item['specimen'];
        })->all();; // group by month 
        $html_result = '';
        if(count($wards) > 0){
            $html_result .= '<thead>';
            $html_result .= '<tr>';
            $html_result .= '<th>Specimen \ Wards</th>';
            foreach($wards as $title => $ward){
                $html_result .= '<th>'.$title.'</th>';
            }
            $html_result .= '<th>Rejected comment</th>';
            $html_result .= '</tr>';
            $html_result .= '</thead>';
            $tbody = '<tbody>';
            
            foreach($specimen as $specy_title => $specie){
                $tbody .='<tr>';
                $tbody .='<td>'.$specy_title.'</td>';
                $ward_pointer = 0;
                $cmts         = array();
                
                foreach($wards as $title => $ward){
                    $number = 0;
                    $check = false;
                    // Check ward in specimen 
                    foreach($specie as $row){
                        if($title == $row['ward']){
                            $number = $row['total'];
                            //$cmd[]  = $row['reject_comment'];
                            // check existing comment
                            $comment = json_decode($row['reject_comment']);

                            foreach($comment as $c){
                                $cmts[] = $c;
                            }                            
                            $check  = true;
                            break;
                        }                       
                    }
                    if($check){
                        $tbody .= '<td>'.$number.'</td>';
                    }else{
                        $tbody .= '<td> 0 </td>';
                    }
                    
                }
                $cmt_string = implode(',', $cmts);
                $tbody .= '<td> '.$cmt_string.' </td>';
                
                $cmts = array();
                $tbody .='<tr>';
            }
            $tbody .= '</tbody>';
            $html_result .= $tbody;
        }
        
        echo json_encode([ 'html_string' => $html_result ,'wards' => $wards , 'specimen' => $specimen , 'result' => $result_list]);
    }
}
