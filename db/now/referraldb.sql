-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2024 at 01:01 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `referraldb`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `create_facility` (IN `new_fclt_name` VARCHAR(255), IN `new_fclt_ref_id` VARCHAR(255), IN `new_fclt_type` VARCHAR(255), IN `new_img_url` VARCHAR(255), IN `new_fclt_contact` VARCHAR(255), IN `new_fclt_status` VARCHAR(255), IN `new_verification` VARCHAR(255), IN `new_region` VARCHAR(255), IN `new_province` VARCHAR(255), IN `new_municipality` VARCHAR(255), IN `new_region_code` INT)   BEGIN
    INSERT INTO facilities (
        fclt_name,
        fclt_ref_id,
        fclt_type,
        img_url,
        fclt_contact,
        fclt_status,
        verification,
        region_code,
        region,
        province,
        municipality
    ) VALUES (
        new_fclt_name,
        new_fclt_ref_id,
        new_fclt_type,
        new_img_url,
        new_fclt_contact,
        new_fclt_status,
        new_verification,
        new_region_code,
        new_region,
        new_province,
        new_municipality
    );
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `create_referral` (IN `new_name` VARCHAR(255), IN `new_age` VARCHAR(255), IN `new_sex` VARCHAR(255), IN `new_bdate` VARCHAR(255), IN `new_address` VARCHAR(255), IN `new_admitting_dx` VARCHAR(255), IN `new_rtpcr` VARCHAR(255), IN `new_antigen` VARCHAR(255), IN `new_clinical_ssx` VARCHAR(255), IN `new_exposure_to_covid` VARCHAR(255), IN `new_temp` VARCHAR(255), IN `new_hr` VARCHAR(255), IN `new_resp` VARCHAR(255), IN `new_bp` VARCHAR(255), IN `new_O2sat` VARCHAR(255), IN `new_O2aided` VARCHAR(255), IN `new_procedures_need` VARCHAR(255), IN `new_fh` VARCHAR(255), IN `new_ie` VARCHAR(255), IN `new_fht` VARCHAR(255), IN `new_lmp` VARCHAR(255), IN `new_edc` VARCHAR(255), IN `new_aog` VARCHAR(255), IN `new_utz` VARCHAR(255), IN `new_utz_aog` VARCHAR(255), IN `new_edd` VARCHAR(255), IN `new_enterpretation` VARCHAR(255), IN `new_diagnostic_test` VARCHAR(255), IN `new_time` VARCHAR(255), IN `new_date` VARCHAR(255), IN `new_fclt_id` INT, IN `new_referred_hospital` INT, IN `new_patients_id` INT, IN `new_referral_reason` VARCHAR(500), IN `new_icon` TEXT)   BEGIN
    INSERT INTO referral_forms (
        name, age, sex, bdate, address, admitting_dx, rtpcr, antigen, clinical_ssx, exposure_to_covid, temp, hr, resp, bp, O2sat, O2aided, procedures_need, fh, ie, fht, lmp, edc, aog, utz, utz_aog, edd, enterpretation, diagnostic_test, referral_reason
    ) VALUES (
        new_name, new_age, new_sex, new_bdate, new_address, new_admitting_dx, new_rtpcr, new_antigen, new_clinical_ssx, new_exposure_to_covid, new_temp, new_hr, new_resp, new_bp, new_O2sat, new_O2aided, new_procedures_need, new_fh, new_ie, new_fht, new_lmp, new_edc, new_aog, new_utz, new_utz_aog, new_edd, new_enterpretation, new_diagnostic_test, new_referral_reason
    );
    
    SET @last_id = LAST_INSERT_ID();
    
    INSERT INTO referral_records (
        fclt_id,
        rfrrl_id,
        patients_id,
        date,
        time,
        referred_hospital,
        status
    ) VALUES (
        new_fclt_id,
        @last_id,
        new_patients_id,
        new_date,
        new_time,
        new_referred_hospital,
        'Pending'
    );
        INSERT INTO referral_notification (
        icon,
        message,
        from_fclt_id,
        to_fclt_id,
        date,
        time,
        is_displayed,
        type
    ) VALUES (new_icon, 'New Referral', new_fclt_id, new_referred_hospital, new_date, new_time, 0, 'Referral');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `facility_verification` (IN `new_fclt_id` INT, IN `new_latitude` DECIMAL(10,8), IN `new_longitude` DECIMAL(11,8), IN `new_fname` VARCHAR(255), IN `new_mname` VARCHAR(255), IN `new_lname` VARCHAR(255), IN `new_username` VARCHAR(255), IN `new_contact_num` VARCHAR(255), IN `new_birthdate` VARCHAR(255), IN `new_img` TEXT, IN `new_role` VARCHAR(255), IN `new_pwd` VARCHAR(255), IN `new_default_pwd` VARCHAR(255), IN `new_verification` VARCHAR(255), IN `new_status` VARCHAR(255))   BEGIN
    UPDATE facilities
    SET latitude = new_latitude, longitude = new_longitude, verification = new_verification
    WHERE fclt_id = new_fclt_id;
    
    INSERT INTO staff (fname, mname, lname, username, contact_num, birth_date, role, img, fclt_id, pwd, default_pwd, status)
	VALUES (new_fname, new_mname, new_lname, new_username, new_contact_num, new_birthdate, new_role, new_img, new_fclt_id, new_pwd, new_default_pwd, new_status);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_birth_experience` (IN `new_patients_id` INT, IN `new_record_num` INT)   BEGIN
    DECLARE current_count INT;

    -- Check if new_record_num is not empty
    IF new_record_num IS NOT NULL AND new_record_num != '' THEN
        SET current_count = new_record_num;
    ELSE
    
        SELECT COUNT(*)
        INTO current_count
        FROM prenatal_records
        WHERE patients_id = new_patients_id;
    END IF;
    
    SELECT *
    FROM prenatal_records
    INNER JOIN birth_experience ON birth_experience.patients_id = prenatal_records.patients_id
        AND birth_experience.records_count = prenatal_records.records_count
    WHERE birth_experience.patients_id = new_patients_id
        AND birth_experience.records_count = current_count ORDER BY birth_experience.records_count DESC LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_facility` (IN `new_fclt_id` INT)   BEGIN

    SELECT *
    FROM facilities
    WHERE fclt_id = new_fclt_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_patients_details` (IN `new_patients_id` INT, IN `new_record_num` INT)   BEGIN
    DECLARE current_count INT;

    -- Check if new_record_num is not empty
    IF new_record_num IS NOT NULL AND new_record_num != '' THEN
        SET current_count = new_record_num;
    ELSE
        -- If new_record_num is empty, retrieve the current count from prenatal_records
        SELECT COUNT(*)
        INTO current_count
        FROM prenatal_records
        WHERE patients_id = new_patients_id;
    END IF;

    -- Your existing code
    SELECT *
    FROM prenatal_records
    INNER JOIN patients_details ON patients_details.patients_id = prenatal_records.patients_id
        AND patients_details.records_count = prenatal_records.records_count
    WHERE patients_details.patients_id = new_patients_id
        AND patients_details.records_count = current_count ORDER BY patients_details.records_count DESC LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_staff` (IN `new_staff_id` INT)   BEGIN

    SELECT *
    FROM staff
    WHERE staff_id = new_staff_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_trimester` (IN `new_patients_id` INT, IN `trimester_table_name` VARCHAR(255), IN `new_check_up` VARCHAR(255))   BEGIN
    DECLARE current_count INT;

    SELECT COUNT(*)
    INTO current_count
    FROM prenatal_records
    WHERE patients_id = new_patients_id;

    SET current_count = current_count;
    
    SELECT *
    FROM prenatal_records
    INNER JOIN trimester_table_name ON trimester_table_name.patients_id = prenatal_records.patients_id
        AND trimester_table_name.records_count = prenatal_records.records_count
    WHERE trimester_table_name.patients_id = new_patients_id
        AND trimester_table_name.records_count = current_count AND trimester_table_name.check_up = new_check_up ORDER BY trimester_table_name.records_count DESC LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_birth_experience` (IN `new_patient_id` INT, IN `new_date_of_delivery` VARCHAR(100), IN `new_type_of_delivery` VARCHAR(255), IN `new_birth_outcome` VARCHAR(255), IN `new_number_of_children_delivered` VARCHAR(100), IN `new_pregnancy_hypertension` VARCHAR(255), IN `new_preeclampsia_eclampsia` VARCHAR(255), IN `new_bleeding_during_pregnancy` VARCHAR(255), IN `new_record` INT)   BEGIN
    
    INSERT INTO birth_experience (
        patients_id,
        date_of_delivery,
        type_of_delivery,
        birth_outcome,
        number_of_children_delivered,
        pregnancy_hypertension,
        preeclampsia_eclampsia,
        bleeding_during_pregnancy,
        records_count
    ) VALUES (
        new_patient_id,
        new_date_of_delivery,
        new_type_of_delivery,
        new_birth_outcome,
        new_number_of_children_delivered,
        new_pregnancy_hypertension,
        new_preeclampsia_eclampsia,
        new_bleeding_during_pregnancy,
        new_record
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_first_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_hemoglobin_count` VARCHAR(100), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_stis_using_a_syndromic_approach` VARCHAR(255), IN `new_tetanus_containing_vaccine` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record` INT, IN `new_staff_id` INT, IN `new_fclt_id` INT)   BEGIN

    INSERT INTO first_trimester (
        check_up,
        patients_id,
        date,
        weight,
        height,
        age_of_gestation,
        blood_pressure,
        nutritional_status,
        laboratory_tests_done,
        hemoglobin_count,
        urinalysis,
        complete_blood_count,
        stis_using_a_syndromic_approach,
        tetanus_containing_vaccine,
        given_services,
        date_of_return,
        health_provider_name,
        hospital_referral,
        records_count
    ) VALUES (
        new_checkup,
        new_patient_id,
        new_date,
        new_weight,
        new_height,
        new_age_of_gestation,
        new_blood_pressure,
        new_nutritional_status,
        new_laboratory_tests_done,
        new_hemoglobin_count,
        new_urinalysis,
        new_complete_blood_count,
        new_stis_using_a_syndromic_approach,
        new_tetanus_containing_vaccine,
        new_given_services,
        new_date_of_return,
        new_health_provider_name,
        new_hospital_referral,
        new_record
    );
-- Check if there's a new date of return
IF new_date_of_return IS NOT NULL AND new_date_of_return <> '' THEN

    -- Replace checkup names
    SET new_checkup = REPLACE(REPLACE(REPLACE(new_checkup, 'first_checkup', 'First Checkup'), 'second_checkup', 'Second Checkup'), 'third_checkup', 'Third Checkup');

	 -- Check for 'Third Checkup'
    IF new_checkup = 'First Checkup' THEN
        -- Insert new schedule entry for Second Trimester and First Checkup
        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'First Trimester', 'Second Checkup', new_record, 'Upcoming', new_staff_id);

    -- Check for 'Third Checkup'
    ELSEIF new_checkup = 'Second Checkup' THEN
        -- Insert new schedule entry for Second Trimester and First Checkup
        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'First Trimester', 'Third Checkup', new_record, 'Upcoming', new_staff_id);

        -- Update the status for the Second Checkup in the First Trimester to 'Past'
        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'First Trimester' AND check_up = 'Second Checkup' AND record = new_record;

    ELSEIF new_checkup = 'Third Checkup' THEN
        -- Insert logic for 'Final Checkup'
        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'Second Trimester', 'First Checkup', new_record, 'Upcoming', new_staff_id);
        
        -- Update the status for the Second Checkup in the First Trimester to 'Past'
        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'First Trimester' AND check_up = 'Third Checkup' AND record = new_record;
    END IF;

END IF;

IF new_hospital_referral IS NOT NULL AND new_hospital_referral <> '' THEN
    INSERT INTO for_referral_patients (patients_id, referred_hospital, staff_id, date, fclt_id) 
    VALUES (new_patient_id, new_hospital_referral, new_staff_id, CURDATE(), new_fclt_id);
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_patient` (IN `new_fname` VARCHAR(255), IN `new_mname` VARCHAR(255), IN `new_lname` VARCHAR(255), IN `new_gender` VARCHAR(255), IN `new_age` INT, IN `new_birthdate` VARCHAR(255), IN `new_contactNum` VARCHAR(20), IN `new_region` VARCHAR(255), IN `new_province` VARCHAR(255), IN `new_municipality` VARCHAR(255), IN `new_barangay` VARCHAR(255), IN `new_email` VARCHAR(255), IN `new_fclt_id` INT, IN `new_dateregistered` VARCHAR(255), IN `new_users_id` INT)   BEGIN
    INSERT INTO patients (
        fname,
        mname,
        lname,
        gender,
        age,
        birthdate,
        contact,
        region,
        province,
        municipality,
        barangay,
        email,
        fclt_id,
        date_registered,
        staff_id
    ) VALUES (
        new_fname,
        new_mname,
        new_lname,
        new_gender,
        new_age,
        new_birthdate,
        new_contactNum,
        new_region,
        new_province,
        new_municipality,
        new_barangay,
        new_email,
        new_fclt_id,
        new_dateregistered,
        new_users_id
    );
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_patients_details` (IN `new_petsa_ng_unang_checkup` VARCHAR(100), IN `new_edad` VARCHAR(100), IN `new_timbang` VARCHAR(100), IN `new_taas` VARCHAR(100), IN `new_kalagayan_ng_kalusugan` VARCHAR(255), IN `new_petsa_ng_huling_regla` VARCHAR(100), IN `new_kailan_ako_manganganak` VARCHAR(100), IN `new_pang_ilang_pagbubuntis` INT, IN `new_patient_id` INT, IN `new_record` INT)   BEGIN

    INSERT INTO patients_details (
        petsa_ng_unang_checkup,
        edad,
        timbang,
        taas,
        kalagayan_ng_kalusugan,
        petsa_ng_huling_regla,
        kailan_ako_manganganak,
        pang_ilang_pagbubuntis,
        patients_id,
        records_count
    ) VALUES (
        new_petsa_ng_unang_checkup,
        new_edad,
        new_timbang,
        new_taas,
        new_kalagayan_ng_kalusugan,
        new_petsa_ng_huling_regla,
        new_kailan_ako_manganganak,
        new_pang_ilang_pagbubuntis,
        new_patient_id,
        new_record
    );
    
    UPDATE patients
    SET age = new_edad WHERE id = new_patient_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_patient_record` (IN `new_patients_id` INT, IN `new_fclt_id` INT, IN `new_date` VARCHAR(100), IN `new_time` VARCHAR(100))   BEGIN
    DECLARE current_count INT;

    SELECT COUNT(*) INTO current_count
    FROM prenatal_records
    WHERE patients_id = new_patients_id;
    
    IF current_count > 0 THEN
        SET current_count = current_count + 1;
    ELSE
        SET current_count = 1;
    END IF;

    INSERT INTO prenatal_records (
        patients_id,
        fclt_id,
        date,
        time,
        records_count
    ) VALUES (
        new_patients_id,
        new_fclt_id,
        new_date,
        new_time,
        current_count
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_second_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record` INT, IN `new_staff_id` INT)   BEGIN
    
    INSERT INTO second_trimester (
        check_up,
        patients_id,
        date,
        weight,
        height,
        age_of_gestation,
        blood_pressure,
        nutritional_status,
        given_advise,
        laboratory_tests_done,
        urinalysis,
        complete_blood_count,
        given_services,
        date_of_return,
        health_provider_name,
        hospital_referral,
        records_count
    ) VALUES (
        new_checkup,
        new_patient_id,
        new_date,
        new_weight,
        new_height,
        new_age_of_gestation,
        new_blood_pressure,
        new_nutritional_status,
        new_given_advise,
        new_laboratory_tests_done,
        new_urinalysis,
        new_complete_blood_count,
        new_given_services,
        new_date_of_return,
        new_health_provider_name,
        new_hospital_referral,
        new_record
    );
    
-- Check if there's a new date of return
IF new_date_of_return IS NOT NULL AND new_date_of_return <> '' THEN

    -- Replace checkup names
    SET new_checkup = REPLACE(REPLACE(REPLACE(new_checkup, 'first_checkup', 'First Checkup'), 'second_checkup', 'Second Checkup'), 'third_checkup', 'Third Checkup');

    IF new_checkup = 'First Checkup' THEN

        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'Second Trimester', 'Second Checkup', new_record, 'Upcoming', new_staff_id);


        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'Second Trimester' AND check_up = 'First Checkup' AND record = new_record;

    ELSEIF new_checkup = 'Second Checkup' THEN

        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'Second Trimester', 'Third Checkup', new_record, 'Upcoming', new_staff_id);


        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'Second Trimester' AND check_up = 'Second Checkup' AND record = new_record;

ELSEIF new_checkup = 'Third Checkup' THEN
        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'Third Trimester', 'First Checkup', new_record, 'Upcoming', new_staff_id);
        

        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'Second Trimester' AND check_up = 'Third Checkup' AND record = new_record;
    END IF;

END IF;

IF new_hospital_referral IS NOT NULL AND new_hospital_referral <> '' THEN
    INSERT INTO for_referral_patients (patients_id, referred_hospital, staff_id, date, fclt_id) 
    VALUES (new_patient_id, new_hospital_referral, new_staff_id, CURDATE(), new_fclt_id);
END IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_staff` (IN `new_fname` VARCHAR(255), IN `new_mname` VARCHAR(255), IN `new_lname` VARCHAR(255), IN `new_contactNum` VARCHAR(12), IN `new_address` VARCHAR(255), IN `new_role` VARCHAR(20), IN `new_fclt_id` INT, IN `new_img` TEXT, IN `new_birth_date` VARCHAR(100), IN `new_username` VARCHAR(255), IN `new_pwd` VARCHAR(255), IN `new_default_pwd` VARCHAR(255), IN `new_status` VARCHAR(255))   BEGIN
    INSERT INTO staff (
        fname,
        mname,
        lname,
        username,
        contact_num,
        birth_date,
        address,
        role,
        img,
        fclt_id,
        pwd,
        default_pwd,
        status
    ) VALUES (
        new_fname,
        new_mname,
        new_lname,
        new_username,
        new_contactNum,
        new_birth_date,
        new_address,
        new_role,
        new_img,
        new_fclt_id,
        new_pwd,
        new_default_pwd,
        new_status
    );
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_third_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record` INT, IN `new_staff_id` INT)   BEGIN
    
    INSERT INTO third_trimester (
        check_up,
        patients_id,
        date,
        weight,
        height,
        age_of_gestation,
        blood_pressure,
        nutritional_status,
        given_advise,
        laboratory_tests_done,
        urinalysis,
        complete_blood_count,
        given_services,
        date_of_return,
        health_provider_name,
        hospital_referral,
        records_count
    ) VALUES (
        new_checkup,
        new_patient_id,
        new_date,
        new_weight,
        new_height,
        new_age_of_gestation,
        new_blood_pressure,
        new_nutritional_status,
        new_given_advise,
        new_laboratory_tests_done,
        new_urinalysis,
        new_complete_blood_count,
        new_given_services,
        new_date_of_return,
        new_health_provider_name,
        new_hospital_referral,
        new_record
    );
    

IF new_date_of_return IS NOT NULL AND new_date_of_return <> '' THEN


    SET new_checkup = REPLACE(REPLACE(REPLACE(new_checkup, 'first_checkup', 'First Checkup'), 'second_checkup', 'Second Checkup'), 'third_checkup', 'Third Checkup');


    IF new_checkup = 'First Checkup' THEN

        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'Third Trimester', 'Second Checkup', new_record, 'Upcoming', new_staff_id);


        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'Third Trimester' AND check_up = 'First Checkup' AND record = new_record;

    ELSEIF new_checkup = 'Second Checkup' THEN
 
        INSERT INTO patient_schedule (patients_id, date, trimester, check_up, record, status, staff_id) 
        VALUES (new_patient_id, new_date_of_return, 'Third Trimester', 'Third Checkup', new_record, 'Upcoming', new_staff_id);

        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'Third Trimester' AND check_up = 'Second Checkup' AND record = new_record;

    ELSEIF new_checkup = 'Third Checkup' THEN 

        UPDATE patient_schedule 
        SET status = 'Past' 
        WHERE trimester = 'Third Trimester' AND check_up = 'Third Checkup' AND record = new_record;
    END IF;

END IF;

IF new_hospital_referral IS NOT NULL AND new_hospital_referral <> '' THEN
    INSERT INTO for_referral_patients (patients_id, referred_hospital, staff_id, date, fclt_id) 
    VALUES (new_patient_id, new_hospital_referral, new_staff_id, CURDATE(), new_fclt_id);
END IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `notification` (IN `new_icon` TEXT, IN `new_message` TEXT, IN `new_from_fclt_id` INT, IN `new_to_fclt_id` INT, IN `new_date` TEXT, IN `new_time` TEXT, IN `new_type` TEXT)   BEGIN
        INSERT INTO referral_notification (
        icon,
        message,
        from_fclt_id,
        to_fclt_id,
        date,
        time,
        is_displayed,
        type
    ) VALUES (new_icon, new_message,new_from_fclt_id, new_to_fclt_id, new_date, new_time, 0, new_type);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `patient_note` (IN `new_patient_id` INT, IN `new_note` TEXT, IN `new_record` INT)   BEGIN

    UPDATE prenatal_records
    SET note = new_note
    WHERE patients_id = new_patient_id AND records_count = new_record;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_facility` (IN `new_fclt_id` INT)   BEGIN

    DELETE FROM facilities WHERE fclt_id = new_fclt_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_staff` (IN `new_staff_id` INT)   BEGIN

    DELETE FROM staff WHERE staff_id=new_staff_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `submit_referral_to_doctor` (IN `new_doctor_id` INT, IN `new_rfrrl_id` INT, IN `new_staff_id` INT, IN `new_date` VARCHAR(10), IN `new_time` VARCHAR(10))   BEGIN
    INSERT INTO doctors_referral (
        doctor_id,
        rfrrl_id,
        staff_id,
        sent_date,
        sent_time
    ) VALUES (
        new_doctor_id,
        new_rfrrl_id,
        new_staff_id,
        new_date,
        new_time
    );
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_birth_experience` (IN `new_patient_id` INT, IN `new_date_of_delivery` VARCHAR(100), IN `new_type_of_delivery` VARCHAR(255), IN `new_birth_outcome` VARCHAR(255), IN `new_number_of_children_delivered` VARCHAR(100), IN `new_pregnancy_hypertension` VARCHAR(255), IN `new_preeclampsia_eclampsia` VARCHAR(255), IN `new_bleeding_during_pregnancy` VARCHAR(255), IN `new_records_count` INT)   BEGIN
    UPDATE birth_experience
    SET
        date_of_delivery = new_date_of_delivery,
        type_of_delivery = new_type_of_delivery,
        birth_outcome = new_birth_outcome,
        number_of_children_delivered = new_number_of_children_delivered,
        pregnancy_hypertension = new_pregnancy_hypertension,
        preeclampsia_eclampsia = new_preeclampsia_eclampsia,
        bleeding_during_pregnancy = new_bleeding_during_pregnancy
    WHERE
        patients_id = new_patient_id AND records_count = new_records_count;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_first_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_hemoglobin_count` VARCHAR(100), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_stis_using_a_syndromic_approach` VARCHAR(255), IN `new_tetanus_containing_vaccine` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record_count` INT)   BEGIN
    UPDATE first_trimester
    SET
        date = new_date,
        weight = new_weight,
        height = new_height,
        age_of_gestation = new_age_of_gestation,
        blood_pressure = new_blood_pressure,
        nutritional_status = new_nutritional_status,
        laboratory_tests_done = new_laboratory_tests_done,
        hemoglobin_count = new_hemoglobin_count,
        urinalysis = new_urinalysis,
        complete_blood_count = new_complete_blood_count,
        stis_using_a_syndromic_approach = new_stis_using_a_syndromic_approach,
        tetanus_containing_vaccine = new_tetanus_containing_vaccine,
        given_services = new_given_services,
        date_of_return = new_date_of_return,
        health_provider_name = new_health_provider_name,
        hospital_referral = new_hospital_referral
    WHERE
        patients_id = new_patient_id AND records_count = new_record_count;
        
	    IF new_date_of_return IS NOT NULL AND new_date_of_return <> '' THEN
    
		SET new_checkup = REPLACE(REPLACE(REPLACE(new_checkup, 'first_checkup', 'First Checkup'), 'second_checkup', 'Second Checkup'), 'third_checkup', 'Third Checkup');
        UPDATE patient_schedule SET date = new_date_of_return WHERE trimester = 'First Trimester' AND check_up = 'First Checkup' AND patients_id = new_patient_id AND record = new_record_count;


   		END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_patients_details` (IN `new_petsa_ng_unang_checkup` VARCHAR(100), IN `new_edad` VARCHAR(100), IN `new_timbang` VARCHAR(100), IN `new_taas` VARCHAR(100), IN `new_kalagayan_ng_kalusugan` VARCHAR(255), IN `new_petsa_ng_huling_regla` VARCHAR(100), IN `new_kailan_ako_manganganak` VARCHAR(100), IN `new_pang_ilang_pagbubuntis` INT, IN `new_patient_id` INT, IN `new_record_count` INT)   BEGIN
    UPDATE patients_details
    SET
        petsa_ng_unang_checkup = new_petsa_ng_unang_checkup,
        edad = new_edad,
        timbang = new_timbang,
        taas = new_taas,
        kalagayan_ng_kalusugan = new_kalagayan_ng_kalusugan,
        petsa_ng_huling_regla = new_petsa_ng_huling_regla,
        kailan_ako_manganganak = new_kailan_ako_manganganak,
        pang_ilang_pagbubuntis = new_pang_ilang_pagbubuntis
    WHERE
        patients_id = new_patient_id AND records_count = new_record_count;
        
        UPDATE patients
    	SET age = new_edad WHERE id = new_patient_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_second_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record_count` INT)   BEGIN
    UPDATE second_trimester
    SET
        date = new_date,
        weight = new_weight,
        height = new_height,
        age_of_gestation = new_age_of_gestation,
        blood_pressure = new_blood_pressure,
        nutritional_status = new_nutritional_status,
        given_advise = new_given_advise,
        laboratory_tests_done = new_laboratory_tests_done,
        urinalysis = new_urinalysis,
        complete_blood_count = new_complete_blood_count,
        given_services = new_given_services,
        date_of_return = new_date_of_return,
        health_provider_name = new_health_provider_name,
        hospital_referral = new_hospital_referral
    WHERE
        patients_id = new_patient_id AND records_count = new_record_count;
        
       	IF new_date_of_return IS NOT NULL AND new_date_of_return <> '' THEN
    
		SET new_checkup = REPLACE(REPLACE(REPLACE(new_checkup, 'first_checkup', 'First Checkup'), 'second_checkup', 'Second Checkup'), 'third_checkup', 'Third Checkup');
        UPDATE patient_schedule SET date = new_date_of_return WHERE trimester = 'First Trimester' AND check_up = 'First Checkup' AND patients_id = new_patient_id AND record = new_record_count;


   		END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_third_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(100), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255))   BEGIN
    UPDATE third_trimester
    SET
        check_up = new_checkup,
        date = new_date,
        weight = new_weight,
        height = new_height,
        age_of_gestation = new_age_of_gestation,
        blood_pressure = new_blood_pressure,
        nutritional_status = new_nutritional_status,
        given_advise = new_given_advise,
        laboratory_tests_done = new_laboratory_tests_done,
        urinalysis = new_urinalysis,
        complete_blood_count = new_complete_blood_count,
        given_services = new_given_services,
        date_of_return = new_date_of_return,
        health_provider_name = new_health_provider_name,
        hospital_referral = new_hospital_referral
    WHERE
        patients_id = new_patient_id;
        
       	IF new_date_of_return IS NOT NULL AND new_date_of_return <> '' THEN
    
		SET new_checkup = REPLACE(REPLACE(REPLACE(new_checkup, 'first_checkup', 'First Checkup'), 'second_checkup', 'Second Checkup'), 'third_checkup', 'Third Checkup');
        UPDATE patient_schedule SET date = new_date_of_return WHERE trimester = 'First Trimester' AND check_up = 'First Checkup' AND patients_id = new_patient_id AND record = new_record_count;


   		END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ap_risk_codes`
--

CREATE TABLE `ap_risk_codes` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `title` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ap_risk_codes`
--

INSERT INTO `ap_risk_codes` (`id`, `code`, `title`) VALUES
(1, 1, 'Age younger than 15 years or older than 35 years'),
(2, 2, 'Height lower than 145 cm'),
(3, 3, 'Poor obstetric History'),
(4, 4, 'Leg or pelvic deformities such as Polio paralysis'),
(5, 5, 'No prenatal or irregular prenatal in previous and present pregnancy'),
(6, 6, 'First Pregnancy'),
(7, 7, 'Pregnancy interval less than 24 months'),
(8, 8, 'Pregnancy More than 5'),
(9, 9, 'Pregnancy longer than 294 days or 42 weeks'),
(10, 10, 'Pregnancy weight less than 80% of standard weight for Filipino women'),
(11, 11, 'Anemia less than 8 grams Hemoglobin'),
(12, 12, 'Weight gain less than 40% of pregnancy weight pre-trimester'),
(13, 13, 'Weight gain less than 60% of pregnancy weight pre-trimester'),
(14, 14, 'Abnormal Presentation: Breech, Transverse and others'),
(15, 15, 'Multiple Fetuses'),
(16, 16, 'Hypertension'),
(17, 17, 'Dizziness & blurring of vision'),
(18, 18, 'Convulsions'),
(19, 19, 'Positive Urine Albumin'),
(20, 20, 'Positive Urine AlbuminVaginal Infection'),
(21, 21, 'Hepa Reactive');

-- --------------------------------------------------------

--
-- Table structure for table `birth_experience`
--

CREATE TABLE `birth_experience` (
  `birth_experience_id` int(11) NOT NULL,
  `date_of_delivery` varchar(50) NOT NULL,
  `type_of_delivery` varchar(100) NOT NULL,
  `birth_outcome` varchar(100) NOT NULL,
  `number_of_children_delivered` varchar(100) NOT NULL,
  `pregnancy_hypertension` varchar(10) NOT NULL,
  `preeclampsia_eclampsia` varchar(10) NOT NULL,
  `bleeding_during_pregnancy` varchar(10) NOT NULL,
  `patients_id` int(11) NOT NULL,
  `records_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `birth_experience`
--

INSERT INTO `birth_experience` (`birth_experience_id`, `date_of_delivery`, `type_of_delivery`, `birth_outcome`, `number_of_children_delivered`, `pregnancy_hypertension`, `preeclampsia_eclampsia`, `bleeding_during_pregnancy`, `patients_id`, `records_count`) VALUES
(23, '2024-01-12', 'Normal (N)', 'Miscarriage', 'Twins', 'No', 'No', 'No', 85, 1);

-- --------------------------------------------------------

--
-- Table structure for table `demo_message`
--

CREATE TABLE `demo_message` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `message` text NOT NULL,
  `receiver` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `demo_message`
--

INSERT INTO `demo_message` (`id`, `sender`, `message`, `receiver`) VALUES
(1, 1, '0', 1),
(2, 1, 'aw', 1),
(3, 2, 'aw', 1),
(4, 2, 'aw', 1),
(5, 2, 'aw', 1);

-- --------------------------------------------------------

--
-- Table structure for table `demo_messages`
--

CREATE TABLE `demo_messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors_referral`
--

CREATE TABLE `doctors_referral` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `rfrrl_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `sent_date` varchar(10) NOT NULL,
  `sent_time` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors_referral`
--

INSERT INTO `doctors_referral` (`id`, `doctor_id`, `rfrrl_id`, `staff_id`, `sent_date`, `sent_time`) VALUES
(118, 121, 396, 121, '2024-01-03', '22:26'),
(119, 121, 397, 121, '2024-01-03', '22:26'),
(120, 121, 398, 121, '2024-01-03', '22:26'),
(121, 121, 399, 121, '2024-01-03', '22:26'),
(122, 121, 400, 121, '2024-01-03', '22:42'),
(123, 121, 401, 121, '2024-01-03', '22:43'),
(124, 121, 402, 121, '2024-01-03', '22:48'),
(125, 121, 403, 121, '2024-01-03', '22:48'),
(126, 121, 404, 121, '2024-01-03', '22:50'),
(127, 121, 405, 121, '2024-01-03', '22:51'),
(128, 121, 406, 121, '2024-01-03', '22:51'),
(129, 121, 407, 121, '2024-01-04', '01:04'),
(130, 121, 418, 121, '2024-01-04', '18:46'),
(131, 121, 419, 121, '2024-01-04', '19:40'),
(132, 121, 420, 121, '2024-01-04', '19:43'),
(133, 121, 421, 121, '2024-01-04', '19:50');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `fclt_id` int(11) NOT NULL,
  `fclt_name` varchar(255) NOT NULL,
  `fclt_ref_id` varchar(255) NOT NULL,
  `fclt_type` varchar(255) NOT NULL,
  `img_url` text NOT NULL,
  `fclt_contact` varchar(11) NOT NULL,
  `fclt_status` varchar(10) NOT NULL,
  `verification` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `region_code` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`fclt_id`, `fclt_name`, `fclt_ref_id`, `fclt_type`, `img_url`, `fclt_contact`, `fclt_status`, `verification`, `latitude`, `longitude`, `region_code`, `region`, `province`, `municipality`) VALUES
(1, 'Caraga Regional Hospital', '001', 'Hospital', '327551649_914690853228097_6434233597114540894_n.jpg', '98768726379', 'Active', 'Verified', 9.79050280, 125.49356970, '13', 'REGION XIII', 'SURIGAO DEL NORTE', 'SURIGAO CITY'),
(2, 'Gigaquit Birthing Home', '002', 'Birthing Home', '128134146_173298477853059_7754180633774849296_n.jpg', '91827462721', 'Active', 'Verified', 9.59581880, 125.69763700, '13', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT'),
(3, 'Surigao Del Norte Provincial Hospital', '003', 'Provincial Hospital', '308023437_179721321252551_4795686202601493702_n.jpg', '09090909099', 'Active', 'Verified', 9.63336650, 125.56612800, '13', 'REGION XIII', 'SURIGAO DEL NORTE', 'BAD-AS'),
(4, 'Miranda', '004', 'Hospital', 'logo.png', '09876865271', 'Offline', 'Unverified', 0.00000000, 0.00000000, '13', 'REGION XIII', 'SURIGAO DEL NORTE', 'SURIGAO CITY'),
(5, 'Claver RHU', '005', 'Birthing Home', 'logo.png', '82746172634', 'Offline', 'Unverified', 0.00000000, 0.00000000, '13', 'REGION XIII', 'SURIGAO DEL NORTE', 'CLAVER');

-- --------------------------------------------------------

--
-- Table structure for table `first_trimester`
--

CREATE TABLE `first_trimester` (
  `first_trimester_id` int(11) NOT NULL,
  `check_up` varchar(255) NOT NULL,
  `patients_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL,
  `height` varchar(255) NOT NULL,
  `age_of_gestation` varchar(255) NOT NULL,
  `blood_pressure` varchar(255) NOT NULL,
  `nutritional_status` varchar(255) NOT NULL,
  `laboratory_tests_done` varchar(255) NOT NULL,
  `hemoglobin_count` varchar(255) NOT NULL,
  `urinalysis` varchar(255) NOT NULL,
  `complete_blood_count` varchar(255) NOT NULL,
  `stis_using_a_syndromic_approach` varchar(255) NOT NULL,
  `tetanus_containing_vaccine` varchar(255) NOT NULL,
  `given_services` varchar(255) NOT NULL,
  `date_of_return` varchar(255) NOT NULL,
  `health_provider_name` varchar(255) NOT NULL,
  `hospital_referral` varchar(255) NOT NULL,
  `records_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `for_referral_patients`
--

CREATE TABLE `for_referral_patients` (
  `id` int(11) NOT NULL,
  `patients_id` int(11) NOT NULL,
  `referred_hospital` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `date` text NOT NULL,
  `fclt_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `users_id` int(11) NOT NULL,
  `msg_status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `message`, `receiver_id`, `date`, `time`, `users_id`, `msg_status`) VALUES
(448, 3, 'hey', 1, '2023-12-31', '11:28:09', 121, 'Seen'),
(449, 3, 'yow', 2, '2023-12-31', '11:28:19', 121, 'Seen'),
(450, 2, 'yow', 3, '2023-12-31', '11:30:30', 130, 'Seen'),
(451, 2, 'hi', 3, '2023-12-31', '11:59:02', 130, 'Seen'),
(452, 2, 'sup', 3, '2023-12-31', '12:01:19', 130, 'Seen'),
(453, 2, 'asd', 3, '2023-12-31', '12:01:32', 130, 'Seen'),
(454, 3, 'ha?', 2, '2023-12-31', '12:36:22', 121, 'Seen'),
(455, 2, 'wala man', 3, '2023-12-31', '12:36:33', 130, 'Seen'),
(456, 2, 'hello can i join?', 3, '2023-12-31', '12:49:49', 136, 'Seen'),
(457, 3, 'asdaasdasdasdsd', 2, '2023-12-31', '13:28:48', 121, 'Seen'),
(458, 2, 'asdaddasd', 3, '2023-12-31', '13:29:20', 136, 'Seen'),
(459, 2, 'asdaasdasd', 3, '2023-12-31', '13:29:26', 136, 'Seen'),
(460, 2, 'asdadasdadd', 3, '2023-12-31', '13:29:59', 130, 'Seen'),
(461, 2, 'asdadadad', 3, '2023-12-31', '13:30:02', 130, 'Seen'),
(462, 3, 'asad', 2, '2023-12-31', '13:30:34', 121, 'Seen'),
(463, 3, 'asdadd', 2, '2023-12-31', '13:30:36', 121, 'Seen'),
(464, 1, 'what', 3, '2023-12-31', '21:16:16', 133, 'Seen'),
(465, 3, 'heyy', 1, '2023-12-31', '21:37:07', 131, 'Seen'),
(466, 3, 'hahahaa', 1, '2023-12-31', '21:37:10', 131, 'Seen'),
(467, 1, 'hi', 2, '2024-01-01', '14:28:19', 134, 'Seen'),
(468, 2, 'hey', 1, '2024-01-01', '14:29:55', 130, 'Seen'),
(469, 2, 'sup', 1, '2024-01-01', '14:31:16', 130, 'Seen'),
(470, 2, 'wew', 1, '2024-01-01', '14:31:27', 130, 'Seen'),
(471, 1, 'hahaha', 3, '2024-01-01', '17:36:45', 134, 'Seen'),
(472, 2, 'hi po', 1, '2024-01-01', '18:07:09', 130, 'Seen'),
(473, 2, 'hey bitches', 3, '2024-01-03', '00:45:35', 137, 'Seen'),
(474, 2, 'hahahha', 3, '2024-01-03', '14:56:48', 137, 'Seen'),
(475, 2, 'yow', 3, '2024-01-03', '19:43:28', 130, 'Seen'),
(476, 2, 'hey', 3, '2024-01-03', '19:43:43', 130, 'Seen'),
(477, 2, 'yow', 3, '2024-01-03', '19:43:51', 130, 'Seen'),
(478, 3, 'hello', 2, '2024-01-03', '19:44:00', 131, 'Seen'),
(479, 2, 'hehe', 3, '2024-01-03', '19:44:09', 130, 'Seen'),
(480, 2, 'yow', 3, '2024-01-03', '19:44:28', 130, 'Seen'),
(481, 2, 'hello', 3, '2024-01-03', '19:50:36', 130, 'Seen'),
(482, 2, 'hey', 3, '2024-01-03', '19:51:03', 130, 'Seen'),
(483, 2, 'yow', 3, '2024-01-03', '19:52:15', 130, 'Seen'),
(484, 2, 'yow', 3, '2024-01-03', '19:54:49', 130, 'Seen'),
(485, 2, 'sup', 3, '2024-01-03', '19:54:57', 130, 'Seen'),
(486, 2, 'yow', 3, '2024-01-03', '19:55:04', 130, 'Seen'),
(487, 2, 'yow', 3, '2024-01-03', '20:00:31', 130, 'Seen'),
(488, 2, 'hey', 3, '2024-01-03', '20:03:09', 130, 'Seen'),
(489, 2, 'syp', 3, '2024-01-03', '20:04:06', 130, 'Seen'),
(490, 2, 'asadd', 3, '2024-01-03', '20:04:37', 130, 'Seen'),
(491, 2, 'yow', 3, '2024-01-03', '20:07:54', 130, 'Seen'),
(492, 2, 'hey', 3, '2024-01-03', '20:08:08', 130, 'Seen'),
(493, 2, 'asd', 3, '2024-01-03', '20:08:36', 130, 'Seen'),
(494, 2, 'asd', 3, '2024-01-03', '20:08:42', 130, 'Seen'),
(495, 2, 'asd', 3, '2024-01-03', '20:09:13', 130, 'Seen'),
(496, 2, 'sup', 3, '2024-01-03', '20:10:46', 130, 'Seen'),
(497, 2, 'asdadd', 3, '2024-01-03', '20:11:20', 130, 'Seen'),
(498, 2, 'asd', 3, '2024-01-03', '20:11:41', 130, 'Seen'),
(499, 2, 'asd', 3, '2024-01-03', '20:18:47', 130, 'Seen'),
(500, 2, 'sup', 3, '2024-01-03', '20:48:35', 130, 'Seen'),
(501, 2, 'pre', 3, '2024-01-03', '20:50:23', 130, 'Seen'),
(502, 3, 'yow', 2, '2024-01-03', '23:09:11', 121, 'Seen'),
(503, 3, 'asd', 2, '2024-01-03', '23:09:21', 121, 'Seen'),
(504, 3, 'asd', 2, '2024-01-03', '23:09:29', 121, 'Seen'),
(505, 2, 'yow', 3, '2024-01-03', '23:09:47', 130, 'Sent'),
(506, 3, 'hahaha', 2, '2024-01-03', '23:09:52', 121, 'Seen');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `age` int(11) NOT NULL,
  `birthdate` varchar(200) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `date_registered` varchar(255) NOT NULL,
  `staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `fname`, `mname`, `lname`, `gender`, `age`, `birthdate`, `contact`, `region`, `province`, `municipality`, `barangay`, `email`, `fclt_id`, `date_registered`, `staff_id`) VALUES
(64, 'Jezrael', 'Juarez', 'Salino', 'Male', 15, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28', 137),
(65, 'Jiffer', 'Juarez', 'Salino', 'Male', 20, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28', 137),
(77, 'Sarah', 'll', 'Jane', 'Female', 20, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28', 137),
(79, 'Mark', '', 'Sitoy', 'Male', 12, '2023-12-02', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'Kinabutan', 'jezraelsalino@gmail.com', 2, '2023-11-28', 137),
(80, 'Andrei', '', 'Blanco', 'Male', 12, '2023-12-02', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'LAHI', 'andreiblanco@yahoo.com', 2, '2023-12-20', 137),
(81, 'Mark Ivan', '', 'Blanco', 'Male', 12, '2023-12-01', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'MAHANUB', 'markblanco@yahoo.com', 2, '2023-12-20', 137),
(82, 'Nyko', '', 'Jumamil', 'Male', 18, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'LAHI', 'nyko@yahoo.com', 2, '2023-12-21', 137),
(84, 'asdd', 'asdd', 'asdd', 'Female', 32, '2024-01-02', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'MAHANUB', 'andreiblanco@yahoo.com', 2, '2024-01-02', 137),
(85, 'asdadd', 'asdd', 'asddas', 'Female', 32, '2024-01-04', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'PONIENTE', 'andreiblanco@yahoo.com', 2, '2024-01-04', 138),
(86, 'fff', 'fff', 'fff', 'Female', 42, '2024-01-04', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SICO-SICO', '', 2, '2024-01-04', 138);

-- --------------------------------------------------------

--
-- Table structure for table `patients_attachments`
--

CREATE TABLE `patients_attachments` (
  `attachments_id` int(11) NOT NULL,
  `filename` text NOT NULL,
  `unique_filename` text NOT NULL,
  `patients_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients_attachments`
--

INSERT INTO `patients_attachments` (`attachments_id`, `filename`, `unique_filename`, `patients_id`) VALUES
(6, '06_Handout_1.pdf', '6592e00b8e9aa_06_Handout_1.pdf', 79),
(7, '06_Handout_1.pdf', '6592e3ed47f9c_06_Handout_1.pdf', 79),
(8, '06_Performance_Task_1(8).pdf', '6592e64b2adac_06_Performance_Task_1(8).pdf', 79),
(56, '03_Activity_1(9).pdf', '659501ecd0b20_03_Activity_1(9).pdf', 84),
(61, '03_Activity_1(9).pdf', '6595ba3d05611_03_Activity_1(9).pdf', 84),
(62, '03_Activity_1(9).pdf', '6595bacfa21fe_03_Activity_1(9).pdf', 64);

-- --------------------------------------------------------

--
-- Table structure for table `patients_details`
--

CREATE TABLE `patients_details` (
  `patients_details_id` int(11) NOT NULL,
  `petsa_ng_unang_checkup` varchar(255) NOT NULL,
  `edad` varchar(255) NOT NULL,
  `timbang` varchar(255) NOT NULL,
  `taas` varchar(255) NOT NULL,
  `kalagayan_ng_kalusugan` varchar(255) DEFAULT NULL,
  `petsa_ng_huling_regla` varchar(255) DEFAULT NULL,
  `kailan_ako_manganganak` varchar(255) DEFAULT NULL,
  `pang_ilang_pagbubuntis` varchar(255) DEFAULT NULL,
  `patients_id` int(11) NOT NULL,
  `records_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_schedule`
--

CREATE TABLE `patient_schedule` (
  `schedule_id` int(11) NOT NULL,
  `patients_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `trimester` varchar(255) NOT NULL,
  `check_up` varchar(255) NOT NULL,
  `record` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prenatal_records`
--

CREATE TABLE `prenatal_records` (
  `prenatal_records_id` int(11) NOT NULL,
  `patients_id` int(11) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `date` varchar(100) NOT NULL,
  `time` varchar(100) NOT NULL,
  `records_count` int(11) NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_image`
--

CREATE TABLE `profile_image` (
  `id` int(11) NOT NULL,
  `img_path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile_image`
--

INSERT INTO `profile_image` (`id`, `img_path`) VALUES
(31, 'C:\\xampp\\htdocs\\Referral_System/images/apple.jpg'),
(32, 'C:\\xampp\\htdocs\\Referral_System/images/Apple-Logo-black.png'),
(33, 'C:\\xampp\\htdocs\\Referral_System/images/Apple-Logo-black.png'),
(34, 'C:\\xampp\\htdocs\\Referral_System/images/Apple-Logo-black.png');

-- --------------------------------------------------------

--
-- Table structure for table `referral_format`
--

CREATE TABLE `referral_format` (
  `id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_format`
--

INSERT INTO `referral_format` (`id`, `field_name`) VALUES
(1, 'Name'),
(18, 'V/s');

-- --------------------------------------------------------

--
-- Table structure for table `referral_forms`
--

CREATE TABLE `referral_forms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `bdate` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `admitting_dx` varchar(255) NOT NULL,
  `rtpcr` varchar(255) NOT NULL,
  `antigen` varchar(255) NOT NULL,
  `clinical_ssx` varchar(255) NOT NULL,
  `exposure_to_covid` varchar(255) NOT NULL,
  `temp` varchar(255) NOT NULL,
  `hr` varchar(255) NOT NULL,
  `resp` varchar(255) NOT NULL,
  `bp` varchar(255) NOT NULL,
  `O2sat` varchar(255) NOT NULL,
  `O2aided` varchar(255) NOT NULL,
  `procedures_need` varchar(255) NOT NULL,
  `fh` varchar(255) NOT NULL,
  `ie` varchar(255) NOT NULL,
  `fht` varchar(255) NOT NULL,
  `lmp` varchar(255) NOT NULL,
  `edc` varchar(255) NOT NULL,
  `aog` varchar(255) NOT NULL,
  `utz` varchar(255) NOT NULL,
  `utz_aog` varchar(255) NOT NULL,
  `edd` varchar(255) NOT NULL,
  `enterpretation` varchar(255) NOT NULL,
  `diagnostic_test` varchar(255) NOT NULL,
  `referral_reason` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_forms`
--

INSERT INTO `referral_forms` (`id`, `name`, `age`, `sex`, `bdate`, `address`, `admitting_dx`, `rtpcr`, `antigen`, `clinical_ssx`, `exposure_to_covid`, `temp`, `hr`, `resp`, `bp`, `O2sat`, `O2aided`, `procedures_need`, `fh`, `ie`, `fht`, `lmp`, `edc`, `aog`, `utz`, `utz_aog`, `edd`, `enterpretation`, `diagnostic_test`, `referral_reason`) VALUES
(418, 'Jane, Sarah ll', '20', 'Female', '2023-12-21', 'SAN ISIDRO', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Abnormal Presentation: Breech, Transverse and others (Code 14)'),
(419, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Weight gain less than 60% of pregnancy weight pre-trimester (Code 13)'),
(420, 'Salino, Jiffer Juarez', '20', 'Male', '2023-12-21', 'SAN ISIDRO', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Abnormal Presentation: Breech, Transverse and others (Code 14)'),
(421, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Weight gain less than 60% of pregnancy weight pre-trimester (Code 13)');

-- --------------------------------------------------------

--
-- Table structure for table `referral_notification`
--

CREATE TABLE `referral_notification` (
  `id` int(11) NOT NULL,
  `icon` text NOT NULL,
  `message` varchar(255) NOT NULL,
  `from_fclt_id` int(11) NOT NULL,
  `to_fclt_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `is_displayed` tinyint(1) DEFAULT 0,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_notification`
--

INSERT INTO `referral_notification` (`id`, `icon`, `message`, `from_fclt_id`, `to_fclt_id`, `date`, `time`, `is_displayed`, `type`) VALUES
(1248, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:01 PM', 0, 'Referral Accepted'),
(1249, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:03', 0, 'Referral Process'),
(1250, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:06', 0, 'Referral Process'),
(1251, '308023437_179721321252551_4795686202601493702_n.jpg', 'asdasd', 3, 2, '2024-01-03', '10:12 PM', 0, 'Referral Declined'),
(1252, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:16 PM', 0, 'Referral Accepted'),
(1253, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:23', 0, 'Referral Process'),
(1254, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:23 PM', 0, 'Referral Accepted'),
(1255, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:23 PM', 0, 'Patient Arrival'),
(1256, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:26', 0, 'Referral Process'),
(1257, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:26', 0, 'Referral Process'),
(1258, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:26', 0, 'Referral Process'),
(1259, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:26', 0, 'Referral Process'),
(1260, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:27 PM', 0, 'Referral Accepted'),
(1261, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:28 PM', 0, 'Patient Arrival'),
(1262, '308023437_179721321252551_4795686202601493702_n.jpg', 'idk', 3, 2, '2024-01-03', '10:29 PM', 0, 'Referral Declined'),
(1263, '308023437_179721321252551_4795686202601493702_n.jpg', 'asdad', 3, 2, '2024-01-03', '10:37 PM', 0, 'Referral Declined'),
(1264, '308023437_179721321252551_4795686202601493702_n.jpg', 'asdd', 3, 2, '2024-01-03', '10:38 PM', 0, 'Referral Declined'),
(1265, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:42', 0, 'Referral Process'),
(1266, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:43 PM', 0, 'Referral Accepted'),
(1267, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:43', 0, 'Referral Process'),
(1268, '308023437_179721321252551_4795686202601493702_n.jpg', 'asd', 3, 2, '2024-01-03', '10:43 PM', 0, 'Referral Declined'),
(1269, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:44 PM', 0, 'Patient Arrival'),
(1270, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:48', 0, 'Referral Process'),
(1271, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:48', 0, 'Referral Process'),
(1272, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:48 PM', 0, 'Referral Accepted'),
(1273, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:48 PM', 0, 'Referral Accepted'),
(1274, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:48 PM', 0, 'Patient Arrival'),
(1275, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:48 PM', 0, 'Patient Arrival'),
(1276, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:49 PM', 0, 'Patient Arrival'),
(1277, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:50', 0, 'Referral Process'),
(1278, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:50 PM', 0, 'Referral Accepted'),
(1279, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:50 PM', 0, 'Patient Arrival'),
(1280, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:51', 0, 'Referral Process'),
(1281, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-03', '22:51', 0, 'Referral Process'),
(1282, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:51 PM', 0, 'Referral Accepted'),
(1283, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-03', '10:51 PM', 0, 'Referral Accepted'),
(1284, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-03', '10:52 PM', 0, 'Patient Arrival'),
(1285, '128134146_173298477853059_7754180633774849296_n.jpg', 'New Referral', 2, 3, '2024-01-04', '12:59 AM', 0, 'Referral'),
(1286, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-04', '01:03 AM', 0, 'Patient Arrival'),
(1287, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-04', '01:04', 0, 'Referral Process'),
(1288, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-04', '01:04 AM', 0, 'Referral Accepted'),
(1289, '308023437_179721321252551_4795686202601493702_n.jpg', 'Patient Arrived', 3, 2, '2024-01-04', '01:05 AM', 0, 'Patient Arrival'),
(1290, '128134146_173298477853059_7754180633774849296_n.jpg', 'New Referral', 2, 3, '2024-01-04', '05:39 PM', 0, 'Referral'),
(1291, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-04', '18:46', 0, 'Referral Process'),
(1292, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-04', '07:34 PM', 0, 'Referral Accepted'),
(1293, '128134146_173298477853059_7754180633774849296_n.jpg', 'New Referral', 2, 3, '2024-01-04', '07:40 PM', 0, 'Referral'),
(1294, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-04', '19:40', 0, 'Referral Process'),
(1295, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-04', '07:42 PM', 0, 'Referral Accepted'),
(1296, '128134146_173298477853059_7754180633774849296_n.jpg', 'New Referral', 2, 3, '2024-01-04', '07:43 PM', 0, 'Referral'),
(1297, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-04', '19:43', 0, 'Referral Process'),
(1298, '128134146_173298477853059_7754180633774849296_n.jpg', 'New Referral', 2, 3, '2024-01-04', '07:49 PM', 0, 'Referral'),
(1299, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is on the process', 3, 2, '2024-01-04', '19:50', 0, 'Referral Process'),
(1300, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-04', '07:50 PM', 0, 'Referral Accepted'),
(1301, '308023437_179721321252551_4795686202601493702_n.jpg', 'Your referral is accepted', 3, 2, '2024-01-04', '07:52 PM', 0, 'Referral Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `referral_records`
--

CREATE TABLE `referral_records` (
  `id` int(11) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `rfrrl_id` int(11) NOT NULL,
  `patients_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `referred_hospital` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_records`
--

INSERT INTO `referral_records` (`id`, `fclt_id`, `rfrrl_id`, `patients_id`, `date`, `time`, `referred_hospital`, `status`) VALUES
(393, 2, 418, 77, '2024-01-04', '05:39 PM', '3', 'Accepted'),
(394, 2, 419, 79, '2024-01-04', '07:40 PM', '3', 'Accepted'),
(395, 2, 420, 65, '2024-01-04', '07:43 PM', '3', 'Accepted'),
(396, 2, 421, 79, '2024-01-04', '07:49 PM', '3', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `referral_transaction`
--

CREATE TABLE `referral_transaction` (
  `id` int(11) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `rfrrl_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `arrival` varchar(255) NOT NULL,
  `expected_arrival` varchar(255) NOT NULL,
  `patient_status_upon_arrival` varchar(255) NOT NULL,
  `receiving_officer` varchar(255) NOT NULL,
  `arrival_date` varchar(255) NOT NULL,
  `arrival_time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_transaction`
--

INSERT INTO `referral_transaction` (`id`, `fclt_id`, `rfrrl_id`, `status`, `date`, `time`, `reason`, `arrival`, `expected_arrival`, `patient_status_upon_arrival`, `receiving_officer`, `arrival_date`, `arrival_time`) VALUES
(246, 3, 396, 'Accepted', '2024-01-03', '10:27 PM', 'NULL', 'Arrived', '23.63 minutes', 'asdad', '121', '2024-01-03', '22:27'),
(247, 3, 397, 'Declined', '2024-01-03', '10:29 PM', 'idk', '', '23.63 minutes', '', '', '', ''),
(248, 3, 398, 'Declined', '2024-01-03', '10:37 PM', 'asdad', '', '23.63 minutes', '', '', '', ''),
(249, 3, 399, 'Declined', '2024-01-03', '10:38 PM', 'asdd', '', '23.63 minutes', '', '', '', ''),
(250, 3, 400, 'Accepted', '2024-01-03', '10:43 PM', 'NULL', 'Arrived', '23.63 minutes', 'asd', '121', '2024-01-03', '22:44'),
(251, 3, 401, 'Declined', '2024-01-03', '10:43 PM', 'asd', '', '23.63 minutes', '', '', '', ''),
(252, 3, 402, 'Accepted', '2024-01-03', '10:48 PM', 'NULL', 'Arrived', '23.63 minutes', 'asdda', '121', '2024-01-03', '22:48'),
(253, 3, 403, 'Accepted', '2024-01-03', '10:48 PM', 'NULL', 'Arrived', '23.63 minutes', 'asdd', '121', '2024-01-03', '22:48'),
(254, 3, 404, 'Accepted', '2024-01-03', '10:50 PM', 'NULL', 'Arrived', '23.63 minutes', 'asdad', '121', '2024-01-03', '22:50'),
(255, 3, 406, 'Accepted', '2024-01-03', '10:51 PM', 'NULL', 'Arrived', '23.63 minutes', 'asad', '121', '2024-01-03', '22:52'),
(256, 3, 405, 'Accepted', '2024-01-03', '10:51 PM', 'NULL', 'Arrived', '23.63 minutes', 'asdad', '121', '2024-01-04', '01:03'),
(257, 3, 407, 'Accepted', '2024-01-04', '01:04 AM', 'NULL', 'Arrived', '23.63 minutes', 'asdd', '121', '2024-01-04', '01:05'),
(258, 3, 418, 'Accepted', '2024-01-04', '07:34 PM', 'NULL', 'Arriving', '23.63 minutes', '', '', '', ''),
(259, 3, 419, 'Accepted', '2024-01-04', '07:42 PM', 'NULL', 'Arriving', '23.63 minutes', '', '', '', ''),
(260, 3, 420, 'Accepted', '2024-01-04', '07:50 PM', 'NULL', 'Arriving', '23.63 minutes', '', '', '', ''),
(261, 3, 421, 'Accepted', '2024-01-04', '07:52 PM', 'NULL', 'Arriving', '23.63 minutes', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `second_trimester`
--

CREATE TABLE `second_trimester` (
  `second_trimester_id` int(11) NOT NULL,
  `check_up` varchar(255) DEFAULT NULL,
  `patients_id` int(11) DEFAULT NULL,
  `date` varchar(50) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `height` varchar(50) DEFAULT NULL,
  `age_of_gestation` varchar(50) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `nutritional_status` varchar(50) DEFAULT NULL,
  `given_advise` varchar(255) DEFAULT NULL,
  `laboratory_tests_done` varchar(255) DEFAULT NULL,
  `urinalysis` varchar(255) DEFAULT NULL,
  `complete_blood_count` varchar(255) DEFAULT NULL,
  `given_services` varchar(255) DEFAULT NULL,
  `date_of_return` varchar(50) DEFAULT NULL,
  `health_provider_name` varchar(255) DEFAULT NULL,
  `hospital_referral` varchar(255) DEFAULT NULL,
  `records_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `contact_num` varchar(13) NOT NULL,
  `staff_email` varchar(255) NOT NULL,
  `birth_date` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `img` varchar(300) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `default_pwd` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `fname`, `mname`, `lname`, `username`, `contact_num`, `staff_email`, `birth_date`, `address`, `role`, `img`, `fclt_id`, `pwd`, `default_pwd`, `status`) VALUES
(121, 'Claire', '', 'Roflo', 'claire', '09090676022', 'claire@gmail.com', '2023-12-25', 'TONDO', 'Doctor', '3bd6cffb519be59f_404387151_1982226245486627_8156855323126660934_n.jpg', 3, '$2y$10$H/T6GFVP4Qcu4mpzCG32Cecdxox4z/lmWHBs9IFCxiq4yd4A3IG.6', 'claire.2023-12-25.a1778', 'Active'),
(122, 'Peachy', '', 'Lucero', 'Peachy', '09090676022', '', '2023-12-25', 'NUEVA', 'Nurse', 'efc0d9f4bad607d3_405237346_3652158395058787_5474001873580579588_n.jpg', 3, '$2y$10$SK9nhQ9N5v2ip/w9dA8Ps.t9qAxdcVIUc9jfqqIEO.SKYAdroLCzG', 'Peachy.2023-12-25.e8bec', 'Active'),
(130, 'Sarah Jane', '', 'Dahug', 'Sarahjane', '09090676022', 'sarahjane@gmail.com', '2023-12-26', '', 'Admin', '658d476ea3471_sarah.jpg', 2, '$2y$10$YrPIBfzFmMb/7B/5FNc5Cent4mI8K0iI3LFduTn88NyFlcmBaPFkK', 'Sarahjane.2023-12-26.6bd0e', 'Active'),
(131, 'Nyko', 'Ebero', 'Jumamil', 'Nyko', '09090676022', '', '2023-12-26', '', 'Admin', '65916e5c15977_nyko.jpg', 3, '$2y$10$7bEgQ/EwWNmc8bssnbQWqurhofxq1psKJAIengS2motpayplrrJ4G', 'Nyko.2023-12-26.80c81', 'Active'),
(133, 'Ronald Marvin', '', 'Bautista', 'Ronald', '09090676022', '', '2023-12-27', '', 'Admin', '65916e0741d81_ronald.jpg', 1, '$2y$10$YzM2xyJlnOirDvWjM8cg9upRdhibFXqB6u5UJx9jFDRGDBdi6F77u', 'Ronald.2023-12-27.7403a', 'Active'),
(134, 'Norberto', '', 'Bruzon', 'Norberto', '09090676022', '', '2023-12-28', 'Mati.i', 'Doctor', '65968566ef709_hacker-with-question-mark-3d-zxtpr1lv2j90jnv7.jpg', 1, '$2y$10$s7H6cilUzdNr77K2vuFDa.o2U4iklMxq6FKBKmDZ9JcJ3ORtatVVW', 'Norberto.2023-12-28.d69ee', 'Active'),
(135, 'Peachy', '', 'Lucero', 'Peachy01', '09090676022', '', '2023-12-28', 'Nueva', 'Midwife', 'a2c0e7b4bdcb0b2a_405237346_3652158395058787_5474001873580579588_n.jpg', 1, '$2y$10$0bgn3v7HugUlxa3C/wpvIO3qAvHseUrS0avOoisPk6lJR9rridheu', 'Peachy01.2023-12-28.06a83', 'Active'),
(136, 'Dave', '', 'Tenio', 'Dave', '09090676022', '', '2023-12-31', 'Nueva', 'Doctor', '96b9034581f6eb62_doctor-with-stethoscope-in-hospital.jpg', 2, '$2y$10$4ClgjbjUHDJ69wgOBDq0feN0CK8yK9nsSkUCPnBQAifgp9kGNwgqe', 'Dave.2023-12-31.27f53', 'Offline'),
(137, 'Isabella', 'Grace', 'Rodriguez', 'Isabella', '09090676022', '', '2023-12-31', 'Brgy. Luna', 'Midwife', '91cabd1fbf98304a_female-physician.jpg', 2, '$2y$10$BNxWnhYAVyXT2dRB15XF6uZRkYAEZNkItgQihN.YS5YVBpbe3jh.q', 'Isabella.2023-12-31.72332', 'Offline'),
(138, 'Jacky', '', 'Rico', 'jacky', '09090676022', '', '2024-01-04', 'Kinabutan', 'Midwife', '41ada9817c557731_336908895_682199006996516_157572522712414769_n.jpg', 2, '$2y$10$poIfyjw.wRG1ellLvhrXKOLp3mxGLlndO4iAXw25aOfkVoVuWqGOe', 'jacky.2024-01-04.f4f07', 'Offline');

-- --------------------------------------------------------

--
-- Table structure for table `third_trimester`
--

CREATE TABLE `third_trimester` (
  `third_trimester_id` int(11) NOT NULL,
  `check_up` varchar(255) NOT NULL,
  `patients_id` int(11) DEFAULT NULL,
  `date` varchar(50) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `height` varchar(50) DEFAULT NULL,
  `age_of_gestation` varchar(50) DEFAULT NULL,
  `blood_pressure` varchar(50) DEFAULT NULL,
  `nutritional_status` varchar(50) DEFAULT NULL,
  `given_advise` text DEFAULT NULL,
  `laboratory_tests_done` text DEFAULT NULL,
  `urinalysis` text DEFAULT NULL,
  `complete_blood_count` text DEFAULT NULL,
  `given_services` text DEFAULT NULL,
  `date_of_return` varchar(20) DEFAULT NULL,
  `health_provider_name` varchar(100) DEFAULT NULL,
  `hospital_referral` varchar(100) DEFAULT NULL,
  `records_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trimester_demo`
--

CREATE TABLE `trimester_demo` (
  `id` int(11) NOT NULL,
  `check-up` varchar(255) NOT NULL,
  `patients_id` varchar(255) NOT NULL,
  `hospital_referral` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_files`
--

CREATE TABLE `uploaded_files` (
  `id` int(11) NOT NULL,
  `file_name` text NOT NULL,
  `file_path` text NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `usersId` int(11) NOT NULL,
  `usersName` varchar(128) NOT NULL,
  `usersEmail` varchar(128) NOT NULL,
  `usersUid` varchar(128) NOT NULL,
  `usersrole` varchar(255) NOT NULL,
  `usersPwd` varchar(128) NOT NULL,
  `usersImg` text NOT NULL,
  `fclt_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`usersId`, `usersName`, `usersEmail`, `usersUid`, `usersrole`, `usersPwd`, `usersImg`, `fclt_id`) VALUES
(7, 'Nyko Jumamil', 'jezraelsalino@gmail.com', 'Nyko', 'Admin', '$2y$10$zyga/EpPBf7Gw8iGIdELGOwxGVV5cKsMPcTG7G7DmDqhop6tdZpBK', '2ad392c4_🤓.png', 2),
(8, 'Jezrael Salino', 'jezraelsalino@yahoo.com', 'Jezmahboi', 'Admin', '$2y$10$KHzZQ20quKBf7qR/AGUSz.BTjnZjYpm5pHrVOinVYz3Rbo1Ab251i', '2ad392c4_🤓.png', 1),
(10, 'Sarah Jane', 'sarahjane@gmail.com', 'Sarah', 'Admin', '$2y$10$jScQbxdMvFTBFdma7wBcS.Iv5BYpniSqxrlI/8TSA03Hvvfqt/IrC', '2ad392c4_🤓.png', 3);

-- --------------------------------------------------------

--
-- Table structure for table `your_table`
--

CREATE TABLE `your_table` (
  `id` int(11) NOT NULL,
  `region` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `your_table`
--

INSERT INTO `your_table` (`id`, `region`, `province`, `municipality`, `barangay`) VALUES
(9, 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO'),
(10, 'REGION X', 'CAMIGUIN', 'GUINSILIBAN', 'LIONG'),
(11, 'NCR', 'NATIONAL CAPITAL REGION - SECOND DISTRICT', 'CITY OF MARIKINA', 'CONCEPCION DOS'),
(12, 'REGION XIII', 'SURIGAO DEL SUR', 'CANTILAN', 'MAGASANG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ap_risk_codes`
--
ALTER TABLE `ap_risk_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `birth_experience`
--
ALTER TABLE `birth_experience`
  ADD PRIMARY KEY (`birth_experience_id`);

--
-- Indexes for table `demo_message`
--
ALTER TABLE `demo_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `demo_messages`
--
ALTER TABLE `demo_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `doctors_referral`
--
ALTER TABLE `doctors_referral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`fclt_id`);

--
-- Indexes for table `first_trimester`
--
ALTER TABLE `first_trimester`
  ADD PRIMARY KEY (`first_trimester_id`);

--
-- Indexes for table `for_referral_patients`
--
ALTER TABLE `for_referral_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients_attachments`
--
ALTER TABLE `patients_attachments`
  ADD PRIMARY KEY (`attachments_id`);

--
-- Indexes for table `patients_details`
--
ALTER TABLE `patients_details`
  ADD PRIMARY KEY (`patients_details_id`);

--
-- Indexes for table `patient_schedule`
--
ALTER TABLE `patient_schedule`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `prenatal_records`
--
ALTER TABLE `prenatal_records`
  ADD PRIMARY KEY (`prenatal_records_id`);

--
-- Indexes for table `profile_image`
--
ALTER TABLE `profile_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_format`
--
ALTER TABLE `referral_format`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_forms`
--
ALTER TABLE `referral_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_notification`
--
ALTER TABLE `referral_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_records`
--
ALTER TABLE `referral_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_transaction`
--
ALTER TABLE `referral_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `second_trimester`
--
ALTER TABLE `second_trimester`
  ADD PRIMARY KEY (`second_trimester_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `third_trimester`
--
ALTER TABLE `third_trimester`
  ADD PRIMARY KEY (`third_trimester_id`);

--
-- Indexes for table `trimester_demo`
--
ALTER TABLE `trimester_demo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`usersId`);

--
-- Indexes for table `your_table`
--
ALTER TABLE `your_table`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ap_risk_codes`
--
ALTER TABLE `ap_risk_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `birth_experience`
--
ALTER TABLE `birth_experience`
  MODIFY `birth_experience_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `demo_message`
--
ALTER TABLE `demo_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `demo_messages`
--
ALTER TABLE `demo_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors_referral`
--
ALTER TABLE `doctors_referral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `fclt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `first_trimester`
--
ALTER TABLE `first_trimester`
  MODIFY `first_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `for_referral_patients`
--
ALTER TABLE `for_referral_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=507;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `patients_attachments`
--
ALTER TABLE `patients_attachments`
  MODIFY `attachments_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `patients_details`
--
ALTER TABLE `patients_details`
  MODIFY `patients_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `patient_schedule`
--
ALTER TABLE `patient_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `prenatal_records`
--
ALTER TABLE `prenatal_records`
  MODIFY `prenatal_records_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `profile_image`
--
ALTER TABLE `profile_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `referral_format`
--
ALTER TABLE `referral_format`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `referral_forms`
--
ALTER TABLE `referral_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=422;

--
-- AUTO_INCREMENT for table `referral_notification`
--
ALTER TABLE `referral_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1302;

--
-- AUTO_INCREMENT for table `referral_records`
--
ALTER TABLE `referral_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=397;

--
-- AUTO_INCREMENT for table `referral_transaction`
--
ALTER TABLE `referral_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT for table `second_trimester`
--
ALTER TABLE `second_trimester`
  MODIFY `second_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `third_trimester`
--
ALTER TABLE `third_trimester`
  MODIFY `third_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `trimester_demo`
--
ALTER TABLE `trimester_demo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `usersId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `your_table`
--
ALTER TABLE `your_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `demo_messages`
--
ALTER TABLE `demo_messages`
  ADD CONSTRAINT `demo_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `facilities` (`fclt_id`),
  ADD CONSTRAINT `demo_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `facilities` (`fclt_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;