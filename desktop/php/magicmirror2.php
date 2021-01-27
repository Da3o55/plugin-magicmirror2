<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('magicmirror2');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
   <div class="col-xs-12 eqLogicThumbnailDisplay">
  <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
  <div class="eqLogicThumbnailContainer">
      <div class="cursor eqLogicAction logoPrimary" data-action="add">
        <i class="fas fa-plus-circle"></i>
        <br>
        <span>{{Ajouter}}</span>
    </div>
      <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
      <i class="fas fa-wrench"></i>
    <br>
    <span>{{Configuration}}</span>
  </div>
  </div>
  <legend><i class="fas fa-table"></i> {{Mes templates}}</legend>
	   <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
<div class="eqLogicThumbnailContainer">
    <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
	echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
	echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
	echo '<br>';
	echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
    <div role="tabpanel" class="tab-pane active" id="eqlogictab">
      <br/>
    <form class="form-horizontal">
        <fieldset>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                <div class="col-sm-3">
                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement template}}"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                <div class="col-sm-3">
                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                        <option value="">{{Aucun}}</option>
                        <?php
foreach (jeeObject::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                   </select>
               </div>
           </div>
	   <div class="form-group">
                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                <div class="col-sm-9">
                 <?php
                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                    echo '</label>';
                    }
                  ?>
               </div>
           </div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-9">
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
		</div>
	</div>
	<!-- Equipement Configuration Zone -->
    <div class="form-group">
        <label class="col-sm-3 control-label">{{Addresse IP}}</label>
        <div class="col-sm-3">
            <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="magicmirror_ip" placeholder="ip address of magicmirror²"/>
        </div>
		<a class="btn btn-default cmdAction" data-cmd="checkAPI">Tester l'accès à l'api&nbsp;&nbsp;<span class="iconCheck"><i class='fas fa-question'></i></span></a> <span id="cjmm_apiCheckedAdvise"></span>
		<input type="hidden" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cjmm_apiChecked"  id="cjmm_apiChecked">
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">{{Port}}</label>
        <div class="col-sm-3">
            <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cjmm_customport" placeholder="8080"/>
        </div>
		<script>
		var cmd = $('.cmdAction[data-cmd=checkAPI]');

		$('.eqLogicAttr[data-l2key=magicmirror_ip]').on('change', function () {
			$('#cjmm_apiChecked').value(0);
		});

		$('#cjmm_apiChecked').on('change', function () {
			if($('#cjmm_apiChecked').value() == '1'){
				cmd.find('.iconCheck').empty().append("<i class='fas fa-check' style='color: lightgreen;'></i>");
			}else{
				cmd.find('.iconCheck').empty().append("<i class='fas fa-times' style='color: red;'></i>");
			}
		});

		cmd.on('click', function () {
			$('#cjmm_apiCheckedAdvise').empty().append("Tentative de connexion...");
            $.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // méthode de transmission des données au fichier php
                url: "plugins/magicmirror2/core/ajax/magicmirror2.ajax.php", // url du fichier php
                data: {
                    action: "checkAPI",
					ip_addr: ''+($('.eqLogicAttr[data-l2key=magicmirror_ip]').value())+'',
					customport: ''+($('.eqLogicAttr[data-l2key=cjmm_customport]').value())+'',
                },
                dataType: 'json',
                global: false,
                error: function (request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
					$('#cjmm_apiCheckedAdvise').empty().append("Vérifier votre réseau ou l'installation de MMM-Remote-Control sur votre MagicMirror2 !");
					cmd.find('.iconCheck').empty().append("<i class='fas fa-times' style='color: red;'></i>");
                    return;
                }
				$('#cjmm_apiCheckedAdvise').empty().append("L'api est accessible !");
				cmd.find('.iconCheck').empty().append("<i class='fas fa-check' style='color: lightgreen;'></i>");
				$('#cjmm_apiChecked').value(1);
				console.log(data);
            }
        });
		});
	</script>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">{{Type de notification}}</label>
        <div class="col-sm-3">
            <select id="sel_object" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cjmm_notification_type" >
                <option value="notification">{{Notification}}</option>
                <option value="alert">{{Alerte}}</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">{{Délai d'affichage de la notification}}</label>
        <div class="col-sm-3">
            <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cjmm_notification_timer" placeholder="10000" text="10000"/>
        </div>
    </div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Plugin::{{MMM-BackgroundSlideshow}}</label>
		<div class="col-sm-9">
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr"  data-l1key="configuration" data-l2key="plugin-BackgroundSlideshow-enable"/>{{Activer}}</label>
		</div>
	</div>
</fieldset>
</form>
</div>
      <div role="tabpanel" class="tab-pane" id="commandtab">
<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
<table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>{{Id}}</th><th width="100px">{{Nom}}</th><th width="300px">{{Description}}<th>{{Type}}</th><th>{{Options}}</th><th>{{Action}}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>
</div>

</div>
</div>

<?php include_file('desktop', 'magicmirror2', 'js', 'magicmirror2');?>
<?php include_file('core', 'plugin.template', 'js');?>
