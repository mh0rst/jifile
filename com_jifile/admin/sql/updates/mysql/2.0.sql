-- drop table `#__jifiledocuments`;

CREATE TABLE `#__jifiledocuments` (
  `keyid` varchar(255) NOT NULL COMMENT 'Identificativo univoco del documento',
  `delete` tinyint(1) NOT NULL default '0' COMMENT 'Cancellazione Logica del documento',
  PRIMARY KEY  (`keyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- drop table `#__jifileaddon`;

CREATE TABLE `#__jifileaddon` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id addon',
  `addon` varchar(255) NOT NULL COMMENT 'identificativo univoco dell''addon, composto da Context.NomeUnivoco (il nome univoco, deve essere quello della cartella dell''addon)',
  `core` tinyint(1) NULL COMMENT 'Addon Core',
  `rules` varchar(255) NULL COMMENT 'Definisce il ruolo (Action - ACL) per accedere alla funzionalità',
  `context` varchar(255) NOT NULL default 'admin' COMMENT 'admin / site (permette di filtrare per add-on di front o back)',
  `image` varchar(255) default NULL COMMENT 'Immagine da visualizzare nella Frontpage di backend',
  `type` tinyint(4) NOT NULL default '0' COMMENT '0: componente Joomla! – 1: link esterno - 2: Plugin Interno di JiFile per le Componenti',
  `option` varchar(255) default NULL COMMENT 'Definisce la option (componenete) da richiamare',
  `task` varchar(255) default NULL COMMENT 'Controller da richiamare (esempio: addon.#)',
  `view` varchar(255) default NULL COMMENT 'View da richiamare',
  `template` varchar(255) default NULL COMMENT 'Template da richiamare',
  `onclick` varchar(255) default NULL COMMENT 'Eventuale chiamata javascript sul onclick',
  `link` varchar(4000) default NULL COMMENT 'Permette di richiamare un link esterno o componente in Joomla!',
  `target` varchar(255) default NULL COMMENT 'Definisce il target per i link esterni',
  `title` varchar(255) default NULL COMMENT 'Titolo da  visualizzare sotto l’icona della funzionalità',
  `description` varchar(4000) default NULL COMMENT 'Descrizione dell''add-on, da visualizzare nella lista degli Add-on installati',
  `dtinstall` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Data di installazione',
  `published` tinyint(1) NOT NULL default '1' COMMENT 'Serve per non pubblicare e quindi usare l’add-on. Forse non necessario ',
  `delete` tinyint(1) NOT NULL default '1' COMMENT 'Se valorizzato a Zero non sarà possibile cancellare l''addon', 
  `ordering` int(11) NOT NULL COMMENT 'Ordine di visualizzazione degli addon',
  `author` varchar(255) default NULL COMMENT 'Autore della Addon',
  `version` varchar(255) default NULL COMMENT 'Versione della Addon',
  `plugin` varchar(4000) default NULL COMMENT 'Plugin, l’add-on installato potrebbe intervenire anche nella fase di ricerca.',
  `manifest_cache` text NOT NULL COMMENT 'json xml installazione',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `addon` (`addon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `#__jifileaddon` (`id`, `addon`, `core`, `rules`, `context`, `image`, `type`, `option`, `task`, `view`, `template`, `onclick`, `link`, `target`, `title`, `description`, `dtinstall`, `published`, `delete`, `ordering`, `author`, `version`, `plugin`) VALUES
(2, 'admin.support', 1, '', 'admin', 'components/com_jifile/images/icons/help_64.png', 1, NULL, '', NULL, NULL, NULL, 'http://www.isapp.it/forum/isapp-it-jifile-joomla.html', '_blank', 'Support', 'JIFILE_SUPPORT_DESC', '2011-11-22 00:00:00', 1, 0, 2, 'isApp.it', '2.0', NULL),
(3, 'admin.synchronize', 1, 'core.admin', 'admin', 'components/com_jifile/images/icons/refresh_64.png', 0, 'com_jifile', 'synchronize.', '', 'component', NULL, NULL, NULL, 'SYNC', 'JIFILE_SYNCHRONIZE_DESC', '2011-11-22 00:00:00', 1, 0, 3, 'isApp.it', '2.0', NULL),
(4, 'admin.configuration', 1, 'core.admin', 'admin', 'components/com_jifile/images/icons/config_64.png', 0, 'com_jifile', 'config.', NULL, NULL, NULL, NULL, NULL, 'Config', 'JIFILE_CONFIG_DESC', '2011-11-22 00:00:00', 1, 0, 4, 'isApp.it', '2.0', NULL),
(5, 'admin.index', 1, 'core.index', 'admin', 'components/com_jifile/images/icons/index_64.png', 0, 'com_jifile', 'lucene.', NULL, NULL, NULL, NULL, NULL, 'Index', 'JIFILE_INDEX_DESC', '2011-11-22 00:00:00', 1, 0, 5, 'isApp.it', '2.0', NULL),
(6, 'admin.filesystem', 1, 'core.filesystem', 'admin', 'components/com_jifile/images/icons/filesystem_64.png', 0, 'com_jifile', 'filesystem.', NULL, NULL, NULL, NULL, NULL, 'Filesystem', 'JIFILE_FILESYSTEM_DESC', '2011-11-22 00:00:00', 1, 0, 6, 'isApp.it', '2.0', NULL);

-- (1, 'admin.addon', 1, 'core.admin', 'admin', 'components/com_jifile/images/icons/addon_64.png', 0, 'com_jifile', 'addon.', NULL, NULL, NULL, '', '', 'Addon', 'JIFILE_ADDON_DESC', '2011-11-22 00:00:00', 1, 0, 1, 'isApp.it', '2.0', NULL),