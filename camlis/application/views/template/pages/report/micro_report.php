<script>
    var msg_required_data = "<?php echo _t('global.msg.fill_required_data'); ?>";
    var std_sample  = <?php echo json_encode($samples); ?>;
</script>
<style>
    body {
        position: relative;
    }
    .affix {
        top: 20px;
        z-index: 9999 !important;
    }   

    ol.toc , ol.header{
        counter-reset: section;                /* Creates a new instance of the
                                                    section counter with each ol
                                                    element */
        list-style-type: none;
        list-style-position: outside;
    }
    ol.toc>li:before , ol.header>li:before {
        counter-increment: section;            /* Increments only this instance
                                                    of the section counter */
        content: counters(section, ".") " ";   /* Adds the value of all instances
                                                of the section counter separated
                                                by a ".". */
    }
    ol.toc>li>a{
        display:inline-block !important;
        padding: 3px 5px !important;
    }
    ol.header>li>h4,ol.header>li>h5{
        display:inline-block !important;
        padding: 3px 5px !important;
    }
    table#antibiotic_bps_tbl , table.antibiotic_tbl{
        width: 100% !important;
    }
    table.antibiotic_tbl tbody td:nth-child(2),
    table#antibiotic_bps_tbl tbody td:nth-child(2){
        text-align: center;
        width: 5em;
        font-weight: bold;
        color: #6698FF !important;
    }
    table.antibiotic_tbl tbody td:nth-child(4),
    table#antibiotic_bps_tbl tbody td:nth-child(4){
        text-align: center;
        width: 5em;
        font-weight: bold;
        color: #F88017 !important;
    }
    table#antibiotic_bps_tbl tbody td:nth-child(3),
    table#antibiotic_bps_tbl tbody td:nth-child(5),
    table.antibiotic_tbl tbody td:nth-child(3),
    table.antibiotic_tbl tbody td:nth-child(5){
        text-align: center;
    }
    table#antibiotic_bps_tbl thead th:nth-child(1),
    table.antibiotic_tbl thead th:nth-child(1){
        width: 50% !important;
    }
    table#antibiotic_bps_tbl thead th:nth-child(2),
    table#antibiotic_bps_tbl thead th:nth-child(3),
    table#antibiotic_bps_tbl thead th:nth-child(4),
    table#antibiotic_bps_tbl thead th:nth-child(5),
    table.antibiotic_tbl thead th:nth-child(2),
    table.antibiotic_tbl thead th:nth-child(3),
    table.antibiotic_tbl thead th:nth-child(4),
    table.antibiotic_tbl thead th:nth-child(5){
        text-align: center;
        width: 50px !important;
        font-weight: bold;        
    }
    table.antibiotic_tbl thead{
        background: #FFA500;
    }
</style>
<?php 
    $name = 'name_'.strtolower($app_lang);     
    $TITLES  = [];
    $ANTIBIOTIC_TABLE_BODY = '<thead> <tr> <th>Antibiotic</th> <th>S</th> <th>I</th> <th>R</th> <th>Total</th> </tr></thead> <tbody></tbody> <tfoot> <tr> <td colspan="5">S: susceptible, I: intermediate, R: resistant</td></tr></tfoot>';
    $TITLES[]=['id' =>'1' , 'title' => 'Distribution by Gender', 'abbre' => null, 'child' => null , 
               'data' => [
                   ['type' => 'graph', 'id' => 'gender_chart']
               ]
            ];
    $TITLES[]=['id' =>'2' , 'title' => 'Distribution by Age Group', 'abbre' => null, 'child' => null,
                'data' => [
                    ['type' => 'graph', 'id' => 'age_group_chart' , 'height' => 600]
                ]
            ];
    $TITLES[]=['id' =>'3' , 'title' => 'Microbiology Specimen', 'abbre' => null, 'child' => null, 
                'data' => null
            ];
    $TITLES[]=['id' =>'4' , 'title' => 'Isolated Pathogens among Blood and CSF', 'abbre' => null, 
                'child' => [
                    ['subtitle' => 'Blood Culture', 'type' => 'graph' , 'id' => 'blood_culture_pathogent_chart'],
                    ['subtitle' => 'Cerebrospinal fluid (CSF)', 'type' => 'graph' , 'id' => 'csf_pathogent_chart'],
                ],
                'data' => null
            ];
    $TITLES[]=['id' =>'5' , 'title' => 'Bloodstream pathogens isolated', 'abbre' => null, 'child' => null,
                'data' => [
                    ['type' => 'graph', 'id' => 'bloodstream_pathogens_chart_adult'],
                    ['type' => 'graph', 'id' => 'bloodstream_pathogens_chart_pediatric'],
                    ['type' => 'table', 'id' => 'bloodstream_pathogens_tbl' , 'content' => '']
                ]
            ];
    $TITLES[]=['id' =>'6' , 'title' => 'Blood culture true pathogen rate and contamination rate by ward', 'abbre' => null, 'child' => null,
                'data' => [
                    ['type' => 'table', 'id' => 'true_pathogen_tbl' , 'content' => '<thead> <th>Wards</th> <th>Patient request</th> <th>True pathogens (%)</th> <th>Number of bottle</th> <th>Contamination (%)</th> </thead>'],
                ]
            ];
    $TITLES[]=['id' =>'7' , 'title' => 'Blood culture volume', 'abbre' => null, 
                'child' => [
                    ['subtitle' => 'Pediatric (age 0 - 28d)' , 'type' => 'graph', 'id' => 'blood_culture_volume_for_pediatric_28d'],
                    ['subtitle' => 'Pediatric (age 29d - < 1y)' , 'type' => 'graph', 'id' => 'blood_culture_volume_for_pediatric_29d1y'],
                    ['subtitle' => 'Pediatric (age 1y - 14y)' , 'type' => 'graph', 'id' => 'blood_culture_volume_for_pediatric_1y14y'],
                    ['subtitle' => 'Adult (age>14 years)' , 'type' => 'graph', 'id' => 'blood_culture_volume_adult'],
                    ['subtitle' => 'Total' , 'type' => 'graph', 'id' => 'blood_culture_volume_for_total'],
                ],
                'data' => null
            ];
    $TITLES[]=['id' =>'8' , 'title' => 'Notifiable and other important pathogens list', 'abbre' => null, 'child' => null,
                'data' => [
                    ['type' => 'table', 'id' => 'pathogens_list_tbl' , 'content'=>''],
                ]
            ];
    $TITLES[]=['id' =>'9' , 'title' => 'Cumulative Antimicrobial Susceptibility Testing - Blood Culture', 'abbre' => 'CAST - Blood Culture', 
               'child' => [
                    ['subtitle' => '<i>Burkholderia pseudomallei (Bps)</i>', 'type' => 'graph', 'id' => 'bps_chart'],
                    ['subtitle' => '<i>Salmonella cases</i>', 'type' => 'graph', 'id' => 'salmonella_chart'],
                    ['subtitle' => '<i>Escherichia coli</i>', 'type' => 'table', 'id' => 'escherichia_coli_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],
                    ['subtitle' => '<i>Acinetobacter Sp.</i>', 'type' => 'table', 'id' => 'acinetobacter_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],
                    ['subtitle' => '<i>Klebsiella pneumoniae</i>', 'type' => 'table', 'id' => 'Klebsiella_pneumoniae_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],
                    ['subtitle' => '<i>Staphylococcus aureus</i>', 'type' => 'table', 'id' => 'staphylococcus_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],
                    ['subtitle' => '<i>Streptococcus pneumoniae</i>', 'type' => 'table', 'id' => 'streptococcus_pneumoniae_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],
               ],
               'data' => null
            ];
    $TITLES[]=['id' =>'10' , 'title' => 'Cumulative Antimicrobial Susceptibility Testing - CSF', 'abbre' => 'CAST - CSF', 
               'child' => [
                    ['subtitle' => '<i>Streptococcus suis</i>', 'type' => 'table', 'id' => 'streptococcus_suis_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],
                    ['subtitle' => '<i>Streptococcus pneumoniae</i>', 'type' => 'table', 'id' => 'streptococcus_pneumoniae_organism_csf_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],
                    ['subtitle' => '<i>Streptococcus, beta-haem. Group B</i>', 'type' => 'table', 'id' => 'streptococcus_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY], // Streptococcus Group B
                    ['subtitle' => '<i>Neisseria meningitidis</i>', 'type' => 'table', 'id' => 'neisseria_meningitidis_organism_tbl' , 'class'=> 'antibiotic_tbl', 'content'=>$ANTIBIOTIC_TABLE_BODY],                   
               ],
               'data' => null
            ];
    /*
    $TITLES[]=['id' =>'11' , 'title' => 'TAT', 'abbre' => null, 'child' => null,
                'data' => null
            ];
    */
    $TITLES[]=['id' =>'12' , 'title' => 'Rejection', 'abbre' => null, 'child' => null, 
                'data' => [
                    ['type' => 'table', 'id' => 'rejection_tbl' , 'content'=>''],
                ]
            ];
?>
<div class="row">
    <div class="col-sm-12">
    <div class="form-vertical border-box">
        <form id="audit_user" role="form">
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label"><?php echo _t('global.laboratory'); ?></label>
                    <select name="laboratories[]" id="laboratories" class="form-control" multiple>
                        <?php foreach ($laboratories as $laboratory): ?>
                            <option value="<?php echo $laboratory->labID; ?>"><?php echo $laboratory->$name; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="control-label"><?php echo _t('report.start_receive_date'); ?> *</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="start-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control narrow-padding" id="start-time" size="10" value="00:00">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label class="control-label"><?php echo _t('report.end_receive_date'); ?> *</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="end-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control narrow-padding" id="end-time" size="10" value="23:59">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label class="control-label">&nbsp;</label>
                    <div class="input-group">
                        <button class="btn btn-primary" id="btnGenerate"><i class="fa fa-search"></i> <?php echo _t('report.filter'); ?></button> &nbsp;
                        <button class="btn btn-success" id="btnSave"><i class="fa fa-save"></i> <?php echo _t('report.save_as_word'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>


<div class="row" id="result_page" style="display:none;">
    <nav class="col-sm-3" id="myScrollspy">
      <ol class="nav nav-pills nav-stacked toc" data-spy="affix" data-offset-top="205">
        <?php 
            foreach($TITLES as $title ){                
                $id_title = str_replace(' ','_',strtolower($title['title']));
                $ttl = ($title['abbre'] == null) ? $title['title'] : $title['abbre'];
                if ($title['child'] == null){
                    echo '<li><a href="#'.$id_title.'">'.$ttl.'</a></li>';
                } else{
                    $children = $title['child'];
                    echo '<li class="dropdown">';
                    echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#'.$id_title.'">'.$ttl.'<span class="caret"></span></a>';
                    echo '<ol class="dropdown-menu toc">';
                    foreach($children as $child){
                        $id_child = str_replace(' ','_',strtolower($child['subtitle']));
                        echo '<li><a href="#'.$id_child.'">'.$child['subtitle'].'</a></li>';
                    }                    
                    echo '</ol>';
                    echo '</li>';
                }        
            }
        ?>
      </ol>
    </nav>
    <div class="col-sm-9">
        <div class="text-center">
            <h3><span class="lab_name"></span></h3>
            <h5>Microbiology Laboratory Report</h5>
            <h5><span class="date_frame"></span></h5>            
        </div>
        <ol class="header">
            <?php 
                foreach($TITLES as $title ){
                    $id_title = str_replace(' ','_',strtolower($title['title']));
                    $ttl = $title['title'];
                    $data = $title['data'];
                    if ($title['child'] == null){
                        echo '<li><h4 id="'.$id_title.'">'.$ttl.'</h4>';
                        if($data !== null){
                            foreach($data as $item){
                                if($item['type'] == 'graph'){
                                    $height = !empty($item['height']) ? $item['height'] : '350';
                                    echo '<div id="'.$item['id'].'" style="width: 75%; height: '.$height.'px;"></div>';
                                }else if($item['type'] == 'table'){

                                    if($title["id"] == 12){
                                        // apply overflow 
                                        $tbl_content = !empty($item['content']) ? $item['content']:'';
                                        $class = !empty($item['class']) ? $item['class']:'';
                                        echo '<div style="overflow: auto;">';
                                        echo '<table class="table table-hover '.$class.' " id="'.$item['id'].'">'.$tbl_content.'</table>';
                                        echo '</div>';
                                    }else{
                                        $tbl_content = !empty($item['content']) ? $item['content']:'';
                                        $class = !empty($item['class']) ? $item['class']:'';
                                        echo '<table class="table table-hover '.$class.' " id="'.$item['id'].'">'.$tbl_content.'</table>';
                                    }
                                    
                                }
                            }
                        }else{
                            // Special case for Micrology specimen
                            if($title['id'] == 3){
                        ?>
                                <div class="row">
                                    <?php
                                        foreach($samples as $sample){
                                            $id_chart = str_replace(" ","_",$sample["sample_name"])."_chart";
                                            $sample_name = $sample["sample_name"];
                                            if (!in_array($sample["ID"], array(9,17))) {
                                                echo "<div class='col-sm-6 ".$sample["ID"]."' id=".$id_chart."_parent"."> ";
                                                echo "<p>".$sample_name."</p>";
                                                echo "<div id=".$id_chart." style='width: 100%; height: 350px;'></div>";
                                                echo "</div>";
                                            }
                                        }
                                        // For Pus swap and pus aspirate
                                        echo "<div class='col-sm-6' id='pus_parent'> ";
                                        echo "<p>Pus</p>";
                                        echo "<div id='pus_chart' style='width: 100%; height: 350px;'></div>";
                                        echo "</div>";
                                    ?>
                                </div>
                                <div class="row" id="specimen_by_month">
                                </div>
                        <?php
                            }
                        }
                        echo '</li>';
                    } else{
                        $children = $title['child'];
                        echo '<li><h4>'.$ttl.'</h4>';
                        echo '<ol class="header">';
                        foreach($children as $child){
                            $id_child = str_replace(' ','_',strtolower($child['subtitle']));
                            $subtitle = $child['subtitle'];
                            echo '<li><h5 id="'.$id_child.'">'.$subtitle.'</h5>';
                            if($child['type'] == 'graph'){
                                echo '<div id="'.$child['id'].'" style="width: 75%; height: 350px;"></div>';
                            }else if($child['type'] == 'table'){
                                $tbl_content = !empty($child['content']) ? $child['content']:'';  
                                $class = !empty($child['class']) ? $child['class']:'';                              
                                echo '<table class="table table-hover '.$class.'" id="'.$child['id'].'">'.$tbl_content.'</table>';
                            }
                            // Special case 
                            //Burkholderia pseudomallei has antibiotic table, so we need to customize it
                            if($child['id'] == 'bps_chart'){
                                echo '<table class="table table-hover" id="antibiotic_bps_tbl">
                                <thead>
                                    <tr>
                                        <th style="border-bottom:hidden;padding-bottom:0; padding-left:3px;padding-right:3px;text-align: center; font-weight: bold; padding-right: 4px; padding-left: 4px; background-color: #FFA500 !important;" colspan="5">
                                            <div style="border-bottom: 1px solid #ddd; padding-bottom: 5px; ">
                                                Antibiotic Susceptibility Patterns
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Antibiotic</th>
                                        <th width="50">S</th>
                                        <th width="50">I</th>
                                        <th width="50">R</th>
                                        <th width="50">Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">S: susceptible, I: intermediate, R: resistant</td>
                                    </tr>
                                </tfoot>
                            </table>';
                            }
                            echo '</li>';
                        }
                        echo '</ol>';
                        echo '</li>';
                    } 
                }
            ?>
        </ol>
    </div>

<!-- Save as words -->

<page side="A4" id="report_wrapper_2" style="display: none;">
    <div style="text-align: center">
        <div><h3><span class="lab_name"></span></h3></div>
        <div><h5>Microbiology Laboratory Report</h4></div>
        <div><h5><span class="date_frame"></span></h5></div>
    </div>
    <?php 
        echo '<ol class="toc">';
        foreach($TITLES as $title ){
            $id_title = str_replace(' ','_',strtolower($title['title']));
            $ttl = $title['title'];
            $data = $title['data'];
            if ($title['child'] == null){
                echo '<li><h4>'.$ttl.'</h4>';
                if($data !== null){
                    foreach($data as $item){
                        if($item['type'] == 'graph'){
                            echo '<div style="width: 500px; text-align: center; positive: relative; overflow: auto;">';
                            echo '<div id="'.$item['id'].'_image" style="display: inline-block;"></div>';
                            echo '</div>';
                        }else if($item['type'] == 'table'){
                            echo '<table id="'.$item['id'].'_clone" class="tbl_pathogen"></table>';
                        }
                    }
                }else{
                    // Special case for Micrology specimen
                    if($title['id'] == 3){
                ?>
                        <table border="0" width="100%">
                            <?php
                                foreach($samples as $sample){
                                    $id_chart = str_replace(" ","_",$sample["sample_name"])."_chart";
                                    $sample_name = $sample["sample_name"];
                                    if (!in_array($sample["ID"], array(9,17))) {
                                        echo "<tr id='parent_".$id_chart."_image'>"; 
                                        echo "<td style='text-align:center;'><p style='text-align:left;'>".$sample_name."</p><div id=".$id_chart."_image"." ></div></td>";                                            
                                        echo "</tr>";
                                    }
                                }
                                echo "<tr>";
                                echo "<td style='text-align:center;'><p style='text-align:left;'>Pus</p><div id='pus_chart_image' ></div></td>";                                            
                                echo "</tr>";
                            ?>
                            <tr>
                                <td style="text-align: center;"><div id="specimen_by_month_wrapper"></div></td>
                            </tr>
                        </table>
                <?php
                    }
                }
                echo '</li>';
            } else{
                $children = $title['child'];
                echo '<li><h4>'.$ttl.'</h4>';
                echo '<ol class="header">';
                foreach($children as $child){
                    $id_child = str_replace(' ','_',strtolower($child['subtitle']));
                    $subtitle = $child['subtitle'];
                    echo '<li><h5 id="'.$id_child.'">'.$subtitle.'</h5>';
                    if($child['type'] == 'graph'){
                        echo '<div id="'.$child['id'].'_image"></div>';
                    }else if($child['type'] == 'table'){                        
                        echo '<table id="'.$child['id'].'_clone" class="tbl_pathogen"></table>';
                    }
                    // Special case 
                    //Burkholderia pseudomallei has antibiotic table, so we need to customize it
                    if($child['id'] == 'bps_chart'){
                        echo '<table id="antibiotic_bps_tbl_clone" class="tbl_pathogen"></table>';                        
                    }
                    echo '</li>';
                }
                echo '</ol>';
                echo '</li>';
            } 
        }
    echo '</ol>';
    ?>    
</page>




<script>
    /*
    window.addEventListener('load', (event) => {
        console.log('page is fully loaded');
        $('body').attr('data-spy','scroll');
        $('body').attr('data-target','#myScrollspy');
        $('body').attr('data-offset','15');
        
    });
    
    var lastScrollTop = 0;
    $(window).scroll(function(event){
    var st = $(this).scrollTop();
    if (st > lastScrollTop){
        //$('#myScrollspy').css('margin-top','100');
        // downscroll code
        console.log("top");
        
    } else {
        // upscroll code
        console.log("up");
    }
    lastScrollTop = st;
    });
    
    $("#template-wrapper").off("scroll").on("scroll", function (evt) {
		var sTop = $(this).scrollTop();

		var top_menu = $("nav#main-menu");
		if (sTop >= 150 && !top_menu.hasClass("fixed-top")) {
			top_menu.addClass("fixed-top");
			top_menu.animate({
				"marginTop": "0"
			});
		} else if (top_menu.hasClass("fixed-top") && sTop < 100) {
			top_menu.removeAttr("style");
			top_menu.removeClass("fixed-top");
		}
	});
    */
</script>