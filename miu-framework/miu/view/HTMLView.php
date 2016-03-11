<?php

namespace miu\view;

class HTMLView extends View
{
	private $headTags = array();

	protected function getDependencyClasses()
	{
		return array(
			ScriptDependency::getResource(''),
			StylesheetDependency::getResource('')
		);
	}

	protected function renderWrapper($dependencies, $content)
	{
		$result = '<!doctype html><head>'.
			'<meta charset="'.\env::get('settings.encoding').'">'.
			'<title>'.\env::get('settings.name').'</title>';
		foreach($this->headTags as $tag)
		{
			$result.= $tag->render();
		}

		foreach($dependencies as $dependency)
		{
			$result.= $dependency->render();
		}

		$result.= "</head><body>\n".$content->render('html')."\n</body></html>";
		return $result;
	}

	public function addTag(Tag $tag)
	{
		$this->headTags[] = $tag;
	}
}
?>
