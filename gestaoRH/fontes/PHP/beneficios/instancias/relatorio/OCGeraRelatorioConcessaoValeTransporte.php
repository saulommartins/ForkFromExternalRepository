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
* Página de relatório de Concessao de Vale Transporte
* Data de Criação: 12/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30925 $
$Name$
$Author: tiago $
$Date: 2007-06-26 17:19:23 -0300 (Ter, 26 Jun 2007) $

* Casos de uso: uc-04.06.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"                                   );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php"                                   );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioGrupoConcessao.class.php"                             );

$rsRelatorio         = Sessao::read('transf5');
$obRRelatorio        = new RRelatorio;
$obRRelatorio->setCodigoEntidade   ( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio()        );

$obPDF               = new ListaPDF();
$obROrganogramaOrgao = new ROrganogramaOrgao;
$obROrganogramaLocal = new ROrganogramaLocal;
$obRBeneficioGrupoConcessao = new RBeneficioGrupoConcessao;

$obRRelatorio->setExercicio     ( Sessao::getExercicio()                          );
$obRRelatorio->recuperaCabecalho( $arConfiguracao                             );
$obPDF->setModulo               ( "Relatório"                                 );
$obPDF->setTitulo               ( "Relatório de Concessão de Vale-Transporte" );
$obPDF->setSubTitulo            ( ""                                          );
$obPDF->setUsuario              ( Sessao::getUsername()                           );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao                             );

//Buscando nomes das lotações para exibição no filtro
$obROrganogramaOrgao->listarUltimaCriacao( $rsLotacao );
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

//Buscando nome dos grupos para exibição no filtro
$obRBeneficioGrupoConcessao->listarGrupoConcessao($rsGrupo);
$arGrupo = array();
while (!$rsGrupo->eof()) {
    $arGrupo[$rsGrupo->getCampo('cod_grupo')] = $rsGrupo->getCampo('descricao');
    $rsGrupo->proximo();
}

//Adição de Filtros
$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');
$inNumCGM = $arSessaoFiltroRelatorio['inNumCGM'];
$stNomCGM = $arSessaoFiltroRelatorio['inCampoInner'];

if (($inNumCGM) && ($stNomCGM))
    $obPDF->addFiltro('CGM ', $inNumCGM." - ".$stNomCGM );

$inCodGrupo = $arSessaoFiltroRelatorio['inCodGrupo'];
if ($inCodGrupo)
    $obPDF->addFiltro('Grupo ', $arGrupo[$inCodGrupo] );

$inContrato = $arSessaoFiltroRelatorio['inContrato'];
if ($inContrato)
    $obPDF->addFiltro('Matrícula ', $inContrato );

$arTmp = array();
if (is_array($arSessaoFiltroRelatorio['inCodLotacaoSelecionados'])) {
    foreach ($arSessaoFiltroRelatorio['inCodLotacaoSelecionados'] as $stVal) {
        $arVal = explode("-",$stVal);
        $inCodOrgao = $arVal[0];
        $arTmp[] = $arLotacao[$inCodOrgao];
    }
    if (count($arTmp))
        $obPDF->addFiltro('Lotação ',$arTmp);
}

$arTmp = array();
if (is_array($arSessaoFiltroRelatorio['inCodLocalSelecionados'])) {
    foreach ($arSessaoFiltroRelatorio['inCodLocalSelecionados'] as $inCodLocal) {
        $arTmp[] = $arLocal[$inCodLocal];
    }
    if (count($arTmp))
        $obPDF->addFiltro('Local ',$arTmp);
}

switch ($arSessaoFiltroRelatorio['inPeriodicidade']) {
    case 1: $obPDF->addFiltro('Periodicidade ' , $arSessaoFiltroRelatorio['stDataInicial']);
    break;
    case 2: $obPDF->addFiltro('Periodicidade ' , $arSessaoFiltroRelatorio['stMes']."/".$arSessaoFiltroRelatorio['stAnoMes']);
    break;
    case 3: $obPDF->addFiltro('Periodicidade ' , $arSessaoFiltroRelatorio['stAno']);
    break;
    case 4: $obPDF->addFiltro('Periodicidade ' , $arSessaoFiltroRelatorio['stDataInicial']." a ".$arSessaoFiltroRelatorio['stDataFinal']);
    break;
}

//Gerando relatorio de grupos
if ($rsRelatorio->getCampo('dadosGrupo') != "") {
    while (!$rsRelatorio->eof()) {
        $rsNomeGrupo = new RecordSet;
        $rsNomeGrupo->preenche($rsRelatorio->getCampo('dadosGrupo'));

        $obPDF->addRecordSet    ( $rsNomeGrupo  );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1",  10 );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2",  8  );

        $obPDF->addRecordSet         ( new RecordSet );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( 1             );
        $obPDF->setAlturaLinha       ( 1             );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "" ,       8  );

        $rsLabel = new RecordSet;
        $arLabel[0] = array('campo1' => 'Concessões');
        $rsLabel->preenche($arLabel);

        $obPDF->addRecordSet         ( $rsLabel       );
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 5              );
        $obPDF->addCabecalho         ( "",     100, 10);
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->addCampo             ( "campo1",  10  );

        $obPDF->addRecordSet         ( new RecordSet );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( 1             );
        $obPDF->setAlturaLinha       ( 1             );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "" ,       8  );

        if ($rsRelatorio->getCampo('concessoesGrupo')) {
            $rsConcessoes = new RecordSet;
            $rsConcessoes->preenche($rsRelatorio->getCampo('concessoesGrupo'));

            $obPDF->addRecordSet         ( $rsConcessoes          );
            $obPDF->setQuebraPaginaLista ( false                  );
            $obPDF->setAlturaCabecalho   ( 1                      );
            $obPDF->setAlturaLinha       ( 5                      );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Mês"           ,  5, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Exercicio"     , 10, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Tipo"          , 15, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Origem/Destino", 50, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Quantidade"    , 12, 10);

            $obPDF->setAlinhamento       ( "C"                    );
            $obPDF->addCampo             ("mes"           , 8     );
            $obPDF->setAlinhamento       ( "C"                    );
            $obPDF->addCampo             ("exercicio"     , 8     );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCampo             ("tipo"          , 8     );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCampo             ("itinerario"    , 8     );
            $obPDF->setAlinhamento       ( "R"                    );
            $obPDF->addCampo             ("quantidade"    , 8     );
        }

        $rsTotal = new RecordSet;
        $rsTotal->preenche($rsRelatorio->getCampo('total'));

        $obPDF->addRecordSet         ( $rsTotal      );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( 1             );
        $obPDF->setAlturaLinha       ( 1             );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCabecalho         ( "",     89, 10);
        $obPDF->addCabecalho         ( "",     89, 10);
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "campo1",  10 );
        $obPDF->setAlinhamento       ( "L"           );
        $obPDF->addCampo             ( "campo2",  8  );

        $rsLabel = new RecordSet;
        $arLabel[0] = array('campo1' => 'Matrículas pertencentes ao Grupo');
        $rsLabel->preenche($arLabel);

        $obPDF->addRecordSet         ( $rsLabel       );
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 5              );
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->addCabecalho         ( "",     100, 10);
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->addCampo             ( "campo1",  10  );

        $obPDF->addRecordSet         ( new RecordSet  );
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 1              );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCabecalho         ( "",     10, 10 );
        $obPDF->setAlinhamento       ( "R"            );
        $obPDF->addCampo             ( "" ,       8   );

        $rsContratosGrupo = new RecordSet;
        $rsContratosGrupo->preenche($rsRelatorio->getCampo('contratosGrupo'));

        $obPDF->addRecordSet         ( $rsContratosGrupo      );
        $obPDF->setQuebraPaginaLista ( false                  );
        $obPDF->setAlturaCabecalho   ( 1                      );
        $obPDF->setAlturaLinha       ( 5                      );
        $obPDF->setAlinhamento       ( "C"                    );
        $obPDF->addCabecalho         ("CGM"           , 40, 10);
        $obPDF->setAlinhamento       ( "C"                    );
        $obPDF->addCabecalho         ("Matrícula"      , 15, 10);
        $obPDF->setAlinhamento       ( "C"                    );
        $obPDF->addCabecalho         ("Lotação"       , 20, 10);
        $obPDF->setAlinhamento       ( "C"                    );
        $obPDF->addCabecalho         ("Local"         , 20, 10);
        $obPDF->setAlinhamento       ( "C"                    );

        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCampo             ("cgm"           , 8     );
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCampo             ("contrato"      , 8     );
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCampo             ("lotacao"       , 8     );
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCampo             ("local"         , 8     );

        $obPDF->addRecordSet         ( new RecordSet );
        $obPDF->setQuebraPaginaLista ( false         );
        $obPDF->setAlturaCabecalho   ( 1             );
        $obPDF->setAlturaLinha       ( 1             );
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCabecalho         ( "",     10, 10);
        $obPDF->setAlinhamento       ( "R"           );
        $obPDF->addCampo             ( "" ,       8  );

        $rsLabel = new RecordSet;
        $arLabel[0] = array('campo1' => 'Vale-Transporte por Empresa');
        $rsLabel->preenche($arLabel);

        $obPDF->addRecordSet         ( $rsLabel       );
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 5              );
        $obPDF->addCabecalho         ( "",     100, 10);
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->addCampo             ( "campo1",  10  );

        if ($rsRelatorio->getCampo('fornecedor')) {
            $rsFornecedor = new RecordSet;
            $rsFornecedor->preenche($rsRelatorio->getCampo('fornecedor'));

            while (!$rsFornecedor->eof()) {
                $rsNomeFornecedor = new RecordSet;
                $rsNomeFornecedor->preenche($rsFornecedor->getCampo('cabecalho'));

                $obPDF->addRecordSet         ( $rsNomeFornecedor );
                $obPDF->setQuebraPaginaLista ( false             );
                $obPDF->setAlturaCabecalho   ( 1                 );
                $obPDF->setAlturaLinha       ( 5                 );
                $obPDF->setAlinhamento       ( "R"               );
                $obPDF->addCabecalho         ( "",     10, 10    );
                $obPDF->addCabecalho         ( "",     10, 10    );
                $obPDF->setAlinhamento       ( "R"               );
                $obPDF->addCampo             ( "campo1",  10     );
                $obPDF->setAlinhamento       ( "L"               );
                $obPDF->addCampo             ( "campo2",  8      );

                $obPDF->addRecordSet         ( new RecordSet );
                $obPDF->setQuebraPaginaLista ( false         );
                $obPDF->setAlturaCabecalho   ( 1             );
                $obPDF->setAlturaLinha       ( 1             );
                $obPDF->setAlinhamento       ( "R"           );
                $obPDF->addCabecalho         ( "",     10, 10);
                $obPDF->setAlinhamento       ( "R"           );
                $obPDF->addCampo             ( "" ,       8  );

                $rsLinhasFornecedor = new RecordSet;
                $rsLinhasFornecedor->preenche($rsFornecedor->getCampo('concessoes'));

                $obPDF->addRecordSet         ( $rsLinhasFornecedor    );
                $obPDF->setQuebraPaginaLista ( false                  );
                $obPDF->setAlturaCabecalho   ( 1                      );
                $obPDF->setAlturaLinha       ( 5                      );
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCabecalho         ("Mês"           ,  5, 10);
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCabecalho         ("Origem/Destino", 75, 10);
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCabecalho         ("Quantidade"    , 12, 10);

                $obPDF->setAlinhamento       ( "C"                    );
                $obPDF->addCampo             ("mes"           , 8     );
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCampo             ("itinerario"    , 8     );
                $obPDF->setAlinhamento       ( "R"                    );
                $obPDF->addCampo             ("quantidade"    , 8     );

                $rsTotalFornecedor = new RecordSet;
                $rsTotalFornecedor->preenche($rsFornecedor->getCampo('total'));

                $obPDF->addRecordSet($rsTotalFornecedor);
                $obPDF->setQuebraPaginaLista ( false                  );
                $obPDF->setAlturaCabecalho   ( 1                      );
                $obPDF->setAlturaLinha       ( 5                      );
                $obPDF->setAlinhamento       ( "R"                    );
                $obPDF->addCabecalho         ( "",     89, 10         );
                $obPDF->addCabecalho         ( "",     89, 10         );
                $obPDF->setAlinhamento       ( "R"                    );
                $obPDF->addCampo             ( "campo1",  10          );
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCampo             ( "campo2",  8           );

                $rsFornecedor->proximo();
            }
        }

        $rsRelatorio->proximo();
    }

//Relatório de contratos
} elseif ($rsRelatorio->getCampo('dadosContrato') != "") {

    while (!$rsRelatorio->eof()) {

        $rsDadosContrato = new RecordSet;
        $rsDadosContrato->preenche($rsRelatorio->getCampo('dadosContrato'));

        $obPDF->addRecordSet    ($rsDadosContrato);
        $obPDF->setAlinhamento  ( "R"            );
        $obPDF->addCabecalho    ( "",     10, 100 );
        $obPDF->addCabecalho    ( "",     100, 100 );
        $obPDF->setAlinhamento  ( "R"            );
        $obPDF->addCampo        ( "campo1",  10  );
        $obPDF->setAlinhamento  ( "L"            );
        $obPDF->addCampo        ( "campo2",  8   );

        $rsLabel = new RecordSet;
        $arLabel[0] = array('campo1' => 'Concessões');
        $rsLabel->preenche($arLabel);
        $obPDF->addRecordSet         ($rsLabel        );
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 5              );
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->addCabecalho         ( "",     100, 100);
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->addCampo             ( "campo1",  10  );

        if ($rsRelatorio->getCampo('concessoesContrato')) {
            $rsConcessoes = new RecordSet;
            $rsConcessoes->preenche($rsRelatorio->getCampo('concessoesContrato'));

            $obPDF->addRecordSet         ( $rsConcessoes          );
            $obPDF->setQuebraPaginaLista ( false                  );
            $obPDF->setAlturaCabecalho   ( 5                      );
            $obPDF->setAlturaLinha       ( 5                      );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Mês"           ,  5, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Exercicio"     , 10, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Tipo"          , 15, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Origem/Destino", 50, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Quantidade"    , 12, 10);

            $obPDF->setAlinhamento       ( "C"                    );
            $obPDF->addCampo             ("mes"           , 8     );
            $obPDF->setAlinhamento       ( "C"                    );
            $obPDF->addCampo             ("exercicio"     , 8     );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCampo             ("tipo"          , 8     );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCampo             ("itinerario"    , 8     );
            $obPDF->setAlinhamento       ( "R"                    );
            $obPDF->addCampo             ("quantidade"    , 8     );
        }

        $obPDF->addRecordSet         ( new RecordSet          );
        $obPDF->setQuebraPaginaLista ( false                  );
        $obPDF->setAlturaCabecalho   ( 1                      );
        $obPDF->setAlturaLinha       ( 1                      );
        $obPDF->setAlinhamento       ( "L"                    );
        $obPDF->addCabecalho         (""              ,  5, 100);
        $obPDF->setAlinhamento       ( "C"                    );
        $obPDF->addCampo             (""              , 8     );

        if ($rsRelatorio->getCampo('gruposContrato')) {
            $rsGrupos = new RecordSet;
            $rsGrupos->preenche($rsRelatorio->getCampo('gruposContrato'));

            $obPDF->addRecordSet         ( $rsGrupos              );
            $obPDF->setQuebraPaginaLista ( false                  );
            $obPDF->setAlturaCabecalho   ( 5                      );
            $obPDF->setAlturaLinha       ( 5                      );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Mês"           ,  5, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Exercicio"     , 10, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Grupo"         , 65, 10);
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCabecalho         ("Quantidade"    , 12, 10);

            $obPDF->setAlinhamento       ( "C"                    );
            $obPDF->addCampo             ("mes"           , 8     );
            $obPDF->setAlinhamento       ( "C"                    );
            $obPDF->addCampo             ("exercicio"     , 8     );
            $obPDF->setAlinhamento       ( "L"                    );
            $obPDF->addCampo             ("grupo"         , 8     );
            $obPDF->setAlinhamento       ( "R"                    );
            $obPDF->addCampo             ("quantidade"    , 8     );
        }

        $rsTotalContrato = new RecordSet;
        $rsTotalContrato->preenche($rsRelatorio->getCampo('totalContrato'));

        $obPDF->addRecordSet         ( $rsTotalContrato );
        $obPDF->setQuebraPaginaLista ( false            );
        $obPDF->setAlturaCabecalho   ( 1                );
        $obPDF->setAlturaLinha       ( 1                );
        $obPDF->setAlinhamento       ( "R"              );
        $obPDF->addCabecalho         ( "",     89, 10   );
        $obPDF->addCabecalho         ( "",     89, 100   );
        $obPDF->setAlinhamento       ( "R"              );
        $obPDF->addCampo             ( "campo1",  10    );
        $obPDF->setAlinhamento       ( "L"              );
        $obPDF->addCampo             ( "campo2",  8     );

        $rsLabel = new RecordSet;
        $arLabel[0] = array('campo1' => 'Vale-Transporte por Empresa');
        $rsLabel->preenche($arLabel);

        $obPDF->addRecordSet         ( $rsLabel       );
        $obPDF->setQuebraPaginaLista ( false          );
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->setAlturaCabecalho   ( 1              );
        $obPDF->setAlturaLinha       ( 5              );
        $obPDF->addCabecalho         ( "",     100, 10);
        $obPDF->setAlinhamento       ( "C"            );
        $obPDF->addCampo             ( "campo1",  10  );

        if ($rsRelatorio->getCampo('fornecedor')) {

            $rsFornecedor = new RecordSet;
            $rsFornecedor->preenche($rsRelatorio->getCampo('fornecedor'));

            while (!$rsFornecedor->eof()) {

                $rsNomeFornecedor = new RecordSet;
                $rsNomeFornecedor->preenche($rsFornecedor->getCampo('cabecalho'));

                $obPDF->addRecordSet($rsNomeFornecedor);
                $obPDF->setQuebraPaginaLista ( false         );
                $obPDF->setAlturaCabecalho   ( 1             );
                $obPDF->setAlturaLinha       ( 5             );
                $obPDF->setAlinhamento       ( "R"           );
                $obPDF->addCabecalho         ( "",     10, 10);
                $obPDF->addCabecalho         ( "",     10, 100);
                $obPDF->setAlinhamento       ( "R"           );
                $obPDF->addCampo             ( "campo1",  10 );
                $obPDF->setAlinhamento       ( "L"           );
                $obPDF->addCampo             ( "campo2",  8  );

                $obPDF->addRecordSet         ( new RecordSet );
                $obPDF->setQuebraPaginaLista ( false         );
                $obPDF->setAlturaCabecalho   ( 1             );
                $obPDF->setAlturaLinha       ( 1             );
                $obPDF->setAlinhamento       ( "L"           );
                $obPDF->addCabecalho         (""     ,  5, 10);
                $obPDF->setAlinhamento       ( "C"           );
                $obPDF->addCampo             (""     , 8     );

                $rsLinhasFornecedor = new RecordSet;
                $rsLinhasFornecedor->preenche($rsFornecedor->getCampo('concessoes'));

                $obPDF->addRecordSet         ( $rsLinhasFornecedor    );
                $obPDF->setQuebraPaginaLista ( false                  );
                $obPDF->setAlturaCabecalho   ( 5                      );
                $obPDF->setAlturaLinha       ( 5                      );
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCabecalho         ("Mês"           ,  5, 10);
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCabecalho         ("Origem/Destino", 75, 10);
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCabecalho         ("Quantidade"    , 12, 10);

                $obPDF->setAlinhamento       ( "C"                    );
                $obPDF->addCampo             ("mes"           , 8     );
                $obPDF->setAlinhamento       ( "L"                    );
                $obPDF->addCampo             ("itinerario"    , 8     );
                $obPDF->setAlinhamento       ( "R"                    );
                $obPDF->addCampo             ("quantidade"    , 8     );

                $rsTotalFornecedor = new RecordSet;
                $rsTotalFornecedor->preenche($rsFornecedor->getCampo('total'));

                $obPDF->addRecordSet         ( $rsTotalFornecedor );
                $obPDF->setQuebraPaginaLista ( false              );
                $obPDF->setAlturaCabecalho   ( 1                  );
                $obPDF->setAlturaLinha       ( 5                  );
                $obPDF->setAlinhamento       ( "R"                );
                $obPDF->addCabecalho         ( "",     89, 10     );
                $obPDF->addCabecalho         ( "",     89, 100     );
                $obPDF->setAlinhamento       ( "R"                );
                $obPDF->addCampo             ( "campo1",  10      );
                $obPDF->setAlinhamento       ( "L"                );
                $obPDF->addCampo             ( "campo2",  8       );

                $rsFornecedor->proximo();
            }
        }

        $rsRelatorio->proximo();
    }

}

$obPDF->show();
?>
