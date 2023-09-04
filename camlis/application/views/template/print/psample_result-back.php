<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Result</title>
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/A4_print_tmp.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/psample_result.css'); ?>">
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

		    public function __construct($row_count, $row_per_page)
            {
                $this->row_count    = $row_count;
                $this->row_per_page = $row_per_page;
                $this->setReportFooterText();
            }

            public function setReportFooterText($text = "") {
                $this->report_footer	 = "<div class='report-footer'>";
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
		        $row = str_repeat("<tr><td colspan='4' style='color:transparent;'>&nbsp;</td></tr>", $count);
		        echo $row;
            }

            public function isEndOfPage($showResultHeader = FALSE, $breakPage = TRUE, $isNewSample = FALSE) {
		        if ($this->row_count > $this->row_per_page) {
		            if ($breakPage) {
                        echo "</tbody></table></div>";
                        $this->getReportFooter();
                        echo "</page>";
                        echo "<page size='A4' class='result-print-layout'>";

                        if (!$isNewSample) echo "<div class='result'><table>";
                        if ($showResultHeader) $this->getResultHeader();
                    }

                    $this->row_count = 0;
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

        $reportTemplate = new ReportTemplate(10, 30);
		$reportTemplate->setReportFooterText(isset($laboratoryInfo) ? $laboratoryInfo->address_kh : "");

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
				<span class="lab_nameKH"><?php echo $laboratoryInfo->name_kh; ?></span><br>
				<span class="lab_nameEN"><?php echo $laboratoryInfo->name_en; ?></span><br>
				<span class="result_title">Laboratory Results</span>
			</div>
			<div class="motto_wrapper">
				<span class="country">KINGDOM OF CAMBODIA</span><br>
				<span class="motto">NATION RELIGION KING</span>
			</div>
		</div>
		<div class="psample_info">
			<div class="section1">
				<table>
					<tr>
						<th class="text-right text-top label-name">Patient ID :</th>
						<td class="label-value"><?php echo $patient['pid']; ?></td>
					</tr>
					<tr>
						<th class="text-right text-middle label-name">Name :</th>
						<td class="text-middle label-value"><?php echo $patient['name']; ?></td>
					</tr>
					<tr>
						<th class="text-right text-top label-name">Gender :</th>
						<td class="label-value" style='width:4.8cm;'>
							<?php
								if ($patient['sex'] == 1 || $patient['sex'] == 'M') echo "Male";
								else if ($patient['sex'] == 2 || $patient['sex'] == 'F') echo "Female";
								else echo "";
							?>
						</td>
					</tr>
					<tr>
						<th class="text-right text-top label-name">Address :</th>
						<td class="label-value"><?php echo $patient_address; ?></td>
					</tr>
				</table>
			</div>
			<div class="section2">
				<table>
                    <tr>
                        <th class="text-right text-top label-name">Sample Source :</th>
                        <td class="label-value" style="width:6.2cm;"><?php echo $patient_sample['sample_source_name']; ?></td>
                    </tr>
					<tr>
						<th class="text-right text-middle label-name">Phone Number :</th>
						<td class="label-value text-middle"><?php echo $patient['phone']; ?></td>
					</tr>
					<tr>
						<th class="text-right text-top label-name">Age :</th>
						<td class="label-value">
						<?php
							$days	= getAge($patient['dob']);
							$year	= floor($days / 365);
							$month	= floor(($days % 365) / 30);
							$day	= floor(($days % 365) % 30);
							
							$age	= sprintf("%02d", $year).' Years '.sprintf("%02d", $month).' Months '.sprintf("%02d", $day).' days';
							echo $age;
						?>
						</td>
					</tr>
					<tr>
						<th class="text-right text-top label-name">Clinical :</th>
						<td class="label-value"><?php echo $clinical_history; ?></td>
					</tr>
				</table>
			</div>
		</div>

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
                $tests         = array_values($sample->tests);
                $firstTest     = array_shift($tests);
                $firstTestDate = NULL;
                if (isset($firstTest->patient_test_id)) {
                    $_result        = isset($psample_results[$firstTest->patient_test_id]) ? $psample_results[$firstTest->patient_test_id] : NULL;
                    $firstTestDate  = isset($_result['first_test_date']) ? $_result['first_test_date'] : 'N/A';
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

                                    if ($test->is_heading) {
                                        echo "<tr><td colspan='4' style='padding-left:" . $padding . "px'><b>" . $test->test_name . "</b></td></tr>";
                                    }
                                    else {
                                        $attributes     = "style='padding-left:" . $padding . "px;'";
                                        $ref_range      = isset($ref_ranges[$test->sample_test_id]) ? $ref_ranges[$test->sample_test_id] : "";
                                        $ref_range_text = "";
                                        if (is_array($ref_range) && is_numeric($rowValue)) {
                                            $ref_range_text = $ref_range['min_value'].' '.$ref_range['range_sign'].' '.$ref_range['max_value'];
                                            if (in_array($ref_range['range_sign'], array("-", "≤")) && !((float)$rowValue >= $ref_range['min_value']  && (float)$rowValue <= $ref_range['max_value'])) {
                                                $rowValue = "<b>$rowValue</b>";
                                            }
                                            else if ($ref_range['range_sign'] == "<" && !((float)$rowValue >= $ref_range['min_value'] && (float)$rowValue < $ref_range['max_value'])) {
                                                $rowValue = "<b>$rowValue</b>";
                                            }
                                        }
                                        $reportTemplate->getResultRow("<p class='value-pair'><span class='name'>" . $indents.$test->test_name . "</span></p>", $rowValue, in_array($test->field_type, [1, 2]) ? '' : $test->unit_sign, $ref_range_text);
                                    }

                                    $multiple_row_count += $reportTemplate->countTextAsRow($rowValue, $max_result_type1);
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
            </table>
        </div>
        <?php }}}} ?>
		
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
							echo "<tr><td class='text-left' style='vertical-align:top; width:2.5cm;'><b>".$title."</b></td><td>&#8211;&nbsp;&nbsp;".$comment."</td></tr>";
							
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
						echo "<tr><td class='text-left' style='width:2.2cm;'><b>Comment : </b></td><td>N/A</td></tr>";
                        $reportTemplate->row_count++;
						if ($reportTemplate->isEndOfPage()) { $multiple_row_count = 0; }
					}
						
				?>
			</table>
		</div> <!-- End of Comment -->
	
		<div class="result">
			<table>
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
				<tr>
					<td class='text-left'>Last Date Test : <?php $test_date = DateTime::createFromFormat('Y-m-d', $patient_sample['test_date']); echo $test_date ? $test_date->format('d-M-Y') : 'N/A'; ?></td>
                    <td></td>
                    <td class='text-right'>Report Date : <?php echo date('d-M-Y H:i'); ?></td>
				</tr>
				<tr>
					<td style="padding-left:1cm;">
                    <?php
                        echo isset($laboratory_variables['left-result-footer']['value']) && $laboratory_variables['left-result-footer']['status'] == 1 ? $laboratory_variables['left-result-footer']['value'] : '';
                    ?>
                    </td>
                    <td class="text-center">
                    <?php
                        echo isset($laboratory_variables['middle-result-footer']['value']) && $laboratory_variables['middle-result-footer']['status'] == 1 ? $laboratory_variables['middle-result-footer']['value'] : '';
                    ?>
                    </td>
                    <td class="text-right" style='padding-right:0.7cm;'>
                    <?php
                        echo isset($laboratory_variables['right-result-footer']['value']) && $laboratory_variables['right-result-footer']['status'] == 1 ? $laboratory_variables['right-result-footer']['value'] : '';
                    ?>
                    </td>
				</tr>
			</table>
		</div>
	<!-- Report Footer -->
	<?php $reportTemplate->getReportFooter(); ?>
	</page> <!-- End of Page -->
	
	<?php if (isset($type) && $type == 'print') { ?>
	<script>
		window.print();
	</script>
	<?php } ?>
</body>
</html>