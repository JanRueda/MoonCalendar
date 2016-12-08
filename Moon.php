<?php 

//========================================================================================
//
//  Contiene todas las funciones relativas para mostrar las lunas
//  
//========================================================================================

//Muestra las fases de las lunas gracias al api
function MostrarLunas($pdf, $cMonth, $cYear, $monthNames,$dir){

  $date = $cMonth.'/01/'.$cYear;
  $moon_phase = 5;

  $Url = 'http://api.usno.navy.mil/moon/phase?date='.$date.'&nump='.$moon_phase;
  $mi_cadena = '';

  $mi_url= $Url; 

  $fo= fopen("$mi_url","r") or die ("No se ha encontrado la pagina."); 

  while ( !feof($fo) ) { 

    $mi_cadena .= fgets($fo, 4096); 

  
  } 

  fclose ($fo); 

  //Variable de prueba para ver resultado de la api
  $q = 0;

  if ( $q == 1 ){

     $pdf->Write(5,$mi_cadena);

  }  
  else {

  //Convierto json en array
  $mi_cadena = json_decode($mi_cadena, true);

  //Numero de fases en este mes
  $Total_fases_luna = $mi_cadena['numphases'];

  //$pdf->Write(5,'Numero de Fases Lunares >>  '.$mi_cadena['numphases']);
  //$pdf->Ln();
  $pdf->SetXY(170,20);
  //$pdf->Write(5, $monthNames[$cMonth-1].' '.$cYear);
  $pdf->Ln(2);

  //Guardo en un arry el mes actual dado por la api
  $fecha_Fase_Lunar = $mi_cadena['phasedata'][0]['date'];
  $fecha_Fase_Lunar_no_space = explode(" ", $fecha_Fase_Lunar);
  // Separo el mes dentro de la información del array
  $Control_mes_Lunar = $fecha_Fase_Lunar_no_space[1];

    for ($i=0; $i < $Total_fases_luna; $i++) { 

      //Muestro array
      $tipo_Luna = $mi_cadena['phasedata'][$i]['phase'];
      // $pdf->Write(5,$tipo_Luna);
      // $pdf->Write(5,' >>> ');
      // Solo mostrar dia, separo por espacios y muestro dia
      $fecha_Fase_Lunar = $mi_cadena['phasedata'][$i]['date'];
      $fecha_Fase_Lunar_no_space = explode(" ", $fecha_Fase_Lunar);
      $dia_luna = $fecha_Fase_Lunar_no_space[2];
      // Separo el mes dentro de la información del array
      $mes_luna = $fecha_Fase_Lunar_no_space[1];

      //Controlo el ultimo evento lunar para que esté dentro del mes
      //Esto se hace porque existen meses con 5 lunas
      If ($Control_mes_Lunar == $mes_luna){

      	Moon_2($pdf,$tipo_Luna, $dia_luna,$i,$dir);

      }
      	  

      
       
    }

    $pdf->Ln();

  }
}


//Traduce el nombre al castellano para mostrar
function nombre_fase($tipo_Luna){

  if ( $tipo_Luna == 'New Moon' ) {

    $tipo_Luna = 'Nueva';
  }
  elseif ( $tipo_Luna == 'First Quarter' ) {

    $tipo_Luna = 'Creciente';
  }
  elseif ( $tipo_Luna == 'Full Moon' ) {

    $tipo_Luna = 'LLena';
  }
  else{

    $tipo_Luna = 'Menguante';
  }

  return $tipo_Luna;
}


//Muestra las imagenes de la luna y las celdas con el nombre de la fase lunar que le corresponde.
function Moon_2($pdf,$tipo_Luna, $dia_luna,$i,$dir){

  $tipo_Luna = nombre_fase($tipo_Luna);

  // $pdf->AddPage();
  //$pdf->Ln(5);
  $pdf->SetFont('Arial','',15);
  //$pdf->Ln(5);
  //$pdf->Cell(40,10,$tipo_Luna.' '.$dia_luna,'LRBT', 0,'C');
  //Coordenadas para mostrar las imagenes de la luna
  if ( $i == 0 ){

    $Coord_X = 20;
    $Coord_Y = 5;
  }
  elseif ( $i == 1 ) {
    $Coord_X = 60;
    $Coord_Y = 5;
  }
  elseif ( $i == 2 ) {
    $Coord_X = 100;
    $Coord_Y = 5;
  }
  elseif ( $i == 3 ){
    $Coord_X = 140;
    $Coord_Y = 5;
  } 
  else {
    $Coord_X = 180;
    $Coord_Y = 5;
  } 

  $pdf->Image($dir.'img/'.$tipo_Luna.'.JPG' , $Coord_X ,$Coord_Y,'JPG');
  $pdf->Cell(40,10,$tipo_Luna.' '.$dia_luna,'LRBT', 0,'C');
}




 ?>