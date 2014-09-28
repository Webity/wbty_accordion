<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Email cloack plugin class.
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.wbty_accordion
 */
class plgContentWbty_accordion extends JPlugin
{
	var $script_added = false;
	/**
	 * Plugin that cloaks all emails in content from spambots via Javascript.
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	mixed	An object with a "text" property or the string to be cloaked.
	 * @param	array	Additional parameters. See {@see plgEmailCloak()}.
	 * @param	int		Optional page number. Unused. Defaults to zero.
	 * @return	boolean	True on success.
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}

		if (is_object($row)) {
			return $this->_scan($row->text);
		}
		return $this->_scan($row);
	}

	protected function _scan(&$text)
	{
		for ($i=0; $i<8; $i++) {
			$text = preg_replace_callback('/({wbty_accordion})(.*)({\/wbty_accordion})/siU',
					  array(get_class($this),'_buildAccordion'),
					  $text);
		}
		return true;
	}
	
	protected function _buildAccordion($matches) {
		
		$header_tag = $this->params->get('header_tag');
		
		if (!$this->script_added) {
			$document =& JFactory::getDocument();
            $jversion = new JVersion();
            $above3 = version_compare($jversion->getShortVersion(), '3.0', 'ge');
            if ($above3) {
                JHTML::_('jquery.framework');
            } else {
                //JHTML::script('plg_content_wbty_accordion/jquery-1.8.3.js', false, true);
            }
			$document->addStyleSheet(JURI::root(true) . '/media/plg_content_wbty_accordion/css/no-theme/jquery-ui-1.9.2.custom.css');
			$document->addScriptDeclaration("
	window.addEvent('load', function() {
		loadAccordion();
	});
	function loadAccordion() {
		if(!window.jQuery)
		{
		   var script = document.createElement('script');
		   script.type = \"text/javascript\";
		   script.src = \"".JURI::root(true) . '/media/plg_content_wbty_accordion/js/jquery-1.8.3.js'."\";
		   document.getElementsByTagName('head')[0].appendChild(script);
		   setTimeout(function() {loadAccordion();}, 1500);
		} else {
			jQuery.getScript(\"".JURI::root(true) . '/media/plg_content_wbty_accordion/js/jquery-ui-1.9.2.custom.min.js'."\")
				.done(function(script, textStatus) {
					jQuery('.wbty_accordion').accordion({
						header: '".$header_tag."', 
						heightStyle: 'content'". ( $this->params->get('collapsed') ? ", active: false, collapsible: true" : "" ) ."
					});
				});
		}
	}
			");
			
			$this->script_added = true;
		}
		
		$return = $matches[2];
		$append = '';
		
		if (strpos($return, '{/wbty_accordion}')!== FALSE) {
			$split = explode('{/wbty_accordion}', $return);
			$return = $split[0];
			unset($split[0]);
			$append = implode('{/wbty_accordion}', $split).'{/wbty_accordion}';
		}
		
		$return = '<div class="wbty_accordion"><div>'.str_replace(array('<'.$header_tag.'>','</'.$header_tag.'>'), array('</div><'.$header_tag.'>','</'.$header_tag.'><div>'), $return).'</div></div>'.$append;
		return $return;
	}
}
