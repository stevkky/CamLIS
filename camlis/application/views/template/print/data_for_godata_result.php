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
		
		td span {
			transform: rotate(-90deg);
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			position:relative;
			white-space: nowrap;
			height: 120px;
			width:25px;
			top:45px;
			left: 55px;
			display:inline-block;
			font-size:11px; 
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
		 <table widtd="100%" height="100%" cellpadding="0" cellspacing="0" id="tbl_result" class="tbl" border="1" bgcolor="#FFFFFF">
         	<thead>
            	<th>Laboratory</th>
				<th>Sample_Type</th>
				<th>Test Type</th>
				<th>Status</th>
				<th>Tested For</th>
				<th>ID</th>
				<th>Code Laboratory</th>
				<th>Health Facility</th>
				<th>Date of Completion</th>
				<th>Completed by</th>
				<th>Telephone</th>
				<th>Reasons for Testing</th>
				<th>Contact of Patient</th>
				<th>Relationship</th>
				<th>Patient Name</th>
				<th>Patient ID</th>
				<th>Passport No</th>
				<th>Sex</th>
				<th>Age</th>
				<th>Nationality</th>
				<th>Patient_Tel</th>
				<th>Occupation</th>
				<th>Occupation2</th>
				<th>Address (house, village, commune, district)</th>
				<th>Province</th>
				<th>District</th>
				<th>Commune</th>
				<th>Village</th>
				<th>Fever (Y/N)</th>
				<th>Cough (Y/N)</th>
				<th>Runny nose (Y/N)</th>
				<th>Sore Throat (Y/N)</th>
				<th>Difficulty Breathing (Y/N)</th>
				<th>No Symptoms (Y/N)</th>
				<th>Date Onset (dd/mm/yyyy)</th>
				<th>Previous Covid (Y/N)</th>
				<th>Date_Prev.Test</th>
				<th>Country/Province</th>
				<th>Date_Arrival&nbsp;&nbsp;(dd/mm/yyyy)</th>
				<th>Flight No</th>
				<th>Seat No</th>
				<th>Place_Collection</th>
				<th>Date_Collection</th>
				<th>Visit No</th>
				<th>Sample Collector</th>
				<th>Collector_Tel</th>
				<th>Received _Date&nbsp;&nbsp;(dd/mm/yyyy)</th>
				<th>Testing Date&nbsp;&nbsp;(dd/mm/yyyy)</th>
				<th>Test Result (Neg/Pos)</th>
				<th>Vaccinated Status</th>
				<th>VaccinatedOne</th>
				<th>Vaccinated1 Date</th>
				<th>Vaccinated2</th>
				<th>Vaccinated2 Date</th>
				<th>Vaccine Name</th>
				<th>Vaccinated3</th>
				<th>Vaccinated3 Date</th>
				<th>Vaccine3 Name</th>
				<th>Types of Test</th>
				<th>Surveillance</th>
				<th>Patient type</th>
				<th>Date Reporting</th>
				<th>Requested by</th>
				<th>NEW Reasons for Testing</th>
			</thead>
            <tbody>
				<?php 
					for($i=0 ; $i < count($bact_list) ; $i++){
						$data = $bact_list[$i];
						for($j =0; $j < count($data) ; $j++){
							$sample_status = "";
							$reason_for_testing = ($data[$j]['reason_for_testing'] == '') ? $data[$j]['diagnosis'] : $data[$j]['reason_for_testing'];
							echo "<tr>";
							echo "<td>".$data[$j]['lab_code']."</td>";
							echo "<td>".$data[$j]['sample_type']."</td>";
							echo "<td>".$data[$j]['test_name']."</td>";
							if(isset($data[$j]['result_organism'])) $sample_status = "Completed";
							echo "<td>".$sample_status."</td>";
							echo "<td>SARS-CoV2</td>";
							echo "<td>".$data[$j]['sample_id']."</td>";
							echo "<td>".$data[$j]['patient_code']."</td>";
							echo "<td>".$data[$j]['health_facility']."</td>";
							echo "<td>&nbsp;</td>";// date of completion
							echo "<td>".$data[$j]['completed_by']."</td>";
							echo "<td>".$data[$j]['phone_number']."</td>";
							echo "<td>".$reason_for_testing."</td>";
							echo "<td>".$data[$j]['contact_with']."</td>";
							echo "<td>&nbsp;</td>"; // relationship
							echo "<td>".$data[$j]['patient_name']."</td>";
							echo "<td>".$data[$j]['patient_code']."</td>";
							echo "<td>".$data[$j]['passport_number']."</td>";
							echo "<td>".$data[$j]['patient_gender']."</td>";
							echo "<td>".$data[$j]['patient_age']."</td>";
							echo "<td>".$data[$j]['nationality']."</td>";
							echo "<td>".$data[$j]['phone']."</td>";
							echo "<td>".$data[$j]['occupation']."</td>";
							echo "<td>&nbsp;</td>"; // occupation2
							echo "<td>&nbsp;</td>"; // address full
							echo "<td>".$data[$j]['province_name']."</td>";
							echo "<td>".$data[$j]['district_name']."</td>";
							echo "<td>".$data[$j]['commune_name']."</td>";
							echo "<td>".$data[$j]['village_name']."</td>";
							$symptoms = $data[$j]['symptoms'];
							$symptoms = str_replace(['{','}'],'',$symptoms); // remove {}
							$symptoms = explode(',',$symptoms); // turn to array
							if(in_array("Fever",$symptoms)){
								echo "<td>Y</td>";
							}else{
								echo "<td>N</td>";
							}
							if(in_array("Cough",$symptoms)){
								echo "<td>Y</td>";
							}else{
								echo "<td>N</td>";
							}
							if(in_array('"Runny Nose"',$symptoms)){
								echo "<td>Y</td>";
							}else{
								echo "<td>N</td>";
							}
							if(in_array('"Sore Throat"',$symptoms)){
								echo "<td>Y</td>";
							}else{
								echo "<td>N</td>";
							}
							if(in_array('"Difficulty Breathing"',$symptoms)){
								echo "<td>Y</td>";
							}else{
								echo "<td>N</td>";
							}
							if(in_array('"No symptoms"',$symptoms)){
								echo "<td>Y</td>";
							}else{
								echo "<td>N</td>";
							}
							echo "<td>".$data[$j]['date_of_onset']."</td>";
							echo "<td>".$data[$j]['is_positive_covid']."</td>";
							echo "<td>".$data[$j]['test_date']."</td>";
							echo "<td>".$data[$j]['country_name']."</td>";
							echo "<td>".$data[$j]['date_arrival']."</td>";
							echo "<td>".$data[$j]['flight_number']."</td>";							
							echo "<td>".$data[$j]['seat_number']."</td>";
							echo "<td>".$data[$j]['place_of_collection']."</td>";
							echo "<td>".$data[$j]['date_of_collection']."</td>";
							echo "<td>".$data[$j]['number_of_sample']."</td>";
							echo "<td>".$data[$j]['sample_collector']."</td>";
							echo "<td>".$data[$j]['phone_collector']."</td>";
							echo "<td>".$data[$j]['received_date']."</td>";
							echo "<td>".$data[$j]['test_result_date']."</td>";
							echo "<td>".$data[$j]['result_organism']."</td>";
							echo "<td>".$data[$j]['vaccination_status']."</td>";
							echo "<td>".$data[$j]['vaccinated_one']."</td>";
							echo "<td>".$data[$j]['first_vaccinated_date']."</td>";
							echo "<td>".$data[$j]['vaccinated_two']."</td>";
							echo "<td>".$data[$j]['second_vaccinated_date']."</td>";
							echo "<td>".$data[$j]['first_vaccine_name']."</td>";
							echo "<td>".$data[$j]['vaccinated_three']."</td>";
							echo "<td>".$data[$j]['third_vaccinated_date']."</td>";
							echo "<td>".$data[$j]['second_vaccine_name']."</td>";
							echo "<td>".$data[$j]['test_name']."</td>";
							echo "<td>".$reason_for_testing."</td>";
							echo "<td>".$reason_for_testing."</td>";
							echo "<td>&nbsp;</td>";
							echo "<td>".$data[$j]["requester"]."</td>";
							echo "<td>".$reason_for_testing."</td>";
							echo "</tr>";
						}
						
					}
				?>
            	
			</tbody>
		</table>		
		</div>	 
	</page> <!-- End of Page -->
</body>
</html>