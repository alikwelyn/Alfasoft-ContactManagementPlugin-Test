<?php get_header(); ?>
<main role="main" class="container">
   <div class="row">
      <div class="col-md-12">
         <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Country Code</th>
                  <th>Number</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  global $wpdb;
                  
                  $cmPersons = "{$wpdb->prefix}contact_management_persons";
                  $cmContacts = "{$wpdb->prefix}contact_management_contacts";
                  
                  // The SQL query
                  $results = $wpdb->get_results("SELECT $cmPersons.id, $cmPersons.name, $cmPersons.email, $cmContacts.countryCode, $cmContacts.number FROM $cmPersons LEFT JOIN $cmContacts on $cmContacts.person_id = $cmPersons.id");
                  
                  // Loop though rows data
                  foreach( $results as $row ){
                  ?>
               <tr>
                  <td><?php echo $row->id ?></td>
                  <td><?php echo $row->name ?></td>
                  <td><?php echo $row->email ?></td>
                  <td><?php echo $row->countryCode ?></td>
                  <td><?php echo $row->number ?></td>
               </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
      <!-- /.blog-main -->
   </div>
   <!-- /.row -->
</main>
<!-- /.container -->
<?php get_footer(); ?>