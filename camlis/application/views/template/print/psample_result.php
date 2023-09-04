<?php
    $_app_lang = $app_lang;
    $is_admin = $this->aauth->is_admin();
    $lab_code = '';
    if(!isPMRSPatientID($patient['pid'])){
        $lab_code = $laboratoryInfo->lab_code.'-';
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Result</title>
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/A4_print_tmp.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/psample_result.css?_='.time()); ?>">
	<script src="<?php echo site_url('assets/camlis/js/camlis_print_psample.js'); ?>"></script>
	<script>
		var site_url          = "<?php echo $this->app_language->site_url(); ?>";
		var patient_sample_id = '<?php echo isset($patient_sample['patient_sample_id']) ? $patient_sample['patient_sample_id'] : 0; ?>';
	</script>
	<?php

		class ReportTemplate {
		    private $report_footer  = "";
		    public  $row_count      = 0; //Current number of row in page
		    public  $row_per_page   = 0; //Maximum number of row per page
		    public  $repeat_header  = "";
		    public  $header_info_patient  = "";
		    public  $app_lang  = "";
            public  $is_admin  = "";
            public  $logo  = "";

		    public function __construct($row_count, $row_per_page,$repeat_header,$header_info_patient,$app_lang,$logo,$is_admin)
            {
                $this->row_count    = $row_count;
                $this->row_per_page = $row_per_page;
                $this->repeat_header = $repeat_header;
                $this->header_info_patient = $header_info_patient;
                $this->app_lang = $app_lang;
                $this->is_admin = $is_admin;
                $this->logo = $logo;
                $this->setReportFooterText();
            }

            public function setReportFooterText($text = "",$template_sign="") {
                $this->report_footer	= "<div class='result report-footer1'>";
                $this->report_footer	.= $template_sign;
				$this->report_footer	.= "</div><div class='report-footer'>";
                $this->report_footer	.= $text;
                $this->report_footer	.= "</div>";
            }

            public function getReportFooter() {
                echo $this->report_footer;
            }

            public function getResultHeader($show = TRUE) {
                $header  = '<thead '.($show ? '' : 'class="hide"').'>';
				$header .= '<tr>';
                $header .= '<th class="text-left" style="width:9cm;">Test Name</th>';
                $header .= '<th class="text-left" style="width:5.2cm;">Result</th>';
                $header .= '<th class="text-left" style="width:2.3cm;">Units</th>';
                $header .= '<th class="text-left" style="width:2.5cm;">Ref.Range</th>';
                $header .= '</tr>';
				$header .= '</thead>';
				echo $header;
            }

            /**
             * @param $testNameColumn
             * @param $resultColumn
             * @param $unitColumn
             * @param $refRangeColumn
             * @param string $rowAttributes
             * @param int $field_type 1 => Numeric Result, 2 => Single, Multiple and Text Result
             */
            public function getResultRow($testNameColumn, $resultColumn, $unitColumn, $refRangeColumn, $rowAttributes = "", $field_type = 1) {
                if (is_string($testNameColumn)) $testNameColumn = ["attributes" => "", "value" => $testNameColumn];
                if (is_string($resultColumn))   $resultColumn   = ["attributes" => "", "value" => $resultColumn];
                if (is_string($unitColumn))     $unitColumn     = ["attributes" => "", "value" => $unitColumn];
                if (is_string($refRangeColumn)) $refRangeColumn = ["attributes" => "", "value" => $refRangeColumn];

		        $row  = "<tr $rowAttributes>";
                $row .= "<td class='text-top' style='width:9cm;'>".$testNameColumn['value']."</td>";
                if ($field_type == 1) {
                    $row .= "<td class='text-top' style='width:5.2cm; padding-right: 10px;'>".$resultColumn['value']."</td>";
                    $row .= "<td class='text-top' style='width:2.3cm;'>".$unitColumn['value']."</td>";
                    $row .= "<td class='text-top' style='width:2.5cm;'>".$refRangeColumn['value']."</td>";
                } else {
                    $row .= "<td class='text-top' colspan='3' style='min-width: 2.7cm;'>".$resultColumn['value']."</td>";
                }
                $row .= "</tr>";
		        echo $row;
            }

            public function getBlankRow($count = 1) {
				if($count>0){
					$row = str_repeat("<tr><td colspan='4' style='color:transparent;'>&nbsp;</td></tr>", $count);
					echo $row;
				}
            }

            public function isEndOfPage($showResultHeader = FALSE, $breakPage = TRUE, $isNewSample = FALSE) {

		        if ($this->row_count > $this->row_per_page) {

		            if ($breakPage) {
						if($this->repeat_header){
							echo "</tbody></table></div>";
							$this->getReportFooter();
							echo "</page>";
							echo '<page size="A4" class="result-print-layout">
									<div class="header">  
									<div class="logo"><img src="'.$this->logo.'" alt="Logo"></div>
									
									<div class="title">';
									if($this->app_lang=='kh'){
										echo '<span class="lab_nameKH">'.$this->repeat_header[0].'</span><br>';
									}else{
										echo '<span class="lab_nameEN">'.$this->repeat_header[1].'</span><br>';
									}

                            if($this->is_admin==1) {
                                //echo '<span class="result_title">' . _t('global.laboratory') . '</span>';
                            }else{
                                //echo '<span class="result_title">' . _t('global.laboratory_result') . '</span>';
                            }


									echo '<span class="result_title">' . _t('global.laboratory_result') . '</span></div>
									<div class="motto_wrapper">
										<span class="country">'._t('global.kingdom').'</span><br>
										<span class="motto">'._t('global.nation').'</span>
									</div> 
								</div>';
							echo $this->header_info_patient;


                        if (!$isNewSample) echo "<div class='result'><table>";
                        if ($showResultHeader) $this->getResultHeader();}
                    }

                    $this->row_count = 10;
                    return TRUE;
                }

                return FALSE;
            }

            public function countTextAsRow($text, $max) {
                //$max : Maximum characters for one row
                $row = ceil(strlen($text) / $max) - 1; //-1 for normal single row
                return $row < 0 ? 0 : $row;
            }
        }
		$test_date = DateTime::createFromFormat('Y-m-d', $patient_sample['test_date']);

		// repeat footer signatur
		$template_sign = "<table>
		<tr>
			<td class='text-left'>Last Date Test : ".($test_date ? $test_date->format('d-M-Y') : 'N/A')."</td>
			<td></td>
			<td class='text-right'>Report Date : ".date('d-M-Y H:i')."</td>
		</tr>
		<tr>
			<td style='padding-left:1cm;'>
			".(isset($laboratory_variables['left-result-footer']['value']) && $laboratory_variables['left-result-footer']['status'] == 1 ? $laboratory_variables['left-result-footer']['value'] : '')."
			</td>
			<td class='text-center'>
			".(isset($laboratory_variables['middle-result-footer']['value']) && $laboratory_variables['middle-result-footer']['status'] == 1 ? $laboratory_variables['middle-result-footer']['value'] : '')."
			</td>
			<td class='text-right' style='padding-right:0.7cm;'>
			".(isset($laboratory_variables['right-result-footer']['value']) && $laboratory_variables['right-result-footer']['status'] == 1 ? $laboratory_variables['right-result-footer']['value'] : '')." </td></tr></table>";
		// end

		// repeat header kingdom
		$header_info = array($laboratoryInfo->name_kh,$laboratoryInfo->name_en);
		// repeat header patient
		$_sex="";
		if ($patient['sex'] == 1 || $patient['sex'] == 'M') $_sex = _t('global.male');
		else if ($patient['sex'] == 2 || $patient['sex'] == 'F') $_sex = _t('global.female');
		else $_sex = "";

    /*$days	= getAge($patient['dob']);
   $year	= floor($days / 365);
   $month	= floor(($days % 365) / 30);
   $day	= floor(($days % 365) % 30);
   $_age	= sprintf("%02d", $year).' Years '.sprintf("%02d", $month).' Months '.sprintf("%02d", $day).' days';
   */
    $_age	= strtolower(getAging($patient['dob']));


    $village_name  = 'village_'.$app_lang;
    $commune_name  = 'commune_'.$app_lang;
    $district_name = 'district_'.$app_lang;
    $province_name = 'province_'.$app_lang;
    $addrF         = [$patient[$village_name], $patient[$commune_name], $patient[$district_name], $patient[$province_name]];

    $header_info_patient = '<div class="psample_info"><div class="section1"><table>
                <tr>
                    <th class="text-right text-top label-name">'._t('global.patient_id').' :</th>
                    <td class="label-value">'.$patient['patient_code'].'</td>
                </tr>
                <tr>
                    <th class="text-right text-middle label-name">'._t('global.patientname').' :</th>
                    <td class="text-middle label-value">'.$patient['name'].'</td>
                </tr>
                <tr>
                    <th class="text-right text-top label-name">'._t('global.patient_gender').' :</th>
                    <td class="label-value" style="width:4.8cm; font-size: 13px;">'.$_sex.'</td>
                </tr>
                <tr>
                    <th class="text-right text-top label-name">'._t('global.patient_address').' :</th>
                    <td class="label-value" style="font-size: 13px;">'.implode(' - ', $addrF).'</td>
                </tr>
            </table></div>
            <div class="section2">
            <table>
                <tr>
                    <th class="text-right text-top label-name">'._t('global.sample_source').' :</th>
                    <td class="label-value">'.$patient_sample['sample_source_name'].'</td>
                </tr>
                <tr>
                    <th class="text-right text-middle label-name">'._t('global.patient_phone_number').' :</th>
                    <td class="label-value text-middle">'.$patient['phone'].'</td>
                </tr>
                <tr>
                    <th class="text-right text-top label-name">'._t('global.patient_age').' :</th>
                    <td class="label-value">'.$_age.'</td>
                </tr>
                <tr>
                    <th class="text-right text-top label-name">'._t('global.patient_clinical').' :</th>
                    <td class="label-value">'.$patient_sample['clinical_history'].'</td>
                </tr>
            </table>
        </div></div>';

/*$days	= getAge($patient['dob']);
$days	= getAging($patient['dob']);
/*year	= floor($days / 365);
$month	= floor(($days % 365) / 30);
$day	= floor(($days % 365) % 30);

$_age	= sprintf("%02d", $year).' Years '.sprintf("%02d", $month).' Months '.sprintf("%02d", $day).' days';
*/
		$_age	= sprintf(getAging($patient['dob']));
		$logo = site_url('assets/camlis/images/moh_logo.png');
		if (isset($laboratoryInfo->photo) && !empty($laboratoryInfo->photo)  && file_exists('./assets/camlis/images/laboratory/'.$laboratoryInfo->photo)) {
			$logo = site_url('assets/camlis/images/laboratory/'.$laboratoryInfo->photo);
		}


        $reportTemplate = new ReportTemplate(10, 34,$header_info,$header_info_patient,$_app_lang,$logo,$is_admin);

		$reportTemplate->setReportFooterText((isset($laboratoryInfo) ? $laboratoryInfo->address_kh : ""),$template_sign);
		$patient_address    = isset($patient['province_en']) ? $patient['province_en'] : "";
        $clinical_history   = $patient_sample['clinical_history'];

        $addressRow			= $reportTemplate->countTextAsRow($patient_address, 31);
        $clinicalRow		= $reportTemplate->countTextAsRow($clinical_history, 25);
        $reportTemplate->row_count	+= $addressRow > $clinicalRow ? ceil($addressRow / 1.5) : ceil($clinicalRow / 1.5);


        $max_test_name      = 40;
        $max_result_type1   = 24; //Numeric result
        $max_result_type2   = 53; //Single/Multiple/Test result
        $multiple_row_count = 0;


	?>
</head>
<body>
	<!--  size="A4"  -->
	<page size="A4" class="result-print-layout">

		<div class="header">
			<div class="logo">
                <?php
                    $logo = site_url('assets/camlis/images/moh_logo.png');
                    if (isset($laboratoryInfo->photo) && !empty($laboratoryInfo->photo)  && file_exists('./assets/camlis/images/laboratory/'.$laboratoryInfo->photo)) {
                        $logo = site_url('assets/camlis/images/laboratory/'.$laboratoryInfo->photo);
                    }
                ?>
				<img src="<?php echo $logo; ?>" alt="Logo">
			</div>
			<div class="title">
            	<?php if($_app_lang=='kh'){ ?>
				<span class="lab_nameKH"><?php echo $laboratoryInfo->name_kh; ?></span>
                <?php } else{?>
				<span class="lab_nameEN"><?php echo $laboratoryInfo->name_en; ?></span>
                <?php } ?><br />

                <!-- checking is admin -->
                <!--?php if($this->aauth->is_admin()==1){?-->
				    <!--span class="result_title">< ?php echo _t('global.laboratory'); ?></span-->
                <!--?php }else{ ?-->
                    <span class="result_title"><?php echo _t('global.laboratory_result'); ?></span>
                <!--?php } ?-->


			</div>
			<div class="motto_wrapper">
				<span class="country"><?php echo _t('global.kingdom'); ?></span><br>
				<span class="motto"><?php echo _t('global.nation'); ?></span>
			</div>
		</div>

		<!-- repeat patient information -->
		<?php echo $header_info_patient;?>

        <!-- Display Sample Info/Result -->
        <?php
        if (isset($psample_tests) && count($psample_tests) > 0) {
        foreach($psample_tests AS $psample_test) {
            if (isset($psample_test->samples) && count($psample_test->samples) > 0) {
                foreach ($psample_test->samples AS $sample) {
                    //Check end of Page
                    $reportTemplate->row_count += 3.5;
                    if ($reportTemplate->isEndOfPage(FALSE, TRUE, TRUE)) {
                        if ($reportTemplate->row_count == 0) $reportTemplate->row_count += 3.5;
                        $multiple_row_count = 0;
                    }

                    //Get first Test Date from result
                    $firstTestDate = NULL;
                    foreach ($sample->tests as $test) {
                        if (!empty($psample_results[$test->patient_test_id])) {
                            $firstTestDate  = isset($psample_results[$test->patient_test_id]['first_test_date']) ? $psample_results[$test->patient_test_id]['first_test_date'] : NULL;
                            break;
                        }
                    }

                    //Sample Description
                    $description = isset($psample_details[$sample->department_sample_id]['sample_description']) && !empty($psample_details[$sample->department_sample_id]['sample_description']) ? " - ".$psample_details[$sample->department_sample_id]['sample_description'] : '';

        ?>
        <!-- Sample Info -->
		<div class="sample_info">
			<table>
				<tr>
                    <th class="text-left"><?php echo $psample_test->department_name; ?></th>
					<th class="text-left">Sample Number</th>
					<th class="text-left">Requested by</th>
					<th class="text-left">Received Date</th>
					<th class="text-left">Test Date</th>
				</tr>
				<tr>
                    <td class="text-top no-wrap"><b><?php echo $sample->sample_name.$description; ?></b></td>
					<td class="text-top no-wrap"><?php echo $patient_sample['sample_number']; ?></td>
					<td class="text-top no-wrap"><?php echo $patient_sample['requester_name']; ?></td>
					<td class="text-top no-wrap">
                    <?php
                        $received_date = DateTime::createFromFormat('Y-m-d', $patient_sample['received_date']);
                        $received_time = DateTime::createFromFormat('H:i:s', $patient_sample['received_time']);
                        echo $received_date ? $received_date->format('d-M-Y') : 'N/A';
                        echo $received_time ? '&nbsp;'.$received_time->format('H:i')   : '';
                    ?>
                    </td>
					<td class="text-top no-wrap">
                    <?php
                        $test_date = DateTime::createFromFormat('Y-m-d', $firstTestDate);
                        echo $test_date ? $test_date->format('d-M-Y') : 'N/A';
                    ?>
                    </td>
				</tr>
			</table>
		</div>
        <!-- Result -->
        <div class="result">
            <table>
                <?php $reportTemplate->getResultHeader(); ?>
                <tbody>
                    <?php
                        if (isset($sample->tests) && count($sample->tests) > 0) {
                            foreach ($sample->tests as $testIndex => $test) {
                                $padding        = (int)$test->level * 15;
                                $indents        = str_repeat('&nbsp;', (int)$test->level == 0 ? 0 : (int)$test->level + 2);
                                $psample_result = isset($psample_results[$test->patient_test_id]) ? $psample_results[$test->patient_test_id] : null;

                                //Single and Multiple Result
                                if (is_array($psample_result['result']) && count($psample_result['result']) > 0 && !$test->is_rejected && !$test->is_heading)
                                {
                                    $i = 1;
                                    $psample_result['result'] = array_values($psample_result['result']);
                                    foreach ($psample_result['result'] as $organismIndex => $resultItem) {
                                        //Show Test info for first result only
                                        $testName  = $i == 1 ? "<p class='value-pair'><span class='name'>" . $indents.$test->test_name . "</span></p>" : "";
                                        $unit_sign = "";
                                        $ref_range = "";

                                        //Organism
                                        $organism_result  = "<p class='value-pair'>";
                                        $organism_result .= "<span class='name'>" . $resultItem["organism_name"] . "</span>";
                                        $organism_result .= "<span class='name-result'>" . $resultItem["quantity"] . "</span>";
                                        $organism_result .= "</p>";

                                        $attributes = "style='padding-left:" . $padding . "px;'";
                                        $reportTemplate->getResultRow($testName, $resultItem["organism_name"]."&nbsp;<b>".$resultItem["quantity"]."</b>", $unit_sign, $ref_range, "", 2);

                                        //count multiple row
                                        $multiple_row_count += $reportTemplate->countTextAsRow($i == 1 ? str_replace('&nbsp;', '.', $indents).$test->test_name : "", $max_test_name);
                                        $multiple_row_count += $reportTemplate->countTextAsRow($resultItem["organism_name"]." ".$resultItem["quantity"], $max_result_type2);
                                        if ($multiple_row_count >= 2) {
                                            $reportTemplate->row_count += floor($multiple_row_count / 2);
                                            $multiple_row_count = $multiple_row_count % 2;
                                        }

                                        $reportTemplate->row_count++;
                                        if ($reportTemplate->isEndOfPage(TRUE && !($organismIndex == count($psample_result['result']) - 1))) {
                                            $multiple_row_count = 0;
                                        }

                                        //Antibiotic
                                        if (count($resultItem["antibiotic"]) > 0) {
                                            foreach ($resultItem["antibiotic"] as $antibioticIndex => $antibiotic) {
                                                if (isset($antibiotic["invisible"]) && $antibiotic["invisible"] > 0) continue;

                                                $sensitivity = isset($sensitivity_type[$antibiotic["sensitivity"]]['abbr']) ? $sensitivity_type[$antibiotic["sensitivity"]]['abbr'] : "N/A";
                                                $anti  = "<p class='value-pair'>";
                                                $anti .= "<span class='name'>&nbsp;&nbsp;&#8211; " . $antibiotic["antibiotic_name"] . "</span>";
                                                $anti .= "<span class='name-result'>" . $sensitivity . "</span>";
                                                $anti .= "</p>";

                                                $reportTemplate->getResultRow("", $anti, "", "", "", 2);

                                                $multiple_row_count += $reportTemplate->countTextAsRow($antibiotic["antibiotic_name"] . " " . $sensitivity, $max_result_type2 - 3);
                                                if ($multiple_row_count >= 2) {
                                                    $reportTemplate->row_count += floor($multiple_row_count / 2);
                                                    $multiple_row_count = $multiple_row_count % 2;
                                                }

                                                $reportTemplate->row_count++;
                                                if ($reportTemplate->isEndOfPage(TRUE && !($antibioticIndex == count($resultItem["antibiotic"]) - 1))) {
                                                    $multiple_row_count = 0;
                                                }
                                            }
                                        }

                                        $i++;
                                    }
                                }
                                else
                                {
                                    $rowValue = $test->is_rejected ? "Rejected" : (isset($psample_result["result"]) && !is_array($psample_result["result"]) ? $psample_result["result"] : "Pending");
                                    $newRowValue = explode('%',$rowValue );
                                    $_rowValue = count($newRowValue)==1?$newRowValue[0]:trim($newRowValue[1]);

                                    if ($test->is_heading) {
                                        echo "<tr><td colspan='4' style='padding-left:" . $padding . "px'><b>" . $test->test_name . "</b></td></tr>";
                                    }
                                    else {
                                        $attributes     = "style='padding-left:" . $padding . "px;'";
                                        $ref_range      = isset($ref_ranges[$test->sample_test_id]) ? $ref_ranges[$test->sample_test_id] : "";
                                        $ref_range_text = "";
                                        if (is_array($ref_range)) { //is_numeric($rowValue)
                                            $ref_range_text = $ref_range['min_value'].' '.$ref_range['range_sign'].' '.$ref_range['max_value'];
                                            if (in_array($ref_range['range_sign'], array("-", "≤")) && !((float)$_rowValue >= $ref_range['min_value']  && (float)$_rowValue <= $ref_range['max_value'])) {
                                                $rowValue = "<b>$rowValue</b>";
                                            }
                                            else if ($ref_range['range_sign'] == "<" && !((float)$_rowValue >= $ref_range['min_value'] && (float)$_rowValue < $ref_range['max_value'])) {
                                                $rowValue = "<b>$rowValue</b>";
                                            }
                                        }
                                        $reportTemplate->getResultRow("<p class='value-pair'><span class='name'>" . $indents.$test->test_name . "</span></p>", str_replace(' ', '&nbsp;', $rowValue), in_array($test->field_type, [1, 2]) ? '' : $test->unit_sign, $ref_range_text);
                                    }

                                    $multiple_row_count += $reportTemplate->countTextAsRow($_rowValue, $max_result_type1);
                                    $multiple_row_count += $reportTemplate->countTextAsRow(str_replace('&nbsp;', '.', $indents).$test->test_name, $max_test_name);
                                    if ($multiple_row_count >= 2) {
                                        $reportTemplate->row_count += floor($multiple_row_count / 2);
                                        $multiple_row_count = $multiple_row_count % 2;
                                    }

                                    $reportTemplate->row_count++;
                                    if ($reportTemplate->isEndOfPage(TRUE && !($testIndex == count($sample->tests) - 1))) {
                                        $multiple_row_count = 0;
                                    }
                                }

                                if (($testIndex == count($sample->tests) - 1)) {
                                    $reportTemplate->getBlankRow();
                                    $reportTemplate->row_count += 1;
                                }
                            }
                        }
                    ?>

                </tbody>

                <?php
                    if($sample->result_comment!='') {
                        //$_comments =preg_split('/\n|\r\n?/',$sample->result_comment);
                        /*$comment_result = '';
                        foreach($_comments as $_r){
                            if($_r!=null){
                                $comment_result.="- $_r<br />\n";
                            }
                        }*/
                        $comment_result = preg_replace('/\n|\r\n?/', '<br/>', $sample->result_comment);
                    echo "<tr>
                            <table>
                                <tr>
                                    <td class='text-left' style='vertical-align:top; width:2.5cm;'><b>Comment :</b></td>
                                    <td> " . $comment_result . "</td>
                                </tr>
                            </table>
                    </tr>";
                    }
                ?>
            </table>
        </div>
        <?php       } // $psample_test as sample
                }// end if two
            }// end $psample_tests
        }// end if one
        ?>

		<div class="result">
			<?php
				if (isset($sensitivity_type) && count($sensitivity_type) > 0) {

					$str = array();
					foreach($sensitivity_type as $typ) {
						$str[] = "<b>".$typ["abbr"]."</b> = ".$typ["full"];
					}

					echo "<table>";
					echo "<tr><td style='padding-top:10px;'>";
					echo "<b>Note : &nbsp;</b>";
					echo implode(', ', $str);
					echo "</td></tr></table>";

					$reportTemplate->row_count++;
					if ($reportTemplate->isEndOfPage()) { $multiple_row_count = 0; }
				}
			?>
		</div>

		<div class="result">
			<table>
				<?php
				    if ($reportTemplate->row_count > 0) {
                        $reportTemplate->getBlankRow();
                        $reportTemplate->row_count++;
                    }

                    $comments	= explode('<br/>', $patient_sample['result_comment']);
					$i		    = 1;

					foreach ($comments as $comment) {
					    $title      = $i == 1 ? 'Comment :' : '';
                        $comment    = trim(substr(trim($comment), 0, 1) == '-' ? substr(trim($comment), 1) : $comment);
						if (!empty($comment)) {
							//echo "<tr><td class='text-left' style='vertical-align:top; width:2.5cm;'><b>".$title."</b></td><td>&#8211;&nbsp;&nbsp;".$comment."</td></tr>";

							$i++;
							$multiple_row_count += $reportTemplate->countTextAsRow('...'.$comment, 112);
							if ($multiple_row_count >= 2) {
                                $reportTemplate->row_count += floor($multiple_row_count / 2);
								$multiple_row_count = $multiple_row_count % 2;
							}
                            $reportTemplate->row_count++;
							if ($reportTemplate->isEndOfPage()) { $multiple_row_count = 0; }
						}
					}
					//No comment
					if ($i == 1) {
						//echo "<tr><td class='text-left' style='width:2.2cm;'><b>Comment : </b></td><td></td></tr>";
                        $reportTemplate->row_count++;
						if ($reportTemplate->isEndOfPage()) { $multiple_row_count = 0; }
					}

				?>
			</table>
		</div> <!-- End of Comment -->
	<?php
					if (($reportTemplate->row_per_page - $reportTemplate->row_count) < 3) {
                        $reportTemplate->row_count	+= 5;
						if (!$reportTemplate->isEndOfPage()) {
							$reportTemplate->row_count -= 5;
						}
					}

					$avRow	= $reportTemplate->row_per_page - $reportTemplate->row_count;
					if ($avRow < $reportTemplate->row_per_page) {
                        $reportTemplate->getBlankRow($avRow - 5);
                        $reportTemplate->row_count += $avRow - 5;
                        if ($reportTemplate->isEndOfPage()) { $multiple_row_count = 0; }
					}
					if ($reportTemplate->row_count > 0) $reportTemplate->getBlankRow();
		 ?>
	<!-- Report Footer -->
	<?php $reportTemplate->getReportFooter(); ?>
	</page> <!-- End of Page -->
    <div style="co"></div>

	<?php if (isset($type) && $type == 'print') { ?>
	<script>
		window.print();
	</script>
	<?php } ?>
</body>
</html>