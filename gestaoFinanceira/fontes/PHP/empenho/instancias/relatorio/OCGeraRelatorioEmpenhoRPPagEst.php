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
    * Data de Criação   : 18/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 31583 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-01-15 12:00:12 -0200 (Ter, 15 Jan 2008) $

    * Casos de uso : uc-02.03.09
*/

/*
$Log$
Revision 1.6  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF( "L" );

$arFiltro = Sessao::read('filtroRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

//$obRRelatorio->setExercicio      ( Sessao::getExercicio()                               );
$obRRelatorio->recuperaCabecalho ( $arConfiguracao                                              );
$obPDF->setModulo                ( "Empenho - ".Sessao::getExercicio()                      );
switch ($arFiltro['inSituacao']) {
    CASE 1:
        $stSituacao = "Pagamentos";
        break;
    CASE 2:
        $stSituacao = "Estornos";
        break;
    DEFAULT:
        $stSituacao = "Pagamentos / Estornos";
        break;
}

$dtPeriodo = $arFiltro['stDataInicial']." a ".$arFiltro['stDataFinal'] ." - ".$stSituacao;
$obPDF->setSubTitulo             ( $dtPeriodo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsRecordSet );

$inTamanho = 9;
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("DATA"			, 5 , $inTamanho);
$obPDF->addCabecalho("EMPENHO"		, 7 , $inTamanho);
$obPDF->addCabecalho("NOTA"			, 5 , $inTamanho);
$obPDF->addCabecalho("CLASSIFICAÇÃO", 10, $inTamanho);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("CREDOR"		, 30, $inTamanho);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("CONTA"		, 6 , $inTamanho);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("BANCO"		, 30, $inTamanho);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("VALOR"		, 7, $inTamanho);
$obPDF->addQuebraLinha("nivel",2,5);

$inTamanho = 7;
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("data"			 , $inTamanho );
$obPDF->addCampo("empenho"		 , $inTamanho );
$obPDF->addCampo("cod_nota"		 , $inTamanho );
$obPDF->addCampo("cod_estrutural", $inTamanho );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("credor"		 , $inTamanho );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("conta"		 , $inTamanho );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("banco"		 , $inTamanho );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"		 , $inTamanho );

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
