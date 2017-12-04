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
    * Data de Criação   : 07/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-08-24 14:25:31 -0300 (Qui, 24 Ago 2006) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.18
*/

/*
$Log$
Revision 1.11  2006/08/24 17:25:31  jose.eduardo
Bug #6763#

Revision 1.10  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF();
$rsVazio      = new RecordSet;

$arRecordSet = Sessao::read('arRecordSet');
$arFiltroRelatorio = Sessao::read('filtroRelatorio');
// Adicionar logo nos relatorios
if ( $arRecordSet[0]->getNumLinhas() == "1" ) {
    $obRRelatorio->setCodigoEntidade( $arRecordSet[0]->getCampo("cod_entidade") );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setAcao                 ( "Nota de Anulação de Empenho" );

if(Sessao::read('reemitir')!='t')
   $obPDF->setSubTitulo( "Empenho N. ".$arFiltroRelatorio['inCodEmpenho']."/".$arFiltroRelatorio['stDtExercicioEmpenho'] );
else
   $obPDF->setSubTitulo( "Empenho N. ".$arFiltroRelatorio['inCodEmpenho']."/".$arFiltroRelatorio['stDtExercicioEmpenho']."  REEMISSÃO" );

$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );

$stData = SistemaLegado::dataToBr( substr($arFiltroRelatorio['timestamp'],0,10) );
$obPDF->setData                 ( $stData );
$obPDF->stHora                  = substr($arFiltroRelatorio['timestamp'],11,8);

//echo "<pre>";
//print_r ( sessao->transf );
//echo "</pre>";
//exit(0);

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
$obPDF->addCabecalho            ( "ÓRGÃO"     , 47, 5, '', '', 'LT');
$obPDF->addCabecalho            ( "UNIDADE"   , 38, 5, '', '', 'RT');
$obPDF->addCabecalho            ( "TIPO"      , 15, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Orgao"   , 8, '', '', 'L');
$obPDF->addCampo                ( "Unidade" , 8, '', '', 'R');
$obPDF->addCampo                ( "Tipo"    , 8, '', '', 'LR');

//Linha2
$obPDF->addRecordSet            ( $arRecordSet[2] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "DOTAÇÃO"   , 85, 5, '', '', 'L');
$obPDF->addCabecalho            ( ""   , 15, 5, '', '', 'LR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Dotacao", 8, '', '', 'LDR');
$obPDF->addCampo                ( "", 8, '', '', 'LDR');

//Linha3
$obPDF->addRecordSet            ( $arRecordSet[3] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
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

//Linha4
$obPDF->addRecordSet            ( $arRecordSet[4] );
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

//Linha5
$obPDF->addRecordSet            ( $arRecordSet[5] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "AUTORIZAÇÃO"         , 33, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "DATA DE EMISSÃO"     , 33, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "DATA DE VENCIMENTO"  , 34, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Autorizacao", 8, '', '', 'LBR');
$obPDF->addCampo                ( "Emissao"    , 8, '', '', 'LBR');
$obPDF->addCampo                ( "Vencimento" , 8, '', '', 'LBR');

//Linha6
$obPDF->addRecordSet            ( $arRecordSet[6] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCabecalho            ( "VALOR ORÇADO"        , 25, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "SALDO ANTERIOR"      , 25, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "VALOR ANULADO"    , 25, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "SALDO ATUAL"         , 25, 5, '', '', 'LTR');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "ValorOrcado"     , 8, '', '', 'LBR');
$obPDF->addCampo                ( "SaldoAnterior"   , 8, '', '', 'LBR');
$obPDF->addCampo                ( "ValorEmpenho"    , 8, '', '', 'LBR');
$obPDF->addCampo                ( "SaldoAtual"      , 8, '', '', 'LBR');

$obPDF->addRecordSet            ( $arRecordSet[10] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "Histórico"       ,  100, 5, 'B', '', 'LTR','');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Historico"       , 8, '', '', 'LRB' );

$obPDF->addRecordSet            ( $arRecordSet[11] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "DESCRIÇÃO DO EMPENHO"   ,  100, 5, 'B', '', 'LTR','');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "1"       , 8, '', '', 'LRB' );

//Linha9
$obPDF->addRecordSet            ( $arRecordSet[9] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( ""               ,  30, 5);
$obPDF->addCabecalho            ( ""               ,  70, 5);
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "Nome"            , 7, 'B', '', 1 );
$obPDF->addCampo                ( "Valor"           , 8, '', '' , 1 );

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->addCabecalho            ( "" , 6, 5);

//Linha7
$obPDF->addRecordSet            ( $arRecordSet[7] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCabecalho            ( "ITEM"                ,  5, 5, 'B', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCabecalho            ( "QUANTIDADE"          ,  8, 5, 'B', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "UNIDADE"             ,  7, 5, 'B', '', 'LTRB','205,206,205');
$obPDF->addCabecalho            ( "ESPECIFICAÇÃO"       , 50, 5, 'B', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCabecalho            ( "VALOR EMPENHADO"     , 15, 5, 'B', '', 'LTRB','205,206,205');
$obPDF->addCabecalho            ( "VALOR ANULADO"       , 15, 5, 'B', '', 'LTRB','205,206,205');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "Item"            , 8, '', '', 'LTRB' );
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "Quantidade"      , 8, '', '', 'LTRB' );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "simbolo"         , 8, '', '', 'LTRB' );
$obPDF->addCampo                ( "Especificacao"   , 8, '', '', 'LTRB' );
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "ValorTotal"      , 8, '', '', 'LTRB' );
$obPDF->addCampo                ( "ValorAnulado"    , 8, '', '', 'LTRB' );

//Linha8
$obPDF->addRecordSet            ( $arRecordSet[8] );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "" , 85, 5, '', '', 'LTR');
$obPDF->addCabecalho            ( "" , 15, 5, '', '', 'LTR','205,206,205');
$obPDF->addCampo                ( "Recurso: [Recurso]"  , 8, '', '', 'LBR');
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "[Total]"             , 8, '', '', 'LBR','205,206,205');

for ( $inCount = 12; $inCount < sizeof($arRecordSet); $inCount++ ) {
    $obPDF->addRecordSet            ( $arRecordSet[$inCount] );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlinhamento          ( "L" );
    $obPDF->addCabecalho            ( "" , 100, 5, '', '', 'TB');
    $obPDF->addCampo                ( "stDtAnulado"  , 8, '', '', 'LTR');
    $inCount++;
    $obPDF->addRecordSet            ( $arRecordSet[$inCount] );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlinhamento          ( "L" );
    $obPDF->addCabecalho            ( "Motivo" , 100, 10, '', '', 'LTR');
    $obPDF->addCampo                ( "stMotivo"  , 8, '', '', 'LR');
}

$obPDF->addRecordSet            ( new RecordSet );
$obPDF->setAlturaCabecalho      ( 5 );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "" , 100, 5, '', '', 'T');
$obPDF->addCampo                ( ""  , 8, '', '', '');

$obPDF->show();
?>
