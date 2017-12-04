<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Arquivo de instância para Domicilio
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19067 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 09:33:57 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.96
*/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
  include_once '../cse.class.php';
  include_once (CAM_FW_LEGADO."configuracaoLegado.class.php");
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");

    //echo $ctrl."<br>";
    if (!(isset($ctrl)))
        $ctrl = 0;

    if (isset($pagina)) {
        $ctrl = 0;
        $arFiltro = $sessao->transf4 ;
        foreach ($arFiltro as $indice => $valor) {
            $$indice = $valor;
            $_POST['true'] = true;
        }
    }

    switch ($ctrl) {
        case 0:
            if ( isset($pagina) ) {
                unset($sessao->transf2);
            }
?>
<script type="text/javascript">

    function atualizaMunicipio()
    {
        document.frm.target = "oculto";
        document.frm.ctrl.value = 3;
        document.frm.submit();
    }

    function zeraMunicipio()
    {
        limpaSelect(document.frm.municipio,1);
        document.frm.txtMunicipio.disabled = true;
        document.frm.municipio.disabled = true;
    }

    function Valida()
    {
        return true;
    }

    function Salvar()
    {
         if (Valida()) {
            document.frm.target = "telaPrincipal";
            document.frm.ctrl.value = 0;
            document.frm.submit();
        }
    }
    function limpaLista()
    {
        aux = document.getElementById("lista");
        aux.innerHTML = "&nbsp;";
    }
</script>
<form action="<?=$PHP_SELF?>?<?=$sessao->id?>" method="POST" name="frm">
<input type="hidden" name="codDomicilio" value="<?=$codDomicilio?>">
<input type="hidden" name="ctrl" value="1">
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Dados para Filtro</td>
    </tr>
    <tr>
        <td class="label" title="Código do logradouro">Código</td>
        <td class="field">
            <input type="text" name="codDom" value="<?=$codDom;?>" size="9" maxlength="9" onKeyPress="return(isValido(this,event,'0123456789'));" onChange="JavaScript: limpaLista();">
        </td>
    </tr>
    <tr>
        <td class="label" title="Cep do logradouro">CEP</td>
        <td class="field">
            <input type="text" name="cep" value="<?=$cep;?>" size="9" maxlength="9" onKeyUp="mascaraCEP(this, event);return autoTab(this, 9, event); " onKeyPress="return(isValido(this,event,'0123456789'));" onChange="JavaScript: limpaLista();">
        </td>
    </tr>
    <tr>
        <td class="label" width="30%" title="Uf do logradouro">Estado</td>
        <td class="field" width="70%">
<?php
if ( strToUpper($estado) == "XXX") {
    $estado = "";
}
?>
            <input type="text" name="txtEstado" maxlength="4" size="4" value="<?=$estado;?>" onChange ="javascript: limpaLista();if ( preencheCampo(this, document.frm.estado) ) { atualizaMunicipio() } else { zeraMunicipio();}" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="estado" onChange="javascript: preencheCampo(this, document.frm.txtEstado);atualizaMunicipio();limpaLista();" style="width: 300px">
                    <option value="xxx">Selecione um estado</option>
<?php
    $sSQL = "SELECT * FROM sw_uf WHERE cod_uf > 0 ORDER by nom_uf";
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $comboEstado = "";
    while (!$dbEmp->eof()) {
        $codg_uf  = trim($dbEmp->pegaCampo("cod_uf"));
        $nomg_uf  = trim($dbEmp->pegaCampo("nom_uf"));
        $dbEmp->vaiProximo();
        if ($codg_uf == $estado) {
            $comboEstado .= "                    <option value=".$codg_uf." selected>".$nomg_uf."</option>\n";
        } else {
            $comboEstado .= "                    <option value=".$codg_uf.">".$nomg_uf."</option>\n";
        }
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo "$comboEstado";
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Município do logradouro">Município</td>
        <td class="field">
            <?php
    if ( strToUpper($estado) != "" ) {
        $stDisabled = "";
    } else {
        $estado = "";
        $stDisabled = " disabled";
    }
    if ( strToUpper($municipio) == "XXX") {
        $municipio = "";
    }

    if ( !empty($estado)) {
        $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$estado." ORDER by nom_municipio";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $comboMunicipio = "";
        while (!$dbEmp->eof()) {
            $stDisabled = "";
            $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
            $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
            $dbEmp->vaiProximo();
            $comboMunicipio .= "                    ";
            $comboMunicipio .= "<option value='".$codg_municipio."'";
            if ($codg_municipio == $municipio) {
                $comboMunicipio .= " SELECTED";
            }
            $comboMunicipio .= ">".$nomg_municipio."</option>\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }
?>
                <input type="text" name="txtMunicipio" maxlength="4" size="4" value="<?=$municipio;?>" <?=$stDisabled;?> onChange="JavaScript: preencheCampo(this, document.frm.municipio);limpaLista();" onKeyPress="return(isValido(this,event,'0123456789'));">
                <select name="municipio" style="width: 300px"<?=$stDisabled;?>  onChange="JavaScript: preencheCampo(this, document.frm.txtMunicipio);limpaLista();">
                    <option value="xxx">Selecione uma cidade</option>
<?=$comboMunicipio;?>
                </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo do logradouro">Tipo Logradouro</td>
        <td class="field">
<?php
if ( strToUpper($codTipoLogradouro) == "XXX" ) {
    $codTipoLogradouro = "";
}
?>
            <input type="text" name="codTxtTipoLogradouro" maxlength="4" size="4" value="<?=$codTipoLogradouro?>" onChange="javascript: preencheCampo(this, document.frm.codTipoLogradouro);limpaLista();" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codTipoLogradouro" onChange="javascript: preencheCampo(this, document.frm.codTxtTipoLogradouro);limpaLista();">
                <option value="xxx">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                   .$stQuebra;
$select .= "     cod_tipo, "            .$stQuebra;
$select .= "     nom_tipo "             .$stQuebra;
$select .= " FROM "                     .$stQuebra;
$select .= "     sw_tipo_logradouro "  .$stQuebra;
$select .= " WHERE "                    .$stQuebra;
$select .= "     cod_tipo > 0 "         .$stQuebra;
$select .= " ORDER BY "                 .$stQuebra;
$select .= "     nom_tipo "             .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $codTipo = $dbConfig->pegaCampo("cod_tipo");
    $stTipo  = $dbConfig->pegaCampo("nom_tipo");
    if ($codTipoLogradouro == $codTipo) {
        $stSelected = " selected";
    } else {
        $stSelected = "";
    }
    echo "                <option value=".$codTipo.$stSelected.">".$stTipo."</option>\n";
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Nome aplicado ao logradouro">Logradouro</td>
        <td class="field">
            <input type="text" name="logradouro" value="<?=$logradouro?>" size="30" maxlength="60" onChange="JavaScript: limpaLista();">
        </td>
    </tr>
    <tr>
        <td class="label" title="Número do logradouro">Número</td>
        <td class="field">
            <input type="text" name="numero" value="<?=$numero;?>" size="6" maxlength="6" onChange="JavaScript: limpaLista();" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="label" title="Complemento do endereço">Complemento</td>
        <td class="field">
            <input type="text" name="complemento" value="<?=$complemento;?>" size="30" maxlength="60" onChange="JavaScript: limpaLista();">
        </td>
    </tr>
    <tr>
        <td class="label" title="Bairro do logradouro">Bairro</td>
        <td class="field">
            <input type="text" name="bairro" value="<?=$bairro;?>" size="30" maxlength="60" onChange="JavaScript: limpaLista();">
        </td>
    </tr>
    <tr>
        <td class="label" title="Fone do logradouro">Fone</td>
        <td class="field">
            <input type="text" name="telefone" value="<?=$telefone;?>" size="10" maxlength="10" onChange="JavaScript: limpaLista();" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <input type="button" name="ok" value="OK" style="width: 60px" onClick="javascript: Salvar();">
        </td>
    </tr>
</table>
</font>
<span id="lista">
<?php
    //break;
    //case 1:
    if ( count( $_POST ) ) {
        $sessao->transf4 = "";
        $arFiltro = array();
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $stQuebra = "<br>";
        $select  = " SELECT "                   .$stQuebra;
        $select .= "     cod_domicilio, "       .$stQuebra;
        $select .= "     logradouro, "          .$stQuebra;
        $select .= "     numero, "              .$stQuebra;
        $select .= "     complemento "          .$stQuebra;
        $select .= " FROM "                     .$stQuebra;
        $select .= "     cse.domicilio "    .$stQuebra;
        $select .= " WHERE "                    .$stQuebra;
        $select .= "     cod_domicilio > 0 "    .$stQuebra;
        if ($codDom) {
            $select .= " AND cod_domicilio = ".$codDom." "           .$stQuebra;
            $arFiltro['codDom'] = $codDom;
        }
        if ($cep) {
            $select .= " AND cep = ".str_replace( "-", "", $cep )." "           .$stQuebra;
            $arFiltro['cep'] = $cep;
        }
        if ( strtoupper($estado) != 'XXX' and $estado != "" ) {
            $select .= " AND cod_uf = ".$estado." "                             .$stQuebra;
            $arFiltro['estado'] = $estado;
        }
        if ( strtoupper($municipio) != 'XXX' and $municipio) {
            $select .= " AND cod_municipio = ".$municipio." "                   .$stQuebra;
            $arFiltro['municipio'] = $municipio;
        }
        if ( strtoupper($codTipoLogradouro) != 'XXX' and strtoupper($codTipoLogradouro) != '' ) {
            $select .= " AND cod_tipo_logradouro = ".$codTipoLogradouro." "     .$stQuebra;
            $arFiltro['codTipoLogradouro'] = $codTipoLogradouro;
        }
        if ($logradouro) {
            $select .= " AND UPPER(logradouro) LIKE UPPER('%".$logradouro."%') "." ".$stQuebra;
            $arFiltro['logradouro'] = $logradouro;
        }
        if ($numero) {
            $select .= " AND numero = ".$numero." ".$stQuebra;
            $arFiltro['numero'] = $numero;
        }
        if ($complemento) {
            $select .= " AND complemento = '".$complemento."' "." "             .$stQuebra;
            $arFiltro['complemento'] = $complemento;
        }
        if ($bairro) {
            $select .= " AND UPPER(bairro) LIKE UPPER('%".$bairro."%') "." "    .$stQuebra;
            $arFiltro['bairro'] = $bairro;
        }
        if ($telefone) {
            $select .= " AND telefone LIKE '%".$telefone."%' "                  .$stQuebra;
            $arFiltro['telefone'] = $telefone;
        }
        $select = str_replace( "<br>", "", $select );
        //echo $select;
        $sessao->transf4 = $arFiltro;
        if (!(isset($pagina))) {
            $sessao->transf = "";
            $sessao->transf = $select;
        }
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"15");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(logradouro)","ASC");
        $sSQL = $paginacao->geraSQL();
        $inCont = $paginacao->contador();
        $dbConfig->abreSelecao($sSQL);
        while (!$dbConfig->eof()) {
            $lista[] = $dbConfig->pegaCampo("cod_domicilio")."/".$dbConfig->pegaCampo("logradouro")."/".
            $dbConfig->pegaCampo("numero")."/".$dbConfig->pegaCampo("complemento");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
    <table width="100%">
        <tr>
            <td class="alt_dados" colspan="4">Logradouros cadastrados</td>
        </tr>
        <tr>
            <td class='label' width='5%'>&nbsp;</td>
            <td class='labelleft' width='12%'>Código</td>
            <td class='labelleft' width='80%'>Logradouro</td>
            <td class='label'>&nbsp;</td>
        </tr>
<?php
        if ( count($lista) ) {
            while (list ($cod, $val) = each ($lista)) { //mostra os tipos de processos na tela
                $fim = explode("/", $val);
?>
        <tr>
            <td class='label'><?=$inCont++;?></td>
            <td class='show_dados_right'><?=$fim[0];?></td>
<?php
$endereco = $fim[1];
if ($fim[2]) {
    $endereco .= " - ".$fim[2];
}
if ($fim[3]) {
    $endereco .= " - ".$fim[3];
}
?>
            <td class='show_dados'><?=$endereco;?></td>
            <td class='botao'>
            <a href='<?=$PHP_SELF;?>?<?=$sessao->id;?>&ctrl=2&codDomicilio=<?=$fim[0];?>&pg=<?=$pagina?>'>
            <img src='<?=CAM_FW_IMAGENS."procuracgm.gif";?>' border='0'>
            </a>
            </td>
        </tr>
<?php
            }
        } else {
?>
        <tr>
            <td class="show_dados_center" colspan="4">
                <b>Nenhum registro encontrado!</b>
            </td>
        </tr>
<?php
        }
?>
        </table>
        <table width="450 "align="center">
            <tr>
                <td align="center">
                    <font size="2">
                        <?php $paginacao->mostraLinks();?>
                    </font>
                </td>
            </tr>
        </table>
</span>
<?php
    }
    break;
    case 2:
            $codMunicipio = pegaConfiguracao("cod_municipio");
            $codUf = pegaConfiguracao("cod_uf");
            $nomMunicipio = pegaConfiguracao("nom_municipio");
            $configuracao = new configuracaoLegado;
            $configuracao->setaEstadoAtual($codUf);
            $configuracao->listaComboEstados();
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $select =   "SELECT *
                        FROM
                        cse.domicilio
                        WHERE
                        cod_domicilio = ".$codDomicilio;
            $dbConfig->abreSelecao($select);
            $var = array(
            codDomicilio=>$dbConfig->pegaCampo("cod_domicilio"),
            estado=>$dbConfig->pegaCampo("cod_uf"),
            municipio=>$dbConfig->pegaCampo("cod_municipio"),
            codLocalidade=>$dbConfig->pegaCampo("cod_localidade"),
            codTipoDomicilio=>$dbConfig->pegaCampo("cod_tipo_domicilio"),
            codConstrucao=>$dbConfig->pegaCampo("cod_construcao"),
            codSituacao=>$dbConfig->pegaCampo("cod_situacao"),
            codTipoLogradouro=>$dbConfig->pegaCampo("cod_tipo_logradouro"),
            logradouro=>$dbConfig->pegaCampo("logradouro"),
            numero=>$dbConfig->pegaCampo("numero"),
            complemento=>$dbConfig->pegaCampo("complemento"),
            bairro=>$dbConfig->pegaCampo("bairro"),
            cep=>$dbConfig->pegaCampo("cep"),
            telefone=>$dbConfig->pegaCampo("telefone"),
            qtdComodos=>$dbConfig->pegaCampo("qtd_comodos"),
            energia=>$dbConfig->pegaCampo("energia_eletrica"),
            qtdResidentes=>$dbConfig->pegaCampo("qtd_residentes"),
            qtdGravidas=>$dbConfig->pegaCampo("qtd_gravidas"),
            qtdMaesAmamentando=>$dbConfig->pegaCampo("qtd_maes_amamentando"),
            qtdDeficientes=>$dbConfig->pegaCampo("qtd_deficientes"),
            codCobertura=>$dbConfig->pegaCampo("cod_cobertura"),
            codAbastecimento=>$dbConfig->pegaCampo("cod_abastecimento"),
            codTratamentoAgua=>$dbConfig->pegaCampo("cod_tratamento"),
            codEsgotamento=>$dbConfig->pegaCampo("cod_esgotamento"),
            codDestinoLixo=>$dbConfig->pegaCampo("cod_destino_lixo")
            );
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
            foreach ($var as $campo => $valor) {
                $$campo = $valor;
            }
            $cep = formataCep($cep);
            //echo $select;
?>
<script type="text/javascript">
      function Volta()
      {
            mudaTelaPrincipal("<?=$PHP_SELF?>?<?=$sessao->id?>&ctrl=0&pagina=<?=$pg?>");
      }
</script>

<form action="<?=$PHP_SELF?>?<?=$sessao->id?>" method="POST" name="frm">
<input type="hidden" name="codDomicilio" value="<?=$codDomicilio?>">
<input type="hidden" name="ctrl" value="">
<input type="hidden" name="pg" value="<?=$pg;?>">
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Dados do domicílio</td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Localização</td>
    </tr>
    <tr>
        <td class="label" title="Cep do logradouro">*CEP</td>
        <td class="field">
            <?=$cep;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="30%" title="Uf do logradouro">*Estado</td>
        <td class="field" width="70%">
            <?=$estado;?>&nbsp;
            <?=pegaDado("nom_uf","sw_uf"," where cod_uf = ".$estado);?>
            <input type="hidden" name="cod_uf" value="<?=$codEstado;?>">
            <input type="hidden" name="cod_municipio" value="">
        </td>
    </tr>
    <tr>
        <td class="label" title="Município do logradouro">*Município</td>
        <td class="field">
                <?=$municipio?>&nbsp;
                <?=pegaDado("nom_municipio","sw_municipio"," where cod_municipio = ".$municipio." and cod_uf = ".$estado);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo do logradouro">*Tipo Logradouro</td>
        <td class="field">
            <?=$codTipoLogradouro?>&nbsp;
            <?=pegaDado("nom_tipo","sw_tipo_logradouro"," where cod_tipo = ".$codTipoLogradouro);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Nome aplicado ao logradouro">*Logradouro</td>
        <td class="field">
            <?=$logradouro?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Número do logradouro">Número</td>
        <td class="field">
            <?=$numero;?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Complemento do endereço">Complemento</td>
        <td class="field">
            <?=$complemento;?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Bairro do logradouro">Bairro</td>
        <td class="field">
            <?=$bairro;?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Fone do logradouro">Fone</td>
        <td class="field">
            <?=$telefone;?>
        </td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Características</td>
    </tr>
    <tr>
        <td class="label" title="Tipo de localidade">*Tipo de localidade</td>
        <td class="field">
            <?=$codLocalidade?>&nbsp;
            <?=pegaDado("nom_localidade","cse.tipo_localidade"," where cod_localidade = ".$codLocalidade);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de domicílio">*Tipo de domicílio</td>
        <td class="field">
            <?=$codDomicilio;?>&nbsp;
            <?=pegaDado("nom_domicilio","cse.tipo_domicilio"," where cod_domicilio = ".$codDomicilio);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de construção">*Tipo de construção</td>
        <td class="field">
            <?=$codConstrucao;?>&nbsp;
            <?=pegaDado("nom_construcao","cse.tipo_construcao"," where cod_construcao = ".$codConstrucao);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Situação de uso do domicílio">*Situação do domicílio</td>
        <td class="field">
            <?=$codSituacao;?>&nbsp;
            <?=pegaDado("nom_situacao","cse.situacao_domicilio"," where cod_situacao = ".$codSituacao);?>
        </td>
    </tr>
        <tr>
        <td class="label" title="Quantidade de cômodos do logradouro">Quantidade de cômodos</td>
        <td class="field">
            <?=$qtdComodos;?>
        </td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Quantidades</td>
    </tr>
    <tr>
        <td class ="" colspan="2" border="0">
            <table width="100%">
                <tr>
                    <td class="label" width="30%">Residentes</td>
                    <td class="field" width="20%">
                        <?=$qtdResidentes;?>
                    </td>
                    <td class="label" width="30%">Mulheres Grávidas</td>
                    <td class="field" width="20%">
                        <?=$qtdGravidas;?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Mães Amamentando</td>
                    <td class="field">
                        <?=$qtdMaesAmamentando;?>
                    </td>
                    <td class="label">Deficientes</td>
                    <td class="field">
                        <?=$qtdDeficientes;?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Serviços</td>
    </tr>
    <tr>
        <td class="label">*Energia Elétrica</td>
        <td class="field">
<?php
if ($energia == "t") {
    echo "          Sim\n";
} else {
    echo "          Não\n";
}
?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de cobertura do domicílio">Cobertura</td>
        <td class="field">
            <?=$codCobertura;?>&nbsp;
            <?=pegaDado("nom_cobertura","cse.tipo_cobertura"," where cod_cobertura = ".$codCobertura);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo do abastecimento do domicílio">Abastecimento</td>
        <td class="field">
            <?=$codAbastecimento;?>&nbsp;
            <?=pegaDado("nom_abastecimento","cse.tipo_abastecimento"," where cod_abastecimento = ".$codAbastecimento);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de tratamento de água">Tratamento de água</td>
        <td class="field">
            <?=$codTratamentoAgua;?>&nbsp;
            <?=pegaDado("nom_tratamento","cse.tipo_tratamento_agua"," where cod_tratamento = ".$codTratamentoAgua);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de esgotamento">Esgotamento</td>
        <td class="field">
            <?=$codEsgotamento;?>&nbsp;
            <?=pegaDado("nom_esgotamento","cse.tipo_esgotamento"," where cod_esgotamento = ".$codEsgotamento);?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Forma de tratamento de lixo">Tratamento de lixo</td>
        <td class="field">
            <?=$codDestinoLixo;?>&nbsp;
            <?=pegaDado("nom_destino_lixo","cse.tipo_destino_lixo"," where cod_destino_lixo = ".$codDestinoLixo);?>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <input type="button" name="Voltar" value="Voltar" style="width: 60px" onClick="javascript: Volta();">
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 3;
        if ( $estado != strtoupper('XXX') ) {
            $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$estado." ORDER by nom_municipio";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboMunicipio = "";
            $iCont = 1;
            $js .= "f.txtMunicipio.value = '';\n";
            $js .= "limpaSelect(f.municipio,1); \n";
            while (!$dbEmp->eof()) {
                $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
                $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
                $dbEmp->vaiProximo();
                $js .= "f.municipio.options[".$iCont++."] = new Option('".$nomg_municipio."','".$codg_municipio."');\n";
            }
            if ($iCont > 1) {
                $js .= "f.txtMunicipio.disabled = false;\n";
                $js .= "f.municipio.disabled = false;\n";
                $js .= "f.txtMunicipio.focus ();\n";
            }
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js .= "limpaSelect(f.municipio,1); \n";
            $js .= "f.txtMunicipio.value = '';\n";
            $js .= "f.txtMunicipio.disabled = true;\n";
            $js .= "f.municipio.disabled = true;\n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
}
    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
