<?php
/**
 * Created by PhpStorm.
 * User: GJY
 * Date: 2018/8/26
 * Time: 18:36
 */

namespace app\api\controller\v1;


use app\api\model\User as UserModel;
use app\api\model\UserAddress;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use app\lib\SuccessMessage;

class Address extends BaseController {
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
    ];

    public function createOrUpdateAddress() {
        // 用一个变量存储验证器，因为后面还要用到这个对象
        $validate = new AddressNew();
        $validate->goCheck();
        // 根据Token获取用户uid
        //  根据用户uid查找用户，判断用户是否存在
        //  获取用户从客户端提交来的信息
        //  根据用户地址信息是否存在判断是添加还是更新地址

        $uid = TokenService::getCurrentUID();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        //AddressModel::
        $data_array = $validate->getDataByRule(input('post.'));

        $user_address = $user->address;
        if (!$user_address) {
            // 通过关联模型新增数据
            $user->address()->save($data_array);
        } else {
            // $user->address 相当于 UserAddress::get($uid);
            $user->address->save($data_array);
        }
        return json(new SuccessMessage(), 201);
    }

    public function getUserAddress() {
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where('user_id', $uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }
}