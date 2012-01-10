db.tickets.find({'client_id':{'$exists':true}}).forEach( function(ticket) {
	ticket.client_id = ObjectId(ticket.client_id);
	db.tickets.save(ticket);
});