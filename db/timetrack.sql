 
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

 

CREATE TABLE `MondayGestionDiaria` (
  `id` int(11) NOT NULL,
  `boardId` int(10) NOT NULL,
  `itemId` int(10) NOT NULL,
  `userId` int(10) NOT NULL,
  `userEmail` varchar(100) NOT NULL,
  `TPP` varchar(100),
  `nameBoard` varchar(255),
  `itemName` varchar(255),
  `duration` varchar(255),
  `milestone` int(1),
  `date` varchar(100),
  `postText` varchar(255),
  `responseText` text,
  `lastResponseId` int(11),
  `creatorIdResponse` int(11),
  `creatorIdPost` int(11)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 
ALTER TABLE `MondayGestionDiaria`
  ADD PRIMARY KEY (`id`);

 
ALTER TABLE `MondayGestionDiaria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
