<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
      #intro {
        /*background-image: url("<?php echo base_url('assets/img/bg.jpg'); ?>");*/
        height: 100vh;
        background-size: 100% 100%;
        background-color: white;
        /*width: 100;*/
      }
</style>
</head>
<body id="intro">

<div class="container">
 <center><a href="http://moonsez.com/" class="logo">
        <img src="http://moonsez.com/img/moonsez.png" alt="Moonsez Consultants Pvt.Ltd." width="235" height="72">
        <!-- <h1>Moonsez</h1> -->
      </a></center>
      <hr>
  <center><h3>Personal Information</h3> </center>                                                                                     
  <div class="table-responsive table-scrollable" style="background-color: white;">          
  <table class="table table-bordered table-striped border-primary">
  	
    <thead>
      <tr>
        <th>#</th>
        <th>Details</th>
        <th>To be filled by client</th>
        <th>Sample Document</th>
        <th>Upload</th>
       
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Name of the Authorized signatory who will sign the application</td>
        <td><input type="text" name="filled_input" id="filled_input" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
        	<input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
        	<input type="file" name="upload_doc" hide>
        </td>
        
      </tr>
       <tr>
        <td>2</td>
        <td>Father's name of Authorized signatory</td>
        <td><input type="text" name="father_nm_sign" id="father_nm_sign" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
        	<input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
        	<input type="file" name="upload_doc" hide>
        </td>
      </tr>

       <tr>
        <td>3</td>
        <td>Age</td>
        <td><input type="text" name="age" id="age" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
        	<input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
        	<input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>4</td>
        <td>Residential address</td>
        <td><input type="text" name="address" id="address" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>5</td>
        <td>Designation</td>
        <td><input type="text" name="designation" id="designation" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>6</td>
        <td>Mobile No.</td>
        <td><input type="number" name="mob_no" id="mob_no" value="" numbaraonly></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>7</td>
        <td>Email id </td>
        <td><input type="text" name="email_id" id="email_id" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>8</td>
        <td>Website address</td>
        <td><input type="text" name="web_addr" id="web_addr" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>


      <tr>
        <td>9</td>
        <td>Landline No.</td>
        <td><input type="text" name="landline_no" id="landline_no" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>10</td>
        <td>Fax No.</td>
        <td><input type="text" name="fax_no" id="fax_no" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>11</td>
        <td>PAN No. of the LLP/Co/Partnership Firm</td>
        <td><input type="text" name="pan_no" id="pan_no" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
        <td>12</td>
        <td>IEC Code of the LLP/Co/Partnership Firm</td>
        <td><input type="text" name="iec_code" id="iec_code" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td>
        <td><input type="checkbox" name="upload_chk">
          <input type="text" name="upload_doc_nm" id="upload_doc_nm" value="" hidden>
          <input type="file" name="upload_doc" hide>
        </td>        
      </tr>

      <tr>
      
        <td colspan="4"></td>
        <!-- <td>IEC Code of the LLP/Co/Partnership Firm</td>
        <td><input type="text" name="iec_code" id="iec_code" value=""></td>
        <td><a href="Leave Policy 2022.pdf">Download PDF file</a></td> -->
        <td ><input type="submit" name="btnsave" class="btn btn-primary">
          <a href="<?php echo base_url('Client_info') ?>"><input type="button" name="btnback" value="Back" class="btn btn-warning"></a>
        
        </td>        
      </tr>



    </tbody>
  </table>
  </div>
</div>

</body>
</html>
