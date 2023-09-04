<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Result</title> 
	<style>
		 
		body {
			background: rgb(204, 204, 204);
		}
		page[size="A4"] {
			background: white;
			width: 100%; 
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
			size: A4 landscape;
			padding: 0mm !important;
		}
		@media print {
			body,
			page[size="A4"] {
				margin: 0;
				box-shadow: 0;
			}
		}

		 table {
			white-space: normal;
			line-height: normal;
			font-weight: normal;
			font-size: medium;
			font-style: normal;
			color: -internal-quirk-inherit;
			text-align: start;
			font-variant: normal normal;
			 
		}
		th{
			font-size:11px;
		}
		td {
			color: #000000;
			font-size:11px;
		}		
		th span {
			transform: rotate(-90deg);
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			position: relative;
			white-space: nowrap;
			height: 150px;
			width: 25px;
			top: 60px;
			left: 64px;
			display: inline-block;
			font-size: 11px;
		}
		 
		tr.stylerow:hover {background-color: #f3f8aa;}
		 
		.tcn{
			text-align:center; 
		}
	</style>
	 
</head>
<body>

<?php
	//var_dump($bact_list);	
?>
	<!--  size="A4"  -->
	<page size="A4" >
    	<div style="overflow:auto; max-height: 600px;">
        
        <div class="result" style="padding-bottom:3px;">
			<?php 
					$sql = 'select * from sensitivity_type where status = TRUE'; 
					$s_result = $this->db->query($sql)->result();
					echo "<center><table>";  
					echo "<tr><td style='padding-top:10px;'>";
					echo "<b>Note : </b>";
					$s= '';
					foreach($s_result as $sow){
						echo $s.$sow->sensitivity_type;
						$s = ", ";
					}
					echo "</td></tr></table></center>";
			?>
		</div>
		
		 <table widtd="100%" height="100%" cellpadding="0" cellspacing="0" class="tbl" border="1" bgcolor="#FFFFFF" id="tbl_result">
         	<thead>
            	<tr bgcolor="#99CCFF">
				    <th  class="tcn">No</th>
                    <th  class="tcn">Lab Name</th>
					<th  class="tcn">Patient ID</th>
                    <th  class="tcn">Lab ID</th>
                    <th  class="tcn">Sex</th>
                    <th  class="tcn">DOB</th>
                    <th  class="tcn">Age</th>
                    <th  class="tcn">Sample</th>
                    <th  class="tcn">Sample site</th>		
					<th  class="tcn">Volume1</th>	
					<th  class="tcn">Volume2</th>	
					<th  class="tcn">Sample source</th>
                    <th >Collection Date</th>
					<th >Admission Date</th>
                    <th >Test Date</th>
                    <th >Diagnosis</th>
					<th >Contaminant</th>
                    <th  class="tcn"><div style="width: 230px">Result</div></th>
					<th><span>Amikacin</span></th>
					<th><span>Amoxi_Clav</span></th>
					<th><span>Ampi_Peni</span></th>
					<th><span>Ampicillin</span></th>
					<th><span>Azithromycin</span></th>
					<th><span>Cefazolin</span></th>
					<th><span>Cefepime</span></th>
					<th><span>Cefotaxime</span></th>
					<th><span>Ceftazidime</span></th>
					<th><span>Ceftriaxone</span></th>
					<th><span>Cephalosporins</span></th>
					<th><span>Cephalothin</span></th>
					<th><span>Chloramphenicol</span></th>
					<th><span>Ciprofloxacin</span></th>
					<th><span>Clindamycin</span></th>
					<th><span>Cloxacillin</span></th>
					<th><span>Erythromycin</span></th>
					<th><span>Fosfomycin</span></th>
					<th><span>Fluoroquinolone</span></th>
					<th><span>Gentamicin</span></th>
					<th><span>Imipenem</span></th>
					<th><span>Levofloxacin</span></th>
					<th><span>Meropenem</span></th>
					<th><span>Metronidazole</span></th>
					<th><span>Minocycline</span></th>
					<th><span>Nalidixic_acid</span></th>
					<th><span>Nitrofurantoin</span></th>
					<th><span>Norfloxacin</span></th>
					<th><span>Oxacillin</span></th>					
					<th><span>Penicillin</span></th>
					<th><span>Tetracycline</span></th>
					<th><span>Trimeth_Sulfa</span></th>
					<th><span>Vancomycin</span></th>
					<th><span>Pip_tazobactam</span></th>
					<th><span>Penicillin_meningitis</span></th>
					<th><span>Penicillin_non_meningitis</span></th>
					<th><span>Ceftriaxone_meningitis</span></th>
					<th><span>Ceftriaxone_non_meningitis</span></th>
					<th><span>Gentamicin_synergy</span></th>
					<th><span>Oral_Cephalosporins</span></th>
					<th><span>Novobiocin</span></th>
					<th><div style="width: 500px">Comment</div></th>
					<th><div style="width: 500px">Rejected comment</div></th>
              	</tr>
              	
			</thead>
            <tbody>
            	<?php $i = 1;foreach($bact_list as $key => $row){?>
					<?php						
						$lab_code		= $row["lab_name"];						
						$patient_id 	= $row["patient_id"];
						$source_name 	= $row["sample_source"];
						$diagnosis 		= $row["diagnosis"];
						$description 	= $row["sample_site"];
						
						$sample_volume1 = $row["volume1"];
						$sample_volume2 = $row["volume2"];												
						
						$age_obj 		= calculateAge($row['dob']);
						$age 			= "1d";

						if($age_obj->y > 0) {$age = $age_obj->y."y";}
						else if ($age_obj->m > 0) {$age = $age_obj->m."m";}
						else if ($age_obj->d > 0) {$age = $age_obj->d."d";}

						$collected_date = date('d-M-Y H:i', strtotime($row["collection_date"]));
						$test_date 		= date('d-M-Y', strtotime($row["test_date"]));						
						$dob 			= date('d-M-Y', strtotime($row["dob"]));
						$admission_date = ($row["admission_date"] !== null) ? date('d-M-Y H:i', strtotime($row["admission_date"])) : '';
						$reject_comment = $row["reject_comment"];
						
						$test = $row["admission_date"];
						$bact_list[$key]['age'] 			= $age;
						$bact_list[$key]["admission_date"] 	= $admission_date;
						$bact_list[$key]["collection_date"] = $collected_date;
						$bact_list[$key]["test_date"] 		= $test_date;
						$bact_list[$key]["dob"] 			= $dob;
					?>
            	<tr class="stylerow">
					<td><?php echo $i;?></td>
                    <td><?php echo $lab_code;?></td>
					<td><?php echo $patient_id;?></td>
                    <td><?php echo $row['lab_id'];?></td>
                    <td class="tcn"><?php echo $row['sex'];?></td>
                    <td class="tcn"><?php echo $dob;?></td>
                    <td class="tcn"><?php echo $age;?></td>
                    <td class="tcn"><?php echo $row['sample'];?></td>
					<td class="tcn"><?php echo $description;?></td>					
					<td class="tcn"><?php echo $sample_volume1;?></td>
					<td class="tcn"><?php echo $sample_volume2;?></td>
					<td class="tcn"><?php echo $source_name;?></td>
                    <td class="tcn"><?php echo $collected_date;?></td>					
					<td class="tcn" data-tst="<?php echo $test." ".date('d-M-Y h:i', strtotime($row["admission_date"]));?>"><?php echo $admission_date;?></td>
                    <td class="tcn"><?php echo $test_date;?></td>					
                    <td class="tcn"><?php echo $diagnosis;?></td>
                    <td class="tcn"><?php echo $row['contaminant'];?></td>
					<td class="tcn"><?php echo $row['results'];?></td>
                    <td class="tcn"><?php echo empty($row['amikacin']) ? '' : $row['amikacin'];?></td>
					<td class="tcn"><?php echo empty($row['amoxi_clav']) ? '' : $row['amoxi_clav'];?></td>
					<td class="tcn"><?php echo empty($row['ampi_ceni']) ? '' : $row['ampi_ceni'];?></td>
					<td class="tcn"><?php echo empty($row['ampicillin']) ? '' : $row['ampicillin'];?></td>
					<td class="tcn"><?php echo empty($row['azithromycin']) ? '' : $row['azithromycin'];?></td>
					<td class="tcn"><?php echo empty($row['cefazolin']) ? '' : $row['cefazolin'];?></td>
					<td class="tcn"><?php echo empty($row['cefepime']) ? '' : $row['cefepime'];?></td>
					<td class="tcn"><?php echo empty($row['cefotaxime']) ? '' : $row['cefotaxime'];?></td>
					<td class="tcn"><?php echo empty($row['ceftazidime']) ? '' : $row['ceftazidime'];?></td>
					<td class="tcn"><?php echo empty($row['ceftriaxone']) ? '' : $row['ceftriaxone'];?></td>
					<td class="tcn"><?php echo empty($row['cephalosporins']) ? '' : $row['cephalosporins'];?></td>
					<td class="tcn"><?php echo empty($row['cephalothin']) ? '' : $row['cephalothin'];?></td>
					<td class="tcn"><?php echo empty($row['chloramphenicol']) ? '' : $row['chloramphenicol'];?></td>
					<td class="tcn"><?php echo empty($row['ciprofloxacin']) ? '' : $row['ciprofloxacin'];?></td>
					<td class="tcn"><?php echo empty($row['clindamycin']) ? '' : $row['clindamycin'];?></td>
					<td class="tcn"><?php echo empty($row['cloxacillin']) ? '' : $row['cloxacillin'];?></td>
					<td class="tcn"><?php echo empty($row['erythromycin']) ? '' : $row['erythromycin'];?></td>
					<td class="tcn"><?php echo empty($row['fosfomycin']) ? '' : $row['fosfomycin'];?></td>
					<td class="tcn"><?php echo empty($row['fluoroquinolone']) ? '' : $row['fluoroquinolone'];?></td>
					<td class="tcn"><?php echo empty($row['gentamicin']) ? '' : $row['gentamicin'];?></td>
					<td class="tcn"><?php echo empty($row['imipenem']) ? '' : $row['imipenem'];?></td>
					<td class="tcn"><?php echo empty($row['levofloxacin']) ? '' : $row['levofloxacin'];?></td>
					<td class="tcn"><?php echo empty($row['meropenem']) ? '' : $row['meropenem'];?></td>
					<td class="tcn"><?php echo empty($row['metronidazole']) ? '' : $row['metronidazole'];?></td>
					<td class="tcn"><?php echo empty($row['minocycline']) ? '' : $row['minocycline'];?></td>
					<td class="tcn"><?php echo empty($row['nalidixic_acid']) ? '' : $row['nalidixic_acid'];?></td>
					<td class="tcn"><?php echo empty($row['nitrofurantoin']) ? '' : $row['nitrofurantoin'];?></td>
					<td class="tcn"><?php echo empty($row['norfloxacin']) ? '' : $row['norfloxacin'];?></td>
					<td class="tcn"><?php echo empty($row['oxacillin']) ? '' : $row['oxacillin'];?></td>					
					<td class="tcn"><?php echo empty($row['penicillin']) ? '' : $row['penicillin'];?></td>
					<td class="tcn"><?php echo empty($row['tetracycline']) ? '' : $row['tetracycline'];?></td>
					<td class="tcn"><?php echo empty($row['trimeth_sulfa']) ? '' : $row['trimeth_sulfa'];?></td>
					<td class="tcn"><?php echo empty($row['vancomycin']) ? '' : $row['vancomycin'];?></td>
					<td class="tcn"><?php echo empty($row['pip_tazobactam']) ? '' : $row['pip_tazobactam'];?></td>
					<td class="tcn"><?php echo empty($row['penicillin_meningitis']) ? '' : $row['penicillin_meningitis'];?></td>
					<td class="tcn"><?php echo empty($row['penicillin_non_meningitis']) ? '' : $row['penicillin_non_meningitis'];?></td>
					<td class="tcn"><?php echo empty($row['ceftriaxone_meningitis']) ? '' : $row['ceftriaxone_meningitis'];?></td>
					<td class="tcn"><?php echo empty($row['ceftriaxone_non_meningitis']) ? '' : $row['ceftriaxone_non_meningitis'];?></td>
					<td class="tcn"><?php echo empty($row['gentamicin_synergy']) ? '' : $row['gentamicin_synergy'];?></td>
					<td class="tcn"><?php echo empty($row['oral_cephalosporins']) ? '' : $row['oral_cephalosporins'];?></td>
					<td class="tcn"><?php echo empty($row['novobiocin']) ? '' : $row['novobiocin'];?></td>
                    <td class="tcn"><?php echo empty($row["comment"]) ? '' : $row["comment"];?></td>
					<td class="tcn"><?php echo $reject_comment; ?></td>
                </tr><?php $i++; } ?>
			</tbody>
		</table>
		</div>	 
	</page> <!-- End of Page -->
</body>
</html>
<script>
	var bacter_result = [];	
	$(document).ready(function (){
		bacter_result = <?php echo json_encode($bact_list); ?>;		
	})
</script>
