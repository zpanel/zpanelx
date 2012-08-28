USE mysql;
DELETE FROM user WHERE User = "" OR Host = "127.0.0.1" OR Host = "::1";
FLUSH PRIVILEGES;