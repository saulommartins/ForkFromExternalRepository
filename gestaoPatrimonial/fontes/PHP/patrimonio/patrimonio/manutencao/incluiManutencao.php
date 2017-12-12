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
    * Arquivo que faz a inclusão
    * Data de Criação   : 28/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 18798 $
    $Name$
    $Autor: $
    $Date: 2006-12-15 11:06:24 -0200 (Sex, 15 Dez 2006) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.35  2006/12/15 13:06:24  rodrigo
7716

Revision 1.34  2006/12/01 15:55:17  hboaventura
bug #7716#

Revision 1.33  2006/07/27 13:53:36  fernando
Bug #6660#

Revision 1.32  2006/07/27 13:21:29  fernando
Bug #6417#

Revision 1.31  2006/07/27 12:58:59  fernando
Bug #6660#

Revision 1.30  2006/07/21 11:36:02  fernando
Inclusão do  Ajuda.

Revision 1.29  2006/07/13 19:45:57  fernando
Alteração de hints

Revision 1.28  2006/07/06 13:17:40  fernando
Arrumando título do campo exercício do empenho

Revision 1.27  2006/07/06 13:16:05  fernando
Arrumando título do campo exercício do empenho

Revision 1.26  2006/07/06 12:11:28  diego
Adicionada tag de log: $Log$
Adicionada tag de log: Revision 1.35  2006/12/15 13:06:24  rodrigo
Adicionada tag de log: 7716
Adicionada tag de log:
Adicionada tag de log: Revision 1.34  2006/12/01 15:55:17  hboaventura
Adicionada tag de log: bug #7716#
Adicionada tag de log:
Adicionada tag de log: Revision 1.33  2006/07/27 13:53:36  fernando
Adicionada tag de log: Bug #6660#
Adicionada tag de log:
Adicionada tag de log: Revision 1.32  2006/07/27 13:21:29  fernando
Adicionada tag de log: Bug #6417#
Adicionada tag de log:
Adicionada tag de log: Revision 1.31  2006/07/27 12:58:59  fernando
Adicionada tag de log: Bug #6660#
Adicionada tag de log:
Adicionada tag de log: Revision 1.30  2006/07/21 11:36:02  fernando
Adicionada tag de log: Inclusão do  Ajuda.
Adicionada tag de log:
Adicionada tag de log: Revision 1.29  2006/07/13 19:45:57  fernando
Adicionada tag de log: Alteração de hints
Adicionada tag de log:
Adicionada tag de log: Revision 1.28  2006/07/06 13:17:40  fernando
Adicionada tag de log: Arrumando título do campo exercício do empenho
Adicionada tag de log:
Adicionada tag de log: Revision 1.27  2006/07/06 13:16:05  fernando
Adicionada tag de log: Arrumando título do campo exercício do empenho
Adicionada tag de log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../bens.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.07");

$inclui = new bens;

if (!(isset($ctrl)))
    $ctrl=0;

switch ($ctrl) {

    // pesquisa e exibicao de BENS
    case 0:
        include_once '../bens/listarBens.php';
    break;

    case 1:
        $incluiLista = $inclui->listaAgendamento($codbem);
?>
        <table width="100%">
        <tr>
            <td colspan="3" class="alt_dados">Manutenção de Bens</td>
        </tr>
        <tr>
            <td class="label" width="5%">&nbsp;</td>
            <td class="labelcenter" >Data do Agendamento</td>
            <td class="label" width="5%">&nbsp;</td>
        </tr>
<?php
        if ($incluiLista != "") {

            $cont = 1;

            while (list ($key, $val) = each ($incluiLista)) {
                $dt = dataToBr($val);
?>
                <tr>
                    <td class="labelcenter" width="5%"><?=$cont++;?></td>
                    <td class="show_dados" width="100%"><?=$dt;?></td>
                    <td class="botao">
                        <a href='incluiManutencao.php?<?=Sessao::getId();?>&cod=<?=$codbem;?>&data=<?=$val;?>&ctrl=2'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title="Alterar" border='0'>
                        </a>
                    </td>
                </tr>
<?php
            }
        } else {
?>
                <tr>
                    <td class="show_dados_center" width="100%" colspan="3">Nenhuma manutenção agendada para este bem.</td>
                </tr>
<?php
        }
        echo "</table>";

    break;

    case 2:

        $inclui->mostraManutencaoAgendada($cod, $data);
         $exercicio = Sessao::getExercicio();
        $arEntidade = $inclui->listaEntidades($exercicio);
        $inclui->codigo = $cod;
        $inclui->dtAgendamento = $data;

        $ArrData = explode("-", $inclui->dtAgendamento);
        $inclui->dtAgendamento = $ArrData[2] . "/" . $ArrData[1] . "/" . $ArrData[0];
?>
        <script type="text/javascript">
            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;

                campo = document.frm.num_cgm.value.length;
                if (campo==0) {
                    mensagem += "@O campo Número do CGM é obrigatório.";
                    erro = true;
                }

               /* campo = document.frm.exercicioEmpenho.value;
                if (campo=="") {
                  mensagem += "@O campo Exercício do empenho é obrigatório.";
                  erro = true;
                }*/

                campo = document.frm.ent.value;
                if (campo=="") {
                  mensagem += "@O campo Entidade é obrigatório.";
                  erro = true;
                }

                /*campo = document.frm.emp.value.length;
                if (campo==0) {
                   mensagem += "@O campo Número do Empenho é obrigatório.";
                   erro = true;
                }*/
                /*else {
                   campo = document.frm.emp.value;
                   if (isNaN(campo)) {
                       mensagem += "@O campo Número do Empenho só aceita Números.";
                       erro = true;
                   }

                }*/

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
                    document.frm.ctrl.value = '3';
                    document.frm.target     = 'oculto';
                    document.frm.submit();
                }
            }

            function buscaCGM()
            {
                var f = document.frm;
                f.ctrl.value = "4";
                f.target = 'oculto';
                f.submit();
            }
            function buscaEmpenho()
            {
                var f = document.frm;
                f.ctrl.value = "5";
                f.target = 'oculto';
                f.submit();
           }
           function Limpar()
           {
               document.frm.reset();
               document.getElementById("nom_cgm").innerHTML = "&nbsp;";

           }
        </script>

        <form action="incluiManutencao.php?<?=Sessao::getId()?>&ctrl=3" method="POST" name="frm">
            <input type="hidden" name="codkey" value="<?=$inclui->codigo;?>" readonly="">
            <input type="hidden" name="ctrl" value="<?=$ctrl?>">

        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="2">Entre com os Dados da Manutenção</td>
        </tr>

        <tr>
            <td class="label" width="20%">Código do Bem</td>
            <td class="field"><?=$inclui->codigo;?></td>
        </tr>

        <tr>
            <td class="label" width="20%">Descrição</td>
            <td class="field"><?=$inclui->descricao;?></td>
        </tr>

        <tr>
            <td class="label">Data de Agendamento</td>
            <td class="field"><?=$inclui->dtAgendamento;?>
                <input type="hidden" name="dataAgenda" value="<?=$inclui->dtAgendamento;?>" readonly="" size="10">
            </td>
        </tr>
<?php
  geraCampoData2("Data de Realização", "dataRealiza", hoje(), false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data de realização da manutenção",'Buscar data de realização' );
  geraCampoData2("Data de Garantia"  , "dataGarantia", $dataGarantia, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data de garantia","Buscar data de garantia");
?>
        <tr>
            <td class="label" title="Informe as observações da manutenção.">Observações</td>
            <td class="field">
                <textarea name="obs" rows="5" cols="50"><?=$inclui->observacao;?></textarea>
            </td>
        </tr>
        <tr>
            <td class="label"  title="Informe o cgm.">*CGM</td>
            <td class="field">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                <td align="left" width="11%" valign="top">
                    <input type='text' id='num_cgm' name='num_cgm' value='<?=$num_cgm;?>' size='10' maxlength='10' onChange="buscaCGM();" onKeyPress="return(isValido(this, event, '0123456789'))">
                    <input type="hidden" name="nom_cgm" value="<?=$nom_cgm;?>">
                </td>
                <td width="1">&nbsp;</td>
                <td align="left" width="60%" id="nom_cgm" class="fakefield" valign="middle">&nbsp;</td>
                <td align="left" valign="top">
                    &nbsp;
                    <a href="javascript:abrePopUp('../../../../../../gestaoAdministrativa/fontes/PHP/CGM/popups/cgm/FLProcurarCgm.php','frm','num_cgm','nom_cgm','todos','<?=Sessao::getId();?>','800','550');">
                    <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar CGM." border="0" align="absmiddle"></a>
                </td>
                </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label" title="Selecione a entidade.">*Entidade</td>
            <td class="field">
                <select name="ent">
                   <option value='' SELECTED>Selecione</option>
<?php
                     $comboEntidade = "";
                     foreach ($arEntidade as $entidade) {
                        $comboEntidade .= '<option value='. $entidade['cod_entidade'] . ">";
                        $comboEntidade .= $entidade['nom_entidade'] . "</option>";
                     }
                     echo($comboEntidade);

?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o exercício do empenho.">Exercício do Empenho</td>
            <td class="field">
                <input type="text" name="exercicioEmpenho" value='<?=Sessao::getExercicio();?>' size='5' maxlength='4' onKeyUp="return autoTab(this, 4, event);" onKeyPress="return(isValido(this, event, '0123456789'));">
            </td>
        </tr>

        <tr>
            <td class="label" title="Informe o número do empenho.">Número do Empenho</td>
            <td class="field">
<!--            <input type="text" id="emp" name="emp" value="" size='10' maxlength='9' onChange="" onKeyUp="return autoTab(this, 9, event);" onKeyPress="return(isValido(this, event, '0123456789'));"
onBlur="buscaEmpenho();">-->
                <input type="text" id="emp" name="emp" value="" size='10' maxlength='9' onChange="" onKeyUp="return autoTab(this, 9, event);" onKeyPress="return(isValido(this, event, '0123456789'));">
                <input type="hidden" id="empdesc" name="empdesc" value="">
                &nbsp;
                <a href="javascript:abrePopUp('../../../../../../gestaoFinanceira/fontes/PHP/empenho/popups/empenho/FLEmpenho.php','frm','emp', 'empdesc', 'buscaEmpenho&inCodigoEntidade='+document.frm.ent.value+'&stExercicioEmpenho='+document.frm.exercicioEmpenho.value,'<?=Sessao::getId();?>','800','550');">
                <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar Empenho" border="0" align="absmiddle"></a>
<!--
                <a href="javascript:abrePopUp('../../../../../../gestaoAdministrativa/fontes/PHP/framework/popupsLegado/empenho/FLEmpenho.php','frm','emp', 'empdesc', 'buscaEmpenho&inCodigoEntidade='+document.frm.ent.value+'&stExercicioEmpenho='+document.frm.exercicioEmpenho.value,'<?=Sessao::getId();?>','800','550');">
                <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar Empenho" border="0" align="absmiddle"></a>
-->
        </tr>
        <tr>
         <td class="label"  title="Informe o valor.">Valor</td>
            <td class="field">
                <input type="text" name="val" value="<?=$val;?>" size='10' maxlength='9'
                    onKeyPress="return validaCharMoeda( this, event );"
                    onBlur="return formataMoeda(this, '2', event);"
                    onKeyUp="return mascaraMoeda(this, '2', event);"
                >
            </td>
        </tr>
        <tr>
            <td class="field" colspan=2>
            <?php geraBotaoOk2(); ?>
            </td>
        </tr>

        </table>

        </form>
<?php
    break;

    case 3:
        $val = str_replace( ".",  "", $val );
        $val = str_replace( ",", ".", $val );
        $inclui->setaVariaveis($codkey, $dataAgenda, $num_cgm, $dataRealiza, $dataGarantia, $obs, (int) $emp, $nota, $val, $exercicioEmpenho, "",$ent);
        $erro = $inclui->incluiManutencao();
        if (!$erro) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $cod); //registra os passos no auditoria
            $audicao->insereAuditoria();

?>
            <script type="text/javascript">
                alertaAviso      ( "Bem: <?=$codkey;?>", "incluir", "aviso", "<?=Sessao::getId()?>"          );
                mudaTelaPrincipal( "incluiManutencao.php?<?=Sessao::getId();?>&ctrl=1&codbem=<?=$codkey;?>"  );
            </script>
<?php

        } else {
?>
            <script type="text/javascript">
                alertaAviso( "<?=$erro;?>", "n_incluir", "erro", "<?=Sessao::getId()?>" );
            </script>
<?php
        }
    break;

    case 4:
        // busca nome do fornecedor atraves do cod_fornecedor informado
        $nom_cgm = "";
        if ($num_cgm != "") {
            $sql = "SELECT
                        c.numcgm, c.nom_cgm
                    FROM
                        sw_cgm                 as c

                    WHERE
                        c.numcgm     > 0
                        AND c.numcgm = ".$num_cgm;
            $conn = new dataBaseLegado;
            $conn->abreBD();
            $conn->abreSelecao($sql);
            $conn->vaiPrimeiro();

            $nom_cgm  = trim( $conn->pegaCampo("nom_cgm") );

            $conn->limpaSelecao();
            $conn->fechaBD();
        }

        if ( strlen($nom_cgm) > 0 ) {
            $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$nom_cgm.'";';
            $js .= 'f.nom_cgm.value = "'.$nom_cgm.'";';
        } else {
            $js .= 'f.num_cgm.value = "" ;';
            $js .= 'd.getElementById("nom_cgm").innerHTML = "&nbsp;";';
            $js .= "erro = true;\n";
            $js .= 'mensagem += "Número do CGM inválido! ('.$num_cgm.').";';
            $js .= 'f.num_cgm.focus()';
        }
        executaFrameOculto($js);
    break;
      case 5:

        if ( strlen($ent) <= 0 ) {
//            $js .= 'f.emp.value = "" ;';
//            $js .= "erro = true;\n";
//            $js .= 'mensagem += "Selecione uma entidade válida!";';
//            $js .= 'f.emp.focus()';
        } else {
            include_once( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );
            $obRegra = new REmpenhoOrdemPagamento( $obRempenho );
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['exercicioEmpenho'] );
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $ent );
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoInicial( $emp );
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoFinal( $emp );
            $obRegra->obREmpenhoNotaLiquidacao->listarNotasDisponiveis( $rsLista );

            while (!$rsLista->eof()) {
                $inCodEmpenho = $rsLista->getCampo('cod_empenho');
                $rsLista->proximo();
            }
            if ( strlen($inCodEmpenho) <= 0 ) {
                $js .= "erro = true;\n";
                $js .= 'mensagem += "Número de Empenho inválido! ( Empenho: '.$emp.' ).";';
                $js .= 'f.emp.value = "" ;';
                $js .= 'f.emp.focus()';
            }
        }
        executaFrameOculto($js);
    break;

}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
