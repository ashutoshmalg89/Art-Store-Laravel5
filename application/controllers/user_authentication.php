<?php


Class User_Authentication extends CI_Controller {

    public function __construct() {
        parent::__construct();

// Load form helper library
        $this->load->helper('form');

        $this->load->database();

// Load form validation library
        $this->load->library('form_validation');

// Load session library
        $this->load->library('session');

// Load database
        $this->load->model('login_database');

        $this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
        $this->output->set_header('Pragma: no-cache');
    }

// Show login page
    public function index() {
        $this->load->view('page1');
    }

    public function page2() {
        $this->load->view('page2');
    }

// Show registration page
    public function user_registration_show() {
        $this->load->view('page4');
    }

    public function displayCart(){
        if(isset($this->session->userdata['bucket'])){

            $result = $this->session->userdata['bucket'];
            $final_cart=array();

            foreach ($result as $key => $value){
                $data = array('isbn'=> $key,'count'=>$value);
                $cart = $this->login_database->returnCart($data);

                $final_price = $value * $cart[0]->price;
                $data = array('title'=> $cart[0]->title,
                                'price'=> $final_price,
                                'count'=>$value);

                $final_cart[$key]=$data;

            }
            $this->session->set_userdata('cart',$final_cart);

            $this->load->view('page3');


        } else{
            $msg = "Cart Empty<br><a href='page2'>Go Back To Shopping Page</a>";
            $this->session->set_userdata('cart_msg',$msg);
            $this->load->view('page3');

        }
    }

// Validate and store registration data in database
    public function new_user_registration() {

// Check validation for user input in SignUp form

            $data = array(
                'username' => $this->input->post('username'),
                'password' => md5($this->input->post('password')),
                'address' => $this->input->post('address'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email_value'),

            );
            $result = $this->login_database->registration_insert($data);
            if ($result == TRUE) {
                $data['message_display'] = 'Registration Successfully !';
                $this->load->view('page1', $data);
            } else {
                $data['message_display'] = 'Username already exist!';
                $this->load->view('page4', $data);
            }
        }

// Check for user login process
    public function user_login_process() {

            $data = array(
                'username' => $this->input->post('username'),
                'password' => md5($this->input->post('password'))
            );
            $result = $this->login_database->login($data);
            if ($result == TRUE) {

                $username = $this->input->post('username');
                $result = $this->login_database->read_user_information($username);
                if ($result != false) {
                    $session_data = array(
                        'username' => $result[0]->username,
                        'email' => $result[0]->email,
                    );
// Add user data in session
                    $this->session->set_userdata('logged_in', $session_data);
                    $this->load->view('page2');
                }
            } else {
                $data = array(
                    'error_message' => 'Invalid Username or Password'
                );
                $this->load->view('page1', $data);
            }
        }

    public function search(){

        $searchStr = $this->input->post('searchStr');
        $condition = $this->input->post('search');

        $data = array('searchStr'=>$searchStr,
                        'condition'=>$condition);
        $result = $this->login_database->fetchSearchResults($data);

        if($result != false){
            $data['searchResult']=$result;
            $this->session->set_userdata('searchResult',$data);
            $this->load->view('page2',$data);
        } else{

            $searchResult = array('message','No Books Found');
            $this->load->view('page2',$searchResult);
        }



    }

    public function addToCart(){

        $isbn = $this->input->post('addCart');
        #echo $isbn;
        $sum = 0;
        if(isset($this->session->userdata['bucket'])){

            $items = $this->session->userdata['bucket'];

            $flag = 1;
            foreach ($items as $key => $value){
                if($key == $isbn){
                    $items[$key]=$value+1;
                    $flag=0;
                }
            }
            if($flag==1){
                $items[$isbn]=1;
            }
         #   print_r($items);
            $this->session->set_userdata('bucket', $items);
          #  print_r($this->session->userdata['bucket']);
        }
        else{

            $data = array($isbn=>1);
           # print_r($data);
            $this->session->set_userdata('bucket', $data);
            #print_r($this->session->userdata['bucket']);

        }

        $this->load->view('page2');
    }


    public function splitStock($result,$qty, $username){

        foreach($result as $key => $value){

            $isbn = $value->isbn;
            $wCode =  $value->warehousecode;
            $stock = $value->number;

            if($stock >= $qty){

                $this->login_database->updataStocks($username,$wCode,$isbn,$qty);

                return true;

            } else{

                $this->login_database->updataStocks($username,$wCode,$isbn,$stock);

                $qty = $qty - $stock;
            }

        }
        return true;

    }

    public function buy(){

        $items = $this->session->userdata['cart'];
        $username = ($this->session->userdata['logged_in']['username']);
        $basketId = $this->login_database->createBasket($username);

        #print_r($basketId);
        if($basketId != false){
            $bid = $basketId[0]->basketId;


            foreach($items as $key => $value){

                $isbn = $key;
                $quantity = $value['count'];

                $data = array('isbn' => $key,
                              'basketId' => $bid,
                               'number' => $value['count']);

                $insertContains = $this->login_database->insertContains($data);

                if($insertContains != false){

                    $insertOrder = $this->login_database->getWarehousecode($isbn,$quantity);

                    if($insertOrder != false){
                        #print_r($insertOrder);
                        $splitData = $this->splitStock($insertOrder,$quantity,$username);

                        if($splitData != false){

                            $data['success']="Purchase Successful<br><a href='page2'>Continue Shopping</a>";



                        } else
                        {
                            $data['failure']="<h4>Error in Purchasing Try Again</h4>";

                            $this->load->view('page3',$data);
                        }

                    } else
                    {
                        $data['failure']="<h4>Error in Purchasing Try Again</h4>";

                        $this->load->view('page3',$data);
                    }



                } else
                {
                    $data['failure']="<h4>Error in Purchasing Try Again</h4>";

                    $this->load->view('page3',$data);
                }




            }


            $this->load->view('page3',$data);


        } else
        {
            $data['failure']="<h4>Error in Purchasing Try Again</h4>";

            $this->load->view('page3',$data);
        }



    }

// Logout from admin page
    public function logout() {

// Removing session data
        $sess_array = array(
            'username' => '',
            'searchResult' => '',
            'bucket' => '',
            'cart' => '',
            'cart_msg' => ''
        );
        $this->session->unset_userdata('logged_in', $sess_array);
        $this->session->unset_userdata('searchResult', $sess_array);
        $this->session->unset_userdata('bucket', $sess_array);
        $this->session->unset_userdata('cart', $sess_array);
        $this->session->unset_userdata('cart_msg', $sess_array);
        $data['message_display'] = 'Successfully Logout';
        $this->load->view('page1', $data);
    }

}

?>

