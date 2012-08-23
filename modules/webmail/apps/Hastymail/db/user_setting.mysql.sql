CREATE TABLE `user_setting` (
    id int auto_increment NOT NULL,
    username varchar(250),
    settings text,
    PRIMARY KEY (id),
    KEY username (username(10))
) ENGINE=MyISAM DEFAULT CHARSET=UTF8
