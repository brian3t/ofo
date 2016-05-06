<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  zip_class.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

  define( 'XPrptrZIP_READ_BLOCK_SIZE', 2048 );
  define( 'XPrptrZIP_SEPARATOR', ',' );
  define( 'XPrptrZIP_ERROR_EXTERNAL', 0 );
  define( 'XPrptrZIP_TEMPORARY_DIR', '/zips/' );
  $g_xprptrzip_version = "1.0";
  define( 'XPrptrZIP_ERR_USER_ABORTED', 2 );
  define( 'XPrptrZIP_ERR_NO_ERROR', 0 );
  define( 'XPrptrZIP_ERR_WRITE_OPEN_FAIL', -1 );
  define( 'XPrptrZIP_ERR_READ_OPEN_FAIL', -2 );
  define( 'XPrptrZIP_ERR_INVALID_PARAMETER', -3 );
  define( 'XPrptrZIP_ERR_MISSING_FILE', -4 );
  define( 'XPrptrZIP_ERR_FILENAME_TOO_LONG', -5 );
  define( 'XPrptrZIP_ERR_INVALID_ZIP', -6 );
  define( 'XPrptrZIP_ERR_BAD_EXTRACTED_FILE', -7 );
  define( 'XPrptrZIP_ERR_DIR_CREATE_FAIL', -8 );
  define( 'XPrptrZIP_ERR_BAD_EXTENSION', -9 );
  define( 'XPrptrZIP_ERR_BAD_FORMAT', -10 );
  define( 'XPrptrZIP_ERR_DELETE_FILE_FAIL', -11 );
  define( 'XPrptrZIP_ERR_RENAME_FILE_FAIL', -12 );
  define( 'XPrptrZIP_ERR_BAD_CHECKSUM', -13 );
  define( 'XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP', -14 );
  define( 'XPrptrZIP_ERR_MISSING_OPTION_VALUE', -15 );
  define( 'XPrptrZIP_ERR_INVALID_OPTION_VALUE', -16 );
  define( 'XPrptrZIP_ERR_ALREADY_A_DIRECTORY', -17 );
  define( 'XPrptrZIP_ERR_UNSUPPORTED_COMPRESSION', -18 );
  define( 'XPrptrZIP_ERR_UNSUPPORTED_ENCRYPTION', -19 );
  define( 'XPrptrZIP_OPT_PATH', 77001 );
  define( 'XPrptrZIP_OPT_ADD_PATH', 77002 );
  define( 'XPrptrZIP_OPT_REMOVE_PATH', 77003 );
  define( 'XPrptrZIP_OPT_REMOVE_ALL_PATH', 77004 );
  define( 'XPrptrZIP_OPT_SET_CHMOD', 77005 );
  define( 'XPrptrZIP_OPT_EXTRACT_AS_STRING', 77006 );
  define( 'XPrptrZIP_OPT_NO_COMPRESSION', 77007 );
  define( 'XPrptrZIP_OPT_BY_NAME', 77008 );
  define( 'XPrptrZIP_OPT_BY_INDEX', 77009 );
  define( 'XPrptrZIP_OPT_BY_EREG', 77010 );
  define( 'XPrptrZIP_OPT_BY_PREG', 77011 );
  define( 'XPrptrZIP_OPT_COMMENT', 77012 );
  define( 'XPrptrZIP_OPT_ADD_COMMENT', 77013 );
  define( 'XPrptrZIP_OPT_PREPEND_COMMENT', 77014 );
  define( 'XPrptrZIP_OPT_EXTRACT_IN_OUTPUT', 77015 );
  define( 'XPrptrZIP_OPT_REPLACE_NEWER', 77016 );
  define( 'XPrptrZIP_OPT_STOP_ON_ERROR', 77017 );
  define( 'XPrptrZIP_CB_PRE_EXTRACT', 78001 );
  define( 'XPrptrZIP_CB_POST_EXTRACT', 78002 );
  define( 'XPrptrZIP_CB_PRE_ADD', 78003 );
  define( 'XPrptrZIP_CB_POST_ADD', 78004 );
	
   class XPraptorZIP
  {
    var $zipname = '';
    var $zip_fd = 0;
    var $error_code = 1;
    var $error_string = '';

  function XPraptorZIP($p_zipname)
  {
    if (!function_exists('gzopen'))
    {
      die('Interrupted ' . basename(__FILE__) . ' : Not recognized file extension');
    }
    $this->zipname = $p_zipname;
    $this->zip_fd = 0;

    return;
  }

  function create($p_filelist /*, options */)
  {
    $v_result=1;
    $this->privErrorReset();
    $v_options = array();
    $v_add_path = "";
    $v_remove_path = "";
    $v_remove_all_path = false;
    $v_options[XPrptrZIP_OPT_NO_COMPRESSION] = FALSE;

    $v_size = func_num_args();
    if ($v_size > 1) {
      $v_arg_list = &func_get_args();
      array_shift($v_arg_list);
      $v_size--;
      if ((is_integer($v_arg_list[0])) && ($v_arg_list[0] > 77000)) {
        $v_result = $this->privParseOptions($v_arg_list, $v_size, $v_options,
                                            array (XPrptrZIP_OPT_REMOVE_PATH => 'optional',
                                                   XPrptrZIP_OPT_REMOVE_ALL_PATH => 'optional',
                                                   XPrptrZIP_OPT_ADD_PATH => 'optional',
                                                   XPrptrZIP_CB_PRE_ADD => 'optional',
                                                   XPrptrZIP_CB_POST_ADD => 'optional',
                                                   XPrptrZIP_OPT_NO_COMPRESSION => 'optional',
                                                   XPrptrZIP_OPT_COMMENT => 'optional'
												   ));
        if ($v_result != 1) {
          return 0;
        }

        if (isset($v_options[XPrptrZIP_OPT_ADD_PATH])) {
          $v_add_path = $v_options[XPrptrZIP_OPT_ADD_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_PATH])) {
          $v_remove_path = $v_options[XPrptrZIP_OPT_REMOVE_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH])) {
          $v_remove_all_path = $v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH];
        }
      }

      else {
        $v_add_path = $v_arg_list[0];
        if ($v_size == 2) {
          $v_remove_path = $v_arg_list[1];
        }
        else if ($v_size > 2) {
          XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER,
		                       "Incorrect arguments number / type");

          return 0;
        }
      }
    }

    $p_result_list = array();
    if (is_array($p_filelist))
    {
      $v_result = $this->privCreate($p_filelist, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options);
    }

    else if (is_string($p_filelist))
    {
      $v_list = explode(XPrptrZIP_SEPARATOR, $p_filelist);

      $v_result = $this->privCreate($v_list, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options);
    }

    else
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect variable type p_filelist ");
      $v_result = XPrptrZIP_ERR_INVALID_PARAMETER;
    }

    if ($v_result != 1)
    {
      return 0;
    }

    return $p_result_list;
  }

  function add($p_filelist /* options */)
  {
    $v_result=1;

    $this->privErrorReset();

    $v_options = array();
    $v_add_path = "";
    $v_remove_path = "";
    $v_remove_all_path = false;
    $v_options[XPrptrZIP_OPT_NO_COMPRESSION] = FALSE;
    $v_size = func_num_args();

    if ($v_size > 1) {
      $v_arg_list = &func_get_args();

      array_shift($v_arg_list);
      $v_size--;
      if ((is_integer($v_arg_list[0])) && ($v_arg_list[0] > 77000)) {
        $v_result = $this->privParseOptions($v_arg_list, $v_size, $v_options,
                                            array (XPrptrZIP_OPT_REMOVE_PATH => 'optional',
                                                   XPrptrZIP_OPT_REMOVE_ALL_PATH => 'optional',
                                                   XPrptrZIP_OPT_ADD_PATH => 'optional',
                                                   XPrptrZIP_CB_PRE_ADD => 'optional',
                                                   XPrptrZIP_CB_POST_ADD => 'optional',
                                                   XPrptrZIP_OPT_NO_COMPRESSION => 'optional',
                                                   XPrptrZIP_OPT_COMMENT => 'optional',
                                                   XPrptrZIP_OPT_ADD_COMMENT => 'optional',
                                                   XPrptrZIP_OPT_PREPEND_COMMENT => 'optional'
												   ));
        if ($v_result != 1) {
          return 0;
        }

        if (isset($v_options[XPrptrZIP_OPT_ADD_PATH])) {
          $v_add_path = $v_options[XPrptrZIP_OPT_ADD_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_PATH])) {
          $v_remove_path = $v_options[XPrptrZIP_OPT_REMOVE_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH])) {
          $v_remove_all_path = $v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH];
        }
      }

      else {
        $v_add_path = $v_arg_list[0];
        if ($v_size == 2) {
          $v_remove_path = $v_arg_list[1];
        }
        else if ($v_size > 2) {
          XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect arguments number / type");
          return 0;
        }
      }
    }

    $p_result_list = array();
    if (is_array($p_filelist))
    {
      $v_result = $this->privAdd($p_filelist, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options);
    }

    else if (is_string($p_filelist))
    {
      $v_list = explode(XPrptrZIP_SEPARATOR, $p_filelist);

      $v_result = $this->privAdd($v_list, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options);
    }

    else
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect variable type p_filelist ");
      $v_result = XPrptrZIP_ERR_INVALID_PARAMETER;
    }

    if ($v_result != 1)
    {
      return 0;
    }

    return $p_result_list;
  }

  function listContent()
  {
    $v_result=1;
    $this->privErrorReset();

    if (!$this->privCheckFormat()) {
      return(0);
    }

    $p_list = array();
    if (($v_result = $this->privList($p_list)) != 1)
    {
      unset($p_list);
      return(0);
    }
    return $p_list;
  }

  function extract(/* options */)
  {
    $v_result=1;
    $this->privErrorReset();

    if (!$this->privCheckFormat()) {
      return(0);
    }

    $v_options = array();
    $v_path = "./";
    $v_remove_path = "";
    $v_remove_all_path = false;

    $v_size = func_num_args();
    $v_options[XPrptrZIP_OPT_EXTRACT_AS_STRING] = FALSE;

    if ($v_size > 0) {

      $v_arg_list = func_get_args();

      if ((is_integer($v_arg_list[0])) && ($v_arg_list[0] > 77000)) {
        $v_result = $this->privParseOptions($v_arg_list, $v_size, $v_options,
                                            array (XPrptrZIP_OPT_PATH => 'optional',
                                                   XPrptrZIP_OPT_REMOVE_PATH => 'optional',
                                                   XPrptrZIP_OPT_REMOVE_ALL_PATH => 'optional',
                                                   XPrptrZIP_OPT_ADD_PATH => 'optional',
                                                   XPrptrZIP_CB_PRE_EXTRACT => 'optional',
                                                   XPrptrZIP_CB_POST_EXTRACT => 'optional',
                                                   XPrptrZIP_OPT_SET_CHMOD => 'optional',
                                                   XPrptrZIP_OPT_BY_NAME => 'optional',
                                                   XPrptrZIP_OPT_BY_EREG => 'optional',
                                                   XPrptrZIP_OPT_BY_PREG => 'optional',
                                                   XPrptrZIP_OPT_BY_INDEX => 'optional',
                                                   XPrptrZIP_OPT_EXTRACT_AS_STRING => 'optional',
                                                   XPrptrZIP_OPT_EXTRACT_IN_OUTPUT => 'optional',
                                                   XPrptrZIP_OPT_REPLACE_NEWER => 'optional'
                                                   ,XPrptrZIP_OPT_STOP_ON_ERROR => 'optional'
												    ));
        if ($v_result != 1) {
          return 0;
        }

        if (isset($v_options[XPrptrZIP_OPT_PATH])) {
          $v_path = $v_options[XPrptrZIP_OPT_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_PATH])) {
          $v_remove_path = $v_options[XPrptrZIP_OPT_REMOVE_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH])) {
          $v_remove_all_path = $v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_ADD_PATH])) {
          if ((strlen($v_path) > 0) && (substr($v_path, -1) != '/')) {
            $v_path .= '/';
          }
          $v_path .= $v_options[XPrptrZIP_OPT_ADD_PATH];
        }
      }

      else {
        $v_path = $v_arg_list[0];

        if ($v_size == 2) {
          $v_remove_path = $v_arg_list[1];
        }
        else if ($v_size > 2) {
          XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect arguments number / type");
          return 0;
        }
      }
    }

    $p_list = array();
    $v_result = $this->privExtractByRule($p_list, $v_path, $v_remove_path,
	                                     $v_remove_all_path, $v_options);
    if ($v_result < 1) {
      unset($p_list);
      return(0);
    }

    return $p_list;
  }

  function extractByIndex($p_index /* $options */)
  {
    $v_result=1;
    $this->privErrorReset();

    if (!$this->privCheckFormat()) {
      return(0);
    }

    $v_options = array();
    $v_path = "./";
    $v_remove_path = "";
    $v_remove_all_path = false;

    $v_size = func_num_args();

    $v_options[XPrptrZIP_OPT_EXTRACT_AS_STRING] = FALSE;

    if ($v_size > 1) {
      $v_arg_list = &func_get_args();

      array_shift($v_arg_list);
      $v_size--;

      if ((is_integer($v_arg_list[0])) && ($v_arg_list[0] > 77000)) {

        $v_result = $this->privParseOptions($v_arg_list, $v_size, $v_options,
                                            array (XPrptrZIP_OPT_PATH => 'optional',
                                                   XPrptrZIP_OPT_REMOVE_PATH => 'optional',
                                                   XPrptrZIP_OPT_REMOVE_ALL_PATH => 'optional',
                                                   XPrptrZIP_OPT_EXTRACT_AS_STRING => 'optional',
                                                   XPrptrZIP_OPT_ADD_PATH => 'optional',
                                                   XPrptrZIP_CB_PRE_EXTRACT => 'optional',
                                                   XPrptrZIP_CB_POST_EXTRACT => 'optional',
                                                   XPrptrZIP_OPT_SET_CHMOD => 'optional',
                                                   XPrptrZIP_OPT_REPLACE_NEWER => 'optional'
                                                   ,XPrptrZIP_OPT_STOP_ON_ERROR => 'optional'
												   ));
        if ($v_result != 1) {
          return 0;
        }

        if (isset($v_options[XPrptrZIP_OPT_PATH])) {
          $v_path = $v_options[XPrptrZIP_OPT_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_PATH])) {
          $v_remove_path = $v_options[XPrptrZIP_OPT_REMOVE_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH])) {
          $v_remove_all_path = $v_options[XPrptrZIP_OPT_REMOVE_ALL_PATH];
        }
        if (isset($v_options[XPrptrZIP_OPT_ADD_PATH])) {
          if ((strlen($v_path) > 0) && (substr($v_path, -1) != '/')) {
            $v_path .= '/';
          }
          $v_path .= $v_options[XPrptrZIP_OPT_ADD_PATH];
        }
        if (!isset($v_options[XPrptrZIP_OPT_EXTRACT_AS_STRING])) {
          $v_options[XPrptrZIP_OPT_EXTRACT_AS_STRING] = FALSE;
        }
        else {
        }
      }

      else {

        $v_path = $v_arg_list[0];

        if ($v_size == 2) {
          $v_remove_path = $v_arg_list[1];
        }
        else if ($v_size > 2) {
          XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect arguments number / type");
          return 0;
        }
      }
    }


    $v_arg_trick = array (XPrptrZIP_OPT_BY_INDEX, $p_index);
    $v_options_trick = array();
    $v_result = $this->privParseOptions($v_arg_trick, sizeof($v_arg_trick), $v_options_trick,
                                        array (XPrptrZIP_OPT_BY_INDEX => 'optional' ));
    if ($v_result != 1) {
        return 0;
    }
    $v_options[XPrptrZIP_OPT_BY_INDEX] = $v_options_trick[XPrptrZIP_OPT_BY_INDEX];

    if (($v_result = $this->privExtractByRule($p_list, $v_path, $v_remove_path, $v_remove_all_path, $v_options)) < 1) {
        return(0);
    }

    return $p_list;
  }

  function delete(/* options */)
  {
    $v_result=1;

    $this->privErrorReset();

    if (!$this->privCheckFormat()) {
      return(0);
    }

    $v_options = array();

    $v_size = func_num_args();

    if ($v_size <= 0) {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect argument");

        return 0;
    }

    $v_arg_list = &func_get_args();

    $v_result = $this->privParseOptions($v_arg_list, $v_size, $v_options,
                                        array (XPrptrZIP_OPT_BY_NAME => 'optional',
                                               XPrptrZIP_OPT_BY_EREG => 'optional',
                                               XPrptrZIP_OPT_BY_PREG => 'optional',
                                               XPrptrZIP_OPT_BY_INDEX => 'optional' ));
    if ($v_result != 1) {
        return 0;
    }

    if (   (!isset($v_options[XPrptrZIP_OPT_BY_NAME]))
        && (!isset($v_options[XPrptrZIP_OPT_BY_EREG]))
        && (!isset($v_options[XPrptrZIP_OPT_BY_PREG]))
        && (!isset($v_options[XPrptrZIP_OPT_BY_INDEX]))) {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "At least one filter is required");
        return 0;
    }

    $v_list = array();
    if (($v_result = $this->privDeleteByRule($v_list, $v_options)) != 1)
    {
      unset($v_list);
      return(0);
    }

    return $v_list;
  }

  function deleteByIndex($p_index)
  {
    
    $p_list = $this->delete(XPrptrZIP_OPT_BY_INDEX, $p_index);

    return $p_list;
  }

  function properties()
  {

    $this->privErrorReset();

    if (!$this->privCheckFormat()) {
      return(0);
    }

    $v_prop = array();
    $v_prop['comment'] = '';
    $v_prop['nb'] = 0;
    $v_prop['status'] = 'not_exist';

    if (@is_file($this->zipname))
    {
      if (($this->zip_fd = @fopen($this->zipname, 'rb')) == 0)
      {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, 'Can\'t open the archive \''.$this->zipname.'\'  in binary mode');
        return 0;
      }

      $v_central_dir = array();
      if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
      {
        return 0;
      }

      $this->privCloseFd();

      $v_prop['comment'] = $v_central_dir['comment'];
      $v_prop['nb'] = $v_central_dir['entries'];
      $v_prop['status'] = 'ok';
    }

    return $v_prop;
  }

  function duplicate($p_archive)
  {
    $v_result = 1;

    $this->privErrorReset();

    if ((is_object($p_archive)) && (get_class($p_archive) == 'XprptZip'))
    {

      $v_result = $this->privDuplicate($p_archive->zipname);
    }

    else if (is_string($p_archive))
    {

      if (!is_file($p_archive)) {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_FILE, "File doesn't exists '" . $p_archive . "'");
        $v_result = XPrptrZIP_ERR_MISSING_FILE;
      }
      else {
        $v_result = $this->privDuplicate($p_archive);
      }
    }

    else
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect variable type p_archive_to_add");
      $v_result = XPrptrZIP_ERR_INVALID_PARAMETER;
    }

    return $v_result;
  }

  function merge($p_archive_to_add)
  {
    $v_result = 1;

    $this->privErrorReset();

    if (!$this->privCheckFormat()) {
      return(0);
    }

    if ((is_object($p_archive_to_add)) && (get_class($p_archive_to_add) == 'XprptZip'))
    {

      $v_result = $this->privMerge($p_archive_to_add);
    }

    else if (is_string($p_archive_to_add))
    {

      $v_object_archive = new XPraptorZIP($p_archive_to_add);

      $v_result = $this->privMerge($v_object_archive);
    }

    else
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect variable type p_filelist  p_archive_to_add");
      $v_result = XPrptrZIP_ERR_INVALID_PARAMETER;
    }

    return $v_result;
  }



  function errorCode()
  {
    if (XPrptrZIP_ERROR_EXTERNAL == 1) {
      return(XprptErrorCode());
    }
    else {
      return($this->error_code);
    }
  }

  function errorName($p_with_code=false)
  {
    $v_name = array ( XPrptrZIP_ERR_NO_ERROR => 'XPrptrZIP_ERR_NO_ERROR',
                      XPrptrZIP_ERR_WRITE_OPEN_FAIL => 'XPrptrZIP_ERR_WRITE_OPEN_FAIL',
                      XPrptrZIP_ERR_READ_OPEN_FAIL => 'XPrptrZIP_ERR_READ_OPEN_FAIL',
                      XPrptrZIP_ERR_INVALID_PARAMETER => 'XPrptrZIP_ERR_INVALID_PARAMETER',
                      XPrptrZIP_ERR_MISSING_FILE => 'XPrptrZIP_ERR_MISSING_FILE',
                      XPrptrZIP_ERR_FILENAME_TOO_LONG => 'XPrptrZIP_ERR_FILENAME_TOO_LONG',
                      XPrptrZIP_ERR_INVALID_ZIP => 'XPrptrZIP_ERR_INVALID_ZIP',
                      XPrptrZIP_ERR_BAD_EXTRACTED_FILE => 'XPrptrZIP_ERR_BAD_EXTRACTED_FILE',
                      XPrptrZIP_ERR_DIR_CREATE_FAIL => 'XPrptrZIP_ERR_DIR_CREATE_FAIL',
                      XPrptrZIP_ERR_BAD_EXTENSION => 'XPrptrZIP_ERR_BAD_EXTENSION',
                      XPrptrZIP_ERR_BAD_FORMAT => 'XPrptrZIP_ERR_BAD_FORMAT',
                      XPrptrZIP_ERR_DELETE_FILE_FAIL => 'XPrptrZIP_ERR_DELETE_FILE_FAIL',
                      XPrptrZIP_ERR_RENAME_FILE_FAIL => 'XPrptrZIP_ERR_RENAME_FILE_FAIL',
                      XPrptrZIP_ERR_BAD_CHECKSUM => 'XPrptrZIP_ERR_BAD_CHECKSUM',
                      XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP => 'XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP',
                      XPrptrZIP_ERR_MISSING_OPTION_VALUE => 'XPrptrZIP_ERR_MISSING_OPTION_VALUE',
                      XPrptrZIP_ERR_INVALID_OPTION_VALUE => 'XPrptrZIP_ERR_INVALID_OPTION_VALUE',
                      XPrptrZIP_ERR_UNSUPPORTED_COMPRESSION => 'XPrptrZIP_ERR_UNSUPPORTED_COMPRESSION',
                      XPrptrZIP_ERR_UNSUPPORTED_ENCRYPTION => 'XPrptrZIP_ERR_UNSUPPORTED_ENCRYPTION' );

    if (isset($v_name[$this->error_code])) {
      $v_value = $v_name[$this->error_code];
    }
    else {
      $v_value = 'NoName';
    }

    if ($p_with_code) {
      return($v_value.' ('.$this->error_code.')');
    }
    else {
      return($v_value);
    }
  }

  function errorInfo($p_full=false)
  {
    if (XPrptrZIP_ERROR_EXTERNAL == 1) {
      return(XprptErrorString());
    }
    else {
      if ($p_full) {
        return($this->errorName(true)." : ".$this->error_string);
      }
      else {
        return($this->error_string." [code ".$this->error_code."]");
      }
    }
  }





  function privCheckFormat($p_level=0)
  {
    $v_result = true;

	// ----- Reset the file system cache
    clearstatcache();

    $this->privErrorReset();

    if (!is_file($this->zipname)) {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_FILE, "Archive file is corrupted '".$this->zipname."'");
      return(false);
    }

    if (!is_readable($this->zipname)) {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, "Can't read the archive '".$this->zipname."'");
      return(false);
    }

    return $v_result;
  }

  function privParseOptions(&$p_options_list, $p_size, &$v_result_list, $v_requested_options=false)
  {
    $v_result=1;

    $i=0;
    while ($i<$p_size) {

      if (!isset($v_requested_options[$p_options_list[$i]])) {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect required parameter '".$p_options_list[$i]."' for this method");

        return XPraptorZIP::errorCode();
      }

      switch ($p_options_list[$i]) {
        case XPrptrZIP_OPT_PATH :
        case XPrptrZIP_OPT_REMOVE_PATH :
        case XPrptrZIP_OPT_ADD_PATH :
          if (($i+1) >= $p_size) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_OPTION_VALUE, "Incorrect option value '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }

          $v_result_list[$p_options_list[$i]] = XprptZipUtilTranslateWinPath($p_options_list[$i+1], false);
          $i++;
        break;

        case XPrptrZIP_OPT_BY_NAME :
          if (($i+1) >= $p_size) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_OPTION_VALUE, "Incorrect option value '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }

          if (is_string($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]][0] = $p_options_list[$i+1];
          }
          else if (is_array($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          }
          else {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_OPTION_VALUE, "Invalid option value '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }
          $i++;
        break;

        case XPrptrZIP_OPT_BY_EREG :
        case XPrptrZIP_OPT_BY_PREG :
          if (($i+1) >= $p_size) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_OPTION_VALUE, "Incorrect parameter option value '".XprptZipUtilOptionText($p_options_list[$i])."'");
            return XPraptorZIP::errorCode();
          }

          if (is_string($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          }
          else {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_OPTION_VALUE, "Error option value '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }
          $i++;
        break;

        case XPrptrZIP_OPT_COMMENT :
        case XPrptrZIP_OPT_ADD_COMMENT :
        case XPrptrZIP_OPT_PREPEND_COMMENT :
          if (($i+1) >= $p_size) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_OPTION_VALUE,
			                     "Incorrect parameter option value '"
								 .XprptZipUtilOptionText($p_options_list[$i])
								 ."'");

            return XPraptorZIP::errorCode();
          }

          if (is_string($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          }
          else {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_OPTION_VALUE,
			                     "Error option value '"
								 .XprptZipUtilOptionText($p_options_list[$i])
								 ."'");

            return XPraptorZIP::errorCode();
          }
          $i++;
        break;

        case XPrptrZIP_OPT_BY_INDEX :
          if (($i+1) >= $p_size) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_OPTION_VALUE, "Incorrect parameter option value '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }

          $v_work_list = array();
          if (is_string($p_options_list[$i+1])) {

              $p_options_list[$i+1] = strtr($p_options_list[$i+1], ' ', '');

              $v_work_list = explode(",", $p_options_list[$i+1]);
          }
          else if (is_integer($p_options_list[$i+1])) {
              $v_work_list[0] = $p_options_list[$i+1].'-'.$p_options_list[$i+1];
          }
          else if (is_array($p_options_list[$i+1])) {
              $v_work_list = $p_options_list[$i+1];
          }
          else {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_OPTION_VALUE, "The value should be integer, strying or array '".XprptZipUtilOptionText($p_options_list[$i])."'");
            return XPraptorZIP::errorCode();
          }
          
          $v_sort_flag=false;
          $v_sort_value=0;
          for ($j=0; $j<sizeof($v_work_list); $j++) {
              $v_item_list = explode("-", $v_work_list[$j]);
              $v_size_item_list = sizeof($v_item_list);
              
              
              if ($v_size_item_list == 1) {
                  $v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
                  $v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[0];
              }
              elseif ($v_size_item_list == 2) {
                  $v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
                  $v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[1];
              }
              else {
                  XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_OPTION_VALUE, "Not enough values for indices range '".XprptZipUtilOptionText($p_options_list[$i])."'");

                  return XPraptorZIP::errorCode();
              }


              if ($v_result_list[$p_options_list[$i]][$j]['start'] < $v_sort_value) {
                  $v_sort_flag=true;

                  XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_OPTION_VALUE, "Incorrect indices sorting '".XprptZipUtilOptionText($p_options_list[$i])."'");

                  return XPraptorZIP::errorCode();
              }
              $v_sort_value = $v_result_list[$p_options_list[$i]][$j]['start'];
          }
          
          if ($v_sort_flag) {
          }

          $i++;
        break;

        case XPrptrZIP_OPT_REMOVE_ALL_PATH :
        case XPrptrZIP_OPT_EXTRACT_AS_STRING :
        case XPrptrZIP_OPT_NO_COMPRESSION :
        case XPrptrZIP_OPT_EXTRACT_IN_OUTPUT :
        case XPrptrZIP_OPT_REPLACE_NEWER :
        case XPrptrZIP_OPT_STOP_ON_ERROR :
          $v_result_list[$p_options_list[$i]] = true;
        break;

        case XPrptrZIP_OPT_SET_CHMOD :
          if (($i+1) >= $p_size) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_OPTION_VALUE, "Incorrect parameter option value '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }

          $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          $i++;
        break;

        case XPrptrZIP_CB_PRE_EXTRACT :
        case XPrptrZIP_CB_POST_EXTRACT :
        case XPrptrZIP_CB_PRE_ADD :
        case XPrptrZIP_CB_POST_ADD :
        /* for futur use
        case XPrptrZIP_CB_PRE_DELETE :
        case XPrptrZIP_CB_POST_DELETE :
        case XPrptrZIP_CB_PRE_LIST :
        case XPrptrZIP_CB_POST_LIST :
        */
          if (($i+1) >= $p_size) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_OPTION_VALUE, "Incorrect parameter option value '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }

          $v_function_name = $p_options_list[$i+1];

          if (!function_exists($v_function_name)) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_OPTION_VALUE, "Function '".$v_function_name."()' can't process the option '".XprptZipUtilOptionText($p_options_list[$i])."'");

            return XPraptorZIP::errorCode();
          }

          $v_result_list[$p_options_list[$i]] = $v_function_name;
          $i++;
        break;

        default :
          XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER,
		                       "Unknown parameter '"
							   .$p_options_list[$i]."'");

          return XPraptorZIP::errorCode();
      }

      $i++;
    }

    if ($v_requested_options !== false) {
      for ($key=reset($v_requested_options); $key=key($v_requested_options); $key=next($v_requested_options)) {
        if ($v_requested_options[$key] == 'mandatory') {
          if (!isset($v_result_list[$key])) {
            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect required parameter ".XprptZipUtilOptionText($key)."(".$key.")");

            return XPraptorZIP::errorCode();
          }
        }
      }
    }

    return $v_result;
  }

  function privCreate($p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options)
  {
    $v_result=1;
    $v_list_detail = array();

    if (($v_result = $this->privOpenFd('wb')) != 1)
    {
      return $v_result;
    }

    $v_result = $this->privAddList($p_list, $p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options);

    $this->privCloseFd();

    return $v_result;
  }

  function privAdd($p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options)
  {
    $v_result=1;
    $v_list_detail = array();

    if ((!is_file($this->zipname)) || (filesize($this->zipname) == 0))
    {

      $v_result = $this->privCreate($p_list, $p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options);

      return $v_result;
    }

    if (($v_result=$this->privOpenFd('rb')) != 1)
    {
      return $v_result;
    }

    $v_central_dir = array();
    if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
    {
      $this->privCloseFd();
      return $v_result;
    }

    @rewind($this->zip_fd);

    $v_zip_temp_name = XPrptrZIP_TEMPORARY_DIR.uniqid('XprptZip-').'.tmp';

    if (($v_zip_temp_fd = @fopen($v_zip_temp_name, 'wb')) == 0)
    {
      $this->privCloseFd();

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, 'Can\'t open temporary file \''.$v_zip_temp_name.'\' in binary mode');

      return XPraptorZIP::errorCode();
    }

    $v_size = $v_central_dir['offset'];
    while ($v_size != 0)
    {
      $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
      $v_buffer = fread($this->zip_fd, $v_read_size);
      @fwrite($v_zip_temp_fd, $v_buffer, $v_read_size);
      $v_size -= $v_read_size;
    }

    $v_swap = $this->zip_fd;
    $this->zip_fd = $v_zip_temp_fd;
    $v_zip_temp_fd = $v_swap;

    $v_header_list = array();
    if (($v_result = $this->privAddFileList($p_list, $v_header_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options)) != 1)
    {
      fclose($v_zip_temp_fd);
      $this->privCloseFd();
      @unlink($v_zip_temp_name);

      return $v_result;
    }

    $v_offset = @ftell($this->zip_fd);

    $v_size = $v_central_dir['size'];
    while ($v_size != 0)
    {
      $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
      $v_buffer = @fread($v_zip_temp_fd, $v_read_size);
      @fwrite($this->zip_fd, $v_buffer, $v_read_size);
      $v_size -= $v_read_size;
    }

    for ($i=0, $v_count=0; $i<sizeof($v_header_list); $i++)
    {
      if ($v_header_list[$i]['status'] == 'ok') {
        if (($v_result = $this->privWriteCentralFileHeader($v_header_list[$i])) != 1) {
          fclose($v_zip_temp_fd);
          $this->privCloseFd();
          @unlink($v_zip_temp_name);

          return $v_result;
        }
        $v_count++;
      }

      $this->privConvertHeader2FileInfo($v_header_list[$i], $p_result_list[$i]);
    }

    $v_comment = $v_central_dir['comment'];
    if (isset($p_options[XPrptrZIP_OPT_COMMENT])) {
      $v_comment = $p_options[XPrptrZIP_OPT_COMMENT];
    }
    if (isset($p_options[XPrptrZIP_OPT_ADD_COMMENT])) {
      $v_comment = $v_comment.$p_options[XPrptrZIP_OPT_ADD_COMMENT];
    }
    if (isset($p_options[XPrptrZIP_OPT_PREPEND_COMMENT])) {
      $v_comment = $p_options[XPrptrZIP_OPT_PREPEND_COMMENT].$v_comment;
    }

    $v_size = @ftell($this->zip_fd)-$v_offset;

    if (($v_result = $this->privWriteCentralHeader($v_count+$v_central_dir['entries'], $v_size, $v_offset, $v_comment)) != 1)
    {
      unset($v_header_list);

      return $v_result;
    }

    $v_swap = $this->zip_fd;
    $this->zip_fd = $v_zip_temp_fd;
    $v_zip_temp_fd = $v_swap;

    $this->privCloseFd();

    @fclose($v_zip_temp_fd);

    @unlink($this->zipname);

    XprptZipUtilRename($v_zip_temp_name, $this->zipname);

    return $v_result;
  }

  function privOpenFd($p_mode)
  {
    $v_result=1;

    if ($this->zip_fd != 0)
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, 'Zip file \''.$this->zipname.'\' is already opened');

      return XPraptorZIP::errorCode();
    }

    if (($this->zip_fd = @fopen($this->zipname, $p_mode)) == 0)
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, 'Can\'t open archive file \''.$this->zipname.'\' in '.$p_mode.' mode');

      return XPraptorZIP::errorCode();
    }

    return $v_result;
  }

  function privCloseFd()
  {
    $v_result=1;

    if ($this->zip_fd != 0)
      @fclose($this->zip_fd);
    $this->zip_fd = 0;

    return $v_result;
  }

  function privAddList($p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options)
  {
    $v_result=1;

    $v_header_list = array();
    if (($v_result = $this->privAddFileList($p_list, $v_header_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options)) != 1)
    {
      return $v_result;
    }

    $v_offset = @ftell($this->zip_fd);

    for ($i=0,$v_count=0; $i<sizeof($v_header_list); $i++)
    {
      if ($v_header_list[$i]['status'] == 'ok') {
        if (($v_result = $this->privWriteCentralFileHeader($v_header_list[$i])) != 1) {
          return $v_result;
        }
        $v_count++;
      }

      $this->privConvertHeader2FileInfo($v_header_list[$i], $p_result_list[$i]);
    }

    $v_comment = '';
    if (isset($p_options[XPrptrZIP_OPT_COMMENT])) {
      $v_comment = $p_options[XPrptrZIP_OPT_COMMENT];
    }

    $v_size = @ftell($this->zip_fd)-$v_offset;

    if (($v_result = $this->privWriteCentralHeader($v_count, $v_size, $v_offset, $v_comment)) != 1)
    {
      unset($v_header_list);

      return $v_result;
    }

    return $v_result;
  }

  function privAddFileList($p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options)
  {
    $v_result=1;
    $v_header = array();

    $v_nb = sizeof($p_result_list);

    for ($j=0; ($j<count($p_list)) && ($v_result==1); $j++)
    {
      $p_filename = XprptZipUtilTranslateWinPath($p_list[$j], false);


      if ($p_filename == "")
      {
        continue;
      }

      if (!file_exists($p_filename))
      {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_MISSING_FILE, "File '$p_filename' doesn\'t exist");

        return XPraptorZIP::errorCode();
      }

      if ((is_file($p_filename)) || ((is_dir($p_filename)) && !$p_remove_all_dir)) {
        if (($v_result = $this->privAddFile($p_filename, $v_header, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options)) != 1)
        {
          return $v_result;
        }

        $p_result_list[$v_nb++] = $v_header;
      }

      if (@is_dir($p_filename))
      {

        if ($p_filename != ".")
          $v_path = $p_filename."/";
        else
          $v_path = "";

        if ($p_hdir = @opendir($p_filename)) {
          $p_hitem = @readdir($p_hdir); // '.' directory
          $p_hitem = @readdir($p_hdir); // '..' directory
          while (($p_hitem = @readdir($p_hdir)) !== false) {

            if (is_file($v_path.$p_hitem)) {

              if (($v_result = $this->privAddFile($v_path.$p_hitem, $v_header, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options)) != 1) {
                return $v_result;
              }

              $p_result_list[$v_nb++] = $v_header;
            }

            else {

              $p_temp_list[0] = $v_path.$p_hitem;
              $v_result = $this->privAddFileList($p_temp_list, $p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options);

              $v_nb = sizeof($p_result_list);
            }
          }
        }

        unset($p_temp_list);
        unset($p_hdir);
        unset($p_hitem);
      }
    }


    return $v_result;
  }

  function privAddFile($p_filename, &$p_header, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options)
  {
    $v_result=1;

    if ($p_filename == "")
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_PARAMETER, "Incorrect parameters list (might be empty)");

      return XPraptorZIP::errorCode();
    }

    $v_stored_filename = $p_filename;

    if ($p_remove_all_dir) {
      $v_stored_filename = basename($p_filename);
    }
    else if ($p_remove_dir != "")
    {
      if (substr($p_remove_dir, -1) != '/')
        $p_remove_dir .= "/";

      if ((substr($p_filename, 0, 2) == "./") || (substr($p_remove_dir, 0, 2) == "./"))
      {
        if ((substr($p_filename, 0, 2) == "./") && (substr($p_remove_dir, 0, 2) != "./"))
          $p_remove_dir = "./".$p_remove_dir;
        if ((substr($p_filename, 0, 2) != "./") && (substr($p_remove_dir, 0, 2) == "./"))
          $p_remove_dir = substr($p_remove_dir, 2);
      }

      $v_compare = XprptZipUtilPathInclusion($p_remove_dir, $p_filename);
      if ($v_compare > 0)
      {

        if ($v_compare == 2) {
          $v_stored_filename = "";
        }
        else {
          $v_stored_filename = substr($p_filename, strlen($p_remove_dir));
        }
      }
    }
    if ($p_add_dir != "")
    {
      if (substr($p_add_dir, -1) == "/")
        $v_stored_filename = $p_add_dir.$v_stored_filename;
      else
        $v_stored_filename = $p_add_dir."/".$v_stored_filename;
    }

    $v_stored_filename = XprptZipUtilPathReduction($v_stored_filename);


    clearstatcache();
    $p_header['version'] = 20;
    $p_header['version_extracted'] = 10;
    $p_header['flag'] = 0;
    $p_header['compression'] = 0;
    $p_header['mtime'] = filemtime($p_filename);
    $p_header['crc'] = 0;
    $p_header['compressed_size'] = 0;
    $p_header['size'] = filesize($p_filename);
    $p_header['filename_len'] = strlen($p_filename);
    $p_header['extra_len'] = 0;
    $p_header['comment_len'] = 0;
    $p_header['disk'] = 0;
    $p_header['internal'] = 0;
    $p_header['external'] = (is_file($p_filename)?0x00000000:0x00000010);
    $p_header['offset'] = 0;
    $p_header['filename'] = $p_filename;
    $p_header['stored_filename'] = $v_stored_filename;
    $p_header['extra'] = '';
    $p_header['comment'] = '';
    $p_header['status'] = 'ok';
    $p_header['index'] = -1;


    if (isset($p_options[XPrptrZIP_CB_PRE_ADD])) {

      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_header, $v_local_header);

      eval('$v_result = '.$p_options[XPrptrZIP_CB_PRE_ADD].'(XPrptrZIP_CB_PRE_ADD, $v_local_header);');
      if ($v_result == 0) {
        $p_header['status'] = "skipped";
        $v_result = 1;
      }

      if ($p_header['stored_filename'] != $v_local_header['stored_filename']) {
        $p_header['stored_filename'] = XprptZipUtilPathReduction($v_local_header['stored_filename']);
      }
    }

    if ($p_header['stored_filename'] == "") {
      $p_header['status'] = "filtered";
    }
    
    if (strlen($p_header['stored_filename']) > 0xFF) {
      $p_header['status'] = 'filename_too_long';
    }

    if ($p_header['status'] == 'ok') {

      if (is_file($p_filename))
      {
        if (($v_file = @fopen($p_filename, "rb")) == 0) {
          XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, "Can't open temporary file '$p_filename' in binary mod");
          return XPraptorZIP::errorCode();
        }

        if ($p_options[XPrptrZIP_OPT_NO_COMPRESSION]) {
          $v_content_compressed = @fread($v_file, $p_header['size']);

          $p_header['crc'] = @crc32($v_content_compressed);

          $p_header['compressed_size'] = $p_header['size'];
          $p_header['compression'] = 0;
        }
        else {
          $v_content = @fread($v_file, $p_header['size']);

          $p_header['crc'] = @crc32($v_content);

          $v_content_compressed = @gzdeflate($v_content);

          $p_header['compressed_size'] = strlen($v_content_compressed);
          $p_header['compression'] = 8;
        }
        
        if (($v_result = $this->privWriteFileHeader($p_header)) != 1) {
          @fclose($v_file);
          return $v_result;
        }

        $v_binary_data = pack('a'.$p_header['compressed_size'],
		                      $v_content_compressed);
        @fwrite($this->zip_fd, $v_binary_data, $p_header['compressed_size']);
        
        @fclose($v_file);
      }

      else {
        if (@substr($p_header['stored_filename'], -1) != '/') {
          $p_header['stored_filename'] .= '/';
        }

        $p_header['size'] = 0;
        $p_header['external'] = 0x00000010;   // Value for a folder : to be checked

        if (($v_result = $this->privWriteFileHeader($p_header)) != 1)
        {
          return $v_result;
        }
      }
    }

    if (isset($p_options[XPrptrZIP_CB_POST_ADD])) {

      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_header, $v_local_header);

      eval('$v_result = '.$p_options[XPrptrZIP_CB_POST_ADD].'(XPrptrZIP_CB_POST_ADD, $v_local_header);');
      if ($v_result == 0) {
        $v_result = 1;
      }

    }

    return $v_result;
  }

  function privWriteFileHeader(&$p_header)
  {
    $v_result=1;


    $p_header['offset'] = ftell($this->zip_fd);

    $v_date = getdate($p_header['mtime']);
    $v_mtime = ($v_date['hours']<<11) + ($v_date['minutes']<<5) + $v_date['seconds']/2;
    $v_mdate = (($v_date['year']-1980)<<9) + ($v_date['mon']<<5) + $v_date['mday'];

    $v_binary_data = pack("VvvvvvVVVvv", 0x04034b50,
	                      $p_header['version_extracted'], $p_header['flag'],
                          $p_header['compression'], $v_mtime, $v_mdate,
                          $p_header['crc'], $p_header['compressed_size'],
						  $p_header['size'],
                          strlen($p_header['stored_filename']),
						  $p_header['extra_len']);

    fputs($this->zip_fd, $v_binary_data, 30);

    if (strlen($p_header['stored_filename']) != 0)
    {
      fputs($this->zip_fd, $p_header['stored_filename'], strlen($p_header['stored_filename']));
    }
    if ($p_header['extra_len'] != 0)
    {
      fputs($this->zip_fd, $p_header['extra'], $p_header['extra_len']);
    }

    return $v_result;
  }

  function privWriteCentralFileHeader(&$p_header)
  {
    $v_result=1;


    $v_date = getdate($p_header['mtime']);
    $v_mtime = ($v_date['hours']<<11) + ($v_date['minutes']<<5) + $v_date['seconds']/2;
    $v_mdate = (($v_date['year']-1980)<<9) + ($v_date['mon']<<5) + $v_date['mday'];

    $v_binary_data = pack("VvvvvvvVVVvvvvvVV", 0x02014b50,
	                      $p_header['version'], $p_header['version_extracted'],
                          $p_header['flag'], $p_header['compression'],
						  $v_mtime, $v_mdate, $p_header['crc'],
                          $p_header['compressed_size'], $p_header['size'],
                          strlen($p_header['stored_filename']),
						  $p_header['extra_len'], $p_header['comment_len'],
                          $p_header['disk'], $p_header['internal'],
						  $p_header['external'], $p_header['offset']);

    fputs($this->zip_fd, $v_binary_data, 46);

    if (strlen($p_header['stored_filename']) != 0)
    {
      fputs($this->zip_fd, $p_header['stored_filename'], strlen($p_header['stored_filename']));
    }
    if ($p_header['extra_len'] != 0)
    {
      fputs($this->zip_fd, $p_header['extra'], $p_header['extra_len']);
    }
    if ($p_header['comment_len'] != 0)
    {
      fputs($this->zip_fd, $p_header['comment'], $p_header['comment_len']);
    }

    return $v_result;
  }

  function privWriteCentralHeader($p_nb_entries, $p_size, $p_offset, $p_comment)
  {
    $v_result=1;

    $v_binary_data = pack("VvvvvVVv", 0x06054b50, 0, 0, $p_nb_entries,
	                      $p_nb_entries, $p_size,
						  $p_offset, strlen($p_comment));

    fputs($this->zip_fd, $v_binary_data, 22);

    if (strlen($p_comment) != 0)
    {
      fputs($this->zip_fd, $p_comment, strlen($p_comment));
    }

    return $v_result;
  }

  function privList(&$p_list)
  {
    $v_result=1;

    if (($this->zip_fd = @fopen($this->zipname, 'rb')) == 0)
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, 'Can\'t open archive file \''.$this->zipname.'\' in binary mode');

      return XPraptorZIP::errorCode();
    }

    $v_central_dir = array();
    if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
    {
      return $v_result;
    }

    @rewind($this->zip_fd);
    if (@fseek($this->zip_fd, $v_central_dir['offset']))
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP, 'Incorrect archive size');

      return XPraptorZIP::errorCode();
    }

    for ($i=0; $i<$v_central_dir['entries']; $i++)
    {
      if (($v_result = $this->privReadCentralFileHeader($v_header)) != 1)
      {
        return $v_result;
      }
      $v_header['index'] = $i;

      $this->privConvertHeader2FileInfo($v_header, $p_list[$i]);
      unset($v_header);
    }

    $this->privCloseFd();

    return $v_result;
  }

  function privConvertHeader2FileInfo($p_header, &$p_info)
  {
    $v_result=1;

    $p_info['filename'] = $p_header['filename'];
    $p_info['stored_filename'] = $p_header['stored_filename'];
    $p_info['size'] = $p_header['size'];
    $p_info['compressed_size'] = $p_header['compressed_size'];
    $p_info['mtime'] = $p_header['mtime'];
    $p_info['comment'] = $p_header['comment'];
    $p_info['folder'] = (($p_header['external']&0x00000010)==0x00000010);
    $p_info['index'] = $p_header['index'];
    $p_info['status'] = $p_header['status'];

    return $v_result;
  }

  function privExtractByRule(&$p_file_list, $p_path, $p_remove_path, $p_remove_all_path, &$p_options)
  {
    $v_result=1;

    if (($p_path == "") || ((substr($p_path, 0, 1) != "/") && (substr($p_path, 0, 3) != "../") && (substr($p_path,1,2)!=":/")))
      $p_path = "./".$p_path;

    if (($p_path != "./") && ($p_path != "/"))
    {
      while (substr($p_path, -1) == "/")
      {
        $p_path = substr($p_path, 0, strlen($p_path)-1);
      }
    }

    if (($p_remove_path != "") && (substr($p_remove_path, -1) != '/'))
    {
      $p_remove_path .= '/';
    }
    $p_remove_path_size = strlen($p_remove_path);

    if (($v_result = $this->privOpenFd('rb')) != 1)
    {
      return $v_result;
    }

    $v_central_dir = array();
    if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
    {
      $this->privCloseFd();

      return $v_result;
    }

    $v_pos_entry = $v_central_dir['offset'];

    $j_start = 0;
    for ($i=0, $v_nb_extracted=0; $i<$v_central_dir['entries']; $i++)
    {

      @rewind($this->zip_fd);
      if (@fseek($this->zip_fd, $v_pos_entry))
      {
        $this->privCloseFd();

        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP, 'Incorrect archive size');

        return XPraptorZIP::errorCode();
      }

      $v_header = array();
      if (($v_result = $this->privReadCentralFileHeader($v_header)) != 1)
      {
        $this->privCloseFd();

        return $v_result;
      }

      $v_header['index'] = $i;

      $v_pos_entry = ftell($this->zip_fd);

      $v_extract = false;

      if (   (isset($p_options[XPrptrZIP_OPT_BY_NAME]))
          && ($p_options[XPrptrZIP_OPT_BY_NAME] != 0)) {

          for ($j=0; ($j<sizeof($p_options[XPrptrZIP_OPT_BY_NAME])) && (!$v_extract); $j++) {

              if (substr($p_options[XPrptrZIP_OPT_BY_NAME][$j], -1) == "/") {

                  if (   (strlen($v_header['stored_filename']) > strlen($p_options[XPrptrZIP_OPT_BY_NAME][$j]))
                      && (substr($v_header['stored_filename'], 0, strlen($p_options[XPrptrZIP_OPT_BY_NAME][$j])) == $p_options[XPrptrZIP_OPT_BY_NAME][$j])) {
                      $v_extract = true;
                  }
              }
              elseif ($v_header['stored_filename'] == $p_options[XPrptrZIP_OPT_BY_NAME][$j]) {
                  $v_extract = true;
              }
          }
      }

      else if (   (isset($p_options[XPrptrZIP_OPT_BY_EREG]))
               && ($p_options[XPrptrZIP_OPT_BY_EREG] != "")) {

          if (ereg($p_options[XPrptrZIP_OPT_BY_EREG], $v_header['stored_filename'])) {
              $v_extract = true;
          }
      }

      else if (   (isset($p_options[XPrptrZIP_OPT_BY_PREG]))
               && ($p_options[XPrptrZIP_OPT_BY_PREG] != "")) {

          if (preg_match($p_options[XPrptrZIP_OPT_BY_PREG], $v_header['stored_filename'])) {
              $v_extract = true;
          }
      }

      else if (   (isset($p_options[XPrptrZIP_OPT_BY_INDEX]))
               && ($p_options[XPrptrZIP_OPT_BY_INDEX] != 0)) {
          
          for ($j=$j_start; ($j<sizeof($p_options[XPrptrZIP_OPT_BY_INDEX])) && (!$v_extract); $j++) {

              if (($i>=$p_options[XPrptrZIP_OPT_BY_INDEX][$j]['start']) && ($i<=$p_options[XPrptrZIP_OPT_BY_INDEX][$j]['end'])) {
                  $v_extract = true;
              }
              if ($i>=$p_options[XPrptrZIP_OPT_BY_INDEX][$j]['end']) {
                  $j_start = $j+1;
              }

              if ($p_options[XPrptrZIP_OPT_BY_INDEX][$j]['start']>$i) {
                  break;
              }
          }
      }

      else {
          $v_extract = true;
      }

	  // ----- Check compression method
	  if (   ($v_extract)
	      && (   ($v_header['compression'] != 8)
		      && ($v_header['compression'] != 0))) {
          $v_header['status'] = 'unsupported_compression';

          if (   (isset($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]))
		      && ($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]===true)) {

              XPraptorZIP::privErrorLog(XPrptrZIP_ERR_UNSUPPORTED_COMPRESSION,
			                       "Filename '".$v_header['stored_filename']."' is "
				  	    	  	   ."compressed by an unsupported compression "
				  	    	  	   ."method (".$v_header['compression'].") ");

              return XPraptorZIP::errorCode();
		  }
	  }
	  
	  // ----- Check encrypted files
	  if (($v_extract) && (($v_header['flag'] & 1) == 1)) {
          $v_header['status'] = 'unsupported_encryption';

          if (   (isset($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]))
		      && ($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]===true)) {

              XPraptorZIP::privErrorLog(XPrptrZIP_ERR_UNSUPPORTED_ENCRYPTION,
			                       "Unsupported encryption for "
				  	    	  	   ." filename '".$v_header['stored_filename']
								   ."'");

              return XPraptorZIP::errorCode();
		  }
	  }

      if (($v_extract) && ($v_header['status'] != 'ok')) {
          $v_result = $this->privConvertHeader2FileInfo($v_header,
		                                        $p_file_list[$v_nb_extracted++]);
          if ($v_result != 1) {
              $this->privCloseFd();
              return $v_result;
          }

          $v_extract = false;
      }
      
      if ($v_extract)
      {

        @rewind($this->zip_fd);
        if (@fseek($this->zip_fd, $v_header['offset']))
        {
          $this->privCloseFd();

          XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP, 'Incorrect archive size');

          return XPraptorZIP::errorCode();
        }

        if ($p_options[XPrptrZIP_OPT_EXTRACT_AS_STRING]) {

          $v_result1 = $this->privExtractFileAsString($v_header, $v_string);
          if ($v_result1 < 1) {
            $this->privCloseFd();
            return $v_result1;
          }

          if (($v_result = $this->privConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted])) != 1)
          {
            $this->privCloseFd();

            return $v_result;
          }

          $p_file_list[$v_nb_extracted]['content'] = $v_string;

          $v_nb_extracted++;
          
          if ($v_result1 == 2) {
          	break;
          }
        }
        elseif (   (isset($p_options[XPrptrZIP_OPT_EXTRACT_IN_OUTPUT]))
		        && ($p_options[XPrptrZIP_OPT_EXTRACT_IN_OUTPUT])) {
          $v_result1 = $this->privExtractFileInOutput($v_header, $p_options);
          if ($v_result1 < 1) {
            $this->privCloseFd();
            return $v_result1;
          }

          if (($v_result = $this->privConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted++])) != 1) {
            $this->privCloseFd();
            return $v_result;
          }

          if ($v_result1 == 2) {
          	break;
          }
        }
        else {
          $v_result1 = $this->privExtractFile($v_header,
		                                      $p_path, $p_remove_path,
											  $p_remove_all_path,
											  $p_options);
          if ($v_result1 < 1) {
            $this->privCloseFd();
            return $v_result1;
          }

          if (($v_result = $this->privConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted++])) != 1)
          {
            $this->privCloseFd();

            return $v_result;
          }

          if ($v_result1 == 2) {
          	break;
          }
        }
      }
    }

    $this->privCloseFd();

    return $v_result;
  }

  function privExtractFile(&$p_entry, $p_path, $p_remove_path, $p_remove_all_path, &$p_options)
  {
    $v_result=1;

    if (($v_result = $this->privReadFileHeader($v_header)) != 1)
    {
      return $v_result;
    }


    if ($this->privCheckFileHeaders($v_header, $p_entry) != 1) {
    }

    if ($p_remove_all_path == true) {
        $p_entry['filename'] = basename($p_entry['filename']);
    }

    else if ($p_remove_path != "")
    {
      if (XprptZipUtilPathInclusion($p_remove_path, $p_entry['filename']) == 2)
      {

        $p_entry['status'] = "filtered";

        return $v_result;
      }

      $p_remove_path_size = strlen($p_remove_path);
      if (substr($p_entry['filename'], 0, $p_remove_path_size) == $p_remove_path)
      {

        $p_entry['filename'] = substr($p_entry['filename'], $p_remove_path_size);

      }
    }

    if ($p_path != '')
    {
      $p_entry['filename'] = $p_path."/".$p_entry['filename'];
    }

    if (isset($p_options[XPrptrZIP_CB_PRE_EXTRACT])) {

      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      eval('$v_result = '.$p_options[XPrptrZIP_CB_PRE_EXTRACT].'(XPrptrZIP_CB_PRE_EXTRACT, $v_local_header);');
      if ($v_result == 0) {
        $p_entry['status'] = "skipped";
        $v_result = 1;
      }
      
      if ($v_result == 2) {
        $p_entry['status'] = "aborted";
      	$v_result = XPrptrZIP_ERR_USER_ABORTED;
      }

      $p_entry['filename'] = $v_local_header['filename'];
    }


    if ($p_entry['status'] == 'ok') {

    if (file_exists($p_entry['filename']))
    {

      if (is_dir($p_entry['filename']))
      {

        $p_entry['status'] = "already_a_directory";
        
        if (   (isset($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]))
		    && ($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]===true)) {

            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_ALREADY_A_DIRECTORY,
			                     "File '".$p_entry['filename']."' is already used in existing folder");

            return XPraptorZIP::errorCode();
		}
      }
      else if (!is_writeable($p_entry['filename']))
      {

        $p_entry['status'] = "write_protected";

        if (   (isset($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]))
		    && ($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]===true)) {

            XPraptorZIP::privErrorLog(XPrptrZIP_ERR_WRITE_OPEN_FAIL,
			                     "File  '".$p_entry['filename']."' exist and is read-only");

            return XPraptorZIP::errorCode();
		}
      }

      else if (filemtime($p_entry['filename']) > $p_entry['mtime'])
      {
        if (   (isset($p_options[XPrptrZIP_OPT_REPLACE_NEWER]))
		    && ($p_options[XPrptrZIP_OPT_REPLACE_NEWER]===true)) {
		}
		else {
            $p_entry['status'] = "newer_exist";

            if (   (isset($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]))
		        && ($p_options[XPrptrZIP_OPT_STOP_ON_ERROR]===true)) {

                XPraptorZIP::privErrorLog(XPrptrZIP_ERR_WRITE_OPEN_FAIL,
			             "Newer version of '".$p_entry['filename']."' exists "
					    ."and option XPrptrZIP_OPT_REPLACE_NEWER is not selected");

                return XPraptorZIP::errorCode();
		    }
		}
      }
      else {
      }
    }

    else {
      if ((($p_entry['external']&0x00000010)==0x00000010) || (substr($p_entry['filename'], -1) == '/'))
        $v_dir_to_check = $p_entry['filename'];
      else if (!strstr($p_entry['filename'], "/"))
        $v_dir_to_check = "";
      else
        $v_dir_to_check = dirname($p_entry['filename']);

      if (($v_result = $this->privDirCheck($v_dir_to_check, (($p_entry['external']&0x00000010)==0x00000010))) != 1) {

        $p_entry['status'] = "path_creation_fail";

        $v_result = 1;
      }
    }
    }

    if ($p_entry['status'] == 'ok') {

      if (!(($p_entry['external']&0x00000010)==0x00000010))
      {
        if ($p_entry['compression'] == 0) {

		  // ----- Opening destination file
          if (($v_dest_file = @fopen($p_entry['filename'], 'wb')) == 0)
          {

            $p_entry['status'] = "write_error";

            return $v_result;
          }


          $v_size = $p_entry['compressed_size'];
          while ($v_size != 0)
          {
            $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
            $v_buffer = fread($this->zip_fd, $v_read_size);
            $v_binary_data = pack('a'.$v_read_size, $v_buffer);
            @fwrite($v_dest_file, $v_binary_data, $v_read_size);
            $v_size -= $v_read_size;
          }

          fclose($v_dest_file);

          touch($p_entry['filename'], $p_entry['mtime']);
          

        }
        else {
          if (($p_entry['flag'] & 1) == 1) {
          }
          else {
              $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);
          }
          
          $v_file_content = @gzinflate($v_buffer);
          unset($v_buffer);
          if ($v_file_content === FALSE) {

            $p_entry['status'] = "error";
            
            return $v_result;
          }
          
          if (($v_dest_file = @fopen($p_entry['filename'], 'wb')) == 0) {

            $p_entry['status'] = "write_error";

            return $v_result;
          }

          @fwrite($v_dest_file, $v_file_content, $p_entry['size']);
          unset($v_file_content);

          @fclose($v_dest_file);

          @touch($p_entry['filename'], $p_entry['mtime']);
        }

        if (isset($p_options[XPrptrZIP_OPT_SET_CHMOD])) {

          @chmod($p_entry['filename'], $p_options[XPrptrZIP_OPT_SET_CHMOD]);
        }

      }
    }

	// ----- Change abort status
	if ($p_entry['status'] == "aborted") {
      $p_entry['status'] = "skipped";
	}
	
    elseif (isset($p_options[XPrptrZIP_CB_POST_EXTRACT])) {

      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      eval('$v_result = '.$p_options[XPrptrZIP_CB_POST_EXTRACT].'(XPrptrZIP_CB_POST_EXTRACT, $v_local_header);');

      if ($v_result == 2) {
      	$v_result = XPrptrZIP_ERR_USER_ABORTED;
      }
    }

    return $v_result;
  }

  function privExtractFileInOutput(&$p_entry, &$p_options)
  {
    $v_result=1;

    if (($v_result = $this->privReadFileHeader($v_header)) != 1) {
      return $v_result;
    }


    if ($this->privCheckFileHeaders($v_header, $p_entry) != 1) {
    }

    if (isset($p_options[XPrptrZIP_CB_PRE_EXTRACT])) {

      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      eval('$v_result = '.$p_options[XPrptrZIP_CB_PRE_EXTRACT].'(XPrptrZIP_CB_PRE_EXTRACT, $v_local_header);');
      if ($v_result == 0) {
        $p_entry['status'] = "skipped";
        $v_result = 1;
      }

      if ($v_result == 2) {
        $p_entry['status'] = "aborted";
      	$v_result = XPrptrZIP_ERR_USER_ABORTED;
      }

      $p_entry['filename'] = $v_local_header['filename'];
    }


    if ($p_entry['status'] == 'ok') {

      if (!(($p_entry['external']&0x00000010)==0x00000010)) {
        if ($p_entry['compressed_size'] == $p_entry['size']) {

          $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);

          echo $v_buffer;
          unset($v_buffer);
        }
        else {

          $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);
          
          $v_file_content = gzinflate($v_buffer);
          unset($v_buffer);

          echo $v_file_content;
          unset($v_file_content);
        }
      }
    }

	if ($p_entry['status'] == "aborted") {
      $p_entry['status'] = "skipped";
	}

    elseif (isset($p_options[XPrptrZIP_CB_POST_EXTRACT])) {

      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      eval('$v_result = '.$p_options[XPrptrZIP_CB_POST_EXTRACT].'(XPrptrZIP_CB_POST_EXTRACT, $v_local_header);');

      if ($v_result == 2) {
      	$v_result = XPrptrZIP_ERR_USER_ABORTED;
      }
    }

    return $v_result;
  }

  function privExtractFileAsString(&$p_entry, &$p_string)
  {
    $v_result=1;

    $v_header = array();
    if (($v_result = $this->privReadFileHeader($v_header)) != 1)
    {
      return $v_result;
    }


    if ($this->privCheckFileHeaders($v_header, $p_entry) != 1) {
    }


    if (!(($p_entry['external']&0x00000010)==0x00000010))
    {
      if ($p_entry['compressed_size'] == $p_entry['size'])
      {

        $p_string = @fread($this->zip_fd, $p_entry['compressed_size']);
      }
      else
      {

        $v_data = @fread($this->zip_fd, $p_entry['compressed_size']);
        
        if (($p_string = @gzinflate($v_data)) === FALSE) {
        }
      }

    }
    else {
    }

    return $v_result;
  }

  function privReadFileHeader(&$p_header)
  {
    $v_result=1;

    $v_binary_data = @fread($this->zip_fd, 4);
    $v_data = unpack('Vid', $v_binary_data);

    if ($v_data['id'] != 0x04034b50)
    {

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, 'Invalid archive structure');

      return XPraptorZIP::errorCode();
    }

    $v_binary_data = fread($this->zip_fd, 26);

    if (strlen($v_binary_data) != 26)
    {
      $p_header['filename'] = "";
      $p_header['status'] = "invalid_header";

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, "Invalid block size : ".strlen($v_binary_data));

      return XPraptorZIP::errorCode();
    }

    $v_data = unpack('vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $v_binary_data);

    $p_header['filename'] = fread($this->zip_fd, $v_data['filename_len']);

    if ($v_data['extra_len'] != 0) {
      $p_header['extra'] = fread($this->zip_fd, $v_data['extra_len']);
    }
    else {
      $p_header['extra'] = '';
    }

    $p_header['version_extracted'] = $v_data['version'];
    $p_header['compression'] = $v_data['compression'];
    $p_header['size'] = $v_data['size'];
    $p_header['compressed_size'] = $v_data['compressed_size'];
    $p_header['crc'] = $v_data['crc'];
    $p_header['flag'] = $v_data['flag'];

    $p_header['mdate'] = $v_data['mdate'];
    $p_header['mtime'] = $v_data['mtime'];
    if ($p_header['mdate'] && $p_header['mtime'])
    {
      $v_hour = ($p_header['mtime'] & 0xF800) >> 11;
      $v_minute = ($p_header['mtime'] & 0x07E0) >> 5;
      $v_seconde = ($p_header['mtime'] & 0x001F)*2;

      $v_year = (($p_header['mdate'] & 0xFE00) >> 9) + 1980;
      $v_month = ($p_header['mdate'] & 0x01E0) >> 5;
      $v_day = $p_header['mdate'] & 0x001F;

      $p_header['mtime'] = mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);

    }
    else
    {
      $p_header['mtime'] = time();
    }


    $p_header['stored_filename'] = $p_header['filename'];

    $p_header['status'] = "ok";

    return $v_result;
  }

  function privReadCentralFileHeader(&$p_header)
  {
    $v_result=1;

    $v_binary_data = @fread($this->zip_fd, 4);
    $v_data = unpack('Vid', $v_binary_data);

    if ($v_data['id'] != 0x02014b50)
    {

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, 'Invalid archive structure');

      return XPraptorZIP::errorCode();
    }

    $v_binary_data = fread($this->zip_fd, 42);

    if (strlen($v_binary_data) != 42)
    {
      $p_header['filename'] = "";
      $p_header['status'] = "invalid_header";

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, "Invalid block size : ".strlen($v_binary_data));

      return XPraptorZIP::errorCode();
    }

    $p_header = unpack('vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $v_binary_data);

    if ($p_header['filename_len'] != 0)
      $p_header['filename'] = fread($this->zip_fd, $p_header['filename_len']);
    else
      $p_header['filename'] = '';

    if ($p_header['extra_len'] != 0)
      $p_header['extra'] = fread($this->zip_fd, $p_header['extra_len']);
    else
      $p_header['extra'] = '';

    if ($p_header['comment_len'] != 0)
      $p_header['comment'] = fread($this->zip_fd, $p_header['comment_len']);
    else
      $p_header['comment'] = '';


    if ($p_header['mdate'] && $p_header['mtime'])
    {
      $v_hour = ($p_header['mtime'] & 0xF800) >> 11;
      $v_minute = ($p_header['mtime'] & 0x07E0) >> 5;
      $v_seconde = ($p_header['mtime'] & 0x001F)*2;

      $v_year = (($p_header['mdate'] & 0xFE00) >> 9) + 1980;
      $v_month = ($p_header['mdate'] & 0x01E0) >> 5;
      $v_day = $p_header['mdate'] & 0x001F;

      $p_header['mtime'] = mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);

    }
    else
    {
      $p_header['mtime'] = time();
    }

    $p_header['stored_filename'] = $p_header['filename'];

    $p_header['status'] = 'ok';

    if (substr($p_header['filename'], -1) == '/') {
      $p_header['external'] = 0x00000010;
    }


    return $v_result;
  }

  function privCheckFileHeaders(&$p_local_header, &$p_central_header)
  {
    $v_result = 1;
/*
	if ($p_local_header['filename'] != $p_central_header['filename']) {
	}
	if ($p_local_header['version_extracted'] != $p_central_header['version_extracted']) {
	}
	if ($p_local_header['flag'] != $p_central_header['flag']) {
	}
	if ($p_local_header['compression'] != $p_central_header['compression']) {
	}
	if ($p_local_header['mtime'] != $p_central_header['mtime']) {
	}
	if ($p_local_header['filename_len'] != $p_central_header['filename_len']) {
	}
*/
	if (($p_local_header['flag'] & 8) == 8) {
        $p_local_header['size'] = $p_central_header['size'];
        $p_local_header['compressed_size'] = $p_central_header['compressed_size'];
        $p_local_header['crc'] = $p_central_header['crc'];
	}

    return $v_result;
  }

  function privReadEndCentralDir(&$p_central_dir)
  {
    $v_result=1;

    $v_size = filesize($this->zipname);
    @fseek($this->zip_fd, $v_size);
    if (@ftell($this->zip_fd) != $v_size)
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, 'Can\’t find the end of the archive \''.$this->zipname.'\'');

      return XPraptorZIP::errorCode();
    }

    $v_found = 0;
    if ($v_size > 26) {
      @fseek($this->zip_fd, $v_size-22);
      if (($v_pos = @ftell($this->zip_fd)) != ($v_size-22))
      {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, 'Can\'t move to the middle of the archive \''.$this->zipname.'\'');

        return XPraptorZIP::errorCode();
      }

      $v_binary_data = @fread($this->zip_fd, 4);
      $v_data = @unpack('Vid', $v_binary_data);

      if ($v_data['id'] == 0x06054b50) {
        $v_found = 1;
      }

      $v_pos = ftell($this->zip_fd);
    }

    if (!$v_found) {
      $v_maximum_size = 65557; // 0xFFFF + 22;
      if ($v_maximum_size > $v_size)
        $v_maximum_size = $v_size;
      @fseek($this->zip_fd, $v_size-$v_maximum_size);
      if (@ftell($this->zip_fd) != ($v_size-$v_maximum_size))
      {
        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, 'Can\'t move to the middle of the archive \''.$this->zipname.'\'');

        return XPraptorZIP::errorCode();
      }

      $v_pos = ftell($this->zip_fd);
      $v_bytes = 0x00000000;
      while ($v_pos < $v_size)
      {
        $v_byte = @fread($this->zip_fd, 1);

        $v_bytes = ($v_bytes << 8) | Ord($v_byte);

        if ($v_bytes == 0x504b0506)
        {
          $v_pos++;
          break;
        }

        $v_pos++;
      }

      if ($v_pos == $v_size)
      {

        XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, "Can’t find the signature for the middle of the archive.");

        return XPraptorZIP::errorCode();
      }
    }

    $v_binary_data = fread($this->zip_fd, 18);

    if (strlen($v_binary_data) != 18)
    {

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT, "Incorrect signature for the end of the archive ".strlen($v_binary_data));

      return XPraptorZIP::errorCode();
    }

    $v_data = unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size', $v_binary_data);

    if (($v_pos + $v_data['comment_size'] + 18) != $v_size) {

	  // ----- Removed in release 2.2 see readme file
	  // The check of the file size is a little too strict.
	  // Some bugs where found when a zip is encrypted/decrypted with 'crypt'.
	  // While decrypted, zip has training 0 bytes
	  if (0) {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_BAD_FORMAT,
	                       'Middle file is not the end file of the archive.'
						   .' There are some files after the end of the archive.');

      return XPraptorZIP::errorCode();
	  }
    }

    if ($v_data['comment_size'] != 0)
      $p_central_dir['comment'] = fread($this->zip_fd, $v_data['comment_size']);
    else
      $p_central_dir['comment'] = '';

    $p_central_dir['entries'] = $v_data['entries'];
    $p_central_dir['disk_entries'] = $v_data['disk_entries'];
    $p_central_dir['offset'] = $v_data['offset'];
    $p_central_dir['size'] = $v_data['size'];
    $p_central_dir['disk'] = $v_data['disk'];
    $p_central_dir['disk_start'] = $v_data['disk_start'];


    return $v_result;
  }

  function privDeleteByRule(&$p_result_list, &$p_options)
  {
    $v_result=1;
    $v_list_detail = array();

    if (($v_result=$this->privOpenFd('rb')) != 1)
    {
      return $v_result;
    }

    $v_central_dir = array();
    if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
    {
      $this->privCloseFd();
      return $v_result;
    }

    @rewind($this->zip_fd);

    $v_pos_entry = $v_central_dir['offset'];
    @rewind($this->zip_fd);
    if (@fseek($this->zip_fd, $v_pos_entry))
    {
      $this->privCloseFd();

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP, 'Incorrect archive size');

      return XPraptorZIP::errorCode();
    }

    $v_header_list = array();
    $j_start = 0;
    for ($i=0, $v_nb_extracted=0; $i<$v_central_dir['entries']; $i++)
    {

      $v_header_list[$v_nb_extracted] = array();
      if (($v_result = $this->privReadCentralFileHeader($v_header_list[$v_nb_extracted])) != 1)
      {
        $this->privCloseFd();

        return $v_result;
      }


      $v_header_list[$v_nb_extracted]['index'] = $i;

      $v_found = false;

      if (   (isset($p_options[XPrptrZIP_OPT_BY_NAME]))
          && ($p_options[XPrptrZIP_OPT_BY_NAME] != 0)) {

          for ($j=0; ($j<sizeof($p_options[XPrptrZIP_OPT_BY_NAME])) && (!$v_found); $j++) {

              if (substr($p_options[XPrptrZIP_OPT_BY_NAME][$j], -1) == "/") {

                  if (   (strlen($v_header_list[$v_nb_extracted]['stored_filename']) > strlen($p_options[XPrptrZIP_OPT_BY_NAME][$j]))
                      && (substr($v_header_list[$v_nb_extracted]['stored_filename'], 0, strlen($p_options[XPrptrZIP_OPT_BY_NAME][$j])) == $p_options[XPrptrZIP_OPT_BY_NAME][$j])) {
                      $v_found = true;
                  }
                  elseif (   (($v_header_list[$v_nb_extracted]['external']&0x00000010)==0x00000010) /* Indicates a folder */
                          && ($v_header_list[$v_nb_extracted]['stored_filename'].'/' == $p_options[XPrptrZIP_OPT_BY_NAME][$j])) {
                      $v_found = true;
                  }
              }
              elseif ($v_header_list[$v_nb_extracted]['stored_filename'] == $p_options[XPrptrZIP_OPT_BY_NAME][$j]) {
                  $v_found = true;
              }
          }
      }

      else if (   (isset($p_options[XPrptrZIP_OPT_BY_EREG]))
               && ($p_options[XPrptrZIP_OPT_BY_EREG] != "")) {

          if (ereg($p_options[XPrptrZIP_OPT_BY_EREG], $v_header_list[$v_nb_extracted]['stored_filename'])) {
              $v_found = true;
          }
      }

      else if (   (isset($p_options[XPrptrZIP_OPT_BY_PREG]))
               && ($p_options[XPrptrZIP_OPT_BY_PREG] != "")) {

          if (preg_match($p_options[XPrptrZIP_OPT_BY_PREG], $v_header_list[$v_nb_extracted]['stored_filename'])) {
              $v_found = true;
          }
      }

      else if (   (isset($p_options[XPrptrZIP_OPT_BY_INDEX]))
               && ($p_options[XPrptrZIP_OPT_BY_INDEX] != 0)) {

          for ($j=$j_start; ($j<sizeof($p_options[XPrptrZIP_OPT_BY_INDEX])) && (!$v_found); $j++) {

              if (($i>=$p_options[XPrptrZIP_OPT_BY_INDEX][$j]['start']) && ($i<=$p_options[XPrptrZIP_OPT_BY_INDEX][$j]['end'])) {
                  $v_found = true;
              }
              if ($i>=$p_options[XPrptrZIP_OPT_BY_INDEX][$j]['end']) {
                  $j_start = $j+1;
              }

              if ($p_options[XPrptrZIP_OPT_BY_INDEX][$j]['start']>$i) {
                  break;
              }
          }
      }

      if ($v_found)
      {
        unset($v_header_list[$v_nb_extracted]);
      }
      else
      {
        $v_nb_extracted++;
      }
    }

    if ($v_nb_extracted > 0) {

        $v_zip_temp_name = XPrptrZIP_TEMPORARY_DIR.uniqid('XprptZip-').'.tmp';

        $v_temp_zip = new XPraptorZIP($v_zip_temp_name);

        if (($v_result = $v_temp_zip->privOpenFd('wb')) != 1) {
            $this->privCloseFd();

            return $v_result;
        }

        for ($i=0; $i<sizeof($v_header_list); $i++) {

            @rewind($this->zip_fd);
            if (@fseek($this->zip_fd,  $v_header_list[$i]['offset'])) {
                $this->privCloseFd();
                $v_temp_zip->privCloseFd();
                @unlink($v_zip_temp_name);

                XPraptorZIP::privErrorLog(XPrptrZIP_ERR_INVALID_ARCHIVE_ZIP, 'Incorrect archive size');

                return XPraptorZIP::errorCode();
            }

            $v_local_header = array();
            if (($v_result = $this->privReadFileHeader($v_local_header)) != 1) {
                $this->privCloseFd();
                $v_temp_zip->privCloseFd();
                @unlink($v_zip_temp_name);

                return $v_result;
            }
            
            if ($this->privCheckFileHeaders($v_local_header,
			                                $v_header_list[$i]) != 1) {
            }
            unset($v_local_header);

            if (($v_result = $v_temp_zip->privWriteFileHeader($v_header_list[$i])) != 1) {
                $this->privCloseFd();
                $v_temp_zip->privCloseFd();
                @unlink($v_zip_temp_name);

                return $v_result;
            }

            if (($v_result = XprptZipUtilCopyBlock($this->zip_fd, $v_temp_zip->zip_fd, $v_header_list[$i]['compressed_size'])) != 1) {
                $this->privCloseFd();
                $v_temp_zip->privCloseFd();
                @unlink($v_zip_temp_name);

                return $v_result;
            }
        }

        $v_offset = @ftell($v_temp_zip->zip_fd);

        for ($i=0; $i<sizeof($v_header_list); $i++) {
            if (($v_result = $v_temp_zip->privWriteCentralFileHeader($v_header_list[$i])) != 1) {
                $v_temp_zip->privCloseFd();
                $this->privCloseFd();
                @unlink($v_zip_temp_name);

                return $v_result;
            }

            $v_temp_zip->privConvertHeader2FileInfo($v_header_list[$i], $p_result_list[$i]);
        }


        $v_comment = '';
        if (isset($p_options[XPrptrZIP_OPT_COMMENT])) {
          $v_comment = $p_options[XPrptrZIP_OPT_COMMENT];
        }

        $v_size = @ftell($v_temp_zip->zip_fd)-$v_offset;

        if (($v_result = $v_temp_zip->privWriteCentralHeader(sizeof($v_header_list), $v_size, $v_offset, $v_comment)) != 1) {
            unset($v_header_list);
            $v_temp_zip->privCloseFd();
            $this->privCloseFd();
            @unlink($v_zip_temp_name);

            return $v_result;
        }

        $v_temp_zip->privCloseFd();
        $this->privCloseFd();

        @unlink($this->zipname);

        XprptZipUtilRename($v_zip_temp_name, $this->zipname);
    
        unset($v_temp_zip);
    }
    
    else if ($v_central_dir['entries'] != 0) {
        $this->privCloseFd();

        if (($v_result = $this->privOpenFd('wb')) != 1) {
          return $v_result;
        }

        if (($v_result = $this->privWriteCentralHeader(0, 0, 0, '')) != 1) {
          return $v_result;
        }

        $this->privCloseFd();
    }

    return $v_result;
  }

  function privDirCheck($p_dir, $p_is_dir=false)
  {
    $v_result = 1;


    if (($p_is_dir) && (substr($p_dir, -1)=='/'))
    {
      $p_dir = substr($p_dir, 0, strlen($p_dir)-1);
    }

    if ((is_dir($p_dir)) || ($p_dir == ""))
    {
      return 1;
    }

    $p_parent_dir = dirname($p_dir);

    if ($p_parent_dir != $p_dir)
    {
      if ($p_parent_dir != "")
      {
        if (($v_result = $this->privDirCheck($p_parent_dir)) != 1)
        {
          return $v_result;
        }
      }
    }

    if (!@mkdir($p_dir, 0777))
    {
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_DIR_CREATE_FAIL, "Can't open folder '$p_dir'");

      return XPraptorZIP::errorCode();
    }

    return $v_result;
  }

  function privMerge(&$p_archive_to_add)
  {
    $v_result=1;

    if (!is_file($p_archive_to_add->zipname))
    {

      $v_result = 1;

      return $v_result;
    }

    if (!is_file($this->zipname))
    {

      $v_result = $this->privDuplicate($p_archive_to_add->zipname);

      return $v_result;
    }

    if (($v_result=$this->privOpenFd('rb')) != 1)
    {
      return $v_result;
    }

    $v_central_dir = array();
    if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
    {
      $this->privCloseFd();
      return $v_result;
    }

    @rewind($this->zip_fd);

    if (($v_result=$p_archive_to_add->privOpenFd('rb')) != 1)
    {
      $this->privCloseFd();

      return $v_result;
    }

    $v_central_dir_to_add = array();
    if (($v_result = $p_archive_to_add->privReadEndCentralDir($v_central_dir_to_add)) != 1)
    {
      $this->privCloseFd();
      $p_archive_to_add->privCloseFd();

      return $v_result;
    }

    @rewind($p_archive_to_add->zip_fd);

    $v_zip_temp_name = XPrptrZIP_TEMPORARY_DIR.uniqid('XprptZip-').'.tmp';

    if (($v_zip_temp_fd = @fopen($v_zip_temp_name, 'wb')) == 0)
    {
      $this->privCloseFd();
      $p_archive_to_add->privCloseFd();

      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, 'Can\'t open temporary file \''.$v_zip_temp_name.'\' in binary mode');

      return XPraptorZIP::errorCode();
    }

    $v_size = $v_central_dir['offset'];
    while ($v_size != 0)
    {
      $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
      $v_buffer = fread($this->zip_fd, $v_read_size);
      @fwrite($v_zip_temp_fd, $v_buffer, $v_read_size);
      $v_size -= $v_read_size;
    }

    $v_size = $v_central_dir_to_add['offset'];
    while ($v_size != 0)
    {
      $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
      $v_buffer = fread($p_archive_to_add->zip_fd, $v_read_size);
      @fwrite($v_zip_temp_fd, $v_buffer, $v_read_size);
      $v_size -= $v_read_size;
    }

    $v_offset = @ftell($v_zip_temp_fd);

    $v_size = $v_central_dir['size'];
    while ($v_size != 0)
    {
      $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
      $v_buffer = @fread($this->zip_fd, $v_read_size);
      @fwrite($v_zip_temp_fd, $v_buffer, $v_read_size);
      $v_size -= $v_read_size;
    }

    $v_size = $v_central_dir_to_add['size'];
    while ($v_size != 0)
    {
      $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
      $v_buffer = @fread($p_archive_to_add->zip_fd, $v_read_size);
      @fwrite($v_zip_temp_fd, $v_buffer, $v_read_size);
      $v_size -= $v_read_size;
    }

    $v_comment = $v_central_dir['comment'].' '.$v_central_dir_to_add['comment'];

    $v_size = @ftell($v_zip_temp_fd)-$v_offset;

    $v_swap = $this->zip_fd;
    $this->zip_fd = $v_zip_temp_fd;
    $v_zip_temp_fd = $v_swap;

    if (($v_result = $this->privWriteCentralHeader($v_central_dir['entries']+$v_central_dir_to_add['entries'], $v_size, $v_offset, $v_comment)) != 1)
    {
      $this->privCloseFd();
      $p_archive_to_add->privCloseFd();
      @fclose($v_zip_temp_fd);
      $this->zip_fd = null;

      unset($v_header_list);

      return $v_result;
    }

    $v_swap = $this->zip_fd;
    $this->zip_fd = $v_zip_temp_fd;
    $v_zip_temp_fd = $v_swap;

    $this->privCloseFd();
    $p_archive_to_add->privCloseFd();

    @fclose($v_zip_temp_fd);

    @unlink($this->zipname);

    XprptZipUtilRename($v_zip_temp_name, $this->zipname);

    return $v_result;
  }

  function privDuplicate($p_archive_filename)
  {
    $v_result=1;

    if (!is_file($p_archive_filename))
    {

      $v_result = 1;

      return $v_result;
    }

    if (($v_result=$this->privOpenFd('wb')) != 1)
    {
      return $v_result;
    }

    if (($v_zip_temp_fd = @fopen($p_archive_filename, 'rb')) == 0)
    {
      $this->privCloseFd();
      XPraptorZIP::privErrorLog(XPrptrZIP_ERR_READ_OPEN_FAIL, 'Can\'t open temporary file \''.$p_archive_filename.'\' in binary mode');

      return XPraptorZIP::errorCode();
    }

    $v_size = filesize($p_archive_filename);
    while ($v_size != 0)
    {
      $v_read_size = ($v_size < XPrptrZIP_READ_BLOCK_SIZE ? $v_size : XPrptrZIP_READ_BLOCK_SIZE);
      $v_buffer = fread($v_zip_temp_fd, $v_read_size);
      @fwrite($this->zip_fd, $v_buffer, $v_read_size);
      $v_size -= $v_read_size;
    }

    $this->privCloseFd();

    @fclose($v_zip_temp_fd);

    return $v_result;
  }

  function privErrorLog($p_error_code=0, $p_error_string='')
  {
    if (XPrptrZIP_ERROR_EXTERNAL == 1) {
      XprptError($p_error_code, $p_error_string);
    }
    else {
      $this->error_code = $p_error_code;
      $this->error_string = $p_error_string;
    }
  }

  function privErrorReset()
  {
    if (XPrptrZIP_ERROR_EXTERNAL == 1) {
      XprptErrorReset();
    }
    else {
      $this->error_code = 0;
      $this->error_string = '';
    }
  }

  function privDecrypt($p_encryption_header, &$p_buffer, $p_size, $p_crc)
  {
    $v_result=1;
    
    $v_pwd = "test";
    
    $p_buffer = XprptZipUtilZipDecrypt($p_buffer, $p_size, $p_encryption_header,
	                                 $p_crc, $v_pwd);
    
    return $v_result;
  }

  }

  function XprptZipUtilPathReduction($p_dir)
  {
    $v_result = "";

    if ($p_dir != "")
    {
      $v_list = explode("/", $p_dir);

      for ($i=sizeof($v_list)-1; $i>=0; $i--)
      {
        if ($v_list[$i] == ".")
        {
        }
        else if ($v_list[$i] == "..")
        {
          $i--;
        }
        else if (($v_list[$i] == "") && ($i!=(sizeof($v_list)-1)) && ($i!=0))
        {
        }
        else
        {
          $v_result = $v_list[$i].($i!=(sizeof($v_list)-1)?"/".$v_result:"");
        }
      }
    }

    return $v_result;
  }

  function XprptZipUtilPathInclusion($p_dir, $p_path)
  {
    $v_result = 1;

    $v_list_dir = explode("/", $p_dir);
    $v_list_dir_size = sizeof($v_list_dir);
    $v_list_path = explode("/", $p_path);
    $v_list_path_size = sizeof($v_list_path);

    $i = 0;
    $j = 0;
    while (($i < $v_list_dir_size) && ($j < $v_list_path_size) && ($v_result)) {

      if ($v_list_dir[$i] == '') {
        $i++;
        continue;
      }
      if ($v_list_path[$j] == '') {
        $j++;
        continue;
      }

      if (($v_list_dir[$i] != $v_list_path[$j]) && ($v_list_dir[$i] != '') && ( $v_list_path[$j] != ''))  {
        $v_result = 0;
      }

      $i++;
      $j++;
    }

    if ($v_result) {
      while (($j < $v_list_path_size) && ($v_list_path[$j] == '')) $j++;
      while (($i < $v_list_dir_size) && ($v_list_dir[$i] == '')) $i++;

      if (($i >= $v_list_dir_size) && ($j >= $v_list_path_size)) {
        $v_result = 2;
      }
      else if ($i < $v_list_dir_size) {
        $v_result = 0;
      }
    }

    return $v_result;
  }

  function XprptZipUtilCopyBlock($p_src, $p_dest, $p_size, $p_mode=0)
  {
    $v_result = 1;

    if ($p_mode==0)
    {
      while ($p_size != 0)
      {
        $v_read_size = ($p_size < XPrptrZIP_READ_BLOCK_SIZE ? $p_size : XPrptrZIP_READ_BLOCK_SIZE);
        $v_buffer = @fread($p_src, $v_read_size);
        @fwrite($p_dest, $v_buffer, $v_read_size);
        $p_size -= $v_read_size;
      }
    }
    else if ($p_mode==1)
    {
      while ($p_size != 0)
      {
        $v_read_size = ($p_size < XPrptrZIP_READ_BLOCK_SIZE ? $p_size : XPrptrZIP_READ_BLOCK_SIZE);
        $v_buffer = @gzread($p_src, $v_read_size);
        @fwrite($p_dest, $v_buffer, $v_read_size);
        $p_size -= $v_read_size;
      }
    }
    else if ($p_mode==2)
    {
      while ($p_size != 0)
      {
        $v_read_size = ($p_size < XPrptrZIP_READ_BLOCK_SIZE ? $p_size : XPrptrZIP_READ_BLOCK_SIZE);
        $v_buffer = @fread($p_src, $v_read_size);
        @gzwrite($p_dest, $v_buffer, $v_read_size);
        $p_size -= $v_read_size;
      }
    }
    else if ($p_mode==3)
    {
      while ($p_size != 0)
      {
        $v_read_size = ($p_size < XPrptrZIP_READ_BLOCK_SIZE ? $p_size : XPrptrZIP_READ_BLOCK_SIZE);
        $v_buffer = @gzread($p_src, $v_read_size);
        @gzwrite($p_dest, $v_buffer, $v_read_size);
        $p_size -= $v_read_size;
      }
    }

    return $v_result;
  }

  function XprptZipUtilRename($p_src, $p_dest)
  {
    $v_result = 1;

    if (!@rename($p_src, $p_dest)) {

      if (!@copy($p_src, $p_dest)) {
        $v_result = 0;
      }
      else if (!@unlink($p_src)) {
        $v_result = 0;
      }
    }

    return $v_result;
  }

  function XprptZipUtilOptionText($p_option)
  {
    
    $v_list = get_defined_constants();
    for (reset($v_list); $v_key = key($v_list); next($v_list)) {
	  $v_prefix = substr($v_key, 0, 10);
	  if ((($v_prefix == 'XPrptrZIP_OPT') || ($v_prefix == 'XPrptrZIP_CB_'))
	      && ($v_list[$v_key] == $p_option)) {
          return $v_key;
	    }
    }
    
    $v_result = 'Unknown';

    return $v_result;
  }

  function XprptZipUtilTranslateWinPath($p_path, $p_remove_disk_letter=true)
  {
    if (stristr(php_uname(), 'windows')) {
      if (($p_remove_disk_letter) && (($v_position = strpos($p_path, ':')) != false)) {
          $p_path = substr($p_path, $v_position+1);
      }
      if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0,1) == '\\')) {
          $p_path = strtr($p_path, '\\', '/');
      }
    }
    return $p_path;
  }
?>
