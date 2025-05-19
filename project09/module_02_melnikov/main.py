from src.module1.import_partners import import_partners
from src.module2.calculate_discounts import calculate_discount
import psycopg2

#Установка соединения с базой данных
database_connection = psycopg2.connect(
    host='localhost',
    dbname='partners',
    user='user',
    password='password'
)

#Импорт партнеров из CSV файла
import_partners('data/partners.csv', database_connection)

#Вычисление и вывод скидки
discount_result = calculate_discount(7, 2.35)
print(discount_result)

