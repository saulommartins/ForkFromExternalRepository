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
 * Página de oculta de geração de relatório
 * Data de Criação   : 18/08/2005

 * @author Analista: Muriel Preuss
 * @author Desenvolvedor: Cleisson Barboza

 * @ignore

 * Casos de uso: uc-02.03.26

 $Id: OCGeraRelatorioProgramacaoPagamentos.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();
$rsVazio      = new RecordSet;
$rsEntidades  = new RecordSet;
$inCount	  = 0;

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');
$rsRecordSet0 = Sessao::read('rsRecordSet0');
$rsRecordSet1 = Sessao::read('rsRecordSet1');
$rsRecordSet2 = Sessao::read('rsRecordSet2');
$rsRecordSet3 = Sessao::read('rsRecordSet3');
$rsRecordSet4 = Sessao::read('rsRecordSet4');
$rsRecordSet5 = Sessao::read('rsRecordSet5');
$rsRecordSet6 = Sessao::read('rsRecordSet6');
$rsRecordSet7 = Sessao::read('rsRecordSet7');
$rsRecordSet8 = Sessao::read('rsRecordSet8');
$rsRecordSet9 = Sessao::read('rsRecordSet9');

// Adicionar logo nos relatorios
if (count($arFiltro['inCodEntidade']) == 1) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio      ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho ( $arConfiguracao );

if ($arFiltro['stDataInicial']) {
    $stPeriodo = $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'];
}

$obPDF->setSubTitulo          ( "Periodicidade do Vencimento: ".$stPeriodo  );
$obPDF->setUsuario            ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura ( $arConfiguracao );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arFiltroNom['entidade'][$inCodEntidade];
}

$obPDF->addFiltro ('Entidades Relacionadas: ', $arNomEntidade);

if ($arFiltro['stNomDespesa']) {
    $obPDF->addFiltro( 'Dotação: ', $arFiltro['stNomDespesa'] );
}

if ($arFiltro['stNomFornecedor']) {
    $obPDF->addFiltro( 'Credor: ',  $arFiltro['stNomFornecedor'] );
}

if ($arFiltro['inCodRecurso']) {
    $obPDF->addFiltro( 'Recurso: ',  $arFiltroNom['recurso'][$arFiltro['inCodRecurso']] );
}

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro( 'Periodicidade: ',  $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
}

$inCount = $inCount2 = 0;

if ($rsRecordSet0[0] instanceof RecordSet) {

    if (count($rsRecordSet0[0]->getElementos()) > 0) {

        foreach ($rsRecordSet0 as $data) {
            // DATA ATUAL
            $obPDF->addRecordSet ( $data );
            if ($inCount==0) {
                $obPDF->setQuebraPaginaLista( true );
            } else {
                $obPDF->setQuebraPaginaLista( false );
            }

            $obPDF->setAlturaCabecalho(0);
            $obPDF->setAlinhamento  ( "L"                );
            $obPDF->addCabecalho    ( "", 10,10          );
            $obPDF->addCabecalho    ( "", 90,8           );
            $obPDF->setAlinhamento  ( "L"                );
            $obPDF->addCampo        ( "coluna1", 10 ,"B" );
            $obPDF->addCampo        ( "coluna2", 8  ,"B" );

            $inCount2 = 0;
            foreach ($rsRecordSet1[$inCount] as $recurso) {

                // RECURSO ATUAL
                $obPDF->addRecordSet( $recurso                );
                $obPDF->setQuebraPaginaLista( false           );
                $obPDF->setAlturaCabecalho(0                  );
                $obPDF->setAlinhamento  ( "L"                 );
                $obPDF->addCabecalho    ( "", 10, 10          );
                $obPDF->addCabecalho    ( "", 90, 8           );
                $obPDF->setAlinhamento  ( "L"                 );
                $obPDF->addCampo        ( "coluna1", 10  ,"B" );
                $obPDF->addCampo        ( "coluna2", 8   ,"B" );

                // EMPENHOS
                $obPDF->addRecordSet( $rsRecordSet4[$inCount][$inCount2] );
                $obPDF->setQuebraPaginaLista( false );
                $obPDF->setAlturaCabecalho(7 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCabecalho("Empenho" , 10, 10,"B");
                $obPDF->addCabecalho("Credor", 70, 10,"B");
                $obPDF->setAlinhamento( "R" );
                $obPDF->addCabecalho("Valor", 20, 10,"B");
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCampo("coluna1", 8 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCampo("coluna2", 8 );
                $obPDF->setAlinhamento ( "R" );
                $obPDF->addCampo("coluna3", 8 );

                // TOTAL DAS DESPESAS PARA O RECURSO
                $obPDF->addRecordSet( $rsRecordSet3[$inCount][$inCount2] );
                $obPDF->setQuebraPaginaLista( false );
                $obPDF->setAlturaCabecalho(-2       );
                $obPDF->setAlinhamento ( "L"        );
                $obPDF->addCabecalho("", 80, 8      );
                $obPDF->setAlinhamento ( "R"        );
                $obPDF->addCabecalho("", 20, 8      );
                $obPDF->setAlinhamento ( "L"        );
                $obPDF->addCampo("coluna1", 10,"B"  );
                $obPDF->setAlinhamento ( "R"        );
                $obPDF->addCampo("coluna2", 8       );

                $inCount2++;
            }

           // TOTAL DAS DESPESAS NA DATA
           $obPDF->addRecordSet( $rsRecordSet2[$inCount] );
           $obPDF->setQuebraPaginaLista( false  );
           $obPDF->setAlturaCabecalho(5         );
           $obPDF->setAlinhamento ( "L"         );
           $obPDF->addCabecalho("", 80, 8       );
           $obPDF->setAlinhamento ( "R"         );
           $obPDF->addCabecalho("", 20, 8       );
           $obPDF->setAlinhamento ( "L"         );
           $obPDF->addCampo("coluna1", 10,"B"   );
           $obPDF->setAlinhamento ( "R"         );
           $obPDF->addCampo("coluna2", 8        );

           $inCount++;
        }

        // TOTAL DAS DESPESAS POR RECURSO - TITULO
        $obPDF->addRecordSet( $rsVazio      );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlturaCabecalho(20       );
        $obPDF->setAlinhamento ( "L"        );
        $obPDF->addCabecalho("TOTAL DAS DESPESAS POR RECURSO", 100,10,"B");

        // TOTAL DAS DESPESAS POR RECURSO - VALORES
        $obPDF->addRecordSet( $rsRecordSet5        );
        $obPDF->setQuebraPaginaLista( false             );
        $obPDF->setAlturaCabecalho(0                    );
        $obPDF->setAlinhamento ( "L"                    );
        $obPDF->addCabecalho("Código"    , 10, 10,"B"   );
        $obPDF->addCabecalho("Descrição" , 70, 10,"B"   );
        $obPDF->setAlinhamento ( "R"                    );
        $obPDF->addCabecalho("Valor"     , 20, 10,"B"   );
        $obPDF->setAlinhamento ( "L"                    );
        $obPDF->addCampo("coluna1", 8 );
        $obPDF->addCampo("coluna2", 8 );
        $obPDF->setAlinhamento ( "R"  );
        $obPDF->addCampo("coluna3", 8 );

        $inCount = 0;
        $recurso = "";

        // DISPONIBILIDADES FINANCEIRAS - TITULO
        $obPDF->addRecordSet( $rsVazio      );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlturaCabecalho(15       );
        $obPDF->setAlinhamento ( "L"        );
        $obPDF->addCabecalho("DISPONIBILIDADES FINANCEIRAS", 100,10,"B");

        if ($rsRecordSet8) {

            foreach ($rsRecordSet8 as $recurso) {
                // RECURSO ATUAL
                $obPDF->addRecordSet( $recurso );
                $obPDF->setQuebraPaginaLista( false );
                if ($inCount==0) {
                    $obPDF->setAlturaCabecalho(-5);
                } else {
                    $obPDF->setAlturaCabecalho(0);
                }
                $obPDF->setAlinhamento  ( "L" );
                $obPDF->addCabecalho    ( "", 10, 1 );
                $obPDF->addCabecalho    ( "", 90, 1 );
                $obPDF->setAlinhamento  ( "L" );
                $obPDF->addCampo        ( "coluna1", 10,"B" );
                $obPDF->addCampo        ( "coluna2", 8,"B" );

                // CONTAS
                $obPDF->addRecordSet( $rsRecordSet7[$inCount] );
                $obPDF->setQuebraPaginaLista( false );
                $obPDF->setAlturaCabecalho(10 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCabecalho("Red.Cta", 10, 10,"B" );
                $obPDF->addCabecalho("Nome da Conta", 60, 10,"B" );
                $obPDF->setAlinhamento( "R" );
                $obPDF->addCabecalho("Valor Disponível na Data"	, 30, 10,"B");
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCampo("coluna1", 8 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCampo("coluna2", 8 );
                $obPDF->setAlinhamento ( "R" );
                $obPDF->addCampo("coluna3", 8 );

                // DISPONIBILIDADE PARA O RECURSO ATUAL
                $obPDF->addRecordSet( $rsRecordSet6[$inCount] );
                $obPDF->setQuebraPaginaLista( false );
                $obPDF->setAlturaCabecalho(-2 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCabecalho("", 80, 8 );
                $obPDF->setAlinhamento ( "R" );
                $obPDF->addCabecalho("", 20, 8 );

                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCampo("coluna1", 10,"B" );
                $obPDF->setAlinhamento ( "R" );
                $obPDF->addCampo("coluna2", 8 );

                $inCount++;
            }
        }

        // TOTAIS
        $obPDF->addRecordSet( $rsRecordSet9 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlturaCabecalho(5 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("", 80, 8);
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho("", 20, 8);

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo("coluna1", 10,"B");
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna2", 8);

    }
}

$arAssinaturas = Sessao::read('assinaturas');

if (count($arAssinaturas['selecionadas']) > 0 ) {
    include_once CAM_FW_PDF."RAssinaturas.class.php";
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    $obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();

?>
