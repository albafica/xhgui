<?php

class Xhgui_Twig_Extension extends Twig_Extension
{
    protected $_app;

    public function __construct($app)
    {
        $this->_app = $app;
    }

    public function getName()
    {
        return 'xhgui';
    }

    public function getFunctions()
    {
        return array(
            'url' => new Twig_Function_Method($this, 'url'),
            'static' => new Twig_Function_Method($this, 'staticUrl'),
            'percent' => new Twig_Function_Method($this, 'makePercent', array(
                'is_safe' => array('html')
            )),
        );
    }

    public function getFilters()
    {
        return array(
            'simple_url' => new Twig_Filter_Function('Xhgui_Util::simpleUrl'),
            'as_bytes' => new Twig_Filter_Method($this, 'formatBytes', array('is_safe' => array('html'))),
            'as_kb' => new Twig_Filter_Method($this, 'formatKBytes', array('is_safe' => array('html'))),
            'as_mb' => new Twig_Filter_Method($this, 'formatMBytes', array('is_safe' => array('html'))),
            'as_title_bytes' => new Twig_Filter_Method($this, 'formatTitleBytes', array('is_safe' => array('html'))),
            'as_title_kb' => new Twig_Filter_Method($this, 'formatTitleKBytes', array('is_safe' => array('html'))),
            'as_title_mb' => new Twig_Filter_Method($this, 'formatTitleMBytes', array('is_safe' => array('html'))),
            'as_time' => new Twig_Filter_Method($this, 'formatTime', array('is_safe' => array('html'))),
            'as_ms' => new Twig_Filter_Method($this, 'formatMillisecondTime', array('is_safe' => array('html'))),
            'as_s' => new Twig_Filter_Method($this, 'formatSecondTime', array('is_safe' => array('html'))),
            'as_title_time' => new Twig_Filter_Method($this, 'formatTitleTime', array('is_safe' => array('html'))),
            'as_title_ms' => new Twig_Filter_Method($this, 'formatTitleMillisecondTime', array('is_safe' => array('html'))),
            'as_title_s' => new Twig_Filter_Method($this, 'formatTitleSecondTime', array('is_safe' => array('html'))),
            'as_diff' => new Twig_Filter_Method($this, 'formatDiff', array('is_safe' => array('html'))),
            'as_percent' => new Twig_Filter_Method($this, 'formatPercent', array('is_safe' => array('html'))),
            'truncate' => new Twig_Filter_Method($this, 'truncate'),
        );
    }

    protected function _getBase()
    {
        $base = dirname($_SERVER['PHP_SELF']);
        if ($base == '/') {
            return '';
        }
        return $base;
    }

    public function truncate($input, $length = 50)
    {
        if (strlen($input) < $length) {
            return $input;
        }
        return substr($input, 0, $length) . "\xe2\x80\xa6";
    }

    /**
     * Get a URL for xhgui.
     *
     * @param string $path The file/path you want a link to
     * @param array $queryarg Additional querystring arguments.
     * @return string url.
     */
    public function url($name, $queryargs = array())
    {
        $query = '';
        if (!empty($queryargs)) {
            $query = '?' . http_build_query($queryargs);
        }
        return $this->_app->urlFor($name)  . $query;
    }

    /**
     * Get the URL for static content relative to webroot
     *
     * @param string $path The file/path you want a link to
     * @return string url.
     */
    public function staticUrl($url)
    {
        $rootUri = $this->_app->request()->getRootUri();

        // Get URL part prepending index.php
        $indexPos = strpos($rootUri, 'index.php');
        if ($indexPos > 0) {
            return substr($rootUri, 0, $indexPos) . $url;
        }
        return $rootUri . '/' . $url;
    }

    public function formatBytes($value)
    {
        return number_format((float)$value) . '&nbsp;<span class="units">bytes</span>';
    }

    public function formatKBytes($value)
    {
        return number_format((float)$value / 1024) . '&nbsp;<span class="units">KB</span>';
    }

    public function formatMBytes($value)
    {
        return number_format((float)$value / 1024 / 1024, 2) . '&nbsp;<span class="units">MB</span>';
    }

    public function formatTitleBytes($value)
    {
        return number_format((float)$value) . '&nbsp;bytes';
    }

    public function formatTitleKBytes($value)
    {
        return number_format((float)$value / 1024) . '&nbsp;KB';
    }

    public function formatTitleMBytes($value)
    {
        return number_format((float)$value / 1024 / 1024, 2) . '&nbsp; MB';
    }

    public function formatTime($value)
    {
        return number_format((float)$value) . '&nbsp;<span class="units">µs</span>';
    }

    public function formatMillisecondTime($value)
    {
        return number_format((float)$value / 1000) . '&nbsp;<span class="units">ms</span>';
    }

    public function formatSecondTime($value)
    {
        return number_format((float)$value / 1000 / 1000, 3) . '&nbsp;<span class="units">s</span>';
    }

    public function formatTitleTime($value)
    {
        return number_format((float)$value) . '&nbsp;µs';
    }

    public function formatTitleMillisecondTime($value)
    {
        return number_format((float)$value / 1000) . '&nbsp;ms';
    }

    public function formatTitleSecondTime($value)
    {
        return number_format((float)$value / 1000 / 1000, 3) . '&nbsp;s';
    }

    public function formatDiff($value)
    {
        $class = 'diff-same';
        $class = $value > 0 ? 'diff-up' : 'diff-down';
        if ($value == 0) {
            $class = 'diff-same';
        }
        return sprintf(
            '<span class="%s">%s</span>',
            $class,
            number_format((float)$value)
        );
    }

    public function makePercent($value, $total)
    {
        $value = (false === empty($total)) ? $value / $total : 0;
        return $this->formatPercent($value);
    }

    public function formatPercent($value)
    {
        return number_format((float)$value * 100, 0) . ' <span class="units">%</span>';
    }
}
