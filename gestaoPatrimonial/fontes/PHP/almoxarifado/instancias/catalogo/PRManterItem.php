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
    * Data de Criação   : 11/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    * Casos de uso: uc-03.03.06

    $Id: PRManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO. "RAlmoxarifadoCatalogoItem.class.php";
include_once CAM_GP_ALM_NEGOCIO. "RAlmoxarifadoEstoqueItem.class.php";
include_once CAM_GP_COM_MAPEAMENTO. "TComprasCotacaoFornecedorItem.class.php";

$stAcao = $request->get('stAcao');

$inCodAtributosSelecionados = $_REQUEST['inCodAtributosSelecionados'];

$obRegra = new RAlmoxarifadoCatalogoItem;

if ($_REQUEST['boAtivo'] == 'false') {

    $obRAlmoxarifadoEstoqueItem =  new RAlmoxarifadoEstoqueItem;
    $obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($_REQUEST['inCodigo']);
    $obRAlmoxarifadoEstoqueItem->retornaSaldoEstoque($saldoItens);

    if ($saldoItens > 0) {
        SistemaLegado::exibeAviso("Este item não pode ser inativado pois possui saldo em estoque!");
        exit;
    }
}

if (Sessao::read('Valores')) {
    foreach ( Sessao::read('Valores') as $stCampo => $stValor ) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterItem";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stFiltro";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stFiltro";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stFiltro";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stFiltro";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stFiltro";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributos_" );
$obAtributos->recuperaVetor( $arChave    );
$stFiltro = '';

$arCodUnidade = explode( '-', $_POST['inCodUnidade']);

switch ($stAcao) {
    case "incluir":

        $obRegra->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogo']);
        $rsCodClassificacao = new RecordSet;
        $obRegra->obRAlmoxarifadoClassificacao->setEstrutural(trim($_REQUEST['stChaveClassificacao']));
        $obRegra->obRAlmoxarifadoClassificacao->recuperaCodigoClassificacao($rsCodClassificacao);
        $obRegra->obRAlmoxarifadoClassificacao->setCodigo($rsCodClassificacao->getCampo('cod_classificacao'));
        $obRegra->setDescricao        (stripcslashes($_REQUEST['stDescricao']) );
        $obRegra->setDescricaoResumida(stripcslashes($_REQUEST['stDescricaoResumida']) );
        $obRegra->obRAlmoxarifadoTipoItem->setCodigo( $_REQUEST['inCodTipo'] );
        $obRegra->obRUnidadeMedida->setCodUnidade( $arCodUnidade[0] );
        $obRegra->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $arCodUnidade[1]);
        $obRegra->obRAlmoxarifadoControleEstoque->setEstoqueMaximo( $_REQUEST['nuEstoqueMaximo'] );
        $obRegra->obRAlmoxarifadoControleEstoque->setEstoqueMinimo( $_REQUEST['nuEstoqueMinimo'] );
        $obRegra->obRAlmoxarifadoControleEstoque->setPontoDePedido( $_REQUEST['nuPontoPedido'] );

        //monta array de atributos dinamicos
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->setChavePersistenteValores( array("cod_atributo"=>$arChaves[0], "cod_cadastro"=>$arChaves[1] ));
            $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            if ( ($rsAtributos->getCampo('nao_nulo') == 'f') AND ( trim($value) == '' ) ) {
                SistemaLegado::exibeAviso(urlencode("Campo ".$rsAtributos->getCampo('nom_atributo')." não pode ser nulo!()"),"","alerta");
                exit;
            } else {
                $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
        }

        for ($inCount = 0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }

        $obErro = $obRegra->incluir();
        if ( !$obErro->ocorreu() ) {
            Sessao::write('carregarCombo',true);
            SistemaLegado::alertaAviso($pgForm."&inCodCatalogo=".$_REQUEST['inCodCatalogo']."&stChaveClassificacao=".$_POST['stChaveClassificacao'], $obRegra->inCodigo." - ".htmlspecialchars($obRegra->stDescricaoResumida),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case "alterar":

        $obRegra->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo( $_POST['inCodCatalogo'] );
        $rsCodClassificacao = new RecordSet;
        $obRegra->obRAlmoxarifadoClassificacao->setEstrutural( $_POST['stChaveClassificacao'] );
        $obRegra->obRAlmoxarifadoClassificacao->recuperaCodigoClassificacao($rsCodClassificacao);
        $obRegra->obRAlmoxarifadoClassificacao->setCodigo( $rsCodClassificacao->getCampo('cod_classificacao') );
        $obRegra->setCodigo( $_POST['inCodigo'] );
        $obRegra->setDescricao          ( stripcslashes($_POST['stDescricao']) );
        $obRegra->setDescricaoResumida  ( stripcslashes($_POST['stDescricaoResumida']) );
        $obRegra->setAtivo              ( $_POST['boAtivo'] );
        $obRegra->obRAlmoxarifadoTipoItem->setCodigo( $_POST['inCodTipo'] );
        $obRegra->obRUnidadeMedida->setCodUnidade( $arCodUnidade[0] );
        $obRegra->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $arCodUnidade[1]);
        $obRegra->obRAlmoxarifadoControleEstoque->setEstoqueMaximo( $_POST['nuEstoqueMaximo'] );
        $obRegra->obRAlmoxarifadoControleEstoque->setEstoqueMinimo( $_POST['nuEstoqueMinimo'] );
        $obRegra->obRAlmoxarifadoControleEstoque->setPontoDePedido( $_POST['nuPontoPedido'] );

        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->setChavePersistenteValores( array("cod_atributo"=>$arChaves[0], "cod_cadastro"=>$arChaves[1] ));
            $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            if ( ($rsAtributos->getCampo('nao_nulo') == 'f') AND ( trim($value) == '' ) ) {
                SistemaLegado::exibeAviso(urlencode("Campo ".$rsAtributos->getCampo('nom_atributo')." não pode ser nulo!()"),"","alerta");
                exit;
            } else {
                $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
        }

        $boBloqueiaCombo = Sessao::read('boBloqueiaCombo');
        if ($boBloqueiaCombo) {
            $rsAtributosSelecionados = Sessao::read('rsAtributosSelecionados');
            $inCount = 0;
            while ( !$rsAtributosSelecionados->eof() ) {
                $inCodAtributosSelecionados[$inCount] = $rsAtributosSelecionados->getCampo('cod_atributo');
                $rsAtributosSelecionados->proximo();
                $inCount++;
            }
        }

        for ($inCount = 0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }

        if (  $obRegra->obRAlmoxarifadoTipoItem->getCodigo() != "" ) {
            $obErro = $obRegra->alterar();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList, $obRegra->inCodigo." - ".htmlspecialchars($obRegra->stDescricao),"alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode("Campo Tipo Inválido!()"),"","alerta");
        }
    break;

    case "excluir";
    $obErro = new Erro;
    $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
    $obTComprasCotacaoFornecedorItem->setDado('cod_item',$_REQUEST['inCodigo']);
    $obTComprasCotacaoFornecedorItem->recuperaCotacaoFornecedorItem($rsRecordSet);

        $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
    $obTAlmoxarifadoEstoqueMaterial->setDado('cod_item',$_REQUEST['inCodigo']);
    $obTAlmoxarifadoEstoqueMaterial->recuperaEstoqueMaterialItem($rsEstoqueItem);

    if ($rsRecordSet->getNumLinhas() > 0 || $rsEstoqueItem->getNumLinhas() > 0) {
        $obErro->setDescricao('Item não pode ser excluído!');
    }
    if ( !$obErro->ocorreu() ) {
        $obRegra->setCodigo($_REQUEST['inCodigo']);
            $obRegra->consultar();
            $obErro = $obRegra->excluir();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,$obRegra->inCodigo." - ".htmlspecialchars($obRegra->getDescricaoResumida()),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","O Item ".$obRegra->inCodigo." - ".$obRegra->getDescricaoResumida()." já está sendo usado pelo sistema","n_excluir","erro", Sessao::getId(), "../");
        }
}

?>
