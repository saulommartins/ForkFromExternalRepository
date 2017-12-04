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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2008-01-15 12:00:12 -0200 (Ter, 15 Jan 2008) $

    * Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRazaoCredor.class.php"  );

$obRegra      = new REmpenhoRelatorioRazaoCredor;
$obPDF        = new ListaPDF( "L" );

$arFiltro = Sessao::read('filtroRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRegra->obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRegra->obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRegra->obRRelatorio->recuperaCabecalho ( $arConfiguracao          );
$obPDF->setModulo                ( "Empenho - ".Sessao::getExercicio()   );
$obPDF->setTitulo                ( "Razão do Credor " . $arFiltro['relatorio'] );

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( $arFiltro['inCGM'] );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->consultar($rsCGM);

$subTitulo = $arFiltro['inCGM'] . " - " . $rsCGM->getCampo('nom_cgm');
$obPDF->setSubTitulo             ( $subTitulo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsRecordSet );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("DATA", 8, 10);
$obPDF->addCabecalho("EMPENHO", 8, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("DESPESA", 23, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("EMPENHADO", 9, 10);
$obPDF->addCabecalho("ANULADO", 9, 10);
$obPDF->addCabecalho("LIQUIDADO", 10, 10);
$obPDF->addCabecalho("PAGO", 10, 10);
$obPDF->addCabecalho("A PAGAR LIQUIDADO", 10, 10);
$obPDF->addCabecalho("A PAGAR", 10, 10);
$obPDF->addQuebraLinha("nivel",2,5);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("data", 8 );
$obPDF->addCampo("empenho", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("despesa", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("empenhado", 8 );
$obPDF->addCampo("anulado", 8 );
$obPDF->addCampo("liquidado", 8 );
$obPDF->addCampo("pago", 8 );
$obPDF->addCampo("pagar_liquidado", 8 );
$obPDF->addCampo("pagar", 8 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    $obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
//$obPDF->montaPDF();
//$obPDF->OutPut();
?>
