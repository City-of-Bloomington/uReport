db.runCommand({distinct:'categories',key:'group'}).values.forEach( function(g) {
	if (g != '') {
		db.categoryGroups.insert({'name':g});
	}
});

db.categoryGroups.find().forEach( function (g) {
	db.categories.update(
		{'group':g.name},
		{'$set':{'group':g}},
		false, true
	)
});

db.categoryGroups.insert({'name':'Other','order':10});
db.categories.update(
	{'group._id':{'$exists':false}},
	{'$set':{'group':db.categoryGroups.findOne({'name':'Other'})}},
	false, true
);
