<?php

Class Login_Database extends CI_Model {

// Insert registration data in database
    public function registration_insert($data) {

// Query to check whether username already exist or not
        $condition = "username =" . "'" . $data['username'] . "'";
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {

// Query to insert data in database
            $this->db->insert('customers', $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
        } else {
            return false;
        }
    }

// Read data using username and password
    public function login($data) {

        $condition = "username =" . "'" . $data['username'] . "' AND " . "password =" . "'" . $data['password'] . "'";
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

// Read data from database to show data in admin page
    public function read_user_information($username) {

        $condition = "username =" . "'" . $username . "'";
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function fetchSearchResults($data){

        $searchStr = $data['searchStr'];
        $condition = $data['condition'];

        if($condition=='SearchByAuthor'){
            $sql = "select b.isbn, b.title,b.year, b.publisher, b.price, sum(s.number) as count\n"
                    ."from book b, stocks s , author a, writtenby w \n"
                    ."where lower(a.name) like '%$searchStr%' \n"
                    ."and b.isbn = s.isbn and a.ssn=w.ssn and w.isbn=b.isbn  and s.number>0 group by s.isbn \n";

            $query = $this->db->query($sql);

            if($query -> num_rows()>0){
                return $query->result();
            }
            else {
                return false;
            }

        } elseif ($condition=='SearchByTitle'){

            $sql = "select b.isbn, b.title,b.year, b.publisher, b.price, sum(s.number) as count \n"
                    ."from book b, stocks s where lower(b.title) like '%$searchStr%' \n"
                    ." and b.isbn = s.isbn  and s.number>0 group by s.isbn ";

            $query = $this->db->query($sql);

            if($query -> num_rows()>0){
                return $query->result();
            }
            else {
                return false;
            }

        }


    }

    public function insertContains($data){

        $this->db->insert('contains', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    public function updataStocks($username,$wCode,$isbn,$qty){

        $data = array('isbn'=>$isbn,
                        'warehousecode'=>$wCode,
                        'username'=>$username,
                        'number'=>$qty
                        );

        $this->db->insert('shippingorder', $data);
        if ($this->db->affected_rows() > 0) {

            $sql = "update stocks set number=number-'$qty' where isbn='$isbn' and warehousecode='$wCode'";

            $query = $this->db->query($sql);

            if ($this->db->affected_rows() > 0) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }


    }

    public function stockwise($isbn){

        $sql="select isbn,warehousecode,number from stocks where isbn='$isbn'";
        $query = $this->db->query($sql);

        if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
    }

    public function getWarehousecode($isbn, $qty){

        $sql = "select isbn,warehousecode, number from stocks    where isbn='$isbn'";
        $query = $this->db->query($sql);

        if($query -> num_rows()>0){

            return $query->result();
        }
        else {
            return false;
        }


    }

    public function returnCart($result){

        $isbn = $result['isbn'];
        $count = $result['count'];

        $sql = "select b.isbn as isbn, b.title as title, b.price as price from book b where b.isbn=" . "'" . $isbn . "'";

        $query = $this->db->query($sql);

        if($query -> num_rows()>0){

            return $query->result();
        }
        else {
            return false;
        }



    }

    public function createBasket($username){

        $this->db->query("insert into shoppingbasket(username) values('$username')");
        #$this->db->insert('shoppingbasket', $username);
        if ($this->db->affected_rows() > 0) {

            $condition = "username =" . "'" . $username . "' order by 1 desc";
            $this->db->select('basketId');
            $this->db->from('shoppingbasket');
            $this->db->where($condition);
            $this->db->limit(1);
            $query = $this->db->get();

            if ($query->num_rows() == 1) {
                return $query->result();
            } else {
                return false;
            }

        }
        else {
            return false;
        }

    }

}

?>

