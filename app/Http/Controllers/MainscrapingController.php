<?php

namespace App\Http\Controllers;

use App\Model\Info;
use Illuminate\Http\Request;

class MainscrapingController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
	public function getDomainfromUrl($url) {
		if(substr($url, 0, 4) == "http") {
			$sub_url = substr($url,strpos($url,"/")+2);
			if(substr($sub_url, 0, 3) == "www")
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

	public function getIssues1($domain) {
		$url = "https://validator.w3.org/nu/?doc=https%3A%2F%2F$domain%2F";
		if($domain != null) {
			$options['http'] = array(
				'user_agent' => 'GoogleScraper',
				'timeout'	 => 5.5
			);
			$context = stream_context_create($options);
			$content = file_get_contents($url, null, $context);
			

			
	        $html = new \DOMDocument();
	        libxml_use_internal_errors(true);
	        $html->loadHTML($content);
	        $dom_xpath =new \DOMXPath($html);
	        $res['error_total'] = $dom_xpath->query('//div[@id="results"]/ol/li[@class="error"]')->length;
	        $res['warning_total'] = $dom_xpath->query('//div[@id="results"]/ol/li[@class="info warning"]')->length;
	        echo $res;
		} else {
			echo "error";;
		}
	}


	public function getIssues($domain) {
		if($domain != null) {
			$scrape_result = "";
			$url = "https://validator.w3.org/nu/?doc=https%3A%2F%2F$domain%2F";
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en; rv:1.9.0.4) Gecko/2009011913 Firefox/3.0.6");
	        curl_setopt($ch, CURLOPT_URL, $url);
	        set_time_limit(0);
	        $htmdata = curl_exec($ch);
	        if (!$htmdata)
	        {
	            $error = curl_error($ch);
	            $info = curl_getinfo($ch);
	            echo "\tError scraping: $error [ $error ]$NL";
	            $scrape_result = "SCRAPE_ERROR";
	            sleep(3);

	            return null;
	        } else
	        {
	            if (strlen($htmdata) < 20)
	            {
	                $scrape_result = "SCRAPE_EMPTY_SERP";
	                sleep(3);

	                return null;
	            }
	        }
	        $html = new \DOMDocument();
	        libxml_use_internal_errors(true);
	        $html->loadHTML($htmdata);
	        $dom_xpath =new \DOMXPath($html);
	        $res['error_total'] = $dom_xpath->query('//div[@id="results"]/ol/li[@class="error"]')->length;
	        $res['warning_total'] = $dom_xpath->query('//div[@id="results"]/ol/li[@class="info warning"]')->length;
	        return $res;
		} else {
			return null;
		}
	}
    public function searchwithKeyword(Request $request) {
    	global $pwd;
        global $uid;
        global $PROXY;
        global $PLAN;
        global $NL;
        global $working_dir;
    	#!/usr/bin/php
	
	    /* License: 
	       Open source for private and commercial use but this comment needs to stay untouched on top.
	       URL of original source code: http://scraping.compunect.com
	       Author of original source code: http://www.compunect.com
	       IP rotation API code from here: http://www.us-proxies.com/automate
	       Under no circumstances and under no legal theory, whether in tort (including negligence), contract, or otherwise, shall the Licensor be liable to anyone for any direct, indirect, special, incidental, or consequential damages of any character arising as a result of this License or the use of the Original Work including, without limitation, damages for loss of goodwill, work stoppage, computer failure or malfunction, or any and all other commercial damages or losses. This limitation of liability shall not apply to the extent applicable law prohibits such limitation.
	       Usage exceptions:
	       Public redistributing modifications of this source code project is not allowed without written agreement.
	       Using this work for private and commercial projects is allowed, redistributing it is not allowed without our written agreement.
	     */

	    ini_set("memory_limit","64M"); // For scraping 100 results pages 32MB memory expected, for scraping the default 10 results pages 4MB are expected. 64MB is selected just in case.
	    ini_set("xdebug.max_nesting_level","2000"); // precaution, might not be required. our parser will require a deep nesting level but I did not check how deep a 100 result page actually is.
	    error_reporting(E_ALL & ~E_NOTICE);
	    // ************************* Configuration variables *************************
	    // Your api credentials, you need a plan at us-proxies.com
	    // It's optional, you can remove the proxy related parts and just use it as a single-IP tool. Just make sure to implement a request delay of around 3-5 minutes in that case.
	    $pwd = '1502f31c69779f472c5dafbe59f410ce';
	    $uid = '9618';

	    // General configuration
	    $test_website_url = "website.com"; // The URL, or a sub-string of it, of the indexed website.
	    $test_keywords = $request->input("keyword"); // comma separated keywords to test the rank for
	    $test_max_pages = 3; // The number of result pages to test until giving up per keyword.
	    $start_page = $request->input("start_page");
	    $end_page = $request->input("end_page");
	    $test_100_resultpage = 0; // Warning: Google ranking results may  become inaccurate

	    /* Local result configuration. Enter 'help' to receive a list of possible choices. use global and en for the default worldwide results in english 
	     * You need to define a country as well as the language. Visit the Google domain of the specific country to see the available languages.
	     * Only a correct combination of country and language will return the correct search engine result pages. */
	    $test_country = "us"; // Country code. "global" is default. Use "help" to receive a list of available codes. [com,us,uk,fr,de,...]
	    $test_language = "en"; // Language code. "EN" is default Use "help" to receive a list. Visit the local Google domain to find available langauges of that domain. [en,fr,de,...]
	    $filter = 1; // 0 for no filter (recommended for maximizing content), 1 for normal filter (recommended for accuracy)
	    $force_cache = 0; // set this to 1 if you wish to force the loading of cache files, even if the files are older than 24 hours. Set to -1 if you wish to force a new scrape.
	    $load_all_ranks = 1; /* set this to 0 if you wish to stop scraping once the $test_website_url has been found in the search engine results,
	                         * if set to 1 all $test_max_pages will be downloaded. This might be useful for more detailed ranking analysis.*/

	    $show_html = 0; // 1 means: output formated with HTML tags. 0 means output for console (recommended script usage)
	    $show_all_ranks = 1; // set to 1 to display a complete list of all ranks per keyword, set to 0 to only display the ranks for the specified website
	    // ***************************************************************************
	    $working_dir = "./local_cache"; // local directory. This script needs permissions to write into it


	    require "functions-ses.php";


	$page = 0;
	$PROXY = array(); // after the rotate api call this variable contains these elements: [address](proxy host),[port](proxy port),[external_ip](the external IP),[ready](0/1)
	$PLAN = array();
	$results = array();


	if ($show_html) $NL = "<br>\n"; else $NL = "\n";
	if ($show_html) $HR = "<hr>\n"; else $HR = "_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_\n";
	if ($show_html) $B = "<b>"; else $B = "!";
	if ($show_html) $B_ = "</b>"; else $B_ = "!";


	/*
	 * Start of main()
	 */

	if ($show_html)
	{
	    echo "<html><body>";
	}

	$keywords = explode(",", $test_keywords);
	if (!count($keywords)) die ("Error: no keywords defined.$NL");
//	if (!rmkdir($working_dir)) die("Failed to create/open $working_dir$NL");

	$country_data = get_google_cc($test_country, $test_language);
	if (!$country_data) die("Invalid country/language code specified.$NL");


	$ready = get_license();
	if (!$ready) die("The specified API key account for user $uid is not active or invalid. $NL");
	if ($PLAN['protocol'] != "http") die("Wrong proxy protocol configured, switch to HTTP. $NL");

//	echo "$NL$B Search Engine Scraper for $test_website_url initated $B_ $NL$NL";

	/*
	 * This loop iterates through all keyword combinations
	 */
	$ch = NULL;
	$rotate_ip = 0; // variable that triggers an IP rotation (normally only during keyword changes)
	$max_errors_total = 3; // abort script if there are 3 keywords that can not be scraped (something is going wrong and needs to be checked)

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
	    for ($page = $start_page; $page <= $end_page; $page++)
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
	    //    echo "$NL$NL$B" . "Ranking information for keyword \"$keyword\" $B_$NL";
	    //    echo "$B" . "Rank [Type] - Website -  Title$B_$NL";
	        $pos = 0;
	        foreach ($ranks as $rank)
	        {
	            $pos++;
	            if (strstr($rank['url'], $test_website_url))
	            {
	        //        echo "$B$pos [$rank[type]] - $rank[url] - $rank[title] $B_$NL";
	//                    echo $rank['desc']."\n";
	            } else
	            {
	        //        echo "$pos [$rank[type]] - $rank[url] - $rank[title] $NL";
	//                    echo $rank['desc']."\n";
	            }
	            $pos_temp = $pos % 10;
	            $pos_1 = ($pos - $pos_temp) / 10;
	            $pos_temp --;
	            $pos_str = ($start_page + $pos_1 + 1) * 10 + $pos_temp;

	            $domain = $this->getDomainfromUrl($rank['url']);
	            if((Info::where('domain_name',$domain)->get()->count()) == 0) { 
		            $new_info = new Info();
		            $new_info->business_name = $rank['title'];
		            $new_info->domain_name = $domain;
		            $new_info->rank = $pos_str;
		            $new_info->flag = 0;
		            $new_info->black = 0;
		            
		            if($domain != null ) {
		            	$detail_info = $this->getdetailInfo($domain);
			            if($detail_info != null) {
			            	$new_info->admins_name = $detail_info['administrative_contact']['full_name'];
			            	$new_info->email = $detail_info['administrative_contact']['email_address'];
			            	$new_info->phone = $detail_info['administrative_contact']['phone_number'];
			            	$new_info->mailing_address = $detail_info['administrative_contact']['mailing_address'];
			            	$new_info->flag = 1;
			            }

			            $issues = $this->getIssues($domain);
			            $new_info->error_total = $issues['error_total'];
			            $new_info->warning_total = $issues['warning_total'];
		            }
		            
		            $new_info->save();
		        }
	        }
	    }
	}


	foreach ($keywords as $keyword)
	{
	    if (!isset($siterank_data[$keyword]))
	    {
	//        echo "$NL$B" . "The specified site was not found in the search results for keyword \"$keyword\". $B_$NL";
	    } else
	    {
	        $siteranks = $siterank_data[$keyword];
	//        echo "$NL$NL$B" . "Ranking information for keyword \"$keyword\" and website \"$test_website_url\" [$test_country / $test_language] $B_$NL";
	//        foreach ($siteranks as $siterank)
	//            echo "Rank $siterank[rank] for URL $siterank[url]$NL";
	    }
	}
	//var_dump($siterank_data);


	if ($show_html)
	{
	    echo "</body></html>";
	}
	echo 1;
	exit();
	return view('home');
    }
}

