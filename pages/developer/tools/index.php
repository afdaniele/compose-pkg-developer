<?php
# @Author: Andrea F. Daniele <afdaniele>
# @Email:  afdaniele@ttic.edu
# @Last modified by:   afdaniele

use \system\classes\Core;
use \system\classes\Configuration;
?>

<style type="text/css">
  .devel-btn {
    width: 174px;
    height: 170px;
    background-color: white;
    background-image: none;
    margin: 6px;
    padding: 0;
  }

  .devel-btn-icon {
    font-size: 40px;
    margin: 17px 0 11px 0;
  }

  .devel-btn legend {
    margin: 0;
  }

  .devel-btn .devel-btn-title {
    margin: 0;
    line-height: 40px;
  }

  .devel-btn .devel-btn-title.new {
    background-color: lemonchiffon;
  }

  .devel-btn .devel-btn-title.setting {
    background-color: lightblue;
  }
</style>

<?php
$tools = [
  "new-package" => [
    "title" => "New Package",
    "icon" => "archive",
    "class" => "new"
  ],
  "new-page" => [
    "title" => "New Page",
    "icon" => "file",
    "class" => "new"
  ],
  "new-api-endpoint" => [
    "title" => "New API end-point",
    "icon" => "plug",
    "class" => "new"
  ],
  "publish-package" => [
    "title" => "Publish package",
    "icon" => "cloud-upload",
    "class" => "setting"
  ],
  "test-api" => [
    "title" => "Test API end-point",
    "icon" => "exchange",
    "class" => "setting"
  ]
];
?>

<h2 class="page-title">Developer Tools</h2>

<div style="padding: 0 10px;">
  <?php
  foreach ($tools as $tool_key => &$tool) {
    ?>
    <a class="btn btn-default devel-btn" href="<?php echo Core::getURL(Configuration::$PAGE, $tool_key)?>" role="button">
      <h1>
        <i class="fa fa-<?php echo $tool['icon'] ?> devel-btn-icon" aria-hidden="true"></i>
      </h1>
      <br/>
      <legend></legend>
      <h4 class="text-center devel-btn-title <?php echo $tool['class'] ?>">
        <?php echo $tool['title'] ?>
      </h4>
      <legend></legend>
    </a>
    <?php
  }
  ?>
</div>
