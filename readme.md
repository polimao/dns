```
CREATE TABLE `domains` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(6) NOT NULL DEFAULT '',
  `word_len` int(11) NOT NULL,
  `pinyin` varchar(22) NOT NULL DEFAULT '',
  `pinyin_len` int(11) NOT NULL,
  `available` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `entry_cnt` int(11) NOT NULL,
  `query_cnt` int(11) NOT NULL,
  `query_status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70480 DEFAULT CHARSET=utf8;

```
