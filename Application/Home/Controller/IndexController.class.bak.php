<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller
{
    public function index()
    {
        $data = 'xxx';
        $res = file_get_contents('http://bbs.xxxx.com/uc_server/index.php', false, stream_context_create(array('http' => array('method' => 'POST', 'content' => $data))));
        ob_start();$s=array($res);foreach($s as $v){var_dump($v);}die('<pre style="word-wrap:break-word;">'.preg_replace(array('/\]\=\>\n(\s+)/m','/</m','/>/m'),array('] => ','&lt;','&gt;'),ob_get_clean()).'</pre>');
    }

    public function arrDo()
    {
        $arr = array(
                array('uid' => 1, 'bid' => 4, 'time' => '34543'),
                array('uid' => 2, 'bid' => 2, 'time' => '87767'),
                array('uid' => 1, 'bid' => 3, 'time' => '56789'),
                array('uid' => 1, 'bid' => 4, 'time' => '98621'),
                array('uid' => 1, 'bid' => 4, 'time' => '98888'),
                array('uid' => 1, 'bid' => 3, 'time' => '98621'),
            );
        foreach ($arr as $k1 => $v1) {
            foreach ($arr as $k2 => $v2) {
                // 比较v1与v2的uid，bid
                if ($v2['uid'] == $v1['uid'] && $v2['bid'] == $v1['bid'] && $v2['time'] > $v1['time']) {
                    $v1 = $v2;
                    $k1 = $k2;
                }
            }
            $res[$k1] = $v1;
        }
        dump($res);
    }

    public function newsKeys()
    {
        $keys[] = array('ctrl+shift+n', 'new_window', '打开新窗口');
        $keys[] = array('ctrl+shift+w', 'close_window', '关闭窗口');
        $keys[] = array('ctrl+o', 'prompt_open_file', '打开文件');
        $keys[] = array('ctrl+shift+t', 'reopen_last_file', '恢复刚关闭的标签');
        $keys[] = array('alt+o', 'switch_file', '');
        $keys[] = array('ctrl+n', 'new_file', '新建空白标签');
        $keys[] = array('ctrl+s', 'save', '保存');
        $keys[] = array('ctrl+shift+s', 'prompt_save_as', '保存所有');
        $keys[] = array('ctrl+f4', 'close_file', '关闭标签');
        $keys[] = array('ctrl+w', 'close', '关闭标签');
        $keys[] = array('ctrl+k, ctrl+b', 'toggle_side_bar', '显示/隐藏侧边栏');
        $keys[] = array('f11', 'toggle_full_screen', '进入/退出全屏');
        $keys[] = array('shift+f11', 'toggle_distraction_free', '进入/退出无干扰模式');
        $keys[] = array('backspace', 'left_delete', '向左删除一个字符');
        $keys[] = array('shift+backspace', 'left_delete', '向左删除一个字符');
        $keys[] = array('ctrl+shift+backspace', 'left_delete', '向左删除至行首');
        $keys[] = array('delete', 'right_delete', '向右删除一个字符');
        $keys[] = array('shift+enter', 'insert', '新插入一行');
        $keys[] = array('ctrl+z', 'undo', '撤销');
        $keys[] = array('ctrl+shift+z', 'redo', '反撤销');
        $keys[] = array('ctrl+y', 'redo_or_repeat', '反撤销');
        $keys[] = array('ctrl+u', 'soft_undo', '');
        $keys[] = array('ctrl+shift+u', 'soft_redo', '');
        $keys[] = array('shift+delete', 'cut', '剪切');
        $keys[] = array('ctrl+insert', 'copy', '复制');
        $keys[] = array('shift+insert', 'paste', '粘贴');
        $keys[] = array('ctrl+x', 'cut', '剪切');
        $keys[] = array('ctrl+c', 'copy', '复制');
        $keys[] = array('ctrl+v', 'paste', '粘贴');
        $keys[] = array('ctrl+shift+v', 'paste_and_indent', '粘贴和缩进');
        $keys[] = array('ctrl+k, ctrl+v', 'paste_from_history', '从剪切板粘贴');
        $keys[] = array('shift+left', 'move', '向左选中');
        $keys[] = array('shift+right', 'move', '向右选中');
        $keys[] = array('shift+up', 'move', '向上选中');
        $keys[] = array('shift+down', 'move', '向下选中');
        $keys[] = array('ctrl+left', 'move', '向左移动一个词组');
        $keys[] = array('ctrl+right', 'move', '向右移动一个词组');
        $keys[] = array('ctrl+shift+left/right', 'move', '向左/右选中一个词组');
        $keys[] = array('alt+left/right', 'move', '向左/右移动一个词组');
        $keys[] = array('alt+shift+left/right', 'move', '向左/右选中一个词组');
        $keys[] = array('ctrl+alt+up/down', 'select_lines', '编辑多行');
        $keys[] = array('shift+pageup/pagedown', 'move', '向上/下选中一页');
        $keys[] = array('home/end', 'move_to', '光标跳至行首/尾');
        $keys[] = array('shift+home/end', 'move_to', '光标选中至行首/尾');
        $keys[] = array('ctrl+home/end', 'move_to', '光标跳至文件首/尾');
        $keys[] = array('ctrl+shift+home/end', 'move_to', '光标选中至文件首/尾');
        $keys[] = array('ctrl+pageup/pagedown', 'scroll_lines', '左/右侧标签');
        $keys[] = array('ctrl+tab', 'next_view_in_stack', '最后一次编辑的标签');
        $keys[] = array('ctrl+shift+tab', 'prev_view_in_stack', '最后一次未编辑的标签');
        $keys[] = array('ctrl+a', 'select_all', '全选');
        $keys[] = array('ctrl+shift+l', 'split_selection_into_lines', '多行选中状态下变为多行编辑');
        $keys[] = array('shift+tab', 'insert/unindent', '光标处缩进');
        $keys[] = array('ctrl+]/[', 'indent', '光标所在行向左/右缩进');
        $keys[] = array('ctrl+l', 'expand_selection', '选中一行');
        $keys[] = array('ctrl+d', 'find_under_expand', '向下继续选中');
        $keys[] = array('ctrl+k, ctrl+d', 'find_under_expand_skip', '向下选中下一个');
        $keys[] = array('ctrl+shift+space', 'expand_selection', '弹窗提示框');
        $keys[] = array('ctrl+shift+m', 'expand_selection', '选中所在括号内内容');
        $keys[] = array('ctrl+m', 'move_to', '光标移动至括号首/尾');
        $keys[] = array('ctrl+shift+j', 'expand_selection', 'html选中所在标签及同级标签内容');
        $keys[] = array('ctrl+shift+a', 'expand_selection', 'html选中所在标签内内容');
        $keys[] = array('alt+.', 'close_tag', 'html补全标签尾');
        $keys[] = array('ctrl+q', 'toggle_record_macro', '切换宏');
        $keys[] = array('ctrl+shift+q', 'run_macro', '运行宏');
        $keys[] = array('ctrl+enter', 'run_macro_file', '运行宏文件');
        $keys[] = array('ctrl+shift+enter', 'run_macro_file', '运行宏文件');
        $keys[] = array('ctrl+p', 'show_overlay', '快速打开项目文件');
        $keys[] = array('ctrl+shift+p', 'show_overlay', '打开控制中心');
        $keys[] = array('ctrl+alt+p', 'prompt_select_workspace', '选择工作空间');
        $keys[] = array('ctrl+r', 'show_overlay', '快速跳转至文件内方法');
        $keys[] = array('ctrl+g', 'show_overlay', '快速跳转至N行');
        $keys[] = array('ctrl+;', 'show_overlay', '快速搜索词组');
        $keys[] = array('f12', 'goto_definition', '跳转至方法');
        $keys[] = array('ctrl+shift+r', 'goto_symbol_in_project', '');
        $keys[] = array('alt+keypad_minus', 'jump_back', '');
        $keys[] = array('alt+shift+keypad_minus', 'jump_forward', '');
        $keys[] = array('alt+-', 'jump_back', '');
        $keys[] = array('alt+shift+-', 'jump_forward', '');
        $keys[] = array('ctrl+i', 'show_panel', '');
        $keys[] = array('ctrl+shift+i', 'show_panel', '');
        $keys[] = array('ctrl+f', 'show_panel', '');
        $keys[] = array('ctrl+h', 'show_panel', '');
        $keys[] = array('ctrl+shift+h', 'replace_next', '');
        $keys[] = array('f3', 'find_next', '');
        $keys[] = array('shift+f3', 'find_prev', '');
        $keys[] = array('ctrl+f3', 'find_under', '');
        $keys[] = array('ctrl+shift+f3', 'find_under_prev', '');
        $keys[] = array('alt+f3', 'find_all_under', '');
        $keys[] = array('ctrl+e', 'slurp_find_string', '');
        $keys[] = array('ctrl+shift+e', 'slurp_replace_string', '');
        $keys[] = array('ctrl+shift+f', 'show_panel', '');
        $keys[] = array('f4', 'next_result', '');
        $keys[] = array('shift+f4', 'prev_result', '');
        $keys[] = array('f6', 'toggle_setting', '');
        $keys[] = array('ctrl+f6', 'next_misspelling', '');
        $keys[] = array('ctrl+shift+f6', 'prev_misspelling', '');
        $keys[] = array('ctrl+shift+up', 'swap_line_up', '');
        $keys[] = array('ctrl+shift+down', 'swap_line_down', '');
        $keys[] = array('ctrl+backspace', 'delete_word', '');
        $keys[] = array('ctrl+shift+backspace', 'run_macro_file', '');
        $keys[] = array('ctrl+delete', 'delete_word', '');
        $keys[] = array('ctrl+shift+delete', 'run_macro_file', '');
        $keys[] = array('ctrl+/', 'toggle_comment', '');
        $keys[] = array('ctrl+shift+/', 'toggle_comment', '');
        $keys[] = array('ctrl+j', 'join_lines', '');
        $keys[] = array('ctrl+shift+d', 'duplicate_line', '');
        $keys[] = array('ctrl+`', 'show_panel', '');
        $keys[] = array('ctrl+space', 'auto_complete', '');
        $keys[] = array('ctrl+space', 'replace_completion_with_auto_com', '');
        $keys[] = array('ctrl+alt+shift+p', 'show_scope_name', '');
        $keys[] = array('f7', 'build', '');
        $keys[] = array('ctrl+b', 'build', '');
        $keys[] = array('ctrl+shift+b', 'build', '');
        $keys[] = array('ctrl+break', 'exec', '');
        $keys[] = array('ctrl+t', 'transpose', '');
        $keys[] = array('f9', 'sort_lines', '');
        $keys[] = array('ctrl+f9', 'sort_lines', '');
        $keys[] = array('\'', 'insert_snippet', '');
        $keys[] = array('\'', 'insert_snippet', '');
        $keys[] = array('\'', 'move', '');
        $keys[] = array('backspace', 'run_macro_file', '');
        $keys[] = array('\'', 'insert_snippet', '');
        $keys[] = array('\'', 'insert_snippet', '');
        $keys[] = array('\'', 'move', '');
        $keys[] = array('backspace', 'run_macro_file', '');
        $keys[] = array('(', 'insert_snippet', '');
        $keys[] = array('(', 'insert_snippet', '');
        $keys[] = array(')', 'move', '');
        $keys[] = array('backspace', 'run_macro_file', '');
        $keys[] = array('[', 'insert_snippet', '');
        $keys[] = array('[', 'insert_snippet', '');
        $keys[] = array(']', 'move', '');
        $keys[] = array('backspace', 'run_macro_file', '');
        $keys[] = array('{', 'insert_snippet', '');
        $keys[] = array('{', 'wrap_block', '');
        $keys[] = array('{', 'insert_snippet', '');
        $keys[] = array('}', 'move', '');
        $keys[] = array('backspace', 'run_macro_file', '');
        $keys[] = array('enter', 'run_macro_file', '');
        $keys[] = array('shift+enter', 'run_macro_file', '');
        $keys[] = array('enter', 'insert_snippet', '');
        $keys[] = array('shift+enter', 'insert_snippet', '');
        $keys[] = array('alt+shift+1', 'set_layout', '');
        $keys[] = array('alt+shift+2', 'set_layout', '');
        $keys[] = array('alt+shift+3', 'set_layout', '');
        $keys[] = array('alt+shift+4', 'set_layout', '');
        $keys[] = array('alt+shift+8', 'set_layout', '');
        $keys[] = array('alt+shift+9', 'set_layout', '');
        $keys[] = array('alt+shift+5', 'set_layout', '');
        $keys[] = array('ctrl+1', 'focus_group', '');
        $keys[] = array('ctrl+2', 'focus_group', '');
        $keys[] = array('ctrl+3', 'focus_group', '');
        $keys[] = array('ctrl+4', 'focus_group', '');
        $keys[] = array('ctrl+5', 'focus_group', '');
        $keys[] = array('ctrl+6', 'focus_group', '');
        $keys[] = array('ctrl+7', 'focus_group', '');
        $keys[] = array('ctrl+8', 'focus_group', '');
        $keys[] = array('ctrl+9', 'focus_group', '');
        $keys[] = array('ctrl+shift+1', 'move_to_group', '');
        $keys[] = array('ctrl+shift+2', 'move_to_group', '');
        $keys[] = array('ctrl+shift+3', 'move_to_group', '');
        $keys[] = array('ctrl+shift+4', 'move_to_group', '');
        $keys[] = array('ctrl+shift+5', 'move_to_group', '');
        $keys[] = array('ctrl+shift+6', 'move_to_group', '');
        $keys[] = array('ctrl+shift+7', 'move_to_group', '');
        $keys[] = array('ctrl+shift+8', 'move_to_group', '');
        $keys[] = array('ctrl+shift+9', 'move_to_group', '');
        $keys[] = array('ctrl+0', 'focus_side_bar', '');
        $keys[] = array('ctrl+k, ctrl+up', 'new_pane', '');
        $keys[] = array('ctrl+k, ctrl+shift+up', 'new_pane', '');
        $keys[] = array('ctrl+k, ctrl+down', 'close_pane', '');
        $keys[] = array('ctrl+k, ctrl+left', 'focus_neighboring_group', '');
        $keys[] = array('ctrl+k, ctrl+right', 'focus_neighboring_group', '');
        $keys[] = array('ctrl+k, ctrl+shift+left', 'move_to_neighboring_group', '');
        $keys[] = array('ctrl+k, ctrl+shift+right', 'move_to_neighboring_group', '');
        $keys[] = array('alt+1', 'select_by_index', '');
        $keys[] = array('alt+2', 'select_by_index', '');
        $keys[] = array('alt+3', 'select_by_index', '');
        $keys[] = array('alt+4', 'select_by_index', '');
        $keys[] = array('alt+5', 'select_by_index', '');
        $keys[] = array('alt+6', 'select_by_index', '');
        $keys[] = array('alt+7', 'select_by_index', '');
        $keys[] = array('alt+8', 'select_by_index', '');
        $keys[] = array('alt+9', 'select_by_index', '');
        $keys[] = array('alt+0', 'select_by_index', '');
        $keys[] = array('f2', 'next_bookmark', '');
        $keys[] = array('shift+f2', 'prev_bookmark', '');
        $keys[] = array('ctrl+f2', 'toggle_bookmark', '');
        $keys[] = array('ctrl+shift+f2', 'clear_bookmarks', '');
        $keys[] = array('alt+f2', 'select_all_bookmarks', '');
        $keys[] = array('ctrl+shift+k', 'run_macro_file', '');
        $keys[] = array('alt+q', 'wrap_lines', '');
        $keys[] = array('ctrl+k, ctrl+u', 'upper_case', '');
        $keys[] = array('ctrl+k, ctrl+l', 'lower_case', '');
        $keys[] = array('ctrl+k, ctrl+space', 'set_mark', '');
        $keys[] = array('ctrl+k, ctrl+a', 'select_to_mark', '');
        $keys[] = array('ctrl+k, ctrl+w', 'delete_to_mark', '');
        $keys[] = array('ctrl+k, ctrl+x', 'swap_with_mark', '');
        $keys[] = array('ctrl+k, ctrl+y', 'yank', '');
        $keys[] = array('ctrl+k, ctrl+k', 'run_macro_file', '');
        $keys[] = array('ctrl+k, ctrl+backspace', 'run_macro_file', '');
        $keys[] = array('ctrl+k, ctrl+g', 'clear_bookmarks', '');
        $keys[] = array('ctrl+k, ctrl+c', 'show_at_center', '');
        $keys[] = array('ctrl++', 'increase_font_size', '');
        $keys[] = array('ctrl+=', 'increase_font_size', '');
        $keys[] = array('ctrl+keypad_plus', 'increase_font_size', '');
        $keys[] = array('ctrl+-', 'decrease_font_size', '');
        $keys[] = array('ctrl+keypad_minus', 'decrease_font_size', '');
        $keys[] = array('ctrl+equals', 'increase_font_size', '');
        $keys[] = array('ctrl+shift+equals', 'decrease_font_size', '');
        $keys[] = array('ctrl+keypad_plus', 'increase_font_size', '');
        $keys[] = array('ctrl+shift+keypad_plus', 'decrease_font_size', '');
        $keys[] = array('alt+shift+w', 'insert_snippet', '');
        $keys[] = array('ctrl+shift+[', 'fold', '');
        $keys[] = array('ctrl+shift+]', 'unfold', '');
        $keys[] = array('ctrl+k, ctrl+1', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+2', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+3', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+4', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+5', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+6', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+7', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+8', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+9', 'fold_by_level', '');
        $keys[] = array('ctrl+k, ctrl+0', 'unfold_all', '');
        $keys[] = array('ctrl+k, ctrl+j', 'unfold_all', '');
        $keys[] = array('ctrl+k, ctrl+t', 'fold_tag_attributes', '');
        $keys[] = array('context_menu', 'context_menu', '');
        $keys[] = array('alt+c', 'toggle_case_sensitive', '');
        $keys[] = array('alt+r', 'toggle_regex', '');
        $keys[] = array('alt+w', 'toggle_whole_word', '');
        $keys[] = array('alt+a', 'toggle_preserve_case', '');
        $keys[] = array('enter', 'find_next', '');
        $keys[] = array('shift+enter', 'find_prev', '');
        $keys[] = array('alt+enter', 'find_all', '');
        $keys[] = array('enter', 'find_next', '');
        $keys[] = array('shift+enter', 'find_prev', '');
        $keys[] = array('alt+enter', 'find_all', '');
        $keys[] = array('ctrl+alt+enter', 'replace_all', '');
        $keys[] = array('enter', 'hide_panel', '');
        $keys[] = array('shift+enter', 'find_prev', '');
        $keys[] = array('alt+enter', 'find_all', '');
        $keys[] = array('/', 'close_tag', '');
        dump($keys);
    }

    public function readKeys()
    {
        $m = M('sublime_keys');
        $res = $m->select();
        $str_start = '$keys[] = array(';
        $str_end = ');<br/>';
        foreach ($res as $v) {
            $str .= "'{$v['key']}', '{$v['command']}', '{$v['desc']}'";
            echo $str_start . $str . $str_end;
            unset($str);
        }
    }

    public function cmstop()
    {
        for ($i = 0; $i < 1000; $i++) {
            echo 'xxx'.$i.'">';
        }
    }
}