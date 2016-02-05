# -*- coding: utf-8 -*-

import sqlite3
import re
import sys
import os
import os.path
import time
import subprocess
import requests
import urllib

DATABASE = os.path.join(os.path.abspath(os.path.split(__file__)[0]), 'captcha.db')
connection = sqlite3.connect(DATABASE)
cursor = connection.cursor()
# problems
"""
<b>Warning</b>:  imagecreatetruecolor(): Invalid image dimensions in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>100</b><br />
<br />
<b>Warning</b>:  imagecopy() expects parameter 1 to be resource, boolean given in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>101</b><br />
<br />
<b>Warning</b>:  imagecolorat() expects parameter 1 to be resource, boolean given in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>103</b><br />
<br />
<b>Warning</b>:  imagesx() expects parameter 1 to be resource, boolean given in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>53</b><br />
<br />
<b>Warning</b>:  imagesy() expects parameter 1 to be resource, boolean given in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>53</b><br />
<br />
<b>Warning</b>:  imagecopyresampled() expects parameter 2 to be resource, boolean given in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>53</b><br />
<br />
<b>Warning</b>:  imagecreatetruecolor(): Invalid image dimensions in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>115</b><br />
<br />
<b>Warning</b>:  imagecolorallocate() expects parameter 1 to be resource, boolean given in <b>/var/www/html/captcha_recognize/Recognize.php</b> on line <b>116</b><br />
<br />
"""
#cursor.execute("""SELECT *, length(сorrect_text) FROM captcha WHERE id IN (107,114,172,200,207,630,882,1034,1125,1255,1313,1531,1587,1896,1934,1977,2159,2175,2305,3282,3395,3441,3615,3846,3914,4183,4452,4510,4674,4822,5038,5050,5199,5301,5455,5612,5658,5707,5763,5856,5913,6151,6152,6196,6197,6232,6272,6541,6589,6723,6809,6919,6991,7161,7218,7742,7788,7808,7849,8111,8208,8245,8265,8324,8336,8726,8761,8811,9020) """)
cursor.execute("""SELECT *, length(сorrect_text) FROM captcha """)
rows = cursor.fetchall()
connection.close()

connection = sqlite3.connect(DATABASE)
cursor = connection.cursor()
for item in rows:
    img_id = item[0]
    img_name = item[1]
    url = 'http://localhost/captcha_recognize/example.php?img_name='+img_name+''
    s = requests.Session()
    content = s.get(url)
    recognate_text = content.text.strip()
    if len(recognate_text) > 10:
        recognate_text = ""
    cursor.execute("""UPDATE captcha SET recognate_text = ? WHERE id = ? """, (recognate_text, img_id))
    connection.commit()
    print(img_id, len(content.text.strip()))
    
connection.close()