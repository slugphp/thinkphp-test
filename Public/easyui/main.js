/**
 * requirejs
 */
require.config({
　　　　paths: {
　　　　　　"jquery": "jquery.min",
　　　　　　"easyui-lang": "easyui-lang-zh_CN",
　　　　　　"easyui": "jquery.easyui",
　　　　　　"admin": "admin"
　　　　}
});
require(['jquery', 'easyui-lang', 'easyui', 'admin'], function ($){
    var test = new TbFmDlgsObj('testdg').setTable({});
});

