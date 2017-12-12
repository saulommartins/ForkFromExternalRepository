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
    * Página de Formulario
    * Data de Criação   : 20/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2006-07-20 13:19:04 -0300 (Qui, 20 Jul 2006) $

    * Casos de uso: uc-02.03.24
*/

/*
$Log$
Revision 1.11  2006/07/20 16:19:04  jose.eduardo
Bug #6365#

Revision 1.10  2006/07/05 20:48:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );

$obRegra      = new TEmpenhoEmpenho;
$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF();
$rsVazio      = new RecordSet;

$arRecordSet = Sessao::read('arRecordSet');
$arFiltroRelatorio = Sessao::read('filtroRelatorio');
// Adicionar logo nos relatorios
if ( $arRecordSet[0]->getNumLinhas() == "1" ) {
    $stCodEntidade = $arRecordSet[0]->getCampo("entidade");
    $inCodEntidade = $stCodEntidade{0};
    $obRRelatorio->setCodigoEntidade( $inCodEntidade );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

  if ( Sessao::read('acao') == '1001' ) {
        $stReemissao = ' Reemissão';
    } else {
        $stReemissao = ' ';
    }

if( $arFiltroRelatorio['stExercicioNota'] )
    $obPDF->setSubTitulo            ( "Nota N. ".$arFiltroRelatorio['inCodNota']." / ".$arFiltroRelatorio['stExercicioNota']. $stReemissao );
else
    $obPDF->setSubTitulo            ( "Nota N. ".$arFiltroRelatorio['inCodNota']." / ".Sessao::getExercicio(). $stReemissao.$stReemissao );
$obPDF->setAcao                 ( "Nota de Anulação de Liquidação");
$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );

$obRegra->setDado("cod_nota"     ,$arFiltroRelatorio['inCodNota']);
$obRegra->setDado("exercicio"    ,$arFiltroRelatorio['stExercicioNota']);
$obRegra->setDado("cod_entidade" ,$arFiltroRelatorio['inCodEntidade']);
$obRegra->recuperaDadosLiquidacaoAnulada($rsLiquidacao,'','',$boTransacao);

$stData = SistemaLegado::dataToBr( substr($rsLiquidacao->getCampo("timestamp"),0,10) );
$obPDF->setData                 ( $stData );
$obPDF->stHora                  = substr($rsLiquidacao->getCampo("timestamp"),11,8);

//echo"<pre>";
//print_r( sessao->transf );
//echo "</pre>";
//die();

//Linha0
$obPDF->addRecordSet            ( $arRecordSet[0] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "ENTIDADE"     , 100, 5, '', '', 'LTR','205,206,205');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "entidade"   , 8, '', '', 'LR','205,206,205');

//Linha1
$obPDF->addRecordSet            ( $arRecordSet[1] );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "CREDOR"    , 45, 5, '', '', 'LT');
$obPDF->addCabecalho            ( "CGC/CPF"   , 40, 5, '', '', 'T');
$obPDF->setAlinhamento          ( "C" );
$obPDF->addCabecalho            ( "CGM"       , 15, 5, '', '', 'TR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Credor"  , 8, '', '', 'L');
$obPDF->addCampo                ( "CpfCnpj" , 8, '', '', '');
$obPDF->setAlinhamento          ( "C" );
$obPDF->addCampo                ( "Cgm"     , 8, '', '', 'R');

//Linha2
$obPDF->addRecordSet            ( $arRecordSet[2] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "ENDEREÇO"  , 35, 5, '', '', 'L');
$obPDF->addCabecalho            ( "FONE"      , 20, 5, '', '', '');
$obPDF->addCabecalho            ( "CIDADE"    , 30, 5, '', '', '');
$obPDF->addCabecalho            ( "UF"        , 15, 5, '', '', 'R');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Endereco", 8, '', '', 'L');
$obPDF->addCampo                ( "Fone"    , 8, '', '', 'B');
$obPDF->addCampo                ( "Cidade"  , 8, '', '', 'B');
$obPDF->addCampo                ( "Uf"      , 8, '', '', 'BR');

//Linha3
$obPDF->addRecordSet            ( $arRecordSet[3] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "EMPENHO"                     , 33, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "DATA DE EMISSÃO EMPENHO"     , 33, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "DATA DE VENCIMENTO LIQUIDACAO"  , 34, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Empenho"    , 8, '', '', 'LBR');
$obPDF->addCampo                ( "Emissao"    , 8, '', '', 'LBR');
$obPDF->addCampo                ( "Vencimento_Liquidacao" , 8, '', '', 'LBR');

//Linha4
$obPDF->addRecordSet            ( $arRecordSet[4] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "DESCRIÇÃO"                   , 100, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "descricao"    , 8, '', '', 'LBR');

$obPDF->addRecordSet            ( $arRecordSet[10] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "OBSERVAÇÃO"   ,  100, 5, '', '', 'LTR','');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "1"       , 8, '', '', 'LR' );

//Vazio
$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->addCabecalho            ( "" , 100, 5, '', '', 'T','');

//Linha ATRIBUTOS
$obPDF->addRecordSet            ( $arRecordSet[9] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( ""               ,  30, 5);
$obPDF->addCabecalho            ( ""               ,  70, 5);
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Nome"            , 7, 'B', '', 1 );
$obPDF->addCampo                ( "Valor"           , 8, '', '' , 1 );

//Vazio
$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->addCabecalho            ( "" , 6, 5);

//Linha5
$obPDF->addRecordSet            ( $arRecordSet[5] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "ITEM"                    , 10, 5, '', '', 'LTBR','205,206,205');
$obPDF->addCabecalho            ( "ESPECIFICAÇÃO"           , 54, 5, '', '', 'LTBR','205,206,205');
$obPDF->addCabecalho            ( "VALOR EMPENHADO"         , 12, 5, '', '', 'LTBR','205,206,205');
$obPDF->addCabecalho            ( "VALOR LIQUIDADO"         , 12, 5, '', '', 'LTBR','205,206,205');
$obPDF->addCabecalho            ( "VALOR LIQ. ANULADO" , 12, 5, '', '', 'LTBR','205,206,205');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "Item"                  , 8, '', '', 'LR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Especificacao"         , 8, '', '', 'LR');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "ValorEmpenhado"        , 8, '', '', 'LR');
$obPDF->addCampo                ( "ValorLiquidado"        , 8, '', '', 'LR');
$obPDF->addCampo                ( "ValorLiquidadoAnulado" , 8, '', '', 'LR');

//Linha6
$obPDF->addRecordSet            ( $arRecordSet[6] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "" , 76, 5, '', '', 'LT');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCabecalho            ( "" , 12, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "" , 12, 5, '', '', 'LTR','205,206,205');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "recurso"     , 8, '', '', 'LB');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "Total"       , 8, '', '', 'LBR');
$obPDF->addCampo                ( "ValorTotal"  , 8, '', '', 'LBR','205,206,205');

//Linha7
//$obPDF->addRecordSet            ( $arRecordSet[7] );
//$obPDF->setAlturaCabecalho      ( 5 );
//$obPDF->setQuebraPaginaLista    ( false );
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCabecalho            ( "" , 100, 5);
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCampo                ( "1" , 8, '', '', '');

//Linha8
$obPDF->addRecordSet            ( $arRecordSet[7] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "" , 10, 5, '', '', 'LT');
$obPDF->addCabecalho            ( "" , 80, 5, '', '', 'T');
$obPDF->addCabecalho            ( "" , 10, 5, '', '', 'TR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "" , 8, '', '', 'L');
$obPDF->addCampo                ( "1", 8, '', '', '');
$obPDF->addCampo                ( "" , 8, '', '', 'R');

//Vazio
$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->addCabecalho            ( "" , 100, 5, '', '', 'T');

////Linha9
//$obPDF->addRecordSet            ( $arRecordSet[8] );
//$obPDF->setAlturaCabecalho      ( 5 );
//$obPDF->setQuebraPaginaLista    ( false );
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCabecalho            ( ""               ,  30, 5);
//$obPDF->addCabecalho            ( ""               ,  70, 5);
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCampo                ( "Nome"            , 7, 'B', '', 1 );
//$obPDF->addCampo                ( "Valor"           , 8, '', '' , 1 );
//
//$obPDF->addRecordSet            ( $rsVazio );
//$obPDF->setAlturaCabecalho      ( 5 );
//$obPDF->setQuebraPaginaLista    ( false );
//$obPDF->addCabecalho            ( "" , 6, 5);

////Linha7
//$obPDF->addRecordSet            ( $arRecordSet[5] );
//$obPDF->setAlturaCabecalho      ( 5 );
//$obPDF->setQuebraPaginaLista    ( false );
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCabecalho            ( "ITEM"                ,  5, 5, 'B', '', 'LTRB','205,206,205');
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCabecalho            ( "QUANTIDADE"          ,  8, 5, 'B', '', 'LTRB','205,206,205');
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCabecalho            ( "UNIDADE"             ,  7, 5, 'B', '', 'LTRB','205,206,205');
//$obPDF->addCabecalho            ( "ESPECIFICAÇÃO"       , 50, 5, 'B', '', 'LTRB','205,206,205');
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCabecalho            ( "VALOR UNITÁRIO"      , 15, 5, 'B', '', 'LTRB','205,206,205');
//$obPDF->addCabecalho            ( "VALOR TOTAL"         , 15, 5, 'B', '', 'LTRB','205,206,205');
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCampo                ( "Item"            , 8, '', '', 'LTRB' );
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCampo                ( "Quantidade"      , 8, '', '', 'LTRB' );
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCampo                ( "Unidade"         , 8, '', '', 'LTRB' );
//$obPDF->addCampo                ( "Especificacao"   , 8, '', '', 'LTRB' );
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCampo                ( "ValorUnitario"   , 8, '', '', 'LTRB' );
//$obPDF->addCampo                ( "ValorTotal"      , 8, '', '', 'LTRB' );

////Linha8
//$obPDF->addRecordSet            ( $arRecordSet[7] );
//$obPDF->setAlturaCabecalho      ( 5 );
//$obPDF->setQuebraPaginaLista    ( false );
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCabecalho            ( "" , 85, 5, '', '', 'LTR');
//$obPDF->addCabecalho            ( "" , 15, 5, '', '', 'LTR','205,206,205');
//$obPDF->addCampo                ( "Recurso: [Recurso]"  , 8, '', '', 'LBR');
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCampo                ( "[Total]"             , 8, '', '', 'LBR','205,206,205');

$obPDF->show();
?>
