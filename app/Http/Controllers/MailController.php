<?php
namespace App\Http\Controllers;

use App\Model\MailTemplate;
use App\Model\BlackList;
use App\Model\Info;
use App\Http\Controllers\Controller;
use App\Mail\DemoEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use DB;
 
class MailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function send()
    {
        $objDemo = new \stdClass();
        $objDemo->demo_one = 'Demo One Value';
        $objDemo->demo_two = 'Demo Two Value';
        $objDemo->sender = 'SenderUserName';
        $objDemo->receiver = 'ReceiverUserName';
 
        Mail::to("kdfwow64@gmail.com")->send(new DemoEmail($objDemo));
    }

    public function replaceFunction($str,$info) {
        $str = str_replace("#domain#", $info['domain_name'], $str);
        $str = str_replace("#fname#", $info['admins_name'], $str);
        $str = str_replace("#rank#", $info['rank'], $str);
        $errors = $info['error_total'] + $info['warning_total'];
        $str = str_replace("#errors#", $errors, $str);
        $str = str_replace("#email#", $info['email'], $str);
        return $str;
        
    }
    public function sendAll()
    {
        $template = MailTemplate::get(['*'])->first();
        $template_name = $template['template_name'];
        $template_text = $template['template_text'];

        $blacklist = BlackList::get(['*']);
        DB::update('update infos set black = "0"');
        for( $i = 0 ; $i < sizeof($blacklist) ; $i++ ) {
            if( $blacklist[$i]['domainORemail'] == '1' ) {
                DB::update('update infos set black = "1" where LOWER(domain_name) like LOWER(?)' , [$blacklist[$i]['domain']]);
                $sub = '%.'.$blacklist[$i]['domain'].'%';
                DB::update('update infos set black = "1" where LOWER(domain_name) like LOWER(?)' , [$sub]);
            } else if( $blacklist[$i]['domainORemail'] == '2' ) {
                DB::update('update infos set black = "1" where email like ?' , [$blacklist[$i]['domain']]);
            } else if( $blacklist[$i]['domainORemail'] == '3' ) {
                DB::update('update infos set black = "1" where admins_name like ?' , [$blacklist[$i]['domain']]);
            }
        }
        $info = Info::get(['*']);
        for( $i = 0 ; $i < sizeof($info) ; $i++ ) {
            if($info[$i]['black'] != 1) {
                $objDemo = new \stdClass();
                $objDemo->title = $this->replaceFunction($template_name,$info[$i]);
                $objDemo->text = $this->replaceFunction($template_text,$info[$i]);
                $objDemo->sender = 'Google Scraping Server';
                $objDemo->receiver = $info[$i]['admins_name'];
         
                if(filter_var($info[$i]['email'],FILTER_VALIDATE_EMAIL)) {
                    Mail::to($info[$i]['email'])->send(new DemoEmail($objDemo));
                }
            }
        }
    }

    public function index() {
    	$template = MailTemplate::get(['*'])->first();
    	$template_name = $template['template_name'];
    	$template_text = $template['template_text'];
    	$template_text2 = $template['template_text2'];
    	$template_id = $template['id'];
    	return view('mails.main',compact('template_name','template_text','template_id','template_text2'));
    }

    public function save(Request $request) {
    	$id = $request->input('template_id');
    	$text = $request->input('template_text');
    	$text2 = $request->input('template_text2');
    	$name = $request->input('template_name');
    	DB::update('update mails set template_name = ? , template_text = ?, template_text2 = ? where id = ? ', [$name, $text, $text2, $id]);
    //	MailTemplate::where('id',$id)->update(['template_text' => 'aa']);
    	echo 1;
    }
}


