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
 * Arquivo paga geração de relatorio de Cadastro de Imoveis
 * Data de Criação: 28/04/2005

 * @author Analista: Fabio Bertoldi
 * @author Desenvolvedor: Marcelo B. Paulino

 * @ignore

 * $Id: OCGeraRelatorioCadastroImobiliario.php 65872 2016-06-23 17:08:30Z evandro $

 * Casos de uso: uc-05.01.23
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_FW_PDF."ListaPDF.class.php";

set_time_limit(300000);

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Cadastro Imobiliário:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arCabecalho = Sessao::read('arCabecalho');
$inCountCabecalho = count( $arCabecalho );

$rsDados = clone Sessao::read('rsImoveis');
$rsDados2 = clone Sessao::read('rsImoveis');

$obPDF->addRecordSet( $rsDados );

if ($inCountCabecalho > 0) {
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho ( "INSCRIÇÃO", 8, 8 );
    $obPDF->addCabecalho ( "ENDEREÇO", 22, 8 );
    $obPDF->addCabecalho ( "PROPRIETÁRIOS", 28, 8 );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo ( "inscricao", 7 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo ( "[endereco]", 7 );
    $obPDF->addCampo ( "proprietario", 7 );

    //monta cabecalhos dinamicos
    $inWidth = $arCabecalho['width'];
    unset( $arCabecalho['width'] );
    foreach ($arCabecalho as $key => $valor) {
        $inNomCabecalho = strtoupper( strtr( $valor, "çãáóé", "ÇÂÁÉÓ" ) );
        $str = preg_replace( "/[^a-zA-Z0-9 ]/", "", strtr($valor, " áàãâéêíóôõúüçñÁÀÃÂÉÊÍÓÔÕÚÜÇÑ", "_aaaaeeiooouucnAAAAEEIIOOOUUCN"));
        $str = strtoupper( $str );
        $valor = trim( $str );
        $obPDF->addCabecalho( $inNomCabecalho , $inWidth, 8 );
        $obPDF->addCampo( $valor , 8 );
    }
} else {
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "INSCRIÇÃO"     ,8,  8 );
    $obPDF->addCabecalho   ( "LOCALIZAÇÃO"   ,10, 8 );
    $obPDF->addCabecalho   ( "LOTE"          ,10, 8 );

    $obPDF->addCabecalho( "ENDEREÇO"    ,30, 8 );
    $obPDF->addCabecalho( "CEP"        ,9, 8 );
    $obPDF->addCabecalho( "PROPRIETÁRIOS" ,25, 8 );
    $obPDF->addCabecalho( "SITUAÇÃO"      ,8,  8 );

    $obPDF->setAlinhamento( "C" );
    $obPDF->addCampo      ( "inscricao"    , 7 );
    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "localizacao"  , 7 );
    $obPDF->addCampo      ( "lote"         , 7 );

    $obPDF->addCampo( "[endereco]"   , 7 );
    $obPDF->addCampo( "cep"       , 7 );
    $obPDF->addCampo( "proprietario" , 7 );
    $obPDF->addCampo( "situacao"     , 7 );
}

$arFiltro = Sessao::read('filtroRelatorio');

$obPDF->addFiltro( 'Código Localização Inicial'    , $arFiltro['inCodInicioLocalizacao']   );
$obPDF->addFiltro( 'Código Localização Final'      , $arFiltro['inCodTerminoLocalizacao']  );
$obPDF->addFiltro( 'Código Lote Inicial'           , $arFiltro['inCodInicioLote']          );
$obPDF->addFiltro( 'Código Lote Final'             , $arFiltro['inCodTerminoLote']         );
$obPDF->addFiltro( 'Inscrição Imobiliária Inicial' , $arFiltro['inCodInicioInscricao']     );
$obPDF->addFiltro( 'Inscrição Imobiliária Final'   , $arFiltro['inCodTerminoInscricao']    );
$obPDF->addFiltro( 'Proprietário Inicial'          , $arFiltro['inCodProprietarioInicial'] );
$obPDF->addFiltro( 'Proprietário Final'            , $arFiltro['inCodProprietarioFinal']   );
$obPDF->addFiltro( 'Código Logradouro Inicial'     , $arFiltro['inCodInicioLogradouro']    );
$obPDF->addFiltro( 'Código Logradouro Final'       , $arFiltro['inCodTerminoLogradouro']   );
$obPDF->addFiltro( 'Código Bairro Inicial'         , $arFiltro['inCodInicioBairro']        );
$obPDF->addFiltro( 'Código Bairro Final'           , $arFiltro['inCodTerminoBairro']       );

$arTipo = array( 0 => "Sem Edificação", 1 => "Com Edificação", 2 => "Todos" );
$obPDF->addFiltro( 'Imóvel', $arTipo[ $arFiltro['stImoEd'] ] );

$obPDF->addFiltro( 'Tipo de Relatório'      , array_key_exists('tipo_relatorio', $arFiltro) ? $arFiltro['tipo_relatorio'][$arFiltro['stTipoRelatorio']] : '' );
$obPDF->addFiltro( 'Atributos Relacionados' , $arCabecalho  );
$obPDF->addFiltro( 'Ordenação'              , array_key_exists('ordenacao', $arFiltro) ? $arFiltro['ordenacao'][$arFiltro[ 'stOrder']] : '');
$obPDF->addFiltro( 'Situação', $arFiltro['stTipoSituacao'] );

$rsTotais = new RecordSet;
$arTeste  = array();
$inTotalRegs = $rsDados->getNumLinhas();
if ( $inTotalRegs < 0)
    $inTotalRegs = 0;

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
