<?php
require("conexiondb.php");

	$stmt = $pdo->query('SELECT COUNT(id_telefono) from Telefonos');
	$idTel = $stmt->fetchColumn(0) + 1;

	$stmt = $pdo->query('SELECT COUNT(id_estatus) from Estatus');
	$idEstatus = $stmt->fetchColumn(0) + 1;

	$stmt = $pdo->query('SELECT COUNT(id_entrada) from Entradas');
	$idE_t = $stmt->fetchColumn(0) + 1;

	$stmt = $pdo->prepare('INSERT INTO Estatus (id_estatus, comentarios, estado_ingreso, c_ent, estado_actual_unidad) VALUES (:idEst, :comentarios, :eIngreso, :cEnt, :eActual)');
	$stmt->execute([
		':idEst' => $idEstatus,
		':comentarios' => $_POST['comentarios_entCel'] ?? '',
		':eIngreso' => '0',
		':cEnt' => 0,
		':eActual' => 1
	]);

	$stmt = $pdo->prepare('INSERT INTO Telefonos (id_telefono, tipo_telefono, marca_telefono, modelo_telefono, nro_telefono, imei_telefono, imeisim_telefono, puk_telefono, usuario_asignado, id_estatus) VALUES (:idTel, :tipo, :marca, :modelo, :nro, :imei, :imeisim, :puk, :uAsig, :idEstatus)');
	$stmt->execute([
		':idTel' => $idTel,
		':tipo' => $_POST['tipo_entCel'] ?? '',
		':marca' => $_POST['marca_entCel'] ?? null,
		':modelo' => $_POST['modelo_entCel'] ?? null,
		':nro' => $_POST['numero_entCel'] ?? '',
		':imei' => $_POST['imei_entCel'] ?? '',
		':imeisim' => $_POST['imeiSim_entCel'] ?? '',
		':puk' => $_POST['puk_entCel'] ?? '',
		':uAsig' => $_POST['asignado_entCel'] ?? 'OTIC',
		':idEstatus' => $idEstatus
	]);

	$stmt = $pdo->prepare('INSERT INTO Entradas (id_entrada, fecha_entrada, id_unidad_hardware, id_encargado, salida_pcp, fecha_salida_pcp, numero_orden, nom_responsable) VALUES (:idE, :fEntrada, :idUnidad, :idEncargado, :salPCP, :fSalPCP, :nOrden, :nomResp)');
	$stmt->execute([
		':idE' => $idE_t,
		':fEntrada' => date('Y-m-d'),
		':idUnidad' => $idTel,
		':idEncargado' => 'OTIC',
		':salPCP' => $_POST['pcp_entCel'] ?? null,
		':fSalPCP' => $_POST['pcpFecha_entCel'] ?? null,
		':nOrden' => $_POST['numero_orden'] ?? null,
		':nomResp' => $_POST['representante_entCel'] ?? 'OTIC'
	]);

?>