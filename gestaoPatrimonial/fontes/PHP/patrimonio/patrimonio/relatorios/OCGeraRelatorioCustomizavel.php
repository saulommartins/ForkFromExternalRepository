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
    * Pagina oculta para gerar relatorio
    * Data de Criação   : 27/09/2004

    * @author Vandré Miguel Ramos
    * Casos de uso: uc-03.01.09, uc-03.01.19
    * @ignore
*/

/*
$Log$
Revision 1.14  2007/06/26 14:57:21  bruce
Bug#9484#

Revision 1.13  2007/06/19 20:52:43  hboaventura
Bug#9422#

Revision 1.12  2007/06/18 19:59:41  hboaventura
Inclusão do campo nota fiscal

Revision 1.11  2007/05/29 18:26:29  hboaventura
Bug #9326#

Revision 1.10  2007/05/21 19:24:32  rodrigo_sr
Bug #8847#

Revision 1.9  2007/03/15 16:00:30  tonismar
bug #8733

Revision 1.8  2007/02/09 17:53:26  tonismar
bug #6946

Revision 1.7  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:11:28  diego

*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/RRelatorio.class.php';
include_once '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/classes/negocio/RPatrimonioRelatorioCustomizavel.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$arFiltro = Sessao::read('filtroRelatorio');

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo               ( "Patrimonio" );
$obPDF->setSubTitulo            ( strtoupper($arFiltro['stTitulo']));
$obPDF->setTitulo               ( "Relatório customizavel Exercício: ".Sessao::getExercicio() );
$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );
$obPDF->addRecordSet	          ( Sessao::read('recordset') );
$obPDF->addIndentacao           ( "nivel", "descricao", "  ");
$obPDF->addQuebraLinha          ( "nivel", 1 );

$inTotalColunas = 41;
for ($icount=0;$icount <= $arFiltro['cont'];$icount++) {
    if ($arFiltro['boAtributoDinamico'.$icount]) {
        $inTotalColunas -= 7;
    }
}

if ($arFiltro['boPlaca']) {
   $inTotalColunas -= 7;
}

if ($arFiltro['boDataBaixa']) {
   $inTotalColunas -= 7;
}

if ($arFiltro['boAquisicao']) {
   $inTotalColunas -= 7;
}
if ($arFiltro['boEmpenho']) {
   $inTotalColunas -= 7;
}

if ($arFiltro['boValor']) {
   $inTotalColunas -= 7;
}

if ($arFiltro['boNotaFiscal']) {
   $inTotalColunas -= 7;
}
if ($arFiltro['codOrgao'] == 'xxx') {
    $inTotalColunas -= 18;
}
if ($arFiltro['codEntidade'] == 'xxx') {
    $inTotalColunas -= 18;
}

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ("CLASSIFICAÇÃO", 10, 8);
$obPDF->addCabecalho   ("CÓDIGO",6 , 8);
$obPDF->addCabecalho   ("DESCRIÇÃO",26+ceil($inTotalColunas/2) , 8);
if ($arFiltro['codOrgao'] == 'xxx') {
    $obPDF->addCabecalho   ("ORGÃO",26+ceil($inTotalColunas/2) , 8);
}
if ($arFiltro['codEntidade'] == 'xxx') {
    $obPDF->addCabecalho   ("ENTIDADE",26+ceil($inTotalColunas/2) , 8);
}

if ($arFiltro[boPlaca]) {
   $obPDF->addCabecalho   ("PLACA",8 , 8);
}

if ($arFiltro[boDataBaixa]) {
   $obPDF->addCabecalho   ("BAIXA",8, 8);
}

if ($arFiltro[boAquisicao]) {
   $obPDF->addCabecalho   ("AQUISIÇÂO",8 , 8);
}
if ($arFiltro[boEmpenho]) {
   $obPDF->addCabecalho   ("EMPENHO",8 , 8);
}
if ($arFiltro[boNotaFiscal]) {
   $obPDF->addCabecalho   ("NF",8 , 8);
}
if ($arFiltro[boValor]) {
   $obPDF->setAlinhamento ( "R" );
   $obPDF->addCabecalho   ("VALOR",8 , 8);
}
setlocale ( LC_CTYPE , 'pt_BR' );
for ($icount=0;$icount <= $arFiltro[cont];$icount++) {
    if ($arFiltro[boAtributoDinamico.$icount]) {
       $obPDF->addCabecalho   (strtoupper($arFiltro[nom_atributo][$arFiltro[boAtributoDinamico.$icount]]), 15, 8);
    }
}

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("classificacao", 8 );
$obPDF->addCampo       ("cod_bem", 8 );
$obPDF->addCampo       ("descricao", 8 );
if ($arFiltro['codOrgao'] == 'xxx') {
    $obPDF->addCampo       ("nom_orgao", 8 );
}
if ($arFiltro['codEntidade'] == 'xxx') {
    $obPDF->addCampo       ("entidade", 8 );
}

if ($arFiltro[boPlaca]) {
   $obPDF->addCampo       ("numero_placa",8);
}
if ($arFiltro[boDataBaixa]) {
   $obPDF->addCampo       ("dt_baixa", 8 );
}
if ($arFiltro[boAquisicao]) {
   $obPDF->addCampo       ("dt_aquisicao", 8 );
}
if ($arFiltro[boEmpenho]) {
   $obPDF->addCampo   ("cod_empenho",8);
}
if ($arFiltro[boNotaFiscal]) {
   $obPDF->addCampo   ("nota_fiscal",8);
}
if ($arFiltro[boValor]) {
   $obPDF->setAlinhamento( "R" );
   $obPDF->addCampo   ("valor_empenho",8);
}

for ($icount=0;$icount <= $arFiltro['cont'];$icount++) {
    if ($arFiltro[boAtributoDinamico.$icount]) {
      $obPDF->addCampo   ("valor_atributo".$arFiltro[boAtributoDinamico.$icount]."",8 );
   }
}
$obPDF->setAlinhamento ( "R" );
$obPDF->show();
?>
