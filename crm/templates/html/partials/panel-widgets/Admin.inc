<div>
	<h2><?= $this->_('labels.admin'); ?></h2>
	<ul>
		<li><a href="<?= BASE_URL; ?>/users">         <?= $this->_(['labels.user'         ,'labels.users'         ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/departments">   <?= $this->_(['labels.department'   ,'labels.departments'   ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/categories">    <?= $this->_(['labels.category'     ,'labels.categories'    ,2]); ?></a> /
			<a href="<?= BASE_URL; ?>/categories/sla"><?= $this->_('labels.sla'); ?></a>
		</li>
		<li><a href="<?= BASE_URL; ?>/categoryGroups"><?= $this->_(['labels.categoryGroup','labels.categoryGroups',2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/substatus">     <?= $this->_(['labels.substatus'    ,'labels.substatuses'   ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/actions">       <?= $this->_(['labels.action'       ,'labels.actions'       ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/clients">       <?= $this->_(['labels.client'       ,'labels.clients'       ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/labels">        <?= $this->_(['labels.label'        ,'labels.labels'        ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/contactMethods"><?= $this->_(['labels.contactMethod','labels.contactMethods',2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/issueTypes">    <?= $this->_(['labels.issueType'    ,'labels.issueTypes'    ,2]); ?></a></li>
	</ul>
</div>
