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

$Revision: 18557 $
$Name$
$Author: cassiano $
$Date: 2006-12-07 08:12:07 -0200 (Qui, 07 Dez 2006) $

* Casos de uso: uc-01.02.92, uc-01.02.93
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."funcoesLegado.lib.php";
include CAM_FW_LEGADO."cgmLegado.class.php"; //Insere a classe que manipula os dados do CGM
include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include 'interfaceCgm.class.php'; //Insere a classe que constroi a interface html para CGM

setAjuda('UC-01.02.92');

if (!isset($_REQUEST["controle"])) {
        $controle = 0;
} else {
    $controle = $_REQUEST["controle"];
}

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php
$html = new interfaceCgm;
switch ($controle) {
    case 0: /** Monta o formulário de busca **/
        $html->formBuscaCgm();
    break;
    case 1:
        //O CONTEUDO DO CASE 1 FOI REMOVIDO POR NÃO SER NECESSÁRIO PARA A CONSULTA DE CGM
    break;
    case 2: /** Exibe uma lista com o resultado da busca **/
        //** Monta um vetor com os dados recebidos do formulário de busca **/
        if ($_GET['volta'] == 'true' or $_GET['paginando'] == 'true') {
            //$dadosBusca = sessao->transf3;
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
        $html->exibeBusca ($objCgm->montaPesquisaCgm($dadosBusca),'consultar');
    break;
    case 3: /** Monta o formulário com os dados do CGM escolhido **/
        $html = new interfaceCgm;
        $objCgm = new cgmLegado;
        $arDadosCGM = $objCgm->pegaDadosCgm($_REQUEST["numCgm"]);
        $html->listaDadosCgm( $arDadosCGM ,$PHP_SELF,0);
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
