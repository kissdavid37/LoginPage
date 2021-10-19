<html>

<head>
    <title>Webfejlesztés beadandó</title>
    <link rel="stylesheet" type="text/css" href="beadando.css" />
   
    
    </script>
</head>

<body>
    <?php
    error_reporting(E_ERROR | E_PARSE);
    function searchForEmail($elemid, $array) {
        foreach ($array as $key => $val) {//0 index a felhasználónév 1 index a jelszó (a splited tömbben)
            if ($val[0] == $elemid) {
                
                return $val;
            }
        }
        return null;
     }
     function searchForPassword($elemid, $array) {
        foreach ($array as $key => $val) {//0 index a felhasználónév 1 index a jelszó  (a splited tömbben)
            if ($val[1] == $elemid) {
                return $val;
            }
        }
        return null;
     }
    //előállitom a kezdőállapotot
    if(isset($_GET["username"])&&isset($_GET["password"])){
        $username=$_GET["username"];
        $password=$_GET["password"];
        if($username=="" && $password=="" )
        {
            echo "<script type='text/javascript'> 
                alert('Kötelező megadni a felhasználónevet és a jelszót!'); 
            </script>";
        }
        else if($username==""){
            echo "<script type='text/javascript'> 
                alert('Kötelező megadni a felhasználónevet!'); 
            </script>";
        }
        else if($password==""){
            echo "<script type='text/javascript'> 
            alert('Kötelező megadni a jelszót!'); 
        </script>";
        }
        else{
            
                        $file = fopen("password.txt", "r") or
                        exit("Unable to open file!");
                        while(!feof($file)) {
                        $coded[]=fgets($file);
                        }
                        array_pop($coded);
                        fclose($file);
                        $coded=explode("\n",file_get_contents('password.txt'));
                        //átalakítom hexába a szöveget és kettessével szétszedem
                    for($i=0;$i<count($coded);$i++) {
                        $converted_hexa[$i]=bin2hex($coded[$i]);
                        $splited_hexa[$i]=str_split($converted_hexa[$i],2);
                        
                    }
                    
                    //a hexadecimális szöveget átalakítom decimálissá és beletöltöm egy tömbbe
                    for($i=0;$i<count($converted_hexa);$i++){
                        for($j=0;$j<count($splited_hexa[$i]);$j++)

                            $converted_decimal[$i][$j]=hexdec($splited_hexa[$i][$j]);
                            
                    }

                        //itt történik az eltolás,minden 4. után ujra a -5-el tolja el ezért kell a $j+4 a végére
                    for($i=0;$i<count($converted_decimal);$i++){
                        for($j=0;$j<count($splited_hexa[$i]);$j++)
                        {
                            $increased_decimal[$i][$j]=$converted_decimal[$i][$j]-5;
                            $increased_decimal[$i][$j+1]=$converted_decimal[$i][$j+1]+14;
                            $increased_decimal[$i][$j+2]=$converted_decimal[$i][$j+2]-31;
                            $increased_decimal[$i][$j+3]=$converted_decimal[$i][$j+3]+9;
                            $increased_decimal[$i][$j+4]=$converted_decimal[$i][$j+4]-3;
                            $j=$j+4;
                            //ha a két tömb hosszusága különbözik akkor annyi elemet törlök az increased decimal végéről ahány a különbség
                            if(count($increased_decimal[$i])!=count($converted_decimal[$i])){
                                $kulonbseg=count($increased_decimal[$i])-count($converted_decimal[$i]);
                                //mivel itt létrehoz egy 6. elemet a tömbben ezért ki kell törölnöm
                                    for($k=0;$k<=$kulonbseg-1;$k++){
                                        array_pop($increased_decimal[$i]);
                                    }
                            }
                            
                        }
                    }
                    

                        //most a decimálist visszaalakítom karakterekre
                    for($i=0;$i<count($increased_decimal);$i++){
                        for($j=0;$j<count($increased_decimal[$i]);$j++){

                        $decoded_pw[$i][$j]=chr($increased_decimal[$i][$j]);
                        }
                    }

                    //itt a törlés
                    array_pop($decoded_pw);
                    //itt összekötöm a karaktereket,hogy összefüggő szöveg legyen
                    for($i=0;$i<count($decoded_pw);$i++){
                        $decoded[$i]=join($decoded_pw[$i]);
                        
                    }

                        //szétválasztom a * karaktertől jobra és balra,a bal oldali rész kerűl a 0 indexre a jobb oldali pedig az 1 indexre
                    for($i=0;$i<count($decoded);$i++){
                        $splited[]=(explode("*",$decoded[$i]));
                    }
                    
                   
                   //ha egyezik az email
                   if(searchForEmail($username, $splited)!=null){
                       //ha egyezik a jelszó
                        if(searchForPassword($password,$splited)!=null){
                               
                                error_reporting(E_ALL);
                                $dbName="adatok";
                                $dbUser="root";
                                $dbPass="";

                                $conn = new mysqli("localhost", $dbUser,$dbPass,$dbName);
                                if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                                }
                                $sql="select titkos from tabla where username='$username'";
                                $result=$conn->query($sql);
                                

                                while($eredmeny=mysqli_fetch_row($result)){
                                $titkos=implode("",$eredmeny);
                                

                                    switch ($eredmeny[0]){
                                        case "piros":
                                            echo "<style type='text/css'> .background-color { background:red } </style>";
                                            break;
                                        case "zold":
                                            echo "<style type='text/css'> .background-color { background:green } </style>";
                                            break;
                                        case "sarga":
                                            echo "<style type='text/css'> .background-color { background:yellow } </style>";
                                            break;
                                        case "kek":
                                            echo "<style type='text/css'> .background-color { background:blue } </style>";
                                            break;
                                            
                                        case "fekete":
                                            echo "<style type='text/css'> .background-color { background:black } </style>";
                                                break;

                                        case "feher":
                                            echo "<style type='text/css'> .background-color { background:white } </style>";
                                            break;
                                    }
                                }
                        }
                        //ha nem egyezik a jelszo=>hibaüzenet
                        else{
                            echo "<script type='text/javascript'> 
                            alert('Hibás jelszót adtál meg!'); 
                        </script>";
                                header( "refresh:3;url=http://www.police.hu/" );
                        }
                   }
                   //nem egyezik az email=> hibaüzenet
                   else{
                    echo "<script type='text/javascript'> 
                    alert('Nincs ilyen felhasználó!'); 
                </script>";
                   }
                    
        }
        



    }
        
        

    ?>

    <div class="background-color" id="bg" name="bg";>
        
        <div class="container" id="container">
            <h1 name="h1" class="title">Welcome!</h1>
            
            <form name="inputform" action="index.php" method="GET">
                <div class="input">

                <input type="text" name="username" id="username" size="30" placeholder="Username" />
                </div>
                <br/>
                <br/>
                <div class="input">
                    <input type="password" name="password" id="password" size="30" placeholder="Password"/>
                    </div>
                    
                    <input type="submit" value="Login" id="login" />

                    


            </form>
        </div>
    </div>


</body>

</html>