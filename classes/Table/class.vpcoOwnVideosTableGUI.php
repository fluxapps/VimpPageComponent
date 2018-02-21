<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class vpcoOwnVideosTableGUI
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class vpcoOwnVideosTableGUI extends xvmpOwnVideosTableGUI {

	protected $available_columns = array(
		'thumbnail' => array(
			'no_header' => true
		),
		'title' => array(
			'sort_field' => 'title',
		),
		'published' => array(
			'sort_field' => 'published'
		),
		'status' => array(
			'sort_field' => 'status'
		),
		'created_at' => array(
			'sort_field' => 'unix_time'
		)
	);

	/**
	 * vpcoSearchVideosTableGUI constructor.
	 *
	 * @param int    $parent_gui
	 * @param string $parent_cmd
	 */
	public function __construct($parent_gui, $parent_cmd) {
		parent::__construct($parent_gui, $parent_cmd);

		$base_link = $this->ctrl->getLinkTargetByClass(array(ilObjPluginDispatchGUI::class, ilObjViMPGUI::class, xvmpOwnVideosGUI::class),'', '', true);
		$this->tpl_global->addOnLoadCode('VimpContent.ajax_base_url = "'.$base_link.'";');

		$this->pl = new ilVimpPageComponentPlugin();
		$this->vimp_pl = ilViMPPlugin::getInstance();
		$this->setRowTemplate($this->pl->getDirectory() . '/templates/' . static::ROW_TEMPLATE);

		$this->addHiddenInput('pco_data', json_encode($_POST));
		$this->addHiddenInput('commandpg', $_POST['commandpg']);
		$this->addHiddenInput('target', json_encode($_POST['target']));

		$this->ctrl->setParameter($this->parent_obj, 'vpco_cmd', 'applyFilterOwnVideos');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_obj));
		$this->ctrl->clearParameters($this->parent_obj);
	}


	/**
	 *
	 */
	protected function initColumns() {
		$this->addColumn('', '', 210, true);

		xvmpTableGUI::initColumns();

		$this->addColumn('', '', 75, true);	}


	/**
	 * @param xvmpObject $a_set
	 */
	protected function fillRow($a_set) {
		$transcoded = ($a_set['status'] == 'legal');

		if ($a_set['status'] == 'error') {
			$this->tpl->setVariable('VAL_DISABLED', 'disabled');
		}

		$this->tpl->setVariable('VAL_MID', $a_set['mid']);

		$this->tpl->setVariable('VAL_STATUS_TEXT', $this->vimp_pl->txt('status_' . $a_set['status']));


		foreach ($this->available_columns as $title => $props)
		{
			$this->tpl->setVariable('VAL_' . strtoupper($title), $a_set[$title]);
		}

		$this->tpl->setVariable('VAL_ADD', $this->getAddButton($a_set));
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * @param $a_set
	 *
	 * @return string
	 */
	protected function getAddButton($a_set) {
		$button = ilLinkButton::getInstance();
		$button->setCaption('add');
		$this->ctrl->setParameter($this->parent_obj, 'mid', $a_set['mid']);
		$button->setUrl($this->ctrl->getLinkTarget($this->parent_obj, ilVimpPageComponentPluginGUI::CMD_CREATE));
		return $button->getToolbarHTML();
	}

//	/**
//	 * @param $a_set
//	 *
//	 * @return string
//	 */
//	protected function buildActionList($a_set) {
//		$actions = new ilAdvancedSelectionListGUI();
//		$actions->setListTitle($this->lng->txt('actions'));
//		$this->ctrl->setParameter($this->parent_obj, 'mid', $a_set['mid']);
//		if ($a_set['status'] == 'legal') {
//			$actions->addItem($this->lng->txt('edit'), 'edit', $this->parent_obj->getLinkTarget(ilVimpPageComponentPluginGUI::CMD_EDIT_VIDEO));
//		}
//		$actions->addItem($this->lng->txt('delete'), 'delete', $this->parent_obj->getLinkTarget(ilVimpPageComponentPluginGUI::CMD_DELETE_VIDEO));
//		return $actions->getHTML();	}
}