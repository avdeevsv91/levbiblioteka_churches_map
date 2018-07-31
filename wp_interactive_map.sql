-- phpMyAdmin SQL Dump
-- version 
-- http://www.phpmyadmin.net
--
-- Хост: levbibliot.mysql
-- Время создания: Апр 14 2017 г., 13:14
-- Версия сервера: 5.6.25-73.1
-- Версия PHP: 5.6.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `levbibliot_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `wp_interactive_map`
--
-- Создание: Апр 01 2017 г., 00:20
--

DROP TABLE IF EXISTS `wp_interactive_map`;
CREATE TABLE IF NOT EXISTS `wp_interactive_map` (
  `point_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `latitude` varchar(10) NOT NULL,
  `longitude` varchar(10) NOT NULL,
  `preset` varchar(64) NOT NULL DEFAULT 'islands#blueWorshipIcon',
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `wp_interactive_map`
--

INSERT INTO `wp_interactive_map` (`point_id`, `title`, `latitude`, `longitude`, `preset`, `url`) VALUES
(4, 'Посёлок Лев Толстой. Храм Святой Троицы', '53.2093', '39.449384', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/lev-tolstoj-xram-svyatoj-troicy/'),
(5, 'Село Большая Карповка. Храмы Архистратига Михаила и Иверской иконы Божией Матери', '53.304081', '39.513429', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/bolshaya-karpovka-xramy-arxistratiga-mixaila-i-iverskoj-ikony-bozhiej-materi/'),
(6, 'Село Гагарино. Храмы святителя Николая Чудотворца и Рождества Христова', '53.342333', '39.701459', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/gagarino-xramy-svyatitelya-nikolaya-chudotvorca-i-rozhdestva-xristova/'),
(7, 'Село Гагино. Храмы Спаса Нерукотворного Образа и Архистратига Михаила', '53.342232', '39.638603', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/gagino-xramy-spasa-nerukotvornogo-obraza-i-arxistratiga-mixaila/'),
(8, 'Село Головинщино. Храмы святителя Алексия, митрополита Московского, Казанской иконы Божией Матери, святых Космы и Дамиана и Преображения Господня', '53.192114', '39.636749', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/golovinshhino-xramy-svyatitelya-aleksiya-mitropolita-moskovskogo-kazanskoj-ikony-bozhiej-materi-svyatyx-kosmy-i-damiana-i-preobrazheniya-gospodnya/'),
(9, 'Село Грязновка. Храм святого Георгия Победоносца', '53.159357', '39.479915', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/gryaznovka-xram-svyatogo-georgiya-pobedonosca/'),
(10, 'Село Домачи. Храм святителя Николая Чудотворца', '53.334125', '39.448915', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/domachi-xram-svyatitelya-nikolaya-chudotvorca/'),
(11, 'Село Загрядчино. Храм Казанской иконы Божией Матери', '53.365047', '39.576798', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/zagryadchino-xram-kazanskoj-ikony-bozhiej-materi/'),
(12, 'Село Знаменское. Храм иконы Божией Матери «Знамение»', '53.288624', '39.396677', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/znamenskoe-xram-ikony-bozhiej-materi-znamenie/'),
(13, 'Село Золотуха. Храм святителя Тихона Задонского, святой Марии Магдалины и святой Марфы', '53.17355', '39.26374', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/zolotuxa-xram-svyatitelya-tixona-zadonskogo-svyatoj-marii-magdaliny-i-svyatoj-marfy/'),
(14, 'Село Астапово. Храм святого Димитрия Солунского', '53.210799', '39.526454', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/astapovo-xram-svyatogo-dimitriya-solunskogo/'),
(15, 'Село Астапово. Храм Покрова Пресвятой Богородицы', '53.210322', '39.52846', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/astapovo-xram-pokrova-presvyatoj-bogorodicy/'),
(16, 'Село Зыково. Храм Покрова Пресвятой Богородицы', '53.343355', '39.777419', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/zykovo-xram-pokrova-presvyatoj-bogorodicy/'),
(17, 'Село Красное Колычёво. Храм Введения во храм Пресвятой Богородицы', '53.132924', '39.47383', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/krasnoe-kolychyovo-xram-vvedeniya-vo-xram-presvyatoj-bogorodicy/'),
(18, 'Село Круглое. Храмы святого Василия Великого и Спаса Нерукотворного Образа', '53.146774', '39.341215', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/krugloe-xramy-svyatogo-vasiliya-velikogo-i-spasa-nerukotvornogo-obraza/'),
(19, 'Село Кузовлево. Храм святого Георгия Победоносца', '53.119798', '39.60653', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/kuzovlevo-xram-svyatogo-georgiya-pobedonosca/'),
(20, 'Село Митягино. Храм Преображения Господня', '53.208699', '39.733994', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/mityagino-xram-preobrazheniya-gospodnya/'),
(21, 'Село Орловка. Храм святого Иакова Алфеева', '53.399051', '39.428935', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/orlovka-xram-svyatogo-iakova-alfeeva/'),
(22, 'Село Острый Камень. Храм святителя Николая Чудотворца', '53.202588', '39.37434', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/ostryj-kamen-xram-svyatitelya-nikolaya-chudotvorca/'),
(23, 'Село Первомайское. Храмы святого Георгия Победоносца и Смоленской иконы Божией Матери', '53.080792', '39.610676', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/pervomajskoe-xramy-svyatogo-georgiya-pobedonosca-i-smolenskoj-ikony-bozhiej-materi/'),
(24, 'Село Племянниково. Храм Казанской иконы Божией Матери', '53.243686', '39.395725', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/plemyannikovo-xram-kazanskoj-ikony-bozhiej-materi/'),
(25, 'Село Свищёвка. Храм Введения во храм Пресвятой Богородицы', '53.289932', '39.457247', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/svishhyovka-xram-vvedeniya-vo-xram-presvyatoj-bogorodicy/'),
(26, 'Село Сланское. Храм святого Сергия Радонежского', '53.114718', '39.387049', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/slanskoe-xram-svyatogo-sergiya-radonezhskogo/'),
(27, 'Село Старочемоданово. Храм Воскресения Христова', '53.277959', '39.66662', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/starochemodanovo-xram-voskreseniya-xristova/'),
(28, 'Село Топки. Храм Богоявления Господня', '53.33657', '39.586272', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/topki-xram-bogoyavleniya-gospodnya/'),
(29, 'Село Троицкое. Храм Святой Троицы', '53.185339', '39.600846', 'islands#blueWorshipIcon', 'http://www.levbiblioteka.ru/churches_map/troickoe-xram-svyatoj-troicy/');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `wp_interactive_map`
--
ALTER TABLE `wp_interactive_map`
  ADD PRIMARY KEY (`point_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `wp_interactive_map`
--
ALTER TABLE `wp_interactive_map`
  MODIFY `point_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
