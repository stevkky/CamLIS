<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Rejected Test</title>
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/A4_print_tmp.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/psample_result.css?_='.time()); ?>">
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
                $header .= '<th class="text-left">Test Name</th>';
                $header .= '</tr>';
				$header .= '</thead>';
				echo $header;
            }

            /**
             * @param $testNameColumn
             * @param $resultColumn
             * @param string $rowAttributes
             * @param int $field_type 1 => Numeric Result, 2 => Single, Multiple and Text Result
             */
            public function getResultRow($testNameColumn, $resultColumn, $rowAttributes = "", $field_type = 1) {
                if (is_string($testNameColumn)) $testNameColumn = ["attributes" => "", "value" => $testNameColumn];
                if (is_string($resultColumn))   $resultColumn   = ["attributes" => "", "value" => $resultColumn];

		        $row  = "<tr $rowAttributes>";
                $row .= "<td class='text-top'>".$testNameColumn['value']."</td>";
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

									echo '<span class="result_title">' . ($this->app_lang == 'kh' ? 'ទម្រង់បដិសេធសំណាក' : 'Sample Rejection Form') . '</span></div>
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
			<td class='text-left'></td>
			<td></td>
			<td class='text-right'>Report Date : ".date('d-M-Y H:i')."</td>
		</tr>
		<tr>
			<td style='padding-left:1cm;'></td>
			<td class='text-center'></td>
			<td class='text-right' style='padding-right:0.7cm;'>".(isset($laboratory_variables['right-result-footer']['value']) && $laboratory_variables['right-result-footer']['status'] == 1 ? $laboratory_variables['right-result-footer']['value'] : '')."</td></tr></table>";
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
    $age = calculateAge($patient['dob'], $patient_sample['collected_date']);
    $_age = $age->y.' '._t('global.year').' '.$age->m.' '._t('global.month').' '.($age->days > 0 ? $age->d : 1).' '._t('global.day');

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


        $reportTemplate = new ReportTemplate(10, 36,$header_info,$header_info_patient,$app_lang,$logo,$this->aauth->is_admin());

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
            	<?php if($app_lang=='kh'){ ?>
				<span class="lab_nameKH"><?php echo $laboratoryInfo->name_kh; ?></span>
                <?php } else{?>
				<span class="lab_nameEN"><?php echo $laboratoryInfo->name_en; ?></span>
                <?php } ?><br />
                    <span class="result_title">
                        <?php
                            if ($app_lang == "kh") {
                                echo 'ទម្រង់បដិសេធសំណាក';
                            } else {
                                echo 'Sample Rejection Form';
                            }
                        ?>
                    </span>
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

                                $reject_comment = preg_replace('/\n|\r\n?/', '<br/>', $test->reject_comment);
                                echo "<p id='display_reject_comment_1' style='display: none'>";
                                //print_r($test);
                                print_r($reject_comment);
                                echo "</p>";
                                $rowValue = $test->is_rejected ? "Rejected" : (isset($psample_result["result"]) && !is_array($psample_result["result"]) ? $psample_result["result"] : "Pending");
                                $newRowValue = explode('%',$rowValue );
                                $_rowValue = count($newRowValue)==1?$newRowValue[0]:trim($newRowValue[1]);

                                if ($test->is_heading) {
                                    echo "<tr><td  colspan='4' style='padding-left:" . $padding . "px'><b>" . $test->test_name . "</b></td></tr>";
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
                                    $reportTemplate->getResultRow($test->group_result, "");
                                }

                                $multiple_row_count += $reportTemplate->countTextAsRow($_rowValue, $max_result_type1);
                                $multiple_row_count += $reportTemplate->countTextAsRow(str_replace('&nbsp;', '.', $indents).$test->group_result, $max_test_name);
                                if ($multiple_row_count >= 2) {
                                    $reportTemplate->row_count += floor($multiple_row_count / 2);
                                    $multiple_row_count = $multiple_row_count % 2;
                                }

                                $reportTemplate->row_count++;
                                if ($reportTemplate->isEndOfPage(TRUE && !($testIndex == count($sample->tests) - 1))) {
                                    $multiple_row_count = 0;
                                }

                                if (($testIndex == count($sample->tests) - 1)) {
                                    $reportTemplate->getBlankRow();
                                    $reportTemplate->row_count += 1;
                                }
                            }
                        }
                    ?>

                </tbody>
            </table>
        </div>
        <?php       } // $psample_test as sample
                }// end if two
        ?>
        <!--
        * 29/08/2018
        * Add this block for display reject comment for each department 
        -->
        <div class="result">
            <table>
                <tr>
                    <td class='text-left' style='vertical-align:top; width:2.5cm;'><b>Comment :</b></td>
                    <td>
                        <?php 
                            echo $reject_comment; 
                            $reportTemplate->row_count++; 
                            if ($reportTemplate->isEndOfPage()) { 
                                $multiple_row_count = 0; 
                            } 
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <!-- End comment -->
        <?php
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

                    $comment_result = preg_replace('/\n|\r\n?/', '<br/>', $patient_sample['reject_comment']);
                    $comments	= explode('<br/>', $comment_result);
					$i		    = 1;

					foreach ($comments as $comment) {
					    $title      = $i == 1 ? 'Comment :' : '';
                        $comment    = trim(substr(trim($comment), 0, 1) == '-' ? substr(trim($comment), 1) : $comment);
						if (!empty($comment)) {
							echo "<tr><td class='text-left' style='vertical-align:top; width:2.5cm;'><b>".$title."</b></td><td>".$comment."</td></tr>";

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
				?>
			</table>
		</div> <!-- End of Comment -->
	<?php
//					if (($reportTemplate->row_per_page - $reportTemplate->row_count) < 3) {
//                        $reportTemplate->row_count	+= 5;
//						if (!$reportTemplate->isEndOfPage()) {
//							$reportTemplate->row_count -= 5;
//						}
//					}
//
//					$avRow	= $reportTemplate->row_per_page - $reportTemplate->row_count;
//					if ($avRow < $reportTemplate->row_per_page) {
//                        $reportTemplate->getBlankRow($avRow - 5);
//                        $reportTemplate->row_count += $avRow - 5;
//                        if ($reportTemplate->isEndOfPage()) { $multiple_row_count = 0; }
//					}
//					if ($reportTemplate->row_count > 0) $reportTemplate->getBlankRow();
		 ?>
	<!-- Report Footer -->
	<?php $reportTemplate->getReportFooter(); ?>
	</page> <!-- End of Page -->
    <div style="display: none;" id="testing_code">
    <?php
    echo "<pre>";
    print_r($patient_sample);
    echo "</pre>";
    echo "<pre>";
    print_r($patient_sample['reject_comment']);
    echo "</pre>";
    echo "<pre>";
    print_r($psample_details);
    echo "</pre>";
    echo "<pre>";
    print_r($psample_tests);
    echo "</pre>";
    ?>
    </div>

	<?php if (isset($action) && $action == 'print') { ?>
	<script>
		window.print();
	</script>
	<?php } ?>
</body>
</html>