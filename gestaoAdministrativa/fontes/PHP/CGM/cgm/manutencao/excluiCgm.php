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
* Arquivo de instância para manutenção de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 18661 $
$Name$
$Author: cassiano $
$Date: 2006-12-11 10:14:43 -0200 (Seg, 11 Dez 2006) $

* Casos de uso: uc-01.02.92, uc-01.02.93
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."cgmLegado.class.php"); //Insere a classe que manipula os dados do CGM
include (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria
include (CAM_FW_LEGADO."paginacaoLegada.class.php");
include 'interfaceCgm.class.php'; //Insere a classe que constroi a interface html para CGM

setAjuda('UC-01.02.92');

$objCgm = new cgmLegado;
$html = new interfaceCgm;

if (isset($_REQUEST["excluir"])) {
        $controle = 1;
} else {
    if (!isset($_REQUEST["controle"])) {
        $controle = 0;
    } else {
        $controle = $_REQUEST["controle"];
    }
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($controle) {
    case 0: /** Monta o formulário de busca **/
        $html = new interfaceCgm;
        $html->formBuscaCgm('', 2);
    break;

    case 1: //O valor 1 da variável $controle está reservado para executar o método excluir
        $objCgm = new cgmLegado;
        if ($objCgm->excluiCgm($_REQUEST["excluir"])) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["excluir"]);
            $audicao->insereAuditoria();
            alertaAviso($PHP_SELF."?controle=2&pagina=".$pagina."&volta=true","CGM ".$_REQUEST["excluir"] ,"excluir","aviso");
        } else {
            $stMensagem = $objCgm->stErro;
            if ( strpos($stMensagem,"fk_") ) {
                $stMensagem = "O CGM ".$_REQUEST["excluir"]." não pode ser excluído porque está sendo utilizado";
                alertaAviso($PHP_SELF,$stMensagem,"n_excluir","erro");
            } else {
                alertaAviso($PHP_SELF,"CGM ".$_REQUEST["excluir"]." $stMensagem","n_excluir","erro");
            }
        }
    break;

    case 2: /** Exibe uma lista com o resultado da busca **/
        //** Monta um vetor com os dados recebidos do formulário de busca **/
        if ($_GET['volta'] == 'true' || $_GET['paginando'] == 'true') {
            $dadosBusca = Sessao::read('dadosBusca');
        } else {
            $dadosBusca = array(
                numCgm=>$_REQUEST["numCgm"],
                nomCgm=>str_replace('\'','\\\\\\\'\'',$_REQUEST["nomCgm"]),
                cnpj=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST["cnpj"] ),
                cpf=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST["cpf"] ),
                rg=>$_REQUEST["rg"],
                tipoBusca=>$_REQUEST["tipoBusca"] );
            Sessao::write('dadosBusca', $dadosBusca);
        }
        $objCgm = new cgmLegado;
        $html = new interfaceCgm;
        $html->exibeBusca($objCgm->montaPesquisaCgm($dadosBusca),'excluir', 'cgm', 4);
    break;

    case 3: /** Monta o formulário com os dados do CGM escolhido **/
        echo "<br><b>Verifique os dados do CGM e confirme a exclusão:</b><br>";
        $html = new interfaceCgm;
        $objCgm = new cgmLegado;
        $html->formCgmExcluir($objCgm->pegaDadosCgm($_REQUEST["numCgm"]));
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
