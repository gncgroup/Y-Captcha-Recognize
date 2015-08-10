<?php
        header('Content-Type: text/html; charset=utf-8');
        require_once('Recognize.php');

        $yc = new Recognize("test.gif");
        $res=$yc->recognize();
        $word="";
        for($t=0;$t<count($res);$t++)
                $word=$word.$res[$t][0];
        print "Possible results:<br>";
        print_r($res);
        print "<br>Result:<br>";
        print $word;
