<?php
/**
 * Created by PhpStorm.
 * User: smallzz
 * Date: 2018/1/9
 * Time: 上午9:34
 */

namespace extend\service;

class WechatTpl
{
    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 认证失败
     * @param $param
     * @return array
     */
    public function auth_fail($param)
    {
        $data = [
            'touser' => $param['openid'],#openid
            'template_id' => 'GLDBJ9BTZBVfnGRnyIpLL3MN2OaBad6hPEWYLfA1aec',#模版id//赶紧说小程序
            'page' => $param['page'],
            'form_id' => $param['form_id'],
            'data' => [
                'keyword1' => [
                    'value' => $param['key1'], //认证姓名
                ],
                'keyword2' => [
                    'value' => $param['key2'], //认证时间
                ],
                'keyword3' => [
                    'value' => $param['key3'], //认证结果
                ],
                'keyword4' => [
                    'value' => '您的实名认证未通过，请核对后重新认证，或者联系客服', //备注
                ],

            ]
        ];
        return $data;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 微信认证失败
     * @param $param
     * @return array
     */
    public function wechat_fail($param)
    {
        $data = [
            'touser' => $param['openid'],#openid
            'template_id' => 'GLDBJ9BTZBVfnGRnyIpLL3MN2OaBad6hPEWYLfA1aec',#模版id//赶紧说小程序
            'page' => $param['page'],
            'form_id' => $param['form_id'],
            'data' => [
                'keyword1' => [
                    'value' => $param['key1'], //认证姓名
                ],
                'keyword2' => [
                    'value' => $param['key2'], //认证时间
                ],
                'keyword3' => [
                    'value' => $param['key3'], //认证结果
                ],
                'keyword4' => [
                    'value' => '您填写的微信号不正确，请修改。', //备注
                ],

            ]
        ];
        return $data;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 认证成功
     * @param $param
     * @return array
     */
    public function auth_pass($param)
    {
        $data = [
            'touser' => $param['openid'],#openid
            'template_id' => 'CDCp1HsFhiBgYsCBJh-SatyHaPom-k-qvYCl3V-IFUc',#模版id//赶紧说小程序
            'page' => $param['page'],
            'form_id' => $param['form_id'],#formid
            'data' => [
                'keyword1' => [
                    'value' => $param['key1'], //认证姓名
                ],
                'keyword2' => [
                    'value' => $param['key2'], //认证时间
                ],
                'keyword3' => [
                    'value' => $param['key3'], //认证结果
                ],
                'keyword4' => [
                    'value' => '恭喜您，您的实名认证已通过', //备注
                ],
            ]

        ];
        return $data;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 购买通知
     * @param $param
     * @return array
     */
    public function buy($param)
    {
        $data = [
            'touser' => $param['openid'],#openid
            'template_id' => 'JxwCUHVWB3vj9gFlszO0bd6gMWF5-t49dzebw40BHq0',//家教小程序模版id
            'page' => $param['page'],#todo
            'form_id' => $param['form_id'],#formid
            'data' => [
                'keyword1' => [
                    'value' => $param['key1'], //物品名称x 老师/女士/先生的联系方式
                ],
                'keyword2' => [
                    'value' => $param['key2'], //购买金额
                ],
                'keyword3' => [
                    'value' => $param['key3'], //购买时间
                ],
                'keyword4' => [
                    'value' => '您已成功购买对方的联系方式，快去和他/她联系吧。',
                ]
            ]

        ];
        return $data;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 被购买通知
     * @param $param
     * @return array
     */
    public function bought($param)
    {
        $data = [
            'touser' => $param['openid'],#openid
            'template_id' => 'j_clD8XfNEoHbJ1wts2Eo010dXPmRlyhQiv3B2Nq9vE',#模版id//赶紧说小程序
            'page' => $param['page'],#todo
            'form_id' => $param['form_id'],#formid
            'data' => [
                'keyword1' => [
                    'value' => $param['key1'], //x／先生 女士 /老师 购买人
                ],
                'keyword2' => [
                    'value' => $param['key2'], //购买时间
                ],
                'keyword3' => [
                    'value' => '对方已购买您的联系，快去和他/她联系吧。',
                ],
            ]

        ];
        return $data;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 审核成功
     * @param $param
     * @return array
     */
    public function verify_pass($param)
    {
        $data = [
            'touser' => $param['openid'],#openid
            'template_id' => 'sxRRW6gliwpl3PElsrIWdeN8gHp_CeRTxxVI8LFWteU',#模版id//赶紧说小程序
            'page' => $param['page'],#todo
            'form_id' => $param['form_id'],#formid
            'data' => [
                'keyword1' => [
                    'value' => '通过',
                ],
                'keyword2' => [
                    'value' => $param['key2'], //用户姓名
                ],
                'keyword3' => [
                    'value' => $param['key3'], // 通过时间
                ],
                'keyword4' => [
                    'value' => '您的学历审核已通过，快去看看有哪些符合您要求的家长吧！',
                ]
            ]

        ];
        return $data;
    }

    /**
     * @Author panhao
     * @DateTime 2018-04-02
     *
     * @description 审核失败
     * @param $param
     * @return array
     */
    public function verify_fail($param)
    {
        $data = [
            'touser' => $param['openid'],#openid
            'template_id' => 'wvBk5FrRGOSihhnHsL1cytYrqGOi5zLg4MVLa9biBZI',#模版id//赶紧说小程序
            'page' => $param['page'],#todo
            'form_id' => $param['form_id'],#formid
            'data' => [
                'keyword1' => [
                    'value' => '不通过',
                ],
                'keyword2' => [
                    'value' => $param['key2'], //用户姓名
                ],
                'keyword3' => [
                    'value' => $param['key3'], //通过时间
                ],
                'keyword4' => [
                    'value' => '您的学历认证未通过，请重新上传认证，如有疑问则联系客服。',
                ]
            ]

        ];
        return $data;
    }
}