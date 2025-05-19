def calculate_discount(rating, product_coef):
    base_discount = rating * 0.5
    return round(base_discount * product_coef, 2)
