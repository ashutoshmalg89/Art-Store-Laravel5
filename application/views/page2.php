<html xmlns="http://www.w3.org/1999/html">
<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
} else {
    header("location: login");
}
?>
<head>

    <title>Shopping Page</title>
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

        header {background: white;color: black;}
        footer {background: #aaa;color:white;}

        .nav ul {
            list-style-type: none;
            padding: 0;
        }

        #search{
            padding: 25px;
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
        .Cart{
            width: 25%;
            float: right;
            padding: 15px;
        }
    </style>


</head>
<body>
<div class="flex-container">
<header>
        <?php
        echo "Hello <b id='welcome'><i>" . $username . "</i> !</b>";
        echo "<br/>";
        echo "<br/>";
        echo "Welcome to Shopping Page";
        echo "<br/>";
        ?>
<div class="head">
    <div class="logout">
        <button onclick="location.href='logout';">Logout</button>
    </div>
    <div class="Cart">
        <?php echo form_open('user_authentication/displayCart');?>
        <table>

            <tr><td>No of Items :</td>
                <td>
                    <?php
                    $sum = 0;
                    if(isset($this->session->userdata['bucket'])){

                        $items = $this->session->userdata['bucket'];
                        foreach ($items as $key => $value){
                            $sum = $sum + $value;

                        }
                        echo $sum;
                    }else{

                        echo $sum;
                    }

                    ?>
                </td></tr>
            <tr><td colspan="2"><input type="submit" value="ShoppingBasket"></td> </tr>
        </table>
        <?php echo form_close(); ?>
    </div>
</div>
</header>
<div id="search">
    <h2>Search the Book Here</h2>
    <?php echo form_open('user_authentication/search'); ?>
      <label>Search :</label>
    <input type="text" name="searchStr"  placeholder="Enter Text to Search"/><br /><br />

    <input type="submit" value="SearchByAuthor" name="search"/>
    <input type="submit" value="SearchByTitle" name="search"/><br />
    <?php echo form_close(); ?>

</div>
<hr/>
<div id="searchResult">
    <?php
    $openForm = form_open('user_authentication/addToCart');
    $closeForm = form_close();
    if(isset($searchResult)){


        $output =  "<table border='1'>
						<tr><th>ISBN</th><th>Title</th><th>Year</th><th>Price</th><th>Publisher</th><th>Qty</th></tr>";

        foreach($searchResult as $row){

            $output.= "".$openForm."
                    <tr>
                    <td>".$row->isbn."</td>
                    <td>".$row->title."</td>
                    <td>".$row->year."</td>
                    <td>".$row->price."</td>
                    <td>".$row->publisher."</td>
                    <td>".$row->count."</td><td><button type='submit' name='addCart' value=".$row->isbn.">Add To Cart</button></td>
                    </tr>".$closeForm." ";

        }
        $output.= "</table>";
        echo $output;
    } else
    if (isset($this->session->userdata['searchResult'])){
        $openForm = form_open('user_authentication/addToCart');
        $closeForm = form_close();
        $searchResult = $this->session->userdata['searchResult']['searchResult'];
        $output =  "<table border='1'>
						<tr><th>ISBN</th><th>Title</th><th>Year</th><th>Price</th><th>Publisher</th><th>Qty</th></tr>";

        foreach($searchResult as $row){

            $output.= " ".$openForm."
                    <tr>
                    <td>".$row->isbn."</td>
                    <td>".$row->title."</td>
                    <td>".$row->year."</td>
                    <td>".$row->price."</td>
                    <td>".$row->publisher."</td>
                    <td>".$row->count."</td><td><button type='submit' name='addCart' id='addCart' onclick='validate()' value=".$row->isbn.">Add To Cart</button></td>
                    </tr>".$closeForm."";

        }
        $output.= "</table>";
        echo $output;

    }

    ?>
</div>
</body>
</html>