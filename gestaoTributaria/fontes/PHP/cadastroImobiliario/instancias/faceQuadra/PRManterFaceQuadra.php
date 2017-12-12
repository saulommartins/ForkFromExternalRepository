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
    * Página de processamento para o cadastro de face de auqdra
    * Data de Criação   : 02/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonisma Régis Bernardo

    * @ignore

    * $Id: PRManterFaceQuadra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.7  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GT_CIM_NEGOCIO."RCIMFaceQuadra.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stAcao = $request->get('stAcao');
//Define o nome dos arquivos PHP
$stPrograma = "ManterFaceQuadra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RCIMFaceQuadra;
$obErro  = new Erro;

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {

    case "incluir":

        include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorComposto($_REQUEST["stChaveLocalizacao"]);
        $obRCIMLocalizacao->consultaCodigoLocalizacao($_REQUEST['inCodigoLocalizacao']);

        $obRegra->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST['inCodigoLocalizacao']);

        // dados de atributos dinamicos
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRegra->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRegra->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRegra->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRegra->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRegra->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRegra->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRegra->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRegra->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRegra->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        // dados de face de quadra_trecho
        $arTrechosSessao = Sessao::read('Trechos');
        if (count($arTrechosSessao) > 0) {
            for ($inCount=0; $inCount<count($arTrechosSessao); $inCount++) {
                $obRegra->addTrecho( $arTrechosSessao[$inCount] );
            }
        } else {
            $obErro->setDescricao( "É necessário incluir pelo menos um trecho." );
        }

        if (!$obErro->ocorreu()) {
            $obErro = $obRegra->incluirFaceQuadra();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Face de quadra para localização: ".$_REQUEST['stChaveLocalizacao'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

     break;

    case "alterar":
        // dados de face de quadra
        $obRegra->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST['inCodigoLocalizacao'] );
        $obRegra->setCodigoFace( $_REQUEST['inCodigoFace'] );

        // dados de atributos dinamicos
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRegra->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRegra->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRegra->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRegra->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRegra->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRegra->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRegra->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRegra->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRegra->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        // dados de face de quadra_trecho
        $arTrechosSessao = Sessao::read('Trechos');
        if (count($arTrechosSessao) > 0) {
            for ($inCount=0; $inCount<count($arTrechosSessao); $inCount++) {
                $obRegra->addTrecho( $arTrechosSessao[$inCount] );
            }
        } else {
            $obErro->setDescricao( "É necessário incluir pelo menos um trecho." );
        }

        if (!$obErro->ocorreu()) {
            $obErro = $obRegra->alterarFaceQuadra();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Face de quadra para localização: ".$_REQUEST['inCodigoFace'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case "excluir":
        $obRegra->setCodigoFace ( $_REQUEST['inCodigoFace'] );
        $obRegra->obRCIMTrecho->setCodigoTrecho( $inCodigoTrecho );
        $obRegra->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST['inCodigoLocalizacao'] );

        $obErro = $obRegra->excluirFaceQuadra();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Face de quadra: ".$_REQUEST["inCodigoFace"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;

    case "reativar":
        $obRegra->setDataBaixa ( $_REQUEST["stTimestamp"] );
        $obRegra->setCodigoFace  ( $_REQUEST["inCodigoFace"] );
        $obRegra->obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST['inCodigoLocalizacao'] );
        $obRegra->setJustificativa ( $_REQUEST["stJustificativa"] );
        $obRegra->setJustificativaReativar ( $_REQUEST["stJustReat"] );
        $obErro = $obRegra->reativarFaceQuadra();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Reativar face concluído com sucesso! (Face de Quadra: ".$_REQUEST["inCodigoFace"].")","reativar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso("Erro ao reativar face (".urlencode($obErro->getDescricao()).")","n_reativar","erro");
        }
        break;

    case "baixar":
        $obRegra->setCodigoFace  ( $_REQUEST["inCodigoFace"]     );
        $obRegra->obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST['inCodigoLocalizacao'] );
        $obRegra->setJustificativa ( $_REQUEST["stJustificativa"] );
        $obErro = $obRegra->baixarFaceQuadra();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Face de Quadra: ".$_REQUEST["nCodigoFace"],"baixar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
    break;

    case "historico";

        $obRegra->setCodigoFace  ( $_REQUEST['inCodigoFace']  );
        $obRegra->obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST['inCodigoLocalizacao'] );

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRegra->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRegra->alterarCaracteristicas();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Face de Quadra: ".$_REQUEST['inCodigoFace'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

}

?>
