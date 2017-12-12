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
* Classe de oculto para modelo 3 do modulo LRF
* Data de Criação: 24/05/2005

* @author Analista: Diego Barbosa
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Oculto

$Revision: 30668 $
$Name$
$Author: cako $
$Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

* Casos de uso: uc-02.05.05, uc-02.01.35
*/

/*
$Log$
Revision 1.7  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.6  2006/08/25 17:50:22  fernando
Bug #6773#

Revision 1.5  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_LRF_NEGOCIO."RLRFRelatorioModelos3.class.php"  );

$obRegra      = new RLRFRelatorioModelos3;
$obPDF        = new ListaPDF();

$obRegra->obRRelatorio->recuperaCabecalho ( $arConfiguracao          );
$obPDF->setModulo                ( "LRF - ".Sessao::getExercicio()   );
$obPDF->setTitulo                ( "Modelo " . $sessao->filtro['inCodModelo'] );

$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obROrcamentoEntidade->setVerificaConfiguracao  ( true );
$obRegra->obROrcamentoEntidade->obRCGM->consultar($rsCGM);

$subTitulo = "Período: de " . $sessao->transf4[1] . " até " . $sessao->transf4[2];
$obPDF->setSubTitulo             ( $subTitulo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsVazio = new RecordSet;
/*
$rsAux   =  $sessao->transf5[3];
$arAux   =  $rsAux->getElementos();

for ($inCount = 0;$inCount < count($arAux);$inCount++) {
     $rsAux =  "rsTotal".$inCount;
     $$rsAux = new RecordSet;
     $$rsAux->preenche(array($arAux[$inCount]));
}
*/
$obPDF->SetMargins(2,2,2);
$obPDF->recalculaDimensoes();

$obPDF->addRecordSet($rsVazio);
$obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8)
    $obPDF->addCabecalho("Modelo 3 - Demonstrativo da Disponibilidade de Caixa", 100, 12);

$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8)
    $obPDF->addCabecalho("LC Federal nº 101/2000, ART. 54 e alínea \"A\" do Inciso III do ART. 55", 100, 10);

if (!empty($sessao->transf5[1])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("I - Executivo/Legislativo e Indiretas Municipais", 73, 10);
    $obPDF->addCabecalho("", 27,10);
//
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("(Exceto RPPS - Portaria STN n 470, de 31-8-2004)", 73, 10);
    $obPDF->addCabecalho("Contábil", 9, 8);
    $obPDF->addCabecalho("     Adição/       Exclusão", 9, 8);
    $obPDF->addCabecalho("Ajustado", 9, 8);

    $obPDF->addRecordSet( $sessao->transf5[1] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addIndentacao("nivel","coluna1","    ");
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3",6 );
    $obPDF->addCampo("coluna4",6 );
    $obPDF->addCampo("coluna5",6 );

}

if (!empty( $sessao->transf5[2])) {
    $obPDF->addRecordSet(  $sessao->transf5[2]);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("", 73, 8);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
}

if (!empty($sessao->transf6[1])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("II - Regime Próprio de Previdência Social do Servidor - RPPS", 73, 10);
    $obPDF->addCabecalho("Contábil", 9, 8);
    $obPDF->addCabecalho("     Adição/       Exclusão", 9, 8);
    $obPDF->addCabecalho("Ajustado", 9, 8);

    $obPDF->addRecordSet( $sessao->transf6[1] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addIndentacao("nivel","coluna1","    ");
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3",6 );
    $obPDF->addCampo("coluna4",6 );
    $obPDF->addCampo("coluna5",6 );

}

if (!empty( $sessao->transf6[2])) {
    $obPDF->addRecordSet(  $sessao->transf6[2]);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("", 73, 8);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
}

$obPDF->show();
?>
