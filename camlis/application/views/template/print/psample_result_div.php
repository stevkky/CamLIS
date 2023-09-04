<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Result</title>
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/A4_print_tmp.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/psample_result.css'); ?>">
	<style>
		body {
			background:transparent;
		}
		.result-print-layout .tb {
			display : table;
			margin-top : 12px;
		}
		.result-print-layout .tb .tb-row,
		.result-print-layout .tb .tb-header {
			display : table-row;
		}
		
		.result-print-layout .tb .tb-row .tb-cell,
		.result-print-layout .tb .tb-header .tb-cell {
			display : table-cell;
			padding:5px 0;
		}
		
		.result-print-layout .tb .tb-header .tb-cell {
			font-weight : bold;
			border-bottom : 1px solid #d4d4d4;
		}
		
		.result-print-layout .no-result {
			padding: 6px 0;
			text-align: center;
		}
		
		.result-print-layout p.value-pair {
			position: relative;
			width: 98%;
			margin: 0;
		}
		
		.result-print-layout p.value-pair:before {
			content: '';
			display: block;
			position: absolute;
			width: 98%;
			bottom: .2rem;
			height: 0;
			line-height: 0;
			border-bottom: 2px dotted black;
		}
		
		.result-print-layout p.value-pair .name {
			background: white;
			z-index: 1;
			position: relative;
			padding-right: 5px;
		}
		
		.result-print-layout p.value-pair .name-result {
			background: white;
			z-index: 1;
			right: 0;
			position: absolute;
			padding-left: 5px;
			font-weight: bold;
		}
	</style>
	<?php
		$row_count	= 12;
		function getTableRow($col1, $col2, $col3, $col4) {
			return "<div class='tb-row'>
						<div class='tb-cell' style='width:5.5cm;'>$col1</div>
						<div class='tb-cell' style='vertical-align:bottom; width:2.2cm;'>$col2</div>
						<div class='tb-cell' style='vertical-align:bottom; width:3cm;'>$col3</div>
						<div class='tb-cell' style='vertical-align:bottom; width:10.3cm;'>$col4</div>
					</div>";
		}

		function checkEndOfPage(&$row_count) {
			if ($row_count == 34) {
				echo "</div></page>";
				echo '<page size="A4" class="result-print-layout">';
				echo '<div class="result tb">';

				$row_count = 0;
			}
		}
	?>
</head>
<body>
	<!--  size="A4"  -->
	<page size="A4" class="result-print-layout">
		<div class="header">
			<div class="logo">
				<img src="<?php echo site_url('assets/camlis/images/moh_logo.png'); ?>" alt="Logo">
			</div>
			<div class="title">
				<span class="lab_nameKH"><?php echo $laboratoryInfo->nameKH; ?></span><br>
				<span class="lab_nameEN"><?php echo $laboratoryInfo->nameEN; ?></span><br>
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
						<th class="text-right">Patient's ID :</th>
						<td><?php echo $patient->pid; ?></td>
					</tr>
					<tr>
						<th class="text-right">Name :</th>
						<td><?php echo $patient->name; ?></td>
					</tr>
					<tr>
						<th class="text-right">Gender :</th>
						<td><?php echo $patient->sex == 1 ? "Male" : "Female"; ?></td>
					</tr>
					<tr>
						<th class="text-right">Address :</th>
						<td><?php echo $patient->provinceEN; ?></td>
					</tr>
				</table>
			</div>
			<div class="section2">
				<table>
					<tr>
						<th class="text-right">Sample Source :</th>
						<td><?php echo $psample->sample_source; ?></td>
					</tr>
					<tr>
						<th class="text-right">Phone Number :</th>
						<td><?php echo $patient->phone; ?></td>
					</tr>
					<tr>
						<th class="text-right">Age :</th>
						<td>
						<?php
                            echo getAging($patient['dob']);
							/*$days	= getAge($patient->dob);
							$year	= floor($days / 365);
							$month	= floor(($days % 365) / 30);
							$day	= floor(($days % 365) % 30);
							
							$age	= $year.' Years '.$month.' Months '.$day.' days';
							echo $age;*/
						?>
						</td>
					</tr>
					<tr>
						<th class="text-right">Clinical :</th>
						<td><?php echo $psample->clinical_history; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="sample_info">
			<table>
				<tr>
					<th class="text-left"><?php echo $psample->department_name; ?></th>
					<th class="text-left">Sample Number</th>
					<th class="text-left">Requested by</th>
					<th class="text-left">Received Date</th>
					<th class="text-left">Test Date</th>
				</tr>
				<tr>
					<td><?php echo $psample->sample_name; ?></td>
					<td><?php echo $psample->sample_number; ?></td>
					<td><?php echo $psample->requester_name; ?></td>
					<td><?php echo $psample->received_date; ?></td>
					<td><?php echo ""; ?></td>
				</tr>
			</table>
		</div>
		<div class="result tb">
			<div class="tb-header">
				<div class="text-left tb-cell" style="width:5.5cm;">Test Name</div>
				<div class="text-left tb-cell" style="width:2.2cm;">Units</div>
				<div class="text-left tb-cell" style="width:3cm;">Ref.Range</div>
				<div class="text-left tb-cell" style="width:10.3cm;">Result</div>
			</div>
			<?php
				foreach($result as $row) {
					$result_tmp = "";

					//Result
					if (isset($row["result"]) && !is_array($row["result"])) {
						$result_tmp = $row["result"];

						echo getTableRow("<p class='value-pair'><span class='name'>".$row["testName"]."</span></p>",
										 $row["unit_sign"],
										 $row["ref_range"],
										 $result_tmp);
						
						$row_count++;
						checkEndOfPage($row_count);
						
					}
					else if (isset($row["result"]) && is_array($row["result"]) && count($row["result"]) > 0 ) {
						//Single and Multiple Result
						$i = 1;
						foreach($row["result"] as $arr) {
							//Show Test info for first result only
							$testName	= $i == 1 ? "<p class='value-pair'><span class='name'>".$row["testName"]."</span></p>" : "";
							$unit_sign	= $i == 1 ? $row["unit_sign"] : "";
							$ref_range	= $i == 1 ? $row["ref_range"] : "";
							
							//Organism
							$result_tmp		 = "";
							$result_tmp		.= "<p class='value-pair'>";
							$result_tmp		.= "<span class='name'>".$arr["result_name"]."</span>";
							$result_tmp		.= "<span class='name-result'>".$arr["qty"]."</span>";
							$result_tmp		.= "</p>";

							echo getTableRow($testName, $unit_sign, $ref_range, $result_tmp);
							$row_count++;
							checkEndOfPage($row_count);
							
							//Antibiotic
							if (count($arr["antibiotic"]) > 0) {
								foreach($arr["antibiotic"] as $a) {
									$anti	 = "<p class='value-pair'>";
									$anti	.= "<span class='name'>&nbsp;&nbsp;&nbsp;- ".$a["antibiotic_name"]."</span>";
									$anti	.= "<span class='name-result'>".$a["sensitivity"]."</span>";
									$anti	.= "</p>";
									
									echo getTableRow("", "", "", $anti);
									$row_count++;
									checkEndOfPage($row_count);
								}

							}
							
							$i++;
						}
					} else {
						echo getTableRow("<p class='value-pair'><span class='name'>".$row["testName"]."</span></p>",
										 $row["unit_sign"],
										 $row["ref_range"],
										 "");

						$row_count++;
						checkEndOfPage($row_count);
					}
				}
			?>
			
		</div>
		
		<?php if (count($result) == 0) { ?>
			<div class="no-result">No Result!</div>
		<?php $row_count++; checkEndOfPage($row_count); } ?>
		
		<div class="note">
			<?php
				if (count($sensitivity_type) > 0) {
					echo "<b>Note : </b>";
					$str = array();
					foreach($sensitivity_type as $typ) {
						$str[] = "<b>".$typ["abbr"]."</b> = ".$typ["full"];
					}
					echo implode(', ', $str);
				}
			?>
		</div>
		<div class="comment">
			<table>
				<tr>
					<th class="text-left" style="width:2.2cm; vertical-align:top;">Comment : </th>
					<td>
					<?php
						$cmt = explode('<br/>', $psample->result_comment);
						$tmp = "<ul class='comment-list'>";
						
						if (isset($cmt[0]) && empty($cmt[0]) && count($cmt) == 1)
							$tmp .= "N/A";
						
						foreach ($cmt as $c) {
							$c	= strpos(trim($c), '-') == 0 ? substr(trim($c), 1) : $c;
							if (!empty($c))
								$tmp .= "<li>".trim($c)."</li>";
						}
						
						$tmp .= "</ul>";
						echo $tmp;
					?>
					</td>
				</tr>
			</table>
		</div>
	</page>
	<?php if ($type == 'print') { ?>
	<script>
		window.print();
	</script>
	<?php } ?>
</body>
</html>