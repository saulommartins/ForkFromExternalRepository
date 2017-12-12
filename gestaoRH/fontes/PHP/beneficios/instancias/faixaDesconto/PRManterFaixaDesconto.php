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
* Página de Processamento Beneficio Faixa Desconto
* Data de Criação   : 07/07/2005

* @author Analista: Vandré Ramos
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_BEN_NEGOCIO."RBeneficioVigencia.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterFaixaDesconto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RBeneficioVigencia;

switch ($stAcao) {
    case "incluir":
        $obRegra->setDataVigencia       ( $_POST['dtDataVigencia'] );
        $obRegra->setTipo               ( $_POST['stTipo'] );
    $obRegra->setCodNorma           ( $_POST['inCodNorma'] );

        $arFaixa = array ();
        $srSessaoFaixas = Sessao::read('Faixas');
        for ($inCount=0; $inCount<count($srSessaoFaixas); $inCount++) {
            $arFaixa[$inCount]["inId"] =              $srSessaoFaixas[$inCount]['inId']    ;
            $arFaixa[$inCount]["flSalarioInicial"] =  $srSessaoFaixas[$inCount]['flSalarioInicial'];
            $arFaixa[$inCount]["flSalarioFinal"]   =  $srSessaoFaixas[$inCount]['flSalarioFinal']  ;
            $arFaixa[$inCount]["flPercentualDesc"] =  $srSessaoFaixas[$inCount]['flPercentualDesc'];
        }

        $obRegra->addBeneficioFaixaDesconto();
        $obRegra->roUltimoFaixaDesconto->setFaixa( $arFaixa );
        $obErro = $obRegra->incluirVigencia( $boTransacao );

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgForm,"Faixa de desconto: ".$obRegra->getCodVigencia(),"incluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;

    case "excluir";
        $obRegra->setCodVigencia             ( $_REQUEST['inCodVigencia'] );
        $obRegra->addBeneficioFaixaDesconto();
        $obErro = $obRegra->excluirVigencia();

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgList,"Vigência: ".$obRegra->getCodVigencia(),"excluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::alertaAviso($pgList,"Vigência: ".urlencode( $obErro->getCodVigencia() ),"n_excluir","erro", Sessao::getId(), "../");

    break;
}

?>
