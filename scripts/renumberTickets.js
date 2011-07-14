function counter() {
	var ret = db.counters.findAndModify({query:{_id:'tickets'},update:{'$inc':{'next':1}}});
	return ret.next;
}
db.counters.insert({'_id':'tickets','next':1});
db.tickets.find().forEach( function(ticket) {
	ticket.number = counter();
	db.tickets.save(ticket);
});