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
    * Página de Include Oculta - Exportação Arquivos GPC

    * Data de Criação   : 03/06/2008

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/
    /* Arquivos de busca de sql */
    require_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
    require_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";
    require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPAUnidadeOrcamentaria.class.php";
    require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoRemuneracao.class.php";
    require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
    require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPALotacao.class.php";
    require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPASituacaoFuncional.class.php";
    require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoCargo.class.php";

    /* Arquivos de geração de arquivos*/
    require_once CAM_GPC_TCMPA_NEGOCIO."IdentificacaoInformacoes.class.php";
    require_once CAM_GPC_TCMPA_NEGOCIO."UnidadeGestora.class.php";
    require_once CAM_GPC_TCMPA_NEGOCIO."UnidadeOrcamentaria.class.php";
    require_once CAM_GPC_TCMPA_NEGOCIO."Lotacionograma.class.php";
    require_once CAM_GPC_TCMPA_NEGOCIO."FuncionariosAgentesPoliticos.class.php";
    require_once CAM_GPC_TCMPA_NEGOCIO."InformacoesPagamento.class.php";
    require_once CAM_GPC_TCMPA_NEGOCIO."rodapeFimArquivo.class.php";

    /* Faz a montagem do arquivo de exportação
     * Esse arquivo deve seguir o seguinte formato:
     *
     * ARQUIVO DE FOLHAS DE PAGAMENTO
     *
     *      Registro 000 - Identificação das Informações
     *      Registro 010 - Órgão/ Unidades Gestoras
     *          |- Registro 020 - Órgão/ Unidade Orçamentárias
     *              |- Registro 030 - Lotacionograma
     *              |- Registro 040 - Funcionários e Ag. Políticos
     *                  |- Registro 041 - Informações de Pagamento
     *                  |- Registro 042 - Diárias ( NÃO TEM COMO FAZER )
     *      Registro 999 - Indicador do Fim de Arquivo
     *
     * */

    // inicaliza o sequencial que é usado na hora de gerar o arquivo

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');

    // faz a filtragem do quadrimestre pois os dados devem ser referentes a competencia informada
    if ($arFiltroRelatorio['inQuadrimestre']) {
        if ($arFiltroRelatorio['inQuadrimestre'] == 1) {
            $stPeriodosInicial = Sessao::getExercicio().'01';
            $stPeriodosFinal = Sessao::getExercicio().'04';
        } elseif ($arFiltroRelatorio['inQuadrimestre'] == 2) {
            $stPeriodosInicial = Sessao::getExercicio().'05';
            $stPeriodosFinal = Sessao::getExercicio().'08';
        } else {
            $stPeriodosInicial = Sessao::getExercicio().'09';
            $stPeriodosFinal = Sessao::getExercicio().'12';
        }
    }

    $arFiltroRelatorio['nro_sequencial'] = 1;
    Sessao::write('filtroRelatorio', $arFiltroRelatorio);

    // monta o recorset da Identificacao das Informacoes
    $rsIdentificacaoInformacoes = buscaIdentificacaoInformacoes();

    // gera linhas da identificação das informações do arquivo
    $obIdentificacaoInformacoes = new IdentificacaoInformacoes();
    $obIdentificacaoInformacoes->setRecordSet( $rsIdentificacaoInformacoes );
    $obIdentificacaoInformacoes->setExportador( $obExportador );
    $obIdentificacaoInformacoes->geraArquivo();

    $inCodEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura",8,Sessao::getExercicio());

    $arFiltroRelatorio['nro_sequencial']++;
    Sessao::write('filtroRelatorio', $arFiltroRelatorio);

    //ForEach das entidades selecionadas pelo usuário na interface
    foreach ($arFiltroRelatorio['inNumCGM'] as $indiceEntidades => $numCgmEntidade) {

        $inCodEntidade = buscaCodEntidade($numCgmEntidade);

        if ($inCodEntidadePrefeitura == $inCodEntidade) {
            Sessao::setEntidade("");
        } else {
            Sessao::setEntidade($inCodEntidade);
        }

        $rsUnidadeGestora = buscaUnidadeGestora($inCodEntidade);

        // busca todas as unidades gestoras
        foreach ($rsUnidadeGestora->arElementos as $chaveUnidadeGestora => $arDadosUnGest) {

            $arDadosUnGest['nro_sequencial'] = $arFiltroRelatorio['nro_sequencial'];

            // chama a classe para gerar a linha da unidade gestora
            $obUnidadeGestora = new UnidadeGestora();
            $obUnidadeGestora->setDados( $arDadosUnGest );
            $obUnidadeGestora->setExportador( $obExportador );
            $obUnidadeGestora->geraArquivo();

            $arFiltroRelatorio['nro_sequencial']++;
            Sessao::write('filtroRelatorio', $arFiltroRelatorio);

            // faz a busca das unidades orçamentarias de acordo com a entidade e unidade gestora em questão
            $rsUnidadeOrcamentaria = buscaUnidadeOrcamentaria( $arDadosUnGest['cod_entidade'] );

            // Inclui no arquivo todas as unidades Orçamentarias
            foreach ($rsUnidadeOrcamentaria->arElementos as $inChvUnOrc => $arDadosUnOrcamentaria) {

                $arDadosUnOrcamentaria['nro_sequencial'] = $arFiltroRelatorio['nro_sequencial'];

                // chama a classe para gerar a linha da unidade orçamentaria
                $obUnidadeOrcamentaria = new UnidadeOrcamentaria();
                $obUnidadeOrcamentaria->setDados( $arDadosUnOrcamentaria );
                $obUnidadeOrcamentaria->setExportador( $obExportador );
                $obUnidadeOrcamentaria->geraArquivo();

                $arFiltroRelatorio['nro_sequencial']++;
                Sessao::write('filtroRelatorio', $arFiltroRelatorio);

                // faz as buscas do lotacionograma de acordo com o órgão da unidade orçamentaria
                $rsLotacionograma = buscaLotacionograma();

                foreach ($rsLotacionograma->arElementos as $inChvLot => $arDadosLotacionograma) {

                    // monta os dados no arquivo do lotacionograma
                    $arDadosLotacionograma['nro_sequencial'] = $arFiltroRelatorio['nro_sequencial'];
                    $obLotacionograma = new Lotacionograma();
                    $obLotacionograma->setDados( $arDadosLotacionograma);
                    $obLotacionograma->setExportador( $obExportador );
                    $obLotacionograma->geraArquivo();

                    $arFiltroRelatorio['nro_sequencial']++;
                    Sessao::write('filtroRelatorio', $arFiltroRelatorio);

                    //faz as buscas dos funcionários ou agentes politicos de acordo com o órgão da unidade orçamentaria
                    $rsAgenteFuncionarioPoliticos = buscaFuncionariosAgentesPoliticos( $arDadosUnOrcamentaria['cod_orgao'], $arDadosLotacionograma, $stPeriodosInicial, $stPeriodosFinal );

                    foreach ($rsAgenteFuncionarioPoliticos->arElementos as $inChvFuncPol => $arDadosFuncPol) {

                        $arDadosFuncPol['nro_sequencial'] = $arFiltroRelatorio['nro_sequencial'];

                        // monta os dados no arquivo dos funcionarios/ag. politicos
                        $obFuncionariosAgentesPoliticos = new FuncionariosAgentesPoliticos();
                        $obFuncionariosAgentesPoliticos->setDados( $arDadosFuncPol );
                        $obFuncionariosAgentesPoliticos->setExportador( $obExportador );
                        $obFuncionariosAgentesPoliticos->geraArquivo();

                        $arFiltroRelatorio['nro_sequencial']++;
                        Sessao::write('filtroRelatorio', $arFiltroRelatorio);

                        $rsInformacoesPagamento = buscaInformacoesPagamento( $arDadosFuncPol['cod_contrato'], $arDadosLotacionograma, $stPeriodosInicial, $stPeriodosFinal );

                        //foreach dos elementos encontrados para a pesquisa do lotacionograma acima
                        if (is_array($rsInformacoesPagamento)) {
                            foreach ($rsInformacoesPagamento as $chave => $arDadosInfPagamento) {

                                $arDadosInfPagamento['nro_sequencial'] = $arFiltroRelatorio['nro_sequencial'];

                                //monta os dados no arquivo das informações financeiras de acordo com o array de dados
                                $obInfPagamento = new InformacoesPagamento();
                                $obInfPagamento->setDados( $arDadosInfPagamento );
                                $obInfPagamento->setExportador( $obExportador );
                                $obInfPagamento->geraArquivo();

                                $arFiltroRelatorio['nro_sequencial']++;
                                Sessao::write('filtroRelatorio', $arFiltroRelatorio);
                            }
                        }
                    }
                }
            }
        }
    }
    montaRodapeArquivo($obExportador);

/**
 *buscaCodEntidade
 *Busca os codigos das entidades selecionadas pelo usuário
 *
 *@param int numCgm numero do cgm da entidade
 *@return int cod_entidade retorna o codigo da entidade
 */
function buscaCodEntidade($numCgm)
{
    $stCondicao = "\n AND c.numcgm=".$numCgm;

    $obTOrcamentoEntidade = new TOrcamentoEntidade();
    $obTOrcamentoEntidade->setDado("exercicio",Sessao::getExercicio());
    $obTOrcamentoEntidade->recuperaEntidades($rsEntidades,$stCondicao);

    return $rsEntidades->getCampo("cod_entidade");
}

/**
 *buscaIdentificacaoInformacoes
 *faz a busca dos dados para informar a identificação das informações
 *
 *@param void
 *@return RecordSet retorna o recordSet dos dados da Identificação
 */
function buscaIdentificacaoInformacoes()
{
    $arFiltroRelatorio = Sessao::read('filtroRelatorio');

    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado( 'modulo'   , 48 ); // 48-TCM - PA
    $obTAdministracaoConfiguracao->setDado( 'parametro', 'tc_cod_municipio' );
    $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
    $obTAdministracaoConfiguracao->recuperaPorChave( $rsAdministracaoConfiguracao );

    $rsAdministracaoConfiguracao->arElementos[0]['tipo_registro']  = '000';
    $rsAdministracaoConfiguracao->arElementos[0]['nro_sequencial'] = $arFiltroRelatorio['nro_sequencial'];
    $rsAdministracaoConfiguracao->arElementos[0]['versao_layout']  = 'FPG200600';
    $rsAdministracaoConfiguracao->arElementos[0]['codigo_tcm']     = $arFiltroRelatorio['inCodOrgaoResponsavel'];
    $rsAdministracaoConfiguracao->arElementos[0]['exercicio']      = Sessao::getExercicio();
    $rsAdministracaoConfiguracao->arElementos[0]['competencia']    = '4'.$arFiltroRelatorio['inQuadrimestre'];
    $rsAdministracaoConfiguracao->arElementos[0]['data_geracao']   = date('dmY');
    $rsAdministracaoConfiguracao->arElementos[0]['retificadora']   = $arFiltroRelatorio['inRetificadora'];
    $rsAdministracaoConfiguracao->arElementos[0]['uso_orgao']      = $arFiltroRelatorio['stUsoOrgao'];
    $rsAdministracaoConfiguracao->arElementos[0]['fim_registro']   = '*';

    return $rsAdministracaoConfiguracao;
}

/**
 *faz a busca dos dados das unidades gestoras
 *
 *@param int codEntidade O codigo da entidade
 *@return RecordSet Informações da unidade gestora
 */
function buscaUnidadeGestora($inCodEntidade)
{
    $stCondicao = "\n AND conf_unidade_gestora.cod_entidade=".$inCodEntidade;

    $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
    $obTAdministracaoConfiguracaoEntidade->setDado('exercicio', Sessao::getExercicio());
    $obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade', $inCodEntidade);
    $obTAdministracaoConfiguracaoEntidade->recuperaUnidadesGestoras( $rsAdministracaoConfiguracaoEntidade, $stCondicao );

    return $rsAdministracaoConfiguracaoEntidade;
}

/**
 *faz a busca das unidades orçamentárias
 *
 *@param int codEntidade O codigo da entidade
 *@return RecordSet Informações da unidade Orçamentaria
 */
function buscaUnidadeOrcamentaria($inCodEntidade)
{
    $obTTPAUnidadeOrcamentaria = new TTPAUnidadeOrcamentaria();
    $obTTPAUnidadeOrcamentaria->setDado( 'cod_entidade', $inCodEntidade );
    $obTTPAUnidadeOrcamentaria->setDado( 'exercicio', Sessao::getExercicio() );
    $obTTPAUnidadeOrcamentaria->recuperaListagemOrgaosOrcamentarios( $rsUnidadeOrcamentaria, $stCondicao );

    return $rsUnidadeOrcamentaria;
}

/**
 *buscaLotacionograma
 *faz a busca do lotacionograma( informações de cargos e subdivisões configuradas pelo cliente)
 *
 *@return Recordset array de registros com informações do lotacionograma
 */
function buscaLotacionograma()
{
    $obTTPALotacao= new TTPAlotacao();
    $obTTPALotacao->recuperaLotacionograma( $rsPessoalContratoServidor );

    return $rsPessoalContratoServidor;
}

/**
 *buscaFuncionariosAgentesPoliticos
 * faz a busca dos funcionários e agentes políticos de acordo com o orgao e cargo ocupado
 *
 *@param integer $inCodOrgao codigo do orgao
 *@param array $dadosLotacionograma dados dos cargos pertencentes a esse orgao
 *@param string $stPeriodoInicial data inicial
 *@param string $stPeriodoFinal data final
 *
 *@return recordSet array de registros do funcionarios e agentes politicos
 *
 */
function buscaFuncionariosAgentesPoliticos($inCodOrgao, $dadosLotacionograma, $stPeriodosInicial, $stPeriodosFinal)
{
    $obTTPATipoCargo = new TTPATipoCargo();

    $obTTPATipoCargo->setDado('cod_orgao', $inCodOrgao );
    $obTTPATipoCargo->setDado('cod_cargo', $dadosLotacionograma['cod_cargo'] );
    $obTTPATipoCargo->setDado('cod_regime', $dadosLotacionograma['cod_regime'] );
    $obTTPATipoCargo->setDado('cod_sub_divisao', $dadosLotacionograma['cod_sub_divisao'] );
    $obTTPATipoCargo->setDado('dt_final', $stPeriodosFinal );
    $obTTPATipoCargo->setDado('dt_inicial', $stPeriodosInicial );
    $obTTPATipoCargo->recuperaDadosFuncionariosAgentesPoliticos($rsPessoalContratoServidor, $stFiltro, $stOrdem);

    return $rsPessoalContratoServidor;
}

/**
 *buscaInformacoesPagamento
 * Busca as informações de pagamento dos agentes e funcionarios de acordo com as informações
 * fornecidas no lotacionograma
 *
 *@param integer $codContrato Codigo do contrato do agente/funcionario
 *@param array $dadosLotacao array com as informações do lotacionograma em que pertence o agente/funcionario
 *@param string $stPeriodoInicial data inicial
 *@param string $stPeriodoFinal data final
 *
 *@return RecordSet array de registros com as informações dos pagamentos aos funcionarios
 *
 */
function buscaInformacoesPagamento($codContrato, $dadosLotacao, $stPeriodosInicial, $stPeriodosFinal)
{
    $obTTPATipoRemuneracao = new TTPATipoRemuneracao();
    //$obTPessoalContratoServidor = new TPessoalContratoServidor();
    $obTTPASituacaoFuncional = new TTPASituacaoFuncional();

    //recupera a situacao funcional do fucionario segundo seu lotacionograma
    $stFiltroCodCargo ="\n      , tcmpa.lotacao";
    $stFiltroCodCargo.="\n  where lotacao.cod_cargo = ".$dadosLotacao['cod_cargo'];
    $stFiltroCodCargo.="\n    and lotacao.cod_sub_divisao = ".$dadosLotacao['cod_sub_divisao'];
    $stFiltroCodCargo.="\n    and lotacao.cod_regime = ".$dadosLotacao['cod_regime'];
    $stFiltroCodCargo.="\n    and lotacao.cod_situacao = situacao_funcional.cod_situacao";

    $obTTPASituacaoFuncional->recuperaListagemSituacaoFuncional($rsCodSituacao,$stFiltroCodCargo);

    $situacao = $rsCodSituacao->getCampo('cod_situacao');

    // recupera as datas de competencia do servidor
    $stFiltroPagamento = "\n where to_char(periodo_movimentacao.dt_final, 'yyyymm') BETWEEN '".$stPeriodosInicial."' AND '".$stPeriodosFinal."'";
    $obTTPATipoRemuneracao->recuperaDataPagamento($rsPeriodoMovimentacao,$stFiltroPagamento);

    //recupera todos os eventos para esse arquivo de acordo com as configurações de tipo de remuneracao tcmpa
    $stOrdemEventos ="\n order by codigo";
    $obTTPATipoRemuneracao->recuperaEventos($rsEventos,"",$stOrdemEventos);

    // buscar os codigos dos eventos da configuração do tcmpa
    while ( !$rsEventos->eof()) {

        if ($rsEventos->getCampo('codigo') == 1) {
            $eventosCalculadosRemuneracaoBase[] = $rsEventos->getCampo('cod_evento');
        } elseif ($rsEventos->getCampo('codigo') == 2) {
            $eventosCalculadosGratificacaoFuncao[] = $rsEventos->getCampo('cod_evento');
        } elseif ($rsEventos->getCampo('codigo') == 3) {
            $eventosCalculadosOutrasRemuneracao[] = $rsEventos->getCampo('cod_evento');
        }
        $rsEventos->proximo();
    }

    $indice = 0;

    while (!$rsPeriodoMovimentacao->eof()) {

        //Pega todos os eventos calculados (salarial)
        $stFiltroEventosCalculados = " AND cod_contrato=".$codContrato;
        $stFiltroEventosCalculados .=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');

        $obTTPATipoRemuneracao->recuperaEventosCalculados($rsEventosCalculados,$stFiltroEventosCalculados);

        //Pega todos os eventos calculados de adiantamento de decimo
        $stFiltroEventoDecimoCalculadoAdiantamento = " AND cod_contrato=".$codContrato;
        $stFiltroEventoDecimoCalculadoAdiantamento.=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltroEventoDecimoCalculadoAdiantamento.=" AND evento_decimo_calculado.desdobramento = 'A'";

        $obTTPATipoRemuneracao->recuperaEventosDecimoCalculados($rsEventoDecimoCalculadosAdiantamento,$stFiltroEventoDecimoCalculadoAdiantamento);

        //Pega todos os eventos calculados de decimo
        $stFiltroEventoDecimoCalculado = " AND cod_contrato=".$codContrato;
        $stFiltroEventoDecimoCalculado .=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltroEventoDecimoCalculado .=" AND evento_decimo_calculado.desdobramento = 'D'";

        $obTTPATipoRemuneracao->recuperaEventosDecimoCalculados($rsEventoDecimoCalculados,$stFiltroEventoDecimoCalculado);

        //Pega todos os eventos calculados de ferias
        $stFiltroEventoFeriasCalculado = " AND cod_contrato=".$codContrato;
        $stFiltroEventoFeriasCalculado .=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');

        $obTTPATipoRemuneracao->recuperaEventosFeriasCalculados($rsEventoFeriasCalculados,$stFiltroEventoFeriasCalculado);

        //Pega todos os eventos calculados de complementar com cod_configuracao <> 3
        $stFiltroEventoComplementarCalculado = "AND cod_contrato=".$codContrato;
        $stFiltroEventoComplementarCalculado.=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltroEventoComplementarCalculado.=" AND evento_complementar_calculado.cod_configuracao <> 3";

        $obTTPATipoRemuneracao->recuperaEventosComplementarCalculados($rsEventoComplementarCalculados,$stFiltroEventoComplementarCalculado);

        //Pega todos os eventos calculados de recisao com desdobramento <> D
        $stFiltroEventoRecisaoCalculado = "AND cod_contrato=".$codContrato;
        $stFiltroEventoRecisaoCalculado.=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltroEventoRecisaoCalculado.=" AND evento_rescisao_calculado.desdobramento <> 'D' ";

        $obTTPATipoRemuneracao->recuperaEventosRecisaoCalculados($rsEventoRecisaoCalculados,$stFiltroEventoRecisaoCalculado);

        //Pega todos os eventos calculados complementares que tem cod_configuracao = 3
        $stFiltroEventoComplementarCalculado = "AND cod_contrato=".$codContrato;
        $stFiltroEventoComplementarCalculado.=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltroEventoComplementarCalculado.=" AND evento_complementar_calculado.cod_configuracao = 3";

        $obTTPATipoRemuneracao->recuperaEventosComplementarCalculados($rsEventoComplementarCalculadosAdiantamento,$stFiltroEventoComplementarCalculado);

        //Pega todos os eventos calculados recisao que tem desdobramento = D
        $stFiltroEventoRecisaoCalculado = "AND cod_contrato=".$codContrato;
        $stFiltroEventoRecisaoCalculado.=" AND cod_periodo_movimentacao=".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltroEventoRecisaoCalculado.=" AND evento_rescisao_calculado.desdobramento = 'D' ";

        $obTTPATipoRemuneracao->recuperaEventosRecisaoCalculados($rsEventoRecisaoCalculadosDecimo,$stFiltroEventoRecisaoCalculado);

        // recupera o cod de evento de irrf
        $stFiltroEventoIrrf = "\n AND tabela_irrf_evento.cod_tipo in (3,6)";

        $obTTPATipoRemuneracao->recuperaCodEventoIrrf($rsEventoIrrf,$stFiltroEventoIrrf);

        $arCodEventosIrrf = $rsEventoIrrf->arElementos;

        //Pega todos os eventos de previdencia do funcionario
        $obTTPATipoRemuneracao->setDado('cod_contrato', $codContrato);
        $obTTPATipoRemuneracao->recuperaCodEventoPrevidencia($rsCodEventoPrevidencia);

        $inCodEventoPrevidencia = $rsCodEventoPrevidencia->getCampo('cod_evento');

        //Recupera o numero de dependentes do contrato informado
        $obTTPATipoRemuneracao->setDado('cod_contrato',$codContrato);
        $obTTPATipoRemuneracao->setDado('dt_final',$stPeriodosFinal);
        $obTTPATipoRemuneracao->recuperaNumeroDependentesIr($rsDependentes);

        $numeroDependentes = $rsDependentes->getCampo('qtd_dependentes');

        $nuTotalRemuneracaoBase = 0;
        $nuTotalAdiantamentoDecimo = 0;
        $nuTotalFinalDecimo = 0;
        $nuTotalDescontos = 0;
        $nuTotalDescontosPrevidencia = 0;
        $nuTotalIrrf = 0;

        if ( ( $rsEventosCalculados->getNumLinhas() >0) || ($rsEventoFeriasCalculados->getNumLinhas() >0) || ($rsEventoComplementarCalculados->getNumLinhas()>0) || ($rsEventoRecisaoCalculados->getNumLinhas()>0)) {

            //folha salarial
            while (!$rsEventosCalculados->eof()) {
                //Remuneração Base
                if (in_array($rsEventosCalculados->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBase += $rsEventosCalculados->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventosCalculados->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimo += $rsEventosCalculados->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventosCalculados->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimo += $rsEventosCalculados->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventosCalculados->getCampo("natureza") == "D") {
                    $nuTotalDescontos += $rsEventosCalculados->getCampo("valor");
                }

                //total descontos previdencia do funcionario
                if ($rsEventosCalculados->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidencia += $rsEventosCalculados->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventosCalculados->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrf += $rsEventosCalculados->getCampo("valor");
                }
                $rsEventosCalculados->proximo();
            }

            //folha ferias
            while (!$rsEventoFeriasCalculados->eof()) {
                //Remuneração Base
                if (in_array($rsEventoFeriasCalculados->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBase += $rsEventoFeriasCalculados->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventoFeriasCalculados->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimo += $rsEventoFeriasCalculados->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventoFeriasCalculados->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimo += $rsEventoFeriasCalculados->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventoFeriasCalculados->getCampo("natureza") == "D") {
                    $nuTotalDescontos += $rsEventoFeriasCalculados->getCampo("valor");
                }
                //total descontos previdencia do funcionario
                if ($rsEventoFeriasCalculados->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidencia += $rsEventoFeriasCalculados->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventoFeriasCalculados->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrf += $rsEventoFeriasCalculados->getCampo("valor");
                }
                $rsEventoFeriasCalculados->proximo();
            }

            //folha complementar
            while (!$rsEventoComplementarCalculados->eof()) {
                //Remuneração Base
                if (in_array($rsEventoComplementarCalculados->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBase += $rsEventoComplementarCalculados->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventoComplementarCalculados->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimo += $rsEventoComplementarCalculados->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventoComplementarCalculados->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimo += $rsEventoComplementarCalculados->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventoComplementarCalculados->getCampo("natureza") == "D") {
                    $nuTotalDescontos += $rsEventoComplementarCalculados->getCampo("valor");
                }
                //total descontos previdencia do funcionario
                if ($rsEventoComplementarCalculados->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidencia += $rsEventoComplementarCalculados->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventoComplementarCalculados->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrf += $rsEventoComplementarCalculados->getCampo("valor");
                }
                $rsEventoComplementarCalculados->proximo();
            }

            //folha recisao
            while (!$rsEventoRecisaoCalculados->eof()) {
                //Remuneração Base
                if (in_array($rsEventoRecisaoCalculados->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBase += $rsEventoRecisaoCalculados->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventoRecisaoCalculados->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimo += $rsEventoRecisaoCalculados->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventoRecisaoCalculados->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimo += $rsEventoRecisaoCalculados->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventoRecisaoCalculados->getCampo("natureza") == "D") {
                    $nuTotalDescontos += $rsEventoRecisaoCalculados->getCampo("valor");
                }
                //total descontos previdencia do funcionario
                if ($rsEventoRecisaoCalculados->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidencia += $rsEventoRecisaoCalculados->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventoRecisaoCalculados->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrf += $rsEventoRecisaoCalculados->getCampo("valor");
                }
                $rsEventoRecisaoCalculados->proximo();
            }

            $arInformacaoPagamento[$indice]['tipo_pagamento'] = 1;
            $arInformacaoPagamento[$indice]['remuneracaoBase'] = $nuTotalRemuneracaoBase;
            $arInformacaoPagamento[$indice]['gratificacaoFuncao'] = $nuTotalAdiantamentoDecimo;
            $arInformacaoPagamento[$indice]['outraRemuneracao'] = $nuTotalFinalDecimo;
            $arInformacaoPagamento[$indice]['dataPagamento'] = $rsPeriodoMovimentacao->getCampo("dt_final");
            $arInformacaoPagamento[$indice]['contribuicaoPrevidencia'] = $nuTotalDescontosPrevidencia;
            $arInformacaoPagamento[$indice]['descontos'] = $nuTotalDescontos;
            $arInformacaoPagamento[$indice]['irrf'] = $nuTotalIrrf;
            $arInformacaoPagamento[$indice]['dependentesIr'] = $numeroDependentes;
            $arInformacaoPagamento[$indice]['cargo'] = $dadosLotacao['cod_cargo'];
            $arInformacaoPagamento[$indice]['fundef'] = 1;
            $arInformacaoPagamento[$indice]['situacao'] = $situacao;

            $indice++;
        }

        $nuTotalRemuneracaoBaseAdiantamento = 0;
        $nuTotalAdiantamentoDecimoAdiantamento = 0;
        $nuTotalFinalDecimoAdiantamento = 0;
        $nuTotalDescontosAdiantamento = 0;
        $nuTotalDescontosPrevidenciaAdiantamento = 0;
        $nuTotalIrrfAdiantamento = 0;

        if ($rsEventoDecimoCalculadosAdiantamento->getNumLinhas() >0) {

            //folha adiantamento de decimo
            while (!$rsEventoDecimoCalculadosAdiantamento->eof()) {
                //Remuneração Base
                if (in_array($rsEventoDecimoCalculadosAdiantamento->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBaseAdiantamento += $rsEventoDecimoCalculadosAdiantamento->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventoDecimoCalculadosAdiantamento->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimoAdiantamento += $rsEventoDecimoCalculadosAdiantamento->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventoDecimoCalculadosAdiantamento->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimoAdiantamento += $rsEventoDecimoCalculadosAdiantamento->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventoDecimoCalculadosAdiantamento->getCampo("natureza") == "D") {
                    $nuTotalDescontosAdiantamento += $rsEventoDecimoCalculadosAdiantamento->getCampo("valor");
                }
                //total descontos previdencia do funcionario
                if ($rsEventoDecimoCalculadosAdiantamento->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidenciaAdiantamento += $rsEventoDecimoCalculadosAdiantamento->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventoDecimoCalculadosAdiantamento->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrfAdiantamento += $rsEventoDecimoCalculadosAdiantamento->getCampo("valor");
                }
                $rsEventoDecimoCalculadosAdiantamento->proximo();
            }

            $arInformacaoPagamento[$indice]['tipo_pagamento'] = 2;
            $arInformacaoPagamento[$indice]['remuneracaoBase'] = $nuTotalRemuneracaoBaseAdiantamento;
            $arInformacaoPagamento[$indice]['gratificacaoFuncao'] = $nuTotalAdiantamentoDecimoAdiantamento;
            $arInformacaoPagamento[$indice]['outraRemuneracao'] = $nuTotalFinalDecimoAdiantamento;
            $arInformacaoPagamento[$indice]['dataPagamento'] = $rsPeriodoMovimentacao->getCampo("dt_final");
            $arInformacaoPagamento[$indice]['contribuicaoPrevidencia'] = $nuTotalDescontosPrevidenciaAdiantamento;
            $arInformacaoPagamento[$indice]['descontos'] = $nuTotalDescontosAdiantamento;
            $arInformacaoPagamento[$indice]['irrf'] = $nuTotalIrrfAdiantamento;
            $arInformacaoPagamento[$indice]['dependentesIr'] = $numeroDependentes;
            $arInformacaoPagamento[$indice]['cargo'] = $dadosLotacao['cod_cargo'];
            $arInformacaoPagamento[$indice]['fundef'] = 1;
            $arInformacaoPagamento[$indice]['situacao'] = $situacao;

            $indice++;
        }

        $nuTotalRemuneracaoBaseDecimoFinal = 0;
        $nuTotalAdiantamentoDecimoFinal = 0;
        $nuTotalFinalDecimoFinal = 0;
        $nuTotalDescontosDecimoFinal = 0;
        $nuTotalDescontosPrevidenciaDecimo = 0;
        $nuTotalIrrfDecimo = 0;

        if( ( $rsEventoDecimoCalculados->getNumLinhas() >0) || ($rsEventoComplementarCalculados->getNumLinhas() >0) || ($rsEventoRecisaoCalculados->getNumLinhas()>0)
            || ($rsEventoRecisaoCalculadosDecimo->getNumLinhas()>0) || ($rsEventoComplementarCalculadosAdiantamento->getNumLinhas()>0)) {

            //folha de decimo
            while (!$rsEventoDecimoCalculados->eof()) {
                //Remuneração Base
                if (in_array($rsEventoDecimoCalculados->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBaseDecimoFinal += $rsEventoDecimoCalculados->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventoDecimoCalculados->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimoFinal += $rsEventoDecimoCalculados->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventoDecimoCalculados->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimoFinal += $rsEventoDecimoCalculados->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventoDecimoCalculados->getCampo("natureza") == "D") {
                    $nuTotalDescontosDecimoFinal += $rsEventoDecimoCalculados->getCampo("valor");
                }
                //total descontos previdencia do funcionario
                if ($rsEventoDecimoCalculados->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidenciaDecimo += $rsEventoDecimoCalculados->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventoDecimoCalculados->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrfDecimo += $rsEventoDecimoCalculados->getCampo("valor");
                }
                $rsEventoDecimoCalculados->proximo();
            }

            //folha complementar com adiantamento de decimo
            while (!$rsEventoComplementarCalculadosAdiantamento->eof()) {
                //Remuneração Base
                if (in_array($rsEventoComplementarCalculadosAdiantamento->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBaseDecimoFinal += $rsEventoComplementarCalculadosAdiantamento->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventoComplementarCalculadosAdiantamento->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimoFinal += $rsEventoComplementarCalculadosAdiantamento->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventoComplementarCalculadosAdiantamento->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimoFinal += $rsEventoComplementarCalculadosAdiantamento->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventoComplementarCalculadosAdiantamento->getCampo("natureza") == "D") {
                    $nuTotalDescontosDecimoFinal += $rsEventoComplementarCalculadosAdiantamento->getCampo("valor");
                }
                //total descontos previdencia do funcionario
                if ($rsEventoComplementarCalculadosAdiantamento->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidenciaDecimo += $rsEventoComplementarCalculadosAdiantamento->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventoComplementarCalculadosAdiantamento->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrfDecimo += $rsEventoComplementarCalculadosAdiantamento->getCampo("valor");
                }
                $rsEventoComplementarCalculadosAdiantamento->proximo();
            }

            //folha recisao com o desdobramento de decimo
            while (!$rsEventoRecisaoCalculadosDecimo->eof()) {
                //Remuneração Base
                if (in_array($rsEventoRecisaoCalculadosDecimo->getCampo("cod_evento"),$eventosCalculadosRemuneracaoBase)) {
                    $nuTotalRemuneracaoBaseDecimoFinal += $rsEventoRecisaoCalculadosDecimo->getCampo("valor");
                }
                //Gratificação de Função
                if (in_array($rsEventoRecisaoCalculadosDecimo->getCampo("cod_evento"),$eventosCalculadosGratificacaoFuncao)) {
                    $nuTotalAdiantamentoDecimoFinal += $rsEventoRecisaoCalculadosDecimo->getCampo("valor");
                }
                //Outras Remunerações
                if (in_array($rsEventoRecisaoCalculadosDecimo->getCampo("cod_evento"),$eventosCalculadosOutrasRemuneracao)) {
                    $nuTotalFinalDecimoFinal += $rsEventoRecisaoCalculadosDecimo->getCampo("valor");
                }
                //Total de Desconto
                if ($rsEventoRecisaoCalculadosDecimo->getCampo("natureza") == "D") {
                    $nuTotalDescontosDecimoFinal += $rsEventoRecisaoCalculadosDecimo->getCampo("valor");
                }
                //total descontos previdencia do funcionario
                if ($rsEventoRecisaoCalculadosDecimo->getCampo("cod_evento") == $inCodEventoPrevidencia) {
                    $nuTotalDescontosPrevidenciaDecimo += $rsEventoRecisaoCalculadosDecimo->getCampo("valor");
                }
                //total de imposto de renda retido na fonte
                if (in_array($rsEventoRecisaoCalculadosDecimo->getCampo("cod_evento"),$arCodEventosIrrf)) {
                    $nuTotalIrrfDecimo += $rsEventoRecisaoCalculadosDecimo->getCampo("valor");
                }
                $rsEventoRecisaoCalculadosDecimo->proximo();
            }

            $arInformacaoPagamento[$indice]['tipo_pagamento'] = 3;
            $arInformacaoPagamento[$indice]['remuneracaoBase'] = $nuTotalRemuneracaoBaseDecimoFinal;
            $arInformacaoPagamento[$indice]['gratificacaoFuncao'] = $nuTotalAdiantamentoDecimoFinal;
            $arInformacaoPagamento[$indice]['outraRemuneracao'] = $nuTotalFinalDecimoFinal;
            $arInformacaoPagamento[$indice]['dataPagamento'] = $rsPeriodoMovimentacao->getCampo("dt_final");
            $arInformacaoPagamento[$indice]['contribuicaoPrevidencia'] = $nuTotalDescontosPrevidenciaDecimo;
            $arInformacaoPagamento[$indice]['descontos'] = $nuTotalDescontosDecimoFinal;
            $arInformacaoPagamento[$indice]['irrf'] = $nuTotalIrrfDecimo;
            $arInformacaoPagamento[$indice]['dependentesIr'] = $numeroDependentes;
            $arInformacaoPagamento[$indice]['cargo'] = $dadosLotacao['cod_cargo'];
            $arInformacaoPagamento[$indice]['fundef'] = 1;
            $arInformacaoPagamento[$indice]['situacao'] = $situacao;

            $indice++;
        }
        $rsPeriodoMovimentacao->proximo();
    }

    return $arInformacaoPagamento;
}

/**
 *montaRodapeArquivo
 * Monta rodapé do arquivo quando terminanado os registros do mesmo
 *
 *@param object $exportador
 *@return void
 */
function montaRodapeArquivo($exportador)
{
    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $arDadosFimArquivo['nro_sequencial'] = $arFiltroRelatorio['nro_sequencial'];

    $obRodapeFimArquivo = new rodapeFimArquivo();
    $obRodapeFimArquivo->setDados( $arDadosFimArquivo );
    $obRodapeFimArquivo->setExportador( $exportador );
    $obRodapeFimArquivo->geraArquivo();
}
