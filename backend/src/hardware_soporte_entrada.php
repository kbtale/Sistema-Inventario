<?php
require("conexiondb.php");

	$stmt = $pdo->query('SELECT COUNT(id_entrada) from Entradas');
	$idE_h = $stmt->fetchColumn(0) + 1;

	$stmt = $pdo->prepare('SELECT COUNT(*) from Hardware WHERE bienes_hardware = :comprob');
	$stmt->execute([':comprob' => $_POST['bienes_entrada']]);

	if ($stmt->fetchColumn() > 0) {
		$stmt = $pdo->query('SELECT COUNT(id_hardware) from Hardware');
		$idHard = $stmt->fetchColumn(0) + 1;
		
		$stmt = $pdo->query('SELECT COUNT(id_estatus) from Estatus');
		$idEstatus = $stmt->fetchColumn(0) + 1;

		$stmt = $pdo->prepare('INSERT INTO Hardware (id_hardware, tipo_hardware, marca_hardware, modelo_hardware, bienes_hardware, usuario_hardware, fecha_ingreso, id_estatus) VALUES (:idHard, :tipo, :marca, :modelo, :bienes, :usuario, :fIngreso, :idEstatus)');
		$stmt->execute([
			':idHard' => $idHard,
			':tipo' => $_POST['tipo_entrada'] ?? '',
			':marca' => $_POST['marca_entrada'] ?? null,
			':modelo' => $_POST['modelo_entrada'] ?? null,
			':bienes' => $_POST['bienes_entrada'] ?? '',
			':usuario' => $_POST['asignado_entrada'] ?? 'OTIC',
			':fIngreso' => date('Y-m-d'),
			':idEstatus' => $idEstatus
		]);

		$stmt = $pdo->prepare('INSERT INTO Estatus (id_estatus, comentarios, estado_ingreso, c_ent, estado_actual_unidad) VALUES (:idEst, :comentarios, :eIngreso, :cEnt, :eActual)');
		$stmt->execute([
			':idEst' => $idEstatus,
			':comentarios' => $_POST['h_comentarios_salida'] ?? '',
			':eIngreso' => '0',
			':cEnt' => 0,
			':eActual' => 1
		]);
	}

	$stmt = $pdo->prepare('INSERT INTO Entradas (id_entrada, fecha_entrada, id_unidad_hardware, id_encargado, salida_pcp, fecha_salida_pcp, numero_orden, nom_responsable) VALUES (:idE, :fEntrada, :idUnidad, :idEncargado, :salPCP, :fSalPCP, :nOrden, :nomResp)');
	$stmt->execute([
		':idE' => $idE_h,
		':fEntrada' => date('Y-m-d'),
		':idUnidad' => $idHard ?? null,
		':idEncargado' => $_POST['encargado_entrada'] ?? null,
		':salPCP' => $_POST['pcp_entrada'] ?? null,
		':fSalPCP' => $_POST['pcpFecha_entrada'] ?? null,
		':nOrden' => $_POST['numero_orden'] ?? null,
		':nomResp' => $_POST['representante_entrada'] ?? null
	]);

?>