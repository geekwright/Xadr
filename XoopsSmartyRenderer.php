<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf\Xadr;

/**
 * The XoopsSmartyRenderer is a XOOPS specific renderer that uses XOOPS
 * Smarty templates and the standard $xoopsTpl mechanisms for page
 * rendering. Renderer attributes become Smarty assigned variables,
 * and the actual display is handled by the normal XOOPS cycle.
 *
 * @category  Xmf\Xadr\XoopsSmartyRenderer
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class XoopsSmartyRenderer extends Renderer
{

    /** signal that we used a default template, just dump attributes */
    private $dumpmode;

    protected $xoops;

    /**
     * initContextAware - called by ContextAware::__construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        parent::initContextAware();
        $this->dumpmode   = false;
        $this->xoops = \Xoops::getInstance();
    }


    /**
     * Render the view.
     *
     * We actually just
     * - make sure that a template is set
     * - assign attributes to smarty variables
     *
     * @return void
     */
    public function execute()
    {
        global $xoopsTpl, $xoopsOption;
        if ($this->template == null) {
            if (empty($xoopsOption['template_main'])) {
                $this->template = 'module:system/system_dummy.tpl';
                //$this->dumpmode   = true;
            } else {
                $this->template = $xoopsOption['template_main'];
            }
        }

        // make it easier to access data directly in the template
        $mojavi   = $this->controller()->getMojavi();
        $template = $this->attributes->getAll();
        if ($this->dumpmode) {
            $template['dummy_content']
                ='<pre>' . print_r($this->attributes->getAll(), true) . '</pre>';
        } else {
            $template = $this->attributes->getAll();
        }

        if ($this->mode == Xadr::RENDER_VARIABLE
            || $this->controller()->getRenderMode() == Xadr::RENDER_VARIABLE
        ) {
            $varRender = new XoopsTplRender;
            $varRender->setTemplate($this->template);
            foreach ($template as $k => $v) {
                $varRender->setAttribute($k, $v);
            }
            $varRender->setAttribute('xadr', $mojavi);
            $this->result=$varRender->fetch();
            // echo $this->result;

        } else {
            $GLOBALS['xoopsOption']['template_main'] = $this->template;
            // the following is to make footer.php quit complaining
            if (false === strpos($xoopsOption['template_main'], ':')) {
                $GLOBALS['xoTheme']->contentTemplate
                    = $xoopsOption['template_main'];
            } else {
                $GLOBALS['xoTheme']->contentTemplate = $xoopsOption['template_main'];
            }

            foreach ($template as $k => $v) {
                $xoopsTpl->assign($k, $v);
            }
            $xoopsTpl->assign('xadr', $mojavi);
            // templates and values are assigned, XOOPS will handle the rest
        }
    }
}
