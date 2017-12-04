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
* Manutneção do unidade
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3242 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 15:59:40 -0200 (Qui, 01 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");

$controle = $_REQUEST['controle'];

if (!(isset($controle)))
$controle = 0;

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";
$stMascaraUnidade = $arMascaraSetor[0].".".$arMascaraSetor[1]."/9999";

switch ($controle) {
case 0:
?>
<script type="text/javascript">

    function validaOrganograma(iCod,campo,campoCod,stItemMsg)
    {
        //var cod = limpaZerosAEsquerda(iCod,1);
        var cod = iCod;
        var val;
        var erro = true;
        var msg = stItemMsg+" inválido ("+iCod+")";
        var f = document.frm;
        var tam = campo.options.length - 1;

        if (campoCod.value.length==0) {
            campo.options[0].selected = true;

            return false;
        }
        //Percorre todos os valores para encontrar qual item da combo tem o valor digitado
        while (tam >= 0) {
            val = campo.options[tam].value;
            if (cod==val) {
                campo.options[tam].selected = true;
                erro = false;
            }
            tam = tam - 1 ;
        }
        //Se não encontrou o valor o código digitado é inválido
        if (erro) {
            campoCod.value = "";
            campoCod.focus();
            campo.options[0].selected = true;
            alertaAviso(msg,'unica','erro','<?=Sessao::getId();?>');
        }
    }

     function Valida()
     {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

     campo = document.frm.stCodOrgao.value;
         if (campo == 0) {
            mensagem += "@Órgão";
            erro = true;
         }

     campo = document.frm.nomUnidade.value.length;
         if (campo == 0) {
            mensagem += "@nome da unidade";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');
            return !(erro);
      }

      function AlteraNumCgm()
      {
          var stFrmAnt = document.frm.action;
          document.frm.controle.value = 2;
          document.frm.submit();
          document.frm.controle.value = 1;
          document.frm.action = stFrmAnt;
      }

      function Salvar()
      {
         var f = document.frm;
         f.ok.disabled = true;
         if (Valida()) {
            f.submit();
         } else {
            f.ok.disabled = false;
         }
      }

 </script>
<form name="frm" action="<?=$PHP_SELF;?>?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="controle" value="1">
<table width="100%">
<tr><td class="alt_dados" colspan=2>Dados para unidade</td></tr>
<tr>
    <td class="label">*Órgão </td>
    <td class=field>
    <input type="text" name="stCodOrgao" value="<?=$stCodOrgao;?>" maxlength="15" size="15"
    onBlur="validaOrganograma(this.value,document.frm.stNomeOrgao,document.frm.stCodOrgao,'Órgão');"
    onKeyPress="return(isValido(this,event,'0123456789'));"
    onKeyUp="JavaScript: mascaraDinamico('<?=$stMascaraOrgao;?>', this, event);">
    <?php
        $sSQL = "SELECT cod_orgao, ano_exercicio, nom_orgao
                FROM administracao.orgao
                WHERE cod_orgao > 0
                ORDER BY lower(nom_orgao) ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $combo = "";
        $combo .= "<select name=\"stNomeOrgao\" onchange=\"preencheCampo(this,document.frm.stCodOrgao);\" style=\"width: 400px;\" >\n";
        $combo .= "<option value='XXX'>Selecione o órgão</option>\n";
        while (!$dbEmp->eof()) {
            $codOrgaof = trim($dbEmp->pegaCampo("cod_orgao"));
            $stExercicioOrgao = $dbEmp->pegaCampo("ano_exercicio");
            $nomOrgao = trim($dbEmp->pegaCampo("nom_orgao"));
            $dbEmp->vaiProximo();
            $stCodOrgao = $codOrgaof."/".$stExercicioOrgao;
            $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgao);
            if ($arCodOrgao[0]) {
                $stCodOrgao = $arCodOrgao[1];
            }
            $selected = "";
            if ($stNomeOrgao == ($stCodOrgao)) {
                $selected = "selected";
            }
            $combo .= "<option ".$selected." value=\"".$stCodOrgao."\">".$nomOrgao." - ".$stExercicioOrgao."</option>\n";
    }
        $combo .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $combo;
?>
    </td>
</tr>

<tr>
    <td class="label">*Nome da Unidade</td>
    <td class=field>
        <input type="text" size="60" maxlength="60" name="nomUnidade" value="<?=$nomUnidade;?>">
    </td>
</tr>
<tr>
    <td class=label  title="Usuário responsável pela unidade">Responsável</td>
    <td class=field>
        <?php geraCampoInteiro("usuarioResponsavel",10,10,$responsavel,false,'onChange="AlteraNumCgm();"'); ?>
        <input type="text" name="usuarioResponsavelN" size=40 maxlength=50 readonly="" value="<?=$usuarioResponsavelN?>">
        <a href="#" onClick="procurarCgm('frm','usuarioResponsavel','usuarioResponsavelN','usuario','<?=Sessao::getId();?>');">
        <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="" border=0></a>
    </td>
</tr>
<tr>
    <td class=field colspan=2>
        <?php geraBotaoOk(); ?>
    </td>
</tr>
</table>
</form>
<?php
break;

case 1:

    $stCodOrgao = $_REQUEST['stCodOrgao'];
    $nomUnidade = $_REQUEST['nomUnidade'];
    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

    $arOrgao = explode("/",$stCodOrgao);
    $inCodOrgao = (int) $arOrgao[0];
    $stExercicioOrgao = $arOrgao[1];

    //Pega um novo código para a unidade que será criada
    $codUnidade = pegaID("cod_unidade", "administracao.unidade", "where cod_orgao=".$inCodOrgao ." And ano_exercicio = '".$stExercicioOrgao."'");
    $configuracao = new configuracaoLegado;
    $configuracao->setaValorUnidade($codUnidade,$inCodOrgao,$nomUnidade,$stExercicioOrgao,$usuarioResponsavel);
    if (comparaValor("nom_unidade", $nomUnidade, "administracao.unidade", " and cod_orgao = ".$inCodOrgao." and ano_exercicio = '".$stExercicioOrgao."' ",1)) {
        if ($configuracao->insertUnidade()) {
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomUnidade);
                $audicao->insereAuditoria();
                $stCodUnidade = $inCodOrgao.".".$codUnidade."/".$stExercicioOrgao;
                $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidade);
                if ($arCodUnidade[0]) {
                    $stCodUnidade = $arCodUnidade[1];
                }
                echo '<script type="text/javascript">
                    alertaAviso("'.$stCodUnidade." - ".$nomUnidade.'","incluir","aviso","'.Sessao::getId().'");
                    mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&stCodOrgao='.$stCodOrgao.'&stNomeOrgao='.$stNomeOrgao.'");
                    </script>';
        } else {
                echo '<script type="text/javascript">
                    alertaAviso("'.$nomUnidade.'","n_incluir","erro","'.Sessao::getId().'");
                    </script>';
                    executaFrameOculto("f.ok.disabled = false;");
        }
    } else {
        echo '
            <script type="text/javascript">
            alertaAviso("A Unidade '.$nomUnidade.' já existe","unica","erro","'.Sessao::getId().'");
            </script>';
            executaFrameOculto("f.ok.disabled = false;");
    }

break;

case 2:

    $usuarioNumCgm = $_REQUEST['usuarioResponsavel'];

    $select  = "SELECT nom_cgm FROM sw_cgm, administracao.usuario
                 WHERE sw_cgm.numcgm = ".$usuarioNumCgm."
                   AND sw_cgm.numcgm = usuario.numcgm";
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $usuarioResponsavel = $dbConfig->pegaCampo("nom_cgm");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();

    if ($usuarioResponsavel == "") {
        echo '<script type="text/javascript">
                    alertaAviso("Código de Usuário invalido ('.$usuarioNumCgm.')","unica","erro","'.Sessao::getId().'");
              </script>';
    }

    $js .= "f.usuarioResponsavelN.value = \"".$usuarioResponsavel."\"\n";
    executaFrameOculto($js);
break;

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
