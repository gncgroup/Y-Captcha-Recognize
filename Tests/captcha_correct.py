import sqlite3
import re
import sys
import os
import os.path
import time

def str_lower(string):
    return string.lower()

DATABASE = os.path.join(os.path.abspath(os.path.split(__file__)[0]), 'captcha.db')
connection = sqlite3.connect(DATABASE)
connection.create_function("str_lower", 1, str_lower)
cursor = connection.cursor()
#cursor.execute("""UPDATE captcha SET сorrect_text = ? WHERE id = ? """, ("дымо", "5947"))
cursor.execute("""UPDATE captcha SET сorrect_text = str_lower(сorrect_text) """)
connection.commit()
connection.close()