<?php
// +----------------------------------------------------------------------
// | 功能介绍：自备一些函数助手
// +----------------------------------------------------------------------
// | Created by PhpStorm.
// +----------------------------------------------------------------------
// | Time:  2022/8/31
// +----------------------------------------------------------------------
// | Author: 龙志勇 <longzhiyongdxfncel@163.com>
// +----------------------------------------------------------------------
// | Copyright  www.XXXXX.com All rights reserved.
// +----------------------------------------------------------------------
// + ━━━━━━神兽出没━━━━━━
// +
// + 　　 ┏┓     ┏┓
// + 　　┏┛┻━━━━━┛┻┓
// + 　　┃　　　　　 ┃
// +　 　┃　　━　　　┃
// + 　　┃　┳┛　┗┳  ┃
// + 　　┃　　　　　 ┃
// + 　　┃　　┻　　　┃
// + 　　┃　　　　　 ┃
// + 　　┗━┓　　　┏━┛　Code is far away from bug with the animal protecting
// + 　　　 ┃　　　┃    神兽保佑,代码无bug
// + 　　　　┃　　　┃
// +　 　　　┃　　　┗━━━┓
// + 　　　　┃　　　　　　┣┓
// + 　　　　┃　　　　　　┏┛
// + 　　　　┗┓┓┏━┳┓┏┛
// + 　　　　 ┃┫┫ ┃┫┫
// + 　　　　 ┗┻┛ ┗┻┛
// +
// + ━━━━━━感觉萌萌哒━━━━━━
// +----------------------------------------------------------------------
namespace longzy;

class helper {

    /**
     * 获取文件下所有文件
     * @param $arr_file 返回结果
     * @param $directory 指定目录
     */
    public static function scan_dir(&$arr_file, $directory) {
        $mydir = dir($directory);
        while ($file = $mydir->read()) {
            if ((is_dir("$directory/$file")) and ($file != ".") and ($file != "..")) {
                scan_dir($arr_file, "$directory/$file");
            } else if (($file != ".") and ($file != "..")) {
                $arr_file[] = "$directory/$file";
            }
        }
        $mydir->close();
    }

    /**
     * 删除文件或者目录
     * @param $dir
     * @return bool
     */
    public static function del_dir($dir) {
        if (is_file($dir)) {
            unlink($dir);
            return true;
        } else {
            $dh = opendir($dir);
            while ($file = readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . "/" . $file;
                    if (!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        deleteDir($fullpath);
                    }
                }
            }
            closedir($dh);
            //删除当前文件夹：
            if (rmdir($dir)) {
                return true;
            } else {
                return false;
            }
        }
    }


    /**
     * 生成指定长度的纯数字编号
     * @param string $lenth 长度，不包含前缀
     * @param string $pk 前缀
     * @return int|string
     */
    public static function make_number($lenth = '9', $pk = '') {
        $lenth = intval($lenth);
        if ($lenth <= 0) {
            return 0;
        }

        $unid = uniqid();
        $prefix = (date('s') + rand(9, 40));
        $prefix .= substr(microtime(), 2, 6);

        //截取后面6位,并且把字母随机替换为数字
        $str = substr($unid, 7, 4);
        for ($i = 0; $i < mb_strlen($str); $i++) {
            $v = substr($str, $i, 1);
            if (is_numeric($v)) {
                $prefix = $prefix . $v;
            } else {
                $prefix = $prefix . rand(0, 9);
            }
        }
        //长度处理
        if (mb_strlen($prefix) == $lenth) {
            return $pk . $prefix;
        } elseif (mb_strlen($prefix) > $lenth) {
            $arr = str_split($prefix, 1);
            shuffle($arr);
            $arr = array_slice($arr, 0, $lenth);
            return $pk . implode('', $arr);
        } else {
            $max = $lenth - mb_strlen($prefix);
            for ($i = 0; $i < $max; $i++) {
                $prefix .= rand(0, 9);
            }
            return $pk . $prefix;
        }
    }

    /**
     * 发送请求
     *  $url：地址
     *  null $data：post的数据
     *  null $header：header头，格式 array("key:val","key2:val2")
     *  mixed：请求返回的结果
     */
    public static function https_request($url, $data = null, $header = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if ($output === false) {
            $msg = curl_error($curl);
            curl_close($curl);
            return $msg;
        } else {
            curl_close($curl);
            return $output;
        }
    }

    /**
     * 系统加密方法
     * @param string $data 要加密的字符串
     * @param string $key 加密密钥
     * @param int $expire 过期时间 单位 秒
     * return string
     */
    public static function url_md5($data, $key = 'HelloWord', $expire = 0) {
        $data = base64_encode($data);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l)
                $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }
        $str = sprintf('%010d', $expire ? $expire + time() : 0);
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
    }


    /**
     * 过滤微信昵称
     * @param $nickname
     * @return string
     */
    public static function filter_nickname($nickname) {
        $nickname = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $nickname);

        $nickname = str_replace(array('"', '\''), '', $nickname);

        return addslashes(trim($nickname));
    }


    /**
     * 将数组转为树形结构
     * @param $list
     * @param string $pk 主键字段
     * @param string $pid 父级字段
     * @param string $child 子级数组键值
     * @param int $root 最顶级ID
     * @return array
     */
    public static function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0) {
        $tree = array();
        if (is_array($list)) {
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];

                if ($root == $parentId) {
                    $tree[$data[$pk]] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][$data[$pk]] = &$list[$key];
                        $parent[$child] = array_values($parent[$child]);
                    }
                }
            }
        }
        return $tree;
    }


    /**
     * 获取树形菜单下某个分类的全部子类， ids
     * @param $categorys
     * @param int $catId 指定ID
     * @param string $id id
     * @param string $p pid
     * @return array
     */
    public static function get_subs($categorys, $catId = 0, $id = 'id', $p = 'pid') {
        $arr = [];
        array_push($arr, $catId);
        foreach ($categorys as $k => $v) {
            if ($v[$p] == $catId) {
                array_push($arr, $v[$id]);
                unset($categorys[$k]);
                $a = getSubs($categorys, $v[$id], $id, $p);
                $arr = array_merge($arr, $a);
            }
        }
        $arr = array_values(array_unique($arr));
        return $arr;
    }

    /**
     * 获取指定id的所有父级ID
     * @param $arrCat array
     * @param int $parent_id 要用的id
     * @param int $level
     * @param bool $all
     * @return array|bool|string
     */
    public static function get_menu_tree($arrCat = array(), $parent_id = 0, $level = 0, $all = True) {
        static $arrTree; //使用static取代global
        if (!$all) {
            $arrTree = '';
        }

        if (empty($arrCat)) {
            return FALSE;
        }

        $level++;
        if ($level == 1) {
            $arrTree[] = $parent_id;
        }

        foreach ($arrCat as $key => $value) {
            if ($value['id'] == $parent_id) {
                //$value[ 'level'] = $level;
                $arrTree[] = $value['pid'];
                unset($arrCat[$key]); //注销当前节点数据，降低已没用的遍历
                getMenuTree($arrCat, $value['pid'], $level);
            }
        }
        return array_values(array_unique($arrTree));
    }

}