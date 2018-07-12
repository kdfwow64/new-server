<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Model\Info;
use App\Model\Keyword;
use App\Model\BlackList;
use App\Model\Permission;
use App\User;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;

        $temp  = Keyword::where('user_id','=',Auth::user()->id)->orderBy('created_at','DESC')->get(['*'])->first();
        $flag = "";
        if($temp)
        {
            if($temp->created_at->isToday())
                $flag = "disabled";
        }

        $permission = Permission::get(['*'])->first();
        return view('home',compact('permission','flag'));
    }

    public function getDomains(Request $request) {
        $domain = $request->input('domain');
        $ss = '%'.$domain.'%';
        $items = Info::where('domain_name','like',$ss)->get(['*']);
        return response()->json($items);
    }

    public function getEmail(Request $request) {
        $email = $request->input('email');
        $ss = '%'.$email.'%';
        $items = Info::where('email','like',$ss)->get(['*']);
        return response()->json($items);
    }

    public function blacklist() {
        $blacklist = BlackList::orderBy('id','DESC')->get(['*']);
        $domain_count = 0;
        $email_count = 0;
        $name_count = 0;
        return view('others.blacklist',compact('blacklist','domain_count','email_count','name_count'));
    }

    public function permission() {
        $permission = Permission::get(['*']);
        return view('others.permission',compact('permission'));
    }

    public function insertD(Request $request) {
        $new_info = new BlackList;
        $new_info->domain = $request->input('blacklist_domain');
        $new_info->domainORemail = '1';
        $new_info->save();
        $blacklist = BlackList::get(['*']);
        $domain_count = 0;
        $email_count = 0;
        return redirect()->action('HomeController@blacklist');
    }

    public function insertE(Request $request) {
        $new_info = new BlackList;
        $new_info->domain = $request->input('blacklist_email');
        $new_info->domainORemail = '2';
        $new_info->save();
        $blacklist = BlackList::get(['*']);
        $domain_count = 0;
        $email_count = 0;
        return redirect()->action('HomeController@blacklist');
    }

    public function insertN(Request $request) {
        $new_info = new BlackList;
        $new_info->domain = $request->input('blacklist_name');
        $new_info->domainORemail = '3';
        $new_info->save();
        $blacklist = BlackList::get(['*']);
        $domain_count = 0;
        $email_count = 0;
        return redirect()->action('HomeController@blacklist');
    }

    public function getBlacklistDomains(Request $request) {
        $str = $request->input('str');
        if($str == "") {
            $items = BlackList::where('domainORemail','=',1)->orderBy('id','DESC')->get(['*']);
        } else {
            $str = '%'.$str.'%';
            $items = BlackList::where('domain','like',$str)->where('domainORemail','=',1)->get(['*']);
        }
        return response()->json($items);
    }

    public function getBlacklistEmails(Request $request) {
        $str = $request->input('str');
        if($str == "") {
            $items = BlackList::where('domainORemail','=',2)->orderBy('id','DESC')->get(['*']);
        } else {
            $str = '%'.$str.'%';
            $items = BlackList::where('domain','like',$str)->where('domainORemail','=',2)->get(['*']);
        }
        return response()->json($items);
    }

    public function getBlacklistNames(Request $request) {
        $str = $request->input('str');
        if($str == "") {
            $items = BlackList::where('domainORemail','=',3)->orderBy('id','DESC')->get(['*']);
        } else {
            $str = '%'.$str.'%';
            $items = BlackList::where('domain','like',$str)->where('domainORemail','=',3)->get(['*']);
        }
        return response()->json($items);
    }

    public function blacklistDelete($id) {
        $del = BlackList::where('id',$id)->first();
        $del->delete();
        $blacklist = BlackList::get(['*']);
        $domain_count = 0;
        $email_count = 0;
        return redirect()->action('HomeController@blacklist');
    }

    public function setPermission(Request $request) {
        $id = $request->input('id');
        $value = $request->input('value');
        if($value == 'true')
            DB::update('update permissions set value = 1 where id = ?' , [$id]);
        else
            DB::update('update permissions set value = 0 where id = ?' , [$id]);
        echo $value;
    }
}

