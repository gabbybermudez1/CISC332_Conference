<?php 

function getCompanies($someArray){
    $newVar = "[";
    foreach($someArray as $rows){
        $newVar = $newVar . "'".  $rows['company']. "'".", "; 
    }
    $newVar = $newVar . "]";
    return $newVar;
}

if (isset($_POST['submit-info'])){
    if ( (empty($_POST['company-input']) or ( empty($_POST['rank-input']) )) and empty($_POST['submit-delete'])  ){
        echo "<script> incorrectInput = true;</script>";
    }

    else{
        $servername = "localhost";
        $databaseName = "conference_db";
        $username = "root";

        try{
            $db = new PDO("mysql:host=$servername;dbname=$databaseName", $username);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // variables to send to the database
            $company = $_POST['company-input'];
            $sponsor_rank = $_POST['rank-input'];

            $addSql = "INSERT INTO companies VALUES(" . "'$company'," . "'$sponsor_rank'," . "0);";
            $stmt = $db->prepare($addSql);
            $stmt->execute();
            echo "<script> incorrectInput = false; </script>";
        }

        catch(PDOException $e){
            $errorMessage = $e->getMessage();
            echo "<script>var connectionFailed = true;</script>";
        }
    }
}


// If we've deleted a company from the list
if (isset($_POST['submit-delete'])){

    $servername = "localhost";
    $databaseName = "conference_db";
    $username = "root";

    try{
        $db = new PDO("mysql:host=$servername;dbname=$databaseName", $username);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // variables to send to the database
        $companyToDelete = $_POST['delete-company'];
        $deleteSql = "DELETE FROM companies WHERE company='$companyToDelete'";
        $stmt = $db->prepare($deleteSql);
        $stmt->execute();
        echo "<script> var deleteCompany= true; </script>";
    }

    catch(PDOException $e){
        $errorMessage = $e->getMessage();
        echo "<script>var connectionFailed = true;</script>";
    }
    
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="Companies.css" />
    <link rel="stylesheet" href="./tingle/src/tingle.css">
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
    <a href="Home Page.php">Home</a>
    <title> Companies </title>
</head>
<body>
    <h1>List of Companies </h1>
    <img src='./assets/add_icon.png'  class='add-icon' onclick="formModal.open()" >
    <img src='./assets/subtract-icon.png' style="height:3%; width:3%;" onclick="deleteModal.open()" >
    <table>
        <tr>
            <th> Company Name</th>
            <th> Rank </th>
        </tr> 
        <?php 
            $servername = "localhost";
            $databaseName = "conference_db";
            $username = "root";


            try{
                $db = new PDO("mysql:host=$servername;dbname=$databaseName", $username);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            // Set up a connection to the mysql database
                $sql = "SELECT * FROM companies ORDER BY company";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $data  = $stmt->fetchAll();
                foreach($data as $row){
                    echo "<tr><form method='GET'>";
                    echo "<td><a name='company-name' onclick='this.form.submit()' href='Company.php?company=" . $row['company'] . "'>" . $row["company"] ."</a></td>";
                    echo "<td>" . $row["sponsor_rank"] . "</td>";
                    echo "</form></tr>";
                }
                $companies = getCompanies($data);
                echo "<script> var companies=" . $companies . "</script>";
            }

            catch(PDOException $e){
                $errorMessage = $e->getMessage();
                echo "<script>var connectionFailed = true;</script>";
            }

            ?>
    </table>
</body>
</html>


<script src="./tingle/src/tingle.js"> 
</script>

<script>

var formModalContent =`
    <form method='POST' class='modal-form' action='Companies.php'>
        <div class='modal-content'> 
            <h3 style='align-self:center;'> Add Event </h3>
            <label> Company </label>
            <input type="text" name="company-input"  />
            <label> Rank </label>
            <select  name="rank-input" >
                <option> Bronze </option>
                <option> Silver </option>
                <option> Gold </option>
                <option> Platinum </option>
            <input type='submit' class='submit-info' name='submit-info' style='display:none;'>
        </div>
    </form>
` // end string


function  getModalOptions(){
    var modalOptions = "<select name='delete-company'>"
    for(var i=0; i<companies.length; i++){
        modalOptions = modalOptions + "<option>" + companies[i] + "</option>"
    }
    modalOptions = modalOptions + "</select>"
    modalOptions = modalOptions + "<input type='submit' class='submit-delete' name='submit-delete' style='display:none;'>"
    return modalOptions;
}
var deleteModalContent =`
    <form method='POST' class='modal-form' action='Companies.php'>
        <div class='modal-content'> 
            <h3 style='align-self:center;'> Delete Event</h3>
            <label> Company </label>
        ` + getModalOptions() + `
        </div>
    </form>
` // end string


// Form modal 
var formModal = new tingle.modal({
    footer: true,
    stickyFooter: false,
    closeMethods: ['overlay', 'button', 'escape'],
    closeLabel: "Close",
    cssClass: ['custom-class-1', 'custom-class-2']
});
formModal.setContent(formModalContent);
formModal.addFooterBtn('Submit', 'tingle-btn tingle-btn--primary', function() {
    document.querySelector('.submit-info').click();
});


// Form modal 
var deleteModal = new tingle.modal({
    footer: true,
    stickyFooter: false,
    closeMethods: ['overlay', 'button', 'escape'],
    closeLabel: "Close",
    cssClass: ['custom-class-1', 'custom-class-2']
});
deleteModal.setContent(deleteModalContent);
deleteModal.addFooterBtn('Submit', 'tingle-btn tingle-btn--danger', function() {
    document.querySelector('.submit-delete').click();
});

var errorModal = new tingle.modal({
    footer: true,
    stickyFooter: false,
    closeMethods: ['overlay', 'button', 'escape'],
    closeLabel: "Close",
    cssClass: ['custom-class-1', 'custom-class-2'],
});
errorModal.setContent('<h1>We encountered an error connecting to the database </h1>');
errorModal.addFooterBtn('Refresh', 'tingle-btn tingle-btn--danger', function() {
    location.reload();
});

if (typeof connectionFailed !== 'undefined'){
    if(connectionFailed){
    errorModal.open()
    }
}

if (typeof deleteCompany !== 'undefined'){
    if(deleteCompany){
        alert("Company was successfully deleted from the database");
    }
}


if(typeof incorrectInput !== 'undefined'){
    if (incorrectInput){
        alert('You submitted incomplete or incorrect information. Your sql was not sent to the database');
     }

     else{
         alert("SQL successfully sent");
     }
}


</script>




