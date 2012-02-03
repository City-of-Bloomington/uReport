db.people.find({'roles':{'$exists':true}}).forEach( function(person) {
	db.people.update({'_id':person._id},{'$set':{'role':person.roles[0]}});
	db.people.update({'_id':person._id},{'$unset':{'roles':true}});
});
