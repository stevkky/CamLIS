<?php
    $laboratory_logo_url = site_url('assets/camlis/images/moh_logo.png');
    if (isset($patient_sample_laboratory) && !empty($patient_sample_laboratory->get('photo'))  && file_exists('./assets/camlis/images/laboratory/'.$patient_sample_laboratory->get('photo'))) {
        $laboratory_logo_url = site_url('assets/camlis/images/laboratory/'.$patient_sample_laboratory->get('photo'));
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laboratory Request Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=.5, maximum-scale=12.0, minimum-scale=.25, user-scalable=yes"/>
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/patient_sample_result.css?_='.time()) ?>">
</head>
<style>
page[size="A4"]{
    font-size: 10pt !important;
    line-height: 16px !important;
}
#tblRequest , .tbl{
    border-left: 1px solid #000;
    border-top: 1px solid #000;
    border-spacing: 0px !important;
}
table#tblRequest th , table#tblRequest td, .tbl{
    border-right: 1px solid #000;
    border-bottom: 1px solid #000;
    margin: 0;
    padding: 3px;
    border-spacing: 0px !important;
    border-collapse: separate !important;
}
.form-check{ display: inline-block; padding-left: 5px;}
.center-hv 
{
    text-align: center; 
    vertical-align: middle;
}
#tblRequest p {
    margin: 0 0 0px;
}
input[type=checkbox]{
 vertical-align: top;
}
label{
    font-weight: 100 !important;
}
input[type="checkbox"][readonly] {
  pointer-events: none;
}
.underline_dot{
    border-bottom: dotted 1px;
    padding: 0 10px;
}
.smaller, smaller {
    font-size: 75%;
}
table.noneTbl, table.noneTbl > tbody > tr > td{
    border: 0px !important;
    padding: 0px !important; 
    margin: 0px !important;
}
table.noneTbl > tbody > tr > td > div.info{
    border-bottom: dotted 1px;
    text-align: center;
}
.numberCircle {
    border-radius: 50%;
    width: 28px;
    height: 28px;
    padding: 7px;    
    color: #000;
    text-align: center;
    display: inline-block;
    border: 1px solid #000
}
table#tbl_vaccine tbody td, table#tbl_vaccine tbody th,  table#tbl_vaccine tbody{
    padding: 0px;
    margin:0px;
    font-size: 11px;
    vertical-align: top;
    border: none;
    border-left: 1px solid #000;
}
table#tbl_vaccine tbody td:first-child{
    border: none;
}
table#tbl_vaccine tbody td:nth-child(2){
    padding-left: 5px;
}
</style>
<body>
    <page size="A4">    
        <table border="0" width="100%">
            <thead>
                <tr>
                    <th style="width:100%">
                        <table width="100%" border="0" style="margin-bottom: 5px;">
                            <tr>
                                <td style="width: 110px;">
                                    <img src="<?php echo $laboratory_logo_url; ?>" alt="Logo" style="width: 54px;">
                                </td>
                                <td class="text-top text-center">
                                    <div class="KhmerMoulLight" style="font-size: 14.5pt; font-weight: bold;">ទំរង់ស្នើសុំធ្វើពិសោធន៍</div>
                                    <div class="KhmerMoulLight" style="font-size: 18.5pt; font-weight: bold; margin-top:15px;">Laboratory Request Form</div>
                                </td>
                                <td style="width: 110px;"></td>
                            </tr>
                        </table>
                    </th>
                </tr>
                <!-- Print request form-->
                <tr>
                    <td>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" id="tblRequest" >
    <tbody>
        <tr>
            <td width="162" valign="top">
                <p>
                    គ្រឹះស្ថានសុខាភិបាល:
                    <br/>
                    <small><i>Health Facility</i></small> 
                </p>
            </td>
            <td width="354" colspan="3" valign="top" class="center-hv">
            <?php 
                if(!empty($patient_sample['health_facility'])){
                    echo $patient_sample['health_facility'];
                }else  echo $patient_sample['sample_source_name']; 
               
            ?>
            </td>
            <td width="102" valign="top">
                <p>
                    កាលបរិច្ឆេទ
                    <br/>
                    <small><i>Date/日期</i></small>
                </p>
            </td>
            <td width="138" colspan="2" valign="top" class="center-hv">
                <?php 
                    $received_date = DateTime::createFromFormat('Y-m-d', $patient_sample['received_date']);
                    echo $received_date ? $received_date->format('d-M-Y') : 'N/A';
                ?>               
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    ឈ្មោះអ្នកបំពេញទំរង់:
                    <br/>
                    <small><i>Completed by :</i></small>
                </p>
            </td>
            <td width="354" colspan="3" valign="top" class="center-hv">
                <?php echo $patient_sample["completed_by"];?>
            </td>
            <td width="102" valign="top">
                <p>
                    លេខទូរស័ព្ទ
                    <br/>
                    <small><i>Telephone / 電話</i></small>
                </p>
            </td>
            <td width="138" colspan="2" valign="top" class="center-hv">
                <?php echo $patient_sample["phone_number"];?>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    បំណងនៃការធ្វើតេស្ត
                    <br/>
                    <small><i>Reasons for testing<br />測試目的</i></small>
                </p>
            </td>
            <td width="594" colspan="6" valign="top" class="smaller">
                <p>
                    <?php 
                        $FOR_RESEARCH_ARR_CH = array(
                            '0' => '选择',
                            '1' => '懷疑',
                            '2' => '肺炎',
                            '3' => '医护人员',
                            '4' => '劳工',
                            '5' => '複查',                            
                            '6' => '其他'
                        );
                        $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
                        $FOR_RESEARCH_ARR_KH = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
                        $i = 1;
                        for($i = 1 ; $i < count($FOR_RESEARCH_ARR_KH); $i++){
                            if($patient_sample["for_research"] == $i) $checked = "checked";
                            else $checked = "";
                    ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" readonly <?php echo $checked;?> >
                            <?php $ch_lang = empty($FOR_RESEARCH_ARR_CH[$i]) ? "" : $FOR_RESEARCH_ARR_CH[$i]; ?>
                            <label class="form-check-label"><?php echo $FOR_RESEARCH_ARR_KH[$i]." <br/><small><i>".$FOR_RESEARCH_ARR[$i]." / ".$ch_lang."</i></small>";?></label>
                        </div>
                    <?php
                        }
                    ?>
                </p>
                    <?php 
                    $checked = "";
                    if(!empty($patient_info["is_contacted"])){
                        if($patient_info["is_contacted"]){
                            $checked = "checked";
                        }
                    }
                    ?>
                    <table width="100%" style="border: 0px;" >
                        <tr>
                            <td width="200" style="border: 0px;">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="" readonly <?php echo $checked;?> >
                                <label class="form-check-label">ប៉ះពាល់ជាមួយអ្នកជំងឺកូវីដ១៩ <br /><small><i>Contact with COVID cases</i></small><br /><small><i>新型冠状病毒密切接触者</i></small></label>
                            </div>
                            </td>
                            <td valign="top" style="border: 0px;">
                                <div style="float: left; width:40px;">ឈ្មោះ</div> 
                                <div class="underline_dot" style="margin-left:40px; margin-bottom:5px;">
                                <span>
                                <?php
                                    if(!empty($patient_info["contact_with"])){
                                        echo $t = ($patient_info["contact_with"] == "") ? "" : $patient_info["contact_with"]; 
                                    } 
                                ?>
                                &nbsp;</span>
                                </div>

                                <div class="form-check form-check-inline">                        
                                    <label class="form-check-label">ប្រភេទការប៉ះពាល់<br/><small><i>Type of contact</i></small> :</label>
                                </div>
                                <?php 
                                if(!empty($patient_info["is_direct_contact"])){
                                    if($patient_info["is_direct_contact"] == "true"){ ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" readonly checked >
                                            <label class="form-check-label">ផ្ទាល់<br/><small><i>Direct</i></small></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" readonly >
                                            <label class="form-check-label">មិនផ្ទាល់<br/><small><i>Indirect</i></small></label>
                                        </div>
                                    <?php } else if($patient_info["is_direct_contact"] == "false"){ ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" readonly  >
                                            <label class="form-check-label">ផ្ទាល់<br/><small><i>Direct</i></small></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" readonly checked>
                                            <label class="form-check-label">មិនផ្ទាល់<br/><small><i>Indirect</i></small></label>
                                        </div>
                                    <?php } else { ?> 
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" readonly  >
                                            <label class="form-check-label">ផ្ទាល់<br/><small><i>Direct</i></small></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" readonly>
                                            <label class="form-check-label">មិនផ្ទាល់<br/><small><i>Indirect</i></small></label>
                                        </div>
                                    <?php }?>
                                <?php
                                }else{ ?> 
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" readonly  >
                                        <label class="form-check-label">ផ្ទាល់<br/><small><i>Direct</i></small></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" readonly>
                                        <label class="form-check-label">មិនផ្ទាល់<br/><small><i>Indirect</i></small></label>
                                    </div>
                                <?php } ?>                                                                
                            </td>
                        </tr>
                    </table>
            </td>
        </tr>
        <tr>
            <td width="756" colspan="7" valign="top">
                <p>
                    <em><u>ព័ត៌មានអ្នកជំងឺ</u></em>
                    <em><i> / Patient information / 患者信息</i></em>                    
                    <em><u></u></em>
                </p>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    ឈ្មោះអ្នកជំងឺ <br/>
                    <small><i>Patient Name / 患者姓名</i></small>
                </p>
            </td>
            <td width="354" colspan="3" valign="top" class="center-hv">
            <?php echo $patient_info['name']; ?>
            </td>
            <td width="102" valign="top">
                <p>
                    លេខសំគាល់                   
                    <small><i>Patient ID</i></small>
                </p>
            </td>
            <td width="138" colspan="2" valign="top" class="center-hv">
            <?php echo $patient_info['patient_code']; ?>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    ភេទ <br />
                    <small><i>Sex / 性别</i></small>
                </p>               
            </td>
            <td width="354" colspan="3" valign="top" class="center-hv">
                <p>
                <?php 
                    if(trim($patient_info['sex']) == 'M' || trim($patient_info['sex']) == '1'){
                ?>
                    <div class="form-check form-check-inline" data-gender= "<?php echo $patient_info['sex'];?>">
                        <input class="form-check-input" type="checkbox" value="" readonly checked >
                        <label class="form-check-label">ប្រុស​ <br/> <small><i>Male / 男</i></small> </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="" readonly >
                        <label class="form-check-label">ស្រី <br/><small><i>Female / 女</i></small></label>
                    </div>
                <?php
                    }else{
                ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="" readonly >
                        <label class="form-check-label">ប្រុស​ <br/><small><i>Male / 男</i></small></label>
                    </div>
                    <div class="form-check form-check-inline" data-gender= "<?php echo $patient_info['sex'];?>">
                        <input class="form-check-input" type="checkbox" value="" readonly checked >
                        <label class="form-check-label">ស្រី <br/><small><i>Female / 女</i></small></label>
                    </div>
                <?php
                    }
                ?>
                </p>
            </td>
            <td width="102" valign="top">
                <p>
                    អាយុ <br />
                    <small><i>Age /年齡</i></small>                   
                </p>
            </td>
            <td width="138" colspan="2" valign="top" class="center-hv" >
            
            <?php
                //$age = calculateAge($patient_info['dob'], $patient_sample['collected_date']);
                $age = calculateAge($patient_info['dob']);
            ?>
            <span class="age-year"></span> <?php echo $age->y.' '._t('global.year') ?> &nbsp;
            <span class="age-month"></span> <?php echo $age->m.' '._t('global.month') ?> &nbsp;
            <span class="age-day"></span> <?php echo ($age->days > 0 ? $age->d : 1).' '._t('global.day') ?>
            
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    សញ្ញាតិ <br />
                    <small><i>Nationality /国籍</i></small>
                </p>
            </td>
            <td width="120" valign="top" class="center-hv">
                <?php if(!empty($patient_info["nationality_en"])) echo $patient_info["nationality_en"];?>
            </td>
            <td width="100" valign="top">
                <p>មុខរបរ<br />
                    <small><i>Occupation/职业</i></small>
                </p>
            </td>
            <td width="120" valign="top" class="center-hv">
            <?php if(!empty($patient_info["occupation"])) echo $patient_info["occupation"];?>            
            </td>
            <td width="102" valign="top">
                <p>
                    លេខទូរស័ព្ទ
                    <small><i>Telephone / 電話</i></small>                
                </p>
            </td>
            <td width="138" colspan="2" valign="top" class="center-hv">
                <?php echo $patient_info["phone"];?>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    អាស័យដ្ឋានបច្ចុប្បន្ន <br />
                    <small><i>Address / 地址</i></small>
                </p>
            </td>
            <td width="594" colspan="6" valign="top">
                <?php                    
                    $village  = !empty($patient_info['village_'.$app_lang]) ? $patient_info['village_'.$app_lang] : null;
                    $commune  = !empty($patient_info['commune_'.$app_lang]) ? $patient_info['commune_'.$app_lang] : null;
                    $district = !empty($patient_info['district_'.$app_lang]) ? $patient_info['district_'.$app_lang] : null;
                    $province = !empty($patient_info['province_'.$app_lang]) ? $patient_info['province_'.$app_lang] : null;
                ?>
                <table width="100%" class="noneTbl">
                    <tr>
                        <td colspan="4">
                        <table class="noneTbl" width="100%">
                            <tr>
                                <td width="200">កន្លែងស្នាក់នៅ <small><i> / Residence / 住所</i></small></td>
                                <td><div class="info"><span><?php if(!empty($patient_info["residence"])) echo $patient_info["residence"];?>&nbsp;</span></div></td>
                            </tr>
                        </table>
                        </td>                        
                    </tr>
                    <tr>
                        <td width="25%">
                            <div>ភូមិ <small><i>/ Village / 乡村</i></small></div>
                            <div class="info"><?php if ($village) { echo $village; }?></div>
                        </td>
                        <td width="25%">
                            <div>ឃុំ <small><i>/ Commune / 公社</i></small></div>
                            <div class="info"><?php if ($commune) { echo $commune; } ?></div>
                        </td>
                        <td width="25%">
                            <div >ស្រុក <small><i>/ District / 区</i></small></div>
                            <div class="info"><?php if ($district) { echo $district; }?></div>
                        </td>
                        <td width="25%">
                            <div>ខេត្ត <small><i>/ Province / 省</i></small></div>
                            <div class="info"><?php if ($province) { echo $province; } ?></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    រោគសញ្ញាគ្លីនិក
                    <br/>
                    <small><i>Clinical Symptoms / 临床症状</i></small>
                </p>
            </td>
            <td width="594" colspan="6" valign="top" style="font-size:12px;" class="center-hv">
                <p>
                    <?php
                    $tbl = array();
                        foreach($clinical_symptoms_dd as $cs){
                            $tbl[] = $cs->clinical_symptom_id;
                        }
                    ?>
                    <?php 
                    $clinical_symptoms_ch = array(
                        '1' => '发烧',
                        '2' => '咳嗽',
                        '3' => '流鼻涕',
                        '4' => '喉咙痛',
                        '5' => '呼吸困难',
                        '6' => '没有症状',
                        '7' => '其他'
                    );
                    $i = 1;
                    foreach($clinical_symptoms as $item){
                        if(in_array($item->ID, $tbl)) $checked = "checked";
                        else $checked = "";
                    ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" readonly <?php echo $checked; ?> >
                            <label class="form-check-label"><?php echo $item->name_kh."<br /><small><i>".$item->name_en." <br /> ".$clinical_symptoms_ch[$i]."</i></small>";?></label>
                        </div>
                    <?php
                    $i++;
                    }
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    ថ្ងៃចេញរោគសញ្ញា
                    <br/>
                    <small><i>Date of onset / 发病日期</i></small>
                </p>
            </td>
            <td width="192" valign="top" class="center-hv">
                <?php
                    if(!empty($patient_sample["admission_date"])) {
                        echo date("d-M-Y", strtotime($patient_sample["admission_date"]));
                    }
                ?>
            </td>
            <td width="162" colspan="2" valign="top" class="small">
                <p>ធ្លាប់កើតជំងឺកូវិដ-១៩ឬទេ?<br/><small><i>History of COVID-19 positive? / 你曾经有过新冠肺炎病毒吗?</i></small></p>
            </td>
            <td width="240" colspan="3" valign="top" class="small">
                <p>
                    <?php 
                        $check_yes = "";
                        $check_no = "";
                        if(!empty($patient_info["is_positive_covid"])){ 
                            if($patient_info["is_positive_covid"] == "true"){
                                $check_yes = "checked";
                            }else{
                                $check_no = "checked";
                            }

                         } ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" <?php echo $check_no;?> readonly>
                            <label class="form-check-label">ទេ / No / 没有</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" <?php echo $check_yes; ?> readonly >
                            <label class="form-check-label">មាន / Yes / 有</label>
                        </div>
                    ថ្ងៃតេស្ត:/Test Date / 检测日期: 
                    <div style="border-bottom: dotted 1px; text-align:center;">
                         <span>
                        <?php
                            if(!empty($patient_info['test_date'])){
                                echo $patient_info['test_date'] != "" ? date("d-M-Y", strtotime($patient_info['test_date'])) : '';
                            }
                         ?>
                         &nbsp;</span>
                    </div>
                </p>
            </td>
        </tr>
        <tr>
            <td width="162" rowspan="3" valign="top">
                <p>ប្រវត្តធ្វើដំណើរ<br/><small><i>Travel history / 旅行历史</i></small></p>
            </td>
            <td width="192" valign="top" class="small">
                <p>ឈ្មោះខេត្ត/ប្រទេស<br/><small><i>Province / Country / 国家名称</i></small></p>
            </td>
            <td width="162" colspan="2" valign="top" class="center-hv">
                <?php
                if(!empty($patient_info["country_name"])){
                    echo $patient_info["country_name"];
                }else{
                    if(!empty( $patient_info["country_name_en"])) echo $patient_info["country_name_en"]; 
                }
                ?>
            </td>
            <td width="144" colspan="2" valign="top" class="small">
                <p>ថ្ងៃមកដល់<br/><small><i>Date of arrival / 到达日期</i></small></p>
            </td>
            <td width="96" valign="top" class="center-hv">
                <?php 
                    if(!empty( $patient_info["date_arrival"])){
                        $date_arrival = DateTime::createFromFormat('Y-m-d', $patient_info['date_arrival']);
                        $arrival_time = DateTime::createFromFormat('H:i:s', $patient_info['date_arrival']);
                        echo $date_arrival ? $date_arrival->format('d-M-Y') : '';
                        echo $arrival_time ? '&nbsp;'.$arrival_time->format('H:i') : '';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td width="192" valign="top" class="small">
                <p>លិខិតឆ្លងដែន/អត្តសញ្ញាណប័ណ្ណ<br><small><i>Passport No / 护照号</i></small></p>
            </td>
            <td width="162" colspan="2" valign="top" class="center-hv">
                <?php if(!empty( $patient_info["passport_number"])) echo $patient_info["passport_number"]; ?>
            </td>
            <td width="144" colspan="2" valign="top" class="small">
                <p>
                    លេខកៅអី <small><i>Seat No / 座号</i></small> :
                    <?php if(!empty( $patient_info["seat_number"])) echo $patient_info["seat_number"]; ?>
                </p>
            </td>
            <td width="96" valign="top" >
                <p>
                    <small>លេខជើងហោះហើរ <i>Flight number</i></small>:
                    <?php if(!empty($patient_info["flight_number"])) echo $patient_info["flight_number"]; ?>
                </p>
            </td>
        </tr>
        <tr>
            <td width="192" valign="top" class="small">
                <p>
                    រៀបរាប់ប្រទេសដែលបានធ្វើដំណើរក្នុងរយៈពេល ១៤ថ្ងៃ
                    មុនមកដល់កម្ពុជា។
                </p>
            </td>
            <td width="402" colspan="5" valign="top" class="center-hv">
                <p>
                <?php if(!empty($patient_info["travel_in_past_30_days"])) echo $patient_info["travel_in_past_30_days"]; ?>
                </p>
            </td>
        </tr>
        <tr>
            <td width="756" colspan="7" valign="top">
                <p>
                    <em><u>ផ្នែកមន្ទីរពិសោធន៍</u></em>
                    <em><u>/</u></em>
                    <em><u>Laboratory</u></em>
                </p>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    ទីកន្លែងប្រមូលវត្ថុវិភាគ
                    <br/>
                    Place of collection
                </p>
            </td>
            <td width="354" colspan="3" valign="top" class="center-hv">
                <?php echo $patient_sample['sample_source_name']; ?>
            </td>
            <td width="144" colspan="2" valign="top">
                <p>
                    ថ្ងៃយកវត្ថុវិភាគ
                    <br/>
                    <em>Date of collection</em>
                </p>
            </td>
            <td width="96" valign="top" class="center-hv">
                <?php 
                    $collection_date = DateTime::createFromFormat('Y-m-d', $patient_sample['collected_date']);
                    $collection_time = DateTime::createFromFormat('H:i:s', $patient_sample['collected_time']);
                    echo $collection_date ? $collection_date->format('d-M-Y') : 'N/A';
                    echo $collection_time ? '&nbsp;'.$collection_time->format('H:i') : '';
                ?>
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    ប្រភេទវត្ថុវិភាគ
                    <br/>
                    <small><i>Type of specimen</i></small>
                </p>
            </td>
            <?php 
                // Check is the patient was test Covid
                $test_covid = "";
                $test_other = "";
                if(count($sample_test) > 0){
                    foreach($sample_test as $test){
                        // 419 = Sars-Cov2 geneXpert
                        if($test["test_id"] == 419 || $test["test_id"] == 438 || $test["test_id"] == 446){
                            $test_covid = "checked";
                            $test_other = "";
                            break;
                        }else{
                            $test_other = "checked";
                        }
                    }
                }
            ?>
            <td width="354" colspan="3" valign="top">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" value="" readonly <?php echo $test_covid;?> >
                    <label class="form-check-label">ច្រមុះ<br /><small><i>Nasopharyngeal</i></small></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" value="" readonly <?php echo $test_covid;?> >
                    <label class="form-check-label">បំពង់ក<br /><small><i>Oropharyngeal</i></small></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" value="" readonly <?php echo $test_other;?>>
                    <label class="form-check-label">ផ្សេងទៀត<br /><small><i>other</i></small></label>
                </div>
            </td>
            <td width="240" colspan="3" valign="top">
                <p>
                    មន្ទីរពិសោធន៍ Laboratory
                    <br/>
                     <?php 
                        if(!empty($patient_sample_laboratory)){
                            echo $patient_sample_laboratory->get('name_'.$app_lang);
                        } 
                      ?>
                </p>
            </td>
        </tr>
        <!-- 13-07-2021-->
        <tr>
            <td width="162" valign="top">
                <p>ការចាក់ថ្នាំបង្ការ<br/>Vaccination Status</p>
            </td>
            <td width="594" colspan="6" style="padding: 0;" valign="top">
                <table class="tbl_vaccine" id="tbl_vaccine">
                    <tr>
                        <td width="140">
                        <?php 
                            $checked_0 = "";
                            $checked_1 = "";
                            $checked_2 = "";
                            $checked_3 = "";
                            if(!empty($patient_info["vaccination_status"])){
                                if($patient_info["vaccination_status"] == 1){
                                    $checked_0 = "checked";
                                }else if($patient_info["vaccination_status"] == 2){
                                    $checked_1 = "checked";
                                }else if($patient_info["vaccination_status"] == 3){
                                    $checked_2 = "checked";
                                }else if($patient_info["vaccination_status"] == 4){
                                    $checked_3 = "checked";
                                }
                            }
                        ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" readonly <?php echo $checked_0; ?> />
                            <label class="form-check-label">មិនបានចាក់<br /><small><i>Not vaccinated</i></small></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" readonly <?php echo $checked_1; ?> />
                            <label class="form-check-label">លើកទី១<br /><small><i>1 dose</i></small></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" readonly <?php echo $checked_2; ?> />
                            <label class="form-check-label">លើកទី២<br /><small><i>2 doses</i></small></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="" readonly <?php echo $checked_3; ?> />
                            <label class="form-check-label">លើកទី៣<br /><small><i>3 doses</i></small></label>
                        </div>
                        </td>
                        <td width="90">
                            <p>ថ្ងៃចាក់លើកទី១<br/> <small><i>1<sup>st</sup> injection date</i>:</small></p>
                            <?php
                                if(!empty($patient_info['first_vaccinated_date'])){
                                    $first_vaccinated_date = DateTime::createFromFormat('Y-m-d', $patient_info['first_vaccinated_date']);
                                    echo $first_vaccinated_date ? $first_vaccinated_date->format('d-M-Y') : 'N/A';
                                }
                            ?>
                        </td>
                        <td width="90">
                            <p>ថ្ងៃចាក់លើកទី២<br/> <small><i>2<sup>nd</sup> injection date</i>:</small></p>
                            <?php
                                if(!empty($patient_info['second_vaccinated_date'])){
                                    $second_vaccinated_date = DateTime::createFromFormat('Y-m-d', $patient_info['second_vaccinated_date']);
                                    echo $second_vaccinated_date ? $second_vaccinated_date->format('d-M-Y') : 'N/A';
                                }
                            ?>
                        </td>
                        <td width="90">
                        <p>ថ្ងៃចាក់លើកទី៣<br/> <small><i>3<sup>rd</sup> injection date</i>:</small></p>
                            <?php
                                if(!empty($patient_info['third_vaccinated_date'])){
                                    $third_vaccinated_date = DateTime::createFromFormat('Y-m-d', $patient_info['third_vaccinated_date']);
                                    echo $third_vaccinated_date ? $third_vaccinated_date->format('d-M-Y') : 'N/A';
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                $vaccine_check = '';
                                $vaccine_id = empty($patient_info['vaccine_id']) ? "" : $patient_info['vaccine_id'];
                                $second_vaccine_id = empty($patient_info['second_vaccine_id']) ? "" : $patient_info['second_vaccine_id'];
                                foreach($vaccines as $item){
                                    if($item->id == $vaccine_id || $item->id == $second_vaccine_id){
                                        $vaccine_check = 'checked';
                                    }else $vaccine_check = '';
                            ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="" readonly <?php echo $vaccine_check; ?> />
                                    <label class="form-check-label"><?php echo $item->name?></label>
                                </div>
                            <?php
                                }
                            ?> 
                        </td>                                               
                    </tr>
                </table>
            </td>
            
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    វត្ថុវិភាគលើកទី(គូសរង្វង់)
                    <br/>
                    <small><i>N<sup>o</sup> of Sample (circle)</i></small>
                </p>
            </td>
            <td width="594" colspan="6" valign="top" class="center-hv">
                <?php 
                    if(isset($patient_sample['number_of_sample']) || $patient_sample['number_of_sample'] > 0){
                        echo '<div class="numberCircle">'.$patient_sample['number_of_sample'].'</div>';
                    }else{
                        echo '<div class="numberCircle">'.$number_of_sample["number_sample"].'</div>';
                    }
                    /*
                    $max = $number_of_sample["number_sample"] > 15 ? $number_of_sample["number_sample"] : 15;
                    for($i = 1 ; $i <= $max ; $i++){
                        if($number_of_sample["number_sample"] == $i) {
                            echo '<div class="numberCircle">'.$i.'</div>'."&nbsp;&nbsp;&nbsp;";
                        }else{ echo $i."&nbsp;&nbsp;&nbsp;";}
                    }
                    */
                ?>                    
            </td>
        </tr>
        <tr>
            <td width="162" valign="top">
                <p>
                    ឈ្មោះអ្នកប្រមូល
                    <br/>
                    <small><i> Sample collector</i></small>
                </p>
            </td>
            <td width="240" colspan="2" valign="top" class="center-hv">
                <?php echo $patient_sample["sample_collector"];?>
            </td>
            <td width="114" valign="top">
                <p>
                    លេខទូរស័ព្ទ
                    <br/>
                    <small><i>Telephone</i></small>
                </p>
            </td>
            <td width="240" colspan="3" valign="top" class="center-hv">
                <?php echo $patient_sample["phone_number_sample_collector"];?>
            </td>
        </tr>
    </tbody>
</table>
                    </td>
                </tr>
                <!-- End Form-->
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
        </table>
        <table id="footer" width="100%" border=0>
            <tr>
                <td class="text-no-wrap" style="width: 7cm">
                ហត្ថលេខា / Signature: ...............................
                </td>
                
        </table>
    </page>
    <?php if (isset($action) && $action == 'print') { ?>
        <script>
            window.print();
        </script>
    <?php } ?>
</body>
</html>

