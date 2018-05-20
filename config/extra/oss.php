<?php
/**
 * Created by PhpStorm.
 * User: zzhh
 * Date: 2017/12/6
 * Time: 下午4:28
 */
return [
    'user' => 'pgyapi',
    'access_key' => 'LTAIHvMFiHyWjGan',
    'access_secret_key' => 'iwNdU1WR4oEhbDcyD4MeaKBWCKmQY3',
    'default_bucket_name' => 'wxtutor', # 默认的bucket
    'outer_endpoint' => 'oss-cn-shanghai.aliyuncs.com',# 外网endpoint
    'outer_host' => 'https://wxtutor.oss-cn-shanghai.aliyuncs.com/', # 外网访问域名，注意要以'/'结尾
    'inner_endpoint' => 'oss-cn-shanghai-internal.aliyuncs.com', # 内网endpoint
    'inner_host' => 'https://wxtutor.oss-cn-shanghai-internal.aliyuncs.com/',# 内网访问域名
    'temp_file_path' => 'tmp',#临时文件路径
    'temp_file_suffix' => '.jpg',#临时文件后缀
    'blur_suffix' => '?x-oss-process=image/blur,r_50,s_50', // 模糊处理后缀
];