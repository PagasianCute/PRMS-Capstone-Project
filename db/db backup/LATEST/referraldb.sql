-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2023 at 07:36 AM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `create_referral` (IN `new_name` VARCHAR(255), IN `new_age` VARCHAR(255), IN `new_sex` VARCHAR(255), IN `new_bdate` VARCHAR(255), IN `new_address` VARCHAR(255), IN `new_admitting_dx` VARCHAR(255), IN `new_rtpcr` VARCHAR(255), IN `new_antigen` VARCHAR(255), IN `new_clinical_ssx` VARCHAR(255), IN `new_exposure_to_covid` VARCHAR(255), IN `new_temp` VARCHAR(255), IN `new_hr` VARCHAR(255), IN `new_resp` VARCHAR(255), IN `new_bp` VARCHAR(255), IN `new_O2sat` VARCHAR(255), IN `new_O2aided` VARCHAR(255), IN `new_procedures_need` VARCHAR(255), IN `new_fh` VARCHAR(255), IN `new_ie` VARCHAR(255), IN `new_fht` VARCHAR(255), IN `new_lmp` VARCHAR(255), IN `new_edc` VARCHAR(255), IN `new_aog` VARCHAR(255), IN `new_utz` VARCHAR(255), IN `new_utz_aog` VARCHAR(255), IN `new_edd` VARCHAR(255), IN `new_enterpretation` VARCHAR(255), IN `new_diagnostic_test` VARCHAR(255), IN `new_time` VARCHAR(255), IN `new_date` VARCHAR(255), IN `new_fclt_id` INT, IN `new_referred_hospital` INT, IN `new_patients_id` INT, IN `new_referral_reason` VARCHAR(500))   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_first_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_hemoglobin_count` VARCHAR(100), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_stis_using_a_syndromic_approach` VARCHAR(255), IN `new_tetanus_containing_vaccine` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record` INT)   BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_patient` (IN `new_fname` VARCHAR(255), IN `new_mname` VARCHAR(255), IN `new_lname` VARCHAR(255), IN `new_gender` VARCHAR(255), IN `new_age` INT, IN `new_birthdate` VARCHAR(255), IN `new_contactNum` VARCHAR(20), IN `new_region` VARCHAR(255), IN `new_province` VARCHAR(255), IN `new_municipality` VARCHAR(255), IN `new_barangay` VARCHAR(255), IN `new_email` VARCHAR(255), IN `new_fclt_id` INT, IN `new_dateregistered` VARCHAR(255))   BEGIN
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
        date_registered
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
        new_dateregistered
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_second_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record` INT)   BEGIN
    
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_staff` (IN `new_fname` VARCHAR(255), IN `new_mname` VARCHAR(255), IN `new_lname` VARCHAR(255), IN `new_contactNum` VARCHAR(12), IN `new_address` VARCHAR(255), IN `new_role` VARCHAR(20), IN `new_fclt_id` INT, IN `new_img` TEXT, IN `new_birth_date` VARCHAR(100), IN `new_username` VARCHAR(255), IN `new_pwd` VARCHAR(255), IN `new_default_pwd` VARCHAR(255))   BEGIN
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
        default_pwd
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
        new_default_pwd
    );
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_third_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255), IN `new_record` INT)   BEGIN
    
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
(18, '2023-12-23', 'Ceasarean Delivery (C/S)', 'Miscarriage', 'Multiple Birth', 'No', 'Yes', 'No', 79, 1),
(19, '2023-12-23', 'Normal (N)', 'Alive', 'Single', 'No', 'No', 'Yes', 64, 1),
(20, '2023-12-21', 'Normal (N)', 'Miscarriage', 'Twins', 'Yes', 'Yes', 'Yes', 79, 2);

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
(4, 58, 343, 32, '2023-12-24', '15:17'),
(5, 58, 345, 32, '2023-12-24', '15:17'),
(6, 58, 346, 32, '2023-12-24', '15:18'),
(7, 58, 347, 32, '2023-12-24', '15:18'),
(8, 58, 348, 32, '2023-12-24', '15:47');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `fclt_id` int(11) NOT NULL,
  `fclt_name` varchar(255) NOT NULL,
  `fclt_password` varchar(255) NOT NULL,
  `fclt_ref_id` varchar(255) NOT NULL,
  `fclt_type` varchar(255) NOT NULL,
  `img_url` text NOT NULL,
  `fclt_contact` varchar(11) NOT NULL,
  `fclt_status` varchar(10) NOT NULL,
  `latitude` decimal(18,15) NOT NULL,
  `longitude` decimal(18,15) NOT NULL,
  `region_code` varchar(20) NOT NULL,
  `region` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`fclt_id`, `fclt_name`, `fclt_password`, `fclt_ref_id`, `fclt_type`, `img_url`, `fclt_contact`, `fclt_status`, `latitude`, `longitude`, `region_code`, `region`, `province`, `municipality`) VALUES
(1, 'Caraga Hospital', '$2y$10$e9OJl./loMHTgS5BJu5grOhWgjGak81GUi1LpK6W0q2.DK5usT6we', '001', 'Hospital', 'logo.png', '98768726379', 'Active', 9.784422011854875, 125.489968584703970, '13', 'REGION XIII', 'SURIGAO DEL NORTE', 'SURIGAO CITY'),
(2, 'Gigaquit RHU', '$2y$10$1NzWlXD0t/r7ya4u1MDrOOH3sbO/ZmUz9990FwRozoX.1fpdscklO', '002', 'Birthing Home', 'logo.png', '91827462721', 'Active', 9.590967457918067, 125.696986437228260, '13', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT'),
(3, 'Surigao Del Norte Provincial Hospital', '$2y$10$YWFHX4SDkT3Bp803vcm1XO.RdvBsr8sgaRRiDPjLcfVU/l5WAZtM6', '003', 'Provincial Hospital', 'logo.png', '09090909099', 'Offline', 9.632682467344730, 125.561541776867640, '', '', '', ''),
(4, 'Miranda', '$2y$10$298VYvJ767szo0IanMnkCOc42ubpxLXcOvOpGDWduA/nrSaRifOHq', '004', 'Birthing Home', 'logo.png', '09876865271', 'Offline', 9.781856142942564, 125.485960717361690, '', '', '', ''),
(5, 'Claver RHU', '$2y$10$1Ib5MTXRaF3t5cXiSPRtkuY9DzQ0gwAGYfYiAdg34dI/VLlkTR6sa', '005', 'Birthing Home', 'logo.png', '82746172634', 'Offline', 9.573334780959204, 125.732849346065150, '', '', '', '');

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

--
-- Dumping data for table `first_trimester`
--

INSERT INTO `first_trimester` (`first_trimester_id`, `check_up`, `patients_id`, `date`, `weight`, `height`, `age_of_gestation`, `blood_pressure`, `nutritional_status`, `laboratory_tests_done`, `hemoglobin_count`, `urinalysis`, `complete_blood_count`, `stis_using_a_syndromic_approach`, `tetanus_containing_vaccine`, `given_services`, `date_of_return`, `health_provider_name`, `hospital_referral`, `records_count`) VALUES
(86, 'first_checkup', 79, '2023-12-16', 'first', 'asd', 'asd', 'asd', 'Underweight', 'asd', 'asd', 'asd', 'asd', 'HIV', 'asd', 'Counseling about safe sex', '2023-12-16', 'Jezrael Salino', '', 1),
(87, 'first_checkup', 64, '2023-12-20', 'asd', 'asd', 'asd', 'asd', 'Normal', 'asd', 'asd', 'asd', 'asd', '', 'asd', 'Avoiding alcohol, tobacco, and illegal drugs', '2023-12-21', 'Jezrael Salino', '', 1),
(88, 'first_checkup', 79, '2023-12-21', 'asd', 'asd', 'asd', 'asd', 'Normal', 'asd', 'asd', '', 'asd', 'Syphilis', 'Data', 'Avoiding alcohol, tobacco, and illegal drugs', '2023-12-21', 'Jezrael Salino', '', 2);

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
(226, 2, 'yow', 1, '2023-11-21', '04:03 PM', 8, 'Seen'),
(227, 2, 'sup', 1, '2023-11-21', '04:04 PM', 8, 'Seen'),
(228, 2, 'sup', 1, '2023-11-21', '04:04 PM', 8, 'Seen'),
(229, 1, 'sheesh', 2, '2023-11-21', '04:05 PM', 7, 'Seen'),
(230, 1, 'hi love', 2, '2023-11-21', '04:05 PM', 7, 'Seen'),
(231, 2, 'hello', 1, '2023-11-21', '04:05 PM', 8, 'Seen'),
(232, 2, 'pre', 1, '2023-11-21', '04:13 PM', 7, 'Seen'),
(233, 1, 'what bro', 2, '2023-11-21', '04:18 PM', 7, 'Seen'),
(234, 1, 'what tangina mo', 2, '2023-11-21', '04:32 PM', 7, 'Seen'),
(235, 2, 'yow', 1, '2023-11-21', '06:32 PM', 7, 'Seen'),
(236, 2, 'hey', 1, '2023-11-21', '07:42 PM', 7, 'Seen'),
(237, 1, 'yow', 2, '2023-11-21', '07:42 PM', 7, 'Seen'),
(238, 2, 'sup', 1, '2023-11-22', '12:06 PM', 7, 'Seen'),
(239, 1, 'sup bro', 3, '2023-11-22', '05:43 PM', 7, 'Seen'),
(240, 1, 'sup', 3, '2023-11-22', '05:55 PM', 7, 'Seen'),
(241, 2, 'yow', 3, '2023-11-22', '06:01 PM', 7, 'Seen'),
(242, 2, 'yow', 3, '2023-11-22', '06:03 PM', 7, 'Seen'),
(243, 2, 'hey', 1, '2023-11-22', '06:03 PM', 7, 'Seen'),
(244, 1, 'sup', 2, '2023-11-22', '06:04 PM', 7, 'Seen'),
(245, 2, 'sup', 1, '2023-11-22', '06:04 PM', 7, 'Seen'),
(246, 1, 'sup', 2, '2023-11-22', '06:06 PM', 7, 'Seen'),
(247, 2, 'sup', 1, '2023-11-22', '06:07 PM', 7, 'Seen'),
(248, 1, 'yow', 3, '2023-11-22', '06:15 PM', 7, 'Seen'),
(249, 1, 'hey', 2, '2023-11-22', '06:17 PM', 7, 'Seen'),
(250, 2, 'pre', 1, '2023-11-22', '06:18 PM', 7, 'Seen'),
(251, 2, 'hey', 1, '2023-11-22', '06:23 PM', 7, 'Seen'),
(252, 1, 'yow', 2, '2023-11-22', '06:23 PM', 7, 'Seen'),
(253, 2, 'hey', 1, '2023-11-22', '06:25 PM', 7, 'Seen'),
(254, 2, 'yow', 1, '2023-11-22', '06:48 PM', 7, 'Seen'),
(255, 1, 'hey', 2, '2023-11-22', '06:52 PM', 7, 'Seen'),
(256, 1, 'hey', 2, '2023-11-22', '06:52 PM', 7, 'Seen'),
(257, 2, 'yow', 1, '2023-11-22', '07:04 PM', 7, 'Seen'),
(258, 1, 'ano', 2, '2023-11-23', '11:41 AM', 7, 'Seen'),
(259, 1, 'yow', 2, '2023-11-23', '05:02 PM', 7, 'Seen'),
(260, 1, 'i love you yancy', 3, '2023-11-23', '05:02 PM', 7, 'Seen'),
(261, 1, 'tf', 3, '2023-11-23', '06:13 PM', 7, 'Seen'),
(262, 1, 'asdkiagdiadb', 4, '2023-11-23', '07:03 PM', 7, 'Sent'),
(263, 3, 'HAHHAHAHAH', 1, '2023-11-23', '08:58 PM', 7, 'Seen'),
(264, 2, 'ano', 1, '2023-11-23', '10:23 PM', 7, 'Seen'),
(265, 1, 'yow', 2, '2023-11-24', '01:25 AM', 7, 'Seen'),
(266, 1, 'sup', 2, '2023-11-24', '01:26 AM', 7, 'Seen'),
(267, 1, 'yow', 2, '2023-11-24', '01:30 AM', 7, 'Seen'),
(268, 1, 'diwow', 2, '2023-11-24', '01:34 AM', 7, 'Seen'),
(269, 1, 'yow', 2, '2023-11-24', '01:38 AM', 7, 'Seen'),
(270, 2, 'yow', 1, '2023-11-24', '01:38 AM', 7, 'Seen'),
(271, 3, 'sup', 2, '2023-11-24', '01:41 AM', 7, 'Seen'),
(276, 3, 'deeeym', 1, '2023-11-24', '02:11 AM', 7, 'Seen'),
(277, 2, 'pre', 3, '2023-11-24', '02:35 AM', 7, 'Seen'),
(278, 3, 'Hi miss pwede paisa?', 4, '2023-11-24', '02:36 AM', 7, 'Sent'),
(279, 3, 'Naa moy bbgurl diha available?', 13, '2023-11-24', '02:36 AM', 7, 'Sent'),
(280, 1, 'what bro', 2, '2023-11-24', '02:51 AM', 7, 'Seen'),
(281, 1, 'yow', 2, '2023-11-24', '04:20 AM', 7, 'Seen'),
(282, 2, 'sup', 1, '2023-11-24', '04:20 AM', 7, 'Seen'),
(283, 1, 'hahhahaah', 4, '2023-11-24', '04:58 AM', 7, 'Sent'),
(284, 1, 'yow', 2, '2023-11-24', '08:53 AM', 7, 'Seen'),
(285, 2, 'hey', 1, '2023-11-24', '08:54 AM', 7, 'Seen'),
(286, 1, 'u', 3, '2023-11-24', '08:55 AM', 7, 'Seen'),
(287, 2, 'yow', 1, '2023-11-24', '02:01 PM', 7, 'Seen'),
(288, 1, 'hey', 2, '2023-11-24', '02:01 PM', 7, 'Seen'),
(289, 2, 'sup', 1, '2023-11-24', '02:02 PM', 7, 'Seen'),
(290, 2, 'yow', 1, '2023-11-24', '02:02 PM', 7, 'Seen'),
(291, 1, 'Pre', 2, '2023-11-24', '04:10 PM', 7, 'Seen'),
(292, 1, 'Sup', 2, '2023-11-24', '04:10 PM', 7, 'Seen'),
(293, 2, 'goods lang', 1, '2023-11-24', '04:10 PM', 7, 'Seen'),
(294, 1, 'Ahh okay', 2, '2023-11-24', '04:13 PM', 8, 'Seen'),
(295, 2, 'Bruh', 1, '2023-11-24', '04:40 PM', 8, 'Seen'),
(296, 2, 'gyff', 3, '2023-12-04', '11:56 PM', 7, 'Seen'),
(297, 1, 'ffnghmj', 2, '2023-12-05', '12:20 AM', 7, 'Seen'),
(298, 2, 'vcbn', 1, '2023-12-05', '12:22 AM', 7, 'Seen'),
(299, 2, 'HAHAHAHHAHHAAHA', 1, '2023-12-05', '12:54 AM', 7, 'Seen'),
(300, 2, 'ITS ME JEZMAHBOIIII', 1, '2023-12-05', '12:55 AM', 7, 'Seen'),
(301, 1, 'hello', 2, '2023-12-06', '11:35 AM', 7, 'Seen'),
(302, 1, 'sup', 2, '2023-12-09', '10:52 AM', 8, 'Seen'),
(303, 1, 'sup baaaaaa', 2, '2023-12-09', '10:52 AM', 8, 'Seen'),
(304, 2, 'hello', 1, '2023-12-18', '09:58 PM', 7, 'Seen'),
(305, 2, 'asadad', 1, '2023-12-18', '09:58 PM', 7, 'Seen'),
(306, 1, 'hello', 2, '2023-12-18', '10:46 PM', 8, 'Seen'),
(307, 3, 'yow', 2, '2023-12-22', '03:15 PM', 10, 'Seen'),
(308, 2, 'ey', 3, '2023-12-23', '09:56 PM', 7, 'Seen'),
(309, 3, 'syp', 2, '2023-12-23', '10:02 PM', 10, 'Sent');

-- --------------------------------------------------------

--
-- Table structure for table `patiens_schedule`
--

CREATE TABLE `patiens_schedule` (
  `id` int(11) NOT NULL,
  `patients_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patiens_schedule`
--

INSERT INTO `patiens_schedule` (`id`, `patients_id`, `date`, `type`) VALUES
(1, 64, '12/07/2023', 'Prenatal'),
(2, 65, '12/07/2023', 'Prenatal');

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
  `date_registered` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `fname`, `mname`, `lname`, `gender`, `age`, `birthdate`, `contact`, `region`, `province`, `municipality`, `barangay`, `email`, `fclt_id`, `date_registered`) VALUES
(64, 'Jezrael', 'Juarez', 'Salino', 'Male', 15, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28'),
(65, 'Jiffer', 'Juarez', 'Salino', 'Male', 20, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28'),
(70, 'asd', 'asd', 'sad', 'Female', 20, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28'),
(71, 'hahah', 'hahah', 'hahahh', 'Female', 20, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28'),
(76, 'asdad', 'asd', 'asdad', 'Female', 20, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28'),
(77, 'Sarah', 'll', 'Jane', 'Female', 20, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO', 'jezraelsalino@gmail.com', 2, '2023-11-28'),
(79, 'Mark', '', 'Sitoy', 'Male', 12, '2023-12-02', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'Kinabutan', 'jezraelsalino@gmail.com', 2, '2023-11-28'),
(80, 'Andrei', '', 'Blanco', 'Male', 12, '2023-12-02', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'LAHI', 'andreiblanco@yahoo.com', 2, '2023-12-20'),
(81, 'Mark Ivan', '', 'Blanco', 'Male', 12, '2023-12-01', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'MAHANUB', 'markblanco@yahoo.com', 2, '2023-12-20'),
(82, 'Nyko', '', 'Jumamil', 'Male', 18, '2023-12-21', '09090676022', 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'LAHI', 'nyko@yahoo.com', 2, '2023-12-21');

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

--
-- Dumping data for table `patients_details`
--

INSERT INTO `patients_details` (`patients_details_id`, `petsa_ng_unang_checkup`, `edad`, `timbang`, `taas`, `kalagayan_ng_kalusugan`, `petsa_ng_huling_regla`, `kailan_ako_manganganak`, `pang_ilang_pagbubuntis`, `patients_id`, `records_count`) VALUES
(115, '2023-12-23', '12', 'asdad', 'asdd', 'asdd', '2023-12-30', '2023-12-23', '1', 79, 1),
(116, '2023-12-16', '15', '', '', '', '', '', '0', 64, 1),
(117, '', '30', '', '', '', '', '', '0', 64, 2),
(118, '2023-12-16', '15', '', '', '', '', '', '0', 64, 1),
(119, '2023-12-23', '12', 'asd', 'asd', 'asd', '2023-12-30', '2023-12-30', '2', 79, 2),
(120, '2023-12-22', '12', '', '', '', '', '', '0', 79, 3);

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
  `records_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prenatal_records`
--

INSERT INTO `prenatal_records` (`prenatal_records_id`, `patients_id`, `fclt_id`, `date`, `time`, `records_count`) VALUES
(224, 79, 2, '2023-12-19', '19:38:19', 1),
(226, 64, 2, '2023-12-20', '14:04:40', 1),
(227, 64, 2, '2023-12-20', '14:13:26', 2),
(228, 79, 2, '2023-12-20', '17:24:04', 2),
(229, 79, 2, '2023-12-21', '18:43:57', 3);

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
(339, 'Sitoy, Mark ', '69', 'Male', '2023-12-02', 'Kinabutan', 'asd', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(342, 'Sitoy, Mark ', '69', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(343, 'Jane, Sarah ll', '20', 'Female', '2023-12-21', 'SAN ISIDRO', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(344, 'asdad', '12', 'Male', '2023-12-21', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(345, 'asd', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(346, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(347, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(348, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(349, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(350, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(351, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(352, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(353, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(354, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(355, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(356, 'Jumamil, Nyko ', '18', 'Male', '2023-12-21', 'LAHI', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(357, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Age younger than 15 years or older than 35 years (Code 1)'),
(358, 'Sitoy, Mark ', '12', 'Male', '2023-12-02', 'Kinabutan', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'asdadad'),
(359, 'Salino, Jezrael Juarez', '15', 'Male', '2023-12-21', 'SAN ISIDRO', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'First Pregnancy (Code 6)'),
(360, 'Salino, Jiffer Juarez', '20', 'Male', '2023-12-21', 'SAN ISIDRO', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Hypertension (Code 16)'),
(361, 'Salino, Jiffer Juarez', '20', 'Male', '2023-12-21', 'SAN ISIDRO', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Hypertension (Code 16)');

-- --------------------------------------------------------

--
-- Table structure for table `referral_notification`
--

CREATE TABLE `referral_notification` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `rfrrl_id` int(11) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `is_displayed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_notification`
--

INSERT INTO `referral_notification` (`id`, `message`, `rfrrl_id`, `fclt_id`, `date`, `time`, `is_displayed`) VALUES
(960, 'Referral Accepted', 356, 1, '2023-12-22', '09:20 PM', 0),
(961, 'Patient Arrived', 356, 1, '2023-12-22', '09:20 PM', 0),
(962, 'Referral Declined', 342, 1, '2023-12-22', '09:22 PM', 0),
(963, 'Referral Declined', 343, 1, '2023-12-22', '09:22 PM', 0),
(964, 'Referral Declined', 344, 1, '2023-12-22', '09:22 PM', 0),
(965, 'Referral Accepted', 342, 1, '2023-12-22', '09:22 PM', 0),
(966, 'Patient Arrived', 342, 1, '2023-12-22', '09:22 PM', 0),
(967, 'Referral Accepted', 343, 1, '2023-12-22', '09:26 PM', 0),
(968, 'Patient Arrived', 343, 1, '2023-12-22', '09:27 PM', 0),
(969, 'Referral Accepted', 344, 1, '2023-12-22', '09:28 PM', 0),
(970, 'Patient Arrived', 344, 1, '2023-12-22', '09:28 PM', 0),
(971, 'Referral Declined', 344, 1, '2023-12-22', '09:28 PM', 0),
(972, 'Referral Declined', 344, 1, '2023-12-22', '09:28 PM', 0),
(973, 'Referral Declined', 344, 1, '2023-12-22', '09:28 PM', 0),
(974, 'Referral Declined', 343, 1, '2023-12-22', '09:29 PM', 0),
(975, 'Referral Declined', 342, 1, '2023-12-22', '09:29 PM', 0),
(976, 'Referral Declined', 356, 1, '2023-12-22', '09:29 PM', 0),
(977, 'Referral Declined', 352, 1, '2023-12-22', '09:29 PM', 0),
(978, 'Referral Declined', 351, 1, '2023-12-22', '09:29 PM', 0),
(979, 'Referral Declined', 350, 1, '2023-12-22', '09:29 PM', 0),
(980, 'Referral Declined', 349, 1, '2023-12-22', '09:29 PM', 0),
(981, 'Referral Declined', 348, 1, '2023-12-22', '09:29 PM', 0),
(982, 'Referral Declined', 347, 1, '2023-12-22', '09:29 PM', 0),
(983, 'Referral Declined', 346, 1, '2023-12-22', '09:29 PM', 0),
(984, 'Referral Declined', 345, 1, '2023-12-22', '09:29 PM', 0),
(985, 'Referral Accepted', 342, 1, '2023-12-22', '09:29 PM', 0),
(986, 'Patient Arrived', 342, 1, '2023-12-22', '09:29 PM', 0),
(987, 'Referral Accepted', 343, 1, '2023-12-22', '09:31 PM', 0),
(988, 'Patient Arrived', 343, 1, '2023-12-22', '09:31 PM', 0),
(989, 'Referral Accepted', 344, 1, '2023-12-22', '09:31 PM', 0),
(990, 'Patient Arrived', 344, 1, '2023-12-22', '09:31 PM', 0),
(991, 'Referral Accepted', 345, 1, '2023-12-22', '09:32 PM', 0),
(992, 'Patient Arrived', 345, 1, '2023-12-22', '09:32 PM', 0),
(993, 'Referral Accepted', 346, 1, '2023-12-22', '09:35 PM', 0),
(994, 'Patient Arrived', 346, 1, '2023-12-22', '09:35 PM', 0),
(995, 'Referral Accepted', 347, 1, '2023-12-23', '01:13 PM', 0),
(996, 'Referral Accepted', 339, 3, '2023-12-23', '09:17 PM', 0),
(997, 'Referral Accepted', 353, 3, '2023-12-23', '09:40 PM', 0),
(998, 'Referral Declined', 353, 3, '2023-12-23', '10:05 PM', 0),
(999, 'Referral Declined', 339, 3, '2023-12-23', '10:05 PM', 0),
(1000, 'Referral Accepted', 339, 3, '2023-12-23', '10:13 PM', 0),
(1001, 'Referral Declined', 353, 3, '2023-12-23', '10:23 PM', 0),
(1002, 'Referral Declined', 354, 3, '2023-12-23', '10:25 PM', 0),
(1003, 'Referral Declined', 347, 1, '2023-12-23', '10:29 PM', 0),
(1004, 'Referral Declined', 347, 1, '2023-12-23', '10:29 PM', 0),
(1005, 'Referral Declined', 346, 1, '2023-12-23', '10:29 PM', 0),
(1006, 'Referral Declined', 345, 1, '2023-12-23', '10:29 PM', 0),
(1007, 'Referral Declined', 344, 1, '2023-12-23', '10:29 PM', 0),
(1008, 'Referral Declined', 343, 1, '2023-12-23', '10:29 PM', 0),
(1009, 'Referral Declined', 342, 1, '2023-12-23', '10:30 PM', 0),
(1010, 'Referral Accepted', 342, 1, '2023-12-23', '10:30 PM', 0),
(1011, 'Patient Arrived', 342, 1, '2023-12-23', '10:31 PM', 0),
(1012, 'Referral Accepted', 359, 3, '2023-12-24', '01:25 PM', 0),
(1013, 'Patient Arrived', 339, 3, '2023-12-24', '01:28 PM', 0),
(1014, 'Referral Accepted', 353, 1, '2023-12-24', '01:40 PM', 0),
(1015, 'Referral Accepted', 344, 1, '2023-12-24', '01:44 PM', 0),
(1016, 'Referral Accepted', 355, 3, '2023-12-24', '01:49 PM', 0),
(1017, 'Referral Accepted', 360, 1, '2023-12-24', '01:50 PM', 0),
(1018, 'Referral Accepted', 343, 1, '2023-12-24', '03:42 PM', 0),
(1019, 'Referral Accepted', 345, 1, '2023-12-24', '03:44 PM', 0),
(1020, 'Referral Accepted', 346, 1, '2023-12-24', '03:45 PM', 0),
(1021, 'Referral Accepted', 347, 1, '2023-12-24', '03:45 PM', 0);

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
(314, 2, 339, 79, '2023-12-20', '05:00 PM', '3', 'Accepted'),
(317, 2, 342, 79, '2023-12-21', '01:35 PM', '1', 'Accepted'),
(318, 2, 343, 77, '2023-12-21', '01:40 PM', '1', 'Accepted'),
(319, 2, 344, 0, '2023-12-21', '02:17 PM', '1', 'Accepted'),
(320, 2, 345, 0, '2023-12-21', '02:23 PM', '1', 'Accepted'),
(321, 2, 346, 79, '2023-12-21', '04:27 PM', '1', 'Accepted'),
(322, 2, 347, 79, '2023-12-21', '04:29 PM', '1', 'Accepted'),
(323, 2, 348, 79, '2023-12-21', '04:31 PM', '1', 'Sent To a Doctor'),
(324, 2, 349, 79, '2023-12-21', '04:35 PM', '1', 'Pending'),
(325, 2, 350, 79, '2023-12-21', '04:36 PM', '1', 'Pending'),
(326, 2, 351, 79, '2023-12-21', '04:40 PM', '1', 'Pending'),
(327, 2, 352, 79, '2023-12-21', '04:41 PM', '1', 'Pending'),
(328, 2, 353, 79, '2023-12-21', '04:42 PM', '3', 'Accepted'),
(329, 2, 354, 79, '2023-12-21', '04:44 PM', '3', 'Declined'),
(330, 2, 355, 79, '2023-12-21', '04:48 PM', '3', 'Accepted'),
(331, 2, 356, 82, '2023-12-21', '06:42 PM', '1', 'Pending'),
(332, 2, 357, 79, '2023-12-23', '11:53 PM', '1', 'Pending'),
(333, 2, 358, 79, '2023-12-23', '11:53 PM', '1', 'Pending'),
(334, 2, 359, 64, '2023-12-24', '01:10 PM', '3', 'Accepted'),
(335, 2, 360, 65, '2023-12-24', '01:33 PM', '1', 'Accepted'),
(336, 2, 361, 65, '2023-12-24', '01:33 PM', '3', 'Pending');

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
(138, 1, 307, 'Accepted', '2023-12-02', '06:50 AM', 'NULL', 'Arrived', '07:09 AM', '', '', '', '07:11 AM'),
(139, 1, 317, 'Accepted', '2023-12-02', '06:50 AM', 'NULL', 'Arrived', '07:09 AM', '', '', '', '07:18 AM'),
(140, 1, 318, 'Accepted', '2023-12-02', '07:13 AM', 'NULL', 'Arrived', '07:31 AM', '', '', '', '07:17 AM'),
(141, 1, 319, 'Accepted', '2023-12-02', '07:16 AM', 'NULL', 'Arrived', '07:35 AM', '', '', '', '07:17 AM'),
(142, 1, 320, 'Accepted', '2023-12-02', '07:17 AM', 'NULL', 'Arrived', '07:41 AM', '', '', '', '07:17 AM'),
(143, 1, 321, 'Accepted', '2023-12-02', '07:18 AM', 'NULL', 'Arrived', '07:42 AM', '', '', '', '07:26 AM'),
(144, 1, 322, 'Accepted', '2023-12-02', '07:26 AM', 'NULL', 'Arrived', '07:50 AM', '', '', '', '12:00 AM'),
(145, 1, 323, 'Accepted', '2023-12-02', '07:29 AM', 'NULL', 'Arrived', '07:52 AM', '', '', '', '10:51 AM'),
(146, 1, 324, 'Accepted', '2023-12-05', '12:19 AM', 'NULL', 'Arrived', '12:42 AM', '', '', '', '11:13 AM'),
(147, 1, 327, 'Accepted', '2023-12-06', '11:13 AM', 'NULL', 'Arrived', '11:37 AM', '', '', '', '11:13 AM'),
(148, 1, 331, 'Accepted', '2023-12-09', '02:35 AM', 'NULL', 'Arrived', '02:58 AM', '', '', '', '10:48 AM'),
(149, 1, 325, 'Accepted', '2023-12-09', '02:41 AM', 'NULL', 'Arrived', '03:04 AM', '', '', '', '10:51 AM'),
(150, 1, 326, 'Accepted', '2023-12-09', '02:47 AM', 'NULL', 'Arrived', '03:11 AM', '', '', '', '10:50 AM'),
(151, 1, 329, 'Accepted', '2023-12-09', '10:51 AM', 'NULL', 'Arriving', '11:14 AM', '', '', '', ''),
(152, 1, 328, 'Accepted', '2023-12-09', '11:14 AM', 'NULL', 'Arriving', '11:37 AM', '', '', '', ''),
(153, 1, 333, 'Accepted', '2023-12-18', '10:46 PM', 'NULL', 'Arrived', '11:09 PM', '', '', '', '10:46 PM'),
(154, 1, 340, 'Accepted', '2023-12-20', '05:46 PM', 'NULL', 'Arrived', '06:10 PM', '', '', '', '05:47 PM'),
(178, 3, 339, 'Accepted', '2023-12-23', '10:13 PM', 'NULL', 'Arrived', '10:24 PM', 'Goods lang', 'Sarah Jane', '2023-12-24', '13:28'),
(179, 3, 353, 'Declined', '2023-12-23', '10:23 PM', 'asadad', '', '', '', '', '', ''),
(180, 3, 354, 'Declined', '2023-12-23', '10:25 PM', 'asdad', '', '', '', '', '', ''),
(181, 1, 342, 'Accepted', '2023-12-23', '10:30 PM', 'NULL', 'Arrived', '10:53 PM', 'Goods lang', 'Jezrael Salino', '2023-12-23', '22:31'),
(182, 3, 359, 'Accepted', '2023-12-24', '01:25 PM', 'NULL', 'Arriving', '01:37 PM', '', '', '', ''),
(183, 1, 353, 'Accepted', '2023-12-24', '01:40 PM', 'NULL', 'Arriving', '02:03 PM', '', '', '', ''),
(184, 1, 344, 'Accepted', '2023-12-24', '01:44 PM', 'NULL', 'Arriving', '02:07 PM', '', '', '', ''),
(185, 3, 355, 'Accepted', '2023-12-24', '01:49 PM', 'NULL', 'Arriving', '02:00 PM', '', '', '', ''),
(186, 1, 360, 'Accepted', '2023-12-24', '01:50 PM', 'NULL', 'Arriving', '02:13 PM', '', '', '', ''),
(187, 1, 343, 'Accepted', '2023-12-24', '03:42 PM', 'NULL', 'Arriving', '04:05 PM', '', '', '', ''),
(188, 1, 345, 'Accepted', '2023-12-24', '03:44 PM', 'NULL', 'Arriving', '04:08 PM', '', '', '', ''),
(189, 1, 346, 'Accepted', '2023-12-24', '03:45 PM', 'NULL', 'Arriving', '04:09 PM', '', '', '', ''),
(190, 1, 347, 'Accepted', '2023-12-24', '03:45 PM', 'NULL', 'Arriving', '04:09 PM', '', '', '', '');

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

--
-- Dumping data for table `second_trimester`
--

INSERT INTO `second_trimester` (`second_trimester_id`, `check_up`, `patients_id`, `date`, `weight`, `height`, `age_of_gestation`, `blood_pressure`, `nutritional_status`, `given_advise`, `laboratory_tests_done`, `urinalysis`, `complete_blood_count`, `given_services`, `date_of_return`, `health_provider_name`, `hospital_referral`, `records_count`) VALUES
(18, 'first_checkup', 79, '2023-12-16', 'second', 'asd', 'asd', 'asd', 'Underweight', 'asd', 'asd', 'asd', 'asd', 'Counseling about safe sex', '2023-12-23', 'Jezrael Salino', '', 1),
(19, 'first_checkup', 64, '2023-12-20', 'asd', 'asd', 'asd', 'asd', 'Normal', 'asd', 'asd', 'asd', 'asd', 'Avoiding alcohol, tobacco, and illegal drugs', '2023-12-23', 'Jezrael Salino', '', 1);

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
  `birth_date` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `img` varchar(300) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `default_pwd` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `fname`, `mname`, `lname`, `username`, `contact_num`, `birth_date`, `address`, `role`, `img`, `fclt_id`, `pwd`, `default_pwd`) VALUES
(32, 'Peachy', '', 'Lucero', '', '09090676022', '2002-06-11', 'Brgy 5', 'Nurse', '7e618381_6543111832df2_file.png', 1, '', ''),
(54, 'Claire', '', 'Roflo', '', '09090676022', '2002-06-11', 'Nueva', 'Midwife', 'b3ef4f6b_404387151_1982226245486627_8156855323126660934_n.jpg', 2, '', ''),
(55, 'Norberto', '', 'Bruzon Jr.', '', '09090676022', '2002-06-11', 'Mat-i', 'Doctor', '66fd1158_33639153_594775020895295_1301565684156727296_n.jpg', 2, '', ''),
(56, 'Sarah', '', 'Dahug', '', '09090676022', '2002-06-11', 'Brgy. Luna', 'Midwife', 'd998b3a8_391236895_2029229007422557_5936945941806367658_n.jpg', 2, '', ''),
(57, 'Anne ', '', 'Hyacinth ', '', '09090676022', '2002-06-11', 'Canada', 'Nurse', '53f89f75_319912105_419416400297911_6141335392321441886_n.jpg', 2, '', ''),
(58, 'Yancy', '', 'Liquiran', '', '0909090909', '2002-06-11', 'Parang', 'Doctor', 'dc2139bd_received_1715646048934238.jpeg', 1, '', ''),
(60, 'Yancy', '', 'Liquiran', '', '09090676022', '2002-06-11', 'Parang', 'Doctor', 'cf3719f5_336745583_3364173423798790_5286193800653087590_n.jpg', 2, '', ''),
(61, 'Kirt', 'Aranna', 'Labarite', '', '09090676022', '2002-06-11', 'Bad-as', 'Doctor', 'c17cec3c_387322624_1421331255312706_1520593008633101804_n.jpg', 2, '', ''),
(106, 'asd', 'asd', 'ads', '', 'asd', '2002-06-11', 'asd', 'Midwife', '05c82554591b226d_1.png', 2, '', ''),
(107, 'asdd', 'asdd', 'asda', '', 'asda', '2002-06-11', 'asd', 'Nurse', '869d0a7ee0102605_image.png', 2, '', ''),
(108, 'Yeah', '123', '123', '', '123', '2002-06-11', '123', 'Nurse', 'e972a15c77f43439_image.png', 1, '', ''),
(114, 'Jezrael ', 'Juarez', 'Salino', 'Jezmahboi', '09090676022', '2002-06-11', 'SAN ISIDRO', 'Doctor', '475dc8c9fa32cb30_Snapchat-595984087.png', 1, '', '');

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

--
-- Dumping data for table `third_trimester`
--

INSERT INTO `third_trimester` (`third_trimester_id`, `check_up`, `patients_id`, `date`, `weight`, `height`, `age_of_gestation`, `blood_pressure`, `nutritional_status`, `given_advise`, `laboratory_tests_done`, `urinalysis`, `complete_blood_count`, `given_services`, `date_of_return`, `health_provider_name`, `hospital_referral`, `records_count`) VALUES
(12, 'first_checkup', 79, '2023-12-23', 'third', 'asd', 'asd', 'asd', '', 'asd', 'asd', 'asd', 'asd', 'Counseling about proper diet', '2023-12-23', 'Jezrael Salino', '', 1),
(13, 'first_checkup', 64, '2023-12-21', 'asd', 'asd', 'asd', 'asd', '', 'asd', 'asd', 'asd', 'asd', 'Counseling about proper diet', '2023-12-23', 'Jezrael Salino', '', 1);

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
(9, 'REGION XIII', 'SURIGAO DEL NORTE', 'GIGAQUIT', 'SAN ISIDRO');

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
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patiens_schedule`
--
ALTER TABLE `patiens_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients_details`
--
ALTER TABLE `patients_details`
  ADD PRIMARY KEY (`patients_details_id`);

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
  MODIFY `birth_experience_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `fclt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `first_trimester`
--
ALTER TABLE `first_trimester`
  MODIFY `first_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=310;

--
-- AUTO_INCREMENT for table `patiens_schedule`
--
ALTER TABLE `patiens_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `patients_details`
--
ALTER TABLE `patients_details`
  MODIFY `patients_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `prenatal_records`
--
ALTER TABLE `prenatal_records`
  MODIFY `prenatal_records_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=362;

--
-- AUTO_INCREMENT for table `referral_notification`
--
ALTER TABLE `referral_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1022;

--
-- AUTO_INCREMENT for table `referral_records`
--
ALTER TABLE `referral_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT for table `referral_transaction`
--
ALTER TABLE `referral_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `second_trimester`
--
ALTER TABLE `second_trimester`
  MODIFY `second_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `third_trimester`
--
ALTER TABLE `third_trimester`
  MODIFY `third_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
