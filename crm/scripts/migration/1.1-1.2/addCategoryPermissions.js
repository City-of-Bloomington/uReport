db.categories.update(
	{},
	{'$set':{'postingPermissionLevel':'staff','displayPermissionLevel':'staff'}},
	false,true
)
