<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\User;



class IndexController extends Controller
{
    //登录页面
    public function index(Request $request)
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
        $data = User::where($where)->first();
        $pwd = $data['password'];
        if(empty($data)){
            echo "请先注册";
        }else if($pwd == $password){
            session(['username'=>$username]);
            return redirect("index/show");
        }else{
            echo '账号或密码有误';die;
        }

    }

    //显示直播页面
    public function show()
    {
        $username =  session('username');
        return view('index/show',['username'=>$username]);
    }

    //注册页面]
    public function zhuce()
    {
        return view('index/zhuce');
    }

    //注册
    public function dozhuce()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $tel = $_POST['tel'];
        $preg_phone='/^1[34578]\d{9}$/';
        if(!preg_match($preg_phone,$tel)){
            echo "请输入正确的电话号码";die;
        }
        if(empty($username)){
            echo '请输入正确的用户名';die;
        }else{
            $where= ['username'=>$username];
            $data = User::where($where)->first();
            if(!empty($data)){
                echo "该用户名已被注册，请重新输入用户名";die;
            }
        }
        if(empty($password)){
            echo '请输入正确的密码';die;
        }
        if(empty($tel)){
            echo '请输入正确的电话号码';die;
        }else{
            $where= ['tel'=>$tel];
            $data = User::where($where)->first();
            if(!empty($data)){
                echo "该电话号码已被注册，请重新输入电话号码";die;
            }
        }
        $info = ['username'=>$username, 'password'=>$password, 'tel'=>$tel];
        $res = User::insert($info);
        if($res){
            session(['username'=>$username]);
            return redirect("index/show");
        }
    }

    //清除session数据
    public function session(Request $request)
    {
        $request->session()->flush();
    }

}
