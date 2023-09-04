<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// GoData
class Restservice {
	//private $url			= 'http://110.74.218.40:9009/api';
	private $url			= 'http://110.74.218.40:2020/api';
	private $outbreak_id 	= '747494dc-fdbc-4c68-96c4-7f8ad79f896f'; // national
	function login($username , $password) {
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL				 	=> $this->url.'/oauth/token',
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'POST',
			CURLOPT_POSTFIELDS 			=> '{  
				"username": "'.$username.'",  
				"password": "'.$password.'"  
			  }',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	/*
	 * Count cases of outbreak
	 */
	
	function getTotalCases($token,$outbreak_id){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/count?access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}


	/*
	 * Queries people of outbreak
	 * 
	 */

	function getPeopleOfOutbreak($token, $outbreak_id){
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/people?access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	/*
	 * Query cases of outbreak 
	 * GET /outbreaks/{id}/cases
	 * 
	 * */
	
	function getCasesOfOutbreak($token , $outbreak_id){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 					=> $this->url.'/outbreaks/'.$outbreak_id.'/cases?access_token='.$token,			
			CURLOPT_RETURNTRANSFER 			=> true,
			CURLOPT_ENCODING 				=> '',
			CURLOPT_MAXREDIRS 				=> 10,
			CURLOPT_TIMEOUT 				=> 0,
			CURLOPT_FOLLOWLOCATION 			=> true,
			CURLOPT_HTTP_VERSION 			=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 			=> 'GET',
			CURLOPT_HTTPHEADER 				=> array(
				'Accept: application/json'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		
		return json_decode($response, true);
	}
	/*
	 * Get list of outbreak
	 */
	function getOutbreak($token){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks?access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	/**----------------------------------- EVENT---------------------------------- */
	// Get List of the event
	// Get detail of the event
	// /outbreaks/{id}/events/{fk} 
	public function getDetailOfEvent($token,$outbreak_id, $event_id){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/events/'.$event_id.'?access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	/**
	 * Counts the number of cases grouped by case classification
	 * Sample: 
	 */

	function getNumberCasesByClassification($token , $outbreak_id , $fromDate = null , $toDate = null){
		$curl = curl_init();
		$filer = '';
		if($fromDate !== null || $toDate !== null){
			$filer = 'filter={"where":
								{"and":[
									{"createdAt":{"between":["'.$fromDate.'T00:00:00.000Z","'.$toDate.'T23:59:59.999Z"]}},
									{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}
								]},
								"include":[
									{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},
									{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},
									{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}
								]
							}&';
		}
		
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/per-classification/count?'.$filer.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}

	

	/** 
	 * Number of Sample
	 * Counts the lab results that pass a filer
	 * API: GET /outbreaks/{id}/lab-results/aggregate-filtered-count
	 */
	function getNumberOfSample($token, $outbreak_id){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/lab-results/aggregate-filtered-count?access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	// added 25 Jan 2021
	function getGenderByClassfication($token , $outbreak_id ,$class, $gender){

		$sex 	= ($gender == 'm') ? 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE': 'LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE';
		$filter = 'filter={"where":{"and":[{"classification":"'.$class.'"},{"gender":{"inq":["'.$sex.'"]}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}';
		//$filter = 'filter={"where":{"and":[{"classification":"'.$class.'"},{"gender":{"inq":["'.$sex.'"]}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}';
		$curl 	= curl_init();
		curl_setopt_array($curl, array(			
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/per-classification/count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	function getClassWithGender($token , $outbreak_id , $gender = null , $fromDate = null , $toDate = null){
		$curl = curl_init();
		$filter = "";
		$dateQuery = '';
		//
		$sex = strtolower($gender) == 'm' ? 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE' : 'LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE';
		if($fromDate !== null || $toDate !== null){
			$dateQuery = '{"createdAt":{"between":["'.$fromDate.'T00:00:00.000Z","'.$toDate.'T23:59:59.999Z"]}},';
		}		
		if($gender !== null){
			/*
		 	$filter = 'filter={"where":
								{"and":[
									{"gender":{"inq":["LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE"]}},
									{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}
								]},
								"include":[
									{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},
									{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},
									{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}
								]}&';
								*/
			$filter = 'filter={"where":{"and":[{"gender":{"inq":["'.$sex.'"]}},'.$dateQuery.'{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}&';			
		}
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/per-classification/count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	// only Outbreak Covdid
	// 11 Feb 2021
	public function getDischargedByClass($token , $outbreak_id , $class , $gender = null , $fromDate = null , $toDate = null){
		$curl = curl_init();
		$filter = "";
		$dateQuery = "";
		$sex = strtolower($gender) == 'm' ? 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE' : 'LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE';	
		$today = date('Y-m-d');	
		if($fromDate !== null || $toDate !== null){
			$dateQuery = '{"createdAt":{"between":["'.$fromDate.'T00:00:00.000Z","'.$toDate.'T23:59:59.999Z"]}},';
		}
		if($gender !== null){			
			//$filter = 'filter={"where":{"and":[{"gender":{"inq":["'.$sex.'"]}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}&';
			//$filter = 'filter={"where":{"and":[{"dateRanges":{"elemMatch":{"typeId":"LNG_REFERENCE_DATA_CATEGORY_PERSON_DATE_TYPE_HOSPITALIZATION","startDate":{"$lte":"'.$today.'T23:59:59.999Z"},"$or":[{"endDate":{"$eq":null}},{"endDate":{"$gte":"'.$today.'T23:59:59.999Z"}}]}}},{"and":[{"dateOfOnset":{"lte":"'.$today.'T23:59:59.000Z"}}]},{"gender":{"inq":["'.$sex.'"]}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}&';
			//$filter = 'filter={"where":{"and":[{"dateRanges":{"not":{"$elemMatch":{"typeId":"LNG_REFERENCE_DATA_CATEGORY_PERSON_DATE_TYPE_HOSPITALIZATION","startDate":{"$lte":"'.$today.'T23:59:59.999Z"},"$or":[{"endDate":{"$eq":null}},{"endDate":{"$gte":"'.$today.'T23:59:59.999Z"}}]}}}},{"and":[{"dateOfOnset":{"lte":"'.$today.'T23:59:59.000Z"}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]}]}}&';
			$filter = 'filter={"where":{"and":[{"dateRanges":{"not":{"$elemMatch":{"typeId":"LNG_REFERENCE_DATA_CATEGORY_PERSON_DATE_TYPE_HOSPITALIZATION","startDate":{"$lte":"'.$today.'T23:59:59.999Z"},"$or":[{"endDate":{"$eq":null}},{"endDate":{"$gte":"'.$today.'T23:59:59.999Z"}}]}}}},{"and":[{"dateOfOnset":{"lte":"'.$today.'T23:59:59.000Z"}}]},{"gender":{"inq":["'.$sex.'"]}},'.$dateQuery.'{"classification":{"inq":["'.$class.'"]}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}&';
		}
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/filtered-count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	public function getLocations($token , $outbreak_id){
		$curl = curl_init();			
		$today = date('Y-m-d');
		$filter = 'filter={"where":{"and":[{"dateOfOnset":{"lte":"'.$today.'T23:59:59.000Z"}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]}}&';
		
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/per-location-level/count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}

	public function getGenderByLocation($token , $outbreak_id , $id_location , $gender = null){
		$curl = curl_init();
		$filter = "";		
		//{"classification":"'.$class.'"}
		$sex = strtolower($gender) == 'm' ? 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE' : 'LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE';	
		$today = date('Y-m-d');	
		if($gender !== null){
			$filter = 'filter={"where":{"and":[{"addresses":{"elemMatch":{"typeId":"LNG_REFERENCE_DATA_CATEGORY_ADDRESS_TYPE_USUAL_PLACE_OF_RESIDENCE","parentLocationIdFilter":{"$in":["'.$id_location.'"]}}}},{"dateOfOnset":{"lte":"2021-02-11T23:59:59.000Z"}},{"gender":{"inq":["'.$sex.'"]}},{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}&';
		}
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/filtered-count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	public function getHighRisk($token , $outbreak_id , $gender , $followupOption = null){
		$curl = curl_init();
		$filter = "";		
		$followup = "";
		$sex = strtolower($gender) == 'm' ? 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE' : 'LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE';
		if($followupOption !== null){
			$followup = '{"followUp.status":{"inq":["LNG_REFERENCE_DATA_CONTACT_FINAL_FOLLOW_UP_STATUS_TYPE_UNDER_FOLLOW_UP"]}},';			
		}
		
		$filter = 'filter={"where":{"and":[{"riskLevel":{"inq":["LNG_REFERENCE_DATA_CATEGORY_RISK_LEVEL_3_HIGH"]}},'.$followup.'{"gender":{"inq":["'.$sex.'"]}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}&';
				
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/contacts/filtered-count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	public function getClassByLab($token , $outbreak_id , $lab_name, $class){
		$curl 		= curl_init();	
		$filter 	= 'filter={"where":{"and":[{"labName":{"inq":["'.$lab_name.'"]}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"person","scope":{"where":{"and":[{"classification":{"inq":["'.$class.'"]}}]},"filterParent":true,"justFilter":true}}]}&';
				
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/lab-results/aggregate-filtered-count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
	public function getNationality($token , $outbreak_id){
		$curl 		= curl_init();
		$filter 	= 'filter={"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}}],"limit":25,"skip":0}&';
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/lab-results/aggregate?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}

	public function getGenderByLocationAndClass($token , $outbreak_id , $id_location , $gender = null, $class){
		$curl = curl_init();
		$filter = "";		
		if($class !== null){
			$classQuery = '{"classification":"'.$class.'"},';
		}
		//
		$sex = strtolower($gender) == 'm' ? 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE' : 'LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE';	
		$today = date('Y-m-d');	
		if($gender !== null){
			$filter = 'filter={"where":{"and":[{"addresses":{"elemMatch":{"typeId":"LNG_REFERENCE_DATA_CATEGORY_ADDRESS_TYPE_USUAL_PLACE_OF_RESIDENCE","parentLocationIdFilter":{"$in":["'.$id_location.'"]}}}},{"dateOfOnset":{"lte":"2021-02-11T23:59:59.000Z"}},{"gender":{"inq":["'.$sex.'"]}},'.$classQuery.'{"classification":{"neq":"LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED"}}]},"include":[{"relation":"createdByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"updatedByUser","scope":{"filterParent":false,"justFilter":false}},{"relation":"locations","scope":{"filterParent":false,"justFilter":false}}]}&';
		}
		curl_setopt_array($curl, array(
			CURLOPT_URL 				=> $this->url.'/outbreaks/'.$outbreak_id.'/cases/filtered-count?'.$filter.'access_token='.$token,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_ENCODING 			=> '',
			CURLOPT_MAXREDIRS 			=> 10,
			CURLOPT_TIMEOUT 			=> 0,
			CURLOPT_FOLLOWLOCATION 		=> true,
			CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 		=> 'GET',
			CURLOPT_HTTPHEADER 			=> array(
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}
}

