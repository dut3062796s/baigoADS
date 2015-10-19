<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if(!defined("IN_BAIGO")) {
	exit("Access Denied");
}

include_once(BG_PATH_CLASS . "ajax.class.php"); //载入 AJAX 基类
include_once(BG_PATH_MODEL . "opt.class.php"); //载入管理帐号模型

class AJAX_INSTALL {

	private $obj_ajax;
	private $obj_db;

	function __construct() { //构造函数
		$this->obj_ajax       = new CLASS_AJAX(); //初始化 AJAX 基对象

		if (file_exists(BG_PATH_CONFIG . "is_install.php")) {
			$this->obj_ajax->halt_alert("x030403");
		}

		$this->install_init();
		$this->mdl_opt = new MODEL_OPT();
	}


	/**
	 * install_1_do function.
	 *
	 * @access public
	 * @return void
	 */
	function ajax_dbconfig() {
		$_arr_dbconfig = $this->mdl_opt->input_dbconfig();
		if ($_arr_dbconfig["alert"] != "ok") {
			$this->obj_ajax->halt_alert($_arr_dbconfig["alert"]);
		}

		$_arr_return = $this->mdl_opt->mdl_dbconfig();

		if ($_arr_return["alert"] != "y040101") {
			$this->obj_ajax->halt_alert($_arr_return["alert"]);
		}

		$this->obj_ajax->halt_alert("y030404");
	}


	function ajax_dbtable() {
		$this->check_db();

		$this->table_admin();
		$this->table_user();
		$this->table_app();
		$this->table_app_belong();
		$this->table_log();
		$this->view_user();

		$this->obj_ajax->halt_alert("y030103");
	}


	/**
	 * install_2_do function.
	 *
	 * @access public
	 * @return void
	 */
	function ajax_base() {
		$this->check_db();

		$_num_countSrc = 0;

		foreach ($this->obj_ajax->opt["base"] as $_key=>$_value) {
			if ($_value["min"] > 0) {
				$_num_countSrc++;
			}
		}

		$_arr_const = $this->mdl_opt->input_const("base");

		$_num_countInput = count(array_filter($_arr_const));

		if ($_num_countInput < $_num_countSrc) {
			$this->obj_ajax->halt_alert("x030212");
		}

		$_arr_return = $this->mdl_opt->mdl_const("base");

		if ($_arr_return["alert"] != "y040101") {
			$this->obj_ajax->halt_alert($_arr_return["alert"]);
		}

		$this->obj_ajax->halt_alert("y030405");
	}


	/**
	 * install_3_do function.
	 *
	 * @access public
	 * @return void
	 */
	function ajax_reg() {
		$this->check_db();

		$_num_countSrc = 0;

		foreach ($this->obj_ajax->opt["reg"] as $_key=>$_value) {
			if ($_value["min"] > 0) {
				$_num_countSrc++;
			}
		}

		$_arr_const = $this->mdl_opt->input_const("reg");

		$_num_countInput = count(array_filter($_arr_const));

		if ($_num_countInput < $_num_countSrc) {
			$this->obj_ajax->halt_alert("x030212");
		}

		$_arr_return = $this->mdl_opt->mdl_const("reg");

		if ($_arr_return["alert"] != "y040101") {
			$this->obj_ajax->halt_alert($_arr_return["alert"]);
		}

		$this->obj_ajax->halt_alert("y030406");
	}


	function ajax_admin() {
		$this->check_db();

		include_once(BG_PATH_MODEL . "admin.class.php"); //载入管理帐号模型
		$_mdl_admin  = new MODEL_ADMIN();

		$_arr_adminSubmit = $_mdl_admin->input_submit();

		if ($_arr_adminSubmit["alert"] != "ok") {
			$this->obj_ajax->halt_alert($_arr_adminSubmit["alert"]);
		}

		$_arr_adminPass = validateStr(fn_post("admin_pass"), 1, 0);
		switch ($_arr_adminPass["status"]) {
			case "too_short":
				$this->obj_ajax->halt_alert("x020205");
			break;

			case "ok":
				$_str_adminPass = $_arr_adminPass["str"];
			break;
		}

		$_arr_adminPassConfirm = validateStr(fn_post("admin_pass_confirm"), 1, 0);
		switch ($_arr_adminPassConfirm["status"]) {
			case "too_short":
				$this->obj_ajax->halt_alert("x020211");
			break;

			case "ok":
				$_str_adminPassConfirm = $_arr_adminPassConfirm["str"];
			break;
		}

		if ($_str_adminPass != $_str_adminPassConfirm) {
			$this->obj_ajax->halt_alert("x020206");
		}

		$_str_adminRand      = fn_rand(6);
		$_str_adminPassDo    = fn_baigoEncrypt($_str_adminPass, $_str_adminRand);

		$_arr_adminRow = $_mdl_admin->mdl_submit($_str_adminPassDo, $_str_adminRand);

		$this->obj_ajax->halt_alert("y030407");
	}


	function ajax_over() {
		$this->check_db();

		$_arr_return = $this->mdl_opt->mdl_over();

		if ($_arr_return["alert"] != "y040101") {
			$this->obj_ajax->halt_alert($_arr_return["alert"]);
		}

		$this->obj_ajax->halt_alert("y030408");
	}


	private function table_admin() {
		include_once(BG_PATH_MODEL . "admin.class.php"); //载入管理帐号模型
		$_mdl_admin       = new MODEL_ADMIN();
		$_arr_adminRow    = $_mdl_admin->mdl_create_table();

		if ($_arr_adminRow["alert"] != "y020105") {
			$this->obj_ajax->halt_alert($_arr_adminRow["alert"]);
		}
	}


	private function table_user() {
		include_once(BG_PATH_MODEL . "user.class.php"); //载入管理帐号模型
		$_mdl_user    = new MODEL_USER();
		$_arr_userRow = $_mdl_user->mdl_create_table();

		if ($_arr_userRow["alert"] != "y010105") {
			$this->obj_ajax->halt_alert($_arr_userRow["alert"]);
		}
	}


	private function table_app() {
		include_once(BG_PATH_MODEL . "app.class.php"); //载入管理帐号模型
		$_mdl_app     = new MODEL_APP();
		$_arr_appRow  = $_mdl_app->mdl_create_table();

		if ($_arr_appRow["alert"] != "y050105") {
			$this->obj_ajax->halt_alert($_arr_appRow["alert"]);
		}
	}


	private function table_app_belong() {
		include_once(BG_PATH_MODEL . "appBelong.class.php"); //载入管理帐号模型
		$_mdl_appBelong       = new MODEL_APP_BELONG();
		$_arr_appBelongRow    = $_mdl_appBelong->mdl_create_table();

		if ($_arr_appBelongRow["alert"] != "y070105") {
			$this->obj_ajax->halt_alert($_arr_appBelongRow["alert"]);
		}
	}


	private function table_log() {
		include_once(BG_PATH_MODEL . "log.class.php"); //载入管理帐号模型
		$_mdl_log     = new MODEL_LOG();
		$_arr_logRow  = $_mdl_log->mdl_create_table();

		if ($_arr_logRow["alert"] != "y060105") {
			$this->obj_ajax->halt_alert($_arr_logRow["alert"]);
		}
	}


	private function view_user() {
		include_once(BG_PATH_MODEL . "user.class.php"); //载入管理帐号模型
		$_mdl_user    = new MODEL_USER();
		$_arr_user    = $_mdl_user->mdl_create_view();

		if ($_arr_user["alert"] != "y010108") {
			$this->obj_ajax->halt_alert($_arr_user["alert"]);
		}
	}


	private function check_db() {
		if (!fn_token("chk")) { //令牌
			$this->obj_ajax->halt_alert("x030102");
		}

		if (strlen(BG_DB_HOST) < 1 || strlen(BG_DB_NAME) < 1 || strlen(BG_DB_USER) < 1 || strlen(BG_DB_PASS) < 1 || strlen(BG_DB_CHARSET) < 1) {
			$this->obj_ajax->halt_alert("x030404");
		} else {
			if (!defined("BG_DB_PORT")) {
				define("BG_DB_PORT", "3306");
			}

			$_cfg_host = array(
				"host"      => BG_DB_HOST,
				"name"      => BG_DB_NAME,
				"user"      => BG_DB_USER,
				"pass"      => BG_DB_PASS,
				"charset"   => BG_DB_CHARSET,
				"debug"     => BG_DEBUG_DB,
				"port"      => BG_DB_PORT,
			);

			$GLOBALS["obj_db"]   = new CLASS_MYSQLI($_cfg_host); //设置数据库对象
			$this->obj_db        = $GLOBALS["obj_db"];

			if (!$this->obj_db->connect()) {
				$this->obj_ajax->halt_alert("x030111");
			}

			if (!$this->obj_db->select_db()) {
				$this->obj_ajax->halt_alert("x030112");
			}
		}
	}


	private function install_init() {
		$_arr_extRow     = get_loaded_extensions();
		$_num_errCount   = 0;

		foreach ($this->obj_ajax->type["ext"] as $_key=>$_value) {
			if (!in_array($_key, $_arr_extRow)) {
				$_num_errCount++;
			}
		}

		if ($_num_errCount > 0) {
			$this->obj_ajax->halt_alert("x030417");
		}
	}
}
