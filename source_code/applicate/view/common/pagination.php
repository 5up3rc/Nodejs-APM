<?php
/*
 * First Previous  22 23 24 25 26 [27] 28 29 30 31 32 ... 48 49 50 Next Last
 */
// Number of page links in the begin and end of whole range
$count_out = (! empty ( $config ['count_out'] )) ? ( int ) $config ['count_out'] : 1;
// Number of page links on each side of current page
$count_in = (! empty ( $config ['count_in'] )) ? ( int ) $config ['count_in'] : 2;
// Beginning group of pages: $n1...$n2
// $n1 = 1;
$n2 = min ( $count_out, $total_pages );
// Ending group of pages: $n7...$n8
$n7 = max ( 1, $total_pages - $count_out + 1 );
$n8 = $total_pages;
// Middle group of pages: $n4...$n5
$n4 = max ( $n2, $current_page - $count_in );
if ($n4 > $total_pages - 5){
    $n4 = ($total_pages - 5) <= 0 ? 1 : ($total_pages - 5);
}
$n5 = min ( $n7 - 1, $current_page + $count_in );
if ($current_page <= 3){
    $n5 = $total_pages > 5 ? 5 : $total_pages;
}
$use_middle = ($n5 >= $n4);
// Point $n3 between $n2 and $n4
$n3 = ( int ) (($n2 + $n4) / 2);
$use_n3 = ($use_middle && (($n4 - $n2) > 1));
// Point $n6 between $n5 and $n7
$n6 = ( int ) (($n5 + $n7) / 2);
$use_n6 = ($use_middle && (($n7 - $n5) > 1));
// Links to display as array(page => content)
$links = array ();
// Generate links data in accordance with calculated numbers
for($i = $n4; $i <= $n5; $i ++) {
    $links [$i] = $i;
}
if ($use_n6) {
    $links [$n6] = '&hellip;';
}
for($i = $n7; $i <= $n8; $i ++) {
    $links [$i] = $i;
}
?>
<div class="mui-pagination">
        <a class='mui-global-b' page="<?php echo $first_page; ?>" rel="prev">首页</a>
    <?php if ($previous_page !== FALSE){ ?>
        <a class='mui-global-b' page="<?php echo $previous_page; ?>" rel="prev">上一页</a>
    <?php }else{ ?>
        <a class='mui-global-b' rel="prev">上一页</a>
    <?php } ?>
    <?php foreach ($links as $number => $content){ ?>
        <?php if ($number === $current_page){ ?>
            <a class='mui-global-b mui-global-br cur mui-global-num'><?php echo $content ?></a>
        <?php }else{ ?>
            <a class='mui-global-b mui-global-num' page="<?php echo $number; ?>"><span><?php echo $content ?></span></a>
        <?php } ?>
    <?php } ?>
    <?php if ($next_page !== FALSE){ ?>
        <a class='mui-global-b' page="<?php echo $next_page; ?>" rel="next">下一页</a>
    <?php }else{ ?>
        <a class='mui-global-b' rel="next">下一页</a>
    <?php } ?>
    <a class='mui-global-b' page="<?php echo $total_pages; ?>" rel="next">尾页</a>
</div>
<!-- .pages -->