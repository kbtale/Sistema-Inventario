<?php
require("conexiondb.php");


	$stmt = $pdo->prepare('SELECT nom_responsable from Entradas WHERE id_entrada = :idEntrada');
	$stmt->execute([':idEntrada' => $idEntrada ?? 0]);
	$nomResponsable = $stmt->fetchColumn(0);

	$stmt = $pdo->query('SELECT COUNT(id_salida) from Salidas');
	$idSal = $stmt->fetchColumn(0) + 1;

	$stmt = $pdo->prepare('INSERT INTO Salidas (id_salida, id_entrada, fecha_salida, nom_responsable, pcp, fecha_pcp, reporte, receptor) VALUES (:idS, :idE, :fSal, :nomResp, :pcp, :fpcp, :reporte, :receptor)');
	
	$stmt->execute([
		':idS' => $idSal,
		':idE' => $idEntrada ?? 0,
		':fSal' => date('Y-m-d'),
		':nomResp' => $nomResponsable,
		':pcp' => $_POST['h_pcp_salida'] ?? null,
		':fpcp' => $_POST['h_pcpFecha_salida'] ?? null,
		':reporte' => $_POST['h_comentarios_salida'] ?? '',
		':receptor' => $_POST['h_receptor_salida'] ?? ''
	]);

?>