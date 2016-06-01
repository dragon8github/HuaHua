-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 05 月 23 日 11:17
-- 服务器版本: 5.5.40
-- PHP 版本: 5.4.33

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `huahua`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(20) NOT NULL COMMENT '管路员账号',
  `admin_password` varchar(20) NOT NULL COMMENT '管理员密码',
  `r` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `answer_details`
--

CREATE TABLE IF NOT EXISTS `answer_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL COMMENT '问题ID',
  `user_id` varchar(255) NOT NULL COMMENT '答题用户ID',
  `flag` varchar(2) NOT NULL COMMENT '0：错误 1：答对',
  `answer_time` varchar(10) NOT NULL COMMENT '答题时间',
  `content` varchar(100) DEFAULT NULL,
  `daoju1` varchar(2) NOT NULL DEFAULT '0' COMMENT '道具1',
  `daoju2` varchar(1) NOT NULL DEFAULT '0' COMMENT '道具2',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- 转存表中的数据 `answer_details`
--

INSERT INTO `answer_details` (`id`, `question_id`, `user_id`, `flag`, `answer_time`, `content`, `daoju1`, `daoju2`) VALUES
(44, 259, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '0', '1463973178', '测试一下', '0', '0'),
(43, 259, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '0', '1463972702', '测试一下', '0', '0');

-- --------------------------------------------------------

--
-- 表的结构 `prop`
--

CREATE TABLE IF NOT EXISTS `prop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tips` varchar(50) NOT NULL,
  `release_time` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL COMMENT '画主ID',
  `answer` varchar(20) NOT NULL COMMENT '答案',
  `question_pic` varchar(200) NOT NULL COMMENT '问题图片',
  `prop` varchar(100) DEFAULT NULL COMMENT '道具花费占金额的比例',
  `price` float DEFAULT NULL COMMENT '问题奖励金额',
  `price_count` float DEFAULT NULL COMMENT '总金额',
  `release_time` varchar(10) NOT NULL,
  `expire_time` varchar(10) NOT NULL,
  `flag` varchar(2) NOT NULL DEFAULT '0' COMMENT '0：可答题 1：已过期',
  `hongbao_count` int(11) NOT NULL,
  `shengyu_count` int(11) NOT NULL DEFAULT '0' COMMENT '剩余红包数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=261 ;

--
-- 转存表中的数据 `question`
--

INSERT INTO `question` (`id`, `uid`, `answer`, `question_pic`, `prop`, `price`, `price_count`, `release_time`, `expire_time`, `flag`, `hongbao_count`, `shengyu_count`) VALUES
(258, 'oQUN4xPpV4ArFvaq_GCM1ob0UT-g', '2', 'http://huahua.ncywjd.com/Upload/HuaHua/20160522/5741ccfca9527.jpeg', '0.1', 1, 1, '1463930108', '1464016508', '0', 1, 0),
(259, 'oQUN4xPpV4ArFvaq_GCM1ob0UT-g', '7', 'http://huahua.ncywjd.com/Upload/HuaHua/20160523/57425ca0a581e.jpeg', '0.1', 1, 1, '1463966880', '1464053280', '0', 1, 0),
(260, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '10', 'http://huahua.ncywjd.com/Upload/HuaHua/20160523/57425f7bad230.jpeg', '0.1', 1, 1, '1463967611', '1464054011', '0', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `question_library`
--

CREATE TABLE IF NOT EXISTS `question_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `answer` varchar(20) COLLATE utf8_estonian_ci NOT NULL COMMENT '答案',
  `tips` text COLLATE utf8_estonian_ci COMMENT '提示文字',
  `release_time` varchar(10) COLLATE utf8_estonian_ci NOT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_estonian_ci AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `question_library`
--

INSERT INTO `question_library` (`id`, `answer`, `tips`, `release_time`) VALUES
(1, '金蝉脱壳', '红叶似火金色彩明丽迷惑不解狼吞蝉虎咽梦寐以求走街串脱巷将心比心壳若无其事', '1462515943'),
(3, '偷天换日', '见死不救偷鱼贯而出天愚不可及响彻云霄换欲出又止随心所欲日枯枝烂叶异想天开', '1462515943'),
(5, '万箭穿心', '机毁人亡轻而易举万杂草丛生以防万一箭永驻人间不速之客穿花繁叶茂心不假思索', '1462515943'),
(7, '学富五车', '与世长学辞重见天日傲然挺立怦然富一震月明人静极目五远眺膘肥体车壮辽阔无垠', '1462515943'),
(9, '负荆请罪', '如果今紧大负号不接搜冻结荆安静大少了请管理科公平考评及立法罪及东方红给偶加', '1462515943'),
(11, '纸上谈兵', '提偶日共河按纸时灯红酒绿居然上狗屁哈哈单防就哦破谈听不肯定会业务员而日兵提', '1462515943'),
(2, '叶公好龙', '焕新名叶词的付款量哥发考虑公更量哥看付过款如果好日提为龙了让看看吗西欧覅欧', ''),
(4, '投鼠忌器', '投下的风口票狗日鼠为日飞继续还给入款我提忌居民是肯定会给器的各位老人婆独立是', ''),
(6, '四面楚歌', '去问他要四图与平哦破婆婆哦婆婆阿斯顿发到梵面蒂冈的和东方红楚瑞份非冯绍峰歌', ''),
(8, '马到成功', '大家马卡扎菲内存吧以文图到耳机孔老婆吗斯蒂芬问发成卡萨打了个都没维尔康功邹平', ''),
(10, '飞龙在天', '阿尔西飞开始了是您的阔以普品溶液看森动龙客服利润率的控股裤子在卡洛斯天的', ''),
(12, '一诺千金', '大家一可没看上楼费开闸莫斯科待会诺就考出来点价格太尼玛千搜到返点金可没显卡', '');

-- --------------------------------------------------------

--
-- 表的结构 `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `answer_limit_time` int(10) DEFAULT '5' COMMENT '答题限制时间',
  `change_next_question_time` int(10) NOT NULL DEFAULT '300' COMMENT '切换题目时间',
  `hongbao_timeout` int(10) NOT NULL COMMENT '红包过期时间 单位小时',
  `prop` float NOT NULL COMMENT '道具的价格占总金额的比例',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `setting`
--

INSERT INTO `setting` (`id`, `answer_limit_time`, `change_next_question_time`, `hongbao_timeout`, `prop`) VALUES
(1, 60, 60, 24, 0.1);

-- --------------------------------------------------------

--
-- 表的结构 `statements`
--

CREATE TABLE IF NOT EXISTS `statements` (
  `id` varchar(255) NOT NULL,
  `type` varchar(2) NOT NULL COMMENT '1画主充值2使用道具3道具收益4提现5猜主收益6红包退回',
  `price` float NOT NULL COMMENT '设计金额',
  `happen_time` varchar(10) NOT NULL COMMENT '发生时间',
  `uid` varchar(255) NOT NULL,
  `flag` varchar(2) NOT NULL DEFAULT '0' COMMENT '0未支付1支付完成2支付失败',
  `bid` varchar(255) NOT NULL COMMENT '收益者的id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `statements`
--

INSERT INTO `statements` (`id`, `type`, `price`, `happen_time`, `uid`, `flag`, `bid`) VALUES
('13289208015741d2f9279f5', '2', 0.1, '1463931641', 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '1', 'oQUN4xPpV4ArFvaq_GCM1ob0UT-g'),
('132892080157425ca38acdf', '1', 1, '1463966883', 'oQUN4xPpV4ArFvaq_GCM1ob0UT-g', '1', ''),
('132892080157425f7ebc654', '1', 1, '1463967614', 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '1', ''),
('132892080157425fab2b6fe', '2', 0.1, '1463967659', 'oQUN4xPpV4ArFvaq_GCM1ob0UT-g', '1', 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs'),
('574271ef1ffe3', '5', 1, '1463972335', 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '0', '');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(30) NOT NULL COMMENT '公众号标识',
  `wx_name` varchar(32) DEFAULT NULL COMMENT '微信名',
  `wx_litpic` varchar(200) DEFAULT NULL COMMENT '微信头像',
  `balance` float NOT NULL DEFAULT '0' COMMENT '用户余额',
  `register_time` varchar(10) NOT NULL COMMENT '注册时间',
  `update_time` varchar(10) NOT NULL COMMENT '题库刷新时间',
  `question` varchar(200) NOT NULL COMMENT '当前题库',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `openid`, `wx_name`, `wx_litpic`, `balance`, `register_time`, `update_time`, `question`) VALUES
(1, '123', '', '', 0, '1463129600', '1463372298', '5,7,9,11,4,6,8,12'),
(2, '456', '', '', 0, '1463143854', '1463237788', '9,12,4,5,1,11,8,7,6,3'),
(3, '789', '', '', 0, '1463237803', '1463238202', '9,11,4,6,10,2,3,1,7,12'),
(5, '341', '', '', 0, '1463319524', '1463362282', '1,8'),
(7, '000', '', '', 0, '1463397950', '1463399213', '1,3,7,9,11,2,4,8,10,12'),
(8, '100', '', '', 0, '1463399288', '1463472134', '1,5,7,11,2,4,6,8,10,12'),
(15, '999', '', '', 0, '1463537106', '1463538448', '11,12'),
(19, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '李钊鸿', 'http://wx.qlogo.cn/mmopen/ficCQMgzCd1j85R9jDHHZ5pgdVpKwLdnOjjruDWFhOvyGzPCLefaecT4gvUGScORewL94rDeTOCI9Ic3fH45APqWxKcCtYTaq/0', 0, '1463620880', '1463820423', '2,10'),
(20, 'oQUN4xPpV4ArFvaq_GCM1ob0UT-g', 'KO博', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDb9ibQBPtvuibBLuRTW7kk4ALIhMHwwnVkmZiaR3Ticcia5owqy01Dg0FFrStQUwg70PLQwDcZSObrEbqXJY33NqrSYGZIzMmwbOT4/0', 0, '1463623013', '1463630764', '8,7,4,10,12,6,3,1,11,5'),
(21, 'oQUN4xOM_E4bTyFXez_ulLfpXCyo', '粉 葛', 'http://wx.qlogo.cn/mmopen/1Ty6AkoCpoDjglydKZiace13Fn7ktSFeEvNz0Uzqb8iblRNicuNWibnhdge3zeXUZXO2kHNNdxHuz3Xuu1zho1coofP0Cu6FXzHj/0', 0, '1463623837', '1463623837', '8,1,4,6,7,9,12,3,2,11'),
(22, 'oQUN4xDlh3OkmDYzGhW5ts-3e52Q', '凡人•爱喝茶爱看书', 'http://wx.qlogo.cn/mmopen/bs78ASf438WhVvKunmsryREYLuKnhiacFh7O8QfibgGUhftvMhpPEvyFn2nDBlFFME8ib91r7cQOWrl3ib8l9hIrlXbFPLaC3aEc/0', 0, '1463624702', '1463624702', '12,2,4,9,11,1,6,8,7,10'),
(23, 'oQUN4xBbL9W8NIssNKmdVtwhNQB8', 'Qiao', 'http://wx.qlogo.cn/mmopen/1Ty6AkoCpoDjglydKZiace1byGogiaFsZbQ4zkDECdZHO8O3tgjsAtJKAVqbdqmmdnfd4QOC7xuhtBmoMnfzJxxdBlSKfDTovy/0', 0, '1463750845', '1463750845', ''),
(24, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '李钊鸿', 'http://wx.qlogo.cn/mmopen/ficCQMgzCd1j85R9jDHHZ5pgdVpKwLdnOjjruDWFhOvyGzPCLefaecT4gvUGScORewL94rDeTOCI9Ic3fH45APqWxKcCtYTaq/0', 0, '1463819824', '1463820423', '2,10'),
(25, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '李钊鸿', 'http://wx.qlogo.cn/mmopen/ficCQMgzCd1j85R9jDHHZ5pgdVpKwLdnOjjruDWFhOvyGzPCLefaecT4gvUGScORewL94rDeTOCI9Ic3fH45APqWxKcCtYTaq/0', 0, '1463820434', '1463820434', ''),
(26, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '李钊鸿', 'http://wx.qlogo.cn/mmopen/ficCQMgzCd1j85R9jDHHZ5pgdVpKwLdnOjjruDWFhOvyGzPCLefaecT4gvUGScORewL94rDeTOCI9Ic3fH45APqWxKcCtYTaq/0', 0, '1463820501', '1463820501', ''),
(27, 'oQUN4xCf4MAyf8nOEltPmgLoi9Fs', '李钊鸿', 'http://wx.qlogo.cn/mmopen/ficCQMgzCd1j85R9jDHHZ5pgdVpKwLdnOjjruDWFhOvyGzPCLefaecT4gvUGScORewL94rDeTOCI9Ic3fH45APqWxKcCtYTaq/0', 0, '1463820594', '1463820594', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
