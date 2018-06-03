<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2017/10/26
 * Time: 11:30
 */

namespace app\backend\logic;

use app\common\logic\BaseLogic;
use think\Log;

class ExcelLogic extends BaseLogic
{
    private $Excel = '';
    //private $Excel2017 = '';
    //private $Excel5 = '';
    /**
     * ExcelLogic constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->Excel = new \PHPExcel();
    }

    /**
     * @remark
     * @author smallzz--bliblihome
     * @param $title         标题
     * @param array $data 数据
     * @param array $letter 头部
     * @param array $tableheader 头部标题
     * @return bool|string
     */

    public function export($title, $data = [], $tableheader = [])
    {
        ob_clean();           //超级nb的缓冲区清楚  解决乱码
        //try {
            $hang = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z','AA','AB','AC','AD'];
            if (empty($data)) {
                return false;
            }
            $count = count($tableheader);
            for ($i = 0; $i < $count; $i++) {
                $letter[$i] = $hang[$i];
            }
            //填充表头信息
            $this->Excel->getActiveSheet()->setTitle("$title");
            for ($i = 0; $i < $count; $i++) {

                $this->Excel->getActiveSheet()->setCellValue("$letter[$i]1", "$tableheader[$i]");
                $this->Excel->getActiveSheet()->getColumnDimension("$letter[$i]")->setWidth(21); //设置列宽

                $this->Excel->getActiveSheet()->getStyle("$letter[$i]")->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);  //文本
                //->setFormatCode(\PHPExcel_Cell_DataType::TYPE_STRING);
            }
            for ($i = 2; $i <= count($data) + 1; $i++) {
                $j = 0;
                foreach ($data[$i - 2] as $key => $value) {
                    #$this->Excel->getActiveSheet()->getStyle($letter[$j].$i)->getNumberFormat()
                    #->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);  //文本
                    $this->Excel->getActiveSheet()->setCellValue($letter[$j] . $i, " $value");
                    $j++;
                }
            }
            //创建Excel输入对象
            //$write = new \PHPExcel_Writer_Excel5($this->Excel);
            $write = new \PHPExcel_Writer_Excel2007($this->Excel);
            $path = __DIR__ . './../../../public/excel/';
            //$write->save(__EXCEL__.$title.'.xlsx');
            $write->save($path . $title . '.xlsx');
            return 'http://' . $_SERVER['HTTP_HOST'] . '/excel/' . $title . '.xlsx';
//        } catch (\think\Exception $exception) {
//            Log::error($exception->getMessage());
//            return false;
//        }
    }

    public function improt($file,$houz='xls'){
        #excel导入
        #$file  = __DIR__.'/../../../public/excel/test.xlsx';
        if ($houz == 'xlsx') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        } else {
            $objReader = \PHPExcel_IOFactory::createReader('Excel5'); //use Excel5 for 2003 format
        }
        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();           //取得总行数
        $highestColumn = $sheet->getHighestColumn(); //取得总列数
        $arr = [];
        for ($j = 1; $j <= $highestRow; $j++) {
            $str = "";
            //从A列读取数据
            for ($k = 'A'; $k <= $highestColumn; $k++) {
                $str .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue() . '|-|';//读取单元格
            }
            $strs = explode("|-|", $str);
            #var_dump($strs);
            if($j != 1){
                $arr[] = $strs;
            }
            #$arr[] = $strs;
        }
        #exit;
        return $arr;
    }
    public function improt_($file,$houz='xls'){
        #excel导入
        #$file  = __DIR__.'/../../../public/excel/test.xlsx';
        if ($houz == 'xlsx') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        } else {
            $objReader = \PHPExcel_IOFactory::createReader('Excel5'); //use Excel5 for 2003 format
        }
        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();           //取得总行数
        $highestColumn = $sheet->getHighestColumn(); //取得总列数
        $arr = [];
        for ($j = 1; $j <= $highestRow; $j++) {
            $str = "";
            //从A列读取数据
            for ($k = 'A'; $k <= $highestColumn; $k++) {
                $str .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue() . '|-|';//读取单元格
            }
            $strs = explode("|-|", $str);


            #if($j != 1){
                $arr[] = $strs;
            #}

        }
        #var_dump($arr);exit;
        return $arr;
    }


}