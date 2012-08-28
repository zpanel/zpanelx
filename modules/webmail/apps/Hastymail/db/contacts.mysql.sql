CREATE TABLE `contacts` (
    id int auto_increment NOT NULL,
    username varchar(250),
    contacts longtext,
    PRIMARY KEY (id),
    KEY username (username(10))
) ENGINE=MyISAM DEFAULT CHARSET=UTF8
