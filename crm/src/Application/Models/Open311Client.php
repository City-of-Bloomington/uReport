<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\Database;

class Open311Client
{
    /**
     * Translates an Open311 POST into a CRM POST
     *
     * A valid Client api_key is required in the POST
     *
     * http://wiki.open311.org/GeoReport_v2#POST_Service_Request
     * Open311 POST Service Request: defines what key/values should
     * be posted when adding a service request.
     * However, we're just going to take the Open311 POST and
     * hand it off to Ticket::handleAdd().  So, we have to
     * translate all the Open311 POST parameters into the
     * POST parameters that Ticket::handleAdd() expects.
     *
     * Media should not need any special handling here,
     * since both CRM and Open311 use "media" as the fieldname
     *
     * @param  array     $open311Post  The raw POST from the client
     * @return array                   A POST for Ticket::handleAdd()
     * @throws \Exception
     */
    public static function translatePostArray(array $open311Post): array
    {
        // Make sure we have a valid api_key
        if (!empty($open311Post['api_key'])) { $client = Client::loadByApiKey($open311Post['api_key']); }
        else { throw new \Exception('clients/unknown'); }

        $ticketPost = [
            'client_id'       => $client->getId(),
            'contactMethod_id'=> $client->getContactMethod_id()
        ];

        // The lookup table for keyname translations
        // 'open311Post_key'=>'ticketPost_key'
        $fields = [
            // Ticket Fields
            'service_code'  =>'category_id',
            'lat'           =>'latitude',
            'long'          =>'longitude',
            'address_string'=>'location',
            // Issue Fields
            'description'   =>'description',
            'attribute'     =>'customFields'
        ];
        foreach ($fields as $open311Field=>$crmField) {
            if (!empty($open311Post[$open311Field])) {
                $ticketPost[$crmField] = $open311Post[$open311Field];
            }
        }

        $crmPersonFields = self::personParamsFromOpen311($open311Post);
        if ($crmPersonFields) {
            $person = self::findPerson($crmPersonFields);
            if (!$person) {
                $person = self::createNewPersonRecord($crmPersonFields);
            }
            $ticketPost['reportedByPerson_id'] = $person->getId();
        }

        return $ticketPost;
    }

    /**
     * Returns an array of person data using the CRM fieldnames
     *
     * Converts an array of person data from Open311 fieldnames to
     * the fieldnames used by the CRM.
     *
     * @param  array $post  Person data array using Open311 fieldnames
     * @return array        Person data array using CRM fieldnames
     */
    private static function personParamsFromOpen311(array $post): array
    {
        //   Open311    =>  CRM
        $fields = [
            'first_name'=> 'firstname',
            'last_name' => 'lastname',
            'email'     => 'email',
            'phone'     => 'phone'
        ];

        $person = [];
        foreach ($fields as $o => $c) {
            if (!empty($post[$o])) { $person[$c] = $post[$o]; }
        }
        return $person;
    }

    private static function createNewPersonRecord(array $post): Person
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        $person = new Person();
        try {
            $person->handleUpdate($post);
            $person->save();

            if (!empty($post['email'])) {
                $email = new Email();
                $email->setPerson($person);
                $email->setEmail($post['email']);
                $email->save();
            }

            if (!empty($post['phone'])) {
                $phone = new Phone();
                $phone->setPerson($person);
                $phone->setNumber($post['phone']);
                $phone->save();
            }
        }
        catch (\Exception $e) {
            $pdo->rollBack();
            throw($e);
        }
        $pdo->commit();
        return $person;
    }

    private static function findPerson(array $search): ?Person
    {
        $table = new PersonTable();
        $list  = $table->find($search);
        return $list['total'] == 1 ? $list['rows'][0] : null;
    }
}
