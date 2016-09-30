<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.9.16.
 * Time: 23.56
 */

namespace App\Services;


class DatabaseTesterService extends BaseService
{

    public function fillUsersTable() {

        $data = file_get_contents(__DIR__."/../../../tests/TEST_USER_DATA.json", true);
        $data = json_decode($data);

        foreach($data as $row) {

            $this->db->insert("matey_user", array(
                "first_name" => $row->first_name,
                "last_name" => $row->last_name,
                "email" => $row->email
            ));

        }

    }

    public function deleteAllUsersTable() {

        $this->db->executeUpdate("DELETE FROM matey_user");

    }

}