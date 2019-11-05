CREATE TABLE `person_experience` (
  `id` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `value` float NOT NULL DEFAULT 0,
  `notes` char(30) DEFAULT NULL,
  `inserted` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `person_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`person_id`),
  ADD KEY `inserted` (`inserted`),
  ADD KEY `experience_id` (`rule_id`) USING BTREE;

ALTER TABLE `person_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

CREATE TABLE `person_experience_rules` (
  `id` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `value` float NOT NULL,
  `description` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `person_experience_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

ALTER TABLE `person_experience_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

INSERT INTO `person_experience_rules` (`code`, `value`, `description`) VALUES
('APP_FIRST_DAILY', 1, 'Usar la app por primera vez en el día'),
('SERVICE_FIRST', 2, 'Abrir un servicio por primera vez'),
('PIZARRA_POST_FIRST_DAILY', 1, 'Publicar en Pizarra por primera vez en el día'),
('PIZARRA_COMMENT_FIRST_DAILY', 1, 'Comentar en Pizarra por primera vez en el día'),
('COUPON_EXCHANGE', 3, 'Canjear un cupón'),
('ITEM_BUY', 3, 'Realizar una compra'),
('INVITE_FRIEND', 3, 'Invitar a un amig@ a usar la app'),
('FINISH_CHALLENGE', 1, 'Terminar un reto'),
('FINISH_SURVEY', 3, 'Completar una encuesta'),
('FINISH_COURSE', 5, 'Terminar un curso en Escuela'),
('RAFFLE_BUY_FIRST_TICKET', 5, 'Comprar el primer ticket para la Rifa actual'),
('NEWS_COMMENT_FIRST_DAILY', 2, 'Opinar en las noticias por primera vez en el día'),
('PIROPAZO_MATCH', 3, 'Conseguir pareja en Piropazo'),
('START_CHAT_FIRST', 2, 'Chatear con un usuario por primera vez'),
('USE_APP_ALL_WEEK', 10, 'Usar la app 7 días consecutivos'),
('FINISH_PROFILE_FIRST', 10, 'Llenar tu perfil por primera vez');
