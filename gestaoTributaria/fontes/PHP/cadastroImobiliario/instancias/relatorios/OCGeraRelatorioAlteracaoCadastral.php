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
 * Página de processamento oculto para o relatório de alteração cadastral
 * Data de Criação   : 13/04/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezio Paulino

 * @ignore

 * $Id: OCGeraRelatorioAlteracaoCadastral.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.25
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_FW_PDF."ListaPDF.class.php";

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Alteração Cadastral:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arCabecalho = Sessao::read('arCabecalho');
$inCountCabecalho = count( $arCabecalho );

$rsImoveis = Sessao::read('rsImoveis');

$obPDF->addRecordSet($rsImoveis);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "INSCRIÇÃO"   , 8 , 8);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "LOCALIZAÇÃO" , 9 , 8);
$obPDF->addCabecalho   ( "LOTE"        , 7 , 8);

if ($inCountCabecalho == 0) {
    $obPDF->addCabecalho( "LOGRADOURO"    , 30 , 8);
    $obPDF->addCabecalho( "BAIRRO"        , 8  , 8);
    $obPDF->addCabecalho( "PROPRIETÁRIOS" , 30 , 8);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho( "SITUAÇÃO"      , 8  , 8);
    $obPDF->setAlinhamento ( "L" );
} else {
    //monta cabecalhos dinamicos
    $inWidth     = $arCabecalho['width'];
    unset( $arCabecalho['width'] );
    foreach ($arCabecalho as $key => $valor) {
        $valor = strtr($valor, "çãáóé", "ÇÂÁÉÓ");
        $inNomCabecalho = strtoupper( $valor );
        $obPDF->addCabecalho   ( $inNomCabecalho , $inWidth, 8  );
    }
}

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "inscricao"    , 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "localizacao"  , 7 );
$obPDF->addCampo       ( "lote"         , 7 );

if ($inCountCabecalho == 0) {
    $obPDF->addCampo   ( "logradouro"   , 7 );
    $obPDF->addCampo   ( "bairro"       , 7 );
    $obPDF->addCampo   ( "proprietario" , 7 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo   ( "situacao"     , 7 );
    $obPDF->setAlinhamento ( "L" );
} else {
    foreach ($arCabecalho as $key => $valor) {
        $obPDF->addCampo( $valor , 8 );
    }
}

$arFiltro        = Sessao::read('filtroRelatorio');
$nomFiltroSessao = Sessao::read( "nomFiltro" );
$arNomAtributo = array();

if (!empty($arFiltro['inCodAtributosSelecionados'])) {
    foreach ($arFiltro['inCodAtributosSelecionados'] as $inCodAtributo) {
        $arNomAtributo[] = $nomFiltroSessao['atributo'][$inCodAtributo];
    }
}

$obPDF->addFiltro( 'Código Localização Inicial'    , $arFiltro['inCodInicioLocalizacao'] );
$obPDF->addFiltro( 'Código Localização Final'      , $arFiltro['inCodTerminoLocalizacao']);
$obPDF->addFiltro( 'Código Lote Inicial'           , $arFiltro['inCodInicioLote']        );
$obPDF->addFiltro( 'Código Lote Final'             , $arFiltro['inCodTerminoLote']       );
$obPDF->addFiltro( 'Inscrição Imobiliária Inicial' , $arFiltro['inCodInicioInscricao']   );
$obPDF->addFiltro( 'Inscrição Imobiliária Final'   , $arFiltro['inCodTerminoInscricao']  );
$obPDF->addFiltro( 'Código Logradouro Inicial'     , $arFiltro['inCodInicioLogradouro']  );
$obPDF->addFiltro( 'Código Logradouro Final'       , $arFiltro['inCodTerminoLogradouro'] );
$obPDF->addFiltro( 'Código Bairro Inicial'         , $arFiltro['inCodInicioBairro']      );
$obPDF->addFiltro( 'Código Bairro Final'           , $arFiltro['inCodTerminoBairro']     );
$obPDF->addFiltro( 'Tipo de Relatório'             , $nomFiltroSessao['tipo_relatorio'][$arFiltro['stTipoRelatorio']] );
$obPDF->addFiltro( 'Atributos Relacionados'        , $arNomAtributo );
$obPDF->addFiltro( 'Ordenação'                     , $nomFiltroSessao['ordenacao'][$arFiltro[ 'stOrder']]             );

$rsDados = Sessao::read('rsImoveis');

$rsTotais = new RecordSet;
$arTeste  = array();
$inTotalRegs = $rsDados->getNumLinhas();

if ($inTotalRegs < 0) {
    $inTotalRegs = 0;
}

$arTeste[0]["total"] = $inTotalRegs;
$rsTotais->preenche( $arTeste );

$obPDF->addRecordSet         ($rsTotais);

$obPDF->setQuebraPaginaLista ( false         );
$obPDF->setAlinhamento       ( "L"           );
$obPDF->addCabecalho         ( "Total de registros", 100, 10  );

$obPDF->setAlinhamento       ( "L"           );
$obPDF->addCampo             ( "total",  8  );

$obPDF->show();

?>
