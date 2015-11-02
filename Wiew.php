<?php

namespace Gresakg\Wiew;

class Wiew {
	
	protected $templates;
	
	protected $data;
	
	protected $reload = false;

	public function __construct($templates) {
		$this->templates = $templates;
		$this->setDataObject();
	}


	public function render($template, $data=array()) {
		$this->add($data);
		$templateFile = $this->resolveTemplate($template);
		extract((array) $this->data);
		$view = $this;
		ob_start();
		include $templateFile;
		$contents = ob_get_contents();
		ob_end_clean();
		$contents = $this->doReload($contents);
		return $contents;
	}
	
	public function add(array $data, $namespace='page') {
		$this->data->$namespace->appendArray($data);
	}
	
	public function loadInto($var, $template) {
		$this->reload = [$var, $template];
	}
	
	protected function doReload($contents) {
		if($this->reload === false) return $contents;
		$variable = $this->reload[0]; $template = $this->reload[1];
		$this->reload = false;
		$this->data->widget->$variable = $contents;
		$this->reload = false; // reset to avoid loop
		return $this->render($template);
	
	}
	
	protected function resolveTemplate($template) {
		foreach($this->templates as $location) {
			$templateFile = rtrim($location,"/")."/".trim($template,"/");
			if(file_exists($templateFile)) {
				return $templateFile;
			}
		}
	}
	
	private function setDataObject() {
		$this->data = new Data();
		$this->data->addChild('page');
		$this->data->addChild('widget');
		$this->data->addChild('site');
	}
	
}
