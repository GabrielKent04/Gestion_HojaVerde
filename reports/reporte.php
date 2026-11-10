<?php

session_start();
date_default_timezone_set('America/Caracas');

if (!isset($_SESSION['id_usuario']) ) 
{
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['rol'] !== 'Gerente') {
    header("Location: ../modules/gestion/index.php");
    exit();
}

$laboratorio = trim($_GET['laboratorio'] ?? '');

require_once '../config/conexion.php';

if(empty($laboratorio))
{
    $sql = "SELECT * FROM medicamentos WHERE estado = 1 
    ORDER BY nombre ASC";
     $stmt = $pdo->prepare($sql);

     $stmt->execute();
     $medicamentos = $stmt->fetchAll();
     $subtitulo_filtro = "Filtro: Todos los laboratorios";
}

else
{
    $sql = "SELECT * FROM medicamentos WHERE estado = 1 AND laboratorio = :laboratorio
    ORDER BY nombre ASC";
     $stmt = $pdo->prepare($sql);

     $stmt->execute([':laboratorio' => $laboratorio]);
     $medicamentos = $stmt->fetchAll();
     $subtitulo_filtro = "Filtro por Laboratorio: " . $laboratorio;
}

require_once 'fpdf.php';
class PDF extends FPDF {
    public $subtitulo_filtro = '';
    public $usuario_emisor = '';
    function Header() {
        $this->SetFillColor(20, 69, 23);
        $this->Rect(0, 0, 210, 8, 'F');
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(20, 69, 23);
        $this->Cell(0, 10, utf8_decode('CENTRO NATURISTA HOJA VERDE'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(46, 125, 50);
        $this->Cell(0, 6, utf8_decode('REPORTE OFICIAL DE INVENTARIO Y DISPONIBILIDAD'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 116, 139);
        $fecha_hora = date('d/m/Y - h:i A');
        $this->Cell(0, 5, utf8_decode("Generado por: {$this->usuario_emisor} | Fecha: {$fecha_hora}"), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode($this->subtitulo_filtro), 0, 1, 'C');
        $this->Ln(4);
        $this->SetFillColor(27, 94, 32);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(22, 8, utf8_decode('CÓDIGO'), 1, 0, 'C', true);
        $this->Cell(68, 8, utf8_decode('MEDICAMENTO BOTÁNICO'), 1, 0, 'L', true);
        $this->Cell(45, 8, utf8_decode('LABORATORIO'), 1, 0, 'L', true);
        $this->Cell(23, 8, utf8_decode('UBICACIÓN'), 1, 0, 'C', true);
        $this->Cell(16, 8, utf8_decode('STOCK'), 1, 0, 'C', true);
        $this->Cell(16, 8, utf8_decode('PRECIO'), 1, 1, 'C', true);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 10, utf8_decode('Hoja Verde — Sistema de Control de Inventario | Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->subtitulo_filtro = $subtitulo_filtro;
$pdf->usuario_emisor = $_SESSION['username'] ?? 'Gerente';
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8.5);
$pdf->SetTextColor(15, 23, 42);
$total_unidades = 0;
$total_valor = 0;
$relleno = false;

foreach ($medicamentos as $med) {
    if ($relleno) {
        $pdf->SetFillColor(248, 250, 248);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }
    $precio_formateado = '$' . number_format($med['precio'], 2);
    $total_unidades += (int)$med['stock'];
    $total_valor += ((int)$med['stock'] * (float)$med['precio']);
    $pdf->Cell(22, 7, utf8_decode($med['codigo_ref']), 'LRB', 0, 'C', true);
    $pdf->Cell(68, 7, utf8_decode(substr($med['nombre'], 0, 38)), 'RB', 0, 'L', true);
    $pdf->Cell(45, 7, utf8_decode(substr($med['laboratorio'], 0, 25)), 'RB', 0, 'L', true);
    $pdf->Cell(23, 7, utf8_decode($med['codigo_ubicacion']), 'RB', 0, 'C', true);
    $pdf->Cell(16, 7, (string)$med['stock'], 'RB', 0, 'C', true);
    $pdf->Cell(16, 7, $precio_formateado, 'RB', 1, 'R', true);
    $relleno = !$relleno;
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(232, 245, 233);
$pdf->SetTextColor(20, 69, 23);
$total_items = count($medicamentos);
$valor_total_formateado = '$' . number_format($total_valor, 2);
$pdf->Cell(90, 8, utf8_decode(" Total de Productos Listados: {$total_items}"), 1, 0, 'L', true);
$pdf->Cell(50, 8, utf8_decode(" Unidades en Stock: {$total_unidades}"), 1, 0, 'C', true);
$pdf->Cell(50, 8, utf8_decode(" Valor Total: {$valor_total_formateado}"), 1, 1, 'R', true);
$pdf->Output('I', 'Reporte_Inventario_HojaVerde.pdf');
exit();
?>