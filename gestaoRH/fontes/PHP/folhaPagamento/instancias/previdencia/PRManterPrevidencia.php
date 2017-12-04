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
* Página de Processamento de FolhaPagamentoPrevidencia
* Data de Criação   : 29/11/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30840 $
$Name$
$Author: souzadl $
$Date: 2007-06-22 15:03:26 -0300 (Sex, 22 Jun 2007) $

* Casos de uso: uc-04.05.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterPrevidencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRFolhaPagamentoPrevidencia = new RFolhaPagamentoPrevidencia;

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

switch ($stAcao) {

    case "incluir":
        $obRFolhaPagamentoPrevidencia->setDescricao              ( $_POST['stDescricao'] );
        $obRFolhaPagamentoPrevidencia->setAliquota               ( $_POST['flAliquota'] );
        $obRFolhaPagamentoPrevidencia->setTipo                   ( $_POST['stTipo'] );
        $obRFolhaPagamentoPrevidencia->setVinculo                ( $_POST['inVinculo'] );
        $obRFolhaPagamentoPrevidencia->setVigencia               ( $_POST['dtVigencia'] );
        $obRFolhaPagamentoPrevidencia->setAliquotaRat            ( $_POST['flAliquotaRat'] );
        $obRFolhaPagamentoPrevidencia->setAliquotaFap            ( $_POST['flAliquotaFap'] );
        $obRFolhaPagamentoPrevidencia->setCodRegimePrevidencia   ( $_POST['inCodRegimePrevidencia'] );
        foreach ($_POST as $stName=>$stValue) {
            if ( strpos($stName,'inCodigoPrev') === 0 ) {
                $inCodTipo = substr($stName,12,strlen($stName));
                $obRFolhaPagamentoPrevidencia->addRFolhaPagamentoEvento();
                $obRFolhaPagamentoPrevidencia->roRFolhaPagamentoEvento->setCodigo($stValue);
                $obRFolhaPagamentoPrevidencia->roRFolhaPagamentoEvento->setCodTipo($inCodTipo);
            }
        }
        $arFaixa = array ();
        $arFaixas = Sessao::read("Faixas");
        for ($inCount=0; $inCount<count($arFaixas); $inCount++) {
            $arFaixa[$inCount]["inId"] =              $arFaixas[$inCount]['inId']    ;
            $arFaixa[$inCount]["flSalarioInicial"] =  $arFaixas[$inCount]['flSalarioInicial'];
            $arFaixa[$inCount]["flSalarioFinal"]   =  $arFaixas[$inCount]['flSalarioFinal']  ;
            $arFaixa[$inCount]["flPercentualDesc"] =  $arFaixas[$inCount]['flPercentualDesc'];
        }
        $obRFolhaPagamentoPrevidencia->addFaixa ($arFaixa);
        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obErro = $obRFolhaPagamentoPrevidencia->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRFolhaPagamentoPrevidencia->salvarPrevidencia();
        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm,"Previdência: ".$_POST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;

    case "alterar":
        $obErro = new erro;
        if ( SistemaLegado::comparaDatas($_POST['dtVigenciaAntiga'],$_POST['dtVigencia']) ) {
            $obErro->setDescricao("A vigência informada é menor que a vigência anterior.");
        }
        if ( !$obErro->ocorreu() ) {

            $obRFolhaPagamentoPrevidencia->setDescricao              ( $_POST['stDescricao'] );
            $obRFolhaPagamentoPrevidencia->setAliquota               ( $_POST['flAliquota']  );

            $obRFolhaPagamentoPrevidencia->setTipo                   ( $_POST['stTipo'] );
            $obRFolhaPagamentoPrevidencia->setCodPrevidencia         ( $_POST['inCodPrevidencia'] );

            $obRFolhaPagamentoPrevidencia->setVigencia               ( $_POST['dtVigencia'] );
            $obRFolhaPagamentoPrevidencia->setAliquotaRat            ( $_POST['flAliquotaRat'] );
            $obRFolhaPagamentoPrevidencia->setAliquotaFap            ( $_POST['flAliquotaFap'] );
            $obRFolhaPagamentoPrevidencia->setCodRegimePrevidencia   ( Sessao::read('inCodRegimePrevidencia') );

            foreach ($_POST as $stName=>$stValue) {
                if ( strpos($stName,'inCodigoPrev') === 0 ) {
                    $inCodTipo = substr($stName,12,strlen($stName));
                    $obRFolhaPagamentoPrevidencia->addRFolhaPagamentoEvento();
                    $obRFolhaPagamentoPrevidencia->roRFolhaPagamentoEvento->setCodigo($stValue);
                    $obRFolhaPagamentoPrevidencia->roRFolhaPagamentoEvento->setCodTipo($inCodTipo);
                }
            }
            $arFaixa = array();
            $arFaixas = Sessao::read("Faixas");
            for ($inCount=0; $inCount<count($arFaixas); $inCount++) {
                $arFaixa[$inCount]["inId"] =              $arFaixas[$inCount]['inId']    ;
                $arFaixa[$inCount]["flSalarioInicial"] =  $arFaixas[$inCount]['flSalarioInicial'];
                $arFaixa[$inCount]["flSalarioFinal"]   =  $arFaixas[$inCount]['flSalarioFinal']  ;
                $arFaixa[$inCount]["flPercentualDesc"] =  $arFaixas[$inCount]['flPercentualDesc'];
            }

            $obErro = $obRFolhaPagamentoPrevidencia->addFaixa ($arFaixa);
            //monta array de atributos dinamicos
            foreach ($arChave as $key => $value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode( "," , $value );
                }
                $obErro = $obRFolhaPagamentoPrevidencia->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }

            $obErro = $obRFolhaPagamentoPrevidencia->salvarPrevidencia();
        }
        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList,"Previdência: ".$_POST['stDescricao'],"alterar","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;

    case "excluir";
        $obRFolhaPagamentoPrevidencia->setCodPrevidencia             ( $_REQUEST['inCodPrevidencia'] );
        $obRFolhaPagamentoPrevidencia->consultarPrevidencia();
        $obErro = $obRFolhaPagamentoPrevidencia->excluirPrevidencia();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList,"Previdência: ".$obRFolhaPagamentoPrevidencia->getDescricao(),"excluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList,"Previdência: ".urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");

    break;
}

?>
