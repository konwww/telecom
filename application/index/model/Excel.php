<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/12/21
 * Time: 21:23
 */

namespace app\index\model;


use PHPExcel;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Writer_Excel5;
use think\Model;

class Excel extends Model
{
    /**
     * @param string $file
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    static function import_excel($file="static/1.xlsx"){
        // 判断文件是什么格式
        $type = pathinfo($file);
        $type = strtolower($type["extension"]);
        if ($type=='xlsx') {
            $type='Excel2007';
        }elseif($type=='xls') {
            $type = 'Excel5';
        }
        ini_set('max_execution_time', '0');
        // 判断使用哪种格式
        $objReader = \PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
        // 取得总行数
        $highestRow = $sheet->getHighestRow();
        // 取得总列数
        $highestColumn = $sheet->getHighestColumn();
        //总列数转换成数字
        $numHighestColum = \PHPExcel_Cell::columnIndexFromString("$highestColumn");
        //循环读取excel文件,读取一条,插入一条
        $data=array();
        //从第一行开始读取数据
        for($j=1;$j<=$highestRow;$j++){
            //从A列读取数据
            for($k=0;$k<=$numHighestColum;$k++){
                //数字列转换成字母
                $columnIndex = \PHPExcel_Cell::stringFromColumnIndex($k);
                // 读取单元格
                $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$columnIndex$j")->getValue();
            }
        }
        return $data;
    }

    /**
     * 创建(导出)Excel数据表格
     * @param  array $list 要导出的数组格式的数据
     * @param  string $filename 导出的Excel表格数据表的文件名
     * @param  array $indexKey $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值)
     * @param int $startRow 第一条数据在Excel表格中起始行
     * @param bool $excel2007
     * @return bool
     * @throws \PHPExcel_Exception
     */

    public static function toExcel($list,$filename,$indexKey,$startRow=1,$excel2007=false){
        if(empty($filename)) $filename = time();
        if( !is_array($indexKey)) return false;

        $header_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        //初始化PHPExcel()
        $objPHPExcel = new PHPExcel();

        //设置保存版本格式
        if($excel2007){
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $filename = $filename.'.xlsx';
        }else{
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $filename = $filename.'.xls';
        }

        //接下来就是写数据到表格里面去
        $objActSheet = $objPHPExcel->getActiveSheet();
        //$startRow = 1;
        foreach ($list as $row) {
            foreach ($indexKey as $key => $value){
                //这里是设置单元格的内容
                $objActSheet->setCellValue($header_arr[$key].$startRow,$row[$value]);
            }
            $startRow++;
        }
        $objWriter->save();
        return $filename;
    }
}