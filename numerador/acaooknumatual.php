<?php require_once('../Connections/conexao.php'); ?>
<?php
$colname_atualizar = "1";
if (isset($_GET['id_num'])) {
  $colname_atualizar = $_GET['id_num'];
}
mysqli_select_db($conexao, $database_conexao);
$query_atualizar = sprintf("SELECT num_doc.id_num     , num_doc.cod_org     , num_doc.tipo_doc     , num_tipodoc.desc_tipo_doc     , num_doc.num_doc     , num_doc.cod_sec     , num_doc.ano_doc     , num_doc.assunto     , num_doc.destino     , num_doc.data     , num_doc.elaborador     , num_doc.obs_doc     , num_doc.ELABORADO     , num_doc.ASSINADO     , num_doc.ENCAMINHADO FROM num_doc     INNER JOIN num_tipodoc          ON (num_doc.tipo_doc = num_tipodoc.tipo_doc) WHERE (num_doc.id_num = '%s')", $colname_atualizar);
$atualizar = mysqli_query($conexao, $query_atualizar);
$row_atualizar = mysqli_fetch_assoc($atualizar);
$totalRows_atualizar = mysqli_num_rows($atualizar);
?>
<html>
<head>
<title>Numerador</title>
<link rel="icon" href="/numerador/public/gifs/favicon.png" type="image/png">
<link  href="/numerador/public/css/Geral.css?v=1753940642" rel="stylesheet" type="text/css">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="center">
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p> 
  <h2><?php echo $row_atualizar['desc_tipo_doc']; ?> n&ordm; <?php echo $row_atualizar['num_doc']; ?> / <font color="#333333"><?php echo $row_atualizar['cod_sec']; ?></font> 
    / <font color="#000000"><?php echo $row_atualizar['ano_doc']; ?></font> ATUALIZADO COM &Ecirc;XITO</h2>
  </p>
  <font color="#000099" size="4"><strong><font color="#FFFFFF" size="1"> 
  <script language="JavaScript" type="text/javascript">
function click() {
if (event.button==2||event.button==3) {
oncontextmenu='return false';
}
}
document.onmousedown=click
document.oncontextmenu = new Function("return false;")
  </script>
  </font></strong></font></div>
</body>
</html>
<?php
mysqli_free_result($atualizar);
?>

