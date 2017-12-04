<?php date_default_timezone_set('America/Sao_Paulo'); ?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>
    <table id="table01" cellspacing="0">
      <tr>
        <th class="th0 thalign">
            <?php if( file_exists($arDadosEntidade[0]["logotipo"]) ) { ?>
            <img src="<?php echo $arDadosEntidade[0]["logotipo"]; ?>" />
            <?php } ?>
        </th>
        <th class=" thalign">
            <strong><?php echo $arDadosEntidade[0]["nom_entidade"]; ?></strong><br />
          Fone/Fax: <?php echo $arDadosEntidade[0]["fone"]; ?><br />
          E-mail: <?php echo $arDadosEntidade[0]["e_mail"]; ?><br />
          <?php echo $arDadosEntidade[0]["logradouro"]; ?> <br />
          CEP: <?php echo $arDadosEntidade[0]["cep"]; ?><br />
          CNPJ: <?php echo $arDadosEntidade[0]["cnpj"]; ?></th>
        <th class="th2 thalign">
          <table id="table02" border="1" bordercolor="#000000" cellspacing="0">
            <tr>
              <th colspan="2" align="left"><?php echo $arDadosRelatorio[0]["nom_modulo"]; ?></th>
              <th class="th3 thalign"> Versão: <?php echo $arDadosRelatorio[0]["versao"]; ?></th>
            </tr>
            <tr class="thalign">
              <td colspan="2"><?php echo $arDadosRelatorio[0]["nom_funcionalidade"]; ?></td>
              <td class="th3">Usuário: <?php echo Sessao::read('stUsername'); ?></td>
            </tr>
            <tr class="thalign">
              <td colspan="3"><?php echo $arDadosRelatorio[0]["nom_acao"]; ?></td>
            </tr>
            <tr class="thalign">
              <td colspan="3"><?php echo $stCampoPeriodo ?></td>
            </tr>
            <tr class="thalign">
              <td class="th4">Emissão: {DATE d/m/Y}</td>
              <td class="th5">Hora: {DATE H:i:s}</td>
              <td>Página: {PAGENO} de {nbpg}</td>
            </tr>
          </table>
        </th>
      </tr>
    </table>