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
* Arquivo de implementação de situação de processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

Casos de uso: uc-01.06.98
*/

    include '../../../framework/include/cabecalho.inc.php';
    include '../situacaoProcesso.class.php';
    $sScript = "alteraSituacaoProcesso.php?".Sessao::getId()."";
    $sBotao  = "btneditar.gif";
    if ($acao=='46') { // excluir, caso a acao do item de menu seja 46
        $sScript = "excluiSituacaoProcesso.php".Sessao::getId()."";
        $sBotao  = "btnexcluir.gif";
    }
    $altera = new situacaoProcesso;
    $aSituacao = $altera->listaSituacaoProcesso();
    echo "
    <table width=60%>
    <tr>
        <td colspan=3 class=alt_dados>Situação do Processo</td>
    </tr>";
    if (is_array($aSituacao)) {
        while (list ($key, $val) = each ($aSituacao)) {
            $onClick = "";
            $sPagAlt = "";
            $val = AddSlashes($val);
            if ($acao=='46') {
                if ($key == 1 || $key == 2 || $key == 3 || $key == 4 || $key == 5 || $key == 9) {
                    $onClick = "onClick=\"alertaAviso('Esta é uma situação padrão do sistema e não pode ser apagada!','unica','aviso', '".Sessao::getId()."');\"";
                } else {
                    $onClick = "onClick=\"alertaQuestao('../protocolo/situacaoProcesso/excluiSituacaoProcesso.php','codigo','".$key."','".$val."','sn_excluir', '".Sessao::getId()."');\"";
                }
                $sPagAlt = $sScript."&codigo=$key";
                echo "
            <tr>
                <td class=show_dados width=100%>$val</td>
                <td class=show_dados><a href='' $onClick><img
                src='../../images/$sBotao' border='0'></a></td>
            </tr>";
            } else {
                if ($key == 1 || $key == 2 || $key == 3 || $key == 4 || $key == 5 || $key == 9) {
                    $onClick = "onClick=\'alertaAviso('Esta é uma situação padrão do sistema e não pode ser alterada!','unica','aviso', 'Sessao::getId()');\'";
                    echo "
                    <tr>
                        <td class=show_dados width=100%>$val</td>
                        <td class=show_dados><a href='$sPagAlt' $onClick><img
                        src='../../images/$sBotao' border='0'></a></td>
                    </tr>";
                } else {
                    $sPagAlt = $sScript."&codigo=$key";
                    echo "
                    <tr>
                        <td class=show_dados width=100%>$val</td>
                        <td class=show_dados><a href='$sPagAlt' $onClick><img
                        src='../../images/$sBotao' border='0'></a></td>
                    </tr>";
                }
            }
        }
    }
    echo "
    </table>";
?>
<?php
    include '../../includes/rodape.php';
?>
