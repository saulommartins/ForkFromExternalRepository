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
    * Página de Processamento Almoxarifado
    * Data de Criação   : 30/01/2006

    * @author Analista      : Diego Barbosa Victoria
    * @author Desenvolvedor : Rodrigo

    * @ignore

    * Casos de uso: uc-03.03.01

    $Id: PRManterLocalizacao.php 61639 2015-02-19 13:05:36Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO. "RAlmoxarifadoLocalizacao.class.php"                               );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLocalizacaoFisicaItem.class.php"                );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
 $stPrograma = "ManterLocalizacao";
 $pgFilt     = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
 $pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
 $pgForm     = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
 $pgFormItem = "FM".$stPrograma."Item.php?".Sessao::getId()."&stAcao=$stAcao";
 $pgProc     = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
 $pgOcul     = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

 $obRegra = new RAlmoxarifadoLocalizacao();

switch ($stAcao) {

    case "incluir":
        if (!$_POST['stLocalizacao']) {
            SistemaLegado::exibeAviso('O almoxarifado deve ter a localização cadastrada.','form','erro',Sessao::getId() );
        } else {
            $rsRecordSetItem = new RecordSet;
            $obRegra->obRAlmoxarifadoAlmoxarifado->setCodigo($_POST['inCodAlmoxarifado']);
            $obRegra->setLocalizacao($_POST['stLocalizacao']);

            $arValores = Sessao::read('arValores');

            for ($inPosTransf = 0; $inPosTransf < count($arValores); $inPosTransf++) {
                $obRegra->addLocalizacaoItem();
                $obRegra->roLocalizacaoItem->obRCatalogoItem->setCodigo($arValores[$inPosTransf]['CodItem']);
                $obRegra->roLocalizacaoItem->obRMarca->setCodigo($arValores[$inPosTransf]['CodMarca']);
            }

            $obErro = $obRegra->incluir();

            if (!($obErro->ocorreu())) {
                SistemaLegado::alertaAviso($pgForm, $obRegra->getLocalizacao(),"incluir","aviso", Sessao::getId(), "");
            } else {
                SistemaLegado::exibeAviso($obErro->getDescricao(),"n_incluir","erro");
            }
        }
    break;

    case "alterar":

        $obErro = new Erro;

        $inCodAlmoxarifado = Sessao::read('inCodAlmoxarifado');
        $inNomAlmoxarifado = Sessao::read('inNomAlmoxarifado');

        $rsRecordSetItem = new RecordSet;
        $obRegra->obRAlmoxarifadoAlmoxarifado->setCodigo($_POST['inCodAlmoxarifado']);
        
        if ($_REQUEST['stLocalizacao']) {
            $obRegra->setLocalizacao($_POST['stLocalizacao']);
        } else {
            $obErro->setDescricao('Campo Localização não pode ser vazio');
        }
        
        $stFiltro = " WHERE cod_localizacao = '".$_REQUEST['inCodLocalizacao']."' AND cod_almoxarifado = ".$_POST['inCodAlmoxarifado'];
        $obTAlmoxarifadoLocalizacaoFisicaItem = new TAlmoxarifadoLocalizacaoFisicaItem();
        $obTAlmoxarifadoLocalizacaoFisicaItem->recuperaCodLocal($rsCodLocal, $stFiltro, $stOrdem, $boTransacao);

        $obRegra->setCodigo($rsCodLocal->getCampo('cod_localizacao'));

        $arValores = Sessao::read('arValores');

        for ($inPosTransf = 0; $inPosTransf < count($arValores); $inPosTransf++) {
            $obRegra->addLocalizacaoItem();
            $obRegra->roLocalizacaoItem->obRCatalogoItem->setCodigo($arValores[$inPosTransf]['CodItem']);
            $obRegra->roLocalizacaoItem->obRMarca->setCodigo($arValores[$inPosTransf]['CodMarca']);
        }

        $obErro = $obRegra->alterar();

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList, $obRegra->getLocalizacao(),"alterar","aviso", Sessao::getId(), "");
        } else {
            SistemaLegado::exibeAviso($obErro->getDescricao(),"n_alterar","erro");
        }
    break;

    case "alterarItens":
        $rsRecordSetItem = new RecordSet;
        $obRegra->setCodigo($_POST['inCodLocalizacao']);
        $obRegra->obRAlmoxarifadoAlmoxarifado->setCodigo($_POST['inCodAlmoxarifado']);
        $obRegra->obRAlmoxarifadoItemMarca->obRCatalogoItem->setCodigo($_POST['inCodItem']);
        $obRegra->obRAlmoxarifadoItemMarca->obRMarca->setCodigo($_POST['inCodMarca']);

        $obErro = $obRegra->alterarItens();

        if (!($obErro->ocorreu())) {
            SistemaLegado::alertaAviso($pgFormItem, "Item: ".$obRegra->obRAlmoxarifadoItemMarca->obRCatalogoItem->getCodigo() ." - ". "Almoxarifado: ". $obRegra->obRAlmoxarifadoAlmoxarifado->getCodigo(),"alterar","aviso", Sessao::getId(), "");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "alterarItem":
    break;

    case "excluir":

        $inCodLocalizacao  = $_REQUEST['inCodLocalizacao'];
        $stLocalizacao     = $_REQUEST['stLocalizacao'];
        $inCodAlmoxarifado = $_REQUEST['inCodAlmoxarifado'];

        Sessao::write('inCodAlmoxarifado', $inCodAlmoxarifado);

        $obRegra->setCodigo($inCodLocalizacao);
        $obRegra->setLocalizacao($stLocalizacao);
        $obRegra->obRAlmoxarifadoAlmoxarifado->setCodigo($inCodAlmoxarifado);

        $obErro = $obRegra->excluir();
        if (!$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Localização : ".$obRegra->getCodigo().' - '.$obRegra->getLocalizacao(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }

    break;

    default:
    break;
}

?>
