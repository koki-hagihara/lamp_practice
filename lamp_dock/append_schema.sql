CREATE TABLE order_history (
    order_number int(11) NOT NULL AUTO_INCREMENT,
    user_id int(11),
    order_date datetime,
    primary key(order_number)
    );

CREATE TABLE order_details (
    order_number int(11),
    item_id int(11),
    amount int(11)
    );