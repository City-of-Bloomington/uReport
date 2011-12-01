db.people.update(
	{'authenticationMethod':'LDAP'},
	{'$set':{'authenticationMethod':'Employee'}},
	false,true
)