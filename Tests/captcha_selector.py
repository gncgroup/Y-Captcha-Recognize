import sqlite3
import re
import sys
import os
import os.path
import time

input_dir = '/img/'
file_list  = os.listdir(os.getcwd()+input_dir)

DATABASE = os.path.join(os.path.abspath(os.path.split(__file__)[0]), 'captcha.db')
connection = sqlite3.connect(DATABASE)
cursor = connection.cursor()
#cursor.execute("""SELECT *, length(сorrect_text) FROM captcha WHERE LENGTH(сorrect_text) < 4 """)
#cursor.execute("""SELECT *, length(сorrect_text) FROM captcha WHERE LENGTH(сorrect_text) > 7 """)
#cursor.execute("""SELECT *, length(сorrect_text) FROM captcha WHERE сorrect_text LIKE '%[^a-zA-Z0-9]%' """)
#cursor.execute("""SELECT id, сorrect_text, count(*) FROM captcha group by сorrect_text having count(*) > 1 order by count(*)""")
cursor.execute("""SELECT COUNT(*) FROM captcha WHERE (сorrect_text = recognate_text)""")

rows = cursor.fetchall()
connection.close()
for item in rows:
    print(item[0])
