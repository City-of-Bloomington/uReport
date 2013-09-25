<div>
	<h2>Admin</h2>
	<ul>
		<li><a href="<?php echo BASE_URL; ?>/users">         <?php echo $this->translate(array('labels.user'         ,'labels.users'         ,2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/departments">   <?php echo $this->translate(array('labels.department'   ,'labels.departments'   ,2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/categories">    <?php echo $this->translate(array('labels.category'     ,'labels.categories'    ,2)); ?></a> /
			<a href="<?php echo BASE_URL; ?>/categories/sla"><?php echo $this->translate('labels.sla'); ?></a>
		</li>
		<li><a href="<?php echo BASE_URL; ?>/categoryGroups"><?php echo $this->translate(array('labels.categoryGroup','labels.categoryGroups',2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/substatus">     <?php echo $this->translate(array('labels.substatus'    ,'labels.substatuses'   ,2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/actions">       <?php echo $this->translate(array('labels.action'       ,'labels.actions'       ,2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/clients">       <?php echo $this->translate(array('labels.client'       ,'labels.clients'       ,2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/labels">        <?php echo $this->translate(array('labels.label'        ,'labels.labels'        ,2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/contactMethods"><?php echo $this->translate(array('labels.contactMethod','labels.contactMethods',2)); ?></a></li>
		<li><a href="<?php echo BASE_URL; ?>/issueTypes">    <?php echo $this->translate(array('labels.issueType'    ,'labels.issueTypes'    ,2)); ?></a></li>
	</ul>
</div>
