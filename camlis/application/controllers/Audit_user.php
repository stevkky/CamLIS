<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit_user extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('audit_user_model', 'audit_user');
	}

	public function report($start_date, $start_time, $end_date, $end_time, $type = 'preview', $labs = null)
	{

        $laboratories = $this->input->post('laboratories');
		$start_date =  $start_date.' '.$start_time;
		$end_date = $end_date.' '.$end_time;

		$this->data['start_date'] = $start_date;
    	$this->data['end_date']   = $end_date;
    	$this->data['type']   = $type;

		if ($type == "preview") {
			$this->data['audits'] = $this->audit_user->audit_user_report($laboratories, $start_date, $end_date);
			$result = [];
			if ($this->input->server('REQUEST_METHOD') == 'POST') {
                $result[] = ['template' => $this->load->view('template/print/audit_report.php', $this->data, TRUE)];
            } else {
                $this->load->view('template/print/audit_report.php', $this->data);
            }
            if ($this->input->server('REQUEST_METHOD') == 'POST') echo json_encode($result);
		}

        if ($type == "print") {
            $laboratories = explode('-', $labs);
            $this->data['audits'] = $this->audit_user->audit_user_report($laboratories, $start_date, $end_date);
            $this->load->view('template/print/audit_report.php', $this->data);
        }

		if ($type == "excel") {
			$this->load->library('phptoexcel');
			$fileName = 'audit-trail-user-report';
			$objPHPExcel = new PHPExcel();
			//Style
            $default_style = array(
                'font' => array(
                    'name' => 'Verdana',
                    'color' => array('rgb' => '000000'),
                    'size' => 11
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'borders' => array(
                	'outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                	'inside' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                )
            );

            $odd_row_style = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'CCCCCC')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'AAAAAA')
                    )
                )
            );

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->setShowGridlines(true);
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray($default_style);
            //Header
            $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1','Audit Trial User');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($default_style);
            $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
            $objPHPExcel->getActiveSheet()->setCellValue('A2',$start_date.' to '.$end_date);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()
            ->setCellValue('A3', 'No')
            ->setCellValue('B3', 'User name')
            ->setCellValue('C3', 'User role')
            ->setCellValue('D3', 'Lab name')
            ->setCellValue('E3', 'IP address')
            ->setCellValue('F3', 'Date');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($default_style);
            $objPHPExcel->getActiveSheet()->setTitle($fileName);

            $audits = $this->audit_user->audit_user_report($laboratories, $start_date, $end_date);
            $name = 'name_'.strtolower($this->app_language->app_lang());
            $i = 4;
            foreach ($audits as $audit) {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $i - 3);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $audit->fullname);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $audit->definition);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $audit->$name);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $audit->ip_address);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $audit->timestamp);
                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'F'.$i)->applyFromArray($default_style);
                $i++;
            }
            /*30-11-2018*/
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            /*end 30-11-2018*/
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
		}
	}
}