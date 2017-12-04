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
    * Novo formulário para inclusão de despesa, agora utilizando ação
    * Data de Criação   : 17/08/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TPB_MAPEAMENTO."TTCEPBModalidadeDespesa.class.php";
include_once CAM_GPC_TPB_MAPEAMENTO."TTCEPBOrcamentoModalidadeDespesa.class.php";
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEModalidadeDespesa.class.php";
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEOrcamentoModalidadeDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEspecificacaoDestinacaoRecurso.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecursoDestinacao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecurso.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoConfiguracao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoDespesaAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'DespesaAcao';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$obROrcamentoDespesa = new ROrcamentoDespesa;
$obTOrcamentoRecurso = new TOrcamentoRecurso;
$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
$obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
$obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;

function verificaValoresMetas()
{
    $obErro = new Erro;

    $inNumColunas    = $_REQUEST['inNumCampos'];
    $vlValorOriginal = $_REQUEST['nuValorOriginal'];

    for ($inContColunas = 1; $inContColunas <= $inNumColunas; $inContColunas++) {
        $inValor = 'vlValor_'.$inContColunas;

        $inValor = str_replace('.', '', $_REQUEST[$inValor]);
        $inValor = str_replace(',', '.', $inValor);
        $arValor[$inContColunas] = $inValor;
        $vlTotal += $inValor;
    }

    $vlTotal = $_REQUEST['TotalValor'];
    $vlTotal = str_replace('.', '', $vlTotal);
    $vlTotal = str_replace(',', '.', $vlTotal);

    $vlValorOriginal = str_replace('.', '', $vlValorOriginal);
    $vlValorOriginal = str_replace(',', '.', $vlValorOriginal);

    if ($vlTotal > 0) {
        if (number_format($vlTotal,2,'.','') > number_format($vlValorOriginal,2,'.','')) {
            $obErro->setDescricao('Total da despesa ultrapassou o valor da dotação orçamentária.');
        } elseif (number_format($vlTotal,2,'.','') < number_format($vlValorOriginal,2,'.','')) {
            $obErro->setDescricao('Total da despesa é inferior ao valor da dotação orçamentária.');
        }
    }

    return $obErro;
}

function verificaValorConta($stClassificacao)
{
    $obROrcamentoDespesa = new ROrcamentoDespesa;
    $arClassDespesa = preg_split("/[^a-zA-Z0-9]/", $stClassificacao);
    $inCount        = count($arClassDespesa);

    //busca a posicao do ultimo valor na string de classificacao
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassDespesa[$inPosicao] != 0) {
            break;
        }
    }

    for ($i = 0; $i <= $inPosicao; $i++) {
        $stClassFilha .= $arClassDespesa[$i].".";
    }
    $stClassFilha = substr($stClassFilha, 0, strlen($stClassFilha) - 1);
    $stClassFilhaCompleta = Mascara::validaMascaraDinamica($_REQUEST['stMascClassificacao'], $stClassFilha);

    //monta as classificacoes pai da classificao informada
    $inPosicaoCount = $inPosicao;
    while ($inPosicao > 0) {
        $stClassPai = '';
        for ($i = 0; $i < $inPosicaoCount; $i++) {
            $stClassPai .= $arClassDespesa[$i].".";
        }
        $stClassPai = substr($stClassPai, 0, strlen($stClassPai) - 1);
        $stClassPai = Mascara::validaMascaraDinamica($_REQUEST['stMascClassificacao'], $stClassPai);
        $arClassPai[] = $stClassPai[1];
        $inPosicaoCount--;
        $inPosicao--;
    }

    //monta filtro da consulta com as classificacoes pai montadas
    if (is_array($arClassPai)) {
        foreach ($arClassPai as $key => $valor) {
            $stFiltro .= "'".$valor."',";
        }
    }

    $stFiltro = substr( $stFiltro, 0, strlen( $stFiltro ) - 1 );
    if ($stFiltro) {
        $stFiltro  = " AND ( classificacao IN ( ".$stFiltro.") OR ";
    } else {
        $stFiltro  = " AND ( ";
    }
    $stFiltro .= " classificacao like '".$stClassFilha."%' )";
    $stFiltro .= " AND classificacao <> '".$stClassFilhaCompleta[1]."'";

    $obROrcamentoDespesa->verificaValorConta($inSumConta, $stFiltro);

    return $inSumConta;
}

$stAcao = $request->get('stAcao');

$obErro = new Erro;

$obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
$obTOrcamentoConfiguracao->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoConfiguracao->setDado('parametro', 'recurso_destinacao');
$obTOrcamentoConfiguracao->consultar();
if ($obTOrcamentoConfiguracao->getDado('valor') == 'true') { // Utilização da Destinação de Recursos || 2008 em diante
    $boDestinacao = true;
}

switch ($stAcao) {
case 'incluir':
    $obErro = new Erro;
    $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento($stFormaExecucao);
    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($_REQUEST['inCodDespesa']);
    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->buscaNivelConta($rsNivel, $boTransacao);

    if ($stFormaExecucao == 1) {
        if ($rsNivel->getCampo('nivel_conta') > 5) {
            $obErro->setDescricao('Detalhamento permitido somente na Execução!');
        }
    } else {
        if ($rsNivel->getCampo('nivel_conta') < 6) {
            $obErro->setDescricao('A Rubrica de Despesa deve ser desdobrada!');
        }
    }

    $inSumConta = verificaValorConta($_REQUEST['inCodDespesa']);
    if ($inSumConta > 0) {
        $obErro->setDescricao('Verificar Código da Despesa (Nível anterior/posterior já cadastrado)');
    } else {
        if ($boDestinacao && !$obErro->ocorreu()) {
            $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

            $stFiltroBuscaExiste  = " WHERE exercicio = ".Sessao::getExercicio();
            $stFiltroBuscaExiste .= "   AND cod_uso = ".$arDestinacaoRecurso[0];
            $stFiltroBuscaExiste .= "   AND cod_destinacao = ".$arDestinacaoRecurso[1];
            $stFiltroBuscaExiste .= "   AND cod_especificacao = ".$arDestinacaoRecurso[2];
            $stFiltroBuscaExiste .= "   AND cod_detalhamento = ".$arDestinacaoRecurso[3];
            $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
            $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

            if ($inCodRecursoExiste == '') {
                $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                $obTOrcamentoRecurso->proximoCod( $inCodRecurso );
                $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                if (!$obErro->ocorreu()) {
                    $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio() );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", $inCodRecurso          );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_uso", $arDestinacaoRecurso[0]);
                    $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", $arDestinacaoRecurso[1]);
                    $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]);
                    $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]);
                    $obErro = $obTOrcamentoRecursoDestinacao->inclusao($boTransacao);

                    $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso ( $inCodRecurso );
                }

                if (Sessao::getExercicio() > '2008') {
                    $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio', Sessao::getExercicio());
                    $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                    $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                    $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                    // Verifica qual o cod_recurso que possui conta contabil vinculada C
                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                    $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", '');
                    $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                    $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                    $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC);

                    $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                    if ($inCodRecursoBuscaC == '') {
                        include CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';

                        if (!$obErro->ocorreu()) {
                            $obRContabilidadePlanoBancoC = new RContabilidadePlanoBanco;
                            $obRContabilidadePlanoBancoC->setCodEstrutural('2.9.3.2.0.00.00.');
                            $obRContabilidadePlanoBancoC->getProximoEstruturalRecurso($rsProxCod, $boTransacao);
                            $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                            if ($inProximoCodEstruturalC != 99) {
                                $obRContabilidadePlanoBancoC->obRContabilidadeSistemaContabil->setCodSistema(4);
                                $obRContabilidadePlanoBancoC->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                $inProximoCodEstruturalC++;
                                $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                                $obRContabilidadePlanoBancoC->setCodEstrutural($stCodEstruturalC);
                                $obRContabilidadePlanoBancoC->setNomConta($stNomEspecificacao);
                                $obRContabilidadePlanoBancoC->setExercicio(Sessao::getExercicio());
                                $obRContabilidadePlanoBancoC->setNatSaldo('C');
                                $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                $obRContabilidadePlanoBancoC->setContaAnalitica(true);

                                $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao, false);
                            } else {
                                SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                            }
                        }
                    }

                    // Verifica qual o cod_recurso que possui conta contabil vinculada D
                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'1.9.3.2.0.00.00.%'");
                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoD, '', '', $boTransacao);

                    $inCodRecursoBuscaD = $rsContaRecursoD->getCampo('cod_recurso');

                    if ($inCodRecursoBuscaD == '') {
                        if (!$obErro->ocorreu()) {
                            $obRContabilidadePlanoBancoD = new RContabilidadePlanoBanco;
                            $obRContabilidadePlanoBancoD->setCodEstrutural('1.9.3.2.0.00.00.');
                            $obRContabilidadePlanoBancoD->getProximoEstruturalRecurso($rsProxCodD, $boTransacao);
                            $inProximoCodEstruturalD = $rsProxCodD->getCampo('prox_cod_estrutural');
                            if ($inProximoCodEstruturalD != 99) {
                                $obRContabilidadePlanoBancoD->obRContabilidadeSistemaContabil->setCodSistema(4);
                                $obRContabilidadePlanoBancoD->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                $inProximoCodEstruturalD++;
                                $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, '0', STR_PAD_LEFT);
                                $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                                $obRContabilidadePlanoBancoD->setCodEstrutural($stCodEstruturalD);
                                $obRContabilidadePlanoBancoD->setNomConta($stNomEspecificacao);
                                $obRContabilidadePlanoBancoD->setExercicio(Sessao::getExercicio());
                                $obRContabilidadePlanoBancoD->setNatSaldo('D');
                                $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                $obRContabilidadePlanoBancoD->setContaAnalitica(true);

                                $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao);
                            } else {
                                SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                            }
                        }
                    }
                }
            } else {
                $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
            }
        } else {
            $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($_REQUEST['inCodRecurso']);
            $obROrcamentoDespesa->obROrcamentoRecurso->setExercicio(Sessao::getExercicio());
            $obROrcamentoDespesa->obROrcamentoRecurso->listar($rsRecurso, '',$boTransacao);
            if ($rsRecurso->getNumLinhas() <= 0 && !$obErro->ocorreu()) {
                $obErro->setDescricao('Recurso inválido!');
            }
        }
    }

    if (!$obErro->ocorreu()) {
        //busca o codigo da conta da Classificação de Despesa informada
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->consultar($rsRubrica);

        $inCodConta = $rsRubrica->getCampo('cod_conta');
        //busca cod_ograo
        $arCodOrgao   = explode('-', $_REQUEST['inCodOrgao']);
        //busca cod_unidade
        $arCodUnidade = explode('-', $_REQUEST['inCodUnidade']);

        $obROrcamentoDespesa->setValorOriginal                                                              ($_REQUEST['nuValorOriginal']);
        $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade                                       ($_REQUEST['inCodEntidade']);
        $obROrcamentoDespesa->obROrcamentoPrograma->setCodPrograma                                          ($_REQUEST['inCodPrograma']);
        $obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto                                ($_REQUEST['inCodPAO']);
        $obROrcamentoDespesa->obROrcamentoFuncao->setCodigoFuncao                                           ($_REQUEST['inCodFuncao']);
        $obROrcamentoDespesa->obROrcamentoSubfuncao->setCodigoSubFuncao                                     ($_REQUEST['inCodSubFuncao']);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodConta                                 ($inCodConta);
        $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade                             ($arCodUnidade[0]);
        $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arCodOrgao[1]);

        $obErro = $obROrcamentoDespesa->verificaDuplicidade();

        if (!$obErro->ocorreu()) {
            $obErro = verificaValoresMetas();
            if (!$obErro->ocorreu()) {
                $obErro = $obROrcamentoDespesa->salvar();
                $inCodDespesa = $obROrcamentoDespesa->getCodDespesa();
            }
        }

        if (!$obErro->ocorreu()) {
            $obErro = lancarMetasDespesa($inCodDespesa);
        }

        
        if (!$obErro->ocorreu()) {
            $obTOrcamentoDespesaAcao = new TOrcamentoDespesaAcao;
            $obTOrcamentoDespesaAcao->setDado('cod_acao'            , $_REQUEST['inCodAcao']);
            $obTOrcamentoDespesaAcao->setDado('exercicio_despesa'   , Sessao::getExercicio());
            $obTOrcamentoDespesaAcao->setDado('cod_despesa'         , $inCodDespesa);

            $obErro = $obTOrcamentoDespesaAcao->inclusao();
        }
        
        if (!$obErro->ocorreu()) {
            // Somente Paraiba ou Pernambuco
            $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf');
            
            if ($inCodUF == '15'){
                $obTTCEPBOrcamentoModalidadeDespesa = new TTCEPBOrcamentoModalidadeDespesa();
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('exercicio', Sessao::getExercicio());
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('cod_despesa', $inCodDespesa);
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('cod_modalidade', $_REQUEST['inCodModalidadeDespesa']);
                $obErro = $obTTCEPBOrcamentoModalidadeDespesa->inclusao();    
            }elseif ($inCodUF == '16'){
                $obTTCEPEOrcamentoModalidadeDespesa = new TTCEPEOrcamentoModalidadeDespesa();
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('exercicio'     , Sessao::getExercicio());
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_entidade'  , $_REQUEST['inCodEntidade']);
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_despesa'   , $inCodDespesa);
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_modalidade', $_REQUEST['inCodModalidadeDespesa']);
                $obErro = $obTTCEPEOrcamentoModalidadeDespesa->inclusao();    
            }
        }

        $null = '&nbsp;';
        $js .= 'f.inCodRecurso.value = "";';
        $js .= 'f.nuValorOriginal.value = "";';
        $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$null.'";';
        if (!$obErro->ocorreu()) {
            if ($_REQUEST['rdMesmaAcao'] == 'S') {
                $stLink  = '?inCodEntidade='.$_REQUEST['inCodEntidade'];
                $stLink .= '&stDotacaoOrcamentaria='.$_REQUEST['stDotacaoOrcamentaria'];
                $stLink .= '&inCodAcao='.$_REQUEST['inCodAcao'];
                $stLink .= '&inAno='.$_REQUEST['inAno'];
                $stLink .= '&stAcao='.$_REQUEST['stAcao'];
                SistemaLegado::alertaAviso($pgForm.$stLink, $inCodDespesa.'/'.$obROrcamentoDespesa->getExercicio(), 'incluir', 'aviso', Sessao::getId(), '../');
                SistemaLegado::executaFrameOculto( $js );
            } else {
                SistemaLegado::alertaAviso($pgList.'?stAcao=incluir', $inCodDespesa."/".$obROrcamentoDespesa->getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
    break;

case 'alterar':
    $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento($stFormaExecucao);
    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($_REQUEST['inCodDespesa']);
    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->buscaNivelConta($rsNivel, $boTransacao);

    if ($stFormaExecucao == 1) {
        if ($rsNivel->getCampo('nivel_conta') > 5) {
            $obErro->setDescricao('Detalhamento permitido somente na Execução!');
        }
    } else {
        if ($rsNivel->getCampo('nivel_conta') < 6) {
            $obErro->setDescricao('A Rubrica de Despesa deve ser desdobrada!');
        }
    }

    if ($boDestinacao) {
        $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

        $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , Sessao::getExercicio());
        $obTOrcamentoRecursoDestinacao->setDado('cod_recurso'      , $_REQUEST['inCodRecurso']);
        $obTOrcamentoRecursoDestinacao->setDado('cod_uso'          , '');
        $obTOrcamentoRecursoDestinacao->setDado('cod_destinacao'   , '');
        $obTOrcamentoRecursoDestinacao->setDado('cod_detalhamento' , '');
        $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', '');
        $obTOrcamentoRecursoDestinacao->recuperaPorChave($rsEspecificacao, $boTransacao);
        $inCodEspecificacao = $rsEspecificacao->getCampo('cod_especificacao');

        if ($inCodEspecificacao != $arDestinacaoRecurso[2]) {
            $stFiltroBuscaExiste  = ' WHERE exercicio         = '.Sessao::getExercicio();
            $stFiltroBuscaExiste .= '   AND cod_uso           = '.$arDestinacaoRecurso[0];
            $stFiltroBuscaExiste .= '   AND cod_destinacao    = '.$arDestinacaoRecurso[1];
            $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2];
            $stFiltroBuscaExiste .= '   AND cod_detalhamento  = '.$arDestinacaoRecurso[3];

            $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
            $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

            if ($inCodRecursoExiste == '') {
                $obTOrcamentoRecurso->setDado('exercicio', Sessao::getExercicio());
                $obTOrcamentoRecurso->proximoCod( $inCodRecurso );
                $obTOrcamentoRecurso->setDado('cod_recurso', $inCodRecurso);
                $obErro = $obTOrcamentoRecurso->inclusao($boTransacao);
                if (!$obErro->ocorreu()) {
                    $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , Sessao::getExercicio());
                    $obTOrcamentoRecursoDestinacao->setDado('cod_recurso'      , $inCodRecurso);
                    $obTOrcamentoRecursoDestinacao->setDado('cod_uso'          , $arDestinacaoRecurso[0]);
                    $obTOrcamentoRecursoDestinacao->setDado('cod_destinacao'   , $arDestinacaoRecurso[1]);
                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                    $obTOrcamentoRecursoDestinacao->setDado('cod_detalhamento' , $arDestinacaoRecurso[3]);
                    $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );

                    $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso ( $inCodRecurso );
                }

                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio'        , Sessao::getExercicio());
                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                // Verifica qual o cod_recurso que possui conta contabil vinculada C
                $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , Sessao::getExercicio());
                $obTOrcamentoRecursoDestinacao->setDado('cod_recurso'      , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_uso'          , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_destinacao'   , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_detalhamento' , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural'   , "'2.9.3.2.0.00.00.%'");
                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC, '', '', $boTransacao);

                $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                if ($inCodRecursoBuscaC == '') {
                    include CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';

                    if (!$obErro->ocorreu()) {
                        $obRContabilidadePlanoBancoC = new RContabilidadePlanoBanco;
                        $obRContabilidadePlanoBancoC->setCodEstrutural('2.9.3.2.0.00.00.');
                        $obRContabilidadePlanoBancoC->getProximoEstruturalRecurso($rsProxCod, $boTransacao);
                        $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                        if ($inProximoCodEstruturalC != 99) {
                            $obRContabilidadePlanoBancoC->obRContabilidadeSistemaContabil->setCodSistema(4);
                            $obRContabilidadePlanoBancoC->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                            $inProximoCodEstruturalC++;
                            $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, '0', STR_PAD_LEFT);
                            $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                            $obRContabilidadePlanoBancoC->setCodEstrutural ($stCodEstruturalC);
                            $obRContabilidadePlanoBancoC->setNomConta      ($stNomEspecificacao);
                            $obRContabilidadePlanoBancoC->setExercicio     (Sessao::getExercicio());
                            $obRContabilidadePlanoBancoC->setNatSaldo      ('C');
                            $obRContabilidadePlanoBancoC->setContaAnalitica(true);
                            $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);

                            $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao, false);
                        } else {
                            SistemaLegado::exibeAviso('Limite de Contas Excedido', 'n_incluir', 'erro');
                        }
                    }
                }

                // Verifica qual o cod_recurso que possui conta contabil vinculada D
                $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , Sessao::getExercicio());
                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural'   , "'1.9.3.2.0.00.00.%'");
                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoD, '', '', $boTransacao);

                $inCodRecursoBuscaD = $rsContaRecursoD->getCampo('cod_recurso');

                if ($inCodRecursoBuscaD == '') {
                    if (!$obErro->ocorreu()) {
                        $obRContabilidadePlanoBancoD = new RContabilidadePlanoBanco;
                        $obRContabilidadePlanoBancoD->setCodEstrutural('1.9.3.2.0.00.00.');
                        $obRContabilidadePlanoBancoD->getProximoEstruturalRecurso($rsProxCodD, $boTransacao);
                        $inProximoCodEstruturalD = $rsProxCodD->getCampo('prox_cod_estrutural');
                        if ($inProximoCodEstruturalD != 99) {
                            $obRContabilidadePlanoBancoD->obRContabilidadeSistemaContabil->setCodSistema(4);
                            $obRContabilidadePlanoBancoD->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                            $inProximoCodEstruturalD++;
                            $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, '0', STR_PAD_LEFT);
                            $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                            $obRContabilidadePlanoBancoD->setCodEstrutural ($stCodEstruturalD);
                            $obRContabilidadePlanoBancoD->setNomConta      ($stNomEspecificacao);
                            $obRContabilidadePlanoBancoD->setExercicio     (Sessao::getExercicio());
                            $obRContabilidadePlanoBancoD->setNatSaldo      ('D');
                            $obRContabilidadePlanoBancoD->setContaAnalitica(true);
                            $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);

                            $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao, false);
                        } else {
                            SistemaLegado::exibeAviso('Limite de Contas Excedido', 'n_incluir', 'erro');
                        }
                    }
                }
            } else { // se ja existe o recurso cadastrado, so altera a despesa como o novo cod_recurso
                $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
            }
        } else { // se nao alterou a especificacao, altera somente o recurso
            $stFiltroBuscaExiste  = ' WHERE exercicio         = '.Sessao::getExercicio();
            $stFiltroBuscaExiste .= '   AND cod_uso           = '.$arDestinacaoRecurso[0];
            $stFiltroBuscaExiste .= '   AND cod_destinacao    = '.$arDestinacaoRecurso[1];
            $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2];
            $stFiltroBuscaExiste .= '   AND cod_detalhamento  = '.$arDestinacaoRecurso[3];

            $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
            $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

            if ($inCodRecursoExiste == '') {
                $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , Sessao::getExercicio());
                $obTOrcamentoRecursoDestinacao->setDado('cod_recurso'      , $_REQUEST['inCodRecurso']);
                $obTOrcamentoRecursoDestinacao->setDado('cod_uso'          , $arDestinacaoRecurso[0]);
                $obTOrcamentoRecursoDestinacao->setDado('cod_destinacao'   , $arDestinacaoRecurso[1]);
                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                $obTOrcamentoRecursoDestinacao->setDado('cod_detalhamento' , $arDestinacaoRecurso[3]);
                $obTOrcamentoRecursoDestinacao->alteracao($boTransacao);
            } else {
                $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
            }
        }
    } else {
        $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($_REQUEST['inCodRecurso']);
        $obROrcamentoDespesa->obROrcamentoRecurso->setExercicio (Sessao::getExercicio());
        $obROrcamentoDespesa->obROrcamentoRecurso->listar($rsRecurso, '', $boTransacao);
        if ($rsRecurso->getNumLinhas() <= 0) {
           $obErro->setDescricao('Recurso inválido!');
        }
    }

    if (!$obErro->ocorreu()) {
        include_once CAM_GF_EMP_NEGOCIO.'REmpenhoPreEmpenho.class.php';
        $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
        $obREmpenhoPreEmpenho->setExercicio  (Sessao::getExercicio());
        $obREmpenhoPreEmpenho->setCodEntidade($_REQUEST['inHdnEntidadeAtual']);
        $obREmpenhoPreEmpenho->consultarExistenciaDespesa();

        // A verificação passou a ser feita no componente que monta a dotação, deixando apenas labels que
        // o usuáio não consegue modificar (apenas as metas e o recurso )

        //busca o codigo da conta da Classificação de Despesa informada
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($_REQUEST['inCodDespesa']);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->consultar($rsRubrica);
        $inCodConta = $rsRubrica->getCampo('cod_conta');
        //busca cod_ograo
        $arCodOrgao   = explode('-', $_REQUEST['inCodOrgao']);
        //busca cod_unidade
        $arCodUnidade = explode('-', $_REQUEST['inCodUnidade']);

        $obROrcamentoDespesa->setCodDespesa                                    ($_REQUEST['inCodFixacaoDespesa']);
        $obROrcamentoDespesa->setValorOriginal                                 ($_REQUEST['nuValorOriginal']);
        $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade          ($_REQUEST['inCodEntidade']);
        $obROrcamentoDespesa->obROrcamentoPrograma->setCodPrograma             ($_REQUEST['inCodPrograma']);
        $obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto   ($_REQUEST['inCodPAO']);
        $obROrcamentoDespesa->obROrcamentoFuncao->setCodigoFuncao              ($_REQUEST['inCodFuncao']);
        $obROrcamentoDespesa->obROrcamentoSubfuncao->setCodigoSubFuncao        ($_REQUEST['inCodSubFuncao']);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodConta    ($inCodConta);
        $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($arCodUnidade[0]);
        $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arCodOrgao[1]);

        $obErro = $obROrcamentoDespesa->verificaDuplicidade();

        if (!$obErro->ocorreu()) {
            $obErro = verificaValoresMetas();
            if (!$obErro->ocorreu()) {
                $obErro = $obROrcamentoDespesa->salvar();
                $inCodDespesa = $obROrcamentoDespesa->getCodDespesa();
            }
        }

        if (!$obErro->ocorreu()) {
            $obErro = lancarMetasDespesa($_REQUEST['inCodFixacaoDespesa']);
        }
               
        if (!$obErro->ocorreu()) {
            
            $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf');
            // Somente Paraiba ou Pernambuco
            if($inCodUF == '15'){
                $obTTCEPBOrcamentoModalidadeDespesa = new TTCEPBOrcamentoModalidadeDespesa();
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('exercicio', Sessao::getExercicio());
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('cod_despesa', $inCodDespesa);
                $obErro = $obTTCEPBOrcamentoModalidadeDespesa->exclusao();
                
                $obTTCEPBOrcamentoModalidadeDespesa = new TTCEPBOrcamentoModalidadeDespesa();
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('exercicio', Sessao::getExercicio());
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('cod_despesa', $inCodDespesa);
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('cod_modalidade', $_REQUEST['inCodModalidadeDespesa']);
                $obErro = $obTTCEPBOrcamentoModalidadeDespesa->inclusao();                
            }elseif($inCodUF == '16'){
                $obTTCEPEOrcamentoModalidadeDespesa = new TTCEPEOrcamentoModalidadeDespesa();
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('exercicio'    , Sessao::getExercicio());
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_despesa'  , $inCodDespesa);
                $obErro = $obTTCEPEOrcamentoModalidadeDespesa->exclusao();
                
                $obTTCEPEOrcamentoModalidadeDespesa = new TTCEPEOrcamentoModalidadeDespesa();
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('exercicio'     , Sessao::getExercicio());
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_entidade'  , $_REQUEST['inCodEntidade']);
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_despesa'   , $inCodDespesa);
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_modalidade', $_REQUEST['inCodModalidadeDespesa']);
                $obErro = $obTTCEPEOrcamentoModalidadeDespesa->inclusao();
            }
            
        }

        $stFiltro = '';
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".$stValor."&";
        }
        $stFiltro .= 'pg='.Sessao::read('pg').'&';
        $stFiltro .= 'pos='.Sessao::read('pos').'&';
        $stFiltro .= 'stAcao='.$_REQUEST['stAcao'];
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('LSDespesa.php?'.$stFiltro, $_REQUEST['inCodFixacaoDespesa'].'/'.$obROrcamentoDespesa->getExercicio(), 'alterar', 'aviso', Sessao::getId(), '../');
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_alterar', 'erro');
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_incluir', 'erro');
    }
    break;

case 'excluir':
    include_once CAM_GF_EMP_NEGOCIO.'REmpenhoPreEmpenho.class.php';
    include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoDespesaAcao.class.php';
    $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
    $obREmpenhoPreEmpenho->setExercicio(Sessao::getExercicio());
    $obREmpenhoPreEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
    $obREmpenhoPreEmpenho->consultarExistenciaDespesa();

    if ($obREmpenhoPreEmpenho->getCountDespesaExercicio() == 0) {
        $obTOrcamentoDespesaAcao = new TOrcamentoDespesaAcao;
        $obTOrcamentoDespesaAcao->setDado('cod_despesa'      , $_REQUEST['inCodDespesa']);
        $obTOrcamentoDespesaAcao->setDado('exercicio_despesa', Sessao::getExercicio());
        $obTOrcamentoDespesaAcao->setDado('cod_acao'         , $_REQUEST['inCodAcao']);
        $obErro = $obTOrcamentoDespesaAcao->exclusao();

        if (!$obErro->ocorreu()) {
            
            $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf');
            if($inCodUF == '15'){
                $obTTCEPBOrcamentoModalidadeDespesa = new TTCEPBOrcamentoModalidadeDespesa();
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('exercicio', Sessao::getExercicio());
                $obTTCEPBOrcamentoModalidadeDespesa->setDado('cod_despesa', $inCodDespesa);
                $obErro = $obTTCEPBOrcamentoModalidadeDespesa->exclusao();
            }elseif($inCodUF == '16'){
                $obTTCEPEOrcamentoModalidadeDespesa = new TTCEPEOrcamentoModalidadeDespesa();
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('exercicio'   , Sessao::getExercicio());
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
                $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_despesa' , $_REQUEST['inCodDespesa']);
                $obErro = $obTTCEPEOrcamentoModalidadeDespesa->exclusao();
            }
            
            $obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
            $obErro = $obROrcamentoDespesa->excluir();
            
            if ($boDestinacao && $_REQUEST['inCodRecurso'] && !$obErro->ocorreu()) {
                include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecursoDestinacao.class.php';
                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                $obTOrcamentoRecursoDestinacao->setDado('exercicio'  , Sessao::getExercicio());
                $obTOrcamentoRecursoDestinacao->setDado('cod_recurso', $_REQUEST['inCodRecurso']);
                $obTOrcamentoRecursoDestinacao->exclusao();

                include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecurso.class.php';
                $obTOrcamentoRecurso = new TOrcamentoRecurso;
                $obTOrcamentoRecurso->setDado('exercicio'  , Sessao::getExercicio());
                $obTOrcamentoRecurso->setDado('cod_recurso', $_REQUEST['inCodRecurso']);
                $obTOrcamentoRecurso->exclusao();
            }
        }
    } else {
        $obErro->setDescricao('Já existe movimentação nas contas.');
    }

    $stFiltro = '';
    $arFiltro = Sessao::read('filtro');
    if ($arFiltro) {
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo.'='.$stValor.'&';
        }
        $stFiltro .= 'pg='.Sessao::read('pg').'&';
        $stFiltro .= 'pos='.Sessao::read('pos').'&';
    }
    $stFiltro .= 'stAcao='.$_REQUEST['stAcao'];

    $pgList = 'LSDespesa.php';
    if (!$obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgList.'?'.$stFiltro, $_GET['inCodDespesa'].'/'.$obROrcamentoDespesa->getExercicio(), 'excluir', 'aviso', Sessao::getId(), '../');
    } else {
        SistemaLegado::alertaAviso($pgList.'?stAcao=excluir', $obErro->getDescricao(), 'n_excluir', 'erro', Sessao::getId(), '../');
    }

    break;
}

/*
 *
 * LANÇAR METAS !!
 *
 */

function lancarMetasDespesa($inCodDespesa)
{
    include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoPrevisaoDespesa.class.php';
    include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoPrevisaoOrcamentaria.class.php';
    include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php';

    $obRPrevisaoDespesa                 = new ROrcamentoPrevisaoDespesa;
    $obROrcamentoPrevisaoOrcamentaria   = new ROrcamentoPrevisaoOrcamentaria;
    $obRConfiguracaoOrcamento           = new ROrcamentoConfiguracao;

    $obErro = new Erro;

    $inNumColunas    = $_REQUEST['inNumCampos'];
    $vlValorOriginal = $_REQUEST['nuValorOriginal'];

    $obRPrevisaoDespesa->setQtdColunas($inNumColunas);
    $obRPrevisaoDespesa->setQtdLinhas (1);
    $obRPrevisaoDespesa->setExercicio (Sessao::getExercicio());

    $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->setExercicio($obRPrevisaoDespesa->getExercicio());
    $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->consultar   ($rsPrevisaoOrcamentaria);

    if ($obRPrevisaoDespesa->getExercicio() != $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->getExercicio()) {
        $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->setExercicio($obRPrevisaoDespesa->getExercicio());
        $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->salvar();
    }

    for ($inContColunas = 1; $inContColunas <= $inNumColunas; $inContColunas++) {
        $inValor = "vlValor_".$inContColunas;

        $inValor = str_replace('.', '', $_REQUEST[$inValor]);
        $inValor = str_replace(',', '.', $inValor);
        $arValor[$inContColunas] = $inValor;
        $vlTotal += $inValor;
    }

    $vlTotal = $_REQUEST['TotalValor'];
    $vlTotal = str_replace('.', '', $vlTotal);
    $vlTotal = str_replace(',', '.', $vlTotal);

    $vlValorOriginal = str_replace('.', '', $vlValorOriginal);
    $vlValorOriginal = str_replace(',', '.', $vlValorOriginal);

    $boSalvar = 0;
    if ($vlTotal > 0) {
        if (number_format($vlTotal,2,'.','') > number_format($vlValorOriginal,2,'.','')) {
            $obErro->setDescricao('Total da despesa '.$inCodDespesa.' ultrapassou o valor da dotação orçamentária.');
            $boSalvar++;
        } elseif (number_format($vlTotal,2,'.','') < number_format($vlValorOriginal,2,'.','')) {
                $obErro->setDescricao('Total da despesa '.$inCodDespesa.' é inferior ao valor da dotação orçamentária.');
                $boSalvar++;
        }
    } else {
        $boSalvar++;
    }

    if ($boSalvar == 0) {
        $obRPrevisaoDespesa->setCodigoDespesa($inCodDespesa);
        $obErro = $obRPrevisaoDespesa->limparDados();

        for ($inContColunas = 1; $inContColunas <= $inNumColunas; $inContColunas++) {
            $obRPrevisaoDespesa->setCodigoDespesa($inCodDespesa);
            $obRPrevisaoDespesa->setPeriodo      ($inContColunas);
            $inValor = "vlValor_".$inContColunas;

            if ($arValor[$inContColunas] == "") {
                $obRPrevisaoDespesa->setValorPrevisto(0);
            } else {
                $valor = str_replace('.','',$$inValor);
                $valor = str_replace(',','.',$valor);
                $obRPrevisaoDespesa->setValorPrevisto($arValor[$inContColunas]);
            }
            $obErro = $obRPrevisaoDespesa->salvar();
            if ($obErro->ocorreu()) {
                break;
            }
        }
    }

    return $obErro;
}

?>
