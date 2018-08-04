<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Info;
use App\Model\Keyword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class MainScraping extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MainScraping:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collecting Information';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function getDomainfromUrl($url) {
        if(substr($url, 0, 4) == "http") {
            $sub_url = substr($url,strpos($url,"/")+2);
            if(substr($sub_url, 0, 4) == "www.")
                $sub_url = substr($sub_url,4);
            if(strpos($sub_url,'/') !== false)
                $sub_url = substr($sub_url, 0, strpos($sub_url, '/'));
            return $sub_url;
        } else {
            return null;
        }
    }
    public function getdetailInfo($domain) {
        if($domain != null) {
            $url = "http://api.whoxy.com/?key=ef2366e1534b0892wz0a9d52229b6f53e&whois=".$domain;
            $serdata = json_decode(file_get_contents($url),true);
            return $serdata;
        } else {
            return null;
        }
    }
    public function handle()
    {
        global $pwd;
        global $uid;
        global $PROXY;
        global $PLAN;
        global $NL;
        global $working_dir;


        ini_set("memory_limit","64M"); 
        ini_set("xdebug.max_nesting_level","2000"); 
        error_reporting(E_ALL & ~E_NOTICE);
       
        $pwd = '1502f31c69779f472c5dafbe59f410ce';
        $uid = '9618'; 

        // General configuration
        $test_website_url = "website.com"; // The URL, or a sub-string of it, of the indexed website.
//        $progress = Keyword:::where('status','=',1)->where('start_date', '<', Carbon::now()->subMinutes(35))->orderBy('id','ASC')->first();
        $progress = DB::select("SELECT * FROM keywords WHERE start_date < timestamp(DATE_SUB(NOW(), INTERVAL 40 MINUTE)) AND status = 1");
        if($progress) {
            foreach ($progress as $val) {
                $progress = $val;
                break;
            }
        }
        if(!$progress) {
            $progress = Keyword::where('status','=',0)->orderBy('id','ASC')->first();
            if(!$progress) {
                echo 'nothing else';
                exit();
            }
        }
        
        Keyword::where('id',$progress->id)->update(array('status' => 1));
        $currentTime = new Carbon;
        Keyword::where('id',$progress->id)->update(array('start_date' => $currentTime));

        $test_keywords = $progress->keyword.' '.$progress->city.' '.$progress->state;
        $test_max_pages = 20; 
        $test_100_resultpage = 0; 

        echo $test_keywords;
        
        $test_country = "global"; 
        $test_language = "en"; 
        $filter = 1; 
        $force_cache = 0; 
        $load_all_ranks = 1; 

        $show_html = 0; 
        $show_all_ranks = 1; 
        // ***************************************************************************
        $working_dir = "./local_cache"; // local directory. This script needs permissions to write into it

        require "functions-ses.php";

        $page = 0;
        $PROXY = array(); 
        $PLAN = array();
        $results = array();


        if ($show_html) $NL = "<br>\n"; else $NL = "\n";
        if ($show_html) $HR = "<hr>\n"; else $HR = "_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_\n";
        if ($show_html) $B = "<b>"; else $B = "!";
        if ($show_html) $B_ = "</b>"; else $B_ = "!";



        if ($show_html)
        {
            echo "<html><body>";
        }

        $keywords = explode(",", $test_keywords);
        if (!count($keywords)) {
            Keyword::where('id',$progress->id)->update(array('status' => 0));
            die ("Error: no keywords defined.$NL");
        }
//        if (!rmkdir($working_dir)) die("Failed to create/open $working_dir$NL");

        $country_data = get_google_cc($test_country, $test_language);
        if (!$country_data) {
            Keyword::where('id',$progress->id)->update(array('status' => 0));
            die("Invalid country/language code specified.$NL");
        }


        $ready = get_license();
        if (!$ready) {
            Keyword::where('id',$progress->id)->update(array('status' => 0));
            die("The specified API key account for user $uid is not active or invalid. $NL");
        }
        if ($PLAN['protocol'] != "http") {
            Keyword::where('id',$progress->id)->update(array('status' => 0));
            die("Wrong proxy protocol configured, switch to HTTP. $NL");
        }

    
        $ch = NULL;
        $rotate_ip = 0; 
        $max_errors_total = 3; 
                                                                                                                                                                                                                                                                                                                                                                                                                                            
        $rank_data = array();
        $siterank_data = array();

        $break=0; // variable used to cancel loop without losing ranking data
        foreach ($keywords as $keyword)
        {
            $rank = 0;
            $max_errors_page = 5; // abort script if there are 5 errors in a row, that should not happen

            if ($test_max_pages <= 0) break;
            $search_string = urlencode($keyword);
            $rotate_ip = 1; // IP rotation for each new keyword

            /*
            * This loop iterates through all result pages for the given keyword
            */
            for ($page = 0; $page < $test_max_pages; $page++)
            {
                $serp_data = load_cache($search_string, $page, $country_data, $force_cache); // load results from local cache if available for today
                $serp_data = NULL;
                $maxpages = 0;

                if (!$serp_data)
                {
                    $ip_ready = check_ip_usage(); // test if ip has not been used within the critical time
                    while (!$ip_ready || $rotate_ip)
                    {
                        $ok = rotate_proxy(); // start/rotate to the IP that has not been started for the longest time, also tests if proxy connection is working
                        if ($ok != 1)
                        {
                            die ("Fatal error: proxy rotation failed:$NL $ok$NL");
                        }
                        $ip_ready = check_ip_usage(); // test if ip has not been used within the critical time
                        if (!$ip_ready)
                        {
                            die("ERROR: No fresh IPs left, try again later. $NL");
                        } else
                        {
                            $rotate_ip = 0; // ip rotated
                            break; // continue
                        }
                    }

                    delay_time(); // stop scraping based on the license size to spread scrapes best possible and avoid detection
                    global $scrape_result; // contains metainformation from the scrape_serp_google() function
                    $raw_data = scrape_google($search_string, $page, $country_data); // scrape html from search engine

                    
                    if ($scrape_result != "SCRAPE_SUCCESS")
                    {
                        if ($max_errors_page--)
                        {
                            echo "There was an error scraping (Code: $scrape_result), trying again .. $NL";
                            $page--;
                            continue;
                        } else
                        {
                            $page--;
                            if ($max_errors_total--)
                            {
                                echo "Too many errors scraping keyword $search_string (at page $page). Skipping remaining pages of keyword $search_string .. $NL";
                                break;
                            } else
                            {
                                die ("ERROR: Max keyword errors reached, something is going wrong. $NL");
                            }
                            break;
                        }
                    }
                    mark_ip_usage(); // store IP usage, this is very important to avoid detection and gray/blacklistings
                    global $process_result; // contains metainformation from the process_raw() function
                    $serp_data = process_raw_v2($raw_data, $page); // process the html and put results into $serp_data

                    if (($process_result == "PROCESS_SUCCESS_MORE") || ($process_result == "PROCESS_SUCCESS_LAST"))
                    {
                        $result_count = count($serp_data);
                        $serp_data['page'] = $page;
                        if ($process_result != "PROCESS_SUCCESS_LAST")
                        {
                            $serp_data['lastpage'] = 1;
                        } else
                        {
                            $serp_data['lastpage'] = 0;
                        }
                        $serp_data['keyword'] = $keyword;
                        $serp_data['cc'] = $country_data['cc'];
                        $serp_data['lc'] = $country_data['lc'];
                        $serp_data['result_count'] = $result_count;
                        store_cache($serp_data, $search_string, $page, $country_data); // store results into local cache
                    }

                    if ($process_result != "PROCESS_SUCCESS_MORE")
                    {
                        $break=1;
                        //break;
                    } // last page
                    if (!$load_all_ranks)
                    {
                        for ($n = 0; $n < $result_count; $n++)
                            if (strstr($results[$n]['url'], $test_website_url))
                            {
                                verbose("Located $test_website_url within search results.$NL");
                                $break=1;
                                //break;
                            }
                    }

                } // scrape clause

                $result_count = $serp_data['result_count'];

                for ($ref = 0; $ref < $result_count; $ref++)
                {
                    $rank++;
                    $rank_data[$keyword][$rank]['title'] = $serp_data[$ref]['title'];
                    $rank_data[$keyword][$rank]['url']  = $serp_data[$ref]['url'];
                    $rank_data[$keyword][$rank]['host'] = $serp_data[$ref]['host'];
                    $rank_data[$keyword][$rank]['desc'] = $serp_data[$ref]['desc'];
                    $rank_data[$keyword][$rank]['type'] = $serp_data[$ref]['type'];
                    //$rank_data[$keyword][$rank]['desc']=$serp_data['desc'']; // not really required
                    if (strstr($rank_data[$keyword][$rank]['url'], $test_website_url))
                    {
                        $info = array();
                        $info['rank'] = $rank;
                        $info['url'] = $rank_data[$keyword][$rank]['url'];
                        $siterank_data[$keyword][] = $info;
                    }
                }
                if ($break == 1) break;

            } // page loop
        } // keyword loop
        $fff = 0;
        if ($show_all_ranks)
        {
            foreach ($rank_data as $keyword => $ranks)
            {
                $pos = 0;
                foreach ($ranks as $rank)
                {
                    $domain = $this->getDomainfromUrl($rank['url']);
                    if((Info::where('domain_name',$domain)->where('user_id',$progress->user_id)->get()->count()) == 0) { 
                        $new_info = new Info();
                        $new_info->business_name = $rank['title'];
                        $new_info->domain_name = $domain;
                        $new_info->flag = 1;
                        $new_info->opt_out = 0;
                        $new_info->option = 1;
                        $new_info->black = 0;
                        
                        if($domain != null ) {
                            $detail_info = $this->getdetailInfo($domain);
                            if($detail_info != null) {
                                $new_info->admins_name = $detail_info['administrative_contact']['full_name'];
                                $new_info->email = $detail_info['administrative_contact']['email_address'];
                                $new_info->phone = $detail_info['administrative_contact']['phone_number'];
                                $new_info->mailing_address = $detail_info['administrative_contact']['mailing_address']."|".$detail_info['administrative_contact']['city_name']."|".$detail_info['administrative_contact']['state_name']."|".$detail_info['administrative_contact']['zip_code']."|".$detail_info['administrative_contact']['country_name'];
                                $new_info->user_id = $progress->user_id;
                                $new_info->keyword_id = $progress->id;
                            }
                        }
                        
                        $new_info->save();
                    }
                }
            }
        }
        Keyword::where('id',$progress->id)->update(array('status' => 2));
        if ($show_html)
        {
            echo "</body></html>";
        }
        echo 1;
        exit();
        return view('home');
    }
}
