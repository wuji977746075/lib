<?php
/**
 * xml function
 * rainbow 977746075@qq.com
 * 2018-05-11 11:29:23
 * require  array:fix_null
 */
// test xml
// // header('content-type:text/xml;charset:utf-8');
// $arr = [
//   'name'=>'kkk',
//   'list'=>[
//     ['id'=>'p','id2'=>'p2'],
//     ['id'=>NULL]
//   ]
// ];
// $xml = '<RES><HOS_ID><![CDATA[123456]]></HOS_ID><DEPT_ID><![CDATA[49]]></DEPT_ID><REG_DOCTOR_LIST><DOCTOR_ID><![CDATA[YS49]]></DOCTOR_ID><NAME><![CDATA[妇科专家六诊室普通号]]></NAME><JOB_TITLE><![CDATA[医师]]></JOB_TITLE><REG_LIST><REG_DATE><![CDATA[2018-05-11]]></REG_DATE><REG_WEEKDAY><![CDATA[星期五]]></REG_WEEKDAY><REG_TIME_LIST><REG_ID><![CDATA[20180511-49-2]]></REG_ID><TIME_FLAG><![CDATA[2]]></TIME_FLAG><REG_STATUS><![CDATA[1]]></REG_STATUS><TOTAL><![CDATA[99]]></TOTAL><OVER_COUNT><![CDATA[99]]></OVER_COUNT><REG_LEVEL><![CDATA[1]]></REG_LEVEL><REG_FEE><![CDATA[1000]]></REG_FEE><TREAT_FEE><![CDATA[0]]></TREAT_FEE><ISTIME><![CDATA[0]]></ISTIME></REG_TIME_LIST><REG_TIME_LIST><REG_ID><![CDATA[20180511-49-2]]></REG_ID><TIME_FLAG><![CDATA[2]]></TIME_FLAG><REG_STATUS><![CDATA[1]]></REG_STATUS><TOTAL><![CDATA[99]]></TOTAL><OVER_COUNT><![CDATA[99]]></OVER_COUNT><REG_LEVEL><![CDATA[1]]></REG_LEVEL><REG_FEE><![CDATA[1000]]></REG_FEE><TREAT_FEE><![CDATA[0]]></TREAT_FEE><ISTIME><![CDATA[0]]></ISTIME></REG_TIME_LIST></REG_LIST></REG_DOCTOR_LIST></RES>';

// echo '<textarea style="width:50%;height:50%"">';
// // 不支持接连3层无key值
// echo arr2xml($arr);
// // 不支持xml节点属性
// // var_dump(xml2arr($xml));
// echo '</textarea>';
/**
 *xml转成数组
 * 会去掉最外层标签
 * require  array:fix_null
 */
function xml2arr($xmlstr,$flag="_LIST") {
  //禁止引用外部xml实体
  // libxml_disable_entity_loader(true);
  $ret = json_decode(json_encode(simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
  return fix_list_one(fix_null($ret),$flag);
}
// 协议返回2维数组成员为1时解析成1维 bug
// @params : $flag :2维数组key后缀
function fix_list_one($a,$flag="_LIST") {
  if($flag && is_array($a)){ // []
    foreach ($a as $k=> &$v) {
      if(is_array($v)){ // k=>[]
        $l = strlen($flag);
        $v = fix_list_one($v,$flag);//自内而外递归
        if(substr($k, -$l) == $flag){ // xx${flag}=>[]
          $one = false;
          foreach ($v as $kk=>$vv) {
            if(!is_numeric($kk)){ //检测到2维数组
              $one = true;
              break;
            }
          }
          $one && $v = [$v];
        }
      }else{ // k=>v
      }
    }
  }
  return $a;
}

// 数组或字符串 转xml
function arr2xml($arr,$root='ROOT',$head='<?xml version="1.0" encoding="UTF-8"?>') {
    $xml = $head.arr_to_xml($arr,$root,$root);
    return $xml;
}
// 数组或字符串 转xml
function arr_to_xml($arr,$root="ROOT",$plus=true){
  $s    = "";
  $root = strtoupper($root);
  $pre  = "<$root>";$last = "</$root>";
  if(is_array($arr)){
    foreach ($arr as $k =>$v) {// 0=>[] or k=>[] or k=>v
      $k    = strtoupper($k);
      $temp = is_numeric($k);
      if($temp && is_array($v)){ // 0=>[]
          $plus = false;
          $s .= arr_to_xml($v,$root);
      }else{
        if(is_array($v)){ // k=>[]
          $s .= arr_to_xml($v,$k);
        }else{ // k=>v
          $s .= "<$k><![CDATA[".$v."]]></$k>";
        }
      }
    }
  }else{
    $s = $arr;
  }
  $s = $plus ? $pre.$s.$last : $s;
  return $s;
}