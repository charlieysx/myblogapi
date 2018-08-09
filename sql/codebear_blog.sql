/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 100122
 Source Host           : localhost:3306
 Source Schema         : codebear_blog

 Target Server Type    : MySQL
 Target Server Version : 100122
 File Encoding         : 65001

 Date: 09/08/2018 14:11:25
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `aid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '用户id',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '账号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `salt` varchar(255) NOT NULL COMMENT '秘钥',
  `access_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'access_token',
  `token_expires_in` int(13) DEFAULT NULL COMMENT 'token有效期至',
  `create_time` int(13) NOT NULL COMMENT '创建时间',
  `status` bit(1) NOT NULL DEFAULT b'0' COMMENT '状态，0为正常，默认0',
  `last_login_time` int(13) DEFAULT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`aid`,`user_id`) USING BTREE,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '文章id',
  `title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `category_id` varchar(128) DEFAULT NULL COMMENT '文章分类id',
  `create_time` int(13) NOT NULL COMMENT '创建时间',
  `delete_time` int(13) DEFAULT NULL COMMENT '删除时间',
  `update_time` int(13) DEFAULT NULL COMMENT '更新时间',
  `publish_time` int(13) DEFAULT NULL COMMENT '发布时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态，0-正常（发布），1-删除，2-记录（待发布）',
  `content` text COMMENT '内容',
  `html_content` text COMMENT '生成的html',
  `cover` text COMMENT '封面图',
  `sub_message` text COMMENT '文章简介',
  `pageview` int(11) DEFAULT '0' COMMENT '文章阅读数',
  `is_encrypt` bit(1) DEFAULT b'0' COMMENT '是否加密，0否，1是，默认0',
  PRIMARY KEY (`aid`,`id`) USING BTREE,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for article_tag_mapper
-- ----------------------------
DROP TABLE IF EXISTS `article_tag_mapper`;
CREATE TABLE `article_tag_mapper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '文章id',
  `tag_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '标签id',
  `create_time` int(13) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for blog_config
-- ----------------------------
DROP TABLE IF EXISTS `blog_config`;
CREATE TABLE `blog_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `blog_name` varchar(255) DEFAULT NULL COMMENT '博客名称',
  `avatar` text COMMENT '头像',
  `sign` text COMMENT '个性签名',
  `wxpay_qrcode` text COMMENT '微信支付二维码',
  `alipay_qrcode` text COMMENT '支付宝支付二维码',
  `github` text COMMENT 'github',
  `view_password` varchar(255) DEFAULT NULL COMMENT '阅读加密密码',
  `salt` varchar(255) DEFAULT NULL COMMENT '阅读加密秘钥',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `aid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '分类id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '分类名称',
  `create_time` int(13) NOT NULL COMMENT '创建时间',
  `update_time` int(13) DEFAULT NULL COMMENT '更新时间',
  `status` bit(1) DEFAULT b'0' COMMENT '状态，0为正常，1为删除，默认0',
  `article_count` int(11) DEFAULT '0' COMMENT '该分类的文章数量',
  `can_del` bit(1) NOT NULL DEFAULT b'1' COMMENT '0表示不可删除，1表示可删除，默认1',
  PRIMARY KEY (`aid`,`id`) USING BTREE,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` varchar(128) NOT NULL COMMENT '文章id',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '父id, 默认0',
  `reply_id` int(10) DEFAULT NULL COMMENT '回复的评论id',
  `name` varchar(255) NOT NULL COMMENT '评论者名称',
  `email` varchar(128) DEFAULT NULL COMMENT '评论者邮箱',
  `content` text NOT NULL COMMENT '评论内容json',
  `source_content` text COMMENT '评论内容（原始内容）',
  `create_time` int(13) NOT NULL COMMENT '创建时间',
  `delete_time` int(13) DEFAULT NULL COMMENT '删除时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态，0正常，1删除，默认0',
  `is_author` bit(1) DEFAULT b'0' COMMENT '是否是作者，0否，1是，默认0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for friends
-- ----------------------------
DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends` (
  `aid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `friend_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '友链id',
  `name` varchar(255) NOT NULL COMMENT '友链名称',
  `url` text NOT NULL COMMENT '对应链接',
  `create_time` int(13) NOT NULL COMMENT '创建时间',
  `update_time` int(13) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(13) DEFAULT NULL COMMENT '删除时间',
  `status` bit(1) NOT NULL DEFAULT b'0' COMMENT '状态，0表示可用，1表示删除，默认0',
  `type_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属分类id',
  PRIMARY KEY (`aid`,`friend_id`) USING BTREE,
  UNIQUE KEY `friend_id` (`friend_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for friends_type
-- ----------------------------
DROP TABLE IF EXISTS `friends_type`;
CREATE TABLE `friends_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '分类名称',
  `count` int(11) DEFAULT '0' COMMENT '该分类的友链数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pages
-- ----------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(128) NOT NULL COMMENT '页面名称',
  `md` text COMMENT 'markdown内容',
  `html` text COMMENT '生成的html内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sys_log
-- ----------------------------
DROP TABLE IF EXISTS `sys_log`;
CREATE TABLE `sys_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `time` int(13) NOT NULL COMMENT '时间',
  `content` text COMMENT '日志内容',
  `ip` varchar(30) DEFAULT NULL COMMENT '客户端IP地址',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tag
-- ----------------------------
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '标签id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '标签名称',
  `create_time` int(13) NOT NULL COMMENT '创建时间',
  `update_time` int(13) DEFAULT NULL COMMENT '更新时间',
  `status` bit(1) DEFAULT b'0' COMMENT '状态，0表示正常，1表示删除，默认0',
  `article_count` int(11) DEFAULT '0' COMMENT '该标签的文章数量',
  PRIMARY KEY (`aid`,`id`) USING BTREE,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
