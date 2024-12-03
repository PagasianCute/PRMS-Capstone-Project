<?php
include_once '../../../db/db_conn.php';

session_start();

$fclt_id = $_SESSION['fcltid'];

if (isset($_POST['searchTerm'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);

    $sql = "SELECT patients.*, COUNT(prenatal_records.patients_id) AS prenatal_records_count, MAX(prenatal_records.date) AS latest_record_date
        FROM patients
        LEFT JOIN prenatal_records ON prenatal_records.patients_id = patients.id
        WHERE patients.fclt_id = $fclt_id AND (patients.fname LIKE '%$searchTerm%' OR patients.lname LIKE '%$searchTerm%' OR patients.barangay LIKE '%$searchTerm%')
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
                    <a class="btn btn-primary table-btn" data-toggle="tooltip" data-placement="left" title="View Patient" href="view_patient.php?id=<?php echo $id?>" data-patient-id="<?php echo $id?>" role="button">
                        <i class="fi fi-rr-eye"></i>
                    </a>
                    <button type="button" class="btn btn-primary table-btn deletePatient" value="<?php echo $id?>" data-toggle="tooltip" data-placement="left" title="Delete">
                        <i class="fi fi-rs-trash"></i>
                    </button>
                </td>
            </tr>

            <?php
        }
    } else {
        echo "Error executing query: " . mysqli_error($conn);
    }
}
?>
