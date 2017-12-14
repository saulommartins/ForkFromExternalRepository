<?php
/**
* Arquivo de instância para processamento de relatório de CGM
* Data de Criação: 04/07/2013

* Copyright CNM - Confederação Nacional de Municípios

$Id: $

* @author Analista      : Eduardo Schitz
* @author Desenvolvedor : Franver Sarmento de Moraes

* @package URBEM
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(1,4,1);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relListagemCGM.rptdesign');

$preview->addParametro( 'sTipoFis', (!empty($_REQUEST["inTipoFis"]) ? $_REQUEST["inTipoFis"] : 0));
$preview->addParametro( 'sTipoJur', (!empty($_REQUEST["inTipoJur"]) ? $_REQUEST["inTipoJur"] : 0));
$preview->addParametro( 'sTipoInt', (!empty($_REQUEST["inTipoInt"]) ? $_REQUEST["inTipoInt"] : 0));

$preview->addParametro( 'sDataIni', $_REQUEST["dtDataIni"] );
$preview->addParametro( 'sDataFim', $_REQUEST["dtDataFim"] );

$preview->addParametro( 'sCodPais'    , $_REQUEST["inCodPais"] );
$preview->addParametro( 'sCodEstadoUf', $_REQUEST["inCodEstadoUf"] );
$preview->addParametro( 'sCodCidade'  , $_REQUEST["inCodMunicipio"] );

$arValorComposto = explode(".",$_REQUEST["stValorComposto"]);
$stValorAtividade = '';
if (array_key_exists(2, $arValorComposto)) {
    if ($arValorComposto[2] == '0000') {
        if ($arValorComposto[1] == '000') {
            $stValorAtividade =  $arValorComposto[0].'.';
        } else {
            $stValorAtividade = $arValorComposto[0].'.'.$arValorComposto[1].'.';
        }
    }
} else {
    $stValorAtividade = $_REQUEST["stValorComposto"];
}

$preview->addParametro( 'sCodAtividade' , $stValorAtividade );

$preview->addParametro( 'sOrderBy', $_REQUEST["stOrderBy"] );

$preview->addParametro( 'endereco', $_REQUEST["stEndereco"] );

$preview->preview();
