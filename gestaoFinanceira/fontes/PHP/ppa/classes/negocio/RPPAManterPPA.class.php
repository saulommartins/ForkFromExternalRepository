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
    * Classe de negocio Manter PPA
    * Data de Criação: 21/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * Casos de uso: uc-02.09.01
*/

require_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPAEncaminhamento.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPANorma.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPAPublicacao.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaRecursoValor.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaRecurso.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaInativaNorma.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaDados.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceita.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoQuantidade.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoRecurso.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoNorma.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoDados.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcao.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPrecisao.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPAPrecisao.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPrograma.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAProgramaResponsavel.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAProgramaDados.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAMacroObjetivo.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoUnidadeExecutora.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAProgramaTemporarioVigencia.class.php';
require_once CAM_GF_PPA_NEGOCIO    . 'RPPAManterPrograma.class.php';
require_once CAM_GF_PPA_NEGOCIO    . 'RPPAManterReceita.class.php';
require_once CAM_GF_PPA_NEGOCIO    . 'RPPAManterAcao.class.php';
require_once CAM_GF_PPA_NEGOCIO    . 'RPPAGerarDadosPPA.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TTCETOAcaoIdentificadorAcao.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TTCEALAcaoIdentificadorAcao.class.php';
            
class RPPAManterPPA
{
    public $inCodPPA,
           $stExercicio;
    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stFiltro     o critério que delimita a busca
     * @return RecordSet
     */
    public function pesquisa($stMapeamento, $stMetodo, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obMapeamento = new $stMapeamento();

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . $stFiltro;
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $obMapeamento->$stMetodo($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $rsRecordSet;
    }

    /**
     * Importa ação de um programa para novo programa.
     * @param  integer $inCodAcao     código da ação a importar
     * @param  integer $inCodPrograma código do novo programa
     * @param  string  $tsHomologacao timestamp da homologação
     * @param  boolean $boTransacao   se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaAcao($inCodAcao, $inCodPPA, $inCodPrograma, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        # Encontra acao_dados anterior à homologação.
        $stFiltro = 'cod_acao = ' . $inCodAcao . " AND timestamp_acao_dados < '" . $tsHomologacao . "'";
        $stOrdem = 'timestamp_acao_dados DESC';
        $rsAcaoDados = $this->pesquisa('TPPAAcaoDados', 'recuperaTodos', $stFiltro, $stOrdem, $boTransacao);

        if ($rsAcaoDados->eof()) {
            # Nenhuma ação incluida antes da homologação.

            return $obErro;
        }

        $tsImportacao = $rsAcaoDados->getCampo('timestamp_acao_dados');

        # Inclui novo programa e programa_dados.
        $obRPPAManterAcao = new RPPAManterAcao();

        # Recupera dados da quantidade da ação.
        $rsQuantidades = $obRPPAManterAcao->recuperaQuantidades($inCodAcao, $tsImportacao, $boTransacao);

        if ($rsQuantidades->eof()) {
            $obErro->setDescricao('Quantidades não encontradas!()');

            return $obErro;
        }

        $arParametros = array();

        while (!$rsQuantidades->eof()) {
            if ($_REQUEST['boImportarValorAcao'] == 'true') {
                $arParametros['flQuantidade_'.$rsQuantidades->getCampo('ano').'_'.$rsQuantidades->getCampo('cod_recurso')] = $rsQuantidades->getCampo('quantidade');
                $arParametros['flValor_'.$rsQuantidades->getCampo('ano').'_'.$rsQuantidades->getCampo('cod_recurso')]      = $rsQuantidades->getCampo('valor');
                $arParametros['flValorTotal_'.$rsQuantidades->getCampo('ano').'_'.$rsQuantidades->getCampo('cod_recurso')] = $rsQuantidades->getCampo('valor') * $rsQuantidades->getCampo('quantidade');
            } else {
                $arParametros['flQuantidade_'.$rsQuantidades->getCampo('ano')] = 0;
                $arParametros['flValor_'.$rsQuantidades->getCampo('ano')]      = 0;
                $arParametros['flValorTotal_'.$rsQuantidades->getCampo('ano')] = 0;
            }
            $rsQuantidades->proximo();
        }

        # Recupera norma da ação.
        $rsAcaoNorma = $obRPPAManterAcao->recuperaNorma($inCodAcao, $tsImportacao, $boTransacao);
        $inCodNorma = $rsAcaoNorma->getCampo('cod_norma');
        
        //Estado de AL = 2
        //Estado de TO = 27
        $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
        if ($inCodUf == 2) {
            # Encontra o codigo de identificacao da acao.
            $stFiltro = 'cod_acao = ' . $inCodAcao ;
            $rsAcaoIdentificador = $this->pesquisa('TTCEALAcaoIdentificadorAcao', 'recuperaTodos', $stFiltro, '', $boTransacao);
            $arParametros['inCodIdentificadorAcao'] = $rsAcaoIdentificador->getCampo('cod_identificador');
        }
        
        if ($inCodUf == 27) {
            # Encontra o codigo de identificacao da acao.
            $stFiltro = 'cod_acao = ' . $inCodAcao ;
            $rsAcaoIdentificador = $this->pesquisa('TTCETOAcaoIdentificadorAcao', 'recuperaTodos', $stFiltro, '', $boTransacao);
            $arParametros['inCodIdentificadorAcao'] = $rsAcaoIdentificador->getCampo('cod_identificador');
        }
        
        $arRecursos    = array();
        $arExeRecurso  = array();
        $arCodRecurso  = array();
        $inSizeRecurso = 0;

        $obTPPAAcaoUnidadeExecutora = new TPPAAcaoUnidadeExecutora;
        $obTPPAAcaoUnidadeExecutora->setDado('cod_acao', $inCodAcao);
        $obTPPAAcaoUnidadeExecutora->setDado('timestamp_acao_dados', $tsImportacao);
        $obTPPAAcaoUnidadeExecutora->recuperaUnidadeExecutora($rsAcaoUnidadeExecutora, '', '', $boTransacao);
        $arParametros['stUnidadeOrcamentaria'] = $rsAcaoUnidadeExecutora->getCampo('num_orgao').'.'.$rsAcaoUnidadeExecutora->getCampo('num_unidade');

        if ($_REQUEST['boImportarValorAcao'] == 'true') {
            # Recupera recursos da ação.
            $rsRecursos = $obRPPAManterAcao->recuperaRecursos($inCodAcao, $tsImportacao, '', '', $boTransacao);

            if ($rsRecursos->eof()) {
                $obErro->setDescricao('Recursos não encontrados!()');

                return $obErro;
            }

            $inSizeRecurso = count($rsRecursos->arElementos);

            while (!$rsRecursos->eof()) {
                $arLinha = array();

                $arExeRecurso[]  = $rsRecursos->getCampo('exercicio_recurso');
                $arCodRecurso[]  = $rsRecursos->getCampo('cod_recurso');
                $arRecursos[1][] = $rsRecursos->getCampo('ano1');
                $arRecursos[2][] = $rsRecursos->getCampo('ano2');
                $arRecursos[3][] = $rsRecursos->getCampo('ano3');
                $arRecursos[4][] = $rsRecursos->getCampo('ano4');

                $rsRecursos->proximo();
            }
        }

        # Pega o restante dos dados de acao e tabelas relacionadas.
        $arParametros['inCodAcao']            = '';
        $arParametros['inNumAcao']            = $inCodAcao;
        $arParametros['inCodPrograma']        = $inCodPrograma;
        $arParametros['tsAcaoDados']          = date('Y-m-d H:i:s');
        $arParametros['stDescricao']          = $rsAcaoDados->getCampo('descricao');
        $arParametros['inCodTipo']            = $rsAcaoDados->getCampo('cod_tipo');
        $arParametros['inCodProduto']         = $rsAcaoDados->getCampo('cod_produto');
        $arParametros['inCodRegiao']          = $rsAcaoDados->getCampo('cod_regiao');
        $arParametros['inExercicio']          = $rsAcaoDados->getCampo('exercicio');
        $arParametros['inCodFuncao']          = $rsAcaoDados->getCampo('cod_funcao');
        $arParametros['inCodSubFuncao']       = $rsAcaoDados->getCampo('cod_subfuncao');
        $arParametros['inCodGrandeza']        = $rsAcaoDados->getCampo('cod_grandeza');
        $arParametros['inCodUnidadeMedida']   = $rsAcaoDados->getCampo('cod_unidade_medida');
        $arParametros['flValorEstimado']      = $rsAcaoDados->getCampo('valor_estimado');
        $arParametros['flMetaEstimada']       = $rsAcaoDados->getCampo('meta_estimada');
        $arParametros['stTitulo']             = $rsAcaoDados->getCampo('titulo');
        $arParametros['stFinalidade']         = $rsAcaoDados->getCampo('finalidade');
        $arParametros['slFormaImplementacao'] = $rsAcaoDados->getCampo('cod_forma');
        $arParametros['slTipoOrcamento']      = $rsAcaoDados->getCampo('cod_tipo_orcamento');
        $arParametros['stDetalhamento']       = $rsAcaoDados->getCampo('detalhamento');
        $arParametros['slNaturezaDespesa']    = $rsAcaoDados->getCampo('cod_natureza');
        $arParametros['boImportarValorAcao']  = $_REQUEST['boImportarValorAcao'];
        $arParametros['boImportacao']         = true;
        $arParametros['boHomologado']         = $inCodNorma ? true : false;
        $arParametros['inCodNorma']           = $inCodNorma;
        $arParametros['inSizeRecurso']        = $inSizeRecurso;
        $arParametros['arExeRecurso']         = $arExeRecurso;
        $arParametros['arCodRecurso']         = $arCodRecurso;
        $arParametros['arRecursos']           = $arRecursos;
        $arParametros['inCodPPA']             = $inCodPPA;

        return $obRPPAManterAcao->incluiAcao($arParametros, $boTransacao);
    }

    /**
     * Importa todos as ações do programa para o novo programa.
     * @param  integer $inCodPrograma      código do novo programa
     * @param  integer $inCodProgImportado código do programa importado
     * @param  string  $tsHomologacao      timestamp da homologação
     * @param  boolean $boTransacao        se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaAcoes($inCodPrograma, $inCodPPA, $inCodProgImportado, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        $stFiltro = 'acao.cod_programa = ' . $inCodProgImportado;
        $rsAcoes = $this->pesquisa('TPPAAcao', 'recuperaTodos', $stFiltro, '', $boTransacao);

        while (!$rsAcoes->eof()) {
            $inCodAcao = $rsAcoes->getCampo('cod_acao');

            $obErro = $this->importaAcao($inCodAcao, $inCodPPA, $inCodPrograma, $tsHomologacao, $boTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsAcoes->proximo();
        }

        return $obErro;
    }

    /**
     * Importa todas as vigências de temporários.
     * @param  integer $inCodPrograma      código do novo programa
     * @param  integer $tsProgramaDados    timestamp do novo programa
     * @param  integer $inCodProgImportado código do programa importado
     * @param  string  $tsImportacao       timestamp da última alteração antes da homologação
     * @param  boolean $boTransacao        se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaTemporariosVigencia($inCodPrograma, $tsProgramaDados, $inCodProgImportado, $tsImportacao, $boTransacao)
    {
        $obErro = new Erro();

        $stFiltro = 'cod_programa = '. $inCodProgImportado . " AND timestamp_programa_dados = '" . $tsImportacao . "'";
        $rsTemporarioVigencia = $this->pesquisa('TPPAProgramaTemporarioVigencia', 'recuperaTodos', $stFiltro, '', $boTransacao);

        $obRPrograma = new RPPAManterPrograma();

        while (!$rsTemporarioVigencia->eof()) {
            $arParametros['inCodPrograma']   = $inCodPrograma;
            $arParametros['tsProgramaDados'] = $tsProgramaDados;
            $arParametros['stDataInicial']   = $rsTemporarioVigencia->getCampo('dt_inicial');
            $arParametros['stDataFinal']     = $rsTemporarioVigencia->getCampo('dt_final');
            $arParametros['flValorGlobal']   = $rsTemporarioVigencia->getCampo('valor_global');
            $obErro = $obRPrograma->incluiTemporarioVigencia($arParametros, $boTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsTemporarioVigencia->proximo();
        }

        return $obErro;
    }

    /**
     * Importa todos os indicadores para o novo programa.
     * @param  integer $inCodPrograma      código do novo programa
     * @param  integer $tsProgramaDados    timestamp do novo programa
     * @param  integer $inCodProgImportado código do programa importado
     * @param  string  $tsImportacao       timestamp da última alteração antes da homologação
     * @param  boolean $boTransacao        se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaIndicadores($inCodPrograma, $tsProgramaDados, $inCodProgImportado, $tsImportacao, $boTransacao)
    {
        $obErro = new Erro();

        $stFiltro = 'cod_programa = '. $inCodProgImportado . " AND timestamp_programa_dados = '" . $tsImportacao . "'";
        $stOrdem  = 'timestamp_programa_dados DESC';
        $rsIndicadores = $this->pesquisa('TPPAProgramaIndicadores', 'recuperaTodos', $stFiltro, $stOrdem, $boTransacao);

        $obRPrograma = new RPPAManterPrograma();

        while (!$rsIndicadores->eof()) {
            $arParametros['inCodPrograma']          = $inCodPrograma;
            $arParametros['tsProgramaDados']        = $tsProgramaDados;
            $arParametros['inCodIndicador']         = $rsIndicadores->getCampo('cod_indicador');
            $arParametros['flIndiceRecente']        = $rsIndicadores->getCampo('indice_recente');
            $arParametros['stDescricao']            = $rsIndicadores->getCampo('descricao');
            $arParametros['flIndiceDesejado']       = $rsIndicadores->getCampo('indice_desejado');
            $arParametros['inCodPeriodicidade']     = $rsIndicadores->getCampo('cod_periodicidade');
            $arParametros['inCodUnidadeIndicador']  = $rsIndicadores->getCampo('cod_unidade');
            $arParametros['inCodGrandezaIndicador'] = $rsIndicadores->getCampo('cod_grandeza');
            $arParametros['stFonte']                = $rsIndicadores->getCampo('fonte');
            $arParametros['stBaseGeografica']       = $rsIndicadores->getCampo('base_geografica');
            $arParametros['stFormaCalculo']         = $rsIndicadores->getCampo('forma_calculo');
            $arParametros['dtIndiceRecente']        = $rsIndicadores->getCampo('dt_indice_recente');

            $obErro = $obRPrograma->incluiIndicador($arParametros, $boTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsIndicadores->proximo();
        }

        return $obErro;
    }

    /**
     * Importa um programa, seus responsáveis, órgãos e ações.
     * @param  integer $inCodPrograma código do programa a importar
     * @param  integer $inCodPPA      código do PPA do novo programa
     * @param  integer $inCodSetorial código do Programa setorial a importar
     * @param  string  $tsHomologacao timestamp de homologação do programa a importar
     * @param  boolean $boTransacao   se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaPrograma($inCodProgImportado, $inCodSetorial, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        # Encontra programa_dados anterior à homologação.
        $stFiltro = 'cod_programa = ' . $inCodProgImportado . " AND timestamp_programa_dados < '" . $tsHomologacao . "'";
        $stOrdem  = 'timestamp_programa_dados DESC';
        $rsProgramasDados = $this->pesquisa('TPPAProgramaDados', 'recuperaTodos', $stFiltro, $stOrdem, $boTransacao);

        if ($rsProgramasDados->eof()) {
            # Programa não contem dados prévios à homologação.

            return $obErro;
        }

        $tsImportacao    = $rsProgramasDados->getCampo('timestamp_programa_dados');
        $tsProgramaDados = date('Y-m-d H:i:s');

        $obRPPA = new RPPAManterPPA();
        $rsPPA = $obRPPA->pesquisa('TPPA', 'recuperaTodos', " cod_ppa = $inCodPPA ", '', $boTransacao);

        # Pega os dados de programa_dados.
        $arParametros['inCodPrograma']      = '';
        $arParametros['inCodPPA']           = $inCodPPAImportado;
        $arParametros['inNumPrograma']      = $inCodProgImportado;
        $arParametros['inCodSetorial']      = $inCodSetorial;
        $arParametros['tsProgramaDados']    = $tsProgramaDados;
        $arParametros['inAnoInicioPPA']     = $rsPPA->getCampo('ano_inicio');
        $arParametros['inAnoFinalPPA']      = $rsPPA->getCampo('ano_final');
        $arParametros['inIdentificacao']    = $rsProgramasDados->getCampo('identificacao');
        $arParametros['inDiagnostico']      = $rsProgramasDados->getCampo('diagnostico');
        $arParametros['inObjetivo']         = $rsProgramasDados->getCampo('objetivo');
        $arParametros['inDiretriz']         = $rsProgramasDados->getCampo('diretriz');
        $arParametros['boNatureza']         = $rsProgramasDados->getCampo('continuo');
        $arParametros['stPublicoAlvo']      = $rsProgramasDados->getCampo('publico_alvo');
        $arParametros['inCodTipoPrograma']  = $rsProgramasDados->getCampo('cod_tipo_programa');
        $arParametros['stJustificativa']    = $rsProgramasDados->getCampo('justificativa');
        $arParametros['stExercicioUnidade'] = $rsProgramasDados->getCampo('exercicio_unidade');
        $arParametros['inNumUnidade']       = $rsProgramasDados->getCampo('num_unidade');
        $arParametros['inNumOrgao']         = $rsProgramasDados->getCampo('num_orgao');

        # Inclui novo programa e programa_dados.
        $obRPPAManterPrograma = new RPPAManterPrograma();
        $obErro = $obRPPAManterPrograma->incluiPrograma($arParametros, $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        $obErro = $obRPPAManterPrograma->incluiProgramaDados($arParametros, $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        $inCodPrograma = $arParametros['inCodPrograma'];

        # Importa os indicdores do programa importado.
        $obErro = $this->importaIndicadores($inCodPrograma, $tsProgramaDados, $inCodProgImportado, $tsImportacao, $boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        if ($arParametros['boNatureza'] == 'f') {
            $obErro = $this->importaTemporariosVigencia($inCodPrograma, $tsProgramaDados, $inCodProgImportado, $tsImportacao, $boTransacao);
        }

        if (!$obErro->ocorreu()) {
            # Importa as ações que pertencem ao programa.
            $obErro = $this->importaAcoes($inCodPrograma, $inCodPPA, $inCodProgImportado, $tsHomologacao, $boTransacao);
        }

        return $obErro;
    }

    /**
     * Importa programas com mesmo código do PPA.
     * @param  integer $inCodSetorialImportado código do programa setorial a importar
     * @param  integer $inCodSetorial          código do programa setorial novo
     * @param  string  $tsHomologacao          data de homologação do PPA
     * @param  boolean $boTransacao            se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaProgramas($inCodSetorialImportado, $inCodSetorial, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        $stFiltro = 'programa.cod_setorial = ' . $inCodSetorialImportado . " AND ativo = 't'";
        $rsProgramas = $this->pesquisa('TPPAPrograma', 'recuperaPrograma', $stFiltro, '', $boTransacao);

        while (!$rsProgramas->eof()) {
            $inCodPrograma = $rsProgramas->getCampo('cod_programa');

            $obErro = $this->importaPrograma($inCodPrograma, $inCodSetorial, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsProgramas->proximo();
        }

        return $obErro;
    }

    /**
     * Importa um programa setorial e seus programas.
     * @param  integer $inCodSetorialImportado código do programa setorial a importar
     * @param  integer $inCodMacro             código do PPA do novo macro objetivos
     * @param  string  $tsHomologacao          timestamp de homologação
     * @param  boolean $boTransacao            se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaSetorial($inCodSetorialImportado, $inCodMacro, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        # Encontra programa_dados anterior à homologação.
        $stFiltro = 'cod_setorial = ' . $inCodSetorialImportado . " AND timestamp < '" . $tsHomologacao . "'";
        $stOrdem  = 'timestamp DESC';
        $rsSetorial = $this->pesquisa('TPPAProgramaSetorial', 'recuperaTodos', $stFiltro, $stOrdem, $boTransacao);

        if ($rsSetorial->eof()) {
            # Programa não contem dados prévios à homologação.

            return $obErro;
        }

        $obTPPAProgramaSetorial = new TPPAProgramaSetorial;
        $obTPPAProgramaSetorial->proximoCod($inCodSetorial, $boTransacao);
        $obTPPAProgramaSetorial->setDado('cod_setorial', $inCodSetorial);
        $obTPPAProgramaSetorial->setDado('cod_macro'   , $inCodMacro);
        $obTPPAProgramaSetorial->setDado('descricao'   , $rsSetorial->getCampo('descricao'));
        $obErro = $obTPPAProgramaSetorial->inclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            # Importa os programas que pertencem ao programa setorial.
            $obErro = $this->importaProgramas($inCodSetorialImportado, $inCodSetorial, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao);
        }

        return $obErro;
    }

    /**
     * Importa programas setoriais com mesmo código da macro.
     * @param  integer $inCodMacroImportado código do macro objetivo importado
     * @param  integer $inCodMacro          código do macro objetivo novo
     * @param  string  $tsHomologacao       data de homologação do PPA
     * @param  boolean $boTransacao         se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaSetoriais($inCodMacroImportado, $inCodMacro, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        $stFiltro = 'programa_setorial.cod_macro = ' . $inCodMacroImportado;
        $rsSetoriais = $this->pesquisa('TPPAProgramaSetorial', 'recuperaTodos', $stFiltro, '', $boTransacao);

        while (!$rsSetoriais->eof()) {
            $inCodSetorial = $rsSetoriais->getCampo('cod_setorial');

            $obErro = $this->importaSetorial($inCodSetorial, $inCodMacro, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsSetoriais->proximo();
        }

        return $obErro;
    }

    /**
     * Importa um macro objetivo e seus programas setoriais.
     * @param  integer $inCodMacroImportado código do macro objetivo a importar
     * @param  integer $inCodPPA            código do PPA do novo macro objetivo
     * @param  integer $inCodSetorial       código do Programa setorial a importar
     * @param  string  $tsHomologacao       timestamp de homologação
     * @param  boolean $boTransacao         se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaMacro($inCodMacroImportado, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        # Encontra programa_dados anterior à homologação.
        $stFiltro = 'cod_macro = ' . $inCodMacroImportado . " AND timestamp < '" . $tsHomologacao . "'";
        $stOrdem  = 'timestamp DESC';
        $rsMacro = $this->pesquisa('TPPAMacroObjetivo', 'recuperaTodos', $stFiltro, $stOrdem, $boTransacao);

        if ($rsMacro->eof()) {
            # Programa não contem dados prévios à homologação.

            return $obErro;
        }

        $obTPPAMacroObjetivo = new TPPAMacroObjetivo;
        $obTPPAMacroObjetivo->proximoCod($inCodMacro, $boTransacao);
        $obTPPAMacroObjetivo->setDado('cod_macro', $inCodMacro);
        $obTPPAMacroObjetivo->setDado('cod_ppa'  , $inCodPPA);
        $obTPPAMacroObjetivo->setDado('descricao', $rsMacro->getCampo('descricao'));
        $obErro = $obTPPAMacroObjetivo->inclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            # Importa os programas setoriais que pertencem a macro.
            $obErro = $this->importaSetoriais($inCodMacroImportado, $inCodMacro, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao);
        }

        return $obErro;
    }

    /**
     * Importa macros objetivos com mesmo código do PPA.
     * @param  integer $inCodPPA          código do PPA
     * @param  integer $inCodPPAImportado código do PPA a importar
     * @param  string  $tsHomologacao     data de homologação do PPA
     * @param  boolean $boTransacao       se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaMacros($inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao = '')
    {
        $obErro = new Erro();

        $stFiltro = 'macro_objetivo.cod_ppa = ' . $inCodPPAImportado;
        $rsMacros = $this->pesquisa('TPPAMacroObjetivo', 'recuperaTodos', $stFiltro, '', $boTransacao);

        while (!$rsMacros->eof()) {
            $inCodMacro = $rsMacros->getCampo('cod_macro');

            $obErro = $this->importaMacro($inCodMacro, $inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsMacros->proximo();
        }

        return $obErro;
    }

    /**
     * Importa PPA antigo com base em dados do PPA atual.
     * @param  integer $inCodPPA    número do PPA atual, caso alteração
     * @param  integer $inAnoInicio ano início do PPA atual
     * @param  boolean $boTransacao se dentro de uma transação
     * @return Erro    objeto com descrição do erro em caso de erro
     */
    private function importaPPA($inCodPPA, $inAnoInicio, $boTransacao = '')
    {
        $obErro = new Erro();
        $obMapeamento = new TPPA();

        $inAnoInicio = $_REQUEST['stAnoInicio'];
        $stFiltro  = " WHERE ano_inicio = '" . ($inAnoInicio - 4) . "' AND ano_final = '" . ($inAnoInicio - 1) . "'";
        $obMapeamento->recuperaPPA($rsLista, $stFiltro, '', $boTransacao);

        $inCodPPAImportado = $rsLista->getCampo('cod_ppa');
        $tsHomologacao     = date('Y-m-d H:i:s');

        if ($inCodPPAImportado) {
            $obErro = $this->importaMacros($inCodPPA, $inCodPPAImportado, $tsHomologacao, $boTransacao);
        }

        return $obErro;
    }

    /**
     * Importa dados de PPA existente para o próximo PPA.
     * @param array $arParametros os parâmetros necessários
     */
    public function importar(array $arParametros)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = '';
        $stFiltro        = " pn.cod_norma IS NULL";
        $stOrdem         = " p.ano_inicio";

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            return SistemaLegado::exibeAviso($obErro->getDescricao(), 'n_importar', 'aviso');
        }

        $stFiltro = " ppa.cod_ppa = ";
        $rsPPA = $this->pesquisa('TPPA', 'recuperaDadosPPA', $stFiltro, $stOrdem, $boTransacao);

        $inAnoInicio     = $rsPPA->getCampo('ano_inicio');
        $inAnoFinal      = $rsPPA->getCampo('ano_final');

        if ($rsPPA->eof()) {
            $stFiltro        = " pn.cod_norma IS NOT NULL";
            $stOrdem         = " p.ano_inicio DESC";

            $rsPPA = $this->pesquisa('TPPA', 'recuperaDadosPPA', $stFiltro, $stOrdem, $boTransacao);

            $inAnoInicio     = $rsPPA->getCampo('ano_inicio');
            $inAnoFinal      = $rsPPA->getCampo('ano_final');

            $stMensagem = "Próximo PPA não homologado não encontrado!($inAnoInicio a $inAnoFinal)";

            return SistemaLegado::exibeAviso($stMensagem, 'n_importar', 'aviso');
        }

        $inCodPPA = $rsPPA->getCampo('cod_ppa');
        $obErro = $this->importaPPA($inCodPPA, $inAnoInicio, $boTransacao);

        if ($obErro->ocorreu()) {
            return SistemaLegado::exibeAviso($obErro->getDescricao(), 'n_importar', 'aviso');
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obMapeamento);

        return SistemaLegado::alertaAviso('FMImportarPPA.php?stAcao=importar', "Importar PPA concluído com sucesso! ($inAnoInicio a $inAnoFinal)", 'importar', 'aviso', Sessao::getId());
    }

    public function incluir($arParametros)
    {  
        # Testa se o ano é válido.
        if ($arParametros['stAnoInicio'] % 4 != 2) {
            return SistemaLegado::exibeAviso('Ano inicial inválido!', 'form', 'aviso');
        } elseif ($arParametros['stAnoFinal'] < Sessao::read('exercicio')) {
            return sistemaLegado::exibeAviso("Período informado fora do período de vigência.", 'form', 'aviso');
        } else {
            $timestamp       = date('Y-m-d H:i:s');
            $obMapeamento    = new TPPA;
            $stFiltro        = " where ano_final >= '".$arParametros['stAnoInicio']."' and ano_inicio <= '".$arParametros['stAnoInicio']."'";
            $stOrder         = ' order by ppa.ano_inicio';
            $obTransacao     = new Transacao();
            $boTransacao     = '';
            $boFlagTransacao = false;

            $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

            $obMapeamento->recuperaPPA($rsLista, $stFiltro, $stOrder, $boTransacao);

            if (!count($rsLista->arElementos)) {
                $obMapeamento = new TPPA;
                $obMapeamento->proximoCod($inCodPPA, $boTransacao);
                $obMapeamento->setDado('cod_ppa'           , $inCodPPA);
                $obMapeamento->setDado('timestamp'         , $timestamp);
                $obMapeamento->setDado('ano_inicio'        , $arParametros['stAnoInicio']);
                $obMapeamento->setDado('ano_final'         , $arParametros['stAnoFinal']);
                $obMapeamento->setDado('valor_total_ppa'   , 0);
                $obMapeamento->setDado('destinacao_recurso', $arParametros['boDestRecursos'] == 'on' ? 't' : 'f');
                $obMapeamento->setDado('importado'         , 't');
                $obErro = $obMapeamento->inclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    return SistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'aviso');
                }

                $obRPPAGerarDadosPPA = new RPPAGerarDadosPPA;
                $obRPPAGerarDadosPPA->setExercicioInicioPPA($arParametros['stAnoInicio']);
                $obRPPAGerarDadosPPA->setExercicioReplicar (Sessao::getExercicio());
                $obErro = $obRPPAGerarDadosPPA->incluir($rsRecordSet, $boTransacao);

                if ($obErro->ocorreu()) {
                    return SistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'aviso');
                }

                # Inclui precisão na tabela ppa.ppa_precisao.
                if ($arParametros['inCodPrecisao']) {
                    $obMapeamento = new TPPAPPAPrecisao;
                    $obMapeamento->setDado('cod_ppa', $inCodPPA);
                    $obMapeamento->setDado('cod_precisao', $arParametros['inCodPrecisao']);
                    $obErro = $obMapeamento->inclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return SistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'aviso');
                    }
                }

                if (!$obErro->ocorreu()) {
                    # Importa dados do PPA anterior.
                    $obErro = $this->importaPPA($inCodPPA, $arParametros['stAnoInicio'], $boTransacao);
                }

                if ($obErro->ocorreu()) {
                    return SistemaLegado::exibeAviso($obErro->getDescricao(), 'n_importar', 'aviso');
                }

                # Nenhum erro? Fecha transação.
                $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obMapeamento);

                return sistemaLegado::alertaAviso('FMManterPPA.php?stAcao=incluir',$arParametros['stAnoInicio'].' a '.$arParametros['stAnoFinal'], 'incluir', 'aviso', Sessao::getId());
            } else {
                return sistemaLegado::exibeAviso("Já existe um PPA cadastrado para o período informado.",'n_incluir','aviso' );
            }
        }
    }

    public function listar($arParametros)
    {
        $obTMapeamento = new TPPA();
        $rsRecordSet   = new Recordset;
        $obTMapeamento->recuperaPPA($rsRecordSet, '', ' order by ano_inicio');

        return $rsRecordSet;
    }

    public function listByExercicio(&$rsRecordSet)
    {
        $stFiltro = "
            WHERE ( '" . $this->stExercicio . "' BETWEEN ano_inicio AND ano_final
                    OR '" . $this->stExercicio . "' < ano_inicio )
        ";
        $obTMapeamento = new TPPA();
        $obTMapeamento->recuperaPPA($rsRecordSet, $stFiltro, ' ORDER BY ano_inicio LIMIT 1');
    }

    public function verificaHomologacaoImportacao($arParametros)
    {
        $obMapeamento = new TPPA();
        $rsRecordSet   = new Recordset;

        $inAnoInicio = $arParametros['stAnoInicio'];
        $stFiltro  = " WHERE ano_inicio = '" . ($inAnoInicio - 4) . "' AND ano_final = '" . ($inAnoInicio - 1) . "'";
        $obMapeamento->recuperaPPA($rsLista, $stFiltro, '', $boTransacao);

        if ($rsLista->getNumLinhas() > -1) {
            if ($rsLista->getCampo('homologado') == 't') {
                $boHomologado = true;
            } else {
                $boHomologado = false;
            }
        } else {
            $boHomologado = true;
        }

        return $boHomologado;
    }

    public function excluir($arParametros)
    {
        $inCodPPA = $arParametros['inCodPPA'];

        $obTPPA = new TPPA();
        $obTPPA->setDado('cod_ppa', $inCodPPA);
        $stFiltro = "WHERE cod_ppa = ".$inCodPPA;
        $obErro = $obTPPA->recuperaPPA($rsPPA,$stFiltro,"",$boTransacao);

        if ( !$obErro->ocorreu() ) {
            if ( $rsPPA->getCampo('homologado') == 'true') {
                return sistemaLegado::alertaAviso('LSManterPPA.php?stAcao=excluir','Ocorreu um erro ao excluir, a PPA está Homologado.', 'n_excluir', 'aviso', Sessao::getId());
            }else{
                //Processo para validacao da exclusao da PPA
                $obTPPA = new TPPA();
                $obTPPA->setDado('cod_ppa', $inCodPPA);
                $obErro = $obTPPA->excluirPPA($rsPPAExclusao,"","",$boTransacao);                    
                if (!$obErro->ocorreu()) {
                    if ( $rsPPAExclusao->getCampo('retorno') == 'false') {
                        return SistemaLegado::alertaAviso('LSManterPPA.php?stAcao=excluir','Ocorreu um erro ao excluir o PPA.', 'n_excluir', 'aviso', Sessao::getId());            
                    }    
                }                
            }
        }
        
        if ( $obErro->ocorreu() ) {
            return SistemaLegado::alertaAviso('LSManterPPA.php?stAcao=excluir','Ocorreu um erro ao excluir o PPA.', 'n_excluir', 'aviso', Sessao::getId());
        } else {
            $obTPPAPPAPrecisao = new TPPAPPAPrecisao();
            $obTPPAPPAPrecisao->setDado('cod_ppa', $inCodPPA);
            $obErro = $obTPPAPPAPrecisao->exclusao($boTransacao);

            if (!$obErro->ocorreu()) {
                $obTPPA = new TPPA;
                $obTPPA->setDado('cod_ppa', $inCodPPA);
                $obErro = $obTPPA->exclusao($boTransacao);
            }
        }

        if (!$obErro->ocorreu()) {
            return SistemaLegado::alertaAviso('LSManterPPA.php?stAcao=excluir', $arParametros['stPeriodo'], 'excluir', 'aviso', Sessao::getId());
        }
    }

    public function exception($obErro, $stMessage, $stAcao = "incluir")
    {
        if ($obErro->ocorreu()) {
            return sistemaLegado::alertaAviso('LSManterPPA.php?stAcao='.$stAcao, $stMessage, 'n_'.$stAcao, 'aviso', Sessao::getId());
        } else {
            return true;
        }
    }

    public function pesquisaPrecisoes($inCodPrecisaoIni = '', $inCodPrecisaoFim = '', $stNivel = '', $stOrdem = '', $boTransacao = '')
    {
        $stFiltro    = '';
        $stOrdem     = '';
        $stSeparador = '';

        if ($inCodPrecisaoIni) {
            $stFiltro .= $stSeparador . 'cod_precisao >= ' . $inCodPrecisaoIni;
            $stSeparador = ' AND ';
        }

        if ($inCodPrecisaoFim) {
            $stFiltro .= $stSeparador . 'cod_precisao <= ' . $inCodPrecisaoFim;
            $stSeparador = ' AND ';
        }

        if ($stNivel) {
            $stFiltro .= $stSeparador . "nivel ILIKE '%" . addslashes($stNivel) . "%'";
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        return $this->pesquisa('TPPAPrecisao', 'recuperaTodos', $stFiltro, $stOrdem, $boTransacao);
    }
}
