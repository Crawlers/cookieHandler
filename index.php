<html>
<head>
<title>the island</title>
</head>
<body>
<?php
error_reporting(0);

//function for handle requests
function fetch( $url, $z=null ) {
            $ch =  curl_init();

            $useragent = isset($z['useragent']) ? $z['useragent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';

            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt( $ch, CURLOPT_POST, isset($z['post']) );

            if( isset($z['post']) )         curl_setopt( $ch, CURLOPT_POSTFIELDS, $z['post'] );
            if( isset($z['refer']) )        curl_setopt( $ch, CURLOPT_REFERER, $z['refer'] );

            curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, ( isset($z['timeout']) ? $z['timeout'] : 10 ) );
            curl_setopt( $ch, CURLOPT_COOKIEJAR,  $z['cookiefile'] );
            curl_setopt( $ch, CURLOPT_COOKIEFILE, $z['cookiefile'] );

            $result = curl_exec( $ch );
            curl_close( $ch );
            return $result;
    }
    
   
if (isset($_GET['newsfordate'])){
        //create temp file
        $tmpfname = tempnam (getcwd()."/tmp", "cookie" ); 
        
        //get date
        $date = $_GET['newsfordate'];
        
        //first request to choose date
        $z = array();
        $z['post'] = "mAction=mEventLoadIssueByDate&mdate=".$date;
        $z['cookiefile'] = $tmpfname;
        $url = "http://www.island.lk/site_controler.php";
        fetch($url,$z);
        
        //second request to get all news for the date
        $url = "http://www.island.lk";
        unset($z['post']);
        $result = fetch($url,$z);
        
        $dom = new DOMDocument();
        $dom->loadHTML($result);
        $as = $dom->getElementsByTagName('a');
        $count = 0;
        $outputUrl = "";
        foreach ($as as $a) {
                if (trim($a->nodeValue) == "News"){
                $href = $a->attributes->getNamedItem("href");
                        if (strpos($href->value,
                                "http://www.island.lk/index.php?page_cat=news-section&page=news-section&code_title=")===0) {
                                $outputUrl =  $href->value;
                                break;
                        }
                }
        }
        
        if ($outputUrl == ""){
                echo "no news tag found";
        } else {
                //3rd request to get only news for category News
                $result =  fetch($outputUrl,$z);
                echo $result;
        }
        unlink($tmpfname);
}
?>
</body>
</html>
