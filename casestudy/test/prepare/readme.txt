time php mkdummy_users.php    > ~/users.tsv
time php mkdummy_articles.php > ~/articles.tsv
time php mkdummy_likes.php    > ~/likes.tsv

[www@ip-172-31-5-109 prepare]$ wc -l ~/*.tsv
   3650000 /home/www/articles.tsv
  36500000 /home/www/likes.tsv
    100000 /home/www/users.tsv
  40250000 total

# RDS for Mysql
mysql -h taru8test.cayhlkuryzts.ap-northeast-1.rds.amazonaws.com -u root -pmysqlroot -D casestudy_1

LOAD DATA LOCAL INFILE '~/workspace/users.tsv'    IGNORE INTO TABLE users;
LOAD DATA LOCAL INFILE '~/workspace/articles.tsv' IGNORE INTO TABLE articles;
LOAD DATA LOCAL INFILE '~/workspace/likes.tsv'    IGNORE INTO TABLE likes;



mysql> LOAD DATA LOCAL INFILE '~/users.tsv' IGNORE INTO TABLE users;
Query OK, 0 rows affected (0.23 sec)
Records: 100000  Deleted: 0  Skipped: 100000  Warnings: 0

mysql> LOAD DATA LOCAL INFILE '~/articles.tsv' IGNORE INTO TABLE articles;
Query OK, 3641414 rows affected, 65535 warnings (39.50 sec)
Records: 3650000  Deleted: 0  Skipped: 8586  Warnings: 7300000


# Aurora
mysql -h taru8test-aurora.cayhlkuryzts.ap-northeast-1.rds.amazonaws.com -u root -pmysqlroot -D casestudy_1
db.r3.large
LOAD DATA는mysql보다 느림
