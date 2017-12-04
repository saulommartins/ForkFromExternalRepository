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
    * Página de processamento oculto para o relatório de bairros
    * Data de Criação   : 23/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCGeraRelatorioBairros.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.19
*/

/*
$Log$
Revision 1.7  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatório:"   );
$obPDF->setTitulo            ( "Bairros:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arTransf5Sessao = Sessao::read('sessao_transf5');
$obPDF->addRecordSet( $arTransf5Sessao );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "ESTADO"    ,12, 10 );
$obPDF->addCabecalho   ( "MUNÍCIPIO" ,12, 10 );
$obPDF->addCabecalho   ( "CÓD."      ,5, 10 );
$obPDF->addCabecalho   ( "BAIRROS"   ,21, 10 );

$boRSMD     = Sessao::read('boRSMD');
$boAliquota = Sessao::read('Aliquota');

if ($boRSMD == true) {
    $obPDF->addCabecalho   ( "Vlr.M² Terr."    ,10, 10 );
    $obPDF->addCabecalho   ( "Vlr.M² Pred."    ,10, 10 );
}
if ($boAliquota == true) {
    $obPDF->addCabecalho   ( "Aliquota Terr."    ,10, 10 );
    $obPDF->addCabecalho   ( "Aliquota Pred."    ,10, 10 );
}

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "nom_uf"        , 8 );
$obPDF->addCampo       ( "nom_municipio" , 8 );
$obPDF->addCampo       ( "cod_bairro"    , 8 );
$obPDF->addCampo       ( "nom_bairro"    , 8 );
if ($boRSMD == true) {
    $obPDF->addCampo       ( "valor_m2_territorial" , 8 );
    $obPDF->addCampo       ( "valor_m2_predial"     , 8 );
}
if ($boAliquota == true) {
    $obPDF->addCampo       ( "aliquota_territorial" , 8 );
    $obPDF->addCampo       ( "aliquota_predial"     , 8 );
}

$arFiltro = Sessao::read('filtroRelatorio');
$obPDF->addFiltro( 'Nome do Bairro' , $arFiltro['stNomBairro']                                         );
$obPDF->addFiltro( 'Código Inicial' , $arFiltro['inCodInicio']                                         );
$obPDF->addFiltro( 'Código Final'   , $arFiltro['inCodTermino']                                        );
$obPDF->addFiltro( 'Estado'         , $arFiltro['uf'][$arFiltro[ 'inCodigoUF' ]]              );
$obPDF->addFiltro( 'Município'      , $arFiltro['municipio'][$arFiltro[ 'inCodigoMunicipio']] );
$obPDF->addFiltro( 'Ordenação'      , $arFiltro['ordenacao'][$arFiltro[ 'stOrder']]           );

$rsDados = Sessao::read('sessao_transf5');

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
