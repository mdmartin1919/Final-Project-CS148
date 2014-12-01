
<?php
include "top.php";
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    
    include "menu.php";
    $debug = false;

    $phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");
    $path_parts = pathinfo($phpSelf);
    print '<body id="' . $path_parts['filename'] . '">';

    $tableName = "tblRegister";

    


    $tableNameName = "Tables_in_" . $dbName;
    print "<table>";

    if ($tableName != "") {
        print "<aside id='records'>";

        $query = "SHOW COLUMNS FROM " . $tableName;
        $info = $thisDatabase->select($query);

        $span = count($info);

        //print out the table name and how many records there are
        print "<table border='1'>";
        echo "<tr>";

        

        $query = "SELECT * FROM " . $tableName;
        $a = $thisDatabase->select($query);


       

        echo "</th></tr>";

        //print out the column headings
        print "<tr>";
        $columns = 0;
        foreach ($info as $field) {
            // ok messes up the pk since its not a 3 letter prefix. oh well
            print "<td>";
            $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));

            foreach ($camelCase as $one) {
                print $one . " ";
            }

            "</td>";
            $columns++;
        }
        print "</tr><tr>";

        //now print out each record
        $query = "SELECT * FROM " . $tableName;

        $info2 = $thisDatabase->select($query);

        $highlight = 0; // used to highlight alternate rows
        foreach ($info2 as $rec) {
            $highlight++;
            if ($highlight % 2 != 0) {
                $style = " odd ";
            } else {
                $style = " even ";
            }
            print '<tr class="' . $style . '">';
            for ($i = 0; $i < $columns; $i++) {
                print "<td>" . $rec[$i] . "</td>";
            }
            print "</tr>";
        }

        // all done
        print "</table>";
        print "</aside>";
    }
} else {
    ?>
    <header>
        <h1>You need to log in first to see this page!</h1>
        <h1>please click the link below to log in.</h1>
        <h1><a href="login.php">Login!</a></h1>
    </header>
    <?php
}
?>