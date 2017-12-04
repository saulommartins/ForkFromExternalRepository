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
    * Data de Criação   : 02/03/2005

    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Id: OCGeraRelatorioEmpenhoRPCredor.php 64417 2016-02-18 18:03:51Z michel $

    * Casos de uso : uc-02.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//RELATÓRIO ANTERIOR ATÉ 2015
if(Sessao::getExercicio() < 2016){
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
    include_once CAM_FW_PDF."RRelatorio.class.php";
    include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";

    $obRRelatorio      = new RRelatorio;
    $obPDF             = new ListaPDF( "L" );
    $obREmpenhoEmpenho = new REmpenhoEmpenho;
    
    $arFiltro = Sessao::read('filtroRelatorio');
    $arFiltroNom = Sessao::read('filtroNomRelatorio');
    $rsRecordSet = Sessao::read('rsRecordSet');
    // Adicionar logo nos relatorios
    if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
        $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
        $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
    }

    $obRRelatorio->recuperaCabecalho ( $arConfiguracao );
    $obPDF->setModulo                ( "Empenho - ".Sessao::getExercicio() );
    $obPDF->setTitulo                ( "Restos a Pagar - ". $arFiltro['stOrdenacao']." - ".$arFiltro['inExercicio'] );

    $dtPeriodo = $arFiltro['stDataSituacao'] ."  ".$arFiltro['relatorio'];
    $obPDF->setSubTitulo ( "Posição: ".$dtPeriodo  );

    $obPDF->setUsuario           ( Sessao::getUsername() );
    $obPDF->setEnderecoPrefeitura( $arConfiguracao );

    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        $arNomEntidade[] = $arFiltroNom['entidade'][$inCodEntidade];
    }
    $obPDF->addFiltro( 'Entidades Relacionadas:'      , $arNomEntidade );

    $obPDF->addFiltro( 'Exercício:'                   , $arFiltro['inExercicio'] );
    $obPDF->addFiltro( 'Situação Até:'                , $arFiltro['stDataSituacao'] );

    if($arFiltro['inCodEmpenhoInicial'])
        $obPDF->addFiltro( 'Número do Empenho:'       , $arFiltro[ 'inCodEmpenhoInicial' ] . " até " . $arFiltro[ 'inCodEmpenhoFinal' ]);
    if ($arFiltro['inCodOrgao']) {
        if ($arFiltroNom['orgao'][$arFiltro[ 'inCodOrgao' ]] && $arFiltro['stExercicio'] > '2004') {
            $stNomOrgao = " - " . $arFiltroNom['orgao'][$arFiltro[ 'inCodOrgao' ]];
        } else {
            $stNomOrgao = "";
        }
        $obPDF->addFiltro( 'Órgão Orçamentário:'      , $arFiltro['inCodOrgao'] . $stNomOrgao );
    }
    if ($arFiltro['inCodUnidade']) {
        if ($arFiltroNom['unidade'][$arFiltro[ 'inCodUnidade' ]] && $arFiltro['stExercicio'] > '2004') {
            $stNomUnidade = " - " . $arFiltroNom['unidade'][$arFiltro[ 'inCodUnidade' ]];
        } else {
            $stNomUnidade = "";
        }
        $obPDF->addFiltro( 'Unidade Orçamentária:'    , $arFiltro['inCodUnidade'] . $stNomUnidade );
    }

    if ($arFiltro['stElementoDespesa']) {
        $arMascClassificacao = Mascara::validaMascaraDinamica( $arFiltro['stMascClassificacao'] , $arFiltro['stElementoDespesa'] );

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascara  ( $arFiltro['stMascClassificacao'] );
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1] );
        $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );

        $obPDF->addFiltro( 'Elemento da Despesa:'     , $arFiltro['stElementoDespesa'] . " - " . $stDescricao);
    }
    if(trim($arFiltro['inCodRecurso']) != "" )
        $obPDF->addFiltro( 'Recurso:'                 , $arFiltro['inCodRecurso'] . " - " . $arFiltroNom['recurso'][$arFiltro[ 'inCodRecurso' ]] );
    if ($arFiltro['inCGM']) {
        $obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM($arFiltro['inCGM']);
        $obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->consultar($rsCGM);
        $obPDF->addFiltro( 'Credor:' , $arFiltro[ 'inCGM' ] . " - " . $rsCGM->getCampo( "nom_cgm" ));
    }
    if($arFiltro['stCodFuncao'])
        $obPDF->addFiltro( 'Função:'                  , $arFiltro['stCodFuncao'] );
    if($arFiltro['stCodSubFuncao'])
        $obPDF->addFiltro( 'Sub-função:'              , $arFiltro['stCodSubFuncao'] );

    switch ($arFiltro[ 'inOrdenacao' ]) {
        case 1:
            $stOrdenacao = "Empenho";
            break;
        case 2:
            $stOrdenacao = "Vencimento";
            break;
        case 3:
            $stOrdenacao = "Recurso";
            break;
        case 4:
            $stOrdenacao = "Credor";
            break;
    }
    $obPDF->addFiltro( 'Ordenação:'       , $arFiltro['inOrdenacao'] . " - " . $stOrdenacao );

    $obPDF->addRecordSet( $rsRecordSet );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("Empenho", 10, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("Dotação", 18, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("Fornecedor",24, 10);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Emissão", 8, 10);
    $obPDF->addCabecalho("Vencimento", 8, 10);
    $obPDF->addCabecalho("Empenhado", 8, 10);
    $obPDF->addCabecalho("Liquidado", 7, 10);
    $obPDF->addCabecalho("Anulado", 7, 10);
    $obPDF->addCabecalho("Pago", 5, 10);
    $obPDF->addCabecalho("A pagar", 6, 10);
    $obPDF->addQuebraLinha("nivel",2,5);

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("empenho", 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dotacao", 6);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("razao_social",6);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("data_empenho", 6);
    $obPDF->addCampo("data_vencimento", 6);
    $obPDF->addCampo("empenhado",6);
    $obPDF->addCampo("liquidado",6 );
    $obPDF->addCampo("anulado", 6);
    $obPDF->addCampo("pago", 6);
    $obPDF->addCampo("apagar",6);

    $arAssinaturas = Sessao::read('assinaturas');
    if ( count($arAssinaturas['selecionadas']) > 0 ) {
        include_once( CAM_FW_PDF."RAssinaturas.class.php" );
        $obRAssinaturas = new RAssinaturas;
        $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
        $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
        $obRAssinaturas->montaPDF( $obPDF );
    }

    $obPDF->show();
}else{
    //NOVO RELATÓRIO APÓS 2015
    include_once '../../../../../../config.php';
    include_once CLA_MPDF;

    $arDados        = Sessao::read('arDados');

    $inCodEntidades = $arDados['inCodEntidade'];
    $stDataInicial  = $arDados['stDataInicial'];
    $stDataFinal    = $arDados['stDataFinal'];

    //-------------------------------
    // Preparando a chamada para o layout do relatório

    //Arquivo MPDF LHRPCredor.php
    $obMPDF = new FrameWorkMPDF(2,10,11);
    $obMPDF->setCodEntidades($inCodEntidades);
    $obMPDF->setDataInicio($stDataInicial);
    $obMPDF->setDataFinal($stDataFinal);
    $obMPDF->setNomeRelatorio("Restos a Pagar por Credor");
    $obMPDF->setFormatoFolha("A4-L");

    $obMPDF->setConteudo($arDados);

    $obMPDF->gerarRelatorio();
}
?>
