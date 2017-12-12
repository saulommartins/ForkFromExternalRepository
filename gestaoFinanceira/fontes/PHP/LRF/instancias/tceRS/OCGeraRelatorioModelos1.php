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
* Oculto para modelo 1 do modulo LRF
* Data de Criação: 25/05/2005

* @author Analista: Diego Barbosa
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Oculto

$Revision: 30668 $
$Name$
$Author: cako $
$Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

* Casos de uso: uc-02.05.03, uc-02.01.35
*/

/*
$Log$
Revision 1.8  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.7  2006/08/25 17:50:22  fernando
Bug #6773#

Revision 1.6  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_LRF_NEGOCIO."RLRFRelatorioModelos1.class.php"  );

$obRegra      = new RLRFRelatorioModelos1;
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

$obPDF->SetMargins(2,2,2);
$obPDF->recalculaDimensoes();
$obPDF->addRecordSet($rsVazio);
$obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8 )
    $obPDF->addCabecalho("Modelo 1 - Demonstrativo da Receita Corrente Líquida", 100, 12);
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8 )
    $obPDF->addCabecalho("LC Federal nº 101/2000 - Inciso I do Art. 53", 100, 12);

$rsAux   =  $sessao->transf5[11];
$arAux   =  $rsAux->getElementos();

for ($inCount = 0;$inCount < count($arAux);$inCount++) {
     $rsAux =  "rsTotal".$inCount;
     $$rsAux = new RecordSet;
     $$rsAux->preenche(array($arAux[$inCount]));
}

if (!empty($sessao->transf5[1])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Discriminação", 73, 10);
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
    $obPDF->addCampo("coluna1", 6);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3",6);
    $obPDF->addCampo("coluna4",6);
    $obPDF->addCampo("coluna5",6);
}

if (!empty($sessao->transf5[2])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("II - DEDUÇÕES", 73, 10);
    $obPDF->addCabecalho("Contábil", 9, 8);
    $obPDF->addCabecalho("     Adição/       Exclusão", 9, 8);
    $obPDF->addCabecalho("Ajustado", 9, 8);

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("IRRF sobre Rendimento do Trabalho", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[2] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($sessao->transf5[3])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Cancelamento de Restos a Pagar", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[3] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($sessao->transf5[4])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Deduções de Receitas para a Formatação do FUNDEF", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[4] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($sessao->transf5[5])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Contribuições dos Servidores Ativos/Inativos/Pensionistas", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[5] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($sessao->transf5[6])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( true );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Compensação Previdenciária do RPPS", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[6] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($sessao->transf5[7])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Receitas do RPPS - Aplicações em Títulos, Remunerações e Outras Receitas", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[7] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6);
}

if (!empty($sessao->transf5[8])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Receitas do Fundo de Assistência Social dos Servidores", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[8] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($sessao->transf5[9])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Receitas do Fundo de Assistência à Saúde dos Servidores", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[9] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($sessao->transf5[10])) {
    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Outras Constribuições Sociais", 100, 10);

    $obPDF->addRecordSet( $sessao->transf5[10] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 58, 6);
    $obPDF->addCabecalho("", 15, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);
    $obPDF->addCabecalho("", 9, 6);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 6 );
    $obPDF->addCampo("coluna4", 6 );
    $obPDF->addCampo("coluna5", 6 );
}

if (!empty($rsTotal0)) {
    $obPDF->addRecordSet( $rsTotal0);
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
