<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\User;

class IndexController extends Controller
{
    //登录页面
    public function index()
    {
        return view('index/index');
    }

    //登录
    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if(empty($username)){
            echo '请输入正确的用户名';die;
        }else{
            $where= ['username'=>$username];
        }
        if(empty($password)){
            echo "请输入密码";die;
        }
        $data = User::where($where)->find();
        dd($data);
    }

    //显示直播页面
    public function show()
    {
        echo 55;
    }

    //注册页面
    public function zhuce()
    {
        return view('index/zhuce');
    }

    //注册
    public function dozhuce()
    {

    }
}
