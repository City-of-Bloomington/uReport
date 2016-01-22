/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */
var URL = {
  replaceParam: function(href, key, value) {
    var regex;

    regex = new RegExp("(" + key + "=)\.\*?(;|$)");
    if (regex.test(href)) {
      href = href.replace(regex, '$1' + value + '$2');
    } else {
      if (href.indexOf('?') === -1) {
        href += '?' + key + '=' + value;
      } else {
        href += '&' + key + '=' + value;
      }
    }

    return href;
  }
};
