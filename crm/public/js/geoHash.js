/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */

'use strict';

var GEOHASH = {
	getGeohashLevel: function (zoomLevel) {
		var geohashLevel;
		
		switch (zoomLevel) {
			case 0:
				geohashLevel = 1;
				break;
			case 1:
				geohashLevel = 1;
				break;
			case 2:
				geohashLevel = 1;
				break;
			case 3:
				geohashLevel = 1;
				break;
			case 4:
				geohashLevel = 2;
				break;
			case 5:
				geohashLevel = 2;
				break;
			case 6:
				geohashLevel = 3;
				break;
			case 7:
				geohashLevel = 3;
				break;
			case 8:
				geohashLevel = 3;
				break;
			case 9:
				geohashLevel = 4;
				break;
			case 10:
				geohashLevel = 4;
				break;
			case 11:
				geohashLevel = 5;
				break;
			case 12:
				geohashLevel = 5;
				break;
			case 13:
				geohashLevel = 5;
				break;
			case 14:
				geohashLevel = 6;
				break;
			case 15:
				geohashLevel = 6;
				break;
			case 16:
				geohashLevel = 7;
				break;
			case 17:
				geohashLevel = 7;
				break;
			case 18:
				geohashLevel = 7;
				break;
			case 19:
				geohashLevel = 8;
				break;
			case 20:
				geohashLevel = 8;
				break;
			case 21:
				geohashLevel = 8;
				break;
			default:
				geohashLevel = 1;
		}
		
		return geohashLevel;
	}
};
