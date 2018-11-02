<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart extends CI_Controller {
    function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct();
		$this->load->database();


    }

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	// getFullSetting : get full setting of unit
    public function getFullSetting($unitId){
		
		$setting=$this->db->query("SELECT * FROM `units` LEFT JOIN `unitsetting` ON `unitsetting`.`unit`=`units`.`unitId` LEFT JOIN `products` ON `unitsetting`.`productType`=`products`.`productId` WHERE `units`.`unitId` =$unitId ")->result()[0];
		if($setting->productId=='4'){
			$setting->productMeanValue=75;
		}else if($setting->productId =='2'){
			$setting->productMeanValue=83;
		}
		echo json_encode($setting);	
		
	}
	// accGaugeList : list all gauge list of a unit
	private function accGaugeList($unitId){
		$accGauge=$this->db->get_where('gauges',array("masterUnit"=>$unitId))->result();
		$accGaugeList=[];
		foreach( $accGauge as $row){
			array_push($accGaugeList,$row->gaugeDevId);
		}
		return $accGaugeList;
	}
	//getRecords : get massive last data of specfic unit
	public function getRecords($unitId){
		
		$accGaugeList=$this->accGaugeList($unitId);
		
		//$Result=$this->db->query("SELECT * FROM `m1_all` WHERE `gaugeDev` IN ? ORDER BY `m1_all`.`Id` DESC LIMIT 1,30")->result();
   
		$query=$this->db->where_in('gaugeDev',$accGaugeList)->order_by('Id DESC')->limit(30, 2)->get('m1_all')->result();
		echo json_encode($query);
	}

	//getUpdatedRecords : get last records update for timeline chart
	public function getUpdatedRecords($unitId){
		$accGaugeList=$this->accGaugeList($unitId);
		$num = $this->input->post('limit');
		//$num=39210; test value

		$Result=$this->db->query("SELECT * FROM `m1_all` WHERE `gaugeDev` IN ? AND `Id` > $num   ",array($accGaugeList))->result();
		
		echo json_encode($Result);
   		




	}
}
