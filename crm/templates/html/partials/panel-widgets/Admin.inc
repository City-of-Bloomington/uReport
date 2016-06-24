<div>
	<h2><?= $this->_('admin'); ?></h2>
	<ul>
		<li><a href="<?= BASE_URL; ?>/users">         <?= $this->_(['user'         ,'users'         ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/departments">   <?= $this->_(['department'   ,'departments'   ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/categories">    <?= $this->_(['category'     ,'categories'    ,2]); ?></a> /
			<a href="<?= BASE_URL; ?>/categories/sla"><?= $this->_('sla'); ?></a>
		</li>
		<li><a href="<?= BASE_URL; ?>/categoryGroups"><?= $this->_(['categoryGroup','categoryGroups',2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/substatus">     <?= $this->_(['substatus'    ,'substatuses'   ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/actions">       <?= $this->_(['action'       ,'actions'       ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/clients">       <?= $this->_(['client'       ,'clients'       ,2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/contactMethods"><?= $this->_(['contactMethod','contactMethods',2]); ?></a></li>
		<li><a href="<?= BASE_URL; ?>/issueTypes">    <?= $this->_(['issueType'    ,'issueTypes'    ,2]); ?></a></li>
	</ul>
</div>
