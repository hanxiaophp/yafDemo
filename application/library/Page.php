<?php
/**
 * Project:     手机搜房
 * File:        Page.php
 *
 * <pre>
 * 描述：手机搜房 分页类
 * </pre>
 *
 * @category  PHP
 * @package   Include
 * @author    lizeyu <lizeyu@soufun.com>
 * @copyright 2013 Soufun, Inc.
 * @license   BSD Licence
 * @link      http://example.com
 */

/**
 * 手机搜房 分页类
 *
 * Long description for file (if any)...
 *
 * @category  PHP
 * @package   Include
 * @author    lizeyu <lizeyu@soufun.com>
 * @copyright 2013 Soufun, Inc.
 * @license   BSD Licence
 * @link      http://example.com
 */
class Page
{
    /**
     * 总数
     * @var integer
     */
    private $_total = 0;

    /**
     * 每页显示数
     * @var integer
     */
    private $_limit = 10;

    /**
     * 当前页码
     * @var integer
     */
    private $_page = 1;

    /**
     * 总页数
     * @var integer
     */
    private $_total_page = 0;

    /**
     * 页面显示多少页号
     * @var integer
     */
    private $_page_show = '';

    /**
     * 显示的页号列表
     * @var array
     */
    private $_page_list = array();

    /**
     * 构造函数
     *
     * @param integer $total    总数
     * @param integer $limit    每页数量
     * @param integer $page     当前页码
     * @param integer $pageShow 显示页码个数
     */
    public function __construct($total, $limit, $page = 1, $pageShow = 7)
    {
        $this->_total = intval($total);
        $limit = intval($limit);
        $this->_limit = $limit > 0?$limit:10;
        $page = intval($page);
        $this->_page = $page > 0?$page:1;
        $this->_page_show = $pageShow > 0?$pageShow:10;
        $this->init();
    }

    /**
     * 初始化数据
     * @return null
     */
    public function init()
    {
        $total_page = ceil($this->_total/$this->_limit);
        if ($total_page < 1) {
            $total_page = 1;
        }
        if ($this->_page > $total_page) {
            $this->_page = $total_page;
        }
        $this->_total_page = $total_page;
        // 处理显示的页号列表
        $ps = 1;
        $pe = $this->_total_page;
        if ($this->_total_page > $this->_page_show) {
            // 一次不能完全显示
            $split_num = floor($this->_page_show/2);
            $ps = $this->_page - $split_num;
            $pe = $this->_page_show + $ps - 1;
            if ($ps < 1) {
                $ps = 1;
                $pe = $this->_page_show;
            }
            if ($pe > $this->_total_page) {
                $pe = $this->_total_page;
                $ps = $this->_total_page - $this->_page_show + 1;
            }
        }
        $this->_page_list = range($ps, $pe, 1);
        if ($this->_page_show == 7 && !empty($this->_page_list) && count($this->_page_list) > $this->_page_show) {
            unset($this->_page_list[0]);
        }
    }

    /**
     * 设置每页显示页号数量
     * @param integer $pageShow 每页显示数量
     * @return bool
     */
    public function setPageShow($pageShow = 10)
    {
        $pageShow = intval($pageShow);
        if ($pageShow < 1) {
            return false;
        }
        $this->_page_show = $pageShow;
        $this->init();
        return true;
    }

    /**
     * 获得显示的页号列表
     * @return array
     */
    public function getPageList()
    {
        return $this->_page_list;
    }

    /**
     * 获取页号
     * @return integereger
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * 获取总页数
     * @return integer
     */
    public function getTotalPage()
    {
        return $this->_total_page;
    }

    /**
     * 获取分页信息
     * @param string $tpl 模板路径
     * @param string $tag 分页标识符
     * @return array
     */
    public function getPageInfo($tpl = '', $tag = '{page}')
    {
        // 上一页
        $pre_page = 1;
        if ($this->_page > 2) {
            $pre_page = $this->_page-1;
        }
        // 下一页
        $next_page = $this->_page+1;
        if ($next_page > $this->_total_page) {
            $next_page = $this->_page;
        }
        // 上一页地址
        $pre_page_url = $pre_page;
        // 下一页地址
        $next_page_url = $next_page;
        if ($tpl && $tag) {
            $page_list = $this->getPageListTpl($tpl, $tag);
            $page_list_r = array_reverse($page_list, true);
            $pre_page_url = str_replace($tag, $pre_page, $tpl);
            $next_page_url = str_replace($tag, $next_page, $tpl);
        } else {
            $page_list = $this->_page_list;
            $page_list_r = array_reverse($page_list);
        }
        // 第一页地址
        $first_page_url = str_replace($tag, 1, $tpl);
        // 最后一页地址
        $last_page_url = str_replace($tag, $this->_total_page, $tpl);
        $next_spage = $this->_page + $this->_page_show;
        if ($next_spage > $this->_total_page) {
            $next_spage = $this->_total_page;
        }
        $next_spage_url = str_replace($tag, $next_spage, $tpl);
        $step_page = $this->_page_show;
        // 前一屏页号
        $pre_step_page = $this->_page - $step_page;
        $pre_step_page_url = $pre_step_page > 0?str_replace($tag, $pre_step_page, $tpl):'';
        // 下一屏页号
        $next_step_page = $this->_page + $step_page;
        $next_step_page_url = str_replace($tag, $next_step_page, $tpl);
        return array(
            'page'=>$this->_page,
            'limit'=>$this->_limit,
            'total'=>$this->_total,
            'total_page'=>$this->_total_page,
            'page_show'=>$this->_page_show,
            'page_list'=>$page_list,
            'page_list_r'=>$page_list_r,
            'pre_page'=>$pre_page,
            'pre_page_url'=>$pre_page_url,
            'next_page'=>$next_page,
            'next_page_url'=>$next_page_url,
            'first_page_url'=>$first_page_url,
            'last_page_url'=>$last_page_url,
            'next_spage'=>$next_spage,
            'next_spage_url'=>$next_spage_url,
            'pre_step_page'=>$pre_step_page,
            'pre_step_page_url'=>$pre_step_page_url,
            'next_step_page'=>$next_step_page,
            'next_step_page_url'=>$next_step_page_url,
            'page_url_tpl'=>$tpl,
            'page_url_tag'=>$tag
        );
    }

    /**
     * 用显示模板获取页号列表
     * @param string $tpl 模板路径
     * @param string $tag 分页标识符
     * @return array
     */
    public function getPageListTpl($tpl, $tag = '{page}')
    {
        $tpl = trim($tpl);
        $tag = trim($tag);
        if (!$tpl || !$tag) {
            return false;
        }
        $page_list = array();
        foreach ($this->_page_list as $page) {
            $page_list[$page] = str_replace($tag, $page, $tpl);
        }
        return $page_list;
    }
}

/* End of file Page.php */
