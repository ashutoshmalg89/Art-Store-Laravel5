<!DOCTYPE html>
<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
} else {
    header("location: login");
}
?>
<html>
<head>

<style> 
.flex-container {
    display: -webkit-flex;
    display: flex;  
    -webkit-flex-flow: row wrap;
    flex-flow: row wrap;
    text-align: center;
}

.flex-container > * {
    padding: 15px;
    -webkit-flex: 1 100%;
    flex: 1 100%;
}

header {background: white;color:black;}
footer {background: #aaa;color:white;}

.nav ul {
    list-style-type: none;
  padding: 0;
}

.search{
	border: 1px solid black;
	padding: 50px;
}  

.searchresult{
	width: 75%;
	float: left;
	padding: 15px;
	border: 1px solid black;
}

.logout{
	width: 25%;
	float: left;
	padding: 15px;
}
.cart{
	width: 25%;
	float: right;
	padding: 15px;
	border: 1px solid black;
}
</style>


</head>
<body>

<div class="flex-container">
<header>
  <h1> Welcome <?php echo $username; ?></h1>
  
  <div class="logout">
  	<button onclick="location.href='logout';">Logout</button>
  </div>
</header>

<div class="search">
    <?php
    if(isset($success)){
        $this->session->unset_userdata('cart_msg', '');
        $this->session->unset_userdata('cart', '');
        $this->session->unset_userdata('searchResult', '');
        $this->session->unset_userdata('bucket', '');
        echo $success;

    } else if(isset($failure)){

        echo $failure;

    } else
    if(isset($this->session->userdata['cart'])){
        $data = $this->session->userdata['cart'];
        echo "<div id='displayCart' align='center'>";
        $msg = "<b>Shopping Cart</b><br>
            <a href='page2'>Add More Books</a>
 			<table border='1'>
 			<tr><th>ISBN</th><th>TITLE</th><th>QUANTITY</th><th>PRICE</th></tr>";
        $sum = 0.0;
        foreach ($data as $key=>$value){

            $isbn = $key;
            $title = $value['title'];
            $qty = $value['count'];
            $price = $value['price'];

            $total_price = $qty * $price;

            $msg.="<tr><td>".$key."</td><td>".$title."</td><td>".$qty."</td><td>".$total_price."</td></tr>";
            $sum = $sum + $total_price;


        }
        $openForm = form_open('user_authentication/buy');
        $closeForm = form_close();

        $msg.="<tr><td colspan='3'> Total :</td><td>".$sum." USD</td></tr>
			</table>
			".$openForm."
			<input type='submit' value='Buy'>
			".$closeForm."";
        echo $msg;
        echo "</div>";
    }
    else if($this->session->userdata['cart_msg']){

        print_r($this->session->userdata['cart_msg']);

    }

    ?>
</div>
