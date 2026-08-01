<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Custom_menu_model extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_clients(): array {

        return $this->db
            ->select('userid, company, vat, phonenumber, country, city, zip, state, address, website, datecreated, active, leadid')
            ->from(db_prefix() . 'clients')
            ->get()
            ->result_array();
  
}


public function get_customers_groups(): array
    {
        return $this->db
            ->select('id, group_name')
            ->from(db_prefix() . 'customer_groups')
            ->get()
            ->result_array();
    }

    public function get_countries(): array
    {
        return $this->db
            ->select('id, short_name')
            ->from(db_prefix() . 'countries')
            ->get()
            ->result_array();
    }


}

