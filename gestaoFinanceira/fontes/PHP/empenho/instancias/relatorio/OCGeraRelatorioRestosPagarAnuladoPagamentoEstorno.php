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
    * Página do Oculto que gera o relatório do Relatório Restos a Pagar Anulado, Pagamentos ou Estorno
    * Data de Criação   : 08/09/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * @ignore

    * $Id: OCGeraRelatorioRestosPagarAnuladoPagamentoEstorno.php 64683 2016-03-21 21:17:16Z michel $

    * Casos de uso : uc-02.03.08
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";

include_once CAM_GF_EMP_MAPEAMENTO.'FEmpenhoRPAnuLiqEstLiq.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'FRelatorioPagamentoOrdemNotaEmpenho.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CLA_LISTA_MPDF;

include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_FW_PDF."RAssinaturas.class.php";

$obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;

if(Sessao::getExercicio()<2016){
    // Faz a verificação, caso a situacao seja 1,2,3, chama o relatorio = 3, senão chama o 4.
    $preview = new PreviewBirt(2, 10, ($request->get('inSituacao') < 4 ? 3 : 4));
    $preview->setTitulo('Relatório do Birt');
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setExportaExcel( true );

    $stCodEntidades = implode(', ', $request->get('inCodEntidade'));
    $preview->addParametro('entidade_resto', $stCodEntidades);

    $stIncluirAssinaturas = $request->get('stIncluirAssinaturas');
    if ($stIncluirAssinaturas == 'nao') {
        $stIncluirAssinaturas = 'não';
    } else {
        $stIncluirAssinaturas = 'sim';
    }
    $preview->addParametro('incluir_assinaturas', $stIncluirAssinaturas);

    if (count($request->get('inCodEntidade')) > 1) {
        $stWhere = "where exercicio='".Sessao::getExercicio()."' and parametro='cod_entidade_prefeitura'";
        $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stWhere);
        $preview->addParametro('entidade', $inCodEntidade);
    } else {
        $inCodEntidade = $request->get('inCodEntidade');
        $preview->addParametro('entidade', $inCodEntidade[0]);
    }

    $arEntidades = Sessao::read('arEntidades');
    foreach ($request->get('inCodEntidade') as $inCodEntidade) {
        $arEntidadesAux[] = $arEntidades[$inCodEntidade];
    }
    $stNomeEntidade = implode("<br/>", $arEntidadesAux);

    $preview->addParametro('entidade_descricao', $stNomeEntidade);
    $preview->addParametro('exercicio', Sessao::getExercicio());
    if ($request->get('inExercicio')) {
        $preview->addParametro('exercicio_resto', $request->get('inExercicio'));
    } else {
        $preview->addParametro('exercicio_resto', '');
    }
    $preview->addParametro('data_inicial', $request->get('stDataInicial'));
    $preview->addParametro('data_final', $request->get('stDataFinal'));

    // Esses 2 relatórios forma unificados, e como já existiam as PLs prontas para os 2 relatórios, não havia a necessidade de unificar as 2 PLs.
    // Com isso foi criado 2 datasets no birt para chamar os relatórios, com isso o tipo_relatório faz a diferença entre eles, e na PL do
    // pago_estornado, os códigos da situação são 1 e 2, por isso foi escreve-se esse valor quando o relatório é pago_estornado.
    switch ($request->get('inSituacao')) {
        case 1:
        $preview->addParametro('situacao_descricao' , 'Anulados');
        $preview->addParametro('situacao'           , $request->get('inSituacao'));
        $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
        $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
        break;
        case 2:
        $preview->addParametro('situacao_descricao' , 'Liquidados');
        $preview->addParametro('situacao'           , $request->get('inSituacao'));
        $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
        $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
        break;
        case 3:
        $preview->addParametro('situacao_descricao' , 'Anulados (Liquidados)');
        $preview->addParametro('situacao'           , $request->get('inSituacao'));
        $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
        $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
        break;
        case 4:
        $preview->addParametro('situacao_descricao' , 'Pagamentos');
        $preview->addParametro('situacao'           , '1');
        $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
        $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
        break;
        case 5:
        $preview->addParametro('situacao_descricao' , 'Estornos');
        $preview->addParametro('situacao'           , '2');
        $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
        $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
        break;
    }

    if ($request->get('inCodOrgao')) {
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio($request->get('inExercicio'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('inCodOrgao'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsOrgao);

        $preview->addParametro('cod_orgao', $request->get('inCodOrgao'));
        $preview->addParametro('orgao_descricao', $request->get('inCodOrgao').' - '.$rsOrgao->getCampo('nom_orgao'));
    } else {
        $preview->addParametro('cod_orgao', '');
        $preview->addParametro('orgao_descricao', '');
    }

    if ($request->get('inCodUnidade')) {
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($request->get('inCodUnidade'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('inCodOrgao'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio($request->get('inExercicio'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar($rsUnidade, $stFiltro, "", $boTransacao);

        $preview->addParametro('cod_unidade'      , $request->get('inCodUnidade'));
        $preview->addParametro('unidade_descricao', $request->get('inCodUnidade').' - '.$rsUnidade->getCampo('nom_unidade'));
    } else {
        $preview->addParametro('cod_unidade'      , '');
        $preview->addParametro('unidade_descricao', '');
    }

    if ($request->get('inCodFornecedor')) {
        $RCGM = new RCGM;
        $RCGM->setNumCGM($request->get('inCodFornecedor'));
        $RCGM->listar($rsDadosCGM);
        $stDescFornecedor = $rsDadosCGM->getCampo("nom_cgm");
        $preview->addParametro('cod_credor', $request->get('inCodFornecedor'));
        $preview->addParametro('nome_credor', $request->get('inCodFornecedor').' - '.$stDescFornecedor);
    } else {
        $preview->addParametro('cod_credor', '');
        $preview->addParametro('nome_credor', '');
    }

    if (trim($request->get('inCodDespesa', '')) != "") {
        $stWhere  = " where exercicio='".Sessao::getExercicio()."'";
        $stWhere .= " and cod_estrutural = '".$request->get('inCodDespesa')."'";
        $stDescricao = SistemaLegado::pegaDado('descricao', 'orcamento.conta_despesa', $stWhere);
        $preview->addParametro('elemento_despesa', str_replace(".","",$request->get('inCodDespesa')));
        $preview->addParametro('elemento_despesa_masc', $request->get('inCodDespesa'));

        $stDespesaDescricao = $request->get('inCodDespesa').' - '. $stDescricao;
        $preview->addParametro('despesa_descricao', $stDespesaDescricao);
    } else {
        $preview->addParametro('elemento_despesa', '');
        $preview->addParametro('despesa_descricao', '');
    }

    if ($request->get('inCodRecurso')) {
        $obRegra = new TOrcamentoRecurso();
        $obRegra->setDado("cod_recurso", "'".$request->get('inCodRecurso')."'" );
        $obRegra->setDado("exercicio"  , Sessao::getExercicio() );
        $obRegra->recuperaRelacionamento( $rsLista );

        $stDescricaoRecurso  = $request->get('inCodRecurso');
        $stDescricaoRecurso .= ' - '.$rsLista->getCampo("nom_recurso");

        $preview->addParametro('cod_recurso'      , $request->get('inCodRecurso'));
        $preview->addParametro('recurso_descricao', $stDescricaoRecurso);
    } else {
        $preview->addParametro('cod_recurso'      , '');
        $preview->addParametro('recurso_descricao', '');
    }

    if ($request->get('inCodUso', '') != "" && $request->get('inCodDestinacao', '') != "" && $request->get('inCodEspecificacao', '') != "") {
        $preview->addParametro('destinacao_recurso', $request->get('inCodUso').$request->get('inCodDestinacao').$request->get('inCodEspecificacao'));
    } else {
        $preview->addParametro('destinacao_recurso', '');
    }

    if ($request->get('inCodDetalhamento')) {
        $preview->addParametro('cod_detalhamento', $request->get('inCodDetalhamento'));
    } else {
        $preview->addParametro('cod_detalhamento', '');
    }

    if (Sessao::getExercicio() > '2012') {
        $preview->addParametro('boTCEMS', 'true');
    } else {
        $preview->addParametro('boTCEMS', 'false');
    }

    $preview->addAssinaturas( Sessao::read('assinaturas') );
    $preview->preview();
}else{
    $arFiltro = Sessao::read('filtroRelatorio');
    $request = new Request($arFiltro);

    #Width Colunas da Lista
    $inWidthData      = 6;
    $inWidthEmp       = 7;
    $inWidthNota      = 6;
    $inWidthCredorId  = 4;
    $inWidthCredorNom = 38;
    $inWidthValor     = 39;
    //Total 100%

    $inColSpanRodape  = 5;

    switch ($request->get('inSituacao')) {
        case 1:
            $obFEmpenhoRP = new FEmpenhoRPAnuLiqEstLiq;
            $stSituacao = 'Anulados';
            $inSituacao = $request->get('inSituacao');

            #Total 4 Colunas: Data, Empenho, Credor e Valor
            # Widths : 6%, 13%, 42% e 39% -> 100%
            $inWidthEmp = 13;
            $inWidthNota = 0;
            $inWidthConta = 0;

            $inColSpanRodape = 4;
            $inColSpanCabecalho = 4;
        break;
        case 2:
            $obFEmpenhoRP = new FEmpenhoRPAnuLiqEstLiq;
            $stSituacao = 'Liquidados';
            $inSituacao = $request->get('inSituacao');

            #Total 7 Colunas: Data, Empenho, Nota, Credor, Valor, Estorno e Saldo
            # Widths : 6%, 7%, 6%, 42%, 13%, 13% e 13% -> 100%
            $inWidthConta = 0;
            $inWidthValor = 13;

            $inColSpanCabecalho = 7;
        break;
        case 3:
            $obFEmpenhoRP = new FEmpenhoRPAnuLiqEstLiq;
            $stSituacao = 'Anulação de Liquidação';
            $inSituacao = $request->get('inSituacao');

            #Total 5 Colunas: Data, Empenho, Nota, Credor e Valor
            # Widths : 6%, 14%, 13%, 42% e 25% -> 100%
            $inWidthConta = 0;
            $inWidthEmp       = 14;
            $inWidthNota      = 13;
            $inWidthValor     = 25;

            $inColSpanCabecalho = 5;
        break;
        case 4:
            $obFEmpenhoRP = new FRelatorioPagamentoOrdemNotaEmpenho;
            $stSituacao = 'Pagos';
            $inSituacao = 1;

            #Total 8 Colunas: Data, Empenho, Nota, Credor, Conta, Valor, Estorno e Saldo
            # Widths : 6%, 7%, 6%, 34%, 23%, 8%, 8% e 8% -> 100%
            $inWidthCredorNom = 30;
            $inWidthValor = 8;
            $inWidthConta = 23;

            $inColSpanRodape = 6;
            $inColSpanCabecalho = 8;
        break;
        case 5:
            $obFEmpenhoRP = new FRelatorioPagamentoOrdemNotaEmpenho;
            $stSituacao = 'Anulação de Pagamento';
            $inSituacao = 2;

            #Total 6 Colunas: Data, Empenho, Nota, Credor, Conta e Valor
            # Widths : 6%, 7%, 6%, 35%, 26% e 20% -> 100%
            $inWidthCredorNom = 31;
            $inWidthValor = 20;
            $inWidthConta = 26;

            $inColSpanRodape = 6;
            $inColSpanCabecalho = 6;
        break;
    }

    $inWidthTotal = $inWidthData + $inWidthEmp + $inWidthNota + $inWidthCredorId + $inWidthCredorNom + $inWidthConta;

    $stCodEntidades = implode(', ', $request->get('inCodEntidade'));

    $obFEmpenhoRP->setDado('exercicio'           , Sessao::getExercicio());
    $obFEmpenhoRP->setDado('exercicioEmpenho'    , $request->get('inExercicio'));
    $obFEmpenhoRP->setDado('stFiltro'            , '');
    $obFEmpenhoRP->setDado('stDataInicial'       , $request->get('stDataInicial'));
    $obFEmpenhoRP->setDado('stDataFinal'         , $request->get('stDataFinal'));
    $obFEmpenhoRP->setDado('stEntidade'          , $stCodEntidades);
    $obFEmpenhoRP->setDado('inOrgao'             , $request->get('inCodOrgao'));
    $obFEmpenhoRP->setDado('inUnidade'           , $request->get('inCodUnidade'));
    $obFEmpenhoRP->setDado('inRecurso'           , $request->get('inCodRecurso'));
    $obFEmpenhoRP->setDado('inCodFornecedor'     , $request->get('inCodFornecedor'));

    if($request->get('inCodUso') && $request->get('inCodDestinacao') && $request->get('inCodEspecificacao'))
        $request->set('stDestinacaoRecurso', $request->get('inCodUso').$request->get('inCodDestinacao').$request->get('inCodEspecificacao'));

    $obFEmpenhoRP->setDado('stDestinacaoRecurso' , $request->get('stDestinacaoRecurso'));
    $obFEmpenhoRP->setDado('inCodDetalhamento'   , $request->get('inCodDetalhamento'));
    $obFEmpenhoRP->setDado('stElementoDespesa'   , $request->get('inCodDespesa'));
    $obFEmpenhoRP->setDado('inSituacao'          , $inSituacao);

    $obFEmpenhoRP->setDado('stCodFuncao'         , $request->get('stCodFuncao'));
    $obFEmpenhoRP->setDado('stCodSubFuncao'      , $request->get('stCodSubFuncao'));

    $obFEmpenhoRP->recuperaRP( $rsRP );

    $arRPDia    = array();
    $arTotalDia = array();
    $arTotal    = array();

    $arRPAnuLiqEstLiq = $rsRP->getElementos();

    #AGRUPA RESTOS POR DIA, TOTAL DIA E TOTAL GERAL
    foreach($arRPAnuLiqEstLiq as $restos) {
        $arRPDia[$restos['data']][] = $restos;

        $arTotalDia[$restos['data']]['valor']      += $restos['valor'];
        $arTotalDia[$restos['data']]['vl_anulado'] += $restos['vl_anulado'];
        $arTotalDia[$restos['data']]['vl_saldo']   += $restos['vl_saldo'];

        $arTotal[0]['valor']      += $restos['valor'];
        $arTotal[0]['vl_anulado'] += $restos['vl_anulado'];
        $arTotal[0]['vl_saldo']   += $restos['vl_saldo'];
    }

    #MONTA PDF
    $obListaMPDF = new ListaMPDF();
    $obListaMPDF->setNomeRelatorio("Restos a Pagar");
    $obListaMPDF->setFormatoFolha("A4-L");
    $obListaMPDF->setCodGestao( 2 );
    $obListaMPDF->setCodModulo( 10 );
    $obListaMPDF->setCodEntidades($stCodEntidades);
    $obListaMPDF->setDataInicio($request->get('stDataInicial'));
    $obListaMPDF->setDataFinal($request->get('stDataFinal').' - '.$stSituacao);

    /********** FILTROS **********/
    $arEntidades = Sessao::read('arEntidades');
    foreach ($request->get('inCodEntidade') as $inCodEntidade) {
        $arEntidadesAux[] = $arEntidades[$inCodEntidade];
    }
    $stNomeEntidade = implode(", ", $arEntidadesAux);

    $obListaMPDF->addFiltro( "Entidades", $stNomeEntidade );

    if($request->get('stDataInicial'))
        $obListaMPDF->addFiltro( "Data Inicial", $request->get('stDataInicial') );

    if($request->get('stDataFinal'))
        $obListaMPDF->addFiltro( "Data Final", $request->get('stDataFinal') );

    $obListaMPDF->addFiltro( "Exercício", Sessao::getExercicio() );

    if($request->get('inExercicio'))
        $obListaMPDF->addFiltro( "Exercício Empenho", $request->get('inExercicio') );

    if($stSituacao)
        $obListaMPDF->addFiltro( "Situação", $stSituacao );

    if($request->get('inCodOrgao')){
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio($request->get('inExercicio'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('inCodOrgao'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsOrgao);

        $obListaMPDF->addFiltro( "Órgão", $request->get('inCodOrgao').' - '.$rsOrgao->getCampo('nom_orgao') );
    }

    if($request->get('inCodUnidade')){
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($request->get('inCodUnidade'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('inCodOrgao'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio($request->get('inExercicio'));
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar($rsUnidade);

        $obListaMPDF->addFiltro( "Unidade", $request->get('inCodUnidade').' - '.$rsUnidade->getCampo('nom_unidade') );
    }

    if($request->get('inCodFornecedor')){
        $RCGM = new RCGM;
        $RCGM->setNumCGM($request->get('inCodFornecedor'));
        $RCGM->listar($rsDadosCGM);

        $obListaMPDF->addFiltro( "Credor", $request->get('inCodFornecedor').' - '.$rsDadosCGM->getCampo("nom_cgm") );
    }

    if($request->get('inCodDespesa')){
        $stWhere  = " where exercicio='".Sessao::getExercicio()."'";
        $stWhere .= " and cod_estrutural = '".$request->get('inCodDespesa')."'";
        $stDescricao = SistemaLegado::pegaDado('descricao', 'orcamento.conta_despesa', $stWhere);

        $obListaMPDF->addFiltro( "Elemento de Despesa", $request->get('inCodDespesa').' - '. $stDescricao );
    }

    if($request->get('inCodRecurso')){
        $obRegra = new TOrcamentoRecurso();
        $obRegra->setDado("cod_recurso", "'".$request->get('inCodRecurso')."'" );
        $obRegra->setDado("exercicio"  , Sessao::getExercicio() );
        $obRegra->recuperaRelacionamento( $rsLista );

        $obListaMPDF->addFiltro( "Recurso", $request->get('inCodRecurso').' - '.$rsLista->getCampo("nom_recurso") );
    }

    if($request->get('stCodFuncao'))
        $obListaMPDF->addFiltro( "Função", $request->get('stCodFuncao') );

    if($request->get('stCodSubFuncao'))
        $obListaMPDF->addFiltro( "Sub-função", $request->get('stCodSubFuncao') );
    /*********************************/

    /********** ASSINATURAS **********/
    $assinaturas = Sessao::read('assinaturas');
    if ( count($assinaturas['selecionadas']) > 0 ) {
        $obRAssinaturas = new RAssinaturas;
        $obRAssinaturas->setArAssinaturas( $assinaturas['selecionadas'] );
        $rsAssinaturas = $obRAssinaturas->getArAssinaturas();

        $obListaMPDF->addAssinatura($rsAssinaturas);
    }
    /*********************************/

    /**** CABEÇALHO GERAL ****/
    $stClasse = "cabecalho_geral font_size_10";

    $obListaMPDF->addCabecalhoGeral("DATA"                     , "C",     $inWidthData."%", 1, 1, "", "FALSE", "", $stClasse );
    $obListaMPDF->addCabecalhoGeral("EMPENHO"                  , "C",      $inWidthEmp."%", 1, 1, "", "FALSE", "", $stClasse );

    if($request->get('inSituacao') > 1)
        $obListaMPDF->addCabecalhoGeral("NOTA"                 , "C",     $inWidthNota."%", 1, 1, "", "FALSE", "", $stClasse );

    $obListaMPDF->addCabecalhoGeral("CREDOR"                   , "R", $inWidthCredorId."%", 1, 1, "", "FALSE", "", $stClasse );
    $obListaMPDF->addCabecalhoGeral(""                         , "L",$inWidthCredorNom."%", 1, 1, "", "FALSE", "", $stClasse );

    #Situações Pagamentos E Estornos identificam Conta Bancária
    if($request->get('inSituacao') == 4 || $request->get('inSituacao') == 5)
        $obListaMPDF->addCabecalhoGeral("CONTA BANCÁRIA"       , "L",    $inWidthConta."%", 1, 1, "", "FALSE", "", $stClasse );

    $obListaMPDF->addCabecalhoGeral("VALOR"                    , "R",    $inWidthValor."%", 1, 1, "", "FALSE", "", $stClasse );

    #Situações Liquidados E Pagamentos identificam Valor Estornado e Saldo
    if($request->get('inSituacao') == 2 || $request->get('inSituacao') == 4){
        $obListaMPDF->addCabecalhoGeral("ESTORNADO"            , "R",    $inWidthValor."%", 1, 1, "", "FALSE", "", $stClasse );
        $obListaMPDF->addCabecalhoGeral("SALDO"                , "R",    $inWidthValor."%", 1, 1, "", "FALSE", "", $stClasse );
    }
    /*************************/

    /**** CRIAR LISTA POR DATA ****/
    foreach ($arRPDia as $stDia => $restos) {
        $stClasse = " font_size_9";

        $rsRecordSetLista = new RecordSet();
        $rsRecordSetLista->preenche($restos);
        $obListaMPDF->addLista( $rsRecordSetLista, FALSE, "4mm", $stClasse );

        $stClasse .= " tr_nivel_1 ";

        /****** CABEÇALHO DA LISTA *******/
        $obListaMPDF->addCabecalho($stDia , "C", "",                   1, 1, "", "FALSE", "", $stClasse );
        $obListaMPDF->addCabecalho(""     , "C", "", $inColSpanCabecalho, 1, "", "FALSE", "", $stClasse );
        /*********************************/

        /**** CAMPOS DA LISTA - CORPO ****/
        $obListaMPDF->addCampo(""                                   , "C",     $inWidthData."%", 1, 1, "", ""          , $stClasse                      );
        $obListaMPDF->addCampo("[entidade] - [empenho]/[exercicio]" , "C",      $inWidthEmp."%", 1, 1, "", ""          , $stClasse                      );

        if($request->get('inSituacao') > 1)
            $obListaMPDF->addCampo("cod_nota"                       , "C",     $inWidthNota."%", 1, 1, "", ""          , $stClasse                      );

        $obListaMPDF->addCampo("cgm"                                , "R", $inWidthCredorId."%", 1, 1, "", ""          , $stClasse                      );
        $obListaMPDF->addCampo("razao_social"                       , "L",$inWidthCredorNom."%", 1, 1, "", ""          , "tabulacao_nivel_1 ".$stClasse );

        if($request->get('inSituacao') == 4 || $request->get('inSituacao') == 5)
            $obListaMPDF->addCampo("[conta] [banco]"                , "L",    $inWidthConta."%", 1, 1, "", ""          , $stClasse                      );

        $obListaMPDF->addCampo("valor"                              , "R",    $inWidthValor."%", 1, 1, "", "NUMERIC_BR", $stClasse                      );

        if($request->get('inSituacao') == 2 || $request->get('inSituacao') == 4){
            $obListaMPDF->addCampo("vl_anulado"                     , "R",    $inWidthValor."%", 1, 1, "", "NUMERIC_BR", $stClasse                      );
            $obListaMPDF->addCampo("vl_saldo"                       , "R",    $inWidthValor."%", 1, 1, "", "NUMERIC_BR", $stClasse                      );
        }
        /*********************************/

        /**** TOTAL DA LISTA - RODAPÉ ****/
        $stBorderTop = " border-top: none; ";
        $obListaMPDF->addRodape("SUB-TOTAL DO DIA"                    , "R", "", $inColSpanRodape, 1, $stBorderTop, ""          , $stClasse );
        $obListaMPDF->addRodape($arTotalDia[$stDia]['valor']          , "R", "",                1, 1, $stBorderTop, "NUMERIC_BR", $stClasse );

        if($request->get('inSituacao') == 2 || $request->get('inSituacao') == 4){
            $obListaMPDF->addRodape($arTotalDia[$stDia]['vl_anulado'] , "R", "",                1, 1, $stBorderTop, "NUMERIC_BR", $stClasse );
            $obListaMPDF->addRodape($arTotalDia[$stDia]['vl_saldo']   , "R", "",                1, 1, $stBorderTop, "NUMERIC_BR", $stClasse );
        }
        /*********************************/
    }
    /**** FIM DA LISTA POR DATA ***/

    /**** TOTAL DOS RESTOS ****/
    if(is_array($arTotal) && count($arTotal) > 0){
        $rsRecordSetLista = new RecordSet();
        $rsRecordSetLista->preenche($arTotal);

        $stClasse = " font_size_9";
        $obListaMPDF->addLista( $rsRecordSetLista, FALSE, "4mm", $stClasse );

        $stClasse .= " tr_nivel_1 ";

        $obListaMPDF->addCabecalho("TOTAL DO PERÍODO"            , "R", $inWidthTotal."%", 1, 1, $stBorderTop, "FALSE", ""          , $stClasse );
        $obListaMPDF->addCabecalho($arTotal[0]['valor']          , "R", $inWidthValor."%", 1, 1, $stBorderTop, "FALSE", "NUMERIC_BR", $stClasse );

        if($request->get('inSituacao') == 2 || $request->get('inSituacao') == 4){
            $obListaMPDF->addCabecalho($arTotal[0]['vl_anulado'] , "R", $inWidthValor."%", 1, 1, $stBorderTop, "FALSE", "NUMERIC_BR", $stClasse );
            $obListaMPDF->addCabecalho($arTotal[0]['vl_saldo']   , "R", $inWidthValor."%", 1, 1, $stBorderTop, "FALSE", "NUMERIC_BR", $stClasse );
        }
    }
    /**************************/

    $obListaMPDF->geraRelatorioMPDF();
}

?>
