<?php
# @Author: Andrea F. Daniele <afdaniele>
# @Email:  afdaniele@ttic.edu
# @Last modified by:   afdaniele

use \system\classes\Core;
use \system\classes\Configuration;
use \system\classes\Formatter;

$installed_packages = Core::getPackagesList();
unset($installed_packages['core']);
?>

<!-- Load YAML library -->
<script type="text/javascript" src="<?php echo Configuration::$BASE_URL ?>js/js-yaml.min.js"></script>

<style type="text/css">
  #packages-table > thead > tr{
    font-weight: bold;
  }

  #packages-table > thead td:nth-child(1),
  #packages-table > thead td:nth-child(3),
  #packages-table > thead td:nth-child(4){
    text-align: center;
  }

  #packages-table > tbody .compose-package > td:nth-child(1),
  #packages-table > tbody .compose-package > td:nth-child(3),
  #packages-table > tbody .compose-package > td:nth-child(4){
    text-align: center;
    vertical-align: middle;
  }

  #packages-table > tbody .compose-package > td:nth-child(1){
    font-weight: bold;
  }

  #packages-table > tbody .compose-package > td:nth-child(1),
  #packages-table > tbody .compose-package > td:nth-child(2),
  #packages-table > tbody .compose-package > td:nth-child(3){
    border-right: none;
  }

  #packages-table > tbody .compose-package > td:nth-child(2),
  #packages-table > tbody .compose-package > td:nth-child(3),
  #packages-table > tbody .compose-package > td:nth-child(4){
    border-left: none;
  }

  #packages-table > tbody .compose-package > td:nth-child(4){
    padding-left: 0;
    padding-right: 0;
  }

  #packages-table > tbody .compose-package > td:nth-child(5){
    text-align: center;
    vertical-align: middle;
  }

  #packages-table > tbody .compose-package .main-button{
    width: 100px;
  }

  #packages-table > tbody .compose-package .update-button{
    width: 96px;
    margin: 0 4px;
  }

  #packages-table > tbody .compose-package .package-icon{
    width: 42px;
  }

  #packages-table > tbody .compose-package > td:nth-child(4) .disabled-button{
    background-image: none;
    background-color: grey;
    border: 1px solid lightgray;
  }

  #packages-table > tbody .compose-package.to-be-installed{
    background-color: rgba(0, 255, 0, 0.1);
  }

  #packages-table > tbody .compose-package.to-be-updated{
    background-color: rgba(0, 0, 255, 0.1);
  }

  #packages-table > tbody .compose-package.to-be-uninstalled{
    background-color: rgba(255, 0, 0, 0.1);
  }
</style>

<h2 class="page-title">Developer Tools - Publish Package</h2>

<div class="progress" style="height:14px">
  <div id="loading_status_bar" class="progress-bar progress-bar-striped progress-bar-default active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:100%">
  </div>
</div>

<?php
$assets_index_url = sanitize_url(
  sprintf(
    '%s/%s/index',
    Configuration::$ASSETS_STORE_URL,
    Configuration::$ASSETS_STORE_BRANCH
  )
);
?>

<div class="input-group" style="margin-top:28px">
  <span class="input-group-addon" id="packages-search-addon">Search package</span>
  <input type="text" class="form-control" id="packages-search-field" style="height:42px" aria-describedby="packages-search-addon">
</div>

<table class="table table-striped table-bordered table-hover" id="packages-table" style="margin-top:20px">
  <thead>
    <tr>
      <td class="col-md-1"></td>
      <td class="col-md-6">
        Package
      </td>
      <td class="col-md-1">
        Registered
      </td>
      <td class="col-md-2">
        Status
      </td>
      <td class="col-md-2">
        Actions
      </td>
    </tr>
  </thead>
  <tbody id="packages-table-body">
  </tbody>
</table>


<script type="text/javascript">

  packages_to_install = [];
  packages_to_update = [];
  packages_to_uninstall = [];

  var providers = {
    'github.com': 'https://raw.githubusercontent.com/{0}/{1}/{2}',
    'bitbucket.org': 'https://bitbucket.org/{0}/{1}/raw/{2}'
  }

  var providers_repo = {
    'github.com': 'https://github.com/{0}/{1}',
    'bitbucket.org': 'https://bitbucket.org/{0}/{1}'
  }

  var providers_source = {
    'github.com': 'https://github.com/{0}/{1}/tree/{2}',
    'bitbucket.org': 'https://bitbucket.org/{0}/{1}/src/{2}'
  }

  var installed_packages = JSON.parse('<?php echo json_encode($installed_packages) ?>');

  var installed_packages_ids = Object.keys(installed_packages);

  var packages_table_body_row_template = `
    <tr class="compose-package" id="{6}" data-search="{5}">
      <td>
        {0}
      </td>
      <td>
        {1}
      </td>
      <td>
        {2}
      </td>
      <td>
        {3}
      </td>
      <td>
        {4}
      </td>
    </tr>`;

  var package_template = `
    <strong>
      {0}
      <br/>
    </strong>
    ID: <span class="mono" style="color:grey">{1}</span><br/>
    Maintainer: <span class="mono" style="color:grey">{2}</span><br/>
    {4}<br/>
    <div style="margin-top:4px">
      {3}
    </div>`;

  var package_version_line_template = `
    {0}: <span class="mono" style="color: black">{2}</span>
  `;

  function publish_package(package_id) {
    console.log(package_id);
  }//publish_package

  function add_package_to_list(num, package_id, installed_package, registry_package){
    var is_published = registry_package != null;
    var version_str_fmt = '{0}{1}{2}';
    var published_version = is_published? registry_package.git_branch : null;

    // show installed version
    var installed_version = installed_packages[package_id].codebase.head_tag;
    installed_version = (installed_version == 'ND')? 'devel' : installed_version;
    var installed_version_str = package_version_line_template.format(
      'Installed version',
      installed_version
    );
    // show available version (if any)
    var needs_publish = (!is_published) || true;
    // ---
    var col1 = package_template.format(
      installed_package.name,
      installed_package.id,
      installed_package.codebase.git_owner,
      installed_package.description,
      installed_version_str
    );
    // ---
    var published = (is_published)?
      '<?php echo Formatter::format(1, Formatter::BOOLEAN) ?>' : '<?php echo Formatter::format(0, Formatter::BOOLEAN) ?>';
    // ---
    var publish_btn = `
      <a role="button" class="btn btn-info publish-button action-button {1}" onclick="publish_package('{0}')" href="javascript:void(0);">
        <i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;
        Publish
      </a>`;
    var publish_action = publish_btn.format(
      installed_package.id,
      needs_publish? '' : 'disabled disabled-button'
    );


    var icon_url = is_published? registry_package.icon : 'images/_default.png';
    if (!icon_url.startsWith('http')){
      icon_url = '{0}/{1}'.format(
        '<?php
        echo sprintf(
          '%s/%s',
          Configuration::$ASSETS_STORE_URL,
          Configuration::$ASSETS_STORE_BRANCH
        )
        ?>',
        icon_url
      );
    }
    // ---
    var status = "";
    $('#packages-table-body').html(
      $('#packages-table-body').html() +
      packages_table_body_row_template.format(
        '<img class="package-icon" src="{0}"></img>'.format(icon_url),
        col1,
        published,
        status,
        publish_action,
        "{0},{1},{2}".format(installed_package.id, installed_package.name, installed_package.description),
        "compose-package-{0}".format(installed_package.id)
      )
    )
  }

  function fetch_package_list_success_fcn(result){
    console.log(installed_packages);

    var doc = jsyaml.load(result);
    // add packages to the list
    $('#loading_status_bar').removeClass('progress-bar-striped progress-bar-default');
    $('#loading_status_bar').addClass('progress-bar-success');


    // convert list of packages in object
    registry = {};
    for (var i = 0; i < doc.packages.length; i++) {
      registry[doc.packages[i].id] = doc.packages[i];
    }
    // iterate over the installed packages
    for (var package_id in installed_packages) {
      var installed_package = installed_packages[package_id];
      var registry_package = registry.hasOwnProperty(package_id)? registry[package_id] : null;
      // ---
      add_package_to_list(i+1, package_id, installed_package, registry_package);
      //
      setTimeout(function(){
        $('#loading_status_bar').parent().remove();
      }, 800);
    }
  }

  $(document).ready(function(){
    var url = "<?php echo $assets_index_url ?>";
    callExternalAPI(
      url,
      'GET',
      'text',
      false,
      false,
      fetch_package_list_success_fcn,
      true
    );
  });

  // filter by keyword
  $('#packages-search-field').keyup(function(){
    var valThis = $(this).val().toLowerCase();
    $('.compose-package').each(function(){
      var text = $(this).data('search').toLowerCase();
      var parent = $(this);
      (text.indexOf(valThis) != -1) ? parent.show() : parent.hide();
    });
  });

</script>
