-- MySQL dump 10.13  Distrib 8.0.33, for Linux (x86_64)
--
-- Host: localhost    Database: CSYM019
-- ------------------------------------------------------
-- Server version	8.0.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth` (
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
INSERT INTO `auth` VALUES ('admin','admin'),('airah','airah');
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ucas_regular` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ucas_foundation` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `duration_fulltime` int NOT NULL,
  `duration_parttime` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `duration_foundation` int DEFAULT NULL,
  `duration_placement` tinyint(1) DEFAULT NULL,
  `start_dates` json NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `icon_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `course_name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `highlights` json DEFAULT NULL,
  `req_summary` json DEFAULT NULL,
  `req_foundation` json DEFAULT NULL,
  `english_req` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fees_year` varchar(20) NOT NULL,
  `fees_uk_fulltime` int NOT NULL,
  `fees_uk_parttime` int DEFAULT NULL,
  `fees_uk_foundation` int DEFAULT NULL,
  `fees_intl_fulltime` int NOT NULL,
  `fees_intl_parttime` int DEFAULT NULL,
  `fees_intl_foundation` int DEFAULT NULL,
  `fees_withplacement` int DEFAULT NULL,
  `fees_extras` json DEFAULT NULL,
  `faqs` json DEFAULT NULL,
  `related_courses` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (58,'2023-05-24 08:33:44','2023-05-24 08:33:44','Undergraduate','M930','M931',3,'6',4,0,'[\"September\"]','Waterside','criminology.png','Criminology BA (Hons)','Criminology, Law and Policing','https://www.northampton.ac.uk/courses/criminology-ba-hons/','This BA Criminology course has been designed to contribute towards achieving the following United Nations Sustainable Development Goal: SDG10 of Reduced Inequalities. Studying Criminology with us gives you a distinctive approach to learning, drawing on the disciplines of sociology, psychology, history and law. This provides you with a comprehensive introduction to key theoretical and methodological issues at the heart of the discipline.','[\"Research placement available\", \"Multi-disciplinary approach to criminology\", \"Guest speakers from criminology backgrounds\", \"Membership to the British Society of Criminology (BSC)\"]','[\"BCC at A-Level\", \"DMM at BTEC\", \"Pass (C and above) at T Level\", \"GCSE Maths and English at Grade C/4 or above\"]','[\"DEE at A-Level\", \"MPP at BTEC\", \"Pass (D or E) at T Level\"]','IELTS 6.0 (or equivalent) with a minimum of 5.5 in all bands','2023/2024',9250,1540,9250,14750,NULL,14750,NULL,NULL,NULL,'[\"Criminal and Corporate Investigation BA (Hons)\", \"Law LLB (Hons)\", \"Law with Criminology BA (Hons)\"]'),(59,'2023-05-24 08:41:22','2023-05-24 08:41:22','Postgraduate',NULL,NULL,1,NULL,NULL,1,'[\"September\"]','Waterside','accounting.png','Financial and Investment Analysis MSc','Accounting and Finance','https://www.northampton.ac.uk/courses/financial-and-investment-analysis-msc/','MSc Financial and Investment Analysis will develop you into a specialist investment professional that can take up challenging roles in investment banking, risk management and fund management. This programme will prepare you to write the CFA® professional exams. This course has an Industry Placement Option.','[\"Industry Placement Option available\", \"Hands-on application of skills learnt through simulated trading via the Bloomberg terminals\", \"Exemptions are available from main professional bodies ACCA and CIMA\"]','[\"If you hold a recognised first or second class honours degree from a UK university or international equivalent in finance, banking, accounting or a related discipline then you will be eligible to apply for this course. If you hold professional qualifications you will also be considered for entry onto this course.\"]',NULL,'Minimum standard – IELTS 6.5 (or equivalent) for study at postgraduate level','2023/2024',8010,NULL,NULL,16500,NULL,NULL,1100,NULL,NULL,'[\"Accounting and Finance MSc\"]'),(60,'2023-05-24 08:54:24','2023-05-24 08:54:24','Undergraduate','W340','W341',3,'6',4,0,'[\"September\"]','Waterside','music.png','Popular Music BA (Hons)','Music and Art','https://www.northampton.ac.uk/courses/popular-music-ba-hons/','The Popular Music BA (Hons) course at the University of Northampton is designed and run to invest you with a variety of skills, knowledge and experience. When you graduate, you’ll have the know-how to navigate an ever evolving 21st Century music industry. You’ll have the skills for wider employment, including in the teaching profession and further study should this be something you wish to pursue. This course is Industry Accredited by JAMES representing APRS, MPG and associate industry bodies. Accreditation of a course by relevant industry bodies provides assurance to students and employers of its potential and value.','[\"Performing live on stage\", \"Learning to use a professional grade recording studio\", \"Introduction to a network of music businesses\", \"One-to-one instrument/ vocal coaching sessions (on your main instrument in year 1)\", \"Various performing opportunities, including at Northampton Music Festival\", \"Specialised guest lectures and masterclasses for popular music and composition\", \"Guaranteed paid internship with the Northampton Employment Promise.\"]','[\"BCC at A-Level\", \"DMM at BTEC\", \"Pass (C and above) at T Level\"]','[\"DEE at A-Level\", \"MPP at BTEC\", \"Pass (D or E) at T Level\"]','IELTS 6.0 (or equivalent) with a minimum of 5.5 in all bands','2023/2024',9250,1540,9250,14750,NULL,14750,NULL,'[\"Musical instrument & consumables: Price varies\", \"Vocalists – microphone: £80 (approx.)\", \"Portable hard drive: £60 (approx.)\", \"A good pair of closed back headphones: £60-100 (approx.)\", \"Batteries for portable recording equipment: £30-50 (approx.)\", \"Core textbooks and sheet music: £80 (approx.)\", \"USB Flash drives: £10-20 for two (approx.)\", \"And/ or SD card (£10-20 max)\"]',NULL,'[\"Popular Music (Top-Up) BA (Hons)\", \"Music Production BA (Hons)\", \"Music Production (Top-Up) BA (Hons)\"]'),(61,'2023-05-24 09:26:32','2023-05-24 09:26:32','Undergraduate','W640','W642',3,NULL,4,0,'[\"September\"]','Waterside','media.png','Photography BA (Hons)','Media and Journalism','https://www.northampton.ac.uk/courses/photography-ba-hons/','                            Our Photography BA degree takes a practical approach to exploring the photograph within a range of contemporary practices and contexts. This will extend to a critical understanding of photography subjects and allows the scope for you to develop your creativity and professionalism. During our undergraduate photography course, we will challenge you to consider traditional silver processes and digital technologies, evaluating photographic production and studying the still image in relation to both the moving image and newer interactive technologies. Students studying BA Photography at the University of Northampton will also benefit from our industry standard photography studio facilities, which you can explore via the virtual tours below. Our BA Photography degree has been approved by the Association of Photographers (AOP) as one of the ‘most respected photographic courses in the UK’.','[\"Our BA Photography degree has been accredited by the Association of Photographers (AOP) as one of the ‘most respected photography courses in the UK, You can receive FREE student membership of the AOP as part of your studies, enhancing your CV and helping you stand out from the crowd when you graduate and pursue a career in photography, Students have use of dedicated specialist facilities, We also organise external exhibitions of our students work and in addition, we regularly host Visiting Lecturers and Portfolio reviews.\"]','[\"IELTS 6.0 (or equivalent) with a minimum of 5.5 in all bands\"]','[\"DEE at A-Level, MPP at BTEC, Pass (D or E) at T Level\"]','                            BCC at A-Level\r\nDMM at BTEC\r\nPass (C and above) at T Level\r\nFoundation Diploma in Art and Design','2023/2024',9250,NULL,9250,14750,NULL,14750,NULL,'[\"Study trips to Europe cost approximately £400-450, Trips to America/Asia cost approximately £1000-1200, Own camera (cameras are available to loan from the University – with potential added cost for film, paper, external hard drive and SD/CF cards), External hard drive, Producing a physical portfolio – both the case and the prints, Printing photographs\"]',NULL,'[\"Creative Film, Television and Digital Media Production BA (Hons), Fine Art BA (Hons)\"]'),(62,'2023-05-24 11:10:05','2023-05-24 11:10:05','Postgraduate',NULL,NULL,1,'4',NULL,0,'[\"September\"]','Waterside','humanities.png','English – Contemporary Literature MA','Humanities','https://www.northampton.ac.uk/courses/english-contemporary-literature-ma/','                            This exciting English – Contemporary Literature MA course examines the role of contemporary literature in a number of different contexts. You will have the chance to explore a diverse range of texts, across varied modules, with the chance to explore a topic of your choice in specialist detail through your dissertation. You could study post-1945 classics, including late modernists like Samuel Beckett, as well as surveying the latest novels. You may explore specialist fields such as trauma fiction or gender and sexuality, master the latest literary theories, or investigate new genres and popular narrative media, including contemporary gothic literature, film, video games and comic books.','[\"Research-informed modules on current topics, Flexible evening study time, This course is eligible for the postgraduate loan\"]','[\"To apply for the MA in English (Contemporary Literature), we normally ask that you have a Bachelor’s degree in English, or a related discipline, at 2:1 or higher. Applications with alternative qualifications or experience and from overseas students are welcomed and will be assessed in accordance with the University’s Admissions Policy and Academic Regulations.\"]',NULL,'                            IELT 6.5 (or equivalent) for study at postgraduate level','2023/2024',8010,4450,NULL,16500,NULL,NULL,NULL,'[\"We do ask that you buy the prescribed primary texts for each module. We carefully review the costs of every module each year, so that they do not exceed £100 per module. In practice, by using libraries, freely available online resources and second-hand copies of books, costs are often less than half of this total figure. You will have the chance to attend a range of research seminars, conferences, field trips and other activities beyond the curriculum for free or at minimal cost.\"]',NULL,'[\"English BA (Hons), Primary Education (5-11) (QTS) PGCE\"]'),(63,'2023-05-24 11:14:36','2023-05-24 11:14:36','Postgraduate',NULL,NULL,1,'2',NULL,0,'[\"February\", \"September\"]','Waterside','compscience.png','Computing (Software Engineering) MSc','Computer Science','https://www.northampton.ac.uk/courses/computing-software-engineering-msc/','                            Study the design of software systems at a greater depth with our Computing (Software Engineering) MSc course. Alongside exploring areas of computer software engineering, you will also have the opportunity to develop your analytics and research skills during this Computing postgraduate degree. Students will complete an individual thesis which will investigate an area they find of particular interest.','[\"Object-oriented design and development\", \"Practical real-world projects\", \"Agile software methodology\", \"Core computing fundamentals\", \"Flexible part time study option on the computing software engineering degree\"]','[\"Applicants for the msc computer software engineering will normally hold a recognised first or second class honours degree from a UK university or international equivalent in a relevant subject. We expect that you will have a working knowledge of computers and networks and it is essential that you have practical hands-on experience of at least one programming language.\"]',NULL,'                            IELT 6.5 (or equivalent) for study at postgraduate level','2023/2024',8010,890,NULL,16500,NULL,NULL,NULL,NULL,NULL,'[\"Computing (Computer Networks Engineering) MSc\", \"Computing MSc\", \"Computing (Internet Technology and Security) MSc\"]'),(64,'2023-05-24 11:19:14','2023-05-24 11:19:14','Undergraduate','C602','C604',3,'6',4,0,'[\"September\"]','Waterside','sports.png','Physical Education and Sport BA (Hons)','Sport Sciences','https://www.northampton.ac.uk/courses/physical-education-and-sport-ba-hons/','Our degree in Physical Education and Sport BA (Hons) degree explores the ways that sport, physical education and physical activity are organised across schools and local communities. During this physical education course, you are encouraged to think critically about current and historical practice in sport and physical education and reflect on your own experiences. This Physical Education and Sport course allows you to study key disciplines within sport and physical education. It also includes an opportunity to undertake a work placement at a local sport or physical education provider. A strong emphasis is placed on the application of theory to understanding real-world issues such as barriers to participation, performance pathways, coaching and teaching practice and government influence on sport.','[\"Strong emphasis on employability with work placement opportunities for students at levels two and three\", \"Close partnerships including Northampton Saints, Northampton Town FC and Northants County Cricket Club.\", \"Guaranteed paid internship with the Northampton Employment Promise\"]','[\"CCC at A-Level\", \"DMM at BTEC\", \"Pass (C and above) at T Level\"]','[\"DEE at A-Level\", \"MPP at BTEC\", \"Pass (D or E) at T Level\"]',NULL,'2023/2024',9250,1540,9250,14750,NULL,4750,NULL,'[\"Additional costs will be incurred if you decide to participate in optional field trips/visits as part of the physical education course. The exact cost will depend upon the location chosen/ nature of the trip.\", \"You will need to wear suitable sports kit for practical sessions as part of the degree in physical education. You can purchase Sport and Exercise kit but these are not compulsory. The exact cost of kit will be provided at the start of the course.\", \"The course requires you to complete a compulsory DBS check upon enrolment at your own cost. Exact costs will be provided with final offer information before the start of the programme.\", \"The physical education course also requires you to complete a work placement module during your degree. You will be required to pay any transport costs incurred in completing your placement.\", \"You will need to cover costs associated with producing a poster for the final year conference.\"]',NULL,'[\"Sport Management and Leadership BSc (Hons)\", \"Applied Sport and Exercise Science MSc / MA\"]'),(65,'2023-05-24 11:28:11','2023-05-24 11:28:11','Postgraduate',NULL,NULL,1,'2',NULL,0,'[\"February\", \"September\"]','Waterside','hr.png','Human Resource Management MA','Human Resource Management','https://www.northampton.ac.uk/courses/human-resource-management-ma/','The aim of this masters in Human Resource Management is to develop you as an HR professional. You will be introduced to specialised knowledge and research evidence giving you an in depth understanding of successful people management in organisations. Perhaps more importantly, you will practice the tools and techniques of strategic and operational HRM giving you practical, insight-driven experience which will help further your career. Human Resources can be an exciting, rewarding and challenging career that can take you anywhere in the world.','[\"CIPD accreditation and membership on completion of the MA Human Resource Management course\"]','[\"To study MA Human Resources Management at UON you will normally need to hold a recognised First or Second Class Honours degree from a UK University (or an international equivalent) in human resource management, or in business, commerce, management or related disciplines. It would be an advantage for applicants to have experience of working in a HR or general management role for at least a year. You may be invited to an interview with the Programme Leader to assess your suitability for entry to the course.\"]',NULL,'IELT 6.5 (or equivalent) for study at postgraduate level','2023/2024',8010,2670,NULL,16500,NULL,NULL,NULL,'[\"You are required to join the CIPD as a student member (currently a one-off joining fee of £40 and membership fee of £141 for 18 months). Please see the CIPD website for further details.\"]',NULL,'[\"Human Resource Management PGDip\", \"Master of Business Administration MBA\"]'),(66,'2023-05-24 11:33:15','2023-05-24 11:33:15','Postgraduate',NULL,NULL,1,NULL,NULL,1,'[\"February\", \"September\"]','Waterside','advertising.png','International Marketing Strategy MSc','Advertising and Marketing','https://www.northampton.ac.uk/courses/international-marketing-strategy-msc/','                                                                                                                                            Our MSc International Marketing Strategy is specifically designed to provide you with a strong foundation for a successful career in the exciting and fast-paced world of international marketing. The marketing strategy course also enhances your ability to think strategically about marketing management in an international context.','[\"This international marketing management course has a 12-month Industry Placement Option\", \"This course is eligible for the Alumni Changemaker Discount for UON graduates\"]','[\"You will need to hold a First or Second class honours degree (or equivalent) in order to be eligible for this course. No work experience is required for admission onto this course.\"]',NULL,'                                                                                                                                            IELTS 6.5 overall with a minimum of 6.0 in writing and 5.5 in all other skills','2023/2024',8010,NULL,NULL,16500,NULL,NULL,1000,NULL,NULL,'[\"Social Innovation MA\", \"International Business Management MSc\"]'),(67,'2023-05-24 11:37:34','2023-05-24 11:37:34','Undergraduate','B210','B211',3,NULL,4,0,'[\"September\"]','Waterside','bioscience.png','Pharmacology BSc (Hons)','Biological Sciences',NULL,'                            Pharmacology is the study of drugs and how they interact and work on the body. On our BSc Pharmacology degree you will learn how drugs are made into medicines and how drugs and other chemicals interact at the molecular, cellular and systems levels in the body. This BSc Pharmacology course is suited to those wanting to pursue a career in the pharmaceutical industry or in biomedical research as well as graduate entry to medicine','[\"Flexible programme of study that will allow you to meet your career aspirations, Practice and practical application sessions for a more engaging experience, Our graduates will be equipped with a variety of transferable skills that employers are seeking\"]','[\"BCC at A-Level, DMM at BTEC Extended Diploma (in Applied Science, Applied Human Biology or Pharmaceutical Science or similar science subject), Pass (C and above) in T Level Science\"]','[\"DEE at A-Level, MPP at BTEC, Pass (D or E) at T Level\"]','                            IELTS 6.0 (or equivalent) with a minimum of 5.5 in all bands','2023/2024',9250,1540,9250,14750,NULL,14750,NULL,NULL,NULL,'[\"Biomedical Science BSc (Hons), Biochemistry BSc (Hons), Molecular Bioscience MSc\"]');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `module_code` varchar(10) NOT NULL,
  `stage` varchar(10) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `credits` int NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `prereq` varchar(10) DEFAULT NULL,
  `course_id` int NOT NULL,
  KEY `course_id` (`course_id`),
  CONSTRAINT `modules_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES ('CRI1002','stage1','Crime and Society',20,'Compulsory',NULL,NULL,58),('CRI1010','stage1','From Delinquency to Criminal Careers',20,'Compulsory',NULL,NULL,58),('CRI3009','stage3','Activism in Criminology',20,'Designated',NULL,NULL,58),('CRI4003','stage3','Criminology Dissertation',40,'Compulsory',NULL,NULL,58),('CRI2003','stage2','Doing Research in Criminology',40,'Compulsory',NULL,NULL,58),('CRI2011','stage2','International Policing',20,'Designated',NULL,NULL,58),('FINM025',NULL,'Dissertation and Research Methods',60,'Compulsory','dissertation',NULL,59),('FINM065',NULL,'Portfolio Management',20,'Compulsory','regular',NULL,59),('FINM072',NULL,'Alternative Investments',20,'Compulsory','regular',NULL,59),('FINM074',NULL,'Work-Based Project (60 Credit)',60,'Compulsory','placement',NULL,59),('FINM073',NULL,'Career Futures: Employability Skills',5,'Compulsory','placement',NULL,59),('MUS4121','stage3','Professional Project',40,'Compulsory',NULL,NULL,60),('MUS3143','stage3','Advanced Recording and Production: Client Demos',20,'Designated',NULL,NULL,60),('MUS3135','stage3','The Philosophy of Music',20,'Designated',NULL,NULL,60),('MUS2140','stage1','How Music Works',20,'Designated',NULL,NULL,60),('MUS2151','stage2','Digital Audio Workstation 2: Sampling',20,'Designated',NULL,NULL,60),('MUS1129','stage1','Cultural Theory 1',20,'Compulsory',NULL,NULL,60),('MUS1147','stage1','Composition and Theory 1: Application of Theory',10,'Compulsory',NULL,NULL,60),('PHO1002','stage1','Photography and its Changing Uses',20,'Compulsory',NULL,NULL,61),('PHO1015','stage1','Photographic Techniques and Concepts; Digital Photography',20,'Compulsory',NULL,NULL,61),('PHO2012','stage2','Photography and Arts Practices; Arts Practices',20,'Compulsory',NULL,NULL,61),('PHO2014','stage2','Photography in a Design Context; Fashion Photography',10,'Compulsory',NULL,NULL,61),('PHO3003','stage3','Career Development in Photographic PracticeS',40,'Compulsory',NULL,NULL,61),('PHO4001','stage3','Practice Exhibition',80,'Compulsory',NULL,NULL,61),('LITM043',NULL,'Dissertation',60,'Compulsory','dissertation',NULL,62),('LITM033',NULL,'Critical Theory and Methodologies',20,'Compulsory','regular',NULL,62),('LITM036',NULL,'Literary Modernism in a Postmodern World',20,'Designated','regular',NULL,62),('LITM035',NULL,'Contemporary British Gothic',20,'Compulsory','regular',NULL,62),('CSYM023',NULL,'Dissertation',60,'Compulsory','dissertation',NULL,63),('CSYM015',NULL,'Intelligent Systems',20,'Designated','regular',NULL,63),('CSYM026',NULL,'Software Engineering',20,'Compulsory','regular',NULL,63),('CSYM017',NULL,'Databases',20,'Compulsory','regular',NULL,63),('SPO1011','stage1','Sport Pedagogy for Coaching 1',20,'Compulsory',NULL,NULL,64),('SPO1013','stage1','Introduction to Physical Education and School Sport',20,'Compulsory',NULL,NULL,64),('SPO2044','stage2','Sport, Society & Social Equality',20,'Designated',NULL,NULL,64),('SPO2036','stage2','Positive Psychology Coaching',20,'Designated',NULL,NULL,64),('HRMM032',NULL,'Human Resource Management in Context',20,'Compulsory','regular',NULL,65),('HRMM035',NULL,'Resourcing and Developing Talent',20,'Compulsory','regular',NULL,65),('HRMM030',NULL,'Dissertation and Research Methods',60,'Compulsory','dissertation',NULL,65),('MKTM021',NULL,'Dissertation and Research Methods',60,'Compulsory','dissertation',NULL,66),('MKTM018',NULL,'Global Marketing Strategy',20,'Compulsory','regular',NULL,66),('MKTM044',NULL,'Strategic Marketing Management',20,'Compulsory','regular',NULL,66);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-24 18:27:52
