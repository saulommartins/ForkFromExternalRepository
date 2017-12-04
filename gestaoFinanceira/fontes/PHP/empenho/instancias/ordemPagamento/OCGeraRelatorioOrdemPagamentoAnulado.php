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
    * Data de Criação   : 18/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 31627 $
    $Name$
    $Autor: $
    $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $

    * Casos de uso: uc-02.03.05
                    uc-02.03.25
*/

/*
$Log$
Revision 1.11  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.10  2006/07/21 19:40:17  jose.eduardo
Bug #6608#

Revision 1.9  2006/07/14 18:56:43  jose.eduardo
Bug #6534#

Revision 1.8  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF();
$rsVazio      = new RecordSet;

$arFiltro = Sessao::read('filtroRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');

$obPDF->setCodigoBarras( str_pad('',8,'0').str_pad($arFiltro['inCodigoOrdem'],6,'0',STR_PAD_LEFT).substr(Sessao::getExercicio(),2,2).$arFiltro['inCodEntidade'] );

// Adicionar logo no relatorio
if ( $rsRecordSet[0]->getNumLinhas() == "1" ) {
    $stCodEntidade = $rsRecordSet[0]->getCampo("entidade");
    $inCodEntidade = $stCodEntidade{0};
    $obRRelatorio->setCodigoEntidade( $inCodEntidade );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}
$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
if (Sessao::read('acao') == 1002) {
    $obPDF->setAcao                 ( "Anulação de Ordem de Pagamento - Reemissão" );
} else {
    $obPDF->setAcao                 ( "Anulação de Ordem de Pagamento" );
}
$obPDF->setSubTitulo            ( "Ordem N. ".str_pad($arFiltro['inCodigoOrdem'],6,'0',STR_PAD_LEFT)."/".$arFiltro['stExercicioOrdem']."                    Vencimento: ".$arFiltro['dtDataVencimento']);
$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );

$obPDF->stData                  = SistemaLegado::dataToBr( substr($arFiltro['stTimestampAnulado'],0,10) );
$obPDF->stHora                  = substr($arFiltro['stTimestampAnulado'],11,8);

//echo "<pre>";
//print_r( $rsRecordSet );
//echo "</pre>";
//die(0);

//Linha1
$obPDF->addRecordSet            ( $rsRecordSet[0] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "ENTIDADE"     , 100, 5, '', '', 'LTR','205,206,205');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "entidade"   , 8, '', '', 'LR','205,206,205');

//Bloco Vazio
$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->addCabecalho            ( "" , 100, 5, '', '', 'T');
$obPDF->addCampo                ( "", 8, '', '', '');

//Bloco4
$obPDF->addRecordSet            ( $rsRecordSet[1] );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "Empenho"         , 40, 5, '', '', 'LTB');
$obPDF->addCabecalho            ( "Nota Liquidação" , 30, 5, '', '', 'LTBR');
$obPDF->addCabecalho            ( "Valor Liquidação", 10, 5, '', '', 'LTBR');
$obPDF->addCabecalho            ( "Saldo Liquidação", 10, 5, '', '', 'LTBR');
$obPDF->addCabecalho            ( "Valor Anulado"   , 10, 5, '', '', 'LTBR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "1", 8, '', '', 'LR');
$obPDF->addCampo                ( "2", 8, '', '', 'LR');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "3", 8, '', '', 'LR');
$obPDF->addCampo                ( "4", 8, '', '', 'LR');
$obPDF->addCampo                ( "5", 8, '', '', 'LR');

//Bloco5
$obPDF->addRecordSet            ( $rsRecordSet[2] );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "" , 50, 5, '', '', 'LT');
$obPDF->addCabecalho            ( "" , 30, 5, '', '', 'T');
$obPDF->addCabecalho            ( "" , 10, 5, '', '', 'T');
$obPDF->addCabecalho            ( "" , 10, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "0", 8, '', '', 'LB');
$obPDF->addCampo                ( "0", 8, '', '', 'B');
$obPDF->addCampo                ( "1", 8, '', '', 'B');
$obPDF->addCampo                ( "2", 8, '', '', 'LBR');

//Bloco Vazio
$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->addCabecalho            ( "" , 100, 5, '', '', '');
$obPDF->addCampo                ( "", 8, '', '', '');

//Bloco5
$obPDF->addRecordSet            ( $rsRecordSet[3] );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "" , 100, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "1", 8, '', '', 'LBR');

//Bloco Vazio
$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->addCabecalho            ( "" , 100, 5, '', '', '');
$obPDF->addCampo                ( "", 8, '', '', '');

$obPDF->addRecordSet            ( $rsRecordSet[4] );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "Motivo: " , 100, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "1", 8, '', '', 'LBR');

$obPDF->show();
?>
