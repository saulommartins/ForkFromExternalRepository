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
    * Arquivo que seleciona o método de consulta
    * Data de Criação   : 28/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 22596 $
    $Name$
    $Autor: $
    $Date: 2007-05-15 17:05:21 -0300 (Ter, 15 Mai 2007) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.26  2007/05/15 20:05:21  leandro.zis
Bug #8586#

Revision 1.25  2007/03/06 17:48:55  tonismar
bug #8585 #8586

Revision 1.24  2006/07/27 12:58:59  fernando
Bug #6660#

Revision 1.23  2006/07/21 11:36:02  fernando
Inclusão do  Ajuda.

Revision 1.22  2006/07/13 19:38:23  fernando
correção de aspas simples e duplas

Revision 1.21  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.20  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../bens.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.07");
$agenda = new bens;

if (!(isset($ctrl)))
    $ctrl = 0;
switch ($ctrl) {

    // pesquisa e exibicao de BENS
    case 0:
        include_once '../bens/listarBens.php';
    break;

    // lista agendamentos de manutencao do BEM selecionado
    case 1:
        $agenda->codigo = $codbem;
        $descricao = $agenda->selecionaBem();
        $agendaLista = $agenda->listaAgendamento($codbem);
        $numPlaca = str_replace('\\\'','&#039;',$numPlaca);
        $numPlaca = str_replace('\\"','&#034;',$numPlaca);
?>
        <table width="100%">
        <tr>
            <td colspan="3" class="alt_dados">Manutenção do Bem</td>
        </tr>
        <tr>
        <tr>
            <td class="label">Classificação</td>
            <td class="field"><?=$classificacao;?></td>
        </tr>

        <tr>
            <td class="label" width="20%">Código do Bem</td>
            <td class="field"><?=$codbem;?></td>
        </tr>

        <tr>
            <td class="label">Placa de Identificação</td>
            <td class="field"><?=$numPlaca;?></td>
        </tr>
        <tr>
            <td class="label" width="20%">Descrição</td>
            <td class="field"><?=$descricao;?></td>
        </tr>
        </tr>
        </table>
        <table width="100%">
        <tr>
            <td colspan="3" class="alt_dados">Agendamentos</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%">&nbsp;</td>
            <td class="labelcenter" width="10%">Data do Agendamento</td>
            <td class="labelcenter" width="5%">&nbsp;</td>
        </tr>
<?php
        if ($agendaLista != "") {
            $cont = 1;
            while (list ($key, $val) = each ($agendaLista)) {
                $dt = dataToBr($val);
?>
                <tr>
                    <td class="labelcenter" width="5%"><?=$cont++;?></td>
                    <td class="show_dados" width="100%"><?=$dt;?></td>
                    <td class="botao">
                        <a
href='alteraAgendamento.php?<?=Sessao::getId();?>&codbem=<?=$codbem;?>&empenho=<?=$empenho;?>&empenhoExercicio=<?=$empenhoExercicio;?>&data=<?=$val;?>&dataAntiga=<?=$val;?>&numPlaca=<?=$numPlaca;?>&nomNatureza=<?=$nomNatureza;?>&nomGrupo=<?=$nomGrupo;?>&nomEspecie=<?=$nomEspecie;?>&ctrl=2'>
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

    // formulario de ALTERACAO DE AGENDAMENTO DE MANUTENCAO
    case 2:

        ?>
            <script type="text/javascript">
            function Cancela()
            {
                mudaTelaPrincipal("alteraAgendamento.php?<?=Sessao::getId()?>&ctrl=0&ctrl_frm=2");
                //history.go(-1);
            }
            </script>
        <?php

        $agenda->mostraManutencaoAgendada( $codbem, $data );

        $dataAgenda   = str_replace( "-", "/", $agenda->dtAgendamento );
        $arDataAgenda = explode    ( "/"     , $dataAgenda );
        $dataAgenda   = $arDataAgenda[2]."/".$arDataAgenda[1]."/".$arDataAgenda[0];
        $numPlaca = str_replace('\\\'','&#039;',$numPlaca);
        $numPlaca = str_replace('\\"','&#034;',$numPlaca);
?>
        <script type="text/javascript">
            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;

                campo = document.frm.num_cgm.value.length;
                if (campo==0) {
                    mensagem += "@Campo CGM inválido!( ).";
                    erro = true;
                }

                campo2 = document.frm.dataAgenda.value.length;
                if (campo2==0) {
                    mensagem += "@Campo Data Agendamento inválido!( ).";
                    erro = true;
                }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
                    document.frm.ctrl.value = '3';
                    document.frm.target = 'oculto';
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
        </script>

        <form action="alteraAgendamento.php?<?=Sessao::getId()?>&ctrl=2" method="POST" name="frm" aciton="oculto">
            <input type="hidden" name="codbem" value="<?=$agenda->codigo;?>">
            <input type="hidden" name="dataAntiga" value="<?=$agenda->dtAgendamento;?>">
            <input type="hidden" name="dataRealiza" value="" readonly="">
            <input type="hidden" name="dataGarantia" value = "">
            <input type="hidden" name="empenho" value="<?=$agenda->codEmpenho;?>">
            <input type="hidden" name="empenhoExercicio" value="<?=$agenda->exercicioEmpenho;?>">
            <input type="hidden" name="numPlaca" value="<?=$numPlaca;?>">
            <input type="hidden" name="descricao" value="<?=$descricao;?>">
            <input type="hidden" name="nomNatureza" value="<?=$nomNatureza;?>">

            <input type="hidden" name="val" value="0">
            <input type="hidden" name="ctrl">
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="2">Informe os dados do Agendamento</td>
        </tr>

        <tr>
            <td class="label">Natureza</td>
            <td class="field"><?=$nomNatureza;?></td>
        </tr>

        <tr>
            <td class="label">Grupo</td>
            <td class="field"><?=$nomGrupo;?></td>
        </tr>

        <tr>
            <td class="label">Espécie</td>
            <td class="field"><?=$nomEspecie;?></td>
        </tr>
        <tr>
            <td class="label" width="20%">Código do Bem</td>
            <td class="field"><?=$agenda->codigo;?></td>
        </tr>

        <tr>
            <td class="label">Placa de Identificação</td>
            <td class="field"><?=$numPlaca;?></td>
        </tr>
        <tr>
            <td class="label" width="20%">Descrição</td>
            <td class="field"><?=$agenda->descricao;?></td>
        </tr>
<?php
  geraCampoData2("*Data Agendamento", "dataAgenda", $dataAgenda, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()'); this.value = '';};\"","informe a data do agendamento",'Buscar data do agendamento' );
?>
        <tr>
            <td class="label"  title="Informe o número cgm.">*CGM</td>
            <td class="field">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                <td align="left" width="11%" valign="top">
                    <input type='text' id='num_cgm' name='num_cgm' value='<?=$agenda->numCgm;?>' size='10' maxlength='10' onChange="buscaCGM();" onKeyPress="return(isValido(this, event, '0123456789'))">
                    <input type="hidden" name="nom_cgm" value="<?=$agenda->nomCgm;?>">
                </td>
                <td width="1">&nbsp;</td>
                <td align="left" width="60%" id="nom_cgm" class="fakefield" valign="middle">&nbsp;</td>
                <td align="left" valign="top">
                    &nbsp;
                    <a href="javascript:abrePopUp('../../../../../../gestaoAdministrativa/fontes/PHP/CGM/popups/cgm/FLProcurarCgm.php','frm','num_cgm','nom_cgm','juridica','<?=Sessao::getId();?>','800','550');">
                    <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar cgm" border="0" align="absmiddle"></a>
                </td>
                </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe as observações da manutenção.">Observações</td>
            <td class="field"><textarea name="obs" rows="5" cols="50"><?=$agenda->observacao;?></textarea></td>
        <tr>
            <td class="field" colspan=2>
            <?php geraBotaoAltera(); ?>
            </td>
        </tr>
        </table>

        </form>
<?php
        $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$agenda->nomCgm.'";';
        executaFrameOculto( $js );
    break;

    // executa ALTERACAO DE AGENDAMENTO DE MANUTENCAO no BD
    case 3:
        $agenda->setaVariaveis( $codbem, $dataAgenda, $num_cgm, "", "", $obs, $empenho, "", "", $empenhoExercicio );
        $ArrData1              = explode( "/", $dataAgenda );
        $agenda->dtAgendamento = $ArrData1[2]."-".$ArrData1[1]."-".$ArrData1[0];
        if ($agenda->dtAgendamento != $dataAntiga) {
            $boComparaData = $agenda->comparaData();
        } else {
            $boComparaData = true;
        }
        if ($boComparaData == true) {
            if ( $agenda->alteraAgendamento( $dataAntiga ) ) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $cod); //registra os passos no auditoria
                $audicao->insereAuditoria();
  alertaAviso( $PHP_SELF, "Bem: $codbem", "alterar", "aviso", Sessao::getId() );
            } else {
                exibeAviso("bem $codbem","n_alterar","erro");
            }
        }
    break;

    case 4:
        // busca nome do fornecedor atraves do cod_fornecedor informado
        $nom_cgm = "";
        if ($num_cgm != "") {
            $sql = "SELECT
                        c.numcgm, c.nom_cgm
                    FROM
                        sw_cgm                 as c,
                        sw_cgm_pessoa_juridica as cpj
                    WHERE
                        c.numcgm     > 0
                        AND c.numcgm = ".$num_cgm."
                        AND c.numcgm = cpj.numcgm";
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
            $js .= 'mensagem += "Número do CGM inválido! (Código: '.$num_cgm.').";';
            $js .= 'f.num_cgm.focus()';
        }
        executaFrameOculto($js);
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
