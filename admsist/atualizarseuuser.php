<?php
// 1. INICIALIZAÇÃO E CONEXÃO
// =============================
require_once('../Connections/conexao.php');

// Define a ação do formulário para o próprio arquivo.
$editFormAction = htmlspecialchars($_SERVER['PHP_SELF']);
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlspecialchars($_SERVER['QUERY_STRING']);
}

// 2. LÓGICA DE ATUALIZAÇÃO DE DADOS
// =================================
// --- ATUALIZAÇÃO DOS DADOS PRINCIPAIS ---
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  
  $updateSQL = sprintf("UPDATE num_user SET postfunc=%s, guerra=%s, org_id=%s, nivel_id=%s, situacao=%s WHERE rerg=%s",
                       GetSQLValueString($conexao, $_POST['postfunc'], "text"),
                       GetSQLValueString($conexao, $_POST['guerra'], "text"),
                       GetSQLValueString($conexao, $_POST['org_id'], "int"),
                       GetSQLValueString($conexao, $_POST['Nivel'], "int"),
                       GetSQLValueString($conexao, $_POST['situacao'], "text"),
                       GetSQLValueString($conexao, $_POST['rerg'], "text"));

  $Result1 = mysqli_query($conexao, $updateSQL);

  if ($Result1) {
    // *** AQUI ESTÁ A CORREÇÃO PRINCIPAL ***
    // Agora passamos explicitamente a org_id para a página de sucesso.
    $org_id_param = urlencode($_POST['org_id']);
    $updateGoTo = "../numerador/acaookuser.php?org_id=" . $org_id_param; 
    
    header(sprintf("Location: %s", $updateGoTo));
    exit();
  } else {
    die("Erro ao atualizar os dados do usuário: " . mysqli_error($conexao));
  }
}

// --- ATUALIZAÇÃO DA SENHA ---
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "senhanova")) {
  
  $updateSQL = sprintf("UPDATE num_user SET senha=%s WHERE rerg=%s",
                       GetSQLValueString($conexao, md5($_POST['senha']), "text"),
                       GetSQLValueString($conexao, $_POST['rerg2'], "text"));

  $Result1 = mysqli_query($conexao, $updateSQL);

  if ($Result1) {
    // *** AQUI TAMBÉM PRECISAMOS DA org_id ***
    // Precisamos buscar a org_id do usuário para redirecionar corretamente.
    $query_org_id = sprintf("SELECT org_id FROM num_user WHERE rerg = %s", GetSQLValueString($conexao, $_POST['rerg2'], "text"));
    $org_id_result = mysqli_query($conexao, $query_org_id);
    $user_data = mysqli_fetch_assoc($org_id_result);
    $org_id_param = urlencode($user_data['org_id'] ?? '');

    $updateGoTo = "../numerador/acaookuser.php?org_id=" . $org_id_param;
    
    header(sprintf("Location: %s", $updateGoTo));
    exit();
  } else {
    die("Erro ao atualizar a senha do usuário: " . mysqli_error($conexao));
  }
}

// 3. CONSULTAS PARA PREENCHER O FORMULÁRIO
// ==========================================
$colname_user = "-1";
if (isset($_GET['rerg'])) {
  $colname_user = $_GET['rerg'];
}
$query_user = sprintf("SELECT * FROM num_user WHERE rerg = %s", GetSQLValueString($conexao, $colname_user, "text"));
$user_result = mysqli_query($conexao, $query_user);
$row_user = mysqli_fetch_assoc($user_result);
$totalRows_user = mysqli_num_rows($user_result);

$query_posto = "SELECT * FROM sai_posto ORDER BY cod_posto ASC";
$posto_result = mysqli_query($conexao, $query_posto);

$query_org = "SELECT * FROM num_org ORDER BY org_desc ASC";
$org_result = mysqli_query($conexao, $query_org);

$query_nivel = "SELECT * FROM num_nivel ORDER BY nivel_id ASC";
$nivel_result = mysqli_query($conexao, $query_nivel);
?>
<html>
<head>
<title>Numerador - Atualizar Usuário</title>
<link rel="icon" href="/numerador/public/gifs/favicon.png" type="image/png">
<link href="/numerador/public/css/Geral.css?v=1753940642" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1">
  <table align="center" bgcolor="#E6E6E6">
    <tr valign="baseline" bgcolor="#CCCCCC"> 
      <td colspan="2" align="center" nowrap><font color="#000099" size="3">Atualizar Cadastro de Usuário</font></td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">RE:</td>
      <td><?php echo htmlspecialchars($row_user['rerg'] ?? ''); ?><input type="hidden" name="rerg" value="<?php echo htmlspecialchars($row_user['rerg'] ?? ''); ?>"></td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Posto:</td>
      <td> 
        <select name="postfunc" id="postfunc">
          <option value="" <?php if (empty($row_user['postfunc'])) { echo "SELECTED"; } ?>>Selecionar...</option>
          <?php
          while($row_posto_loop = mysqli_fetch_assoc($posto_result)) {
            $selected = (($row_posto_loop['posto'] ?? '') == ($row_user['postfunc'] ?? '')) ? "SELECTED" : "";
            echo "<option value=\"" . htmlspecialchars($row_posto_loop['posto'] ?? '') . "\" $selected>" . htmlspecialchars($row_posto_loop['posto'] ?? '') . "</option>";
          }
          ?>
        </select>
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Guerra:</td>
      <td><input type="text" name="guerra" value="<?php echo htmlspecialchars($row_user['guerra'] ?? ''); ?>" size="32"></td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Seção:</td>
      <td> 
        <select name="org_id" id="org_id">
          <option value="" <?php if (empty($row_user['org_id'])) { echo "SELECTED"; } ?>>Selecionar...</option>
          <?php
          mysqli_data_seek($org_result, 0);
          while($row_org_loop = mysqli_fetch_assoc($org_result)) {
            $selected = (($row_org_loop['org_id'] ?? '') == ($row_user['org_id'] ?? '')) ? "SELECTED" : "";
            echo "<option value=\"" . ($row_org_loop['org_id'] ?? '') . "\" $selected>" . htmlspecialchars($row_org_loop['org_desc'] ?? '') . "</option>";
          }
          ?>
        </select> 
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Nível:</td>
      <td> 
        <select name="Nivel" id="Nivel">
          <option value="" <?php if (empty($row_user['nivel_id'])) { echo "SELECTED"; } ?>>Selecione...</option>
          <?php
          mysqli_data_seek($nivel_result, 0);
          while($row_nivel_loop = mysqli_fetch_assoc($nivel_result)) {
            $selected = (($row_nivel_loop['nivel_id'] ?? '') == ($row_user['nivel_id'] ?? '')) ? "SELECTED" : "";
            echo "<option value=\"" . ($row_nivel_loop['nivel_id'] ?? '') . "\" $selected>" . htmlspecialchars($row_nivel_loop['desc_nivel'] ?? '') . "</option>";
          }
          ?>
        </select> 
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Função:</td>
      <td><input type="text" name="situacao" value="<?php echo htmlspecialchars($row_user['situacao'] ?? ''); ?>" size="32"></td>
    </tr>
    <tr valign="baseline"> 
      <td colspan="2" align="center" nowrap bgcolor="#CCCCCC"> 
        <input type="submit" value="Atualizar Registro">
      </td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
</form>

<br/>

<form action="<?php echo $editFormAction; ?>" method="POST" name="senhanova" id="senhanova">
  <table align="center" bgcolor="#E6E6E6">
    <tr valign="baseline" bgcolor="#CCCCCC"> 
      <td colspan="2" align="center" nowrap><font color="#000099" size="3">Trocar a Senha</font></td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">Senha Nova:</td>
      <td><input name="senha" type="password" size="32"></td>
    </tr>
    <tr valign="baseline"> 
      <td colspan="2" align="center" nowrap bgcolor="#CCCCCC"> 
        <input name="rerg2" type="hidden" id="rerg2" value="<?php echo htmlspecialchars($row_user['rerg'] ?? ''); ?>">
        <input name="submit" type="submit" value="Trocar a Senha">
      </td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="senhanova">
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
// 4. LIBERAÇÃO DE MEMÓRIA
// =======================
if($user_result) mysqli_free_result($user_result);
if($posto_result) mysqli_free_result($posto_result);
if($org_result) mysqli_free_result($org_result);
if($nivel_result) mysqli_free_result($nivel_result);
?>