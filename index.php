
<?php  

// Connect to the Database 
include('config.php');

$insert = false;
$update = false;
$empty = false;
$delete = false;
$already_card = false;



if(isset($_GET['delete'])){
  $sno = $_GET['delete'];
  $delete = true;
  $sql = "DELETE FROM `cards` WHERE `sno` = $sno";
  $result = mysqli_query($conn, $sql);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset( $_POST['snoEdit'])){
      // Update the record
        $sno = $_POST["snoEdit"];
        $name = $_POST["nameEdit"];
        $id_no = $_POST["id_noEdit"];

      // Sql query to be executed
      $sql = "UPDATE `cards` SET `name` = '$name' , `id_no` = '$id_no' WHERE `cards`.`sno` = $sno";
      $result = mysqli_query($conn, $sql);
      if($result){
        $update = true;
    }
    else{
        echo "We could not update the record successfully";
    }
}
else{
    $name = $_POST["name"];
    $id_no = $_POST["id_no"];
    $grade = $_POST['grade'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $exp_date = $_POST['exp_date'];
    $phone = $_POST['phone'];

    if($name == '' || $id_no == ''){
        $empty = true;
    }
    else{
        //Check that Card no. is Already Registerd or not.
        $querry = mysqli_query($conn, "SELECT * FROM cards WHERE id_no= '$id_no' ");
        if(mysqli_num_rows($querry)>0)
        {
             $already_card = true;
        }
        else{


          // image upload 
          $uploaddir = 'assets/uploads/';
          $uploadfile = $uploaddir . basename($_FILES['image']['name']);

      
          if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
              
          } else {
              echo "Possible file upload attack!\n";
          }
  // Sql query to be executed
  $sql = "INSERT INTO `cards`(`name`, `id_no`, `email`, `phone`, `address`, `dob`, `exp_date`, `image`) VALUES ('$name','$id_no','$email]','$phone','$address','$dob','$exp_date','$uploadfile')"; 

  // $sql = "INSERT INTO `cards` (`name`, `id_no`) VALUES ('$name', '$id_no')";
  $result = mysqli_query($conn, $sql);



   
  if($result){ 
      $insert = true;
  }
  else{
      echo "The record was not inserted successfully because of this error ---> ". mysqli_error($conn);
  } 
}
}
}

 }
?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="icon" type="image/png" href="images/favicon.png"/>
  <title>Add New Student | Coding Cush</title>

</head>

<body>
 

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit This Card</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form method="POST">
          <div class="modal-body">
            <input type="hidden" name="snoEdit" id="snoEdit">
            <div class="form-group">
              <label for="name">Student Name</label>
              <input type="text" class="form-control" id="nameEdit" name="nameEdit">
            </div>

            <div class="form-group">
              <label for="desc">ID Card Number:</label>
              <input class="form-control" id="id_noEdit" name="id_noEdit" rows="3"></input>
            </div> 
          </div>
          <div class="modal-footer d-block mr-auto">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<!-- Navigation bar start  -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-image: linear-gradient(to right, rgb(0,300,255), rgb(93,4,217));">
  <a class="navbar-brand" href="#"><img src="assets/images/codingcush-logo.png" alt=""></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Dropdown
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#">Disabled</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>
<!-- Navigation bar end  -->

  <?php
  if($insert){
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your Card has been inserted successfully
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>
  <?php
  if($delete){
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your Card has been deleted successfully
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>
  <?php
  if($update){
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your Card has been updated successfully
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>
   <?php
  if($empty){
    echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
    <strong>Error!</strong> The Fields Cannot Be Empty! Please Give Some Values.
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>
     <?php
  if($already_card){
    echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
    <strong>Error!</strong> This Card is Already Added.
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>
  <div class="container my-4">
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  <i class="fa fa-plus"></i> Add New Card
  </button>
  <a href="id-card.php" class="btn btn-primary">
  <i class="fa fa-address-card"></i> Generate ID Card
</a>
</p>
<div class="collapse" id="collapseExample">
  <div class="card card-body">

    <form method="POST" enctype="multipart/form-data">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="inputCity">Student Name</label>
        <input type="text" name="name" class="form-control" id="inputCity">
      </div>
      <div class="form-group col-md-4">
        <label for="inputState">Class / Grade</label>
        <select name="grade" class="form-control">
          <option selected>Choose...</option>
          <option value="1st">1st</option>
          <option value="2nd">2nd</option>
          <option value="3rd">3rd</option>
          <option value="4th">4th</option>
          <option value="5th">5th</option>
          <option value="6th">6th</option>
          <option value="7th">7th</option>
        </select>
      </div>
      <div class="form-group col-md-2">
        <label for="inputZip">Date Of Birth</label>
        <input type="date" name="dob" class="form-control">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="inputCity">Address</label>
        <input type="text" name="address" class="form-control">
      </div>
      <div class="form-group col-md-4">
        <label for="inputState">Email Id</label>
        <input type="text" name="email" class="form-control">
      </div>
      <div class="form-group col-md-2">
        <label for="inputZip">Expire Date</label>
        <input type="date" name="exp_date" class="form-control">
      </div>
    </div>
      
      <div class="form-row">
        <div class="form-group col-md-3">
          <label for="id_no">ID Card No.</label>
          <input class="form-control" id="id_no" name="id_no" ></input>
        </div>
        <div class="form-group col-md-3">
          <label for="phone">Phone No.</label>
          <input class="form-control" id="phone" name="phone" ></input>
        </div>
        <div class="form-group col-md-4">
          <label for="photo">Photo</label>
          <input type="file" name="image" />
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Card</button>
    </form>
  </div>
</div>

  <div class="container my-4">


    <table class="table" id="myTable">
      <thead>
        <tr>
          <th scope="col">S.No</th>
          <th scope="col">Name</th>
          <th scope="col">ID Card No.</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          $sql = "SELECT * FROM `cards` order by 1 DESC";
          $result = mysqli_query($conn, $sql);
          $sno = 0;
          while($row = mysqli_fetch_assoc($result)){
            $sno = $sno + 1;
            echo "<tr>
            <th scope='row'>". $sno . "</th>
            <td>". $row['name'] . "</td>
            <td>". $row['id_no'] . "</td>
            <td> <button class='edit btn btn-sm btn-primary' id=".$row['sno'].">Edit</button> <button class='delete btn btn-sm btn-primary' id=d".$row['sno'].">Delete</button>  </td>
          </tr>";
        } 
          ?>


      </tbody>
    </table>
  </div>
  <hr>
  <a href="https://www.youtube.com/channel/UCerL4wDA74l1hmv8gnGJCWg" type="button" class="btn btn-primary btn-lg btn-block" target="_blank">Please Like And Subscribe</a>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
    integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
    crossorigin="anonymous"></script>
  <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#myTable').DataTable();

    });
  </script>
  <script>
    edits = document.getElementsByClassName('edit');
    Array.from(edits).forEach((element) => {
      element.addEventListener("click", (e) => {
        console.log("edit ");
        tr = e.target.parentNode.parentNode;
        name = tr.getElementsByTagName("td")[0].innerText;
        id_no = tr.getElementsByTagName("td")[1].innerText;
        console.log(name, id_no);
        nameEdit.value = name;
        id_noEdit.value = id_no;
        snoEdit.value = e.target.id;
        console.log(e.target.id)
        $('#editModal').modal('toggle');
      })
    })

    deletes = document.getElementsByClassName('delete');
    Array.from(deletes).forEach((element) => {
      element.addEventListener("click", (e) => {
        console.log("edit ");
        sno = e.target.id.substr(1);

        if (confirm("Are you sure you want to delete this note!")) {
          console.log("yes");
          window.location = `index.php?delete=${sno}`;
          // TODO: Create a form and use post request to submit a form
        }
        else {
          console.log("no");
        }
      })
    })
  </script>
</body>

</html>
