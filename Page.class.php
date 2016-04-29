<?php
//how to use
//require_once './IMooc/Page.class.php';
//require_once './IMooc/db.class.php';
////数据库中的总条数:total_rows;
////每一页显示的条数:per_page_rows
//$sql="select count(*) from go_member";
//$count_result = $dbObj->query($sql);
//$total_rows = mysql_fetch_row($count_result)[0];
//$per_page_rows=2;
//
//$page = new IMooc\Page($total_rows,$per_page_rows);
//$sql="select * from go_member {$page->limit}";
//$rt=mysql_query($sql);
//echo '<table width="1000" border="1">';
//while (!!$row=mysql_fetch_assoc($rt)) {
//    echo '<tr>';
//    echo '<td>'.$row['uid'].'</td>';
//    echo '<td>'.$row['username'].'</td>';
//    echo '<td>'.$row['email'].'</td>';
//    echo '<td>'.$row['mobile'].'</td>';
//    echo '</tr>';
//}
//echo '<tr><td colspan="5" align="right">'.$page->showpage().'</td></tr>';
//echo '</table>';
//



/*
 * 分页类来源于开源中国
 *modifier  tinytoobad
 *20160426
 **/
namespace IMooc;

class Page {
    private $total;      //总记录
    private $pagesize;    //每页显示多少条
    private $limit;          //limit
    private $page;           //当前页码
    private $pagenum;      //总页码
    private $url;           //地址
    private $bothnum;      //两边保持数字分页的量

    //构造方法初始化
    public function __construct($_total, $_pagesize) {
        $this->total = $_total ? $_total : 1;
        $this->pagesize = $_pagesize;
        $this->pagenum = ceil($this->total / $this->pagesize);
        $this->page = $this->setPage();
        $this->limit = "LIMIT ".($this->page-1)*$this->pagesize.",$this->pagesize";
        $this->url = $this->setUrl();
        $this->bothnum = 2;
    }

    //拦截器
    public function __get($_key) {
        return $this->$_key;
    }

    //获取当前页码
    private function setPage() {
        if (!empty($_GET['page'])) {
            if ($_GET['page'] > 0) {
                if ($_GET['page'] > $this->pagenum) {
                    return $this->pagenum;
                } else {
                    return $_GET['page'];
                }
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

    //获取地址
    private function setUrl() {
        $_url = $_SERVER["REQUEST_URI"];
        $_par = parse_url($_url);
        if (isset($_par['query'])) {
            parse_str($_par['query'],$_query);
            unset($_query['page']);
            $_url = $_par['path'].'?'.http_build_query($_query);
        }
        return $_url;
    }     //数字目录
    private function pageList() {
        $_pagelist = '';    //添加声明　不然会报未定义
        for ($i=$this->bothnum;$i>=1;$i--) {
            $_page = $this->page-$i;
            if ($_page < 1) continue;
            $_pagelist .= ' <a href="'.$this->url.'&page='.$_page.'">'.$_page.'</a> ';
        }
        $_pagelist .= ' <span class="me">'.$this->page.'</span> ';
        for ($i=1;$i<=$this->bothnum;$i++) {
            $_page = $this->page+$i;
            if ($_page > $this->pagenum) break;
            $_pagelist .= ' <a href="'.$this->url.'&page='.$_page.'">'.$_page.'</a> ';
        }
        return $_pagelist;
    }

    //首页
    private function first() {
        if ($this->page > $this->bothnum+1) {
            return ' <a href="'.$this->url.'">1</a> ...';
        }
    }

    //上一页
    private function prev() {
        if ($this->page == 1) {
            return '<span class="disabled">上一页</span>';
        }
        return ' <a href="'.$this->url.'&page='.($this->page-1).'">上一页</a> ';
    }

    //下一页
    private function next() {
        if ($this->page == $this->pagenum) {
            return '<span class="disabled">下一页</span>';
        }
        return ' <a href="'.$this->url.'&page='.($this->page+1).'">下一页</a> ';
    }

    //尾页
    private function last() {
        if ($this->pagenum - $this->page > $this->bothnum) {
            return ' ...<a href="'.$this->url.'&page='.$this->pagenum.'">'.$this->pagenum.'</a> ';
        }
    }

    //分页信息
    public function showpage() {
        $_page = '';    //添加声明　不然会报未定义
        $_page .= $this->first();
        $_page .= $this->pageList();
        $_page .= $this->last();
        $_page .= $this->prev();
        $_page .= $this->next();
        return $_page;
    }
}
?>
