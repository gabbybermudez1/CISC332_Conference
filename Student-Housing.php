<?php
    $servername = "localhost";
    $username = "root";
    $conn = new PDO("mysql:host=$servername;dbname=conference_db", $username, "");
    $sql="select room_number from rooms";
    $rm = "null";
    $temp = "-- Select Room Number --";
    try {
        $stmt=$conn->prepare($sql);
        $stmt->execute();
        $rows=$stmt->fetchAll();
    }
    catch(Exception $e){
        echo "Connection failed: " . $e->getMessage();
        die;
    }
    if(isset($_POST['rm_num'])){
        $rm = $_POST['rm_num'];
        $temp = $rm;
    }
?>


<!DOCTYPE html>
<html>
<!-- Student Housing -->
    <head>
        <link rel="stylesheet" href="Student-Housing.css" />
        <title> Student Housing </title>
    </head>    
    <a href="Home Page.php">Home</a>
    <form method = "post">
    <body>
        <h1> Student Housing </h1>
        <!--$rm = $_POST['room_number'];?>-->
        <select name = 'rm_num' id = 'rm_num' onchange = "this.form.submit();">
        <!--<input type="submit" name = "submit" value = "Get selected value"/>   -->
            
            <?php
                //echo "<option>". $temp.  "</option>";
                echo "<option> -- Select Room Number -- </option>";   
                echo "<option> All </option>";    
                foreach ($rows as $output) {                                    
                    echo " <option> " . $output["room_number"] . " </option> ";
                };
            ?>    
            </select>
        <table>
            <tr> 
                <th> Room </th> 
                <th> Students </th> 
            </tr>
            <?php 
            if(isset($_POST['rm_num'])){
                if ($rm == "All"){
                    $sql2="select * from students";
                }
                else{
                    $sql2="select * from students where room_number = '$rm'";
                }
                echo "Room Number: ". $rm;
                try {
                    $stmt2=$conn->prepare($sql2);
                    $stmt2->execute();
                    $rows2=$stmt2->fetchAll();
                    foreach ($rows2 as $output){
                        echo "<tr>";
                        echo "<td> ".  $output['room_number']. " </td> ";
                        echo "<td> ".  $output['first_name']. " " .$output['last_name'] . " </td> ";
                        echo "</tr>";
                    }
                }
                catch(Exception $e){
                    echo "Connection failed: " . $e->getMessage();
                    die;
                } 
            }
            ?>
        
        <table>
        
    </body>
    </form>

</html>


