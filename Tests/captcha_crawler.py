import sys
from bs4 import BeautifulSoup
import requests
import urllib
from urllib.parse import urlparse
from urllib.parse import parse_qs
from socket import timeout
import re
import os
import os.path

for inx in range(0,10001):
    url = 'https://passport.yandex.ru/registration'
    s = requests.Session()
    content = s.get(url)
    soup = BeautifulSoup(content.text, "lxml")
    captcha_url = soup.find('img',  {"class": "captcha__captcha__text"}).attrs['src']
    captcha_url_parsed = urlparse(captcha_url)
    img_key = parse_qs(captcha_url_parsed.query)['key'][0]
    img_filename = img_key + '.gif'
    path = os.path.dirname(os.path.abspath(__file__))
    imgpath = os.path.join(path, 'img/', img_filename)
    img_source = urllib.request.urlopen(captcha_url).read()
    s = open(imgpath, "wb")
    s.write(img_source)
    s.close()