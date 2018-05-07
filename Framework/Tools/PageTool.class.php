<?php

/**
 * 分页工具条类
 */
class PageTool
{
    /**
     * 得到分页工具条的方法
     */
    public static function show($count,$totalPage,$pageSize,$page,$url){

        //上一页:
        $pre_page = ($page-1)<1?1:($page-1);
        //下一页:
        $next_page = ($page+1)>$totalPage?$totalPage:($page+1);

        $html = <<<PAGE
    <table id="page-table" cellspacing="0">
        <tbody>
            <tr>
                <td align="right" nowrap="true" style="background-color: rgb(255, 255, 255);">
                    <div id="turn-page">
                        总计  <span id="totalRecords">{$count}</span>个记录,分为 <span id="totalPages">{$totalPage}</span>页,当前第 <span id="pageCurrent">{$page}</span>页
                        <span id="page-link">
                            <a href="index.php?{$url}&page=1" class="btn btn-success btn-sm">第一页</a>
                            <a href="index.php?{$url}&page={$pre_page}" class="btn btn-success btn-sm">上一页</a>
                            <a href="index.php?{$url}&page={$next_page}" class="btn btn-success btn-sm">下一页</a>
                            <a href="index.php?{$url}&page={$totalPage}" class="btn btn-success btn-sm">最末页</a>
                        </span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
PAGE;
        //返回html
        return $html;
    }
}

