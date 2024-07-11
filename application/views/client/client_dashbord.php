<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
      #intro{
       
        background-color: lightgray;
       /* padding: 5%;*/
        width: 1000px;
  
        border: 5px solid gray;
         margin-left: 25%;
 
      }
      .btn{
        margin: 5%;
        height: 100px;
      }
      body{
         margin-top: 12%;
       
      }
</style>
</head>
<body >

           <center>
             <h2>Client Information</h2>
        <div class=" col-lg-12" align="center" id="intro">
        <table >
          <tr>
            <td> <a href="<?php echo base_url('Client_info/personal_info') ?>"> <input type="button" name="btn_client_info" class="btn btn-lg btn-success" value="Personal Information"> </a></td>
            <td rowspan="2" align="center"><img src="<?php echo base_url('assets/theme/dist/img/bg2.jpg'); ?>"> </td>
            <td><a href="<?php echo base_url('Client_info/bank_details') ?>"> <input type="button" name="btn_bank_details" class="btn btn-lg btn-success" value="Bank Details"></a> </td>
          </tr>
          <tr>
            <td><a href="<?php echo base_url('Client_info/covering_letter') ?>"><input type="button" name="btn_authorization" class="btn btn-lg btn-success" value="Covering Letter"></a></td>
            <td><a href="<?php echo base_url('Client_info/capital_details') ?>"><input type="button" name="btn_capital" class="btn btn-lg btn-success" value="capital structure"></a></td>
          </tr>
          <tr>
            <td></td>
            <td align="center"><input type="button" name="btn_other_details" class="btn btn-lg btn-success" value="Other Details"></td>
            <td></td>
          </tr>
        </table>

      </div>
</body>
</html>