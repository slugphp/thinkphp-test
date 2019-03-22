/**
 * jQuery admin model
 *
 * @by weilong
 */

/**
 *  公共的一些方法，每个页面肯能重复使用
 */
var common = {
    // 菜单：添加标签页方法
    addTab: function (node, url) {
        // 兼容写法
        if (typeof node == 'object') {
            var title = node.text,
                url = node.url;
        } else {
            var title = node,
                url = url;
        }
        // 已添加则跳到标签页
        if ($('#tt').tabs('exists', title)){
            $('#tt').tabs('select', title);
        } else {
            // ajax添加
            $('#tt').tabs('add',{
                title: title,
                content: function () {
                    var html = '';
                    $.ajax({
                        url: url,
                        dataType: 'html',
                        async: false,
                    }).done(function(returnData) {
                        html = returnData;
                    }).fail(function() {
                        console.log("'" + url + "' add tab error");
                    });
                    return html;
                },
                closable:true
            });
        }
    },
    // 表格：按字段merge数据，合并方格用显示为红色方法
    cellStylerRed: function (value,row,index) {
        return 'color:red;';
    },
    redStr: function (like, str) {
        var searchSpan = '<span style="color:red">'+like+'</span>';
        return str.replace(like, searchSpan);
    },
    rmRedStr: function (str) {
        var res = str.replace('<span style="color:red">', '');
        return res.replace('</span>', '');
    },
    // 表格：排序方法
    sortnum: function (a, b) {
        return  (a - b > 0 ? 1 : -1);
    },
    // 表格：按字段merge数据，合并方格用
    mergeData: function (data, field) {
        var merge = {},
            sort = false,
            mergesRes = [];
        for (var i = 0; i < data.rows.length; i++) {
            var row = data.rows[i],
                val = row[field];
            if (typeof row.merge == 'number') {
                sort = true;
            }
            if (merge[val] >= 0 && val == data.rows[i-1][field]) {
                merge[val]++;
            } else {
                merge[val] = 0;
            }
        }
        for (var i = 0; i < data.rows.length; i++) {
            var val = data.rows[i][field],
                mergeNum = merge[val] + 1;
            if (merge[val] >= 0 && !sort) {
                data.rows[i].merge = mergeNum
                mergesRes.push({
                    index: i,
                    rowspan: mergeNum,
                });
                merge[val] = undefined;
            } else {
                data.rows[i].merge = 0;
            }
        }
        data.merges = mergesRes;
        return data;
    },
    // 日期格式化位：2016-08-03
    myformatter: function (date){
        var y = date.getFullYear();
        var m = date.getMonth()+1;
        var d = date.getDate();
        return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
    },
    // 获取当前日期框值
    myparser: function (s) {
        if (!s) return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0],10);
        var m = parseInt(ss[1],10);
        var d = parseInt(ss[2],10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
            return new Date(y,m-1,d);
        } else {
            return new Date();
        }
    },
    /**
     * 和PHP一样的时间戳格式化函数
     * @param  {string} format    格式
     * @param  {int}    timestamp 要格式化的时间 默认为当前时间
     * @return {string}           格式化的时间字符串
     */
    date: function (format, timestamp){
        var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date());
        var pad = function(n, c){
            if((n = n + "").length < c){
                return new Array(++c - n.length).join("0") + n;
            } else {
                return n;
            }
        };
        var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        var txt_ordin = {1:"st", 2:"nd", 3:"rd", 21:"st", 22:"nd", 23:"rd", 31:"st"};
        var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var f = {
            // Day
            d: function(){return pad(f.j(), 2)},
            D: function(){return f.l().substr(0,3)},
            j: function(){return jsdate.getDate()},
            l: function(){return txt_weekdays[f.w()]},
            N: function(){return f.w() + 1},
            S: function(){return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'},
            w: function(){return jsdate.getDay()},
            z: function(){return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0},
            // Week
            W: function(){
                var a = f.z(), b = 364 + f.L() - a;
                var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
                if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
                    return 1;
                } else{
                    if(a <= 2 && nd >= 4 && a >= (6 - nd)){
                        nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                        return date("W", Math.round(nd2.getTime()/1000));
                    } else{
                        return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                    }
                }
            },
            // Month
            F: function(){return txt_months[f.n()]},
            m: function(){return pad(f.n(), 2)},
            M: function(){return f.F().substr(0,3)},
            n: function(){return jsdate.getMonth() + 1},
            t: function(){
                var n;
                if( (n = jsdate.getMonth() + 1) == 2 ){
                    return 28 + f.L();
                } else{
                    if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
                        return 31;
                    } else{
                        return 30;
                    }
                }
            },
            // Year
            L: function(){var y = f.Y();return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0},
            //o not supported yet
            Y: function(){return jsdate.getFullYear()},
            y: function(){return (jsdate.getFullYear() + "").slice(2)},
            // Time
            a: function(){return jsdate.getHours() > 11 ? "pm" : "am"},
            A: function(){return f.a().toUpperCase()},
            B: function(){
                // peter paul koch:
                var off = (jsdate.getTimezoneOffset() + 60)*60;
                var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
                var beat = Math.floor(theSeconds/86.4);
                if (beat > 1000) beat -= 1000;
                if (beat < 0) beat += 1000;
                if ((String(beat)).length == 1) beat = "00"+beat;
                if ((String(beat)).length == 2) beat = "0"+beat;
                return beat;
            },
            g: function(){return jsdate.getHours() % 12 || 12},
            G: function(){return jsdate.getHours()},
            h: function(){return pad(f.g(), 2)},
            H: function(){return pad(jsdate.getHours(), 2)},
            i: function(){return pad(jsdate.getMinutes(), 2)},
            s: function(){return pad(jsdate.getSeconds(), 2)},
            O: function(){
                var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
                if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
                return t;
            },
            P: function(){var O = f.O();return (O.substr(0, 3) + ":" + O.substr(3, 2))},
            c: function(){return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()},
            U: function(){return Math.round(jsdate.getTime()/1000)}
        };
        return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
            if( t!=s ){
                ret = s;
            } else if( f[s] ){
                ret = f[s]();
            } else{
                ret = s;
            }
            return ret;
        });
    },
    // 首页初始化init
    init: function () {
        // ajax获取菜单数据
        var html = '';
        $.ajax({
            url: 'index/tree',
            dataType: 'json',
            async: false,
        }).done(function(menu) {
            for (var title in menu) {
                var ul = $('<ul/>').addClass('tree easyui-tree')
                var data = [
                    'animate:false',
                    'lines:true',
                    'lines:true',
                    'onClick:common.addTab',
                    'data:' + JSON.stringify(menu[title]),
                ]
                ul.attr('data-options', data.join(','));
                $('<div/>', {
                    title: title,
                    css: {padding: '8px 0 18px 8px'},    // 每个菜单样式
                    html: ul,
                }).appendTo('.easyui-accordion');
            }
        }).fail(function() {
            console.log("'index/tree' load menu error");
        });
    },
}
common.init();

/**
 * 表格构造器。
 * 工厂模式，生产一个表格：可以包含一个table、一个form、多个dialog。
 * dlg需命名。
 * @param string id 前缀id。每个页面唯一
 */
function TbFmDlgsObj(id) {
    this.url = '';
    this.endEditingFunc = function () {};
    this.table = $('#' + id + 'dg');
    this.form = $('#' + id + 'fm');
    this.toolbar = '#' + id + 'tb';
    this.dialog = {};
    this.dialogOpt = {};
    this.geturl = function () {
        return this.url + '?' + this.form.serialize();
    }
    var editIndex = undefined;
    this.setTable = function (tableOptsNew) {
        this.url = tableOptsNew.url;
        delete tableOptsNew.url;
        var tableOpts = {
            rownumbers: false,
            singleSelect: true,
            url: this.geturl(),
            method: 'get',
            pagination: true,
            pageList: [10, 20, 50, 100, 200, 500, 1000],
            onClickRow: function (index) {
                if (editIndex != index) {
                    if ($(this).datagrid('validateRow', editIndex)) {
                        $(this).datagrid('endEdit', editIndex);
                        if (editIndex != undefined) {
                            eval(id + '.endEditingFunc()');
                        }
                        $(this).datagrid('selectRow', index).datagrid('beginEdit', index);
                        editIndex = index;
                    } else {
                        $(this).datagrid('selectRow', editIndex);
                    }
                }
            },
            loadMsg: '加载中 ...',
            toolbar: this.toolbar,
            css: undefined,
        };
        var opt = $.extend({}, tableOpts, tableOptsNew);
        this.table.datagrid(opt);
        if (opt.css != undefined) {
            for (var i = 0; i < opt.css.length; i++) {
                this.table.parents(opt.css[i].node).css(opt.css[i].info);
            }
        }
        if (opt.pageSize > 0) {
            this.setPager();
        }
        return this;
    }
    this.setPager = function () {
        this.table.datagrid('getPager').pagination({
            beforePageText: '',
            afterPageText: '/ {pages}',
            displayMsg: '{from}-{to}，共计{total}条',
        });
        return this;
    }
    this.setDatebox = function (dbox) {
        dbox.datebox({
            currentText: "今天",
            closeText: "关闭",
        }).datebox('calendar').calendar({
            weeks: ["一", "二", "三", "四", "五", "六", "日"],
            months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            currentText: "今天",
            closeText: "Close",
            okText: "Ok",
        });
        return this;
    }
    // 上部表单提交按钮
    this.search = function () {
        this.table.datagrid('reload', this.geturl());
    }
    // 确认链接
    this.confirm = function (url, title = '您确定删除此项？') {
        var tb = this.table;
        $.messager.confirm('请确认', title, function(res) {
            if (res){
                $.ajax({
                    url: url,
                    dataType: 'json',
                }).done(function(d) {
                    if (d > 0) {
                        $.messager.show({title: 'Success', msg: '删除成功', timeout: 1000});
                        tb.datagrid('reload');
                    } else {
                        $.messager.show({title: 'Error', msg: '未删除', timeout: 1000});
                        tb.datagrid('reload');
                    }
                }).fail(function() {
                    $.messager.show({title: 'Error', msg: '连接失败', timeout: 1000});
                });
            }
        });
        event.stopPropagation();
    }
    // 打开窗口设置
    this.setDialog = function (dlgOpt) {
        if (typeof dlgOpt.dlgName == 'undefined') {
            throw 'setDialog param need dlgName';
        }
        var dlgName = dlgOpt.dlgName;
        this.dialog[dlgName] = $('#' + id + dlgName);
        // 默认按钮
        var buttonsOpt = { buttons: [{
            text: '保存',
            iconCls: 'icon-ok',
            handler: function() {
                var thisId = eval(id),
                    dlgDom = thisId.dialog[dlgName],
                    dlgFm = dlgDom.find('form');
                if (!dlgFm.form('validate')) {
                    return false;
                }
                $.ajax({
                    url: dlgOpt.saveUrl,
                    dataType: 'json',
                    data: dlgFm.serialize(),
                }).done(function(d) {
                    if (d > 0) {
                        dlgDom.dialog('close');
                        thisId.table.datagrid('reload');
                    } else {
                        alert('没有改动');
                        dlgDom.dialog('close');
                        thisId.table.datagrid('reload');
                    }
                }).fail(function() {
                    console.log("'" + dlgOpt.saveUrl + "'save error");
                });
            }
        },{
            text: '取消',
            handler: function() {
                var thisId = eval(id);
                thisId.dialog[dlgName].dialog('close');
            }
        }]};
        this.dialogOpt[dlgName] = $.extend({}, buttonsOpt, dlgOpt);
        this.dialog[dlgName].css('display', 'none');
        return this;
    }
    this.openDialog = function (dlgName, param) {
        this.dialog[dlgName].dialog(this.dialogOpt[dlgName]);
        var fm = this.dialog[dlgName].find('form');
        param.push(event);
        this.dialogOpt[dlgName].openDialogFunc.apply(this, param);
    };
    // 筛选表单enter提交
    this.form.on('keydown', function(e) {
        if (e.keyCode == 13) {
            var form = this;
            eval(form.id.replace('myform', '') + '.search();');
            event.preventDefault();
        }
    });
}
