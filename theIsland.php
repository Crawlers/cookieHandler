<html>
<head>
<title>the island</title>
</head>
<body>
<?php
include "lib.php";
error_reporting(0);

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
else{
        echo "proxy for cookie handing is working<br>";
        echo "use a URL with format '<a><b>http://".$_SERVER[HTTP_HOST].$_SERVER['REQUEST_URI']."?newsfordate=date/month/year</b></a>' to browse news for different dates<br>";
        $url = "http://".$_SERVER[HTTP_HOST].$_SERVER['REQUEST_URI']."?newsfordate=1/1/2013";
        echo "eg: '<a href='".$url."'><b>".$url."</b>'</a><br>";
}
?>
</body>
</html>
