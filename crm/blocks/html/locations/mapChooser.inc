<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div id="location_map"></div>
<script type="text/javascript">
	var DEFAULT_LATITUDE  = <?php echo DEFAULT_LATITUDE; ?>,
		DEFAULT_LONGITUDE = <?php echo DEFAULT_LONGITUDE; ?>;
</script>
<button type="button" id="useThisLocation">
	<span class="fa fa-check"></span>
	<?php echo $this->translate('labels.use_location'); ?>
</button>
<?php
$this->template->addToAsset('scripts', YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts', GOOGLE_MAPS);
$this->template->addToAsset('scripts', BASE_URL.'/js/locations/mapChooser.js');
