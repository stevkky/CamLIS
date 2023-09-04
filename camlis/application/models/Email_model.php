<?php
defined('BASEPATH') OR die('Permission denied.');
class Email_model extends MY_Model {
	public function __construct() {
		parent::__construct();   
	}
	
	/* 
	 * function
	 * send email 
	 * @param: obj
	 * meet criteria send email while add new test
	*/
	public function email_urgent_test($obj){ 
		// get body message​
		/*$obj->message =	"ទូរស័ព្ទទៅកាន់ - Calling to<br />";
		$obj->message.=	"នាយកដ្នានប្រយុទ្ធនឹងជំងឺឆ្លង - Department of Diseases<br />";
		$obj->message.=	"<h2>115</h2>";
		$obj->message.=	"<strong>+855 12 588 981</strong><br />";
		$obj->message.=	"<strong>+855 89 669 567</strong><br /><br /><br />";
		$obj->message.=	"រាយការណ៌ជាបន្ទាន់ក្នុង System បានរកឃើញនូវប្រភេទមេរោគ -	 Urgent kind of diseases found<br /><br />";
		$obj->message.=	"<strong><ul><li>".$obj->diseases."</li></ul></strong><br /><br />";
		$obj->message.=	"ដោយមានជំនួយពីក្រុមការងារបច្ចេកទេស AMR<br />";*/
		
		
		
		$obj->message =	$this->template($obj);
		//
		$obj->title = 'CamLIS '.$obj->inscress_id.' - '.$obj->diseases.' was identified at '.$this->session->userdata["laboratory"]->name_en.' on '.date("dS F Y / h:i:s A");		
		$obj->from 	= 'camlis.noreply@gmail.com';		
		$obj->to 	= array('cdcmohcam@gmail.com','iengvanra@gmail.com');//$row['email'];
		$obj->cc 	= 'vuthysin5284@hotmail.com';//$row['email']; 
		$obj->name 	= "រកឃើញនូវប្រភេទមេរោគ";//$row['name'];
		self::emaillist($obj); 
	}
	/*
	 * every weekly send email a time
	 * criteria while meet three or more than three Diseases send email
	 */
	public function email_weekly($obj){ 
		// get body message​
		/*$obj->message =	"ទូរស័ព្ទទៅកាន់ - Calling to<br />";
		$obj->message.=	"នាយកដ្នានប្រយុទ្ធនឹងជំងឺឆ្លង - Department of Diseases<br />";
		$obj->message.=	"<h2>115</h2>";
		$obj->message.=	"<strong>+855 12 588 981</strong><br />";
		$obj->message.=	"<strong>+855 89 669 567</strong><br /><br /><br />";
		$obj->message.=	"រាយការណ៌ប្រចាំងសប្តាហ៌ System បានរកឃើញនូវប្រភេទមេរោគ - news Weekly diseases found<br />";
		$obj->message.= "<strong>
							<ul>".$obj->diseases."</ul>
						</strong>
						<br />";
		$obj->message.=	"ដោយមានជំនួយពីក្រុមការងារបច្ចេកទេស AMR<br />";
	  
		//
		$obj->title = "រកឃើញនូវប្រភេទមេរោគ ប្រចាំងសប្តាហ៌";		*/
		
		$obj->message =	$this->template_weekly($obj);
		$obj->title = 'CamLIS '.$obj->number.' was identified at '.$this->session->userdata["laboratory"]->name_en.' on '.date("dS F Y / h:i:s A");	
		$obj->from 	= 'camlis.noreply@gmail.com';		
		$obj->to 	= array('iengvanra@gmail.com');//,'cdcmohcam@gmail.com','vuthysin5284@hotmail.com'$row['email']; 
		$obj->cc 	= 'vuthysin5284@hotmail.com';//$row['email']; 
		$obj->name 	= "រកឃើញនូវប្រភេទមេរោគ ប្រចាំងសប្តាហ៌";	//$row['name'];
		self::emaillist($obj); 
	}
	
	/***bacteriology email****/
	function emaillist($obj)
	{  
		$config = array();
        $config['useragent']	= "CodeIgniter";
        $config['protocol']		= "smtp";   
		$config['smtp_host']	= 'mail.aaiischool.com'; 
		$config['smtp_user'] 	= 'vuthy.sin@aaiischool.com';
		$config['smtp_pass'] 	= 'sinvuthy5284';
        $config['smtp_port']	= 225;
		
        $config['mailtype']		= 'html';
        $config['charset']		= 'utf-8'; 
        $config['newline']		= "\r\n"; 
		$this->load->library('email');
		$this->email->initialize($config);
		//$this->email->set_newline("\r\n");
		
		
		
		$this->email->from($obj->from, $obj->name); 
		$this->email->to($obj->to);
		$this->email->cc($obj->cc);  
		$email_msg		=	$obj->message."<div style='clear:both'></div><br /><br /><br />";
		$email_msg		.=	"<br />";
		//$email_msg		.=	"System regarding,<div style='clear:both'><br /><br /><br /><br />"; 
		  
		$this->email->subject($obj->title);
		$this->email->message($email_msg); 
		$ok = $this->email->send();
  		//print_r($this->email->print_debugger());
		return $ok; 
	}
	
	//
	function template($obj){
		$html = '
			<style>.MsoNormal {margin: 0;}p {display: block;-webkit-margin-before: 1em;-webkit-margin-after: 1em;-webkit-margin-start: 0px;			-webkit-margin-end: 0px;}u, ins {text-decoration: underline;}.ii a[href] {color: #15c;}
			</style>
			<table style="width:100%;border-collapse:collapse;margin-left:6.75pt;margin-right:6.75pt" width="100%" cellspacing="0" cellpadding="0" border="0" align="left">
			<tbody>
				<tr style="height:3.95pt">
					<td colspan="3" style="width:99.26%;border:solid #339933 1.5pt;background:#339933;padding:5.65pt 5.4pt 5.65pt 5.4pt;height:3.95pt" width="99%" valign="top">
						<p class="MsoNormal" style="text-align:center" align="center">
						<b>
							<span style="font-size:18.0pt;color:white">CamLIS '.$obj->inscress_id.' - <u>'.$obj->diseases.'</u> was identified at <u>'.$this->session->userdata["laboratory"]->name_en.'</u> on <u>'.date("dS F Y / h:i:s A").'</u></span>
						</b>
						<u></u>
						<u></u>
						</p>
			</td>
					<td style="width:.74%;padding:0cm 0cm 0cm 0cm;height:3.95pt" width="0%"><p class="MsoNormal">&nbsp;<u></u><u></u></p> 
				</td>
				</tr>
				<tr style="height:1.6pt">
					<td colspan="3" style="width:99.26%;border:solid #a3e1a3 1.5pt;border-top:none;background:#a3e1a3;padding:5.65pt 5.4pt 5.65pt 5.4pt;height:1.6pt" width="99%" valign="top"><p class="MsoNormal" style="margin-left:2.25pt;text-align:center" align="center"><b><span style="color:black">Priority Pathogens To Be Reported</span><u></u><u></u></b></p>
					</td>
					<td style="width:.74%;padding:0cm 0cm 0cm 0cm;height:1.6pt" width="0%"><p class="MsoNormal">&nbsp;<u></u><u></u></p>
					</td>
				</tr>
				<tr style="height:1.6pt">
					<td style="width:3.12%;border:solid #a3e1a3 1.5pt;border-top:none;background:#a3e1a3;padding:5.65pt 5.4pt 5.65pt 5.4pt;height:1.6pt" width="3%" valign="top"><p class="MsoNormal" style="margin-left:2.25pt;text-align:center" align="center"><b><span style="font-size:10.0pt;color:#595959">&nbsp;</span></b><u></u><u></u></p>
					</td>
					<td style="width:93.48%;border:none;border-right:solid #a3e1a3 1.5pt;background:white;padding:0cm 0cm 0cm 0cm;height:1.6pt" width="93%" valign="top"><p class="MsoNormal">&nbsp;<u></u><u></u></p>
						<table style="width:100.0%;border-collapse:collapse" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr>
									<td style="width:100.0%;padding:0cm 14.2pt 0cm 14.2pt" width="100%" valign="top">
									<table style="border-collapse:collapse" cellspacing="0" cellpadding="0" border="0">
										<tbody>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top">
													<p class="MsoNormal">
														<b><span style="font-size:10.0pt;color:#1f497d">Pathogen</span></b>
														<b><span style="font-size:10.0pt;color:#595959">:</span></b>
														<u></u>
														<u></u>
													</p>
												</td>
												<td style="width:489.05pt;border:solid #339933 1.5pt;border-left:none;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top">
													<p class="MsoNormal">'.$obj->diseases.'<u></u><u></u></p>
												</td>
											</tr>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top"><p class="MsoNormal"><b><span style="font-size:10.0pt;color:#1f497d">Date Identified</span></b><b><span style="font-size:10.0pt;color:#595959">:</span></b><u></u><u></u></p>
												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top">
													<p class="MsoNormal">'.date("l, dS F Y / h:i:s A e").'<u></u>
													<u></u>
													</p>
												</td>
											</tr>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top"><p class="MsoNormal"><b><span style="font-size:10.0pt;color:#1f497d">Laboratory:<u></u><u></u></span></b></p>
												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top">
												<p class="MsoNormal">'.$this->session->userdata["laboratory"]->name_en.'<u></u>
												<u></u>
												</p>
												</td>
											</tr>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top"><p class="MsoNormal"><b><span style="font-size:10.0pt;color:#1f497d">Patient ID:<u></u><u></u></span></b></p>												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top"><p class="MsoNormal">'.$obj->patient_id.'<u></u><u></u></p>
												</td>
											</tr>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top"><p class="MsoNormal"><b><span style="font-size:10.0pt;color:#1f497d">Age:<u></u><u></u></span></b></p>
												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top"><p class="MsoNormal">'.$obj->_year.' y<u></u><u></u></p>
												</td>
											</tr>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top"><p class="MsoNormal"><b><span style="font-size:10.0pt;color:#1f497d">Sex:<u></u><u></u></span></b></p>
												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top"><p class="MsoNormal">'.($obj->sex=='F'?"Female":"Male").'<u></u><u></u></p>
												</td>
											</tr>
											<!--tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top">
													<p class="MsoNormal">
														<b><span style="font-size:10.0pt;color:#1f497d">Address:
														<u></u>
														<u></u></span></b>
													</p>
												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top">
													<p class="MsoNormal">'.$this->session->userdata["laboratory"]->address_en.'<u></u>
														<u></u>
													 </p>
												</td>
											</tr-->
									   </tbody>
								   </table>
								   
									<p class="MsoNormal">&nbsp;
									   <span style="color:black">&nbsp;</span>
									   <u></u>
									   <u></u>
									</p>
									<p class="MsoNormal">
										<i><span style="font-size:10.0pt">Please do not reply to this message</span></i>
										<u></u>
										<u></u>
									</p>
									
								</td>
							</tr>
						</tbody>
					</table>
					</td>
					<td style="width:2.66%;border-top:none;border-left:none;border-bottom:solid #a3e1a3 1.5pt;border-right:solid #a3e1a3 1.5pt;background:#a3e1a3;padding:0cm 0cm 0cm 0cm;height:1.6pt" width="2%" valign="top">
						<p class="MsoNormal" style="margin-left:2.25pt;text-align:center" align="center">
							<b><span style="font-size:10.0pt;color:#595959">&nbsp;</span></b>
							<u></u>
							<u></u>
						</p>
					</td>
					<td><p class="MsoNormal">&nbsp;<u></u><u></u></p>
					</td>
				</tr><tr style="height:22.7pt">
					<td colspan="4" style="width:100.0%;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt;height:22.7pt" width="100%">
						<table style="width:98.62%;border-collapse:collapse" width="98%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr style="height:20.55pt">
									<td style="width:50.0%;padding:2.85pt 5.4pt 2.85pt 5.4pt;height:20.55pt" width="50%" valign="top">
										<p class="MsoNormal" style="text-align:right" align="right">
											<b><span style="font-size:10.0pt;color:#595959">Contact</span></b>
											<b><span style="font-size:10.0pt;color:#1f497d">:</span></b>
											<b><span style="font-size:8.0pt;color:#595959"><br>Phone: </span></b>
											<u></u>
											<u></u>
										</p>
										<p class="MsoNormal" style="text-align:right" align="right">
											<b><span style="font-size:10.0pt;color:#595959">Email:</span></b>
												<span style="font-size:10.0pt;color:#595959"> </span>
												<u></u><u></u>
										</p>
									</td>
									<td style="width:50.0%;padding:2.85pt 5.4pt 2.85pt 5.4pt;height:20.55pt" width="50%" valign="top">
										<p class="MsoNormal">
											<b><span style="font-size:10.0pt;color:#595959">Dr Sau Sokunna</span></b>
											<u></u>
											<u></u>
										</p>
										<p class="MsoNormal">
											<span style="font-size:10.0pt;color:#595959">
												<a href="tel:012%20920%20480" value="+85512920480" target="_blank">012 920 480</a>
												<u></u>
												<u></u>
											</span>
										</p>
										<p class="MsoNormal">
											<span style="font-size:10.0pt;color:#595959">
												<a href="mailto:kunnasau@gmail.com" target="_blank">kunnasau@gmail.com</a>
												<u></u>
												<u></u>
											</span>
										 </p>
									</td>
								</tr>
							</tbody>
						</table>
						
							<p class="MsoNormal" style="text-align:center" align="center">
								<span style="font-size:10.0pt;color:#7f7f7f">Cambodia Laboratory Information System (CamLIS)– Internal use only</span>
								<u></u>
								<u></u>
							</p>
						
					</td>
				</tr> 
				</tbody>
			</table>
		';
		
		return $html;
	}
	function template_weekly($obj){
		$html = '
			<style>.MsoNormal {margin: 0;}p {display: block;-webkit-margin-before: 1em;-webkit-margin-after: 1em;-webkit-margin-start: 0px;			-webkit-margin-end: 0px;}u, ins {text-decoration: underline;}.ii a[href] {color: #15c;}
			</style>
			<table style="width:100%;border-collapse:collapse;margin-left:6.75pt;margin-right:6.75pt" width="100%" cellspacing="0" cellpadding="0" border="0" align="left">
			<tbody>
				<tr style="height:3.95pt">
					<td colspan="3" style="width:99.26%;border:solid #339933 1.5pt;background:#339933;padding:5.65pt 5.4pt 5.65pt 5.4pt;height:3.95pt" width="99%" valign="top">
						<p class="MsoNormal" style="text-align:center" align="center">
						<b>
							<span style="font-size:18.0pt;color:white">CamLIS <strong><ul>'.$obj->diseases.'</ul></strong> was identified at <u>'.$obj->labo_name.'</u> on <u>'.date("dS F Y / h:i:s A").'</u></span>
							
						</b>
						<u></u>
						<u></u>
						</p>
			</td>
					<td style="width:.74%;padding:0cm 0cm 0cm 0cm;height:3.95pt" width="0%"><p class="MsoNormal">&nbsp;<u></u><u></u></p> 
				</td>
				</tr>
				<tr style="height:1.6pt">
					<td colspan="3" style="width:99.26%;border:solid #a3e1a3 1.5pt;border-top:none;background:#a3e1a3;padding:5.65pt 5.4pt 5.65pt 5.4pt;height:1.6pt" width="99%" valign="top"><p class="MsoNormal" style="margin-left:2.25pt;text-align:center" align="center"><b><span style="color:black">Priority Pathogens To Be Reported</span><u></u><u></u></b></p>
					</td>
					<td style="width:.74%;padding:0cm 0cm 0cm 0cm;height:1.6pt" width="0%"><p class="MsoNormal">&nbsp;<u></u><u></u></p>
					</td>
				</tr>
				<tr style="height:1.6pt">
					<td style="width:3.12%;border:solid #a3e1a3 1.5pt;border-top:none;background:#a3e1a3;padding:5.65pt 5.4pt 5.65pt 5.4pt;height:1.6pt" width="3%" valign="top"><p class="MsoNormal" style="margin-left:2.25pt;text-align:center" align="center"><b><span style="font-size:10.0pt;color:#595959">&nbsp;</span></b><u></u><u></u></p>
					</td>
					<td style="width:93.48%;border:none;border-right:solid #a3e1a3 1.5pt;background:white;padding:0cm 0cm 0cm 0cm;height:1.6pt" width="93%" valign="top"><p class="MsoNormal">&nbsp;<u></u><u></u></p>
						<table style="width:100.0%;border-collapse:collapse" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr>
									<td style="width:100.0%;padding:0cm 14.2pt 0cm 14.2pt" width="100%" valign="top">
									<table style="border-collapse:collapse" cellspacing="0" cellpadding="0" border="0">
										<tbody>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top">
													<p class="MsoNormal">
														<b><span style="font-size:10.0pt;color:#1f497d">Pathogen</span></b>
														<b><span style="font-size:10.0pt;color:#595959">:</span></b>
														<u></u>
														<u></u>
													</p>
												</td>
												<td style="width:489.05pt;border:solid #339933 1.5pt;border-left:none;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top">
													<p class="MsoNormal">'.$obj->diseases.'<u></u><u></u></p>
												</td>
											</tr>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top"><p class="MsoNormal"><b><span style="font-size:10.0pt;color:#1f497d">Date Identified</span></b><b><span style="font-size:10.0pt;color:#595959">:</span></b><u></u><u></u></p>
												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top">
													<p class="MsoNormal">'.date("l, dS F Y / h:i:s A e").'<u></u>
													<u></u>
													</p>
												</td>
											</tr>
											<tr>
												<td style="width:93.65pt;border:solid #339933 1.5pt;border-top:none;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt" width="125" valign="top"><p class="MsoNormal"><b><span style="font-size:10.0pt;color:#1f497d">Laboratory:<u></u><u></u></span></b></p>
												</td>
												<td style="width:489.05pt;border-top:none;border-left:none;border-bottom:solid #339933 1.5pt;border-right:solid #339933 1.5pt;padding:0cm 5.4pt 0cm 5.4pt" width="652" valign="top">
												<p class="MsoNormal">'.$obj->labo_name.'<u></u>
												<u></u>
												</p>
												</td>
											</tr>
											  
									   </tbody>
								   </table>
								   
									<p class="MsoNormal">&nbsp;
									   <span style="color:black">&nbsp;</span>
									   <u></u>
									   <u></u>
									</p>
									<p class="MsoNormal">
										<i><span style="font-size:10.0pt">Please do not reply to this message</span></i>
										<u></u>
										<u></u>
									</p>
									
								</td>
							</tr>
						</tbody>
					</table>
					</td>
					<td style="width:2.66%;border-top:none;border-left:none;border-bottom:solid #a3e1a3 1.5pt;border-right:solid #a3e1a3 1.5pt;background:#a3e1a3;padding:0cm 0cm 0cm 0cm;height:1.6pt" width="2%" valign="top">
						<p class="MsoNormal" style="margin-left:2.25pt;text-align:center" align="center">
							<b><span style="font-size:10.0pt;color:#595959">&nbsp;</span></b>
							<u></u>
							<u></u>
						</p>
					</td>
					<td><p class="MsoNormal">&nbsp;<u></u><u></u></p>
					</td>
				</tr><tr style="height:22.7pt">
					<td colspan="4" style="width:100.0%;background:#a3e1a3;padding:0cm 5.4pt 0cm 5.4pt;height:22.7pt" width="100%">
						<table style="width:98.62%;border-collapse:collapse" width="98%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr style="height:20.55pt">
									<td style="width:50.0%;padding:2.85pt 5.4pt 2.85pt 5.4pt;height:20.55pt" width="50%" valign="top">
										<p class="MsoNormal" style="text-align:right" align="right">
											<b><span style="font-size:10.0pt;color:#595959">Contact</span></b>
											<b><span style="font-size:10.0pt;color:#1f497d">:</span></b>
											<b><span style="font-size:8.0pt;color:#595959"><br>Phone: </span></b>
											<u></u>
											<u></u>
										</p>
										<p class="MsoNormal" style="text-align:right" align="right">
											<b><span style="font-size:10.0pt;color:#595959">Email:</span></b>
												<span style="font-size:10.0pt;color:#595959"> </span>
												<u></u><u></u>
										</p>
									</td>
									<td style="width:50.0%;padding:2.85pt 5.4pt 2.85pt 5.4pt;height:20.55pt" width="50%" valign="top">
										<p class="MsoNormal">
											<b><span style="font-size:10.0pt;color:#595959">Dr Sau Sokunna</span></b>
											<u></u>
											<u></u>
										</p>
										<p class="MsoNormal">
											<span style="font-size:10.0pt;color:#595959">
												<a href="tel:012%20920%20480" value="+85512920480" target="_blank">012 920 480</a>
												<u></u>
												<u></u>
											</span>
										</p>
										<p class="MsoNormal">
											<span style="font-size:10.0pt;color:#595959">
												<a href="mailto:kunnasau@gmail.com" target="_blank">kunnasau@gmail.com</a>
												<u></u>
												<u></u>
											</span>
										 </p>
									</td>
								</tr>
							</tbody>
						</table>
						
							<p class="MsoNormal" style="text-align:center" align="center">
								<span style="font-size:10.0pt;color:#7f7f7f">Cambodia Laboratory Information System (CamLIS)– Internal use only</span>
								<u></u>
								<u></u>
							</p>
						
					</td>
				</tr> 
				</tbody>
			</table>
		';
		
		return $html;
	}
	
	
}
?>