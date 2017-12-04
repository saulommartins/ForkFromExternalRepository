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

    $Id:#

*/

//include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/tabelas.inc.php"     );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/RRelatorio.class.php';
include_once '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/classes/negocio/RPatrimonioRelatorioCustomizavel.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Patrimonio" );
$obPDF->setSubTitulo            ( strtoupper($sessao->filtro[stTitulo]));
$obPDF->setTitulo            ( "Relatório customizavel Exercício: ".Sessao::getExercicio() );
$obPDF->setUsuario           ( $sessao->username );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );
$obPDF->addRecordSet( $sessao->transf5[0] );
$obPDF->addIndentacao  ( "nivel", "descricao", "  ");
$obPDF->addQuebraLinha ( "nivel", 1 );
$inTotalColunas = 41;

for ($icount=0;$icount <= $sessao->filtro[cont];$icount++) {
    if ($sessao->filtro[boAtributoDinamico.$icount]) {
        $inTotalColunas -= 7;
    }
}

if ($sessao->filtro[boPlaca]) {
   $inTotalColunas -= 7;
}

if ($sessao->filtro[boDataBaixa]) {
   $inTotalColunas -= 7;
}

if ($sessao->filtro[boAquisicao]) {
   $inTotalColunas -= 7;
}
if ($sessao->filtro[boEmpenho]) {
   $inTotalColunas -= 7;
}

if ($sessao->filtro[boValor]) {
   $inTotalColunas -= 7;
}

if ($sessao->filtro[boNotaFiscal]) {
   $inTotalColunas -= 7;
}
if ($sessao->filtro['codOrgao'] == 'xxx') {
    $inTotalColunas -= 18;
}
if ($sessao->filtro['codEntidade'] == 'xxx') {
    $inTotalColunas -= 18;
}

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ("CLASSIFICAÇÃO", 12, 10);
$obPDF->addCabecalho   ("CÓDIGO",6 , 10);
$obPDF->addCabecalho   ("DESCRIÇÃO",26+ceil($inTotalColunas/2) , 10);
if ($sessao->filtro['codOrgao'] == 'xxx') {
    $obPDF->addCabecalho   ("ORGÃO",26+ceil($inTotalColunas/2) , 10);
}
if ($sessao->filtro['codEntidade'] == 'xxx') {
    $obPDF->addCabecalho   ("ENTIDADE",26+ceil($inTotalColunas/2) , 10);
}

if ($sessao->filtro[boPlaca]) {
   $obPDF->addCabecalho   ("PLACA",9 , 10);
}

if ($sessao->filtro[boDataBaixa]) {
   $obPDF->addCabecalho   ("BAIXA",9, 10);
}

if ($sessao->filtro[boAquisicao]) {
   $obPDF->addCabecalho   ("AQUISICAO",9 , 10);
}
if ($sessao->filtro[boEmpenho]) {
   $obPDF->addCabecalho   ("EMPENHO",9 , 10);
}
if ($sessao->filtro[boNotaFiscal]) {
   $obPDF->addCabecalho   ("NF",9 , 10);
}
if ($sessao->filtro[boValor]) {
   $obPDF->setAlinhamento ( "R" );
   $obPDF->addCabecalho   ("VALOR",9 , 10);
}
setlocale ( LC_CTYPE , 'pt_BR' );
for ($icount=0;$icount <= $sessao->filtro[cont];$icount++) {
    if ($sessao->filtro[boAtributoDinamico.$icount]) {
       $obPDF->addCabecalho   (strtoupper($sessao->filtro[nom_atributo][$sessao->filtro[boAtributoDinamico.$icount]]), 10, 10);
    }
}

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("classificacao", 8 );
$obPDF->addCampo       ("cod_bem", 8 );
$obPDF->addCampo       ("descricao", 8 );
if ($sessao->filtro['codOrgao'] == 'xxx') {
    $obPDF->addCampo       ("nom_orgao", 8 );
}
if ($sessao->filtro['codEntidade'] == 'xxx') {
    $obPDF->addCampo       ("entidade", 8 );
}

if ($sessao->filtro[boPlaca]) {
   $obPDF->addCampo       ("numero_placa",8);
}
if ($sessao->filtro[boDataBaixa]) {
   $obPDF->addCampo       ("dt_baixa", 8 );
}
if ($sessao->filtro[boAquisicao]) {
   $obPDF->addCampo       ("dt_aquisicao", 8 );
}
if ($sessao->filtro[boEmpenho]) {
   $obPDF->addCampo   ("cod_empenho",8);
}
if ($sessao->filtro[boNotaFiscal]) {
   $obPDF->addCampo   ("nota_fiscal",8);
}
if ($sessao->filtro[boValor]) {
   $obPDF->setAlinhamento( "R" );
   $obPDF->addCampo   ("valor_empenho",8);
}

for ($icount=0;$icount <= $sessao->filtro[cont];$icount++) {
    if ($sessao->filtro[boAtributoDinamico.$icount]) {
      $obPDF->addCampo   ("valor_atributo".$sessao->filtro[boAtributoDinamico.$icount]."",8 );
   }
}
$obPDF->setAlinhamento ( "R" );
$obPDF->show();
?>
