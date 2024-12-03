<?php
include_once 'db_conn.php';

if(isset($_POST['func']) && !empty($_POST['func'])){ 
    switch($_POST['func']){ 
        case 'getCalender': 
            getCalender($_POST['year'],$_POST['month']); 
            break; 
        case 'getEvents': 
            getEvents($_POST['date']); 
            break;
        case 'getEvents': 
            getTotal($_POST['date']); 
            break; 
        default:
            break; 
    } 
} 

function getCalender($year = '', $month = ''){ 
    $dateYear = ($year != '')?$year:date("Y"); 
    $dateMonth = ($month != '')?$month:date("m"); 
    $date = $dateYear.'-'.$dateMonth.'-01'; 
    $currentMonthFirstDay = date("N",strtotime($date)); 
    $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear); 
    $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 1)?($totalDaysOfMonth):($totalDaysOfMonth + ($currentMonthFirstDay - 1)); 
    $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42; 
    
    $prevMonth = date("m", strtotime('-1 month', strtotime($date))); 
    $prevYear = date("Y", strtotime('-1 month', strtotime($date))); 
    $totalDaysOfMonth_Prev = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear); 

    ?> 
            <main class="calendar-contain"> 
            <h4>Total of Giving Birth this Month: <?php getTotal() ?></h4>
                <section class="title-bar"> 
                    <a href="javascript:void(0);" class="title-bar__prev" onclick="getCalendar('calendar_div','<?php echo date("Y",strtotime($date.' - 1 Month')); ?>','<?php echo date("m",strtotime($date.' - 1 Month')); ?>');"></a> 
                    <div class="title-bar__month"> 
                        <select class="month-dropdown form-select" style="width: 130px;"> 
                            <?php echo getMonthList($dateMonth); ?> 
                        </select>
                    </div> 
                    <div class="title-bar__year"> 
                        <select class="year-dropdown form-select" style="width: 130px;"> 
                            <?php echo getYearList($dateYear); ?> 
                        </select> 
                    </div> 
                    <a href="javascript:void(0);" class="title-bar__next" onclick="getCalendar('calendar_div','<?php echo date("Y",strtotime($date.' + 1 Month')); ?>','<?php echo date("m",strtotime($date.' + 1 Month')); ?>');"></a> 
                </section> 
                
                <aside class="calendar__sidebar"> 
                    <div id="event_list"> 
                        <?php echo getEvents(); ?> 
                    </div>
                </aside> 
                
                <section class="calendar__days"> 
                    <section class="calendar__top-bar"> 
                        <span class="top-bar__days">Mon</span> 
                        <span class="top-bar__days">Tue</span> 
                        <span class="top-bar__days">Wed</span> 
                        <span class="top-bar__days">Thu</span> 
                        <span class="top-bar__days">Fri</span> 
                        <span class="top-bar__days">Sat</span> 
                        <span class="top-bar__days">Sun</span> 
                    </section> 
                    
                    <?php  
                        $dayCount = 1; 
                        $eventNum = 0; 
                        
                        echo '<section class="calendar__week">';
                        for ($cb = 1; $cb <= $boxDisplay; $cb++) {
                            if (($cb >= $currentMonthFirstDay || $currentMonthFirstDay == 1) && $cb <= ($totalDaysOfMonthDisplay)) {
                                // Current date
                                $inputDate = $dateYear . '-' . $dateMonth . '-' . $dayCount;
                                $currentDate = date('Y-m-d', strtotime($inputDate));
                        
                                // Get number of events based on the current date
                                global $conn;
                                $result = $conn->query("SELECT patients.municipality FROM patients_details INNER JOIN patients ON patients_details.patients_id = patients.id WHERE kailan_ako_manganganak = '" . $currentDate . "'");
                                $eventNum = $result->num_rows;
                        
                                // Define date cell color
                                if (strtotime($currentDate) == strtotime(date("Y-m-d"))) {
                                    echo '
                                        <div class="calendar__day today" id="date-today" onclick="getEvents(\'' . $currentDate . '\');">
                                            <span class="calendar__date">' . $dayCount . '</span>
                                            <span class="calendar__task calendar__task--today">' . $eventNum . ' Total</span>
                                        </div>
                                    ';
                                } elseif ($eventNum > 0) {
                                    echo '
                                        <div class="calendar__day event" id="with-event" onclick="getEvents(\'' . $currentDate . '\');">
                                            <span class="calendar__date">' . $dayCount . '</span>
                                            <span class="calendar__task">' . $eventNum . ' Total</span>
                                        </div>
                                    ';
                                } else {
                                    echo '
                                        <div class="calendar__day no-event" onclick="getEvents(\'' . $currentDate . '\');">
                                            <span class="calendar__date">' . $dayCount . '</span>
                                            <span class="calendar__task">' . $eventNum . ' Total</span>
                                        </div>
                                    ';
                                }
                                $dayCount++;
                            } else {
                                if ($cb < $currentMonthFirstDay) {
                                    $inactiveCalendarDay = ((($totalDaysOfMonth_Prev - $currentMonthFirstDay) + 1) + $cb);
                                    $inactiveLabel = 'expired';
                                } else {
                                    $inactiveCalendarDay = ($cb - $totalDaysOfMonthDisplay);
                                    $inactiveLabel = 'Upcoming';
                                }
                                echo '
                                    <div class="calendar__day inactive">
                                        <span class="calendar__date">' . $inactiveCalendarDay . '</span>
                                        <span class="calendar__task">' . $inactiveLabel . '</span>
                                    </div>
                                ';
                            }
                            echo ($cb % 7 == 0 && $cb != $boxDisplay) ? '</section><section class="calendar__week">' : '';
                        }
                        echo '</section>';
                        

   
} 

function getMonthList($selected = ''){ 
    $options = ''; 
    for($i=1;$i<=12;$i++) 
    { 
        $value = ($i < 10)?'0'.$i:$i; 
        $selectedOpt = ($value == $selected)?'selected':''; 
        $options .= '<option value="'.$value.'" '.$selectedOpt.' >'.date("F", mktime(0, 0, 0, $i+1, 0, 0)).'</option>'; 
    } 
    return $options; 
} 

function getYearList($selected = ''){ 
    $yearInit = !empty($selected)?$selected:date("Y"); 
    $yearPrev = ($yearInit - 5); 
    $yearNext = ($yearInit + 5); 
    $options = ''; 
    for($i=$yearPrev;$i<=$yearNext;$i++){ 
        $selectedOpt = ($i == $selected)?'selected':''; 
        $options .= '<option value="'.$i.'" '.$selectedOpt.' >'.$i.'</option>'; 
    } 
    return $options; 
} 

function getEvents($date = '')
{
    $date = $date ? $date : date("Y-m-d");
    $currentMonth = date('m');

    $eventListHTML = '<h2 class="sidebar__heading">' . date("l", strtotime($date)) . ', ' . date("F d", strtotime($date)) . '</h2>';

    global $conn;

    $result = $conn->query("SELECT patients.barangay, patients.municipality, COUNT(*) AS count FROM patients_details INNER JOIN patients ON patients_details.patients_id = patients.id WHERE kailan_ako_manganganak = '" . $date . "' GROUP BY patients.municipality, patients.barangay");

    if ($result->num_rows > 0) {
        $eventListHTML .= '<ul class="sidebar__list">';
        $eventListHTML .= '<li class="sidebar__list-item">Total Births for this day:</li>';

        $currentMunicipality = null;

        while ($row = $result->fetch_assoc()) {
            $municipality = $row['municipality'];
            $barangay = $row['barangay'];
            $count = $row['count'];

            // If the municipality changes, start a new list
            if ($municipality != $currentMunicipality) {
                if ($currentMunicipality !== null) {
                    $eventListHTML .= '</ul>';
                }
                $eventListHTML .= '<li class="sidebar__list-item">' . $municipality . ':</li>';
                $eventListHTML .= '<ul class="sidebar__list">';
                $currentMunicipality = $municipality;
            }

            $eventListHTML .= '<li class="sidebar__list-item">' . $barangay . ' (Births: ' . $count . ') </li>';
        }

        // Close the last ul tag
        $eventListHTML .= '</ul>';
        $eventListHTML .= '</ul>';
    } else {
        $eventListHTML .= '<ul class="sidebar__list"><li class="sidebar__list-item"><h5>No record on the current day</h5></li></ul>';

        // Execute another query using the month
        $result = $conn->query("SELECT patients.barangay, patients.municipality, COUNT(*) AS count FROM patients_details INNER JOIN patients ON patients_details.patients_id = patients.id WHERE MONTH(kailan_ako_manganganak) = $currentMonth GROUP BY patients.municipality, patients.barangay");

        if ($result->num_rows > 0) {
            $eventListHTML .= '<ul class="sidebar__list">';
            $eventListHTML .= '<li class="sidebar__list-item">Total Births for this month:</li>';

            $currentMunicipality = null;

            while ($row = $result->fetch_assoc()) {
                $municipality = $row['municipality'];
                $barangay = $row['barangay'];
                $count = $row['count'];

                // If the municipality changes, start a new list
                if ($municipality != $currentMunicipality) {
                    if ($currentMunicipality !== null) {
                        $eventListHTML .= '</ul>';
                    }
                    $eventListHTML .= '<li class="sidebar__list-item">' . $municipality . ':</li>';
                    $eventListHTML .= '<ul class="sidebar__list">';
                    $currentMunicipality = $municipality;
                }

                $eventListHTML .= '<li class="sidebar__list-item">' . $barangay . ' (Births: ' . $count . ') </li>';
            }

            // Close the last ul tag
            $eventListHTML .= '</ul>';
            $eventListHTML .= '</ul>';
        } else {
            $eventListHTML .= '<ul class="sidebar__list"><li class="sidebar__list-item"><h5>No record for the current month</h5></li></ul>';
        }
    }

    echo $eventListHTML;
}


function getTotal($date = ''){ 
    $currentMonth = date('m');

    // Fetch total count based on the specific date 
    global $conn; 
    $result = $conn->query("SELECT COUNT(*) as total FROM patients_details WHERE MONTH(kailan_ako_manganganak) = $currentMonth");
    
    if ($result->num_rows > 0) { 
        $row = $result->fetch_assoc(); 
        $totalCount = $row['total'];

        // Output the span with the total count
        echo '<span id="total-birth">' . $totalCount . '</span>';
    }
}


?>