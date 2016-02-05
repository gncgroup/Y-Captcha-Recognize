import sqlite3
import os.path
import sys

DATABASE = os.path.join(os.path.abspath(os.path.split(__file__)[0]), 'captcha.db')

connection = sqlite3.connect(DATABASE)
cursor = connection.cursor()

cursor.execute('''
    CREATE TABLE captcha (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        img_name TEXT,
        сorrect_text TEXT,
        recognate_text TEXT
    )
''')

connection.commit()
connection.close()