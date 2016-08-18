CREATE TABLE IF NOT EXISTS `users` (
`userId` int(11) NOT NULL AUTO_INCREMENT,
`userName` varchar(30) NOT NULL,
`userEmail` varchar(60) NOT NULL,
`userPass` varchar(255) NOT NULL,
PRIMARY KEY (`userId`),
UNIQUE KEY `userEmail` (`userEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
