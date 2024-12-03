<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];
$admin_id = $_SESSION['usersid'];

if (isset($_POST['searchTerm'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);

    $sql = "SELECT patients.*, COUNT(prenatal_records.patients_id) AS prenatal_records_count, MAX(prenatal_records.date) AS latest_record_date
        FROM patients
        LEFT JOIN prenatal_records ON prenatal_records.patients_id = patients.id
        WHERE patients.staff_id = $admin_id AND (patients.fname LIKE '%$searchTerm%' OR patients.lname LIKE '%$searchTerm%' OR patients.barangay LIKE '%$searchTerm%')
        GROUP BY patients.id DESC";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        foreach ($patients as $key => $patient) {
            $id = $patient['id'];
            $fname = $patient['fname'];
            $lname = $patient['lname'];
            $address = $patient['barangay'];
            $records = $patient['prenatal_records_count'];
            $latest_record_date = $patient['latest_record_date'];

            if (!empty($latest_record_date)) {
                $latest_record_dateTimestamp = strtotime($latest_record_date);
                $formattedlatest_record_date = date('M jS, Y', $latest_record_dateTimestamp);
            } else {
                $formattedlatest_record_date = "No Records";
            }
            ?>

            <tr>
                <th scope='row'><?php echo $key + 1?></th>
                <td><?php echo $fname?></td>
                <td><?php echo $lname?></td>
                <td><?php echo $address?></td>
                <td><?php echo $records?></td>
                <td><?php echo $formattedlatest_record_date?></td>
                <td>
                    <a class="btn btn-primary table-btn createNewPrenatalRecord" data-toggle="tooltip" data-placement="left" title="Add Record" href="view_prenatal.php?id=<?php echo $id?>&record=<?php echo $newrecords?>" data-patient-id="<?php echo $id?>" role="button"><i class="fi fi-rr-add-folder"></i></a>
                    <button type="button" class="btn btn-primary table-btn viewPatient" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Edit (View)"><i class="fi fi-rs-pencil"></i></button>
                    <button type="button" class="btn btn-primary table-btn deletePatient" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fi fi-rs-trash"></i></button>
                </td>
            </tr>

            <?php
        }
    } else {
        echo "Error executing query: " . mysqli_error($conn);
    }
}
?>
