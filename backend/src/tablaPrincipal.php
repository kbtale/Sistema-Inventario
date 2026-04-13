<?php 

	require("conexiondb.php"); 


	$stmt = $pdo->query('SELECT e.*, h.tipo_hardware, h.marca_hardware, h.modelo_hardware, h.bienes_hardware, h.usuario_hardware 
	                    FROM Entradas e 
	                    LEFT JOIN Hardware h ON e.id_hardware = h.id_hardware 
	                    ORDER BY e.fecha_entrada DESC');
	$entradas = $stmt->fetchAll();

	if (count($entradas) > 0) {
		$salida .= "<table class='table table-hover border table-sm table-bordered' id='td'>
                        <thead>
                            <tr>
                                <th scope='col'>Nº Orden</th>
                                <th scope='col'>Tipo</th>
                                <th scope='col'>Marca</th>
                                <th scope='col'>Modelo</th>
                                <th scope='col'>Nª Bienes</th>
                                <th scope='col'>Asignado</th>
                                <th scope='col'>Fecha de Recepción</th>
                                <th scope='col'>Responsable</th>
                            </tr>
                        </thead>
                        <tbody>";

		foreach ($entradas as $fila) {
			$salida .= "<tr>
							<td>" . htmlspecialchars($fila['numero_orden'] ?? '-') . "</td>
							<td>" . htmlspecialchars($fila['tipo_hardware'] ?? '-') . "</td>
							<td>" . htmlspecialchars($fila['marca_hardware'] ?? '-') . "</td>
							<td>" . htmlspecialchars($fila['modelo_hardware'] ?? '-') . "</td>
							<td>" . htmlspecialchars($fila['bienes_hardware'] ?? '-') . "</td>
							<td>" . htmlspecialchars($fila['usuario_hardware'] ?? '-') . "</td>
							<td>" . htmlspecialchars($fila['fecha_entrada'] ?? '-') . "</td>
							<td>" . htmlspecialchars($fila['nom_responsable'] ?? '-') . "</td>
						</tr>";
		}
		$salida .= "</tbody></table>";
	} else {
		$salida .= "<div class='alert alert-info'>No Hay Datos...</div>";
	}

	echo $salida;

		$mysqli->close();

		



 ?>