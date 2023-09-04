<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tat extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	private function cells($number) {
        return PHPExcel_Cell::stringFromColumnIndex($number);
    }

	public function generate_to_excel()
	{
		$this->load->model('tat_model', 'tat');
        $this->load->library('phptoexcel');

        $conditon = array(
            'start_date' => date("Y-m-d", strtotime(str_replace('/', '-', $this->input->post('start_date')))),
            'end_date' => date("Y-m-d", strtotime(str_replace('/', '-', $this->input->post('end_date')))),
            'group_result' => $this->input->post('group_result') 
        );

        $fileName = 'TAT-REPORT-2';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()
        ->getPageSetup()
        ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        //Header
        $objPHPExcel->getActiveSheet()
        ->mergeCells('A1:F1')
        ->setCellValue('A1','TAT REPORT 2')
        ->getStyle('A1')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()
        ->mergeCells('A2:F2')
        ->setCellValue('A2', 'Laboratory:');

        /*title header*/
        $objPHPExcel->getActiveSheet()
        ->mergeCells('A3:A4')
        ->setCellValue('A3', 'No')
        ->setCellValue('B3', 'LabID')
        ->mergeCells('B3:B4');

        /*get the name of group result*/
        $group_results = $this->tat->get_group_result_by_id($conditon['group_result']);
        /*counting the number of the group result*/
        $number_of_group_results = count($group_results);
        /*tats collection*/
        $tats = collect($this->tat->get_tat_excel($conditon));
        if ($tats->count() > 0) {
            $objPHPExcel->getActiveSheet()
            ->mergeCells("C3:" . $this->cells($number_of_group_results + 1). "3")
            ->setCellValue('C3', 'Test Name')
            ->getStyle('C3')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()
            ->mergeCells($this->cells($number_of_group_results + 2) . "3:" . $this->cells($number_of_group_results + 2) . "4")
            ->setCellValue($this->cells($number_of_group_results + 2). "3", 'Collection date/time')
            ->mergeCells($this->cells($number_of_group_results + 3) . "3:" . $this->cells($number_of_group_results + 3) . "4")
            ->setCellValue($this->cells($number_of_group_results + 3). "3", 'Receive date/time')
            ->mergeCells($this->cells($number_of_group_results + 4) . "3:" . $this->cells($number_of_group_results + 4) . "4")
            ->setCellValue($this->cells($number_of_group_results + 4). "3", 'Print date/time');

            /*group results title*/
            $columns = 2;
            $tbl_group_result = array();//28102021
            foreach ($group_results as $group_result) {
                $objPHPExcel->getActiveSheet()
                ->setCellValue($this->cells($columns). "4", $group_result->group_result);
                $tbl_group_result[$group_result->group_result] = $columns;//28102021
                $columns++;
            }
            /*group result data rows*/
            $rows = 5;
            $tats->groupBy('sample_number')->each(function($items, $key) use ($number_of_group_results, &$results, $objPHPExcel, &$rows, $tbl_group_result){
                $columns = 2;
                foreach ($items as $item) {
                    $objPHPExcel->getActiveSheet()
                    ->setCellValue($this->cells(0). $rows, $rows - 4);
                    if (isset($item->sample_number)) {
                        $objPHPExcel->getActiveSheet()
                        ->setCellValue($this->cells(1). $rows, $item->sample_number);
                    }
                    $columns = $tbl_group_result[$item->group_result];//28102021
                    $objPHPExcel->getActiveSheet()
                    ->setCellValue($this->cells($columns). $rows, (isset($item->total)) ? $item->total : '')
                    ->getStyle($this->cells($columns). $rows)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    if (isset($item->collected_date)) {
                        $objPHPExcel->getActiveSheet()                        
                        ->setCellValue($this->cells($number_of_group_results + 2). $rows, str_replace(['"', '{', '}'], '', $item->collected_date))
                        ->setCellValue($this->cells($number_of_group_results + 3). $rows, str_replace(['"', '{', '}'], '', $item->received_date))
                        ->setCellValue($this->cells($number_of_group_results + 4). $rows, $item->printedDate);
                    }
                    $columns++;  
                }
                $rows++;
            });    
        }
        
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
	}
}