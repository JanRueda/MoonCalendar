

<?php 

// Mes , Dia , Nombre Fiesta, R, G, B

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
  echo "<p><b>Row number $row</b></p>";
  echo "<ul>";
  for ($col = 0; $col < 6; $col++) {
    echo "<li>".$Fiestas2017[$row][$col]."</li>";
  }
  echo "</ul>";
}




 ?>

