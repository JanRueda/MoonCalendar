<?php
//========================================================================================
//  AutoRoad : Automatizaciones para la vida cotidiana
//  Función  : Genera calendario en PDF con el nombre de los santos y las fases lunares
//  Autores  : Jan Rueda
//  Fecha    : 26/10/2016
//  Licencia : Open Source
//========================================================================================

  
// require('fpdf.php');
require('WriteHTML.php');
require('Festivos.php');
require('Moon.php');
$pdf = new FPDF();

// Array con los meses del año
$monthNames = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", 
"Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

//Automatizar url y directorio local del fichero
$dir = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';

// Si no existe mes y año seleccionar los actuales
if (!isset($_REQUEST["mes"])) $_REQUEST["mes"] = date("n");
if (!isset($_REQUEST["anio"])) $_REQUEST["anio"] = date("Y");


// Añade una pagina del pdf por año
for ($i=1; $i <= 12; $i++) { 

      $cMonth = "$i";
      $cYear = $_REQUEST["anio"]+1;
       
      $prev_year = $cYear;
      $next_year = $cYear;
      $prev_month = $cMonth-1;
      $next_month = $cMonth+1;
       
      if ($prev_month == 0 ) {
          $prev_month = 12;
          $prev_year = $cYear - 1;
      }
      if ($next_month == 13 ) {
          $next_month = 1;
          $next_year = $cYear + 1;
      }
           
        //Fecha numerica actual
        $timestamp = mktime(0,0,0,$cMonth,1,$cYear);    

        //Variable para llevar la cuenta del dia actual
        $dia_actual = 1;

        //Ultimo dia del mes
        $ultimo_dia = date("t",$timestamp);  

        //Array toda la información del mes
        $thismonth = getdate ($timestamp);                        
         
        //calculo el numero (posicion) del primer dia de la primera semana (Menos 1 porque al ser un array empieza en 0 a contar)
        $posicion_primer_dia = $thismonth['wday']-1;

        // Este caso es una esception en algunos meses si la posicion es menor a cero es que tiene valor 6
        // Nos permite corregir este número que le restamos debeido a que en el Array se cuenta desde la posición 0
        if ($posicion_primer_dia == -1)
        {
            $posicion_primer_dia = 6;
        }

      // Año actual
      Mes($monthNames, $cYear, $cMonth,$pdf, $posicion_primer_dia, $ultimo_dia, $dia_actual,$dir);
      //$pdf->Write(5,$_POST['santo']);

}

// LLamada a la funcion para representar el primer mes del siguiente año
EneroProxYear($cYear,$cMonth,$monthNames,$pdf,$dir);

// Escribe el primer mes del siguiente año
function EneroProxYear($cYear,$cMonth,$monthNames,$pdf,$dir){

      //// Enero siguiente año /////
            
        $cYear = $cYear+1;
        $cMonth = 1;
        //Fecha numerica actual
        $timestamp = mktime(0,0,0,$cMonth,1,$cYear);    

        //Variable para llevar la cuenta del dia actual
        $dia_actual = 1;

        //Ultimo dia del mes
        $ultimo_dia = date("t",$timestamp);  

        //Array toda la información del mes
        $thismonth = getdate ($timestamp);                        
               
        //calculo el numero (posicion) del primer dia de la primera semana (Menos 1 porque al ser un array empieza en 0 a contar)
        $posicion_primer_dia = $thismonth['wday']-1;

        // Este caso es una esception en algunos meses si la posicion es menor a cero es que tiene valor 6
        // Nos permite corregir este número que le restamos debeido a que en el Array se cuenta desde la posición 0
        if ($posicion_primer_dia == -1)
        {
          $posicion_primer_dia = 6;
        }

       // Mes($monthNames, $cYear, $cMonth,$pdf, $posicion_primer_dia, $ultimo_dia, $dia_actual,$dir);

      /// Fin Enero  Del año siguiente ////

}

function Mes($monthNames, $cYear, $cMonth,$pdf, $posicion_primer_dia, $ultimo_dia, $dia_actual,$dir){

  //Añadimos una pagina
  $pdf->AddPage('L','','');
  
  //Añadimos letra y fondo
  $pdf->SetFont('Arial','B',18);
  $pdf->SetFillColor(224,224,224);

  //Mostramos nombre del Mes y Año actual
  Year_mouth($pdf, $monthNames, $cYear, $cMonth);
  MostrarLunas($pdf, $cMonth, $cYear, $monthNames,$dir);

  //>>>>>

  // Prueba Formulario
  //$pdf->Write(5,$_POST['santo'].' '.$_POST['dia'].' '.$_POST['mes'].' '.$_POST['color']);

  // //Paso de hex a rgb
  // $hex = $_POST['color'];
  // $rgb = hex2rgb($hex);

  //<<<<

  // Salto de linea
  $pdf->Ln(); 
  //>>>Celda dias de la semana    
  $pdf->Cell(40,10, 'Lunes',1,0,'C',0);     // empty cell with left,top, and right borders, C >> centrar linea
  $pdf->Cell(40,10, 'Martes',1,0,'C',0);    //Cell(ancho,alto, 'texto',1,0,'C',0);    
  $pdf->Cell(40,10, 'Miercoles',1,0,'C',0); // ,1,0 >> es que van en filas ,, 1,1 >> en columnas
  $pdf->Cell(40,10, 'Jueves',1,0,'C',0);    // 'C' centrado 
  $pdf->Cell(40,10, 'Viernes',1,0,'C',0);
  $pdf->Cell(40,10, 'Sabado',1,0,'C',0);

  // Al ser domingo lo marco en rojo
  $pdf->SetTextColor(255,0,0);
  $pdf->Cell(40,10, 'Domingo',1,0,'C',0);
  // A partir de aqui el resto de los colores vuelven ser negros
  $pdf->SetTextColor(0,0,0);
  //<<<Fin de celdas dias de la semana

  $pdf->Ln();    


  // Escribo primera fila 
  $pdf->SetFont('Arial','',30);
  //$pdf->SetY(10); // Posicion del texto dentro de la celda
  $primer_dia_santos = "nada";   // Variable de control para guardar primer dia de santos

  for ( $i=0;$i<7;$i++ ){

      if ( $i < $posicion_primer_dia ){

        //Si el dia de la semana i es menor que el numero de posicion del primer dia de la semana no pongo nada en la celda
        $pdf->Cell(40,11, ' ','LRT',0,'C',0); // LR sin borde infirior y superior

      } else {

        if( $primer_dia_santos == "nada" ){   //Guardo el primer dia de santos para poder escribir los de la primera linea
          
          $primer_dia_santos = $dia_actual;      

        }

        // Se pinta de rojo por ser 1 de Enero
        if( $dia_actual==1 && $cMonth==1 ){

        // Se pinta el dia de rojo
        $pdf->SetTextColor(255,0,0);
        $pdf->Cell(40,11, $dia_actual,'LRT',0,'C',0);

        // A partir de aqui el resto de las celdas estaran en negro
        $pdf->SetTextColor(0,0,0);
        }

        // Se pinta de rojo por ser 1 de Noviembre por ser santos
        elseif( $dia_actual==1 && $cMonth==11 ){

        // Se pinta el dia de rojo
        $pdf->SetTextColor(255,0,0);
        $pdf->Cell(40,11, $dia_actual,'LRT',0,'C',0);

        // A partir de aqui el resto de las celdas estaran en negro
        $pdf->SetTextColor(0,0,0);
        }

        // Se pinta de rojo por ser 1 de Mayo por ser dia del Trabajo
        elseif( $dia_actual==1 && $cMonth==5 ){

        // Se pinta el dia de rojo
        $pdf->SetTextColor(255,0,0);
        $pdf->Cell(40,11, $dia_actual,'LRT',0,'C',0);

        // A partir de aqui el resto de las celdas estaran en negro
        $pdf->SetTextColor(0,0,0);
        }




        // Si es el dia 6 se pinta de rojo por ser Domingo
        elseif( $i==6 ){

        // Se pinta el dia de rojo
        $pdf->SetTextColor(255,0,0);
        $pdf->Cell(40,11, $dia_actual,'LRT',0,'C',0);

        // A partir de aqui el resto de las celdas estaran en negro
        $pdf->SetTextColor(0,0,0);

        } else {

          $pdf->Cell(40,11, $dia_actual,'LRT',0,'C',0);
        }

        $dia_actual++;
      }
  } 

// Fin de la primera fila

$pdf->SetFont('Arial','',11);

// Comienzo filas de nombre de santos  

$pdf->Ln();  

// Solicion para este dia  >> Para el for de santos debo cambiar la variable dia actual 
// porque si no continua contando 
$dia_actual_santos = $dia_actual;

  for ( $i=0;$i<7;$i++ ){

    if ( $i < $posicion_primer_dia ){

      //Si el dia de la semana i es menor que el numero de posicion del primer dia de la semana no pongo nada en la celda
      $pdf->Cell(40,11, ' ','LR',0,'C',0);

    } else {

      // Si es el dia 6 se pinta de rojo por ser Domingo
      if( $i==6 ){

        // Se pinta el dia de rojo
        $pdf->SetTextColor(255,0,0);
        // Corrijo la i santo por se array y empezar de cero
        $Nombre_Santo = Nombres_Santos($primer_dia_santos, $cMonth,$dir);  // Funcion para conocer Santo del dia 
        //$pdf->Cell(40,11, $Nombre_Santo.$dia_actual_santos,'LR',0,'C',0);
        $Nombre_Santo = utf8_decode($Nombre_Santo);
        $pdf->Cell(40,11, $Nombre_Santo,'LR',0,'C',0);
        $primer_dia_santos = $primer_dia_santos +1; 
        // A partir de aqui el resto de las celdas estaran en negro
        $pdf->SetTextColor(0,0,0);

      } else {

        // Corrijo la i santo por se array y empezar de cero
        $Nombre_Santo = Nombres_Santos($primer_dia_santos, $cMonth,$dir);  // Funcion para conocer Santo del dia 
        //$pdf->Cell(40,11, $Nombre_Santo.$dia_actual_santos,'LR',0,'C',0);

        $Nombre_Santo = utf8_decode($Nombre_Santo);

          if ($Nombre_Santo == 'Nuevo'){

            $Nombre_Santo = utf8_decode('Año Nuevo');
            // Se pinta el dia de rojo
            $pdf->SetTextColor(255,0,0);
            $pdf->Cell(40,11, $Nombre_Santo,'LR',0,'C',0);
            // A partir de aqui el resto de las celdas estaran en negro
            $pdf->SetTextColor(0,0,0);
            $primer_dia_santos = $primer_dia_santos +1; 

          } else {
            
            $pdf->Cell(40,11, $Nombre_Santo,'LR',0,'C',0);
            $primer_dia_santos = $primer_dia_santos +1; 

          }

      }

    // Corrijo la i santo por se array y empezar de cero
    //$Nombre_Santo = Nombres_Santos($primer_dia_santos, $cMonth,$dir);  // Funcion para conocer Santo del dia 
    //$pdf->Cell(40,11, $Nombre_Santo.$dia_actual_santos,'LR',0,'C',0);
    //$pdf->Cell(40,11, $Nombre_Santo,'LR',0,'C',0);
    //$primer_dia_santos = $primer_dia_santos +1; 
    $dia_actual_santos++;

    }
  } 
// Fin de la fila santos

// Fila Pruebas y Comprobacion
  // $pdf->Ln();   
  // $pdf->Cell(280,10, 'El dia de Hoy es >> '.$dia_actual.' Ultimo dia >>'.$ultimo_dia,1,0,'C',0);  
  // $pdf->Ln();   
// Comienzo del resto de filas 
//$pdf->SetFont('Arial','B',36);                // Letra de días
//Color Borde Verde 
// $pdf->SetDrawColor(0, 153, 51);
// //Color Text
// $pdf->SetTextColor(85,107,47);

  //Recorro todos los demás días hasta el final del mes
  $numero_dia = 0;

  while ( $dia_actual <= $ultimo_dia ){

    $pdf->SetFont('Arial','',30); 
    //Si estamos a principio de la semana escribo el Ln()

    
    if ( $numero_dia == 0 ){

      $pdf->Ln();

    }

 // Pinta festivos y domingos
  fest_Dom($pdf, $dia_actual,$numero_dia, $cMonth);

  $dia_actual++;
  $numero_dia++;
  

    //si es el ultimo de la semana, me pongo al principio de la semana y escribo el Ln()
    if ( $numero_dia == 7 ){

      $numero_dia = 0;
      $pdf->SetFont('Arial','',11);   // Letra segunda fila santos
      $pdf->Ln();

      // Al dia actual le resto 7 que son los dias de la primear semana 
      $dia_para_santos = $dia_actual-7;
      //$pdf->Cell(40,10, 'Hola','LRT',0,'C',0);

      for ( $i=0;$i<7;$i++ ){

        // Le voy sumando 1 dia para que los dias de los santos me coincida con el numero
        // La primera interacción del for queda con el $dia_actual -7
        // A partir de esta se va sumando uno a uno
        //$pdf->SetTextColor(24,255,255);

        $Nombre_Santo = Nombres_Santos($dia_para_santos, $cMonth,$dir);  // Funcion para conocer Santo del dia
        //Indico que reconozca utf8  
        $Nombre_Santo = utf8_decode($Nombre_Santo);
        //$pdf->Cell(40,11, $Nombre_Santo.$dia_para_santos.'-'.$i.' ND> '.$numero_dia,'LRB',0,'C',0);

        if ( $Nombre_Santo == 'San Anton' ) {

          // Es San Anton y se pinta de lila
          //$pdf->SetFillColor(2,157,116); y los ultimos parametros de cell 1  es para alterar color de fondo
          //Indico que reconozca utf8  
          $Nombre_Santo = utf8_decode('San Antón');
          $pdf->SetFillColor(2,157,116);
          $pdf->SetTextColor(0,0,0);
          $pdf->Cell(40,11, $Nombre_Santo,'LRB',0,'C',1);
          // A partir de aqui el resto de las celdas estaran en negro
          $pdf->SetTextColor(0,0,0);

        }
        elseif ( $Nombre_Santo == 'San Patricio' ) {

          // Es San Patricion y se pinta de verde
          //$pdf->SetFillColor(2,157,116); y los ultimos parametros de cell 1  es para alterar color de fondo
          $pdf->SetFillColor(2,157,116);
          $pdf->SetTextColor(0,0,0);
          $pdf->Cell(40,11, $Nombre_Santo ,'LRB',0,'C',1);
          // A partir de aqui el resto de las celdas estaran en negro
          $pdf->SetTextColor(0,0,0);

        }

        // Prueba formulario 

        // elseif ( $Nombre_Santo == $_POST['santo'] ) {

        //   // Es San Patricion y se pinta de verde
        //   //$pdf->SetFillColor(2,157,116); y los ultimos parametros de cell 1  es para alterar color de fondo
        //   $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2]);
        //   $pdf->SetTextColor(0,0,0);
        //   $pdf->Cell(40,11, $Nombre_Santo ,'LRB',0,'C',1);
        //   // A partir de aqui el resto de las celdas estaran en negro
        //   $pdf->SetTextColor(0,0,0);

        // }


        elseif ( $i==6 ){

          // Si es el dia 6 se pinta de rojo por ser Domingo
          $pdf->SetTextColor(255,0,0);
          $pdf->Cell(40,11, $Nombre_Santo,'LRB',0,'C',0);
          // A partir de aqui el resto de las celdas estaran en negro
          $pdf->SetTextColor(0,0,0);
        }
        else {

          $pdf->Cell(40,11, $Nombre_Santo,'LRB',0,'C',0);
        }
        //
        //$pdf->Cell(40,11, $Nombre_Santo,'LRB',0,'C',0);
        $dia_para_santos = $dia_para_santos+1;
      }
          
   }
 
 }


  // Ultima fila 
  //Dias pendientes para poner santos
  $ultimos_dias = ($dia_actual)-$dia_para_santos;

  // Celdas que hay que añadir en blaco
   $celdas_blanco = 7 - $ultimos_dias;

   if ( $ultimos_dias != 0 ){

    $pdf->SetFont('Arial','',30); 
    //Compruebo que celdas me faltan por escribir vacias de la última semana del mes
    for ($i=$numero_dia;$i<7;$i++){

   	  $pdf->Cell(40,12, ' ','LRT',0,'C',0);
    }

    //Hacer lo mismo con la ultima linea - NO SE PONE AQUI EL ROJO DE DOMINGO PORQUE NUNCA LLEGA ESTA FILA A DOMINGO
    $pdf->Ln();

    $pdf->SetFont('Arial','',11); 
    // Escribo santos de ultima fila  
    for ( $i=0; $i < $ultimos_dias; $i++ ) { 

      //$pdf->Cell(40,10, 'Da >> '.$dia_para_santos.' f> '.$ultimos_dias,'LRB',0,'C',0);
      //Suma el numero real para dia de santos

      $Nombre_Santo = Nombres_Santos($dia_para_santos, $cMonth,$dir);  // Funcion para conocer Santo del dia 
      //Indico que reconozca utf8  
      $Nombre_Santo = utf8_decode($Nombre_Santo);
      //$pdf->Cell(40,12, $Nombre_Santo.$dia_para_santos.'-'.$i.' ND> '.$numero_dia,'LRB',0,'C',0);
      $pdf->Cell(40,12, $Nombre_Santo,'LRB',0,'C',0);

      $dia_para_santos+=1;
    }

    // Añado celdas en blaco de las fila de santos para las celdas que no tiene dias

    for ( $i=0; $i < $celdas_blanco; $i++ ) { 

      //$pdf->Cell(40,10, 'blaco '.$celdas_blanco,1,0,'C',0);
      $pdf->Cell(40,12, ' ','LRB',0,'C',0);
      //Suma el numero real para dia de santos
      $dia_para_santos+=1;
    }

  }

  }// Fin Funtion Mes


//Escribe el Mes y el Año actual
function Year_mouth($pdf, $monthNames, $cYear, $cMonth){

  //$pdf->Multicell(280,15, $monthNames[$cMonth-1].' '.$cYear,1,0,'C',0);
  $pdf->SetXY(220,20);
  $pdf->Write(5, $monthNames[$cMonth-1].' '.$cYear);
  //$pdf->Ln(15);

}


//Escribe el nombre de los santos segun el dia del mes a partir de los ficheros json 
//que asocian el nombre del santo con el dia de este
 function Nombres_Santos($i,$cMonth,$dir){

  $Url = $dir.'json/'.$cMonth.'.json';
  $mi_cadena = '';

  $mi_url= $Url; 

  $fo= fopen("$mi_url","r") or die ("No se ha encontrado la pagina."); 

  while ( !feof($fo) ) { 

    $mi_cadena .= fgets($fo, 4096); 
  } 

  fclose ($fo); 

  //Convierto json en array
  $mi_cadena = json_decode($mi_cadena, true);




  // Hace para coincidir dias, se suma uno porque array empieza desde cero
  // Pero solo para la primera fila (7 primeros puestos)
  // for ($i=1; $i <32; $i++) { 

  //     if ($i == "$i" && $cMonth == $Mes)
  //     {
  //       $Nombre_Santo = $mi_cadena["$i"];
  //     }
  //     else
  //     {
  //       $Nombre_Santo = "Nul";
  //     }
  //     return $Nombre_Santo;
  // }


  $Nombre_Santo = $mi_cadena[$i];
  
  return $Nombre_Santo;
}


function festivos($dia_actual,$cMonth,$pdf){

  $diaFest = $dia_actual;

        $Fiestas2017 =  array( 
          array(1,1, "Año Nuevo", 255,0,0),
          array(1,6, "Reyes", 255,0,0),
          array(3,13, "Jueves Santo", 255,0,0), 
          array(3,14, "Viernes Santo", 255,0,0),
          array(5,1, "Dia del Trabajo", 255,0,0),
          array(5,17, "Letras Galegas", 255,0,0), 
          array(7,25, "D. Galicia", 255,0,0), 
          array(8,15, "Asuncion", 255,0,0), 
          array(10,12, "D. Hispanidad", 255,0,0),
          array(11,1, "Todos los Santos", 255,0,0), 
          array(12,6, "D. Constitucion", 255,0,0),  
          array(12,8, "La Inmaculada", 255,0,0),  
          array(12,15, "Navidad", 255,0,0)
          );

    for ($row = 0; $row < count($Fiestas2017) ; $row++) {
    // echo "<p><b>Row number $row</b></p>";
    // echo "<ul>";
        for ($col = 0; $col < 6; $col++) {
          //echo "<li>".$Fiestas2017[$row][$col]."</li>";

          if( $Fiestas2017[$row][1]==$diaFest && $Fiestas2017[$row][0]== $cMonth){

            // Es San Patricio y se pinta de verde
            //$pdf->SetFillColor(2,157,116); y los ultimos parametros de cell 1 es para alterar color de fondo
            $pdf->SetFillColor(255,0,0);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(40,11, $diaFest,'LRT',0,'C',1);
            // A partir de aqui el resto de las celdas estaran en negro
            $pdf->SetTextColor(0,0,0);
            $dia_actual = $dia_actual + 1 ;
            break;

          //}
          }
        // echo "</ul>";
        }
    }
  return $dia_actual;
}

//Convierte color Hexadecimal en rgb
function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

// $pdf->AddPage('L','','');

// MostrarLunas($pdf, $cMonth, $cYear, $monthNames);

//Envio pdf a browser cuando se termine de ejecutar
//$pdf->Output('D',"calendario.pdf");
//Mustro pdf en browser
$pdf->Output();


?>


