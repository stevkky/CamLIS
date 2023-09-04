<!-- Print Preview Modal -->
<div class="modal fade" id="print_preview_modal" style="padding-top:40px; background: rgba(0, 0, 0, 0.54);">
	<div class="print-preview-header">
        <div class="presult-pagination">
            <input type="text" class="page-number" value="1" onfocus="this.select()"> /
            <span class="page-count">1</span>
        </div>
		<ul>			
			<?php if ($this->aauth->is_allowed('print_patient_result')): ?>
				<li style="display: inline-block;" class="print">
					<a href="javascript:void(0)" id="doPrinting"><i class="fa fa-print"></i>&nbsp;<b><?php echo _t('global.print'); ?></b></a>
				</li>
			<?php endif ?>
		</ul>
		<span class="close-preview-return" data-dismiss='modal' onclick="document.location.href='<?php echo $this->app_language->site_url('sample/view'); ?>'" title="<?php echo _t('global.returnsample'); ?>"><i class="fa fa-arrow-circle-right"><b><?php echo _t('global.sample'); ?></i>&nbsp;</b></span>
		<span class="close-preview" data-dismiss='modal' title="<?php echo _t('global.close'); ?>"><i class="fa fa-times"></i></span>
	</div>
	<div class="modal-dialog A4-portrait">
		<?php
		$title_array = array(
			"Distribution by Gender",
			"Distribution by Age Group",
			"Microbiology Specimen",
			"Isolated Pathogens among Blood and CSF",
			"Bloodstream pathogens isolated",
			"Blood culture true pathogen rate and contamination rate by ward",
			"Blood culture volume",
			"Notifiable and other important pathogens list",
			"Burkholderia pseudomallei (Bps)",
			"Salmonella",
			"Staphylococcus aureus",
			"Escherichia coli",
			"Klebsiella pneumoniae",
			"Pseudomonas aeruginosa",
			"Acinetobacter"
		);
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>Microbiology Report</title>
			<meta name="viewport" content="width=device-width, initial-scale=.5, maximum-scale=12.0, minimum-scale=.25, user-scalable=yes"/>
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
										</td>
										<td class="text-top text-center">
											<div class="KhmerMoulLight" style="font-size: 13pt; font-weight: bold;"><span class="lab_name"></span></div>
											<div class="KhmerMoulLight" style="font-size: 10pt;">Microbiology Laboratory Report</div>
											<div class="tacteng" style="font-size: 24pt; margin-bottom: 10px;">3</div>
											<div class="KhmerMoulLight" style="font-size: 12.5pt; font-weight: bold;"><span class="date_frame"></span></div>
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
					
					<tbody class="sample-result">
						<tr>
							<td>
								<table width="100%" border="0">
									<tr><td><h2>1) <?php echo $title_array[0];?><h2></td></tr>
									<tr><td>The proportion of patients by gender</td></tr>
									<tr><td id="gender_chart_image" style="text-align: center;"></td></tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table width="100%" border="0">
									<tr><td><h2>2) <?php echo $title_array[1];?><h2></td></tr>
									<tr><td>Distribution of age group and gender. We accepted the range 0 to 110 years old. We have  patient(s)</td></tr>
									<tr><td id="agechartdiv_image" style="text-align: center;"></td></tr>
								</table>
							</td>
						</tr>

						<tr>
							<td>
								<table width="100%" border="0">
									<tbody>
										<tr><td><h2>3) <?php echo $title_array[2];?></h2></td></tr>
										<tr>
											<td>
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
											</td>
										</tr>
									</tbody>
								</table> 
							</td>
						</tr>

						<tr>
							<td>
								<table width="100%" border="0">
									<tbody>
										<tr><td><h2>4) <?php echo $title_array[3];?></h2></td></tr>
										<tr>
											<td>4.1) Cerebrospinal fluid (CSF)</td>
										</tr>
										<tr>
											<td style="text-align: center;">
												<div id="csf_pathogent_chart_image"></div>
											</td>
										</tr>
										<tr>
											<td>4.2) Blood culture</td>
										</tr>
										<tr>
											<td style="text-align: center;">
												<div id="blood_culture_pathogent_chart_image"></div>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>			
				</table>
				
			</page>
			<?php if (isset($action) && $action == 'print') { ?>
				<script>
					window.print();
				</script>
			<?php } ?>

		</body>
		</html>
	</div>
    <button class="previous"><i class="fa fa-chevron-left"></i></button>
    <button class="next"><i class="fa fa-chevron-right"></i></button>
</div>