<?php
require("conexiondb.php");

	$stmt = $pdo->query('SELECT COUNT(id_usuario) from Usuarios');
	$idUser = $stmt->fetchColumn(0) + 1;

	if (isset($_POST['inop_asigCel'])) {
		$stmt = $pdo->prepare('UPDATE Estatus SET estado_actual_unidad = 2 WHERE id_estatus = :idEstatus');
		$stmt->execute([':idEstatus' => $idEstatus ?? 0]);
	} else {
		$stmt = $pdo->prepare('INSERT INTO Usuarios (id_usuario, nombre_usuario, apellido_usuario, ci_usuario) VALUES (:idU, :nomU, :apeU, :ciU)');
		$stmt->execute([
			':idU' => $idUser,
			':nomU' => $_POST['nom_asigCel'] ?? '',
			':apeU' => $_POST['apellido_asigCel'] ?? '',
			':ciU' => $_POST['ci_asigCel'] ?? ''
		]);
	}

?>