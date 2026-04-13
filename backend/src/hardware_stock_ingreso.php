<?php

require("conexiondb.php");


	$stmt = $pdo->prepare('SELECT COUNT(id_hardware) from Hardware');
	$stmt->execute();
	$numRows = $stmt->fetchColumn(0); 
	$idHard = $numRows + 1;

	$stmt = $pdo->prepare('SELECT COUNT(id_estatus) from Estatus');
	$stmt->execute();
	$numRows = $stmt->fetchColumn(0); 
	$idEstatus = $numRows + 1;

	$stmt = $pdo->prepare('INSERT INTO Estatus (id_estatus, comentarios, estado_ingreso, c_ent, estado_actual_unidad) VALUES (:idEst, :comentarios, :estadoIngreso, :cEnt, :eActual)');
	$stmt->execute([
		':idEst' => $idEstatus,
		':comentarios' => 'Nada, por el momento.',
		':estadoIngreso' => $_POST['h_estado_ingreso'] ?? '',
		':cEnt' => 0,
		':eActual' => 1
	]);

	$stmt = $pdo->prepare('INSERT INTO Hardware (id_hardware, tipo_hardware, marca_hardware, modelo_hardware, bienes_hardware, usuario_hardware, fecha_ingreso, id_estatus) VALUES (:idHard, :tipo, :marca, :modelo, :bienes, :usuario, :fIngreso, :idEstatus)');
	
	$stmt->execute([
		':idHard' => $idHard,
		':tipo' => $_POST['h_tipo_ingreso'] ?? '',
		':marca' => $_POST['h_marca_ingreso'] ?? null,
		':modelo' => $_POST['h_modelo_ingreso'] ?? null,
		':bienes' => $_POST['h_bienes_ingreso'] ?? '',
		':usuario' => 'OTIC',
		':fIngreso' => $_POST['h_fechaEnt_ingreso'] ?? date('Y-m-d'),
		':idEstatus' => $idEstatus
	]);


?>