<?php

include("conexiondb.php");
if (isset($estado)) {
	$stmt = $pdo->prepare('UPDATE Estatus SET estado_actual_unidad = :estado WHERE id_estatus = :idEstatus');
	$stmt->execute([
		':estado' => $estado,
		':idEstatus' => $idEstatus ?? 0
	]);
} else if (isset($_POST['comentarios_estatus'])) {
	$stmt = $pdo->prepare('UPDATE Estatus SET fallas = :fallas WHERE id_estatus = :idEstatus');
	$stmt->execute([
		':fallas' => $_POST['comentarios_estatus'],
		':idEstatus' => $idEstatus ?? 0
	]);
}
?>