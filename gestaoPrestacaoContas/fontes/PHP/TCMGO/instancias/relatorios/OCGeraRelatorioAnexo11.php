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
    * Pagina oculta para gerar relatorio de Demonstrativo de Despesa - Anexo 11
    * Data de Criação: 19/02/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    $Id:$

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../config.php';
include_once CAM_FW_PDF.'RRelatorio.class.php';

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF('L');
$rsVazio      = new RecordSet;

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');

// Adicionar logo nos relatorios
if ( count( $arFiltroRelatorio['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltroRelatorio['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  (Sessao::getExercicio());
$obRRelatorio->recuperaCabecalho($arConfiguracao);
$obPDF->setModulo            ('Relatorio');
$obPDF->setAcao              ('Anexo 11 - Comparativo da Despesa Autorizada com a Realizada');
$obPDF->setSubTitulo         ("Período: ".$arFiltroRelatorio['stDataInicial']." até ".$arFiltroRelatorio['stDataFinal']);
$obPDF->setUsuario           (Sessao::getUsername());
$obPDF->setEnderecoPrefeitura($arConfiguracao);

$obPDF->addRecordSet($rsRecordSet);
$obPDF->addIndentacao('nivel', 'descricao', '   ');
//$obPDF->addIndentacao  ( "nivel", "cod_estrutural" , "   " );

$obPDF->setAlinhamento('R');
$obPDF->addCabecalho  ('CONTAS/CÓDIGO'                           , 15, 10 );
$obPDF->setAlinhamento('L');
$obPDF->addCabecalho  ('CATEGORIA ECONÔMICA'                     , 30, 10 );
$obPDF->setAlinhamento('R' );
$obPDF->addCabecalho  ('CRÉDITOS ORÇAMENTÁRIOS E SUPLEMENTAÇÕES' , 14, 10 );
$obPDF->addCabecalho  ('CRÉDITOS ESPECIAIS E EXTRAORDINÁRIOS'    , 14, 10 );
$obPDF->addCabecalho  ('TOTAL'                                   , 9, 10 );
$obPDF->addCabecalho  ('REALIZADO'                               , 9, 10 );
$obPDF->addCabecalho  ('DIFERENÇA'                               , 9, 10 );

$obPDF->setAlinhamento('R');
$obPDF->addCampo      ('cod_estrutural'          , 6 );
$obPDF->setAlinhamento('L');
$obPDF->addCampo      ('descricao'               , 6 );
$obPDF->setAlinhamento('R');
$obPDF->addCampo      ('vl_credito_orcamentario' , 6 );
$obPDF->addCampo      ('vl_credito_especial'     , 6 );
$obPDF->addCampo      ('vl_total'                , 6 );
$obPDF->addCampo      ('vl_realizado'            , 6 );
$obPDF->addCampo      ('vl_diferenca'            , 6 );

$obPDF->show();
?>
