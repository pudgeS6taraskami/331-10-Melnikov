import csv
import psycopg2

def load_partners_from_csv(csv_file_path, database_connection):
    with open(csv_file_path, newline='', encoding='utf-8') as csv_file:
        csv_reader = csv.DictReader(csv_file)
        with database_connection.cursor() as cursor:
            for record in csv_reader:
                cursor.execute('''
                    INSERT INTO partners (type, name, director, email, phone, address, inn, rating)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                ''', (
                    record['Тип партнера'],
                    record['Наименование партнера'],
                    record['Директор'],
                    record['Электронная почта партнера'],
                    record['Телефон партнера'],
                    record['Юридический адрес партнера'],
                    record['ИНН'],
                    record['Рейтинг']
                ))
        database_connection.commit()
