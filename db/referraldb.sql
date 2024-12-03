-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2023 at 08:16 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

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
    SELECT COUNT(*)
    INTO current_count
    FROM prenatal_records
    WHERE patients_id = new_patients_id;

    SET current_count = current_count;
    
    SELECT *
    FROM prenatal_records
    INNER JOIN birth_experience ON birth_experience.patients_id = prenatal_records.patients_id
        AND birth_experience.records_count = prenatal_records.records_count
    WHERE birth_experience.patients_id = new_patients_id
        AND birth_experience.records_count = current_count ORDER BY birth_experience.records_count DESC LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_patients_details` (IN `new_patients_id` INT)   BEGIN
    DECLARE current_count INT;

    SELECT COUNT(*)
    INTO current_count
    FROM prenatal_records
    WHERE patients_id = new_patients_id;

    SET current_count = current_count;
    
    SELECT *
    FROM prenatal_records
    INNER JOIN patients_details ON patients_details.patients_id = prenatal_records.patients_id
        AND patients_details.records_count = prenatal_records.records_count
    WHERE patients_details.patients_id = new_patients_id
        AND patients_details.records_count = current_count ORDER BY patients_details.records_count DESC LIMIT 1;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_birth_experience` (IN `new_patient_id` INT, IN `new_date_of_delivery` VARCHAR(100), IN `new_type_of_delivery` VARCHAR(255), IN `new_birth_outcome` VARCHAR(255), IN `new_number_of_children_delivered` VARCHAR(100), IN `new_pregnancy_hypertension` VARCHAR(255), IN `new_preeclampsia_eclampsia` VARCHAR(255), IN `new_bleeding_during_pregnancy` VARCHAR(255))   BEGIN
	DECLARE current_count INT;

    SELECT COUNT(*)
    INTO current_count
    FROM birth_experience
    WHERE patients_id = new_patient_id;
    
    SET current_count = current_count + 1;
    
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
        current_count
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_first_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_hemoglobin_count` VARCHAR(100), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_stis_using_a_syndromic_approach` VARCHAR(255), IN `new_tetanus_containing_vaccine` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255))   BEGIN
    DECLARE current_count INT;

    SELECT COUNT(*) INTO current_count
    FROM first_trimester
    WHERE patients_id = new_patient_id
      AND check_up = new_checkup;

    -- Check if the patient and check_up combination already exists
    IF current_count > 0 THEN
        SET current_count = current_count + 1;
    ELSE
        SET current_count = 1;
    END IF;

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
        current_count
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_patient` (IN `new_fname` VARCHAR(255), IN `new_mname` VARCHAR(255), IN `new_lname` VARCHAR(255), IN `new_contactNum` VARCHAR(20), IN `new_address` VARCHAR(255), IN `new_fclt_id` INT)   BEGIN
    INSERT INTO patients (
        fname,
        mname,
        lname,
        contactNum,
        address,
        fclt_id
    ) VALUES (
        new_fname,
        new_mname,
        new_lname,
        new_contactNum,
        new_address,
        new_fclt_id
    );
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_patients_details` (IN `new_petsa_ng_unang_checkup` VARCHAR(100), IN `new_edad` VARCHAR(100), IN `new_timbang` VARCHAR(100), IN `new_taas` VARCHAR(100), IN `new_kalagayan_ng_kalusugan` VARCHAR(255), IN `new_petsa_ng_huling_regla` VARCHAR(100), IN `new_kailan_ako_manganganak` VARCHAR(100), IN `new_pang_ilang_pagbubuntis` INT, IN `new_patient_id` INT)   BEGIN
    DECLARE current_count INT;

    SELECT COUNT(*)
    INTO current_count
    FROM patients_details
    WHERE patients_id = new_patient_id;

    SET current_count = current_count + 1;

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
        current_count
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_second_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255))   BEGIN
	DECLARE current_count INT;

	SELECT COUNT(*) INTO current_count
    FROM second_trimester
    WHERE patients_id = new_patient_id
      AND check_up = new_checkup;

    -- Check if the patient and check_up combination already exists
    IF current_count > 0 THEN
        SET current_count = current_count + 1;
    ELSE
        SET current_count = 1;
    END IF;
    
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
        current_count
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_third_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255))   BEGIN
	DECLARE current_count INT;

    SELECT COUNT(*) INTO current_count
    FROM third_trimester
    WHERE patients_id = new_patient_id
      AND check_up = new_checkup;

    -- Check if the patient and check_up combination already exists
    IF current_count > 0 THEN
        SET current_count = current_count + 1;
    ELSE
        SET current_count = 1;
    END IF;
    
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
        current_count
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_birth_experience` (IN `new_patient_id` INT, IN `new_date_of_delivery` VARCHAR(100), IN `new_type_of_delivery` VARCHAR(255), IN `new_birth_outcome` VARCHAR(255), IN `new_number_of_children_delivered` VARCHAR(100), IN `new_pregnancy_hypertension` VARCHAR(255), IN `new_preeclampsia_eclampsia` VARCHAR(255), IN `new_bleeding_during_pregnancy` VARCHAR(255))   BEGIN
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
        patients_id = new_patient_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_first_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_hemoglobin_count` VARCHAR(100), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_stis_using_a_syndromic_approach` VARCHAR(255), IN `new_tetanus_containing_vaccine` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255))   BEGIN
    UPDATE first_trimester
    SET
        check_up = new_checkup,
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
        patients_id = new_patient_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_patients_details` (IN `new_petsa_ng_unang_checkup` VARCHAR(100), IN `new_edad` VARCHAR(100), IN `new_timbang` VARCHAR(100), IN `new_taas` VARCHAR(100), IN `new_kalagayan_ng_kalusugan` VARCHAR(255), IN `new_petsa_ng_huling_regla` VARCHAR(100), IN `new_kailan_ako_manganganak` VARCHAR(100), IN `new_pang_ilang_pagbubuntis` INT, IN `new_patient_id` INT)   BEGIN
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
        patients_id = new_patient_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_second_trimester` (IN `new_checkup` VARCHAR(255), IN `new_patient_id` INT, IN `new_date` VARCHAR(100), IN `new_weight` VARCHAR(100), IN `new_height` VARCHAR(100), IN `new_age_of_gestation` VARCHAR(100), IN `new_blood_pressure` VARCHAR(255), IN `new_nutritional_status` VARCHAR(255), IN `new_given_advise` VARCHAR(255), IN `new_laboratory_tests_done` VARCHAR(255), IN `new_urinalysis` VARCHAR(255), IN `new_complete_blood_count` VARCHAR(255), IN `new_given_services` VARCHAR(255), IN `new_date_of_return` VARCHAR(100), IN `new_health_provider_name` VARCHAR(255), IN `new_hospital_referral` VARCHAR(255))   BEGIN
    UPDATE second_trimester
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
 --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `fclt_id` int(11) NOT NULL,
  `fclt_name` varchar(255) NOT NULL,
  `fclt_password` varchar(255) NOT NULL,
  `fclt_ref_id` varchar(255) NOT NULL,
  `fclt_type` varchar(255) NOT NULL,
  `fclt_address` varchar(255) NOT NULL,
  `img_url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user1` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `user2` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `fclt_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

-------------------------------------------------------

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
--------------------------------------------------------

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
- --------------------------------------------------------

--
-- Table structure for table `profile_image`
--

CREATE TABLE `profile_image` (
  `id` int(11) NOT NULL,
  `img_path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile_image`

-- Table structure for table `referral_format`
--

CREATE TABLE `referral_format` (
  `id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_format`
--

--
-- Table structure for table `referral_forms`
--

CREATE TABLE `referral_forms` (
  `id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Vs` varchar(255) DEFAULT NULL,
  `kjbj` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--

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
- --------------------------------------------------------

--
-- Table structure for table `referral_records`
--

CREATE TABLE `referral_records` (
  `id` int(11) NOT NULL,
  `fclt_id` int(11) NOT NULL,
  `rfrrl_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `referred_hospital` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_records`
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
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_transaction`
--
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
Table structure for table `third_trimester`
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