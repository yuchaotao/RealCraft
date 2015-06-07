<?php

class Config extends CI_CONTROLLER{
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper('security');
        $this->load->model('construction');
        $this->load->model('resourceBase');
    }

    //  上海交通大学的GPS坐标范围
    private $GPS_RANGE = array('leftUp'=>array('longitude'=> 121.429462, 'latitude'=>31.033725), 
                      'leftDown'=>array('longitude'=> 121.434313, 'latitude'=>31.022801),
                      'rightUp'=>array('longitude'=> 121.462053, 'latitude'=>31.044276),
                      'rightDown'=>array('longitude'=> 121.466823, 'latitude'=>31.032611)
                );
     // 参考点步进值，在平面上就是单位矩形
    private $GPS_X_STEP = array('longitude'=>0.000831, 'latitude'=> 0.000248);
    private $GPS_Y_STEP = array('longitude'=>-0.000242, 'latitude'=> 0.000472); 

    // 资源初始范围设置
    private $WOOD_RANGE = array('start'=>100,'end'=>200);
    private $STONE_RANGE = array('start'=>50,'end'=>100);
    private $FOOD_RANGE = array('start'=>50,'end'=>100);

    // 参考点步进值的细分，决定每个参考范围内随机点的细腻度
    private $GPS_INTERVAL = 10; 

    // 建筑点和资源点的比例
    private $RATIO_CONS_RES = 0.7;




    function init(){

        echo "begin";
        echo "</br>";

        $this->construction->delete_all();
        $this->resourceBase->delete_all();
        // 生成随机点，并存入数据库
        for ($y_step=array('longitude'=>0, 'latitude'=>0);
            $y_step['longitude'] > $this->GPS_RANGE['leftUp']['longitude'] - $this->GPS_RANGE['leftDown']['longitude'] - $this->GPS_Y_STEP['longitude'],
            $y_step['latitude'] < $this->GPS_RANGE['leftUp']['latitude'] - $this->GPS_RANGE['leftDown']['latitude'] - $this->GPS_Y_STEP['latitude'];
            $y_step['longitude'] += $this->GPS_Y_STEP['longitude'],
            $y_step['latitude'] += $this->GPS_Y_STEP['latitude']         
            )
        {
            for ($x_step=array('longitude'=>0, 'latitude'=>0);
                $x_step['longitude'] < $this->GPS_RANGE['rightDown']['longitude'] - $this->GPS_RANGE['leftDown']['longitude'] - $this->GPS_X_STEP['longitude'],
                $x_step['latitude'] < $this->GPS_RANGE['rightDown']['latitude'] - $this->GPS_RANGE['leftDown']['latitude'] - $this->GPS_X_STEP['latitude'];
                $x_step['longitude'] += $this->GPS_X_STEP['longitude'],
                $x_step['latitude'] += $this->GPS_X_STEP['latitude']          
                )
            {
                $location_ref = array('longitude' => $this->GPS_RANGE['leftDown']['longitude'] + $x_step['longitude'] + $y_step['longitude'] ,
                                    'latitude' => $this->GPS_RANGE['leftDown']['latitude'] + $x_step['latitude'] + $y_step['latitude'] );
                $location = array('longitude'=>NULL, 'latitude'=>NULL);
                $location['longitude'] = $location_ref['longitude'] + 
                                        $this->GPS_X_STEP['longitude'] * (mt_rand(1,$this->GPS_INTERVAL) / $this->GPS_INTERVAL) + 
                                        $this->GPS_Y_STEP['longitude'] * (mt_rand(1,$this->GPS_INTERVAL) / $this->GPS_INTERVAL);
                $location['latitude']  = $location_ref['latitude'] + 
                                        $this->GPS_X_STEP['latitude'] * (mt_rand(1,$this->GPS_INTERVAL) / $this->GPS_INTERVAL) + 
                                        $this->GPS_Y_STEP['latitude'] * (mt_rand(1,$this->GPS_INTERVAL) / $this->GPS_INTERVAL);
                // $location = $location['longitude'].','.$location['latitude'];
            
                $type = mt_rand(1,100);
                // echo "t";
                if ($type < 100 * $this->RATIO_CONS_RES){
                    $this->construction->setBase($location['longitude'],$location['latitude']);
                    // echo "t";
                } else {
                    $this->resourceBase->setBase($location['longitude'],$location['latitude'],
                        mt_rand($this->WOOD_RANGE['start'],$this->WOOD_RANGE['end']),
                        mt_rand($this->STONE_RANGE['start'],$this->STONE_RANGE['end']),
                        mt_rand($this->FOOD_RANGE['start'],$this->FOOD_RANGE['end'])
                    );
                }
            }
        }
        echo "success";
    }    

    function fresh(){
        // $scale = $this->account->getNum();
        $this->resourceBase->fresh($this->WOOD_RANGE, $this->STONE_RANGE, $this->FOOD_RANGE);
    }
}