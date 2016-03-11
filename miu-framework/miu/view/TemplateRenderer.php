<?php
namespace miu\view;

abstract class TemplateRenderer extends CompositeRenderer

{
	protected $data = array();


	public function render($type)
	{
		$template = $this->getPreferredTemplate($type);
		if(!$template)
			$template = $this->getDefaultTemplate($type);
		if(!$template)
			throw new MissingTemplateException($this->getTemplateClass() . ': could not find a valid template for type ' . $type);

		ob_start();
			$meta_data = array('data' => $this->data, 'components' => $this->components);
			extract($meta_data, EXTR_SKIP);
			include($template);
			$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	protected function getPreferredTemplate($type)
	{
		$autoloader = \miu\app\AutoLoader::getInstance();
		$user_agent = \miu\app\UserAgentManager::getInstance()->getUserAgent();
		return $autoloader->getFileLocation($this->getTemplateClass().'.'.$user_agent.'.'.$type);
	}

	protected function getDefaultTemplate($type)
	{
		$autoloader = \miu\app\AutoLoader::getInstance();
		return $autoloader->getFileLocation($this->getTemplateClass().'.default.'.$type);
	}

	protected function getTemplateClass()
	{
		return get_class($this);
	}

}
?>
