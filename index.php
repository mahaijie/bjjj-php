<?php
require_once 'config.php';
require_once 'entercarlist.php';
require_once 'submitpaper.php';

// users.json获取多个用户
$info_users = loadConfig('users.json');
for ($i = 0; $i < count($info_users); $i++) {
    $userid = $info_users[$i];
    // 检查use日的目录是否存在
    if (!is_dir($userid)) {
        makeOutHtml("User $i has no folder!!!");
        continue;
    }
    // 检查时间戳目录是否存在
    $date = date("Y-m-d");
    if (!is_dir($userid.'/'.$date)) {
        makeOutHtml("User $i date = ".$date." has no folder!!!");
        continue;
    }

    // 检查车辆列表信息
    $result_array = entercarlist($userid);
    if ($result_array[0] != 200 || $result_array[1] == null) {
        makeOutHtml("Enter car list $i code = ".$result_array[0]);
        continue;
    }

    // 解析json，判断是否需要申请
    $data_json = json_decode($result_array[1]);
    if ($data_json->{'rescode'} != "200") {
        // 输出异常信息
        makeOutHtml("Enter car list $i rescode = ".$data_json->{'rescode'}." resdes = ".$data_json->{'resdes'});
        continue;
    }
    // 数组，一辆车对应一个
    $datalist = $data_json->{'datalist'};
    // 这里我默认只有一辆车
    $carobj = $datalist[0];
    // 是否可以申请，carinfo下边用applyflag来判断
    $applyflag = $carobj->{'applyflag'};
    if ($applyflag != '1') {
        makeOutHtml("Enter car list $i applyflag != 1, 无需申请");
        continue;
    }

    // 提交表单
    $result_array = submitPaper($userid);
    if ($result_array[0] != 200 || $result_array[1] == null) {
        makeOutHtml("Submit paper $i code = ".$result_array[0]);
        continue;
    }
    // 解析json
    $paper_json = json_decode($result_array[1]);
    // 输出结果
    makeOutHtml("Submit paper $i rescode = ".$paper_json->{'rescode'}." resdes = ".$paper_json->{'resdes'});
}
?>