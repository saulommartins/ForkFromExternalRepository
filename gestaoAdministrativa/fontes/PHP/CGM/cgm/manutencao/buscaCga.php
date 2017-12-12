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

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

* Casos de uso: uc-01.02.92, uc-01.02.93
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."cgmLegado.class.php"; //Insere a classe que manipula os dados do CGM
include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include 'interfaceCgm.class.php'; //Insere a classe que constroi a interface html para CGM

$controle = $_REQUEST['controle'];

if(!isset($controle))
        $controle = 0;

switch ($controle) {
    case 0: /** Monta o formulário de busca **/
        $html = new interfaceCgm;
        $html->formBuscaCgm("", 0, "CGA");
    break;

    case 2: /** Exibe uma lista com o resultado da busca **/
        $dadosBusca = Sessao::read('dadosBusca');
        $objCgm = new cgmLegado;
        $html = new interfaceCgm;
        $html->exibeBusca($objCgm->montaPesquisaCga($dadosBusca),'consultar', "cga", $controle);
    break;

    case 3: /** Monta o formulário com os dados do CGA escolhido **/
        $numCgm = $_REQUEST['numCgm'];
        $timestamp = $_REQUEST['timestamp'];

        $html = new interfaceCgm;
        $objCgm = new cgmLegado;
        $html->formCga($objCgm->pegaDadosCga($numCgm,$timestamp));
        break;

    case 4: /** Exibe uma lista com o resultado da busca **/
        $dadosBusca = Sessao::read('dadosBusca');
        $objCgm = new cgmLegado;
        $html = new interfaceCgm;
        $html->exibeBusca($objCgm->montaPesquisaCga($dadosBusca),'excluir', "cga", $controle);
    break;

    case 5: /** Exibe uma lista com o resultado da busca **/
        $dadosBusca = Sessao::read('dadosBusca');
        $objCgm = new cgmLegado;
        $html = new interfaceCgm;
        $html->exibeBusca($objCgm->montaPesquisaCga($dadosBusca),'alterar', "cga", $controle);
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
