<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class ilVimpPageComponentPluginGUI
 *
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilVimpPageComponentPluginGUI: ilPCPluggedGUI
 */
class ilVimpPageComponentPluginGUI extends ilPageComponentPluginGUI {

	const CMD_CREATE = 'create';
	const CMD_INSERT = 'insert';
	const CMD_SHOW_FILTERED = 'showFiltered';


	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;

	public function __construct() {
		global $ilCtrl, $tpl;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
	}

	public function executeCommand() {
		$next_class = $this->ctrl->getNextClass();

		switch ($next_class) {
			default:
				if ($cmd = $_GET['vpco_cmd']) {
					$this->{$cmd}();
					break;
				} else {
					$cmd = $this->ctrl->getCmd();
					$this->$cmd();
					break;
				}
		}
	}

	protected function redirect($cmd) {
		$this->ctrl->setParameter($this, 'vpco_cmd', $cmd);
		$this->ctrl->redirect($this, self::CMD_INSERT);
	}


	public function insert() {
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'), true);
		$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_CREATE);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->setResetCommand('');
		$this->tpl->setContent($table_gui->getHTML());
	}

	/**
	 *
	 */
	protected function showFiltered() {
		$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$this->tpl->setContent($table_gui->getHTML());
	}


	/**
	 *
	 */
	public function applyFilter() {
		$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->resetOffset();
		$table_gui->writeFilterToSession();
		$this->redirect(self::CMD_SHOW_FILTERED);
	}


//	/**
//	 *
//	 */
//	public function resetFilter() {
//		$table_gui = new xvmpSearchVideosTableGUI($this, self::CMD_INSERT);
//		$table_gui->resetOffset();
//		$table_gui->resetFilter();
//		$this->ctrl->redirect($this, self::CMD_INSERT);
//	}



	public function create() {
		global $lng;

		$mid = $_GET['mid'];

		$video_properties = array(
			"mid" => $mid,
			"width" => 200,
			"height" => 150
		);

		if ($this->createElement($video_properties)) {
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$this->returnToParent();
		}
	}


	public function edit() {
		global $tpl;

		$form = $this->initForm();
		$tpl->setContent($form->getHTML());
	}


	public function update() {
		global $tpl, $lng;

		$form = $this->initForm();
		if ($form->checkInput()) {
			$properties = $this->getProperties();
			$properties['width'] = $form->getInput('width');
			$properties['height'] = $form->getInput('height');
			if ($this->updateElement($properties)) {
				ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
				$this->returnToParent();
			}
		}

		$form->setValuesByPost();
		$tpl->setContent($form->getHtml());
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	public function initForm() {
		global $lng, $ilCtrl;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();

		// width
		$width = new ilTextInputGUI($this->getPlugin()->txt("width"), "width");
		$width->setMaxLength(4);
		$width->setSize(40);
		$width->setRequired(true);
		$form->addItem($width);

		// height
		$height = new ilTextInputGUI($this->getPlugin()->txt("height"), "height");
		$height->setMaxLength(3);
		$height->setSize(40);
		$height->setRequired(true);

		$form->addItem($height);

		$prop = $this->getProperties();
		$width->setValue($prop["width"]);
		$height->setValue($prop["height"]);

		$form->addCommandButton("update", $lng->txt("save"));
		$form->addCommandButton("cancel", $lng->txt("cancel"));
		$form->setTitle($this->getPlugin()->txt("edit_ex_el"));

		$form->setFormAction($ilCtrl->getFormAction($this));

		return $form;
	}


	public function cancel() {
		$this->returnToParent();
	}


	/**
	 * Get HTML for element
	 *
	 * @param       $a_mode
	 * @param array $a_properties
	 * @param       $a_plugin_version
	 *
	 * @return mixed
	 */
	public function getElementHTML($a_mode, array $a_properties, $a_plugin_version) {
		$video = xvmpMedium::find($a_properties['mid']);

		xvmpVideoPlayer::loadVideoJSAndCSS(false);
		$video_player = new xvmpVideoPlayer($video);
		$video_player->setOption('height', $a_properties['height'] . 'px');
		$video_player->setOption('width', $a_properties['width'] . 'px');
		return $video_player->getHTML();
	}
}