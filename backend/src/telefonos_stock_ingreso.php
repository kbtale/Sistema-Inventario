<?php
require("conexiondb.php");

	$stmt = $pdo->prepare('SELECT COUNT(id_telefono) from Telefonos');
	$stmt->execute();
	$numRows = $stmt->fetchColumn(0); 
	$idTel = $numRows + 1;

	$stmt = $pdo->prepare('SELECT COUNT(id_estatus) from Estatus');
	$stmt->execute();
	$numRows = $stmt->fetchColumn(0); 
	$idEstatus = $numRows + 1;

	$stmt = $pdo->prepare('INSERT INTO Estatus (id_estatus, comentarios, estado_ingreso, c_ent, estado_actual_unidad) VALUES (:idEst, :comentarios, :estadoIngreso, :cEnt, :eActual)');
	$stmt->execute([
		':idEst' => $idEstatus,
		':comentarios' => $_POST['comentarios_ingCel'] ?? '',
		':estadoIngreso' => $_POST['estado_ingCel'] ?? '',
		':cEnt' => 0,
		':eActual' => 1
	]);

	$stmt = $pdo->prepare('INSERT INTO Telefonos (id_telefono, tipo_telefono, marca_telefono, modelo_telefono, nro_telefono, imei_telefono, imeisim_telefono, puk_telefono, usuario_asignado, id_estatus) VALUES (:idTel, :tipo, :marca, :modelo, :nro, :imei, :imeisim, :puk, :uAsig, :idEstatus)');

	$stmt->execute([
		':idTel' => $idTel,
		':tipo' => $_POST['tipo_ingCel'] ?? '',
		':marca' => $_POST['marca_ingCel'] ?? null,
		':modelo' => $_POST['modelo_ingCel'] ?? null,
		':nro' => $_POST['numero_ingCel'] ?? '',
		':imei' => $_POST['imei_ingCel'] ?? '',
		':imeisim' => $_POST['imeiSim_ingCel'] ?? '',
		':puk' => $_POST['puk_ingCel'] ?? '',
		':uAsig' => $_POST['asignado_ingCel'] ?? '',
		':idEstatus' => $idEstatus
	]);


?>