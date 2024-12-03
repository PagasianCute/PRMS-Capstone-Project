-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2023 at 06:40 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `create_referral` (IN `new_name` VARCHAR(255), IN `new_age` VARCHAR(255), IN `new_date` VARCHAR(255), IN `new_time` VARCHAR(255), IN `new_referred_hospital` INT, IN `new_fclt_id` INT)   BEGIN
    INSERT INTO referral_forms (
        name,
        age
    ) VALUES (
        new_name,
        new_age
    );
    
    SET @last_id = LAST_INSERT_ID();
    
    INSERT INTO referral_records (
        fclt_id,
        rfrrl_id,
        date,
        time,
        referred_hospital,
        status
    ) VALUES (
        new_fclt_id,
        @last_id,
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_patient` (IN `new_fname` VARCHAR(255), IN `new_mname` VARCHAR(255), IN `new_lname` VARCHAR(255), IN `new_contactNum` VARCHAR(20), IN `new_address` VARCHAR(255), IN `new_fclt_id` INT)   BEGIN
    INSERT INTO patients (
        fname,
        mname,
        lname,
        contact,
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
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `fclt_id` int(11) NOT NULL,
  `fclt_name` varchar(255) NOT NULL,
  `fclt_password` varchar(255) NOT NULL,
  `fclt_ref_id` varchar(255) NOT NULL,
  `fclt_type` varchar(255) NOT NULL,
  `fclt_address` varchar(255) NOT NULL,
  `img_url` text NOT NULL,
  `fclt_contact` varchar(11) NOT NULL,
  `fclt_status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`fclt_id`, `fclt_name`, `fclt_password`, `fclt_ref_id`, `fclt_type`, `fclt_address`, `img_url`, `fclt_contact`, `fclt_status`) VALUES
(1, 'Caraga Hospital', '$2y$10$e9OJl./loMHTgS5BJu5grOhWgjGak81GUi1LpK6W0q2.DK5usT6we', '003', 'Hospital', 'Surigao City', '', '98768726379', 'Active'),
(2, 'Gigaquit RHU', '$2y$10$1NzWlXD0t/r7ya4u1MDrOOH3sbO/ZmUz9990FwRozoX.1fpdscklO', '008', 'Birthing Home', 'Gigaquit', '', '91827462721', 'Active'),
(3, 'Surigao Del Norte Provincial Hospital', '$2y$10$YWFHX4SDkT3Bp803vcm1XO.RdvBsr8sgaRRiDPjLcfVU/l5WAZtM6', '009', 'Provincial Hospital', 'Surigao del Norte', '', '09090909099', 'Offline'),
(4, 'Miranda', '$2y$10$298VYvJ767szo0IanMnkCOc42ubpxLXcOvOpGDWduA/nrSaRifOHq', '005', 'Birthing Home', 'Surigao City', '', '09876865271', 'Offline'),
(13, 'Claver RHU', '$2y$10$1Ib5MTXRaF3t5cXiSPRtkuY9DzQ0gwAGYfYiAdg34dI/VLlkTR6sa', '004', 'Birthing Home', 'Claver Surigao Del Norte', '', '82746172634', 'Offline');

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
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `message`, `receiver_id`, `date`, `time`) VALUES
(147, 1, 'asdad', 3, '2023-11-20', '07:16 PM'),
(148, 1, 'koko', 13, '2023-11-20', '07:16 PM'),
(149, 1, 'asdads', 4, '2023-11-20', '07:17 PM'),
(150, 1, 'asad', 3, '2023-11-20', '07:20 PM'),
(151, 2, 'hahha', 1, '2023-11-20', '07:30 PM'),
(152, 1, 'asds', 3, '2023-11-20', '07:32 PM'),
(153, 2, 'hey', 1, '2023-11-20', '10:11 PM'),
(154, 1, 'hello', 2, '2023-11-20', '10:11 PM'),
(155, 2, 'yow', 1, '2023-11-20', '10:12 PM'),
(156, 2, 'wew', 1, '2023-11-20', '10:13 PM'),
(157, 1, 'yow', 2, '2023-11-20', '10:43 PM'),
(158, 2, 'wew', 1, '2023-11-20', '10:45 PM'),
(159, 1, 'oh yeah', 2, '2023-11-20', '10:46 PM'),
(160, 1, 'sadds', 2, '2023-11-20', '10:54 PM'),
(161, 1, 'aw', 2, '2023-11-20', '11:01 PM'),
(162, 1, 'asd', 2, '2023-11-20', '11:01 PM'),
(163, 1, 'yow', 2, '2023-11-20', '11:38 PM'),
(164, 1, 'hey', 2, '2023-11-20', '11:38 PM'),
(165, 1, 'yow', 2, '2023-11-20', '11:38 PM'),
(166, 1, 'yow', 2, '2023-11-20', '11:39 PM'),
(167, 1, 'hey', 2, '2023-11-20', '11:41 PM'),
(168, 1, 'hoy', 3, '2023-11-20', '11:41 PM');

-- --------------------------------------------------------

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

INSERT INTO `patients` (`id`, `fname`, `mname`, `lname`, `contact`, `address`, `fclt_id`) VALUES
(64, 'Jez', '', 'mahboi', '090909', 'P-4, Brgy. Togbongon', 2),
(65, 'asd', 'asd', 'asd', 'sad', 'sad', 2),
(70, 'asd', 'asd', 'sad', 'ad', 'ad', 2),
(71, 'hahah', 'hahah', 'hahahh', 'hahh', 'hah', 2),
(72, 'asd', 'sad', 'ad', 'sad', 'ad', 2),
(73, 'asd', 'asd', 'asd', 'ad', 'asd', 2),
(74, 'hahah', 'hahah', 'haha', 'haha', 'ahaha', 2);

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
(98, '', '1', '', '', '', '', '', '0', 64, 1),
(99, '', '6', '', '', '', '', '', '0', 64, 6),
(100, '11/16/2023', '123', 'asd', 'asd', 'asd', '11/18/2023', '11/24/2023', '2', 65, 1),
(101, '11/24/2023', '', '', '', '', '', '', '0', 64, 7);

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
(64, 64, 2, '2023-11-13', '22:01:35', 1),
(84, 64, 2, '2023-11-13', '23:04:35', 2),
(85, 64, 2, '2023-11-13', '23:09:30', 3),
(86, 65, 2, '2023-11-15', '17:15:17', 1),
(87, 65, 2, '2023-11-15', '17:15:49', 2),
(88, 64, 2, '2023-11-16', '13:02:29', 4),
(89, 64, 2, '2023-11-16', '13:02:38', 5),
(94, 65, 2, '2023-11-16', '23:11:23', 3),
(95, 64, 2, '2023-11-16', '23:15:22', 6),
(96, 65, 2, '2023-11-16', '23:35:02', 4),
(97, 64, 2, '2023-11-17', '16:41:08', 7),
(98, 64, 2, '2023-11-17', '16:41:19', 8),
(99, 74, 2, '2023-11-20', '23:53:01', 1);

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
  `age` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_forms`
--

INSERT INTO `referral_forms` (`id`, `name`, `age`) VALUES
(266, 'Jez1', '12'),
(267, 'Joey', '17'),
(268, '', ''),
(269, 'asdasd', 'asdad'),
(270, 'asdasd', 'asdad'),
(271, 'asdasd', 'asdad'),
(272, 'asdasd', 'asdad'),
(273, 'Brix', '15'),
(274, 'Peachy', '12'),
(275, 'Sarah', '17'),
(276, '', ''),
(277, 'Hez', '123'),
(278, 'asdasd', 'asdad'),
(279, 'jaj', 'jaja'),
(280, 'yah', 'yah'),
(281, 'nn', 'nnn'),
(282, 'kjaja', 'jajaj'),
(283, 'asd', 'asdadd'),
(284, 'jajaj', 'jajaja'),
(285, 'jajaj', 'jajaj'),
(286, 'hahah', 'hahah'),
(287, 'yah', 'yayh'),
(288, 'asd', 'asdad'),
(289, 'asda', 'asdd'),
(290, 'ad', 'asdadasda'),
(291, 'lknkjn', 'nkjnkj'),
(292, 'kakak', 'kakak'),
(293, 'haha', '123'),
(294, 'nanan', 'nanana'),
(295, 'ijniu', 'oini'),
(296, 'ijbib', 'ibijbi'),
(297, 'jbihb', 'ibii'),
(298, 'oiiub', 'ibibi'),
(299, 'jjin', 'ijnij'),
(300, 'njnj', 'njnj'),
(301, 'ooijo', 'ijoij'),
(302, 'jnjn', 'jnjjn'),
(303, 'jbihb', 'ibibhb'),
(304, 'nii', 'jnjj'),
(305, 'njnj', 'njnjn');

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
(511, 'Referral Declined', 163, 10, '2023-10-08', '11:33 AM', 0),
(512, 'Referral Declined', 163, 10, '2023-10-08', '12:04 PM', 0),
(513, 'Referral Declined', 163, 10, '2023-10-08', '12:05 PM', 0),
(514, 'Referral Declined', 163, 10, '2023-10-08', '12:05 PM', 0),
(515, 'Referral Declined', 163, 10, '2023-10-08', '12:06 PM', 0),
(516, 'Referral Accepted', 163, 3, '2023-10-08', '12:06 PM', 0),
(517, 'Referral Declined', 163, 10, '2023-10-08', '12:06 PM', 0),
(518, 'Referral Declined', 163, 10, '2023-10-08', '12:07 PM', 0),
(519, 'Referral Declined', 163, 10, '2023-10-08', '12:07 PM', 0),
(520, 'Referral Declined', 165, 10, '2023-10-08', '12:07 PM', 0),
(521, 'Referral Declined', 162, 3, '2023-10-08', '12:08 PM', 0),
(522, 'Referral Accepted', 162, 10, '2023-10-08', '12:37 PM', 0),
(523, 'Referral Declined', 162, 10, '2023-10-08', '12:37 PM', 0),
(524, 'Referral Declined', 162, 10, '2023-10-08', '12:45 PM', 0),
(525, 'Referral Declined', 165, 10, '2023-10-08', '01:02 PM', 0),
(526, 'Referral Accepted', 162, 10, '2023-10-08', '01:07 PM', 0),
(527, 'New referral', 166, 2, '2023-10-08', '02:20 PM', 0),
(528, 'New referral', 167, 2, '2023-10-08', '02:21 PM', 0),
(529, 'Referral Declined', 166, 3, '2023-10-08', '02:21 PM', 0),
(530, 'Referral Accepted', 167, 3, '2023-10-08', '02:30 PM', 0),
(531, 'Referral Accepted', 166, 1, '2023-10-08', '02:38 PM', 0),
(532, 'Referral Declined', 166, 3, '2023-10-08', '02:44 PM', 0),
(533, 'Referral Declined', 166, 3, '2023-10-08', '02:44 PM', 0),
(534, 'Referral Declined', 166, 3, '2023-10-08', '02:48 PM', 0),
(535, 'Referral Accepted', 166, 3, '2023-10-08', '02:49 PM', 0),
(536, 'Referral Declined', 166, 3, '2023-10-08', '02:49 PM', 0),
(537, 'Referral Accepted', 166, 3, '2023-10-08', '02:57 PM', 0),
(538, 'Referral Declined', 167, 3, '2023-10-08', '02:57 PM', 0),
(539, 'Referral Declined', 166, 3, '2023-10-08', '02:57 PM', 0),
(540, 'Referral Declined', 166, 3, '2023-10-08', '02:57 PM', 0),
(541, 'Referral Declined', 166, 3, '2023-10-08', '03:04 PM', 0),
(542, 'Referral Declined', 166, 3, '2023-10-08', '03:04 PM', 0),
(543, 'Referral Declined', 166, 3, '2023-10-08', '03:04 PM', 0),
(544, 'Referral Declined', 166, 3, '2023-10-08', '03:05 PM', 0),
(545, 'Referral Declined', 166, 3, '2023-10-08', '03:06 PM', 0),
(546, 'Referral Declined', 166, 3, '2023-10-08', '03:06 PM', 0),
(547, 'Referral Declined', 166, 3, '2023-10-08', '03:24 PM', 0),
(548, 'Referral Declined', 166, 3, '2023-10-08', '03:25 PM', 0),
(549, 'Referral Declined', 166, 3, '2023-10-08', '03:37 PM', 0),
(550, 'Referral Declined', 166, 3, '2023-10-08', '03:44 PM', 0),
(551, 'Referral Declined', 166, 3, '2023-10-08', '03:45 PM', 0),
(552, 'Referral Declined', 166, 3, '2023-10-08', '03:45 PM', 0),
(553, 'Referral Declined', 166, 3, '2023-10-08', '03:48 PM', 0),
(554, 'Referral Declined', 166, 3, '2023-10-08', '03:48 PM', 0),
(555, 'Referral Declined', 167, 3, '2023-10-08', '04:01 PM', 0),
(556, 'Referral Declined', 167, 3, '2023-10-08', '04:01 PM', 0),
(557, 'Referral Declined', 167, 3, '2023-10-08', '04:01 PM', 0),
(558, 'Referral Declined', 167, 3, '2023-10-08', '04:02 PM', 0),
(559, 'Referral Declined', 166, 3, '2023-10-08', '04:02 PM', 0),
(560, 'Referral Declined', 166, 3, '2023-10-08', '04:02 PM', 0),
(561, 'Referral Declined', 166, 3, '2023-10-08', '04:03 PM', 0),
(562, 'Referral Declined', 166, 3, '2023-10-08', '04:03 PM', 0),
(563, 'Referral Declined', 167, 3, '2023-10-08', '04:05 PM', 0),
(564, 'Referral Declined', 167, 3, '2023-10-08', '04:05 PM', 0),
(565, 'Referral Declined', 166, 3, '2023-10-08', '04:06 PM', 0),
(566, 'Referral Declined', 166, 3, '2023-10-08', '04:06 PM', 0),
(567, 'Referral Declined', 167, 3, '2023-10-08', '04:07 PM', 0),
(568, 'Referral Declined', 167, 3, '2023-10-08', '04:09 PM', 0),
(569, 'Referral Declined', 166, 3, '2023-10-08', '04:10 PM', 0),
(570, 'Referral Declined', 167, 3, '2023-10-08', '04:10 PM', 0),
(571, 'Referral Declined', 167, 3, '2023-10-08', '04:11 PM', 0),
(572, 'Referral Declined', 166, 3, '2023-10-08', '04:11 PM', 0),
(573, 'Referral Declined', 167, 3, '2023-10-08', '04:11 PM', 0),
(574, 'Referral Declined', 167, 3, '2023-10-08', '04:13 PM', 0),
(575, 'Referral Declined', 166, 3, '2023-10-08', '04:13 PM', 0),
(576, 'Referral Declined', 166, 3, '2023-10-08', '04:13 PM', 0),
(577, 'Referral Declined', 166, 3, '2023-10-08', '04:14 PM', 0),
(578, 'Referral Declined', 167, 3, '2023-10-08', '04:17 PM', 0),
(579, 'Referral Declined', 167, 3, '2023-10-08', '04:20 PM', 0),
(580, 'Referral Declined', 166, 3, '2023-10-08', '04:20 PM', 0),
(581, 'Referral Declined', 166, 3, '2023-10-08', '04:20 PM', 0),
(582, 'Referral Accepted', 167, 3, '2023-10-08', '04:20 PM', 0),
(583, 'Referral Declined', 167, 3, '2023-10-08', '04:20 PM', 0),
(584, 'Referral Declined', 166, 3, '2023-10-08', '04:21 PM', 0),
(585, 'Referral Accepted', 167, 3, '2023-10-08', '04:29 PM', 0),
(586, 'Referral Declined', 167, 3, '2023-10-08', '04:29 PM', 0),
(587, 'Referral Declined', 166, 3, '2023-10-08', '04:29 PM', 0),
(588, 'Referral Declined', 167, 3, '2023-10-08', '04:29 PM', 0),
(589, 'Referral Accepted', 167, 1, '2023-10-08', '10:53 PM', 0),
(590, 'Referral Declined', 167, 1, '2023-10-08', '10:53 PM', 0),
(591, 'Referral Declined', 167, 3, '2023-10-08', '11:01 PM', 0),
(592, 'Referral Accepted', 166, 3, '2023-10-08', '11:02 PM', 0),
(593, 'Referral Declined', 166, 3, '2023-10-08', '11:03 PM', 0),
(594, 'Referral Declined', 167, 3, '2023-10-08', '11:03 PM', 0),
(595, 'Referral Declined', 166, 3, '2023-10-08', '11:03 PM', 0),
(596, 'Referral Accepted', 166, 1, '2023-10-08', '11:07 PM', 0),
(597, 'Referral Accepted', 167, 3, '2023-10-09', '01:03 AM', 0),
(598, 'Referral Declined', 167, 3, '2023-10-09', '01:17 AM', 0),
(599, 'Referral Declined', 167, 3, '2023-10-09', '01:17 AM', 0),
(600, 'New referral', 168, 2, '2023-10-09', '01:29 AM', 0),
(601, 'Referral Accepted', 168, 3, '2023-10-09', '01:29 AM', 0),
(602, 'Referral Declined', 168, 3, '2023-10-09', '01:40 AM', 0),
(603, 'Referral Declined', 168, 3, '2023-10-09', '01:41 AM', 0),
(604, 'New referral', 169, 2, '2023-10-09', '01:55 AM', 0),
(605, 'Referral Declined', 169, 3, '2023-10-09', '02:00 AM', 0),
(606, 'Referral Declined', 169, 3, '2023-10-09', '02:28 AM', 0),
(607, 'Referral Accepted', 169, 3, '2023-10-09', '02:28 AM', 0),
(608, 'New referral', 170, 2, '2023-10-09', '02:35 AM', 0),
(609, 'New referral', 171, 2, '2023-10-09', '02:36 AM', 0),
(610, 'Referral Declined', 171, 3, '2023-10-09', '02:39 AM', 0),
(611, 'Referral Declined', 170, 3, '2023-10-09', '02:39 AM', 0),
(612, 'Referral Accepted', 171, 3, '2023-10-09', '03:01 AM', 0),
(613, 'Referral Accepted', 170, 3, '2023-10-09', '03:01 AM', 0),
(614, 'Referral Accepted', 168, 3, '2023-10-09', '03:03 AM', 0),
(615, 'Referral Declined', 169, 3, '2023-10-09', '03:04 AM', 0),
(616, 'Referral Declined', 170, 3, '2023-10-09', '03:04 AM', 0),
(617, 'Referral Accepted', 171, 3, '2023-10-09', '03:11 AM', 0),
(618, 'Referral Declined', 168, 3, '2023-10-09', '03:33 AM', 0),
(619, 'Referral Accepted', 168, 1, '2023-10-09', '03:33 AM', 0),
(620, 'Referral Declined', 168, 1, '2023-10-09', '03:37 AM', 0),
(621, 'Referral Declined', 171, 3, '2023-10-09', '03:38 AM', 0),
(622, 'Referral Accepted', 171, 3, '2023-10-09', '03:38 AM', 0),
(623, 'Referral Declined', 171, 3, '2023-10-09', '03:39 AM', 0),
(624, 'Referral Accepted', 171, 3, '2023-10-09', '03:39 AM', 0),
(625, 'Referral Declined', 171, 3, '2023-10-09', '03:39 AM', 0),
(626, 'Referral Accepted', 171, 3, '2023-10-09', '03:39 AM', 0),
(627, 'Referral Declined', 171, 3, '2023-10-09', '03:39 AM', 0),
(628, 'Referral Declined', 171, 3, '2023-10-09', '03:40 AM', 0),
(629, 'Referral Accepted', 168, 3, '2023-10-09', '03:41 AM', 0),
(630, 'Referral Accepted', 170, 3, '2023-10-09', '03:43 AM', 0),
(631, 'Referral Accepted', 169, 3, '2023-10-09', '03:43 AM', 0),
(632, 'Referral Accepted', 168, 3, '2023-10-09', '03:44 AM', 0),
(633, 'Referral Accepted', 169, 3, '2023-10-09', '03:45 AM', 0),
(634, 'Referral Accepted', 170, 3, '2023-10-09', '03:46 AM', 0),
(635, 'Referral Accepted', 168, 3, '2023-10-09', '03:47 AM', 0),
(636, 'Referral Accepted', 168, 3, '2023-10-09', '03:49 AM', 0),
(637, 'Referral Declined', 169, 3, '2023-10-09', '03:50 AM', 0),
(638, 'Referral Accepted', 169, 1, '2023-10-09', '03:51 AM', 0),
(639, 'New referral', 172, 2, '2023-10-09', '04:38 AM', 0),
(640, 'Referral Accepted', 172, 3, '2023-10-09', '04:38 AM', 0),
(641, 'Referral Declined', 172, 3, '2023-10-09', '04:38 AM', 0),
(642, 'Referral Declined', 172, 3, '2023-10-09', '04:39 AM', 0),
(643, 'Referral Accepted', 172, 1, '2023-10-09', '04:40 AM', 0),
(644, 'New referral', 173, 2, '2023-10-23', '09:11 PM', 0),
(645, 'Referral Accepted', 173, 1, '2023-10-23', '09:11 PM', 0),
(646, 'Referral Accepted', 171, 3, '2023-10-23', '09:12 PM', 0),
(647, 'Referral Accepted', 173, 1, '2023-10-23', '09:12 PM', 0),
(648, 'Referral Declined', 170, 3, '2023-10-23', '09:12 PM', 0),
(649, 'Referral Accepted', 170, 1, '2023-10-23', '09:12 PM', 0),
(650, 'Referral Declined', 170, 3, '2023-10-24', '12:54 AM', 0),
(651, 'Referral Declined', 170, 3, '2023-10-24', '12:54 AM', 0),
(652, 'New referral', 174, 2, '2023-10-25', '12:04 AM', 0),
(653, 'New referral', 175, 2, '2023-10-25', '12:18 AM', 0),
(654, 'New referral', 176, 2, '2023-10-25', '12:21 AM', 0),
(655, 'New referral', 177, 2, '2023-10-25', '12:23 AM', 0),
(656, 'New referral', 178, 2, '2023-10-25', '12:25 AM', 0),
(657, 'New referral', 181, 2, '2023-10-25', '12:29 AM', 0),
(658, 'New referral', 183, 2, '2023-10-25', '12:31 AM', 0),
(659, 'New referral', 185, 2, '2023-10-25', '12:31 AM', 0),
(660, 'New referral', 187, 2, '2023-10-25', '12:31 AM', 0),
(661, 'New referral', 189, 2, '2023-10-25', '12:32 AM', 0),
(662, 'New referral', 191, 2, '2023-10-25', '12:34 AM', 0),
(663, 'New referral', 193, 2, '2023-10-25', '12:36 AM', 0),
(664, 'New referral', 195, 2, '2023-10-25', '12:37 AM', 0),
(665, 'New referral', 197, 2, '2023-10-25', '12:38 AM', 0),
(666, 'New referral', 199, 2, '2023-10-25', '12:39 AM', 0),
(667, 'New referral', 201, 2, '2023-10-25', '12:44 AM', 0),
(668, 'New referral', 203, 2, '2023-10-25', '12:52 AM', 0),
(669, 'New referral', 205, 2, '2023-10-25', '12:57 AM', 0),
(670, 'New referral', 207, 2, '2023-10-25', '12:57 AM', 0),
(671, 'New referral', 208, 2, '2023-10-25', '01:00 AM', 0),
(672, 'New referral', 209, 2, '2023-10-25', '01:04 AM', 0),
(673, 'New referral', 210, 2, '2023-10-25', '01:05 AM', 0),
(674, 'New referral', 211, 2, '2023-10-25', '01:05 AM', 0),
(675, 'New referral', 212, 2, '2023-10-25', '01:08 AM', 0),
(676, 'New referral', 214, 2, '2023-10-25', '01:09 AM', 0),
(677, 'New referral', 216, 2, '2023-10-25', '01:11 AM', 0),
(678, 'New referral', 218, 2, '2023-10-25', '01:11 AM', 0),
(679, 'New referral', 219, 2, '2023-10-25', '01:13 AM', 0),
(680, 'New referral', 220, 2, '2023-10-25', '01:13 AM', 0),
(681, 'New referral', 221, 2, '2023-10-25', '01:14 AM', 0),
(682, 'New referral', 222, 2, '2023-10-25', '01:15 AM', 0),
(683, 'New referral', 223, 2, '2023-10-25', '01:18 AM', 0),
(684, 'New referral', 224, 2, '2023-10-25', '01:19 AM', 0),
(685, 'New referral', 225, 2, '2023-10-25', '01:19 AM', 0),
(686, 'New referral', 226, 2, '2023-10-25', '01:20 AM', 0),
(687, 'New referral', 227, 2, '2023-10-25', '01:21 AM', 0),
(688, 'New referral', 228, 2, '2023-10-25', '01:23 AM', 0),
(689, 'New referral', 229, 2, '2023-10-25', '01:26 AM', 0),
(690, 'New referral', 230, 2, '2023-10-25', '01:27 AM', 0),
(691, 'New referral', 231, 2, '2023-10-25', '01:28 AM', 0),
(692, 'New referral', 232, 2, '2023-10-25', '01:28 AM', 0),
(693, 'New referral', 233, 2, '2023-10-25', '01:31 AM', 0),
(694, 'Referral Accepted', 233, 1, '2023-10-25', '01:35 AM', 0),
(695, 'New referral', 234, 2, '2023-10-25', '01:48 AM', 0),
(696, 'Referral Accepted', 234, 1, '2023-10-25', '01:48 AM', 0),
(697, 'New referral', 235, 2, '2023-10-25', '01:49 AM', 0),
(698, 'Referral Declined', 235, 3, '2023-10-25', '01:50 AM', 0),
(699, 'Referral Accepted', 235, 1, '2023-10-25', '02:00 AM', 0),
(700, 'New referral', 236, 2, '2023-10-25', '02:01 AM', 0),
(701, 'New referral', 237, 2, '2023-10-25', '02:02 AM', 0),
(702, 'Referral Accepted', 237, 3, '2023-10-25', '02:02 AM', 0),
(703, 'Referral Declined', 237, 3, '2023-10-25', '02:02 AM', 0),
(704, 'Referral Declined', 237, 3, '2023-10-25', '02:02 AM', 0),
(705, 'New referral', 238, 2, '2023-10-25', '09:33 AM', 0),
(706, 'Referral Declined', 236, 1, '2023-10-26', '01:54 PM', 0),
(707, 'Referral Declined', 236, 1, '2023-10-26', '01:54 PM', 0),
(708, 'Referral Accepted', 237, 1, '2023-10-26', '04:36 PM', 0),
(709, 'Referral Declined', 236, 1, '2023-10-26', '04:37 PM', 0),
(710, 'Referral Declined', 236, 1, '2023-10-26', '04:37 PM', 0),
(711, 'Referral Declined', 237, 3, '2023-10-26', '04:46 PM', 0),
(712, 'Referral Declined', 235, 3, '2023-10-26', '04:46 PM', 0),
(713, 'New referral', 239, 2, '2023-10-26', '04:49 PM', 0),
(714, 'New referral', 240, 2, '2023-10-26', '04:50 PM', 0),
(715, 'New referral', 241, 2, '2023-10-26', '04:51 PM', 0),
(716, 'New referral', 242, 2, '2023-10-26', '04:51 PM', 0),
(717, 'New referral', 243, 2, '2023-10-26', '04:52 PM', 0),
(718, 'New referral', 244, 2, '2023-10-26', '04:53 PM', 0),
(719, 'New referral', 245, 2, '2023-10-26', '04:53 PM', 0),
(720, 'New referral', 246, 2, '2023-10-26', '04:53 PM', 0),
(721, 'New referral', 247, 2, '2023-10-26', '04:54 PM', 0),
(722, 'New referral', 248, 2, '2023-10-26', '05:17 PM', 0),
(723, 'Referral Accepted', 248, 1, '2023-10-26', '05:17 PM', 0),
(724, 'New referral', 249, 2, '2023-10-26', '06:10 PM', 0),
(725, 'New referral', 250, 2, '2023-11-08', '05:30 PM', 0),
(726, 'New referral', 251, 2, '2023-11-08', '05:39 PM', 0),
(727, 'Referral Accepted', 251, 3, '2023-11-08', '06:00 PM', 0),
(728, 'New referral', 252, 2, '2023-11-08', '06:00 PM', 0),
(729, 'Referral Declined', 252, 3, '2023-11-08', '06:01 PM', 0),
(730, 'New referral', 253, 2, '2023-11-08', '06:10 PM', 0),
(731, 'Referral Accepted', 253, 1, '2023-11-08', '06:17 PM', 0),
(732, 'Referral Accepted', 252, 1, '2023-11-08', '06:26 PM', 0),
(733, 'New referral', 254, 2, '2023-11-08', '10:41 PM', 0),
(734, 'Referral Accepted', 257, 3, '2023-11-13', '11:18 AM', 0),
(735, 'Referral Declined', 258, 3, '2023-11-13', '11:19 AM', 0),
(736, 'Referral Declined', 266, 3, '2023-11-13', '11:32 AM', 0),
(737, 'Referral Accepted', 266, 1, '2023-11-13', '11:33 AM', 0),
(738, 'Referral Accepted', 273, 3, '2023-11-13', '07:58 PM', 0),
(739, 'Referral Declined', 274, 3, '2023-11-13', '08:00 PM', 0),
(740, 'Referral Accepted', 274, 1, '2023-11-13', '08:01 PM', 0),
(741, 'Referral Accepted', 275, 1, '2023-11-13', '08:52 PM', 0),
(742, 'Referral Accepted', 267, 1, '2023-11-16', '10:57 AM', 0),
(743, 'Referral Accepted', 277, 1, '2023-11-16', '10:58 AM', 0),
(744, 'Referral Accepted', 278, 1, '2023-11-16', '11:43 AM', 0),
(745, 'New referral', 279, 2, '2023-11-16', '01:50 PM', 0),
(746, 'New referral', 280, 2, '2023-11-16', '01:50 PM', 0),
(747, 'Referral Accepted', 279, 1, '2023-11-16', '10:38 PM', 0),
(748, 'New referral', 281, 2, '2023-11-16', '10:58 PM', 0),
(749, 'New referral', 282, 2, '2023-11-17', '12:14 PM', 0),
(750, 'New referral', 283, 2, '2023-11-17', '12:18 PM', 0),
(751, 'New Referral', 286, 2, '2023-11-17', '12:32 PM', 0),
(752, 'New Referral', 287, 2, '2023-11-17', '12:32 PM', 0),
(753, 'New Referral', 288, 2, '2023-11-17', '12:52 PM', 0),
(754, 'New Referral', 289, 2, '2023-11-17', '12:54 PM', 0),
(755, 'New Referral', 290, 2, '2023-11-17', '01:01 PM', 0),
(756, 'New Referral', 291, 2, '2023-11-17', '01:04 PM', 0),
(757, 'New Referral', 292, 2, '2023-11-17', '01:08 PM', 0),
(758, 'New Referral', 293, 2, '2023-11-17', '01:38 PM', 0),
(759, 'New Referral', 294, 2, '2023-11-17', '01:50 PM', 0),
(760, 'New Referral', 295, 2, '2023-11-17', '01:51 PM', 0),
(761, 'New Referral', 296, 2, '2023-11-17', '01:52 PM', 0),
(762, 'New Referral', 297, 2, '2023-11-17', '01:52 PM', 0),
(763, 'New Referral', 298, 2, '2023-11-17', '01:54 PM', 0),
(764, 'New Referral', 299, 2, '2023-11-17', '01:55 PM', 0),
(765, 'New Referral', 300, 2, '2023-11-17', '01:55 PM', 0),
(766, 'New Referral', 301, 2, '2023-11-17', '01:56 PM', 0),
(767, 'New Referral', 302, 2, '2023-11-17', '01:56 PM', 0),
(768, 'New Referral', 303, 2, '2023-11-17', '01:57 PM', 0),
(769, 'New Referral', 304, 2, '2023-11-17', '01:57 PM', 0),
(770, 'New Referral', 305, 2, '2023-11-17', '01:57 PM', 0),
(771, 'New Referral', 306, 2, '2023-11-17', '02:04 PM', 0),
(772, 'Referral Accepted', 280, 1, '2023-11-21', '12:18 AM', 0);

-- --------------------------------------------------------

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

INSERT INTO `referral_records` (`id`, `fclt_id`, `rfrrl_id`, `date`, `time`, `referred_hospital`, `status`) VALUES
(242, 2, 266, '2023-11-13', '11:32:24', '3', 'Accepted'),
(243, 2, 267, '2023-11-13', '11:46:27', '1', 'Accepted'),
(244, 2, 268, '2023-11-13', '11:48:55', '0', 'Pending'),
(245, 2, 269, '2023-11-13', '11:49:04', '0', 'Pending'),
(246, 2, 270, '2023-11-13', '11:49:05', '0', 'Pending'),
(247, 2, 271, '2023-11-13', '11:49:11', '0', 'Pending'),
(248, 2, 272, '2023-11-13', '11:49:18', '13', 'Pending'),
(249, 2, 273, '2023-11-13', '19:57:22', '3', 'Accepted'),
(250, 2, 274, '2023-11-13', '20:00:17', '3', 'Accepted'),
(251, 2, 275, '2023-11-13', '20:52:36', '1', 'Accepted'),
(252, 2, 276, '2023-11-13', '21:48:45', '0', 'Pending'),
(253, 2, 277, '2023-11-13', '21:50:02', '1', 'Accepted'),
(254, 2, 278, '2023-11-16', '11:12:27', '1', 'Accepted'),
(255, 2, 279, '2023-11-16', '01:50 PM', '1', 'Accepted'),
(256, 2, 280, '2023-11-16', '01:50 PM', '1', 'Accepted'),
(257, 2, 281, '2023-11-16', '10:58 PM', '1', 'Pending'),
(258, 2, 282, '2023-11-17', '12:14 PM', '1', 'Pending'),
(259, 2, 283, '2023-11-17', '12:18 PM', '1', 'Pending'),
(260, 2, 285, '2023-11-17', '12:28 PM', '2', 'Pending'),
(261, 2, 286, '2023-11-17', '12:32 PM', '3', 'Pending'),
(262, 2, 287, '2023-11-17', '12:32 PM', '2', 'Pending'),
(263, 2, 288, '2023-11-17', '12:52 PM', '2', 'Pending'),
(264, 2, 289, '2023-11-17', '12:54 PM', '2', 'Pending'),
(265, 2, 290, '2023-11-17', '01:01 PM', '3', 'Pending'),
(266, 2, 291, '2023-11-17', '01:04 PM', '4', 'Pending'),
(267, 2, 292, '2023-11-17', '01:08 PM', '13', 'Pending'),
(268, 2, 293, '2023-11-17', '01:38 PM', '1', 'Pending'),
(269, 2, 294, '2023-11-17', '01:50 PM', '2', 'Pending'),
(270, 2, 295, '2023-11-17', '01:51 PM', '1', 'Pending'),
(271, 2, 296, '2023-11-17', '01:52 PM', '3', 'Pending'),
(272, 2, 297, '2023-11-17', '01:52 PM', '3', 'Pending'),
(273, 2, 298, '2023-11-17', '01:54 PM', '3', 'Pending'),
(274, 2, 299, '2023-11-17', '01:55 PM', '2', 'Pending'),
(275, 2, 300, '2023-11-17', '01:55 PM', '3', 'Pending'),
(276, 2, 301, '2023-11-17', '01:56 PM', '2', 'Pending'),
(277, 2, 302, '2023-11-17', '01:56 PM', '3', 'Pending'),
(278, 2, 303, '2023-11-17', '01:57 PM', '3', 'Pending'),
(279, 2, 304, '2023-11-17', '01:57 PM', '4', 'Pending'),
(280, 2, 305, '2023-11-17', '01:57 PM', '2', 'Pending');

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
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referral_transaction`
--

INSERT INTO `referral_transaction` (`id`, `fclt_id`, `rfrrl_id`, `status`, `date`, `time`, `reason`) VALUES
(63, 3, 266, 'Declined', '2023-11-13', '11:32 AM', 'IDK'),
(64, 1, 266, 'Accepted', '2023-11-13', '11:33 AM', 'NULL'),
(65, 3, 273, 'Accepted', '2023-11-13', '07:58 PM', 'NULL'),
(66, 3, 274, 'Declined', '2023-11-13', '08:00 PM', 'idk'),
(67, 1, 274, 'Accepted', '2023-11-13', '08:01 PM', 'NULL'),
(68, 1, 275, 'Accepted', '2023-11-13', '08:52 PM', 'NULL'),
(69, 1, 267, 'Accepted', '2023-11-16', '10:57 AM', 'NULL'),
(70, 1, 277, 'Accepted', '2023-11-16', '10:58 AM', 'NULL'),
(71, 1, 278, 'Accepted', '2023-11-16', '11:43 AM', 'NULL'),
(72, 1, 279, 'Accepted', '2023-11-16', '10:38 PM', 'NULL'),
(73, 1, 280, 'Accepted', '2023-11-21', '12:18 AM', 'NULL');

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
(9, 'first_checkup', 64, '11/17/2023', '', '', '', '', '', '', '', '', '', '', '', '', '', 8);

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
(7, 'Jezrael Salino', 'jezraelsalino@gmail.com', 'admin', 'Admin', '$2y$10$zyga/EpPBf7Gw8iGIdELGOwxGVV5cKsMPcTG7G7DmDqhop6tdZpBK', 'assets/65435628ea089_🤓.png', 0),
(8, 'Jezmahboi', 'jezraelsalino@yahoo.com', 'Jezipoo', 'Staff', '$2y$10$KHzZQ20quKBf7qR/AGUSz.BTjnZjYpm5pHrVOinVYz3Rbo1Ab251i', 'assets/boy.png', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `birth_experience`
--
ALTER TABLE `birth_experience`
  ADD PRIMARY KEY (`birth_experience_id`);

--
-- Indexes for table `demo_messages`
--
ALTER TABLE `demo_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `birth_experience`
--
ALTER TABLE `birth_experience`
  MODIFY `birth_experience_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `demo_messages`
--
ALTER TABLE `demo_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `fclt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `first_trimester`
--
ALTER TABLE `first_trimester`
  MODIFY `first_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `patients_details`
--
ALTER TABLE `patients_details`
  MODIFY `patients_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `prenatal_records`
--
ALTER TABLE `prenatal_records`
  MODIFY `prenatal_records_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;

--
-- AUTO_INCREMENT for table `referral_notification`
--
ALTER TABLE `referral_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=773;

--
-- AUTO_INCREMENT for table `referral_records`
--
ALTER TABLE `referral_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `referral_transaction`
--
ALTER TABLE `referral_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `second_trimester`
--
ALTER TABLE `second_trimester`
  MODIFY `second_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `third_trimester`
--
ALTER TABLE `third_trimester`
  MODIFY `third_trimester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `usersId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
