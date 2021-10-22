<?php
list($tmp, $menu_main, $menu_sub) = explode('/', $this->reqData['tpf']);
$arrMenu[$menu_main] = 'active ';
$arrMenuSub[$menu_sub] = ' class="active"';

$arrBoard = $this->objDBH->getRows("select code,title,is_mass,is_order from board order by code");
?>

<!-- sidebar -->
<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="<?=@$arrMenu['member']?>treeview">
                <a href="#">
                <i class="fa fa-user"></i> <span>회원 관리</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li<?=!empty($arrMenuSub['list']) ? $arrMenuSub['list'] : ""?>><a href="?tpf=admin/member/list"><i class="fa fa-circle-o"></i> 회원 리스트</a></li>
                </ul>
                <ul class="treeview-menu">
                    <li<?=!empty($arrMenuSub['level']) ? $arrMenuSub['level'] : ""?>><a href="?tpf=admin/member/level"><i class="fa fa-circle-o"></i> 등급 관리</a></li>
                </ul>
            </li>
            <li class="<?=$arrMenu['board']?>treeview">
                <a href="#">
                <i class="fa fa-list-alt"></i> <span>게시판 관리</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li<?=!empty($arrMenuSub['manage']) ? $arrMenuSub['manage'] : ""?>><a href="index.php?tpf=admin/board/manage"><i class="fa fa-circle-o"></i> 게시판 리스트</a></li>
                </ul>
<?php
if (!empty($arrBoard['list'])) {
    foreach($arrBoard['list'] as $key => $val) {
        if ($val['is_mass'] == 'y') $link = 'list_upload';
        else if ($val['is_order'] == 'y') $link = 'list_order';
        else $link = 'list';

        echo '  <ul class="treeview-menu">
                    <li'; if(@$this->reqData['board_code'] == $val['code']) echo ' class="active"'; echo '><a href="index.php?tpf=admin/board/'.$link.'&board_code='.$val['code'].'"><i class="fa fa-circle-o"></i> '.$val['title'].'</a></li>
                </ul>';
    }
}
?>
            </li>
            <li class="<?=$arrMenu['setting']?>treeview">
                <a href="#">
                <i class="fa fa-gear"></i> <span>설정</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li<?=!empty($arrMenuSub['contract']) ? $arrMenuSub['contract'] : ""?>><a href="?tpf=admin/setting/contract"><i class="fa fa-circle-o"></i> 약관 관리</a></li>
<!--    // 사이트의 각종 정보를 셋팅하는 페이지
                    <li<?=!empty($arrMenuSub['info']) ? $arrMenuSub['info'] : ""?>><a href="?tpf=admin/setting/info"><i class="fa fa-circle-o"></i> 각종정보 관리</a></li>
-->
                </ul>
            </li>
        </ul>
    </section>
</aside><!-- /.sidebar -->