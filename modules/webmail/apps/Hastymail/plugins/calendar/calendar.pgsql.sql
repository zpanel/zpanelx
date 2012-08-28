DROP TABLE calendar;
CREATE TABLE calendar (
    id serial,
    username varchar(250),
    datetime timestamp, 
    duration float,
    title varchar(250),
    description text,
    repeat_val int
)
