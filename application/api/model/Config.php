<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/4/8
 * Time: 上午11:57
 */

namespace app\api\model;


use app\common\model\BaseModel;

class Config extends BaseModel
{
    protected $table = 'config';

    /** 设置值
     * auth smallzz
     */
    public function setValue($id=0,$val){
        $data = [
            'value'=>$val,
            'updatetime'=>date('Y-m-d H:i:s')
            ];
        if(empty($id)){
            return $this->save($data);
        }else{
            $where = [
                'id'=>$id
            ];
            return $this->save($data,$where);
            #return $this->getLastSql();
        }

    }

    public function setValues($id=0,$val){
        $data = [
            'namecn'=>$val,
            'updatetime'=>date('Y-m-d H:i:s')
        ];
        if(empty($id)){
            $data['type'] = 3;
            return $this->save($data);
        }else{
            $where = [
                'id'=>$id
            ];
            return $this->save($data,$where);
            #return $this->getLastSql();
        }

    }
    /**
     * auth smallzz
     */
    public function upValue(){

    }

    /**
     * auth smallzz
     */
    public function delValue(){

    }

    /**
     * @Author panhao
     * @DateTime 2018-04-09
     *
     * @description 根据id获取名称
     * @param array $where
     * @param string $value
     * @return mixed
     */
    public function getValue(array $where, string $value){
        return $this->where($where)->value($value);
    }


}