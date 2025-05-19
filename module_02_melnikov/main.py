from src.module1.import_partners import import_partners
from src.module2.calculate_discounts import calculate_discount
import psycopg2

conn = psycopg2.connect(host='localhost', dbname='partners', user='user', password='password')
import_partners('data/partners.csv', conn)
print(calculate_discount(7, 2.35))
