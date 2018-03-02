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
	const CMD_STANDARD = self::CMD_INSERT;
	const CMD_SHOW = 'show';
	const CMD_SHOW_FILTERED = 'showFiltered';
	const CMD_SHOW_FILTERED_OWN_VIDEOS = 'showFilteredOwnVideos';
	const CMD_OWN_VIDEOS = 'indexOwnVideos';
	const CMD_SHOW_OWN_VIDEOS = 'showOwnVideos';
	const CMD_EDIT_VIDEO = 'editVideo';
	const CMD_DELETE_VIDEO = 'deleteVideo';
	const CMD_UPDATE_VIDEO = 'updateVideo';

	const SUBTAB_SEARCH = 'subtab_search';
	const SUBTAB_OWN_VIDEOS = 'subtab_own_videos';


	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilVimpPageComponentPlugin
	 */
	protected $pl;


	/**
	 * ilVimpPageComponentPluginGUI constructor.
	 */
	public function __construct() {
		global $ilCtrl, $tpl, $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->pl = new ilVimpPageComponentPlugin();
	}


	/**
	 *
	 */
	public function executeCommand() {
		try {
			$next_class = $this->ctrl->getNextClass();

			switch ($next_class) {
				default:
					if ($cmd = $_GET['vpco_cmd']) {
						$this->$cmd();
						break;
					} else {
						$cmd = $this->ctrl->getCmd();
						$this->$cmd();
						break;
					}
			}
		} catch (xvmpException $e) {
			ilUtil::sendFailure($e->getMessage(), true);
			$this->ctrl->returnToParent($this);
		}
	}


	/**
	 * @param $cmd
	 */
	public function redirect($cmd) {
		$this->ctrl->setParameter($this, 'vpco_cmd', $cmd);
		$this->ctrl->redirect($this, self::CMD_INSERT);
	}


	/**
	 * @param $cmd
	 *
	 * @return string
	 */
	public function getLinkTarget($cmd) {
		$this->ctrl->setParameter($this, 'vpco_cmd', $cmd);
		return $this->ctrl->getLinkTarget($this, self::CMD_INSERT);
	}


	/**
	 *
	 */
	public function insert() {
		$this->setSubTabs(self::SUBTAB_SEARCH);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'));
		try {
			$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_CREATE);
		} catch (xvmpException $e) {
			ilUtil::sendFailure($e->getMessage(), true);
			$this->ctrl->returnToParent($this);
		}
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$this->tpl->setContent($table_gui->getHTML());
	}


	/**
	 *
	 */
	public function show() {
		$this->setSubTabs(self::SUBTAB_SEARCH);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'));
		try {
			$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_CREATE);
		} catch (xvmpException $e) {
			ilUtil::sendFailure($e->getMessage(), true);
			$this->ctrl->returnToParent($this);
		}
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$this->tpl->setContent($table_gui->getHTML());
	}

	/**
	 *
	 */
	protected function showFiltered() {
		xvmpVideoPlayer::loadVideoJSAndCSS(false);
		$this->setSubTabs(self::SUBTAB_SEARCH);
		$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$table_gui->determineOffsetAndOrder();
		$this->tpl->setContent($table_gui->getHTML() . xvmpGUI::getModalPlayer()->getHTML());
	}


	/**
	 *
	 */
	public function applyFilter() {
		$this->ctrl->clearParameters($this);
		$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->resetOffset();
		$table_gui->writeFilterToSession();
		$this->redirect(self::CMD_SHOW_FILTERED);
	}


	/**
	 *
	 */
	public function resetFilter() {
		$table_gui = new xvmpSearchVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->resetOffset();
		$table_gui->resetFilter();
		$this->ctrl->redirect($this, self::CMD_INSERT);
	}


	/**
	 *
	 */
	public function indexOwnVideos() {
		$this->setSubTabs(self::SUBTAB_OWN_VIDEOS);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'), true);
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_CREATE);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$this->tpl->setContent($table_gui->getHTML());
	}


	/**
	 *
	 */
	public function showOwnVideos() {
		$this->setSubTabs(self::SUBTAB_OWN_VIDEOS);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'), true);
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_CREATE);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$table_gui->determineOffsetAndOrder();
		$this->tpl->setContent($table_gui->getHTML());
	}

	/**
	 *
	 */
	public function applyFilterOwnVideos() {
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->resetOffset();
		$table_gui->writeFilterToSession();
		$this->redirect(self::CMD_SHOW_FILTERED_OWN_VIDEOS);
	}

	/**
	 *
	 */
	protected function showFilteredOwnVideos() {
		xvmpVideoPlayer::loadVideoJSAndCSS(false);
		$this->setSubTabs(self::SUBTAB_OWN_VIDEOS);
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$this->tpl->setContent($table_gui->getHTML() . xvmpGUI::getModalPlayer()->getHTML());
	}

	/**
	 *
	 */
	public function editVideo() {
		$mid = $_GET['mid'];
		$xvmpEditVideoFormGUI = new xvmpEditVideoFormGUI($this, $mid);
		$xvmpEditVideoFormGUI->fillForm();
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}


	/**
	 *
	 */
	public function updateVideo() {
		$xvmpEditVideoFormGUI = new xvmpEditVideoFormGUI($this, $_POST['mid']);
		$xvmpEditVideoFormGUI->setValuesByPost();
		if ($xvmpEditVideoFormGUI->saveForm()) {
			ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
			$this->redirect(xvmpOwnVideosGUI::CMD_EDIT_VIDEO);
		}
		ilUtil::sendFailure($this->pl->txt('msg_incomplete'));
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}

	/**
	 *
	 */
	public function uploadVideoForm() {
		$xvmpEditVideoFormGUI = new xvmpUploadVideoFormGUI($this);
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}


	/**
	 *
	 */
	public function createVideo() {
		$xvmpEditVideoFormGUI = new xvmpUploadVideoFormGUI($this);
		$xvmpEditVideoFormGUI->setValuesByPost();
		if ($xvmpEditVideoFormGUI->uploadVideo()) {
			ilUtil::sendSuccess($this->pl->txt('video_uploaded'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		}

		ilUtil::sendFailure($this->pl->txt('form_incomplete'));
		$xvmpEditVideoFormGUI->setValuesByPost();
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}


	/**
	 *
	 */
	public function deleteVideo() {
		$mid = $_GET['mid'];
		$video = xvmpMedium::find($mid);
		$confirmation_gui = new ilConfirmationGUI();
		$confirmation_gui->setFormAction($this->ctrl->getFormAction($this));
		$confirmation_gui->setHeaderText($this->pl->txt('confirm_delete_text'));
		$confirmation_gui->addItem('mid', $mid, $video->getTitle());
		$confirmation_gui->setConfirm($this->lng->txt('delete'),xvmpOwnVideosGUI::CMD_CONFIRMED_DELETE_VIDEO);
		$confirmation_gui->setCancel($this->lng->txt('cancel'), xvmpOwnVideosGUI::CMD_STANDARD);
		$this->tpl->setContent($confirmation_gui->getHTML());
	}


	/**
	 *
	 */
	public function confirmedDeleteVideo() {
		$mid = $_POST['mid'];

		// fetch the video for logging purposes
		$video = xvmpMedium::getObjectAsArray($mid);

		xvmpMedium::deleteObject($mid);

//		xvmpEventLog::logEvent(xvmpEventLog::ACTION_DELETE, $this->getObjId(), $video);

		ilUtil::sendSuccess($this->pl->txt('video_deleted'), true);
		$this->redirect(self::CMD_STANDARD);
	}

	/**
	 *
	 */
	protected function uploadChunks() {
		$xoctPlupload = new xoctPlupload();
		$tmp_id = $_GET['tmp_id'];

		$dir = ILIAS_ABSOLUTE_PATH  . ltrim(ilUtil::getWebspaceDir(), '.') . '/vimp/' . $tmp_id;
		if (!is_dir($dir)) {
			ilUtil::makeDir($dir);
		}

		$xoctPlupload->setTargetDir($dir);
		$xoctPlupload->handleUpload();
	}

	/**
	 *
	 */
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


	/**
	 *
	 */
	public function edit() {
		global $tpl;

		$form = $this->initForm();
		$tpl->setContent($form->getHTML());
	}


	/**
	 *
	 */
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


	/**
	 *
	 */
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
		try {
			$video = xvmpMedium::find($a_properties['mid']);
		} catch (xvmpException $e) {
//			ilUtil::sendInfo($e->getMessage());
			return '<img 
				src="' . ilViMPPlugin::getInstance()->getImagePath('not_available.png') . '" 
				height="' . $a_properties['height'] . '" 
				width="' . $a_properties['width'] . '"
			>';
		}

		xvmpVideoPlayer::loadVideoJSAndCSS(false);
		$video_player = new xvmpVideoPlayer($video, xvmpConf::getConfig(xvmpConf::F_EMBED_PLAYER));
		$video_player->setOption('height', $a_properties['height'] . 'px');
		$video_player->setOption('width', $a_properties['width'] . 'px');
		return $video_player->getHTML();
	}


	/**
	 * @param $active
	 */
	protected function setSubTabs($active) {
		$this->tabs->addSubTab(self::SUBTAB_SEARCH, $this->pl->txt(self::SUBTAB_SEARCH), $this->getLinkTarget(self::CMD_STANDARD));
		$this->tabs->addSubTab(self::SUBTAB_OWN_VIDEOS, $this->pl->txt(self::SUBTAB_OWN_VIDEOS), $this->getLinkTarget(self::CMD_OWN_VIDEOS));
		$this->tabs->setSubTabActive($active);
	}
}