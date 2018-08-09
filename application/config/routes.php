<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = 'notfound';
$route['translate_uri_dashes'] = FALSE;

$route['test'] = 'common/Test/test';


//===========================================admin-后台管理-接口===================================

// 添加-已测试
// $route['a/create']['post'] = 'admin/Admin/create';

// 登录-已测试
$route['a/login']['post'] = 'admin/Admin/login';

// 获取七牛上传图片token-已测试
$route['a/qiniu/token']['get'] = 'admin/Qiniu/get_qiniu_token';

// 获取博客配置-已测试
$route['a/webConfig']['get'] = 'admin/WebConfig/get_web_config';

// 修改博客配置-已测试
$route['a/webConfig/modify']['post'] = 'admin/WebConfig/modify';

// 获取 关于我 页面-已测试
$route['a/webConfig/getAbout']['get'] = 'admin/WebConfig/get_about_me';

// 修改 关于我 页面-已测试
$route['a/webConfig/modifyAbout']['post'] = 'admin/WebConfig/modify_about';

// 获取友链类型列表-已测试
$route['a/friends/typeList']['get'] = 'admin/Friends/get_friends_type';

// 获取友链列表-已测试
$route['a/friends/list']['get'] = 'admin/Friends/get_friends_list';

// 添加友链-已测试
$route['a/friends/add']['post'] = 'admin/Friends/add_friend';

// 编辑友链-已测试
$route['a/friends/modify']['post'] = 'admin/Friends/modify_friend';

// 删除友链-已测试
$route['a/friends/delete']['post'] = 'admin/Friends/del_friend';

// 添加分类
$route['a/category/add']['post'] = 'admin/Categories/add_category';

// 添加标签
$route['a/tag/add']['post'] = 'admin/Categories/add_tag';

// 修改分类
$route['a/category/modify']['post'] = 'admin/Categories/modify_category';

// 修改标签
$route['a/tag/modify']['post'] = 'admin/Categories/modify_tag';

// 删除分类
$route['a/category/delete']['post'] = 'admin/Categories/del_category';

// 删除标签
$route['a/tag/delete']['post'] = 'admin/Categories/del_tag';

// 获取分类列表
$route['a/category/list']['get'] = 'admin/Categories/get_category_list';

// 获取标签列表
$route['a/tag/list']['get'] = 'admin/Categories/get_tag_list';

// 获取分类
$route['a/category']['get'] = 'admin/Categories/get_category';

// 获取标签
$route['a/tag']['get'] = 'admin/Categories/get_tag';

// 保存文章
$route['a/article/save']['post'] = 'admin/Article/save';

// 发布文章
$route['a/article/publish']['post'] = 'admin/Article/publish';

// 编辑文章
$route['a/article/modify']['post'] = 'admin/Article/modify';

// 删除文章
$route['a/article/delete']['post'] = 'admin/Article/delete';

// 获取文章信息
$route['a/article/info']['get'] = 'admin/Article/get_article';

// 获取文章列表
$route['a/article/list']['get'] = 'admin/Article/get_article_list';

// 获取系统日志
$route['a/sys/log']['get'] = 'admin/System/get_sys_log';

// 获取首页面板显示的统计信息
$route['a/statistics/home']['get'] = 'admin/Statistics/get_home_statistics';

// 获取文章评论列表
$route['a/comments/list']['get'] = 'admin/Comments/get_comments';

// 添加评论
$route['a/comments/add']['post'] = 'admin/Comments/add';

// 删除评论
$route['a/comments/delete']['post'] = 'admin/Comments/delete';

// 获取所有评论
$route['a/comments/alllist']['get'] = 'admin/Comments/get_all_comments';

// 获取 我的简历 页面
$route['a/webConfig/getResume']['get'] = 'admin/WebConfig/get_resume';

// 修改 我的简历 页面
$route['a/webConfig/modifyResume']['post'] = 'admin/WebConfig/modify_resume';

//===========================================admin-后台管理-接口-结束===============================






//===========================================web-前端-接口===================================

// 获取 关于我 页面-已测试
$route['w/getAbout']['get'] = 'web/WebConfig/get_about_me';

// 获取文章归档列表
$route['w/article/archives']['get'] = 'web/Article/get_article_archives';

// 获取文章信息
$route['w/article']['get'] = 'web/Article/get_article';

// 获取文章列表
$route['w/article/list']['get'] = 'web/Article/get_article_list';

// 获取分类列表
$route['w/category/list']['get'] = 'web/Categories/get_category_list';

// 获取标签列表
$route['w/tag/list']['get'] = 'web/Categories/get_tag_list';

// 获取博客信息
$route['w/blogInfo']['get'] = 'web/WebConfig/get_blog_info';

// 获取友链列表
$route['w/friends/list']['get'] = 'web/Friends/get_friends_list';

// 获取文章评论列表
$route['w/comments/list']['get'] = 'web/Comments/get_comments';

// 添加评论
$route['w/comments/add']['post'] = 'web/Comments/add';

// 获取 我的简历 页面-已测试
$route['w/getResume']['get'] = 'web/WebConfig/get_resume';

// 按文章标题和简介搜索
$route['w/article/search']['get'] = 'web/article/search';

//===========================================web-前端-接口-结束===============================






//===========================================common-共用-接口===================================


//===========================================common-共用-接口-结束===============================