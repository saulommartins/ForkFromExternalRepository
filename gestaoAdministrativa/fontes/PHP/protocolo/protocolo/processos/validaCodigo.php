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
* Arquivo de implementação de manutenção de processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.06.98
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include 'interfaceProcessos.class.php'; //Inclui classe que contém a interface html

if(!isset($controle))
        $controle = 0;

switch ($controle) {
    case 0:
        $html = new interfaceProcessos;
        $html->formValidaCodigo($PHP_SELF,$especieProcesso);
        break;
    case 1:
        $ok = false;
        $msg = "";
        //Verifica se o código fornecido (Matrícula, inscrição ou CGM) é válido
        if ($especieProcesso=='matricula') {
            $campo = "numMatricula";
            $codigo = pegaDado("v11_matric","ctmbase","Where v11_matric='".$num."' ");
                if (strlen($codigo) == 0) {
                    $msg = "Número de matrícula inválido!";
                    $ok = false;
                } else {
                    $numCgm = pegaDado("v11_numcgm","ctmbase","Where v11_matric='".$num."' ");
                    $ok = true;
                }
        } elseif ($especieProcesso=='inscricao') {
            $campo = "numInscricao";
            $codigo = pegaDado("q02_inscr","issbase","Where q02_inscr='".$num."' ");
                if (strlen($codigo) == 0) {
                    $msg = "Número de inscrição inválido!";
                    $ok = false;
                } else {
                    $numCgm = pegaDado("q02_numcgm","issbase","Where q02_inscr='".$num."' ");
                    $ok = true;
                }
        } else {
            $codigo = pegaDado("numcgmsw_cgmWhere numcgm='".$num."' ");
                if (strlen($codigo) == 0) {
                    $msg = "Número CGM inválido!";
                    $ok = false;
                } else {
                    $numCgm = $codigo;
                    $ok = true;
                }
        }

    if ($ok) {
?>
    <script type="text/javascript">
        //window.opener.parent.frames['telaPrincipal'].document.location = "incluiProcesso.php?<?=Sessao::getId();?>&controle=1&codigo=<?=$codigo;?>&numCgm=<?=$numCgm;?>&processo=<?=$especieProcesso;?>&tipoProcesso=<?=$tipoProcesso;?>";
        window.opener.parent.frames['telaPrincipal'].document.frm.<?=$campo;?>.value=<?=$num;?>;
        window.opener.parent.frames['telaPrincipal'].document.frm.numCgm.value=<?=$numCgm;?>;
        window.opener.parent.frames['telaPrincipal'].document.frm.submit();
        window.close();
    </script>
<?php } else { ?>
    <br>
    <b><?=$msg;?></b>
<?php } ?>
<br><br>
        <input type="button" value="Voltar" onClick="javascript:history.back(-1);">&nbsp;&nbsp;
        <input type="button" value="Fechar" onClick="javascript:window.close();">
<?php
    break;
}//Fim switch
?>
</html>
