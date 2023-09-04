<?php
    $laboratory_logo_url = site_url('assets/camlis/images/moh_logo.png');
    if (isset($patient_sample_laboratory) && !empty($patient_sample_laboratory->get('photo'))  && file_exists('./assets/camlis/images/laboratory/'.$patient_sample_laboratory->get('photo'))) {
        $laboratory_logo_url = site_url('assets/camlis/images/laboratory/'.$patient_sample_laboratory->get('photo'));
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Result</title>
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/patient_sample_result.css?_='.time()) ?>">
</head>
<body>    
    <page size="A4">
        <table border="0" width="100%">
            <thead>
                <tr>
                    <th style="width:100%">
                        <table width="100%" border="0" style="margin-bottom: 5px;">
                            <tr>
                                <td style="width: 110px;">
                                    <img src="<?php echo $laboratory_logo_url; ?>" alt="Logo" style="width: 100px;">
                                </td>
                                <td class="text-top text-center">
                                    <div class="KhmerMoulLight" style="font-size: 13pt; font-weight: bold;"><?php echo _t('cambodia_official_name') ?></div>
                                    <div class="KhmerMoulLight" style="font-size: 10pt;"><?php echo _t('cambodia_motto') ?></div>
                                    <div class="tacteng" style="font-size: 24pt; margin-bottom: 10px;">3</div>
                                    <div class="KhmerMoulLight" style="font-size: 12.5pt; font-weight: bold;"><?php echo $patient_sample_laboratory->get('name_'.$app_lang); ?></div>
                                    <div class="KhmerMoulLight" style="font-size: 12.5pt; font-weight: bold;"><?php echo _t('laboratory_result'); ?></div>
                                </td>
                                <td style="width: 110px;"></td>
                            </tr>
                        </table>
                    </th>
                </tr>
                <tr>
                    <th>
                        <table width="100%" border="0" class="patient-info">
                            <tr>
                                <th class="text-right text-no-wrap" style="width: 2.5cm;"><?php echo _t('patient_id'); ?> :</th>
                                <td style="width: 6.5cm;"><?php echo $patient_info['patient_code']; ?></td>
                                <th class="text-right text-no-wrap" style="width: 3.5cm"><?php echo _t('sample_source'); ?> :</th>
                                <td><?php echo $patient_sample['sample_source_name']; ?></td>
                            </tr>
                            <tr>
                                <th class="text-right text-no-wrap"><?php echo _t('patient_name'); ?> :</th>
                                <td><?php echo $patient_info['name']; ?></td>
                                <th class="text-right text-no-wrap"><?php echo _t('sex'); ?> :</th>
                                <td class="text-no-wrap">
                                    <?php echo $patient_info['sex'] == 'M' ? _t('male') : _t('female'); ?>
                                    &nbsp;&nbsp;
                                    <?php
                                        echo '<b>'._t('age').' : </b>';
                                        $age = calculateAge($patient_info['dob'], $patient_sample['collected_date']);
                                    ?>
                                    <span class="age-year"></span> <?php echo $age->y.' '._t('global.year') ?> &nbsp;
                                    <span class="age-month"></span> <?php echo $age->m.' '._t('global.month') ?> &nbsp;
                                    <span class="age-day"></span> <?php echo ($age->days > 0 ? $age->d : 1).' '._t('global.day') ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right text-no-wrap"><?php echo _t('phone'); ?> :</th>
                                <td><?php echo $patient_info['phone']; ?></td>
                                <th class="text-right text-no-wrap"><?php echo _t('diagnosis'); ?> :</th>
                                <td><?php echo $patient_sample['clinical_history']; ?></td>
                            </tr>
                            <tr>
                                <th class="text-right text-no-wrap"><?php echo _t('address'); ?> :</th>
                                <td colspan="3">
                                    <?php
                                        $village  = !empty($patient_info['village_'.$app_lang]) ? $patient_info['village_'.$app_lang] : null;
                                        $commune  = !empty($patient_info['commune_'.$app_lang]) ? $patient_info['commune_'.$app_lang] : null;
                                        $district = !empty($patient_info['district_'.$app_lang]) ? $patient_info['district_'.$app_lang] : null;
                                        $province = !empty($patient_info['province_'.$app_lang]) ? $patient_info['province_'.$app_lang] : null;
                                        if ($village) { echo $app_lang == "kh" ? _t('village').' '.$village : $village.' '._t('village'); echo ' - '; }
                                        if ($commune) { echo $app_lang == "kh" ? _t('commune').' '.$commune : $commune.' '._t('commune'); echo ' - '; }
                                        if ($district) { echo $app_lang == "kh" ? _t('district').' '.$district : $district.' '._t('district'); echo ' - '; }
                                        if ($province) { echo $app_lang == "kh" ? _t('province').' '.$province : $province.' '._t('province'); }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td width="100%">
                        <table width="100%" border="0">
                            <tr>
                                <td colspan="4" style="height: 134px;">&nbsp;</td>
                            </tr>
                        </table>
                </tfoot>
            <?php
            $department_sample_count = 0;
            foreach ($patient_sample_tests as $department) {
                foreach ($department->samples as $sample) {
                    //Get first Test Date from result
                    $firstTestDate = NULL;
                    foreach ($sample->tests as $test) {
                        if (!empty($patient_sample_results[$test->patient_test_id])) {
                            $firstTestDate  = isset($patient_sample_results[$test->patient_test_id]['first_test_date']) ? $patient_sample_results[$test->patient_test_id]['first_test_date'] : NULL;
                            break;
                        }
                    }

                    $department_sample_count++;
            ?>
            <tbody class="sample-result">
                <tr>
                    <td>
                        <table width="100%" border="0" class="department-sample-info">
                            <tr>
                                <th class='sourceserifpro-bold'><?php echo $department->department_name; ?></th>
                                <th class='sourceserifpro-bold'><?php echo _t('sample_number'); ?></th>
                                <th class='sourceserifpro-bold'><?php echo _t('requested_by'); ?></th>
                                <th class='sourceserifpro-bold'><?php echo _t('collection_date'); ?></th>
                                <th class='sourceserifpro-bold'><?php echo _t('received_date'); ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                        echo $sample->sample_name;
                                        echo !empty($patient_sample_details[$sample->department_sample_id]['sample_description']) ? ' - '.$patient_sample_details[$sample->department_sample_id]['sample_description'] : '';
                                    ?>
                                </td>
                                <td><?php echo $patient_sample['sample_number']; ?></td>
                                <td><?php echo $patient_sample['requester_name']; ?></td>
                                <td>
                                    <?php
									$collection_date = DateTime::createFromFormat('Y-m-d', $patient_sample['collected_date']);
									$collection_time = DateTime::createFromFormat('H:i:s', $patient_sample['collected_time']);
                                    echo $collection_date ? $collection_date->format('d-M-Y') : 'N/A';
									echo $collection_time ? '&nbsp;'.$collection_time->format('H:i') : '';
                                    ?>
                                </td>
                                <td>
                                <?php
                                    $received_date = DateTime::createFromFormat('Y-m-d', $patient_sample['received_date']);
                                    $received_time = DateTime::createFromFormat('H:i:s', $patient_sample['received_time']);
                                    echo $received_date ? $received_date->format('d-M-Y') : 'N/A';
                                    echo $received_time ? '&nbsp;'.$received_time->format('H:i') : '';
                                ?>
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" border="0" class="test-result">
                            <tr>
                                <th class='sourceserifpro-bold'><?php echo _t('test_name'); ?></th>
                                <th class='sourceserifpro-bold' style="width: 6cm;"><?php echo _t('result'); ?></th>
                                <th class='sourceserifpro-bold' style="width: 1.5cm;"><?php echo _t('unit'); ?></th>
                                <th class='sourceserifpro-bold' style="width: 2.5cm;"><?php echo _t('ref_range'); ?></th>
                            </tr>
                            <?php foreach ($sample->tests as $test) {
                                if ($test->is_show == 0) continue;

                                $padding     = (int)$test->level * 15;
                                $indents     = str_repeat('&nbsp;', (int)$test->level == 0 ? 0 : (int)$test->level + 2);
                                $test_result = isset($patient_sample_results[$test->patient_test_id]) ? $patient_sample_results[$test->patient_test_id] : null;

                                //Single and Multiple Result
                                if (is_array($test_result['result']) && count($test_result['result']) > 0 && !$test->is_rejected && !$test->is_heading) {
                                    $i = 1;
                                    $test_result['result'] = array_values($test_result['result']);

                                    foreach ($test_result['result'] as $organismIndex => $resultItem) {
                                        //Show Test info for first result only
                                        $testName  = $i == 1 ? "<p class='value-pair'><span class='name'>".$indents.$test->test_name. "</span></p>" : "";
                                        $unit_sign = "";
                                        $ref_range = "";

                                        //Organism
                                        $organism_result  = "<p class='value-pair'>";

                                        $organism_result .= "<span class='name'>".$resultItem["organism_name"]."</span>";
                                        $organism_result .= "<span class='value'>".$resultItem["quantity"]."</span>";
                                        $organism_result .= "</p>";

                                        if (empty($resultItem["quantity"])) {
                                            $organism_result = $resultItem["organism_name"];
                                        }

                                        echo "<tr>";
                                        echo "<td>$testName</td>";
										
										// bold if result is positive IVR: 28 Jan 2018
										if ((strpos($organism_result,"Positive")!==false) || (strpos($organism_result,"detected")!==false)){
											echo "<td colspan='3'><b class='sourceserifpro-bold'>".$organism_result."</b></td>";
										}else{
											echo "<td colspan='3'>".$organism_result."</td>";
										}
                                        echo "</tr>";

                                        //Antibiotic
                                        if (count($resultItem["antibiotic"]) > 0) {
                                            foreach ($resultItem["antibiotic"] as $antibioticIndex => $antibiotic) {
                                                if (isset($antibiotic["invisible"]) && $antibiotic["invisible"] > 0) continue;

                                                $sensitivity = isset($sensitivity_type[$antibiotic["sensitivity"]]['abbr']) ? $sensitivity_type[$antibiotic["sensitivity"]]['abbr'] : "N/A";
                                                if ($sensitivity == "S") $sensitivity = $sensitivity."*";
                                                echo "<tr>";
                                                echo "<td></td>";
                                                echo "<td colspan='3'>";
                                                echo "<p class='value-pair'>";
                                                echo "<span class='name'>&nbsp;&nbsp;&#8211; ".$antibiotic["antibiotic_name"]."</span>";
                                                echo "<span class='value'>".$sensitivity."</span>";
                                                echo "</p>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        }

                                        $i++;
                                    }
                                }
                                else {
                                    $rowValue       = $test->is_rejected ? "Rejected" : (isset($test_result["result"]) && !is_array($test_result["result"]) ? $test_result["result"] : "Pending");
                                    $rowValue       = str_replace(' ', '&nbsp;', $rowValue);
                                    $newRowValue    = explode('%', $rowValue);
                                    $_rowValue      = count($newRowValue) == 1 ? $newRowValue[0] : preg_replace('/[^\d\.]/i', '', $newRowValue[1]);

                                    if ($test->is_heading) {
                                        echo "<tr><td colspan='4' style='padding-left:" . $padding . "px'><b class='sourceserifpro-bold'>" . $test->test_name . "</b></td></tr>";
                                    }
                                    else {
                                        $attributes     = "style='padding-left:" . $padding . "px;'";
                                        $ref_range      = isset($ref_ranges[$test->sample_test_id]) ? $ref_ranges[$test->sample_test_id] : "";
                                        $ref_range_text = "";
                                        if (is_array($ref_range)) {
                                            $ref_range_text = ($ref_range['range_sign'] != '-' && $ref_range['min_value'] == 0 ? '' : $ref_range['min_value']).' '.$ref_range['range_sign'].' '.$ref_range['max_value'];

                                            if (in_array($ref_range['range_sign'], array("-", "≤")) && !((float)$_rowValue >= $ref_range['min_value']  && (float)$_rowValue <= $ref_range['max_value'])) {
                                                $rowValue = "<b class='sourceserifpro-bold out-of-range'>$rowValue</b>";
                                            }
                                            else if ($ref_range['range_sign'] == "<" && !((float)$_rowValue >= $ref_range['min_value'] && (float)$_rowValue < $ref_range['max_value'])) {
                                                $rowValue = "<b class='sourceserifpro-bold out-of-range'>$rowValue</b>";
                                            }
                                        }
                                        echo "<tr>";
                                        echo "<td style='padding-left: ".$padding."px'><p class='value-pair'>";
                                        echo "<span class='name'>".$test->test_name."</span>";
                                        echo "</p></td>";
                                        echo "<td>".$rowValue."</td>";
                                        echo "<td>".(in_array($test->field_type, [1, 2]) ? '' : $test->unit_sign)."</td>";
                                        echo "<td>".$ref_range_text."</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                            ?>
                            <tr><td colspan="4">&nbsp;</td></tr>
                        </table>
                    </td>
                </tr>
                <!-- Comment -->
                <?php if (!empty($result_comment[$sample->department_sample_id]['result_comment']) || (!empty($patient_sample['reject_comment']) && $total_department_sample == $department_sample_count ) ) { ?>
                    <tr>
                        <td colspan="4">
                            <table>
                                <tr>
                                    <th class="text-top text-no-wrap">Comment : </th>
                                    <td style="padding-left: 6px">
                                        <?php
                                            $comment_result = !empty($result_comment[$sample->department_sample_id]['result_comment']) ? $result_comment[$sample->department_sample_id]['result_comment'] : "";
                                            $comment_result = preg_replace('/\n|\r\n?/', '<br/>', $comment_result);
                                            echo $comment_result;
                                            if ($total_department_sample == $department_sample_count) {
                                                $reject_comment = preg_replace('/\n|\r\n?/', '<br/>', $patient_sample['reject_comment']);
                                                echo !empty($comment_result) ? "<br>" : "";
                                                echo $reject_comment;
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                <?php } ?>
            </tbody>
            <?php }} ?>

            <!-- Note -->
            <?php if (isset($sensitivity_type) && count($sensitivity_type) > 0) { ?>
                <tbody>
                <tr>
                    <td colspan="4">
                        <table>
                            <tr>
                                <th class="text-top">Note : </th>
                                <td>
                                    <?php
                                    $note = array();
                                    foreach($sensitivity_type as $typ) {
                                        $note[] = "<b>".$typ["abbr"]."</b> = ".$typ["full"];
                                    }
                                    echo implode(', ', $note);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td colspan="4">&nbsp;</td></tr>
                </tbody>
            <?php } ?>
        </table>
        <table id="footer" width="100%" border=0>
            <tr>
                <td class="text-center text-no-wrap" style="width: 7cm">
                    <?php echo _t('last_test_date')." : ";
                        $test_date = DateTime::createFromFormat('Y-m-d', $patient_sample['test_date']);
                        echo $test_date ? $test_date->format('d-M-Y') : 'N/A';
                    ?>
                </td>
                <td style="width: 7cm"></td>
                <td class="text-center text-no-wrap" style="width: 7cm">
                    <?php echo _t('report_date')." : ";
                        $printedDate = DateTime::createFromFormat('Y-m-d H:i:s', $patient_sample['printedDate']);
                        echo $printedDate ? $printedDate->format('d-M-Y H:i') : date('d-M-Y H:i');
                    ?>
                </td>
            </tr>
            <tr class="result-footer-signature">
                <td class="text-center text-no-wrap"><?php echo _t('labmanager'); ?></td>
                <td class="text-center text-no-wrap"><?php echo (isset($laboratory_variables['middle-result-footer']['value']) && $laboratory_variables['middle-result-footer']['status'] == 1 ? $laboratory_variables['middle-result-footer']['value'] : ''); ?></td>
                <td class="text-center text-no-wrap"><?php echo _t('labtech'); ?></td>
				
			</tr>
            <tr class="laboratory-address">
                <td colspan="3" class="text-center">
                    <?php echo $app_lang == "kh" ? $patient_sample_laboratory->get('address_kh') : $patient_sample_laboratory->get('address_en'); ?>
                </td>
            </tr>
        </table>
    </page>
    <?php if (isset($action) && $action == 'print') { ?>
        <script>
            window.print();
        </script>
    <?php } ?>
</body>
</html>

