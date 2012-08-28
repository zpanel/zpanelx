DROP TABLE IF EXISTS event_log;
CREATE TABLE event_log (
  id int(11) NOT NULL auto_increment,
  ts date NOT NULL,
  username varchar(250) default NULL,
  server_name varchar(250) default NULL,
  server_port varchar(250) default NULL,
  remote_address varchar(250) default NULL,
  remote_port varchar(250) default NULL,
  query_string varchar(250) default NULL,
  php_self varchar(250) default NULL,
  referer varchar(250) default NULL,
  user_agent varchar(250) default NULL,
  PRIMARY KEY  (`id`),
  KEY ts (ts)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
