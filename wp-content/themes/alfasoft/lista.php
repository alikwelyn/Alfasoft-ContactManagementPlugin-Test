<html>
    <head>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="/wp-content/themes/2022-filho/style.css">
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
        <script>
            $(document).ready(function () {
                $('#example').DataTable();
            });
        </script>
        
        <div class="container">
            <h1>Public Contact Management</h1>
            <br>
            <hr>
            <br>
            <table id="example" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>CountryCode</th>
                    <th>Number</th>
                </tr>
            </thead>
                <tbody>
                    <?php
                        $sql = "select 
                                    wp_person.id,
                                    wp_person.name,
                                    wp_person.email_address,
                                    wp_contacts.country_code,
                                    wp_contacts.number
                                from wp_person
                                left join wp_contacts on wp_contacts.id_person = wp_person.id
                                where wp_person.status = 1";
                        
                        if($result = mysqli_query($conn, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                while($row = mysqli_fetch_array($result)){
                                    echo '<tr>
                                            <td>'.$row['id'].'</td>
                                            <td>'.$row['name'].'</td>
                                            <td>'.$row['email_address'].'</td>
                                            <td>'.$row['country_code'].'</td>
                                            <td>'.$row['number'].'</td>
                                         </tr>';
                                }
                                mysqli_free_result($result);
                            }
                        }
                        mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>