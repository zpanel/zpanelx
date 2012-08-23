/* If the table exists uncomment the following */
/* drop table calendar;*/
CREATE TABLE calendar (
    id int auto_increment NOT NULL,
    username varchar(250),
    `datetime` datetime, 
    duration float,
    title varchar(250),
    description text,
    repeat_val integer,
    PRIMARY KEY id (id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8
