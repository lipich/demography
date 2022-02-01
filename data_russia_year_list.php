<?php
$years = GetYearList();

echo json_encode($years);

// получить список лет для выбора диапазона
function GetYearList()
{
	require_once("database.php");
  
	if (isset($_GET['regions']))
	{
		$id = $_GET['regions'];

		$select_years = "select distinct year from mortality_russia where region in (" . $id . ") order by year";

		$db = database::getInstance();
		$result = $db->query($select_years);

        $years = array();
        $i = 0;

    	while ($type = mysqli_fetch_row($result))
		{
	  		$years[$i] = $type[0];
            $i++;
		}

        return $years;
  	}
}
?>