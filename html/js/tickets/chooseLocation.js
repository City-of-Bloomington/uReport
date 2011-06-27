"use strict";
// Activate the Find Address Form as an overlay
YUI().use('node','overlay','io-form',function(Y) {
	var overlay = new Y.Overlay({
		srcNode:'#find_location_overlay',
		footerContent:'<span class="button"><span class="cancel"><button type="button">Cancel</button</div></div>',
		align:{
			node:'#findAddressButton',
			points:[Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]
		}
	});
	Y.io(BASE_URL+'/locations/partial.php?partial=locations/findLocationForm.inc;return_url='+BASE_URL+'/locations/viewLocation.php',{
		on:{
			complete:function(id,o,args) {
				overlay.set('bodyContent',o.responseText);
			}
		}
	});
	overlay.render();
	overlay.hide();

	Y.on('click',function(e) {
		e.preventDefault();
		overlay.show();
	},'#findAddressButton');

	Y.on('click',Y.bind(overlay.hide, overlay),'#find_location_overlay .cancel button');
	Y.on('submit',function(e) {
		e.preventDefault();
		Y.io(BASE_URL+'/locations/partial.php?partial=locations/findLocationResults.inc',{
			form: { id:e.target },
			on: {
				complete: function(id,o,args) {
					var results = Y.one('#find_location_overlay .findLocationResults');
					if (results) {
						results.remove(true);
					}
					overlay.setStdModContent(
						Y.WidgetStdMod.BODY,
						o.responseText,
						Y.WidgetStdMod.AFTER
					);
					Y.all('#find_location_overlay .findLocationResults a').on('click',function(e) {
						e.preventDefault();
						var uri = e.target.get('href')+';partial=location-panel;disableLinks';
						Y.io(uri,{
							on: {
								complete: function(id,o,args) {
									var locationInfo = Y.one('#location-panel .locationInfo'),
										ticketList = Y.one('#location-panel .ticketList');
									if (locationInfo) { locationInfo.remove(true); }
									if (ticketList) { ticketList.remove(true); }
									Y.one('#location-panel').append(o.responseText);
									overlay.hide();
								}
							}
						});
					});
				}
			}
		});
	},'#find_location_overlay form');
});
