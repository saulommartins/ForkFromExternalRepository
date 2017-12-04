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
* Arquivo de instância para Instituição Educacional
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19055 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 08:44:42 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.87
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");

    if (!(isset($ctrl))) {
        $ctrl = 0;
        unset($sessao->transf2);
    }

    switch ($ctrl) {
        case 0:
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT
                    cod_instituicao,
                    nom_instituicao
                    FROM
                    cse.instituicao_educacional where cod_instituicao > 0 ";
        //echo $select."<br>";
        if (!(isset($sessao->transf2))) {
            $sessao->transf = "";
            $sessao->transf = $select;
            $sessao->transf2 = "nom_instituicao";
        }

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(".$sessao->transf2.")","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbConfig->abreSelecao($sSQL);
        while (!$dbConfig->eof()) {
            $inCod   = $dbConfig->pegaCampo("cod_instituicao");
            $stLista = $dbConfig->pegaCampo("nom_instituicao");
            $lista[$inCod] = $stLista;
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
<table width="100%">
    <tr>
        <td colspan="4" class="alt_dados">
            Instituições Cadastradas
        </td>
    </tr>
    <tr>
        <td class="label" width="5%">
            &nbsp;
        </td>
        <td class="labelcenter" width="12%">
            Código
        </td>
        <td class="labelcenter" width="80%">
            Instituição
        </td>
        <td class="labelleft">
            &nbsp;
        </td>
    </tr>
<?php

        $iCont = $paginacao->contador();

        if ($lista != "") {
            while (list ($cod, $val) = each ($lista)) { //mostra os tipos de processos na tela
                 $parametros = urlencode($val);

?>
    <tr>
        <td class="label">
            <?=$iCont++;?>
        </td>
        <td class="show_dados_right">
            <?=$cod;?>
        </td>
        <td class="show_dados">
            <?=$val;?>
        </td>
        <td class="botao">
            <a href='alteraInstituicao.php?<?=$sessao->id;?>&ctrl=1&codInstituicao=<?=$cod;?>&pagina=<?=$pagina;?>'>
            <img src='<?=CAM_FW_IMAGENS."btneditar.gif";?>' border='0'></a>
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
    <table width="450" align="center">
        <tr>
            <td align="center">
                <font size="2"><?=$paginacao->mostraLinks();?></font>
            </td>
        </tr>
    </table>
<?php
    break;
    case 1:
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $select =   "SELECT
                *
                FROM
                cse.instituicao_educacional
                WHERE
                cod_instituicao = ".$codInstituicao;
    $dbConfig->abreSelecao($select);
    $var = array(
                codInstituicao=>$dbConfig->pegaCampo("cod_instituicao"),
                nomInstituicao=>$dbConfig->pegaCampo("nom_instituicao")
                );
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();
?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomInstituicao.value;
        if (campo == "") {
            mensagem += "@Campo Nome da Instituição inválido";
            erro = true;
         }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id?>');
                return !(erro);
      }

      function Salvar()
      {
         if (Valida()) {
            document.frm.ctrl.value=2;
            document.frm.submit();
         }
      }

      function Cancela()
      {
        document.frm.ctrl.value=0;
        document.frm.submit();
      }
</script>
<form action="alteraInstituicao.php?<?=$sessao->id?>" method="POST" name="frm" onSubmit="return false;">
<input type="hidden" name="ctrl">
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">
            Instituições cadastradas
        </td>
    </tr>
    <input type="hidden" name="codInstituicao" value="<?=$var [codInstituicao];?>">
    <tr>
        <td class="label" width="20%" title="Nome da instituição">
            *Nome
        </td>
        <td class="field"  width="80%">
            <input type="text" name="nomInstituicao" value="<?=htmlentities($var [nomInstituicao]);?>" size="30" maxlength="160">
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <?php
                geraBotaoAltera();
            ?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
        case 2:
        $var = array(
        codInstituicao=>$codInstituicao,
        nomInstituicao=>addslashes($nomInstituicao)
        );
        $alterar = new cse;
        if (comparaValor("nom_instituicao", $var[nomInstituicao], "cse.instituicao_educacional", "and cod_instituicao <> $var[codInstituicao]",1)) {
            if ($alterar->alteraInstituicao($var)) {
                include CAM_FW_LEGADO."auditoriaLegada.class.php";
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $var[nomInstituicao]);
                $audicao->insereAuditoria();
                echo '
                    <script type="text/javascript">
                    alertaAviso("'.$var[nomInstituicao].'","alterar","aviso","'.$sessao->id.'");
                    mudaTelaPrincipal("'.$PHP_SELF.'?'.$sessao->id.'&pagina='.$pagina.'");
                    </script>';
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("'.$var[nomInstituicao].'","n_alterar","erro","'.$sessao->id.'");
                    mudaTelaPrincipal("'.$PHP_SELF.'?'.$sessao->id.'");
                    </script>';
            }
        } else {
            echo '
                <script type="text/javascript">
                alertaAviso("A Instituição '.$var[nomInstituicao].' já existe","unica","erro","'.$sessao->id.'");
                mudaTelaPrincipal("alteraInstituicao.php?'.$sessao->id.'&codInstituicao='.$var[codInsituicao].'&nomInstituicao='.$var[nomInstituicao].'&ctrl=0");
                </script>';
        }
    break;
    }
    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
