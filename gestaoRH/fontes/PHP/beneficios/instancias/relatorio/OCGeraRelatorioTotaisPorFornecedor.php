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
* Página de relatório de Fornecedor
* Data de Criação   : 23/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30922 $
$Name$
$Author: tiago $
$Date: 2007-06-26 17:19:23 -0300 (Ter, 26 Jun 2007) $

* Casos de uso: uc-04.06.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                       );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php"                                );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"                                      );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php"                                   );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php"               );

//!-----------------------------------------------------------------
// @function    imprimeDadosFornecedor
// @desc        Insere no PDF os dados do fornecedor
// @access      private
// @param       rsFornecedores RecordSet RecordSet com dados do fornecedor
// @param       obPDF ListaPDF Objeto PDF no qual serão impressos os dados
// @return      void
// @note        Esta função tem o objetivo de reutilizar código,
//              pois esta rotina é executada em todos os casos
//              neste relatório.
//!-----------------------------------------------------------------
function imprimeDadosFornecedor(&$rsFornecedores,&$obPDF)
{
    $rsVazio = new RecordSet;

    while (!$rsFornecedores->eof()) {
        $rsFornecedor = new RecordSet;
        $rsFornecedor->preenche( $rsFornecedores->getCampo('fornecedor') );

        $obPDF->addRecordSet         ( $rsFornecedor );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( -3            );
        $obPDF->setAlturaLinha       ( 5             );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "campo1",  10 );
        $obPDF->setAlinhamento       ( "L"           );
        $obPDF->addCampo             ( "campo2",  8  );

        $obPDF->addRecordSet         ( $rsVazio      );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( 2             );
        $obPDF->addCabecalho         ( "",     10, 10);

        $rsConcessao = new RecordSet;
        $rsConcessao->preenche( $rsFornecedores->getCampo('dados') );
        $rsConcessao->addFormatacao("valor_unitario","NUMERIC_BR");
        $rsConcessao->addFormatacao("valor_total"   ,"NUMERIC_BR");

        $obPDF->addRecordSet         ( $rsConcessao           );
        $obPDF->setQuebraPaginaLista ( false                  );
        $obPDF->setAlturaCabecalho   ( 1                      );
        $obPDF->setAlturaLinha       ( 5                      );
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCabecalho         ("Mês"           ,  6, 9);
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCabecalho         ("Exercicio"     , 10, 9);
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCabecalho         ("Origem/Destino", 45, 9);
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCabecalho         ("Quantidade"    , 12, 9);
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCabecalho         ("Valor Unitário", 13, 9);
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCabecalho         ("Valor Total"   , 12, 9);

        $obPDF->setAlinhamento       ( "C"                    );
        $obPDF->addCampo             ("mes"           , 8     );
        $obPDF->setAlinhamento       ( "C"                    );
        $obPDF->addCampo             ("exercicio"     , 8     );
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCampo             ("itinerario"    , 8     );
        $obPDF->setAlinhamento       ( "R"                    );
        $obPDF->addCampo             ("quantidade"    , 8     );
        $obPDF->setAlinhamento       ( "R"                    );
        $obPDF->addCampo             ("valor_unitario", 8     );
        $obPDF->setAlinhamento       ( "R"                    );
        $obPDF->addCampo             ("valor_total"   , 8     );

        $rsTotais = new RecordSet;
        $rsTotais->preenche($rsFornecedores->getCampo('totais_fornecedor'));
        $rsTotais->addFormatacao("campo2" ,"NUMERIC_BR");

        $obPDF->addRecordSet         ( $rsTotais     );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( 5             );
        $obPDF->setAlturaLinha       ( 5             );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCabecalho         ( "",     40, 10);
        $obPDF->addCabecalho         ( "",     30, 10);
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "",        8  );
        $obPDF->setAlinhamento       ( "L"           );
        $obPDF->addCampo             ( "campo1",  10 );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "campo2",  8  );

        $obPDF->addRecordSet         ( $rsVazio      );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( 3             );
        $obPDF->addCabecalho         ( "",     10, 10);

        $rsFornecedores->proximo();
    }

        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( -3            );
        $obPDF->setAlturaLinha       ( 5             );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "campo1",  10 );
        $obPDF->setAlinhamento       ( "L"           );
        $obPDF->addCampo             ( "campo2",  8  );

} //end function

//Instanciação de Objetos
$obRRelatorio                    = new RRelatorio;
$obRRelatorio->setCodigoEntidade   ( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio() );

$obPDF                                    = new ListaPDF;
$obRBeneficioValeTransporte               = new RBeneficioValeTransporte;
$obTOrganogramaOrgao                      = new TOrganogramaOrgao;
$obROrganogramaLocal                      = new ROrganogramaLocal;
$rsRelatorioTotaisPorFornecedor           = Sessao::read('rsRelatorioTotaisPorFornecedor');
$obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo             ( "Relatório"                          );
$obPDF->setTitulo             ( "Relatório de Totais por Fornecedor" );
$obPDF->setSubTitulo          ( ""                                   );
$obPDF->setUsuario            ( Sessao::getUsername()                );
$obPDF->setEnderecoPrefeitura ( $arConfiguracao                      );

//Buscando nomes dos fornecedores para exibição no filtro
$obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporte($rsFornecedor);
$arFornecedor = array();
while (!$rsFornecedor->eof()) {
    $arFornecedor[$rsFornecedor->getCampo('numcgm')] = $rsFornecedor->getCampo('nom_cgm');
    $rsFornecedor->proximo();
}

//Buscando nome dos itinerários para exibição no filtro
$obRBeneficioValeTransporte->obRBeneficioItinerario->listarItinerario($rsItinerario);
$arItinerario = array();
while (!$rsItinerario->eof()) {
    $arItinerario[$rsItinerario->getCampo('vale_transporte_cod_vale_transporte')] = $rsItinerario->getCampo('municipio_origem')."/".trim($rsItinerario->getCampo('linha_origem'))." - ".$rsItinerario->getCampo('municipio_destino')."/".trim($rsItinerario->getCampo('linha_destino'));
    $rsItinerario->proximo();
}

//Buscando nomes das lotações para exibição no filtro
$obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);
$stFiltro = " AND orgao_nivel.cod_organograma = ".$rsOrganogramaVigente->getCampo('cod_organograma');

$obTOrganogramaOrgao->setDado('vigencia', $rsOrganogramaVigente->getCampo('dt_final'));
$obTOrganogramaOrgao->recuperaOrgaos( $rsLotacao, $stFiltro, ' ORDER BY cod_estrutural ');

$arLotacao = array();
while (!$rsLotacao->eof()) {
    $arLotacao[$rsLotacao->getCampo('cod_orgao')] = $rsLotacao->getCampo('descricao');
    $rsLotacao->proximo();
}

//Buscando nome dos locais para exibição no filtro
$obROrganogramaLocal->listarLocal( $rsLocal );
$arLocal = array();
while (!$rsLocal->eof()) {
    $arLocal[$rsLocal->getCampo('cod_local')] = $rsLocal->getCampo('descricao');
    $rsLocal->proximo();
}

//Adição de Filtros
$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');
$arTmp = array();
foreach ($arSessaoFiltroRelatorio['FornecedorSelecionados'] as $inCgmFornecedor) {
    $arTmp[] = $arFornecedor[$inCgmFornecedor];
}
if (count($arTmp))
    $obPDF->addFiltro('Fornecedor',$arTmp);

$arTmp = array();
foreach ($arSessaoFiltroRelatorio['ItinerarioSelecionados'] as $inCodValeTransporte) {
    $arTmp[] = $arItinerario[$inCodValeTransporte];
}
if (count($arTmp))
    $obPDF->addFiltro('Itinerário',$arTmp);

$arTmp = array();
if ($arSessaoFiltroRelatorio['inCodLotacaoSelecionados']) {
    foreach ($arSessaoFiltroRelatorio['inCodLotacaoSelecionados'] as $stVal) {
        $arVal = explode("-",$stVal);
        $inCodOrgao = $arVal[0];
        $arTmp[] = $arLotacao[$inCodOrgao];
    }
    if (count($arTmp))
        $obPDF->addFiltro('Lotação',$arTmp);
}

$arTmp = array();
if ($arSessaoFiltroRelatorio['inCodLocalSelecionados']) {
    foreach ($arSessaoFiltroRelatorio['inCodLocalSelecionados'] as $inCodLocal) {
        $arTmp[] = $arLocal[$inCodLocal];
    }
    if (count($arTmp))
        $obPDF->addFiltro('Local',$arTmp);
}

if ($arSessaoFiltroRelatorio['stAgruparPorLotacao'] && $arSessaoFiltroRelatorio['stAgruparPorLocal'])
    $obPDF->addFiltro('Agrupado' , array('Lotação','Local'));
elseif ($arSessaoFiltroRelatorio['stAgruparPorLotacao'])
    $obPDF->addFiltro('Agrupado' , 'Lotacao');
elseif ($arSessaoFiltroRelatorio['stAgruparPorLocal'])
    $obPDF->addFiltro('Agrupado' , 'Local');

$obPDF->addFiltro('Vigência' , $arSessaoFiltroRelatorio['dtVigencia']);

//Inclusão de dados no PDF
//Caso agrupado por Lotação e Local
if ($rsRelatorioTotaisPorFornecedor->getCampo('lotacao') && $rsRelatorioTotaisPorFornecedor->getCampo('locais')) {

    while (!$rsRelatorioTotaisPorFornecedor->eof()) {
        $rsLotacao = new RecordSet;
        $rsLotacao->preenche($rsRelatorioTotaisPorFornecedor->getCampo('lotacao'));

        $obPDF->addRecordSet       ( $rsLotacao      );
        $obPDF->setAlturaCabecalho ( 1               );
        $obPDF->setAlturaLinha     ( 5               );
        $obPDF->setAlinhamento     ( "R"             );
        $obPDF->addCabecalho       ( ""      , 10, 0 );
        $obPDF->addCabecalho       ( ""      , 80, 0 );
        $obPDF->setAlinhamento     ( "R"             );
        $obPDF->addCampo           ( "campo1", 10    );
        $obPDF->setAlinhamento     ( "L"             );
        $obPDF->addCampo           ( "campo2",  8    );

        $rsLocais = new RecordSet;
        $rsLocais->preenche($rsRelatorioTotaisPorFornecedor->getCampo('locais'));

        while (!$rsLocais->eof()) {
            $rsLocal = new RecordSet;
            $rsLocal->preenche($rsLocais->getCampo('local'));

            $obPDF->addRecordSet         ( $rsLocal      );
            $obPDF->setQuebraPaginaLista ( false         );
            $obPDF->setAlturaCabecalho   ( -3            );
            $obPDF->setAlturaLinha       ( 5             );
            $obPDF->setAlinhamento       ( "R"           );
            $obPDF->addCabecalho         ( "",     10, 0 );
            $obPDF->addCabecalho         ( "",     70, 0 );
            $obPDF->setAlinhamento       ( "R"           );
            $obPDF->addCampo             ( "campo1",  10 );
            $obPDF->setAlinhamento       ( "L"           );
            $obPDF->addCampo             ( "campo2",  8  );

            $rsFornecedores = new RecordSet;
            $rsFornecedores->preenche($rsLocais->getCampo('fornecedores'));

            imprimeDadosFornecedor($rsFornecedores,$obPDF);

            $rsTotalLocal = new RecordSet;
            $rsTotalLocal->preenche($rsLocais->getCampo('total_local'));
            $rsTotalLocal->addFormatacao("campo2","NUMERIC_BR");

            $obPDF->addRecordSet         ( $rsTotalLocal  );
            $obPDF->setQuebraPaginaLista ( false          );
            $obPDF->setAlturaCabecalho   ( 5              );
            $obPDF->setAlturaLinha       ( 1              );
            $obPDF->setAlinhamento       ( "R"            );
            $obPDF->addCabecalho         ( "",     40, 10 );
            $obPDF->addCabecalho         ( "",     30, 10 );
            $obPDF->addCabecalho         ( "",     10, 10 );
            $obPDF->setAlinhamento       ( "R"            );
            $obPDF->addCampo             ( "",        8   );
            $obPDF->setAlinhamento       ( "L"            );
            $obPDF->addCampo             ( "campo1",  10  );
            $obPDF->setAlinhamento       ( "R"            );
            $obPDF->addCampo             ( "campo2",  8   );

            $rsLocais->proximo();
        }

        $rsTotalLotacao = new RecordSet;
        $rsTotalLotacao->preenche($rsRelatorioTotaisPorFornecedor->getCampo('total_lotacao'));
        $rsTotalLotacao->addFormatacao("campo2","NUMERIC_BR");

        $obPDF->addRecordSet         ( $rsTotalLotacao);
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 1              );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCabecalho         ( "",     40, 10 );
        $obPDF->addCabecalho         ( "",     30, 10 );
        $obPDF->addCabecalho         ( "",     10, 10 );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCampo             ( "",        8   );
        $obPDF->setAlinhamento       ( "L"            );
        $obPDF->addCampo             ( "campo1",  10  );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCampo             ( "campo2",  8   );

        $rsRelatorioTotaisPorFornecedor->proximo();
    }

//Caso agrupado por Lotação
} elseif ($rsRelatorioTotaisPorFornecedor->getCampo('lotacao')) {

    while (!$rsRelatorioTotaisPorFornecedor->eof()) {

        $rsLotacao = new RecordSet;
        $rsLotacao->preenche($rsRelatorioTotaisPorFornecedor->getCampo('lotacao'));

        $obPDF->addRecordSet    ( $rsLotacao    );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1",  10 );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2",  8  );

        $rsFornecedores = new RecordSet;
        $rsFornecedores->preenche($rsRelatorioTotaisPorFornecedor->getCampo('fornecedores'));

        imprimeDadosFornecedor($rsFornecedores,$obPDF);

        $rsTotalLotacao = new RecordSet;
        $rsTotalLotacao->preenche($rsRelatorioTotaisPorFornecedor->getCampo('total_lotacao'));
        $rsTotalLotacao->addFormatacao("campo2","NUMERIC_BR");

        $obPDF->addRecordSet         ( $rsTotalLotacao);
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 5              );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCabecalho         ( "",     40, 10 );
        $obPDF->addCabecalho         ( "",     30, 10 );
        $obPDF->addCabecalho         ( "",     10, 10 );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCampo             ( "",        8   );
        $obPDF->setAlinhamento       ( "L"            );
        $obPDF->addCampo             ( "campo1",  10  );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCampo             ( "campo2",  8   );

        $rsRelatorioTotaisPorFornecedor->proximo();
    }

//Caso agrupado por Local
} elseif ($rsRelatorioTotaisPorFornecedor->getCampo('local')) {

    while (!$rsRelatorioTotaisPorFornecedor->eof()) {

        $rsLocal = new RecordSet;
        $rsLocal->preenche($rsRelatorioTotaisPorFornecedor->getCampo('local'));

        $obPDF->addRecordSet    ( $rsLocal      );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1",  10 );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2",  8  );

        $rsFornecedores = new RecordSet;
        $rsFornecedores->preenche($rsRelatorioTotaisPorFornecedor->getCampo('fornecedores'));

        imprimeDadosFornecedor($rsFornecedores,$obPDF);

        $rsTotalLocal = new RecordSet;
        $rsTotalLocal->preenche($rsRelatorioTotaisPorFornecedor->getCampo('total_local'));
        $rsTotalLocal->addFormatacao("campo2","NUMERIC_BR");

        $obPDF->addRecordSet    ( $rsTotalLocal );
        $obPDF->setQuebraPaginaLista ( false );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCabecalho    ( "",     40, 10);
        $obPDF->addCabecalho    ( "",     30, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "",        8  );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo1",  10 );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo2",  8  );

        $rsRelatorioTotaisPorFornecedor->proximo();
    }

//Caso agrupado apenas por fornecedor
} else {
    $obPDF->addRecordSet         (new RecordSet);
    $obPDF->addCabecalho         ( "", 20, 10  );
    imprimeDadosFornecedor($rsRelatorioTotaisPorFornecedor,$obPDF);
}

$obPDF->show();
?>
