<?php
# @Author: Andrea F. Daniele <afdaniele>
# @Email:  afdaniele@ttic.edu
# @Last modified by:   afdaniele

use \system\classes\Core;
use \system\classes\Configuration;

$default_tool = 'index';

$tool = is_null(Configuration::$ACTION)? $default_tool : Configuration::$ACTION;
$tool_file = __DIR__."/tools/$tool.php";

if (!file_exists($tool_file)) {
  Core::redirectTo('developer');
}else{
  include_once $tool_file;
}
?>
