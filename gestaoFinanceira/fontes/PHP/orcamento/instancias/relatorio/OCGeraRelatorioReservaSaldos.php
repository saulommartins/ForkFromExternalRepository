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
    * Página que gera o relatorio de Reseva de Saldos
    * Data de Criação   : 09/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 31801 $
    $Name$
    $Author: luciano $
    $Date: 2007-09-12 11:31:51 -0300 (Qua, 12 Set 2007) $

    * Casos de uso: uc-02.01.08
                    uc-02.01.28
*/

/*
$Log$
Revision 1.16  2007/09/12 14:31:51  luciano
Ticket#10081#

Revision 1.15  2007/02/21 19:01:58  rodrigo_sr
Bug #7872#

Revision 1.13  2006/10/25 12:19:07  larocca
Bug #7283#

Revision 1.11  2006/07/06 19:30:02  cako
Bug #6026#

Revision 1.10  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF( "L" );

$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

if ( is_array( $arFiltro['inCodEntidade'] ) ) {
    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        $arEntidade[] = $arNomFiltro['entidade'][$inCodEntidade];
    }
}

$obPDF->addFiltro( 'Entidades' , $arEntidade );
$obPDF->addFiltro( 'Dotação Orçamentária', $arFiltro['stNomDespesa'] );
$obPDF->addFiltro( 'Número da Reserva', $arFiltro['inCodReserva'] );
if( $arFiltro['stDtInicial'] and $arFiltro['stDtFinal'] )
    $stPeriodicidade = $arFiltro['stDtInicial'].' até '.$arFiltro['stDtFinal'];
$obPDF->addFiltro( 'Periodicidade', $stPeriodicidade );
$obPDF->addFiltro( 'Órgão', $arFiltro['inNumOrgao'].$arNomFiltro['orgao'][$arFiltro['inNumOrgao']] );
$obPDF->addFiltro( 'Unidade', $arFiltro['inNumUnidade'].$arNomFiltro['unidade'][$arFiltro['inNumUnidade']] );

if (!$arFiltro['stDtInicial']) {
    $stSubTitulo = "Periodicidade: Não informada";
} else {
    $stSubTitulo = "Periodicidade: ". $arFiltro['stDtInicial'] . " até " . $arFiltro['stDtFinal'];
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Reserva de Saldos" );
$obPDF->setSubTitulo         ( $stSubTitulo );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsLista = Sessao::read('rsLista');
$rsLista->addFormatacao("vl_reserva"   , "NUMERIC_BR_NULL" );

$obPDF->addRecordSet( $rsLista );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("NR. RESERVA", 8, 10);
$obPDF->addCabecalho("TIPO", 6, 10);
$obPDF->addCabecalho("ORIGEM", 10, 10);
$obPDF->addCabecalho("DATA INICIAL", 8, 10);
$obPDF->addCabecalho("DATA FINAL", 8, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("DOTAÇÃO", 25, 10);
$obPDF->addCabecalho("RECURSO", 8, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("VALOR", 8, 10);
$obPDF->addCabecalho("SITUAÇÃO NO PERÍODO", 10, 10);
$obPDF->addCabecalho("DATA ANULAÇÃO", 10, 10);
$obPDF->addQuebraLinha("nivel",0,5);
$obPDF->addQuebraPagina("pagina",1);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("cod_reserva", 8 );
$obPDF->addCampo("tipo", 8 );
$obPDF->addCampo("origem", 8 );
$obPDF->addCampo("dt_validade_inicial", 8 );
$obPDF->addCampo("dt_validade_final", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("[cod_despesa]/[dotacao_formatada]", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("cod_recurso", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("vl_reserva", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("situacao", 8 );
$obPDF->addCampo("dt_anulacao", 8 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    //$obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
//$obPDF->montaPDF();
//$obPDF->OutPut();
?>
