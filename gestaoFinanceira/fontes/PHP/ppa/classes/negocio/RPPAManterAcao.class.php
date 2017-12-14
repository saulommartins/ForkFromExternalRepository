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
 * Classe de negócio de manter Ação
 * Data de Criação: 22/09/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 $Id: RPPAManterAcao.class.php 39832 2009-04-20 20:47:07Z fellipe.santos $

 * Caso de Uso: uc-02.09.04
 */

include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcao.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoDados.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoQuantidade.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceita.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoNorma.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoRecurso.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPrograma.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoUnidadeExecutora.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoPeriodo.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOAcaoValidada.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPANorma.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoRecurso.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoUnidade.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoProjetoAtividade.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoPAOPPAAcao.class.php';
include_once CAM_GRH_PES_MAPEAMENTO. 'TPessoalContrato.class.php';
include_once CAM_GF_PPA_NEGOCIO    . 'RPPAManterPrograma.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoEspecificacaoDestinacaoRecurso.class.php';

class RPPAManterAcao
{
    public $arData = array(),
           $obRPPAManterPrograma,
           $inCodAcao,
           $inCodAcaoFim,
           $inAno,
           $inCodRecurso,
           $stExercicioRecurso,
           $stTimestampAcaoDados,
           $stTitulo;

    public function __construct()
    {
        $this->obRPPAManterPrograma = new RPPAManterPrograma();
    }

    public function set($stChave, $valor)
    {
        $this->arData[$stChave] = $valor;
    }

    public function get($stChave)
    {
        return $this->arData[$stChave];
    }

    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stFiltro     o critério que delimita a busca
     * @return RecordSet
     */
    protected function pesquisa($stMapeamento, $stMetodo, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
        return $rsRecordSet;
    }

    private function incluiQuantidades(array $arParametros, $boTransacao = '')
    {
        require_once CAM_GF_LDO_MAPEAMENTO.'TLDOAcaoValidada.class.php';

        $obErro = new Erro();
        $obTPPAAcaoQuantidade = new TPPAAcaoQuantidade;

        $obTPPA = new TPPA;
        $obTPPA->recuperaTodos($rsPPA, ' WHERE ppa.cod_ppa = '.$arParametros['inCodPPA'], '', $boTransacao);
        $inAno1 = $rsPPA->getCampo('ano_inicio');

        /* Os valores da tabela de recurso estao separados por um array cada um
            [arCodRecurso] (    [arValorAno1] (     [arValorAno2] (     [arValorAno3] (     [arValorAno4] (
                [0] = 0001          [0] = 500,00        [0] = 100,00        [0] = 100,00        [0] = 140,00
                [1] = 0002          [1] = 500,00        [1] = 100,00        [1] = 100,00        [1] = 110,00
            )                   )                   )                   )                   )

            Onde no arCodRecurso fica cada recurso adicionado na tabletree, e arValorAno guarda os valores de cada ano. A chave de cada arValorAno
            equivale com a chave do arCodRecurso, com isso a chave 0 do arCodRecurso quer dizer que o recurso 0001 tem no ano 1 = 500, ano2 = 200
            ano3 = 100 e ano4 = 140. Usa-se esses valores dos arrays para adicinar na tabela de ppa.acao_quantidade e nao os valores que vem da
            dos inputs da tabela de meta-fisica pois se o usuario altera o valor de algum ano do recurso e nao expande o '+', os valores da
            tabela de meta fisica nao serao alterados, com isso acaba salvando de forma errada os valores, por isso pega-se os valores diretamente
            do recurso para salvar na acao_quantidade. (Esse problema ocorria na hora de alterar a acao)
        */
        foreach ($arParametros['arCodRecurso'] as $inChaveRecurso => $inCodRecurso) {
            for ($i = 1; $i < 5; ++$i) {
                $inAno = ($inAno1 + $i - 1);
                $obTPPAAcaoQuantidade->setDado('cod_acao'            , $arParametros['inCodAcao']);
                $obTPPAAcaoQuantidade->setDado('timestamp_acao_dados', $arParametros['tsAcaoDados']);
                $obTPPAAcaoQuantidade->setDado('ano'                 , $i);
                $obTPPAAcaoQuantidade->setDado('cod_recurso'         , $inCodRecurso);
                $obTPPAAcaoQuantidade->setDado('exercicio_recurso'   , $inAno);
                if ($arParametros['boImportacao']) {
                    $obTPPAAcaoQuantidade->setDado('quantidade'          , $arParametros['flQuantidade_'.$i.'_'.(int) $inCodRecurso]);
                    $obTPPAAcaoQuantidade->setDado('valor'               , $arParametros['arRecursos'][$i][$inChaveRecurso]);
                } else {
                    $obTPPAAcaoQuantidade->setDado('quantidade'          , $arParametros['flQuantidade_'.$i.'_'.$inCodRecurso]);
                    $obTPPAAcaoQuantidade->setDado('valor'               , $_REQUEST['arValorAno'.$i][$inChaveRecurso]);
                }
                $obErro = $obTPPAAcaoQuantidade->inclusao($boTransacao);

                if (!$obErro->ocorreu()) {
                    // Aqui deve ser alterado a tabela ldo.acao_validada para que o timestamp que faz referencia continue correto, então é feita
                    // uma atualização no campo para que não se perca o vínculo.
                    if (!$obErro->ocorreu() && isset($arParametros['tsAcaoDadosOld'])) {
                        $rsAcaoValidada = new RecordSet;

                        $obTLDOAcaoValidada = new TLDOAcaoValidada;
                        $obTLDOAcaoValidada->setDado('cod_acao'            , $arParametros['inCodAcao']);
                        $obTLDOAcaoValidada->setDado('ano'                 , $i);
                        $obTLDOAcaoValidada->setDado('timestamp_acao_dados', $arParametros['tsAcaoDadosOld']);
                        $obTLDOAcaoValidada->setDado('cod_recurso'         , $inCodRecurso);
                        $obTLDOAcaoValidada->setDado('exercicio_recurso'   , $inAno);
                        $obTLDOAcaoValidada->setDado('quantidade'          , $arParametros['flQuantidade_'.$i.'_'.(int) $inCodRecurso]);
                        if ($arParametros['boImportacao']) {
                            $obTLDOAcaoValidada->setDado('quantidade'          , $arParametros['flQuantidade_'.$i.'_'.(int) $inCodRecurso]);
                            $obTLDOAcaoValidada->setDado('valor'               , $arParametros['arRecursos'][$i][$inCodRecurso]);
                        } else {
                            $obTLDOAcaoValidada->setDado('valor'               , $_REQUEST['arValorAno'.$i][$inChaveRecurso]);
                            $obTLDOAcaoValidada->setDado('quantidade'          , $arParametros['flQuantidade_'.$i.'_'.$inCodRecurso]);
                        }

                        $obTLDOAcaoValidada->recuperaPorChave($rsAcaoValidada, $boTransacao);
                        if ($rsAcaoValidada->getNumLinhas() > 0) {
                            $obErro = $obTLDOAcaoValidada->exclusao($boTransacao);
                            if (!$obErro->ocorreu()) {
                                $obTLDOAcaoValidada->setDado('timestamp_acao_dados', $arParametros['tsAcaoDados']);
                                $obErro = $obTLDOAcaoValidada->inclusao($boTransacao);
                            }
                        }
                    }
                }

                if ($obErro->getDescricao()) {
                    break 2;
                }
            }
        }

        return $obErro;
    }

    private function recuperaOrcamentario($inNumOrgao, $inNumUnidade, $inExercicio, $boTransacao = '')
    {
        $obTOrcamentoUnidade = new TOrcamentoUnidade();
        $rsOrcamentario      = new RecordSet();

        $stFiltro  = ' AND unidade.num_orgao = ' . $inNumOrgao . ' AND unidade.num_unidade = ' . $inNumUnidade;
        $stFiltro .= " AND unidade.exercicio = '" . $inExercicio . "'";
        $stOrdem   = '';

        $obErro = $obTOrcamentoUnidade->recuperaRelacionamento($rsOrcamentario, $stFiltro, $stOrdem, $boTransacao);

        return $rsOrcamentario;
    }

    private function incluiAcaoDados(array $arParametros, $boTransacao = '')
    {
        $flValorEstimado = ($arParametros['flValorEstimado'] == '') ? 0 : $arParametros['flValorEstimado'];
        $flMetaEstimada  = ($arParametros['flValorMetaEstimada'] == '') ? 0 : $arParametros['flValorMetaEstimada'];

        # Salva dados na tabela acao_dados.
        $obTPPAAcaoDados = new TPPAAcaoDados();
        $obTPPAAcaoDados->setDado('cod_acao'            , $arParametros['inCodAcao']);
        $obTPPAAcaoDados->setDado('timestamp_acao_dados', $arParametros['tsAcaoDados']);
        $obTPPAAcaoDados->setDado('cod_tipo'            , $arParametros['inCodTipo']);
        $obTPPAAcaoDados->setDado('cod_produto'         , $arParametros['inCodProduto']);
        $obTPPAAcaoDados->setDado('cod_regiao'          , $arParametros['inCodRegiao']);
        $obTPPAAcaoDados->setDado('exercicio'           , $arParametros['inExercicio']);
        $obTPPAAcaoDados->setDado('cod_grandeza'        , $arParametros['inCodGrandeza']);
        $obTPPAAcaoDados->setDado('cod_unidade_medida'  , $arParametros['inCodUnidadeMedida']);
        $obTPPAAcaoDados->setDado('titulo'              , stripslashes($arParametros['stTitulo']));
        $obTPPAAcaoDados->setDado('descricao'           , stripslashes($arParametros['stDescricao']));
        $obTPPAAcaoDados->setDado('finalidade'          , stripslashes($arParametros['stFinalidade']));
        $obTPPAAcaoDados->setDado('cod_forma'           , $arParametros['slFormaImplementacao']);
        $obTPPAAcaoDados->setDado('cod_tipo_orcamento'  , $arParametros['slTipoOrcamento']);
        $obTPPAAcaoDados->setDado('detalhamento'        , stripslashes($arParametros['stDetalhamento']));
        $obTPPAAcaoDados->setDado('valor_estimado'      , $flValorEstimado);
        $obTPPAAcaoDados->setDado('meta_estimada'       , $flMetaEstimada);
        if ($arParametros['slNaturezaDespesa'] != '') {
            $obTPPAAcaoDados->setDado('cod_natureza'    , $arParametros['slNaturezaDespesa']);
        }
        if ($arParametros['inCodFuncao'] != '') {
            $obTPPAAcaoDados->setDado('cod_funcao'      , $arParametros['inCodFuncao']);
        }
        if ($arParametros['inCodSubFuncao']) {
            $obTPPAAcaoDados->setDado('cod_subfuncao'   , $arParametros['inCodSubFuncao']);
        }

        return $obTPPAAcaoDados->inclusao($boTransacao);
    }

    private function incluiPeriodo($arParam,$boTransacao = '')
    {
        $obTPPAAcaoPeriodo = new TPPAAcaoPeriodo;
        $obTPPAAcaoPeriodo->setDado('cod_acao',$arParam['inCodAcao']);
        $obTPPAAcaoPeriodo->setDado('timestamp_acao_dados',$arParam['tsAcaoDados']);
        $obTPPAAcaoPeriodo->setDado('data_inicio',$arParam['stDataInicial']);
        $obTPPAAcaoPeriodo->setDado('data_termino',$arParam['stDataFinal']);

        return $obTPPAAcaoPeriodo->inclusao($boTransacao);
    }

    private function incluiNorma($inCodAcao, $tsAcaoDados, $inCodNorma, $boTransacao = '')
    {
        if (empty($inCodNorma)) {
            $obErro = new Erro();
            $obErro->setDescricao('número da Norma faltando');

            return $obErro;
        }

        $obTPPAAcaoNorma = new TPPAAcaoNorma();

        $stFiltro  = ' WHERE cod_acao = ' . $inCodAcao;
        $stFiltro .= " AND timestamp_acao_dados = '" . $tsAcaoDados . "'";
        $stFiltro .= ' AND cod_norma = ' . $inCodNorma;
        $obErro = $obTPPAAcaoNorma->recuperaTodos($rsAcaoNorma, $stFiltro, '', $boTransacao);

        if (!$rsAcaoNorma->eof()) {
            $obErro->setDescricao('Norma já usada para esta ação');

            return $obErro;
        }

        $obTPPAAcaoNorma->setDado('cod_acao', $inCodAcao);
        $obTPPAAcaoNorma->setDado('timestamp_acao_dados', $tsAcaoDados);
        $obTPPAAcaoNorma->setDado('cod_norma', $inCodNorma);

        return $obTPPAAcaoNorma->inclusao($boTransacao);
    }

    private function somaTotalRecursos(array $arParametros)
    {
        $arRecursos = $arParametros['arRecursos'];
        $flValorTotal = 0;

        for ($i = 0; $i < $arParametros['inSizeRecurso']; ++$i) {
            for ($j = 1; $j < 5; ++$j) {
                $flValorTotal += $arRecursos[$j][$i];
            }
        }

        return $flValorTotal;
    }

    private function incluiRecursos(array $arParametros, $boTransacao = '')
    {
        $arRecursos = $arParametros['arRecursos'];
        $obErro = new Erro();
        $obTPPAAcaoRecurso = new TPPAAcaoRecurso();

        include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoConfiguracao.class.php';
        include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEspecificacaoDestinacaoRecurso.class.php';
        include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
        include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
        include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';

        $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
        $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
        $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
        $obTOrcamentoConfiguracao->consultar($boTransacao);
        if ($obTOrcamentoConfiguracao->getDado("valor") == 'true') {
            $boDestinacao = true;
        }

        $obTPPA = new TPPA;
        $obTPPA->recuperaTodos($rsPPA, ' WHERE ppa.cod_ppa = '.$arParametros['inCodPPA'], '', $boTransacao);
        $inAno1 = $rsPPA->getCampo('ano_inicio');

        $arTotalRecursos = array();
        for ($i = 0; $i < $arParametros['inSizeRecurso']; ++$i) {
            $flTotalRecurso = 0;
            for ($j = 1; $j < 5; ++$j) {
                $inAno = ($inAno1 + $j - 1);
                $inCodRecurso = $arParametros['arCodRecurso'][$i];
                $obTPPAAcaoRecurso->setDado('cod_acao'            , $arParametros['inCodAcao']);
                $obTPPAAcaoRecurso->setDado('timestamp_acao_dados', $arParametros['tsAcaoDados']);
                $obTPPAAcaoRecurso->setDado('cod_recurso'         , $inCodRecurso);
                $obTPPAAcaoRecurso->setDado('exercicio_recurso'   , $inAno);
                $obTPPAAcaoRecurso->setDado('ano'                 , "'".$j."'");
                $obTPPAAcaoRecurso->setDado('valor'               , $arRecursos[$j][$i]);
                $obErro = $obTPPAAcaoRecurso->inclusao($boTransacao);
                $arTotalRecursos[$j] += $arRecursos[$j][$i];
                $flTotalRecurso += $arRecursos[$j][$i];
                if ($obErro->ocorreu()) {
                    break;
                }
            }
            if ((float) $flTotalRecurso == 0) {
                $obErro->setDescricao('O valor total do recurso não pode ser 0');

                return $obErro;
            }
        }

        return $obErro;
    }

    private function incluiUnidadeExecutora(array $arParam, $boTransacao = '')
    {
        $obErro = new Erro();
        $arUnidades = (array) Sessao::read('arUnidade');
        $obTPPAUnidadeExecutora = new TPPAAcaoUnidadeExecutora();
        $arUnidadeOrcamentaria = explode('.',$arParam['stUnidadeOrcamentaria']);

        $obTPPAUnidadeExecutora->setDado('cod_acao', $arParam['inCodAcao']);
        $obTPPAUnidadeExecutora->setDado('timestamp_acao_dados', $arParam['tsAcaoDados']);
        $obTPPAUnidadeExecutora->setDado('exercicio_unidade', Sessao::getExercicio());
        $obTPPAUnidadeExecutora->setDado('num_orgao', $arUnidadeOrcamentaria[0]);
        $obTPPAUnidadeExecutora->setDado('num_unidade', $arUnidadeOrcamentaria[1]);

        $obErro = $obTPPAUnidadeExecutora->inclusao($boTransacao);

        return $obErro;
    }

    private function atualizaValorTotalPPA($inCodPPA, $flValorAcao, $boTransacao = '')
    {
        $obErro = new Erro();
        $obTPPAPPA = new TPPA();

        $stFiltro = ' WHERE cod_ppa=' . $inCodPPA;
        $obErro = $obTPPAPPA->recuperaTodos($rsPPA, $stFiltro, '', $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        if ($rsPPA->eof()) {
            $obErro->setDescricao('PPA da ação não encontrado');

            return $obErro;
        }

        $obTPPAPPA->setDado('cod_ppa', $rsPPA->getCampo('cod_ppa'));
        $obTPPAPPA->setDado('timestamp', $rsPPA->getCampo('timestamp'));
        $obTPPAPPA->setDado('ano_inicio', $rsPPA->getCampo('ano_inicio'));
        $obTPPAPPA->setDado('ano_final', $rsPPA->getCampo('ano_final'));
        $obTPPAPPA->setDado('valor_total_ppa', ((float) $rsPPA->getCampo('valor_total_ppa')) + $flValorAcao);
        $obErro = $obTPPAPPA->alteracao($boTransacao);

        return $obErro;
    }

    /**
     * Obtem código do programa com base em seu número e o código do PPA.
     * @param  integer $inCodPPA      código do PPA
     * @param  integer $inNumPrograma número do programa
     * @param  boolean $boTransacao   se existe uma transação ativa
     * @return integer código do programa
     */
    private function recuperaCodPrograma($inCodPPA, $inNumPrograma, $boTransacao = '')
    {
        $inCodPrograma = 0;
        $stFiltro  = " WHERE num_programa = '" . intval($inNumPrograma) . "'\n";
        $stFiltro .= ' AND cod_ppa = ' . intval($inCodPPA) . "\n";

        # Obtem código do programa a partir do número do programa.
        $inNumPrograma = $inNumPrograma;
        $obTPPAPrograma = new TPPAPrograma();
        $obErro = $obTPPAPrograma->recuperaTodos($rsPrograma, $stFiltro, '', $boTransacao);

        if (!$rsPrograma->eof()) {
            $inCodPrograma = $rsPrograma->getCampo('cod_programa');
        }

        return $inCodPrograma;
    }

    /**
     * Obtem código do contrato com base em seu registro.
     * @param  integer $inCodRegistro número do registro
     * @param  boolean $boTransacao   se existe uma transação ativa
     * @return integer código do contrato
     */
    public function recuperaCodContrato($inRegistro, $boTransacao = '')
    {
        $obTPessoalContrato = new TPessoalContrato();
        $obErro = $obTPessoalContrato->recuperaTodos($rsContrato, ' WHERE registro=' . $inRegistro, '', $boTransacao);

        if (!$rsContrato->eof()) {
            $inCodContrato = $rsContrato->getCampo('cod_contrato');
        }

        return $inCodContrato;
    }

    /**
     * Retorna o cálculo de todos os recursos alocados para a ação especificada.
     * @param float   &$flTotalAcao variável que recebe o total da acao_recurso
     * @param integer $inCodAcao    código da ação
     * @param boolean $boTransacao  se dentro de uma transação
     */
    public function calculaTotalAcao(&$flTotalAcao, $inCodAcao, $boTransacao = '')
    {
        $obTPPAcao = new TPPAAcao();
        $stFiltro = ' acao.cod_acao = ' . $inCodAcao;

        $obErro = $obTPPAcao->calculaTotalAcao($rsTotalAcao, $stFiltro, '', $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        if ($rsTotalAcao->eof()) {
            $obErro->setDescricao('Recursos para a ação não encontrados');
        } else {
            $flTotalAcao = $rsTotalAcao->getCampo('valor');
        }

        return $obErro;
    }

    /**
     * Operação para incluir/alterar nova Ação.
     * @param  string  $stAcao       operação a ser realizada: 'inclusao' ou 'alteracao'
     * @param  array   $arParametros dados necessários para inclusão de Ação.
     * @param  boolean $boTransacao  se existe uma transação ativa
     * @return Erro    objeto contendo erro, caso um erro ocorra
     */
    private function salvaAcao($stAcao, array &$arParametros, $boTransacao = '')
    {
        $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
        $obErro = new Erro();
        $obTPPAAcao = new TPPAAcao();
        $flValorAntigo = 0;

        if ($stAcao == 'alteracao') {
            $arParametros['inCodAcao'] = (int) $arParametros['hdnInCodAcao'];
            $arParametros['inNumAcao'] = (int) $arParametros['hdnInNumAcao'];
        } elseif ($stAcao == 'inclusao') {
            if (!isset($arParametros['boImportacao']) && $arParametros['boImportacao'] != true) {
                $arParametros['inNumAcao'] = (int) $arParametros['inCodAcao'];
            }
            $rsAcao = $this->recuperaProxCodAcao($boTransacao);
            $arParametros['inCodAcao'] = $rsAcao->getCampo('cod_acao');
        }

        if ($stAcao != 'inclusao') {
            // Calcula antigo valor total da ação para atualizar ppa.valor_total_ppa.
            $obErro = $this->calculaTotalAcao($flValorAntigo, $arParametros['inCodAcao'], $boTransacao);
        }

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        if (!$arParametros['tsAcaoDados']) {
            $arParametros['tsAcaoDados'] = date('Y-m-d H:i:s.u');
        }

        if (!$arParametros['inNumAcao']) {
            $arParametros['inNumAcao'] = $arParametros['inCodAcao'];
        }

        // Cria ou altera elemento na tabela ppa.acao.
        $obTPPAAcao->setDado('cod_acao'                   , (int) $arParametros['inCodAcao']);
        $obTPPAAcao->setDado('num_acao'                   , (int) $arParametros['inNumAcao']);
        if ($arParametros['hdnInCodPrograma']) {
            $obTPPAAcao->setDado('cod_programa'               , (int) $arParametros['hdnInCodPrograma']);
        } else {
            $obTPPAAcao->setDado('cod_programa'               , (int) $arParametros['inCodPrograma']);
        }
        $obTPPAAcao->setDado('ultimo_timestamp_acao_dados', $arParametros['tsAcaoDados']);
        $obTPPAAcao->setDado('ativo'                      , 't');
        $obTPPAAcao->recuperaPorChave($rsAcao, $boTransacao);

        $arParametros['tsAcaoDadosOld'] = '';
        if ($rsAcao->getNumLinhas() == 1) {
            $arParametros['tsAcaoDadosOld'] = $rsAcao->getCampo('ultimo_timestamp_acao_dados');
            $obErro = $obTPPAAcao->alteracao($boTransacao);
        } else {
            $obErro = $obTPPAAcao->inclusao($boTransacao);

            if (!$obErro->ocorreu()) {
                $arParametros['inCodAcao'] = $obTPPAAcao->getDado('cod_acao');
            }
        }

        if (!$obErro->ocorreu()) {
            $obErro = $this->incluiAcaoDados($arParametros, $boTransacao);
        }

        if (!$obErro->ocorreu()) {
            if ($arParametros['boImportarValorAcao'] != 'false') {
                $obErro = $this->incluiRecursos($arParametros, $boTransacao);
            }
        }

        if (!$obErro->ocorreu()) {
            if ($arParametros['boImportarValorAcao'] != 'false') {
                $obErro = $this->incluiQuantidades($arParametros, $boTransacao);
            }
        }

        if (!$obErro->ocorreu() AND $arParametros['inCodNorma'] != '') {
            $obErro = $this->incluiNorma($arParametros['inCodAcao'], $arParametros['tsAcaoDados'], $arParametros['inCodNorma'], $boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $obErro = $this->incluiUnidadeExecutora($arParametros, $boTransacao);
        }

        if (!$obErro->ocorreu()) {
            if ($arParametros['stDataInicial']) {
                $obErro = $this->incluiPeriodo($arParametros, $boTransacao);
            }
        }

        if (!$obErro->ocorreu()) {
            $obErro = $this->incluiPAO($arParametros, $boTransacao);
        }
        if ($inCodUf == 2 OR $inCodUf == 27) {
            if (!$obErro->ocorreu()) {
                if ($stAcao == 'inclusao') {
                    $obErro = $this->incluiIdentificadorAcao($arParametros, $boTransacao);
                }elseif ($stAcao == 'alteracao') {
                    $obErro = $this->alterarIdentificadorAcao($arParametros, $boTransacao);
                }
            }
        }

        return $obErro;
    }

    public function incluiIdentificadorAcao($arParametros, $boTransacao)
    {
        $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
        //Estado de AL = 2
        //Estado de TO = 27
        if ($inCodUf == 2) {
            include_once CAM_GF_PPA_MAPEAMENTO.'TTCEALAcaoIdentificadorAcao.class.php';
            $obTTCEALAcaoIdentificadorAcao = new TTCEALAcaoIdentificadorAcao();
            $obTTCEALAcaoIdentificadorAcao->setDado('cod_acao'          ,$arParametros['inCodAcao'] );
            $obTTCEALAcaoIdentificadorAcao->setDado('cod_identificador', $arParametros['inCodIdentificadorAcao'] );
            $obErro = $obTTCEALAcaoIdentificadorAcao->inclusao($boTransacao);
        }
        if ($inCodUf == 27) {
            include_once CAM_GF_PPA_MAPEAMENTO.'TTCETOAcaoIdentificadorAcao.class.php';
            $obTTCETOAcaoIdentificadorAcao = new TTCETOAcaoIdentificadorAcao();
            $obTTCETOAcaoIdentificadorAcao->setDado('cod_acao'          ,$arParametros['inCodAcao'] );
            $obTTCETOAcaoIdentificadorAcao->setDado('cod_identificador', $arParametros['inCodIdentificadorAcao'] );
            $obErro = $obTTCETOAcaoIdentificadorAcao->inclusao($boTransacao);
        }

        return $obErro;
    }

    public function alterarIdentificadorAcao($arParametros, $boTransacao)
    {   
        $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
        //Estado de AL = 2
        if ($inCodUf == 2) {
            include_once CAM_GF_PPA_MAPEAMENTO.'TTCEALAcaoIdentificadorAcao.class.php';
            $obTTCEALAcaoIdentificadorAcao = new TTCEALAcaoIdentificadorAcao();
            $stFiltro = " WHERE acao_identificador_acao.cod_acao = ".$arParametros['inCodAcao']."";
            $obTTCEALAcaoIdentificadorAcao->recuperaAcaoIdentificadorAcao($rsIdentificadorAcao, $stFiltro, "",$boTransacao);

            if ($rsIdentificadorAcao->getNumLinhas() < 0) {
                $obTTCEALAcaoIdentificadorAcao->setDado('cod_acao'          ,$arParametros['inCodAcao'] );
                $obTTCEALAcaoIdentificadorAcao->setDado('cod_identificador', $arParametros['inCodIdentificadorAcao'] );
                $obErro = $obTTCEALAcaoIdentificadorAcao->inclusao($boTransacao);
            }else{
                $obTTCEALAcaoIdentificadorAcao->setDado('cod_acao'          ,$arParametros['inCodAcao'] );
                $obTTCEALAcaoIdentificadorAcao->setDado('cod_identificador', $arParametros['inCodIdentificadorAcao'] );
                $obErro = $obTTCEALAcaoIdentificadorAcao->alteracao($boTransacao);    
            }
        }
        //Estado de TO = 27
        if ($inCodUf == 27) {
            include_once CAM_GF_PPA_MAPEAMENTO.'TTCETOAcaoIdentificadorAcao.class.php';
            $obTTCETOAcaoIdentificadorAcao = new TTCETOAcaoIdentificadorAcao();
            $stFiltro = " WHERE acao_identificador_acao.cod_acao = ".$arParametros['inCodAcao']."";
            $obTTCETOAcaoIdentificadorAcao->recuperaAcaoIdentificadorAcao($rsIdentificadorAcao, $stFiltro, "",$boTransacao);
            if ($rsIdentificadorAcao->getNumLinhas() < 0) {
                $obTTCETOAcaoIdentificadorAcao->setDado('cod_acao'          ,$arParametros['inCodAcao'] );
                $obTTCETOAcaoIdentificadorAcao->setDado('cod_identificador', $arParametros['inCodIdentificadorAcao'] );
                $obErro = $obTTCETOAcaoIdentificadorAcao->inclusao($boTransacao);
            }else{
                $obTTCETOAcaoIdentificadorAcao->setDado('cod_acao'          ,$arParametros['inCodAcao'] );
                $obTTCETOAcaoIdentificadorAcao->setDado('cod_identificador', $arParametros['inCodIdentificadorAcao'] );
                $obErro = $obTTCETOAcaoIdentificadorAcao->alteracao($boTransacao);    
            }
        }

        return $obErro;
    }

    public function incluiPAO($arParam, $boTransacao)
    {
        //recupera o exercicio do ppa
        $obTPPAPPA = new TPPA;
        $obTPPAPPA->setDado('cod_ppa',$arParam['inCodPPA']);
        $obTPPAPPA->recuperaPorChave($rsPPA, $boTransacao);

        $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade;
        $obTOrcamentoProjetoAtividade->setDado('num_pao',$arParam['inCodAcao']);
        $obTOrcamentoProjetoAtividade->setDado('nom_pao',stripslashes($arParam['stTitulo']));
        $obTOrcamentoProjetoAtividade->setDado('detalhamento',$arParam['stDetalhamento']);

        $obTOrcamentoPAOPPAAcao =  new TOrcamentoPAOPPAAcao;
        $obTOrcamentoPAOPPAAcao->setDado('num_pao',$arParam['inCodAcao']);
        $obTOrcamentoPAOPPAAcao->setDado('cod_acao',$arParam['inCodAcao']);

        for ($i = $rsPPA->getCampo('ano_inicio'); $i<= $rsPPA->getCampo('ano_final'); $i++) {
            $obTOrcamentoPAOPPAAcao->setDado('exercicio',$i);
            $obTOrcamentoProjetoAtividade->setDado('exercicio',$i);
            $obTOrcamentoProjetoAtividade->recuperaPorChave($rsPAO, $boTransacao);
            if ($rsPAO->getNumLinhas() > 0) {
                $obErro = $obTOrcamentoProjetoAtividade->alteracao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
                $obErro = $obTOrcamentoPAOPPAAcao->alteracao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            } else {
                $obErro = $obTOrcamentoProjetoAtividade->inclusao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
                $obErro = $obTOrcamentoPAOPPAAcao->inclusao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }

        return $obErro;
    }

    /**
     * Inclui nova Ação.
     * @param  array   $arParametros dados necessários para inclusão de Ação.
     * @param  boolean $boTransacao  se existe uma transação ativa
     * @return Erro    objeto contendo erro, caso um erro ocorra
     */
    public function incluiAcao(array &$arParametros, $boTransacao = '')
    {
        return $this->salvaAcao('inclusao', $arParametros, $boTransacao);
    }

    /**
     * Carrega dados restantes necessários para incluir ou alterar ação.
     * @param array   $arParametros dados recebidos da tela
     * @param boolean $boTransacao  se existe uma transação ativa
     */
    private function carregaDados(array &$arParametros, $boTransacao = '')
    {
        $obErro = new Erro();

        # Obtem código do programa a partir do número do programa.
        if (!isset($arParametros['inCodPrograma'])) {
            $arParametros['inCodPrograma'] = $this->recuperaCodPrograma($arParametros['inCodPPA'], $arParametros['inNumPrograma'], $boTransacao);

            if (!$arParametros['inCodPrograma']) {
                $obErro->setDescricao('Erro ao ler o Código do Programa');

                return $obErro;
            }
        }

        return $obErro;
    }

    public function incluir(array &$arParametros)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = '';

        # Inicia nova transação.
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        # Confirma a criação de novo timestamp.
        unset($arParametros['tsAcaoDados']);

        $obErro = $this->incluiAcao($arParametros, $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAAcao);

        return $obErro;
    }

    /**
     * Altera Ação existente.
     * @param  array   $arParametros dados necessários para inclusão de Ação.
     * @param  boolean $boTransacao  se existe uma transação ativa
     * @return Erro    objeto contendo erro, caso um erro ocorra
     */
    public function alteraAcao(array &$arParametros, $boTransacao = '')
    {
        return $this->salvaAcao('alteracao', $arParametros, $boTransacao);
    }

    public function alterar(array &$arParametros)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = '';

        # Inicia nova transação.
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        $obErro = $this->carregaDados($arParametros);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        # Confirma a criação de novo timestamp.
        unset($arParametros['tsAcaoDados']);

        $obErro = $this->alteraAcao($arParametros, $boTransacao);

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAAcao);

        return $obErro;
    }

    public function excluiAcao($inCodAcao, $inCodPPA, $boTransacao = '')
    {
        $obErro = new Erro();

        $obTPPAAcaoPeriodo = new TPPAAcaoPeriodo;
        $obTPPAAcaoPeriodo->setCampoCod('cod_acao');
        $obTPPAAcaoPeriodo->setDado('cod_acao',$inCodAcao);
        $obErro = $obTPPAAcaoPeriodo->exclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            $obTPPAAcaoQuantidade = new TPPAAcaoQuantidade;
            $obTPPAAcaoQuantidade->setCampoCod('cod_acao');
            $obTPPAAcaoQuantidade->setDado('cod_acao',$inCodAcao);
            $obErro = $obTPPAAcaoQuantidade->exclusao($boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $obTPPAAcaoRecurso = new TPPAAcaoRecurso;
            $obTPPAAcaoRecurso->setCampoCod('cod_acao');
            $obTPPAAcaoRecurso->setDado('cod_acao',$inCodAcao);
            $obErro = $obTPPAAcaoRecurso->exclusao($boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $obTPPAAcaoUnidadeExecutora = new TPPAAcaoUnidadeExecutora;
            $obTPPAAcaoUnidadeExecutora->setCampoCod('cod_acao');
            $obTPPAAcaoUnidadeExecutora->setDado('cod_acao',$inCodAcao);
            $obErro = $obTPPAAcaoUnidadeExecutora->exclusao($boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $obTPPAAcaoNorma = new TPPAAcaoNorma;
            $obTPPAAcaoNorma->setCampoCod('cod_acao');
            $obTPPAAcaoNorma->setDado('cod_acao',$inCodAcao);
            $obErro = $obTPPAAcaoNorma->exclusao($boTransacao);
        }

        $obTOrcamentoPAOPPAAcao = new TOrcamentoPAOPPAAcao;
        $obTOrcamentoPAOPPAAcao->setDado('cod_acao',$inCodAcao);

        $obTOrcamentoPAO = new TOrcamentoProjetoAtividade;
        $obTOrcamentoPAO->setDado('num_pao',$inCodAcao);

        //recupera o exercicio do ppa
        $obTPPAPPA = new TPPA;
        $obTPPAPPA->setDado('cod_ppa',$inCodPPA);
        $obTPPAPPA->recuperaPorChave($rsPPA, $boTransacao);

        for ($i = $rsPPA->getCampo('ano_inicio'); $i <= $rsPPA->getCampo('ano_final'); $i++) {
            $obTOrcamentoPAOPPAAcao->setDado('exercicio',$i);
            $obErro = $obTOrcamentoPAOPPAAcao->exclusaoPorCodAcao($boTransacao);
            if ($obErro->ocorreu()) {
                break;
            }
            $obTOrcamentoPAO->setDado('exercicio',$i);
            $obErro = $obTOrcamentoPAO->exclusao($boTransacao);
            if ($obErro->ocorreu()) {
                break;
            }
        }

        if (!$obErro->ocorreu()) {
            $obTPPAAcaoDados = new TPPAAcaoDados;
            $obTPPAAcaoDados->setCampoCod('cod_acao');
            $obTPPAAcaoDados->setDado('cod_acao',$inCodAcao);
            $obErro = $obTPPAAcaoDados->exclusao($boTransacao);
        }

        $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
        //Estado de AL = 2
        if ( $inCodUf == 2 ) {
            if (!$obErro->ocorreu()) {
                include_once CAM_GF_PPA_MAPEAMENTO.'TTCEALAcaoIdentificadorAcao.class.php';
                $obTTCEALAcaoIdentificadorAcao = new TTCEALAcaoIdentificadorAcao();
                $obTTCEALAcaoIdentificadorAcao->setDado('cod_acao', $_REQUEST['inCodAcao'] );
                $obErro = $obTTCEALAcaoIdentificadorAcao->exclusao( $boTransacao );
            }
        }
        //Estado de TO = 27
        if ( $inCodUf == 27 ) {
            if (!$obErro->ocorreu()) {
                include_once CAM_GF_PPA_MAPEAMENTO.'TTCETOAcaoIdentificadorAcao.class.php';
                $obTTCETOAcaoIdentificadorAcao = new TTCETOAcaoIdentificadorAcao();
                $obTTCETOAcaoIdentificadorAcao->setDado('cod_acao', $_REQUEST['inCodAcao'] );
                $obErro = $obTTCETOAcaoIdentificadorAcao->exclusao( $boTransacao );
            }
        }
        
        if (!$obErro->ocorreu()) {
            $obTPPAAcao = new TPPAAcao;
            $obTPPAAcao->setDado('cod_acao',$inCodAcao);
            $obErro = $obTPPAAcao->exclusao($boTransacao);
        }

        return $obErro;
    }

    public function desativaAcao($inCodAcao, $inCodNorma, $boTransacao = '')
    {
        # Recupera dados restantes necessários para atualizar a ação.
        $rsAcao = $this->recuperaAcao($inCodAcao, $boTransacao);
        $inCodPPA      = $rsAcao->getCampo('cod_ppa');
        $inCodPrograma = $rsAcao->getCampo('cod_programa');
        $inCodAcao     = $rsAcao->getCampo('cod_acao');
        $stDescricao   = $rsAcao->getCampo('descricao');
        #$tsAcaoDados   = $rsAcao->getCampo('ultimo_timestamp_acao_dados');
        $tsAcaoDados   = date('Y-m-d H:i:s.u');
        if (!isset($inCodNorma)) {
            $inCodNorma = $rsAcao->getCampo('cod_norma');
        }

        $rsAcaoDados = $this->pesquisa('TPPAAcaoDados', 'recuperaTodos', ' WHERE cod_acao = ' . $inCodAcao, '', $boTransacao);

        $this->criarParametrosAcaoDados($arParametros, $rsAcaoDados, $tsAcaoDados);
        $obErro = $this->incluiAcaoDados($arParametros, $boTransacao);
        if ($obErro->ocorreu()) {
            return $obErro;
        }

        $obErro = $this->incluiNorma($inCodAcao, $tsAcaoDados, $inCodNorma, $boTransacao);

        # Obtem o valor total de todos os recursos da ação.
        if (!$obErro->ocorreu()) {
            $obErro = $this->calculaTotalAcao($flTotalAcao, $inCodAcao, $boTransacao);
        }

        # Subtrai valor total da ação do PPA.
        if (!$obErro->ocorreu()) {
            $boErro = $this->atualizaValorTotalPPA($inCodPPA, -$flTotalAcao, $boTransacao);
        }

        if (!$obErro->ocorreu()) {
            # Altera estado da ação para inativo.
            $obTPPAAcao = new TPPAAcao();
            $obTPPAAcao->setDado('cod_acao', $inCodAcao);
            $obTPPAAcao->setDado('cod_programa', $inCodPrograma);
            $obTPPAAcao->setDado('cod_acao', (int) $inCodAcao);
            $obTPPAAcao->setDado('descricao', $stDescricao);
            $obTPPAAcao->setDado('ultimo_timestamp_acao_dados', $tsAcaoDados);
            $obTPPAAcao->setDado('ativo', 'f');
            $obErro = $obTPPAAcao->alteracao($boTransacao);
        }

        return $obErro;
    }

    /**
     * Verifica se o PPA desta ação foi homologado.
     * @param $inCodAcao código da ação
     * @return boolean PPA homologado ou não
     */
    public function verificaAcaoHomologada($inCodAcao, $boTransacao = '')
    {
        $rsAcao = $this->recuperaAcao($inCodAcao, $boTransacao);

        return $rsAcao->getCampo('homologado') == 't';
    }

    /**
     * Faz a exclusão do PPA.
     *
     * @param  array $arParametros parametros recebidos da visão
     * @return Erro  objeto contendo erro, caso ele ocorra.
     */
    public function excluir(array $arParametros)
    {
        $obTPPAAcao      = new TPPAAcao();
        $obTPPA          = new TPPA();
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = '';

        # Inicia nova transação.
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        $obTAcaoValidada = new TLDOAcaoValidada;
        $stFiltro  = " WHERE cod_acao = ".$arParametros['inCodAcao'];
        $stFiltro .= "   AND timestamp_acao_dados = '".$arParametros['tsAcaoDadosAnterior']."'";
        $obTAcaoValidada->recuperaTodos($rsAcaoValidada, $stFiltro, '', $boTransacao);
        if ($rsAcaoValidada->getNumLinhas() > -1) {
            // Busca o ano inicial do PPA
            $stFiltro = " WHERE cod_ppa = ".$arParametros['inCodPPA'];
            $obTPPA->recuperaPPA($rsPPA, $stFiltro, '', $boTransacao);
            $stExercicioPPAInicio = $rsPPA->getCampo('ano_inicio');

            $arExercicio = array();
            while (!$rsAcaoValidada->EOF()) {
                $arExercicio[] = $stExercicioPPAInicio + $rsAcaoValidada->getCampo('ano') - 1;
                $rsAcaoValidada->proximo();
            }

            $obErro->setDescricao('A ação não pode ser excluída pois está sendo utilizada na LDO do(s) exercício(s) ('.implode(', ', $arExercicio).')');
        }

        if (!$obErro->ocorreu()) {
            $this->excluiAcao($arParametros['inCodAcao'],$arParametros['inCodPPA'], $boTransacao);
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAAcao);
        }

        return $obErro;
    }

    public function recuperaProxCodAcao($boTransacao)
    {
        $obTPPAAcao = new TPPAAcao();
        $boAcao = false;
        $obTPPAAcao->recuperaCodigosAcao($rsAcao, '', '', $boTransacao);
        $arCodigos = array();
        if ($rsAcao->getNumLinhas() > -1) {
            while (!$rsAcao->EOF()) {
                $arCodigos[] = $rsAcao->getCampo('cod_acao');
                $rsAcao->proximo();
            }
        }
        $inCodAcao = 1;
        $rsAcao = new RecordSet;
        while (!$boAcao) {
            if (in_array($inCodAcao, $arCodigos)) {
                $inCodAcao++;
            } else {
                $boAcao = true;
                $rsAcao->setCampo('cod_acao', str_pad($inCodAcao, 4, 0, STR_PAD_LEFT));
            }
        }

        return $rsAcao;
    }

    /**
     * Recupera valor total do PPA.
     *
     * @param integer $inCodPPA    o código do PPA
     * @param bool    $boTransacao se em transação ou não
     */
    public function recuperaTotalPPA($inCodPPA = '', $boTransacao = '')
    {
        $obTPPAAcao  = new TPPAAcao();
        $stFiltro    = '';
        $stOrdem     = '';
        $flTotalPPA  = 0.0;

        if ($inCodPPA) {
            $stFiltro .= 'ppa.cod_ppa = ' . $inCodPPA;
        }

        $obErro = $obTPPAAcao->recuperaTotalPPA($rsTotalPPA, $stFiltro, $stOrdem, $boTransacao);

        if (!$obErro->ocorreu()) {
            if (!$rsTotalPPA->eof()) {
                $flTotalPPA = (float) $rsTotalPPA->getCampo('valor');
            }
        }

        return $flTotalPPA;
    }

    /**
     * Recupera valor total do programa.
     *
     * @param integer $inCodPPA      o código do PPA
     * @param integer $inCodPrograma o código do programa
     * @param bool    $boTransacao   se em transação ou não
     */
    public function recuperaTotalPrograma($inCodPPA = '', $inCodPrograma = '', $boTransacao = '')
    {
        $obTPPAAcao = new TPPAAcao();
        $stFiltro    = '';
        $stOrdem     = '';
        $stSeparador = '';
        $flTotalPrograma = 0;

        if ($inCodPPA) {
            $stFiltro   .= $stSeparador . ' programa.cod_ppa = ' . $inCodPPA;
            $stSeparador = ' AND ';
        }

        if ($inCodPrograma) {
            $stFiltro   .= $stSeparador . ' programa.cod_programa = ' . $inCodPrograma;
            $stSeparador = ' AND ';
        }

        $obErro = $obTPPAAcao->calculaTotalPrograma($rsTotalPrograma, $stFiltro, $stOrdem, $boTransacao);

        if (!$obErro->ocorreu()) {
            if (!$rsTotalPrograma->eof()) {
                $flTotalPrograma = $rsTotalPrograma->getCampo('valor');
            }
        }

        return $flTotalPrograma;
    }

    /**
     * Recupera valor total da Ação.
     *
     * @param integer $inCodAcao   o código da Ação a excluir deste cálculo
     * @param bool    $boTransacao se em transação ou não
     */
    public function recuperaTotalAcao($inCodAcao = '', $boTransacao = '')
    {
        $obTPPAAcao  = new TPPAAcao();
        $stFiltro    = '';
        $stOrdem     = '';
        $flTotalAcao = 0.0;

        if ($inCodAcao) {
            $stFiltro .= 'acao.cod_acao = ' . $inCodAcao;
        }

        $obErro = $obTPPAAcao->calculaTotalAcao($rsTotalAcao, $stFiltro, $stOrdem, $boTransacao);

        if (!$obErro->ocorreu()) {
            if (!$rsTotalAcao->eof()) {
                $flTotalAcao = (float) $rsTotalAcao->getCampo('valor');
            }
        }

        return $flTotalAcao;
    }

    public function recuperaTotalReceitas($inCodPPA = '', $inCodReceita = '', $inExercicio = '', $boTransacao = '')
    {
        $obTPPAReceita = new TPPAReceita();
        $stFiltro    = '';
        $stOrdem     = '';
        $stSeparador = '';
        $flTotalReceitas = 0;

        if ($inCodPPA) {
            $stFiltro   .= $stSeparador . 'ppa_receita.cod_ppa = ' . $inCodPPA;
            $stSeparador = ' AND ';
        }

        if ($inCodReceita) {
            $stFiltro   .= $stSeparador . 'ppa_receita.cod_receita = ' . $inCodReceita;
            $stSeparador = ' AND ';
        }

        if ($inExercicio) {
            $stFiltro .= $stSeparador . "ppa_receita.exercicio = '" . $inExercicio . "'";
        }

        $obErro = $obTPPAReceita->calculaTotalReceitas($rsTotalReceitas, $stFiltro, $stOrdem, $boTransacao);

        if (!$obErro->ocorreu()) {
            if (!$rsTotalReceitas->eof()) {
                $flTotalReceitas = $rsTotalReceitas->getCampo('valor');
            }
        }

        return $flTotalReceitas;
    }

    public function recuperaListaAcoes($inNumPrograma = '', $inCodAcaoInicio = '', $inCodAcaoFim = '', $inCodPPA = '')
    {
        $stFiltro    = '';
        $stOrdem     = 'cod_acao DESC';
        $stSeparador = ' ';

        if ($inNumPrograma) {
            # Obtem código do programa a partir do número do programa.
            $inCodPrograma = $this->recuperaCodPrograma($inCodPPA, $inNumPrograma);

            if ($inCodPrograma) {
                $stFiltro .= $stSeparador . 'programa.cod_programa = ' . $inCodPrograma;
                $stSeparador = ' AND ';
            }
        }

        if ($inCodAcaoInicio) {
            $stFiltro .= $stSeparador . 'cod_acao >= ' . $inCodAcaoInicio;
            $stSeparador = ' AND ';
        }

        if ($inCodAcaoFim) {
            $stFiltro .= $stSeparador . 'cod_acao <= ' . $inCodAcaoFim;
            $stSeparador = ' AND ';
        }

        if ($inCodPPA) {
            $stFiltro .= $stSeparador . 'ppa.cod_ppa = ' . $inCodPPA;
            $stSeparador = ' AND ';
        }

        return $this->pesquisa('TPPAAcao', 'recuperaListaAcoes', $stFiltro, $stOrdem);
    }

    public function recuperaAcao($inCodAcao, $boTransacao = '')
    {
        return $this->pesquisa('TPPAAcao', 'recuperaDados', "acao.cod_acao = $inCodAcao", '', $boTransacao);

    }

    public function recuperaQuantidades($inCodAcao, $tsAcaoDados, $boTransacao = '')
    {
        $stFiltro = " WHERE cod_acao = $inCodAcao AND timestamp_acao_dados = '$tsAcaoDados' ";

        return $this->pesquisa('TPPAAcaoQuantidade', 'recuperaTodos', $stFiltro, '', $boTransacao);
    }

    public function recuperaRecursos($inCodAcao = '', $tsAcaoDados = '', $inCodRecurso = '', $inExercicio = '', $boTransacao = '')
    {
        $stFiltro    = '';
        $stSeparador = '';
        $stOrdem     = ' exercicio_recurso ASC ';

        if ($inCodAcao) {
            $stFiltro .= 'ano1.cod_acao = ' . $inCodAcao;
            $stSeparador = ' AND ';
        }

        if ($tsAcaoDados) {
            $stFiltro .= $stSeparador . "ano1.timestamp_acao_dados = '" . $tsAcaoDados . "'";
            $stSeparador = ' AND ';
        }

        if ($inCodRecurso) {
            $stFiltro .= $stSeparador . 'ano1.cod_recurso = ' . $inCodRecurso;
            $stSeparador = ' AND ';
        }

        if ($inExercicio) {
            $stFiltro .= $stSeparador . 'ano1.exercicio_recurso = ' . $inExercicio;
            $stSeparador = ' AND ';
        }

        return $this->pesquisa('TPPAAcaoRecurso', 'recuperaRecursosAcao', $stFiltro, '', $boTransacao);
    }

    public function buscarMetasFisicas(array $arConfig)
    {
        $stFiltro  = "  AND ano1.cod_acao             = ".$arConfig['inCodAcao'];
        $stFiltro .= "\n  AND ano1.timestamp_acao_dados = '".$arConfig['tsAcaoDados']."'";
        $stFiltro .= "\n  AND ano1.cod_recurso          IN (".implode(',', $arConfig['arCodRecurso']).")";
        $stFiltro .= "\n  AND ano1.exercicio_recurso    IN (".implode(',', $arConfig['arExercicioRecurso']).")";

        $obTPPAAcaoQuantidade = new TPPAAcaoQuantidade;
        $obTPPAAcaoQuantidade->recuperaQuantidadesAcao($rsMetas, $stFiltro);

        return $rsMetas;
    }

    public function recuperaNorma($inCodAcao, $tsAcaoDados, $boTransacao = '')
    {
        $stFiltro = '';
        $stSeparador = ' WHERE ';

        if ($inCodAcao) {
            $stFiltro   .= $stSeparador . 'cod_acao = ' . $inCodAcao;
            $stSeparador = ' AND ';
        }

        if ($tsAcaoDados) {
            $stFiltro .= $stSeparador . "timestamp_acao_dados = '" . $tsAcaoDados . "'";
        }

        return $this->pesquisa('TPPAAcaoNorma', 'recuperaTodos', $stFiltro, '', $boTransacao);
    }

    /**
     * Verifica se o número da ação já foi usado.
     */
    public function verificaAcao($inCodPrograma = '', $inCodAcao = '', $inCodPPA = '', $boTransacao = '')
    {
        $stFiltro    = ' WHERE ';
        $stSeparador = ' ';

        if ($inCodPrograma) {
            $stFiltro .= $stSeparador . 'cod_programa = ' . $inCodPrograma;
            $stSeparador = ' AND ';
        }
        if ($inCodAcao) {
            $stFiltro .= $stSeparador . 'num_acao::INTEGER = ' . $inCodAcao;
            $stSeparador = ' AND ';
        }
        if ($inCodPPA) {
            $stFiltro .= $stSeparador . 'cod_ppa = ' . $inCodPPA;
        }
        $rsAcoes = $this->pesquisa('TPPAAcao', 'recuperaListaAcoesProgramas', $stFiltro, '', $boTransacao);

        return $rsAcoes;
    }

    /**
     * Monta filtro para Busca de Ação com ANDs
     * @param  array  $arParam lista de elementos a filtrar
     * @return string o filtro montado
     */
    public function getFiltro($arParam)
    {
        $arFiltros = array();
        $stFiltro = '';

        if ($arParam['inCodAcao']) {
            $arFiltros[] = 'acao.cod_acao = ' . $arParam['inCodAcao'] . '::integer';
        }
        if ($arParam['inCodPPA']) {
            $arFiltros[] = 'ppa.cod_ppa = ' . $arParam['inCodPPA'];
        }
        if ($arParam['inCodPrograma']) {
            $arFiltros[] = 'programa.cod_programa = ' . $arParam['inCodPrograma'];
        }
        if ($arParam['inNumPrograma']) {
            $arFiltros[] = 'programa.cod_programa = ' . $arParam['inNumPrograma'];
        }
        if ($arParam['inCodAcaoInicio']) {
            $arFiltros[] = 'acao.cod_acao::integer >= ' . $arParam['inCodAcaoInicio'] . '::integer';
        }
        if ($arParam['inCodAcaoFim']) {
            $arFiltros[] = 'acao.cod_acao::integer <= ' . $arParam['inCodAcaoFim'] . '::integer';
        }
        if ($arParam['stHdnTitulo']) {
            $arFiltros[] = "acao_dados.titulo ILIKE '" . $arParam['stHdnTitulo'] . "' ";
        }

        if ($arFiltros) {
            foreach ($arFiltros as $stChave => $stValor) {
                if ($stChave == 0) {
                    $stFiltro .= $stValor;
                } else {
                    $stFiltro .= ' AND ' . $stValor;
                }
            }
        }

        return $stFiltro;
    }

    public function buscaAcao($arParam)
    {
        $stFiltro = $this->getFiltro($arParam);
        if ($arParam['stAcao'] == 'alterar') {
            $rsListaAcao = $this->pesquisa('TPPAAcao', 'recuperaListaAcoesProgramas', $stFiltro, '', $boTransacao);
        } else {
            $rsListaAcao = $this->pesquisa('TPPAAcao', 'recuperaListaAcoesProgramasExclusao', $stFiltro, '', $boTransacao);
        }

        return $rsListaAcao;
    }

    /**
     * Verifica se um PPA está homologado
     * Retorna true se o PPA estiver homologado
     *
     * @param  int  $inCodPPA
     * @return bool
     */
    public function isPPAHomologado($inCodPPA, $boTransacao = '')
    {
        $obRSNorma = new RecordSet;
        $stFiltro = " WHERE ppn.cod_ppa = $inCodPPA";
        $obTPPANorma = new TPPAPPANorma;
        $obTPPANorma->recuperaPPANorma($obRSNorma, $stFiltro, '', $boTransacao);

        if (count($obRSNorma->arElementos) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function pesquisaDestinacao($inCodPPA, $boTransacao = '')
    {
        $stFiltro = ' WHERE cod_ppa = ' . $inCodPPA;
        $rsPPA = $this->pesquisa('TPPA', 'recuperaTodos', $stFiltro, '', $boTransacao);
        $boDestinacao = false;

        if (!$rsPPA->eof()) {
            $boDestinacao = $rsPPA->getCampo('destinacao_recurso') == 't' ? true : false;
        }

        return $boDestinacao;
    }

    /**
     * Armazena o recordset de acção dados num array com nomenclatura
     * esperada pelos métodos da classe
     *
     * @param  array     $arParametros
     * @param  RecordSet $rsAcao
     * @param  string    $timeStamp    "Y-m-d H:i:s.u"
     * @return void      $arParametros por referência
     */
    private function criarParametrosAcaoDados(&$arParametros, RecordSet $rsAcao, $timeStamp = null)
    {
        if (isset($timeStamp)) {
            $arParametros['tsAcaoDados'] = $timeStamp;
        } else {
            $arParametros['tsAcaoDados'] = $rsAcao->getCampo('timestamp_acao_dados');
        }
        $arParametros['inCodAcao']          = $rsAcao->getCampo('cod_acao');
        $arParametros['inCodTipo']          = $rsAcao->getCampo('cod_tipo');
        $arParametros['inCodProduto']       = $rsAcao->getCampo('cod_produto');
        $arParametros['inCodRegiao']        = $rsAcao->getCampo('cod_regiao');
        $arParametros['inCodContrato']      = $rsAcao->getCampo('cod_contrato');
        $arParametros['inExercicio']        = $rsAcao->getCampo('exercicio');
        $arParametros['inCodFuncao']        = $rsAcao->getCampo('cod_funcao');
        $arParametros['inCodSubFuncao']     = $rsAcao->getCampo('cod_subfuncao');
        $arParametros['inExercicioUnidade'] = $rsAcao->getCampo('exercicio_unidade');
        $arParametros['inNumUnidade']       = $rsAcao->getCampo('num_unidade');
        $arParametros['inNumOrgao']         = $rsAcao->getCampo('num_orgao');
        $arParametros['inCodGrandeza']      = $rsAcao->getCampo('cod_grandeza');
        $arParametros['inCodUnidadeMedida'] = $rsAcao->getCampo('cod_unidade_medida');
        $arParametros['inCodEntidade']      = $rsAcao->getCampo('cod_entidade');
    }

    public function recuperaAcaoDespesa($inCodAcao, $inAno, $inCodRecurso, $boTransacao = '')
    {
        $stFiltro = 'acao.cod_acao = '.$inCodAcao;

        if ($inAno != '') {
            $stFiltro .= ' AND acao_quantidade.ano::INTEGER = '.$inAno;
        }

        if ($inCodRecurso != '') {
            $stFiltro .= ' AND acao_recurso.cod_recurso = '.$inCodRecurso;
        }

        return $this->pesquisa('TPPAAcao', 'recuperaDadosDespesa', $stFiltro, '', $boTransacao);

    }

    public function recuperaDadosRecursos($inCodAcao = '', $tsAcaoDados = '', $inCodRecurso = '', $inAno = '', $boTransacao = '')
    {
        $stFiltro    = '';
        $stSeparador = '';
        $stOrdem     = ' acao_recurso.cod_recurso ';

        if ($inCodAcao) {
            $stFiltro .= 'acao_recurso.cod_acao = ' . $inCodAcao;
            $stSeparador = ' AND ';
        }

        if ($tsAcaoDados) {
            $stFiltro .= $stSeparador . "acao_recurso.timestamp_acao_dados = '" . $tsAcaoDados . "'";
            $stSeparador = ' AND ';
        }

        if ($inCodRecurso) {
            $stFiltro .= $stSeparador . 'acao_recurso.cod_recurso = ' . $inCodRecurso;
            $stSeparador = ' AND ';
        }

        if ($inAno) {
            $stFiltro .= $stSeparador . 'acao_recurso.ano = '.$inAno;
            $stSeparador = ' AND ';
        }

        return $this->pesquisa('TPPAAcaoRecurso', 'recuperaDados', $stFiltro, $stOrdem, $boTransacao);
    }

    public function recuperaDadosRecursosDespesa($inCodAcao = '', $inCodRecurso = '', $inAno = '', $boTransacao = '')
    {
        $stFiltro    = '';
        $stOrdem     = ' ORDER BY recursos.cod_recurso ';

        if ($inCodAcao) {
            $stFiltro .= ' AND recursos.cod_acao = ' . $inCodAcao;
        }

        if ($inCodRecurso) {
            $stFiltro .= ' AND recursos.cod_recurso = ' . $inCodRecurso;
        }

        if ($inAno) {
            $stFiltro .= ' AND recursos.ano = '.$inAno;
        }

        return $this->pesquisa('TPPAAcaoRecurso', 'recuperaDadosDespesa', $stFiltro, $stOrdem, $boTransacao);
    }

    public function verificaArrendondarValor($inCodPPA)
    {
        $boReturn = true;
        if (SistemaLegado::pegaDado('cod_ppa', 'ppa.ppa_precisao', 'WHERE cod_ppa = '.$inCodPPA) == '') {
            $boReturn = false;
        }

        return $boReturn;
    }

    public function incluirRecursoDestinacao(&$inCodRecurso, $arParametros)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = '';

        # Inicia nova transação.
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $stExercicioRecurso = $arParametros['stExercicioRecurso'];
        $inCodEspecificacao = $arParametros['inCodEspecificacao'];

        $obTOrcamentoRecurso = new TOrcamentoRecurso;
        $obTOrcamentoRecurso->setDado('exercicio', $stExercicioRecurso);
        $obTOrcamentoRecurso->proximoCod($inCodRecurso, $boTransacao);
        $obTOrcamentoRecurso->setDado('cod_recurso', $inCodRecurso);
        $obErro = $obTOrcamentoRecurso->inclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
            $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , $stExercicioRecurso);
            $obTOrcamentoRecursoDestinacao->setDado('cod_recurso'      , $inCodRecurso);
            $obTOrcamentoRecursoDestinacao->setDado('cod_uso'          , $arParametros['inCodUso']);
            $obTOrcamentoRecursoDestinacao->setDado('cod_destinacao'   , $arParametros['inCodDestinacao']);
            $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
            $obTOrcamentoRecursoDestinacao->setDado('cod_detalhamento' , $arParametros['inCodDetalhamento']);
            $obErro = $obTOrcamentoRecursoDestinacao->inclusao($boTransacao);

            if (!$obErro->ocorreu() && $arParametros['stExercicioRecurso'] == Sessao::getExercicio()) {
                $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;
                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio'        , $stExercicioRecurso);
                $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $inCodEspecificacao);
                $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                // Verifica qual o cod_recurso que possui conta contabil vinculada C
                $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , $stExercicioRecurso);
                $obTOrcamentoRecursoDestinacao->setDado('cod_recurso'      , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_uso'          , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_destinacao'   , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_detalhamento' , '');
                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural'   , "'2.9.3.2.0.00.00.%'");
                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC, '', '', $boTransacao);

                $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                if ($inCodRecursoBuscaC == '') {
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
                            $stCodEstruturalC        = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';

                            $obRContabilidadePlanoBancoC->setCodEstrutural ($stCodEstruturalC);
                            $obRContabilidadePlanoBancoC->setNomConta      ($stNomEspecificacao);
                            $obRContabilidadePlanoBancoC->setExercicio     ($stExercicioRecurso);
                            $obRContabilidadePlanoBancoC->setNatSaldo      ('C');
                            $obRContabilidadePlanoBancoC->setContaAnalitica(true);
                            $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);

                            $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao, false);
                        } else {
                            $obErro->setDescricao('Limite de Contas Excedido');
                        }
                    }
                }

                // Verifica qual o cod_recurso que possui conta contabil vinculada D
                $obTOrcamentoRecursoDestinacao->setDado('exercicio'        , $stExercicioRecurso);
                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
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
                            $stCodEstruturalD        = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';

                            $obRContabilidadePlanoBancoD->setCodEstrutural ($stCodEstruturalD);
                            $obRContabilidadePlanoBancoD->setNomConta      ($stNomEspecificacao);
                            $obRContabilidadePlanoBancoD->setExercicio     ($stExercicioRecurso);
                            $obRContabilidadePlanoBancoD->setNatSaldo      ('D');
                            $obRContabilidadePlanoBancoD->setContaAnalitica(true);
                            $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);

                            $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao);
                        } else {
                            $obErro->setDescricao('Limite de Contas Excedido');
                        }
                    }
                }
            }
        }

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoRecurso);

        return $obErro;
    }
}
?>
