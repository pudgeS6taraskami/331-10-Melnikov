import csv
import psycopg2

def import_partners(csv_path, db_conn):
    with open(csv_path, newline='', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile)
        with db_conn.cursor() as cur:
            for row in reader:
                cur.execute('''
                    INSERT INTO partners (type, name, director, email, phone, address, inn, rating)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                ''', (row['Тип партнера'], row['Наименование партнера'], row['Директор'],
                      row['Электронная почта партнера'], row['Телефон партнера'],
                      row['Юридический адрес партнера'], row['ИНН'], row['Рейтинг']))
        db_conn.commit()
