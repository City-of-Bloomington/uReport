<?php
/**
 * Interface for external address information
 *
 * This allows uReport to request additional information about addresses
 * from whatever official address system the city uses.
 *
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @see /crm/data/Classes/MasterAddress
 */
namespace Application\Models;

interface AddressService
{
	/**
	 * Define custom form fields for dealing with AddressServiceCache data
	 *
	 * AddressServiceCache fields can be included in forms, such as search,
	 * and displayed with the rest of the ticket information.
	 *
	 * When the sytem displays ticket information, it will look at this
	 * array for any additional fields of information to display
	 *
	 * When the system draws a search form, a call will be made to get this
	 * description of any custom fields to include.
	 * The description will be used as the label
	 * The formElement controls what HTML form element to render.
	 *		If the formElement is "select", then a drop down will be created,
	 *		populated with all possible values from the addressServiceCache
	 *
	 *		All other form elements will be rendered as a plain text input
	 */
    public static function customFieldDefinitions(): array;

	/**
	 * Load address data from your address service for the location string
	 *
	 * This function is where you can add=>$value any extra, custom fields that this
	 * application will keep track of.
	 *
	 * Your address system should have only one entry matching the location string.
	 * If there is not exactly one entry, the returned data will be empty
	 * This can be used to see if the location string is a valid address
	 * in your address system
	 *
	 * It's important to match $data fieldnames with Ticket fieldnames.
	 * When this data is given to a Ticket, any fields that have the same name as Ticket
	 * properties will update the appropriate Ticket property.
	 */
	public static function getLocationData(string $address): array;
	public static function searchAddresses(string $address): array;
}
