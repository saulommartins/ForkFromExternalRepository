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
 * Classe de negocio do Manter programas

 * Data de Criação   : 26/09/2008

 * @author Analista      : Bruno Ferreira
 * @author Desenvolvedor : Jânio Eduardo
 * @ignore

 * $Id: $

*Casos de uso: uc-02.09.02
*/

include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrograma.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoProgramaPPAPrograma.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPA.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAPrograma.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAProgramaNorma.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAPPANorma.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAProgramaDados.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAProgramaIndicadores.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAProgramaResponsavel.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAProgramaOrgaoResponsavel.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAProgramaTemporarioVigencia.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAUtils.class.php");
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAAcao.class.php");
include_once(CAM_GF_PPA_VISAO."VPPAManterAcao.class.php");
include_once(CAM_GF_PPA_NEGOCIO."RPPAManterAcao.class.php");
include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';
include_once(TTPB."TTCEPBProgramaObjetivoMilenio.class.php");


class RPPAManterPrograma
{
    private $obOrgao;
    private $arOrgao;
    private $obTOrcamentoPrograma;
    private $obTOrcamentoProgramaPPAPrograma;
    private $obTPrograma;
    private $obTProgramaNorma;
    private $obTProgramaDados;
    private $obTProgramaIndicadores;
    private $obTProgramaResponsavel;
    private $obTProgramaOrgaoResponsavel;
    private $obProgramaTermporarioVigencia;
    private $obTUtils;
    public $codPrograma;
    private $obTPPA;
    private $obTPPANorma;
    public $obRPPAManterPPA;
    private $obTAcao;

    
    public function __construct()
    {
        $this->obTOrcamentoProgramaPPAPrograma = new TOrcamentoProgramaPPAPrograma;
        $this->obTOrcamentoPrograma            = new TOrcamentoPrograma;
        $this->obTPrograma                     = new TPPAPrograma;
        $this->obTProgramaNorma                = new TPPAProgramaNorma;
        $this->obTPPA                          = new TPPA;
        $this->obTPPANorma                     = new TPPAPPANorma;
        $this->obTProgramaDados                = new TPPAProgramaDados;
        $this->obTProgramaIndicadores          = new TPPAProgramaIndicadores;
        $this->obTProgramaResponsavel          = new TPPAProgramaResponsavel;
        $this->obTProgramaOrgaoResponsavel     = new TPPAProgramaOrgaoResponsavel;
        $this->obTProgramaTemporarioVigencia   = new TPPAProgramaTemporarioVigencia;
        $this->obTCEPBProgramaObjetivoMilenio  = new TTCEPBProgramaObjetivoMilenio;
        $this->obRPPAManterPPA                 = new RPPAManterPPA();
        $this->obTUtils = new TPPAUtils;
    }
    
    public function getOrgao()
    {
        return $this->arOrgao;
    }

    public function getCodPrograma() {return $this->codPrograma;}

    public function setOrgao($valor) {return $this->arOrgao = $valor;}

    public function setCodPrograma($valor) {return $this->codPrograma = $valor;}

    public function recuperaOrgao($inCodOrgao)
    {
        $stExercicio = sessao::read('exercicio');
        $this->obOrgao = new TOrcamentoOrgao;
        $filtro = ' AND OO.num_orgao = ' . $inCodOrgao . " AND OO.exercicio = '" . $stExercicio . "'";
        $rsOrgao = new RecordSet();
        $this->obOrgao->recuperaRelacionamento($rsOrgao, $filtro);

        return $rsOrgao;
    }

    public function recuperaUnidade($inCodUnidade, $inCodOrgao)
    {
        $stExercicio = sessao::read('exercicio');
        $this->obUnidade = new TOrcamentoUnidade;
        $stFiltro  = " AND unidade.num_unidade = ".$inCodUnidade." AND unidade.exercicio = '".$stExercicio."'";
        $stFiltro .= " AND unidade.num_orgao = ".$inCodOrgao;
        $rsUnidade = new RecordSet();
        $this->obUnidade->recuperaRelacionamento($rsUnidade, $stFiltro);

        return $rsUnidade;
    }

    public function recuperaPrograma($stFiltro, $metodo)
    {
        if (isset($stFiltro)) {
            $criterio = " WHERE ".$stFiltro;
        }

        $stOrder = " ORDER BY ppa.cod_ppa, programa.num_programa ";
        $obRSPrograma = new RecordSet;
        $this->obTPrograma->$metodo($obRSPrograma,$criterio, $stOrder);

        return $obRSPrograma;
    }

    public function recuperaProgramaLista($stFiltro, $metodo)
    {
        if (isset($stFiltro)) {
            $criterio = " WHERE ".$stFiltro;
        }

        $stOrder = " ORDER BY num_programa ";
        $obRSPrograma = new RecordSet;
        $this->obTPrograma->$metodo($obRSPrograma,$criterio, $stOrder);
        return $obRSPrograma;
    }

    public function verificaCodPrograma($stFiltro, $metodo)
    {
        if (isset($stFiltro)) {
            $criterio = " WHERE ".$stFiltro;
        }

        $obRSPrograma = new RecordSet;
        $this->obTPrograma->$metodo($obRSPrograma,$criterio, $stOrder);

        return $obRSPrograma;
    }

    /**
     * Função para incluir em programa_temporario_vigencia.
     * @param  array   $arParametros parâmetros necessários para inclusão
     * @param  boolean $boTransacao  se existe transação ou não
     * @return Erro    objeto contendo erro, se houver
     */
    public function incluiTemporarioVigencia(array $arParametros, $boTransacao = '')
    {
        $this->obTProgramaTemporarioVigencia->setDado('cod_programa', $arParametros['inCodPrograma']);
        $this->obTProgramaTemporarioVigencia->setDado('timestamp_programa_dados', $arParametros['tsProgramaDados']);
        $this->obTProgramaTemporarioVigencia->setDado('dt_inicial'              , $arParametros['stDataInicial']);
        $this->obTProgramaTemporarioVigencia->setDado('dt_final'                , $arParametros['stDataFinal']);
        $this->obTProgramaTemporarioVigencia->setDado('valor_global'            , $arParametros['flValorGlobal']);

        return $this->obTProgramaTemporarioVigencia->inclusao($boTransacao);
    }

    /**
     * Função para incluir em programa_indicadores.
     * @param  array   $arParametros parâmetros necessários para inclusão
     * @param  boolean $boTransacao  se existe transação ou não
     * @return Erro    objeto contendo erro, se houver
     */
    public function incluiIndicador(array $arParametros, $boTransacao = '')
    {
        $this->obTProgramaIndicadores->setDado('cod_programa'            , $arParametros['inCodPrograma']);
        $this->obTProgramaIndicadores->setDado('timestamp_programa_dados', $arParametros['tsProgramaDados']);
        $this->obTProgramaIndicadores->setDado('cod_indicador'           , $arParametros['inCodIndicador']);
        $this->obTProgramaIndicadores->setDado('indice_recente'          , $arParametros['flIndiceRecente']);
        $this->obTProgramaIndicadores->setDado('dt_indice_recente'       , $arParametros['dtIndiceRecente']);
        $this->obTProgramaIndicadores->setDado('descricao'               , $arParametros['stDescricao']);
        $this->obTProgramaIndicadores->setDado('cod_periodicidade'       , $arParametros['inCodPeriodicidade']);
        $this->obTProgramaIndicadores->setDado('cod_unidade'             , $arParametros['inCodUnidadeIndicador']);
        $this->obTProgramaIndicadores->setDado('cod_grandeza'            , $arParametros['inCodGrandezaIndicador']);
        $this->obTProgramaIndicadores->setDado('fonte'                   , $arParametros['stFonte']);
        $this->obTProgramaIndicadores->setDado('base_geografica'         , $arParametros['stBaseGeografica']);
        $this->obTProgramaIndicadores->setDado('forma_calculo'           , $arParametros['stFormaCalculo']);

        if ($arParametros['flIndiceDesejado']) {
            $this->obTProgramaIndicadores->setDado('indice_desejado', $arParametros['flIndiceDesejado']);
        } else {
            $this->obTProgramaIndicadores->setDado('indice_desejado', 0);
        }

        return $this->obTProgramaIndicadores->inclusao($boTransacao);
    }

    /**
     * Função para incluir em programa_dados.
     * @param  array   $arParametros parâmetros necessários para inclusão
     * @param  boolean $boTransacao  se existe transacao ou não
     * @return Erro    objeto contendo mensagem de erro, se houver
     */
    public function incluiProgramaDados(array $arParametros, $boTransacao = '')
    {
        $this->obTProgramaDados->setDado('cod_programa'            , $arParametros['inCodPrograma']);
        $this->obTProgramaDados->setDado('timestamp_programa_dados', $arParametros['tsProgramaDados']);
        $this->obTProgramaDados->setDado('cod_tipo_programa'       , $arParametros['inCodTipoPrograma']);
        $this->obTProgramaDados->setDado('identificacao'           , $arParametros['inIdentificacao']);
        $this->obTProgramaDados->setDado('diagnostico'             , $arParametros['inDiagnostico']);
        $this->obTProgramaDados->setDado('objetivo'                , $arParametros['inObjetivo']);
        $this->obTProgramaDados->setDado('diretriz'                , $arParametros['inDiretriz']);
        $this->obTProgramaDados->setDado('continuo'                , $arParametros['boNatureza']);
        $this->obTProgramaDados->setDado('publico_alvo'            , $arParametros['stPublicoAlvo']);
        $this->obTProgramaDados->setDado('justificativa'           , $arParametros['stJustificativa']);
        $this->obTProgramaDados->setDado('exercicio_unidade'       , $arParametros['stExercicioUnidade']);
        $this->obTProgramaDados->setDado('num_unidade'             , $arParametros['inNumUnidade']);
        $this->obTProgramaDados->setDado('num_orgao'               , $arParametros['inNumOrgao']);

        return $this->obTProgramaDados->inclusao($boTransacao);
    }

    /**
     * Função para incluir em programa.
     * @param  array   $arParametros parâmetros necessários para inclusão
     * @param  boolean $boTransacao  se existe transacao ou não
     * @return Erro    objeto contendo mensagem de erro, se houver
     */
    public function incluiPrograma(array &$arParametros, $boTransacao = '')
    {
        if (!$arParametros['inCodPrograma']) {
            $rsPrograma = $this->getProximoCodPrograma($boTransacao);
            $arParametros['inCodPrograma'] = $rsPrograma->getCampo('cod_programa');
        }

        if (!$arParametros['inNumPrograma']) {
            $arParametros['inNumPrograma'] = $arParametros['inCodPrograma'];
        }

        $this->obTPrograma->setDado('cod_programa'                   , $arParametros['inCodPrograma']);
        $this->obTPrograma->setDado('num_programa'                   , $arParametros['inNumPrograma']);
        $this->obTPrograma->setDado('cod_setorial'                   , $arParametros['inCodSetorial']);
        $this->obTPrograma->setDado('ultimo_timestamp_programa_dados', $arParametros['tsProgramaDados']);
        $this->obTPrograma->setDado('ativo'                          , 't');
        $obErro = $this->obTPrograma->inclusao($boTransacao);

        $stExercicioPPA = $arParametros['inAnoInicioPPA'];
        while ($stExercicioPPA <= $arParametros['inAnoFinalPPA']) {
            $this->obTOrcamentoPrograma->setDado('exercicio'   , $stExercicioPPA);
            $this->obTOrcamentoPrograma->setDado('cod_programa', $arParametros['inCodPrograma']);
            $this->obTOrcamentoPrograma->setDado('descricao'   , stripslashes($arParametros['inIdentificacao']));
            $this->obTOrcamentoPrograma->inclusao($boTransacao);

            $this->obTOrcamentoProgramaPPAPrograma->setDado('cod_programa'    , $arParametros['inCodPrograma']);
            $this->obTOrcamentoProgramaPPAPrograma->setDado('cod_programa_ppa', $arParametros['inCodPrograma']);
            $this->obTOrcamentoProgramaPPAPrograma->setDado('exercicio'       , $stExercicioPPA);
            $this->obTOrcamentoProgramaPPAPrograma->inclusao($boTransacao);
            $stExercicioPPA++;
        }

        return $obErro;
    }

    public function incluirPrograma($arParam)
    {
        if (is_array($arParam['arDescIndicador'])) {
            foreach ($arParam['arDescIndicador'] as $stDescIndicador) {
                if ($stDescIndicador == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Descrição do Indicador não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arUnidadeMedida'])) {
            foreach ($arParam['arUnidadeMedida'] as $stUnidadeMedida) {
                if ($stUnidadeMedida == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Unidade de Medida não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arIndiceRecente'])) {
            foreach ($arParam['arIndiceRecente'] as $inIndiceRecente) {
                if ($inIndiceRecente == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Indice Recente não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arDtIndiceRecente'])) {
            foreach ($arParam['arDtIndiceRecente'] as $dtIndiceRecente) {
                if ($dtIndiceRecente == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Data do Indice Recente não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arIndiceDesejado'])) {
            foreach ($arParam['arIndiceDesejado'] as $inIndiceDesejado) {
                if ($inIndiceDesejado == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Indice Desejado não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arFonteIndice'])) {
            foreach ($arParam['arFonteIndice'] as $stFonteIndice) {
                if ($stFonteIndice == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Fonte do Índice não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arPeriodicidade'])) {
            foreach ($arParam['arPeriodicidade'] as $stPeriodicidade) {
                if ($stPeriodicidade == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Periodicidade não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arBaseGeografica'])) {
            foreach ($arParam['arBaseGeografica'] as $stBaseGeografica) {
                if ($stBaseGeografica == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Base Geográfica não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arFormaCalculo'])) {
            foreach ($arParam['arFormaCalculo'] as $stFormaCalculo) {
                if ($stFormaCalculo == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Forma de Cálculo não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if ($arParam['inIdPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Identificação do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['stJustificativa'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Justificativa do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inDigPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Diagnóstico do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inObjPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Objetivos do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inDirPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Diretrizes do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inPublicoAlvo'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Público-Alvo do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inCodPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Informe o código do Programa.','n_incluir','aviso');
        }

        if ($arParam['inCodTipoPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Selecione um Tipo Programa.','n_incluir','aviso');
        }

        if (($arParam['inCodTipoPrograma'] == 1) AND (!is_array($arParam['arDescIndicador']))) {
            SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Os programas finalisticos devem ter no mínimo um indicador cadastrado.','n_incluir','aviso');
        }

        if ($arParam['inCodOrgao'] == '') {
            SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Deve ter pelo menos um órgão responsável para este programa.' , 'n_incluir' , 'aviso' );
        }
        if(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 15){
            if ($arParam['inObjMilenio'] == '') {
                SistemaLegado::LiberaFrames(true,false);
    
                return sistemaLegado::exibeAviso('Objetivo do Milenio não pode ser nulo.' , 'n_incluir' , 'aviso' );
            }
        }
        if ($arParam['inCodUnidade'] == '') {
            SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Deve ter pelo menos uma unidade responsável para este programa.' , 'n_incluir' , 'aviso' );
        } else {
            $timestamp = date('Y-m-d H:i:s');
            //Inicio das inclusões
            $obTransacao = new Transacao();
            $boFlagTransacao = false;
            $boTransacao = "";

            #INICIA TRANSAÇÂO
            $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

            $rsPrograma = $this->getProximoCodPrograma($boTransacao);
            $arParam['inNumPrograma'] = $arParam['inCodPrograma'];
            $arParam['inCodPrograma'] = $rsPrograma->getCampo('cod_programa');

            $this->obTPrograma->setDado('cod_programa'                   , $arParam['inCodPrograma']);
            $this->obTPrograma->setDado('num_programa'                   , $arParam['inNumPrograma']);
            $this->obTPrograma->setDado('cod_setorial'                   , $arParam['inCodProgramaSetorial']);
            $this->obTPrograma->setDado('ultimo_timestamp_programa_dados', $timestamp);
            $this->obTPrograma->setDado('ativo'                          , 't');
            $obErro = $this->obTPrograma->inclusao($boTransacao);

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            }

            $this->setCodPrograma($inCodPrograma,$boTransacao);
            $this->obTProgramaDados->setDado('cod_programa'            , $arParam['inCodPrograma']);
            $this->obTProgramaDados->setDado('timestamp_programa_dados', $timestamp);
            $this->obTProgramaDados->setDado('cod_tipo_programa'       , $arParam['inCodTipoPrograma']);
            $this->obTProgramaDados->setDado('identificacao'           , stripslashes($arParam['inIdPrograma']));
            $this->obTProgramaDados->setDado('justificativa'           , stripslashes($arParam['stJustificativa']));
            $this->obTProgramaDados->setDado('diagnostico'             , stripslashes($arParam['inDigPrograma']));
            $this->obTProgramaDados->setDado('objetivo'                , stripslashes($arParam['inObjPrograma']));
            $this->obTProgramaDados->setDado('diretriz'                , stripslashes($arParam['inDirPrograma']));
            $this->obTProgramaDados->setDado('continuo'                , stripslashes($arParam['boNatureza']));
            $this->obTProgramaDados->setDado('publico_alvo'            , stripslashes($arParam['inPublicoAlvo']));
            $this->obTProgramaDados->setDado('exercicio_unidade'       , Sessao::getExercicio());
            $this->obTProgramaDados->setDado('num_unidade'             , $arParam['inCodUnidade']);
            $this->obTProgramaDados->setDado('num_orgao'               , $arParam['inCodOrgao']);
            $obErro = $this->obTProgramaDados->inclusao($boTransacao);

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            }

            if (is_array($arParam['arDescIndicador'])) {
                $this->reordenaValoresLista($arParam);

                for ($x = 0;$x < count($arParam['arDescIndicador']);$x++) {
                    $stUnidade = explode('-', $arParam['arUnidadeMedida'][$x]);
                    $this->obTProgramaIndicadores->proximoCod($inCodIndicador, $boTransacao);
                    $this->obTProgramaIndicadores->setDado('cod_indicador'           , $inCodIndicador);
                    $this->obTProgramaIndicadores->setDado('cod_programa'            , $arParam['inCodPrograma']);
                    $this->obTProgramaIndicadores->setDado('timestamp_programa_dados', $timestamp);
                    $this->obTProgramaIndicadores->setDado('cod_periodicidade'       , $arParam['arPeriodicidade'][$x]);
                    $this->obTProgramaIndicadores->setDado('cod_unidade'             , $stUnidade[0]);
                    $this->obTProgramaIndicadores->setDado('cod_grandeza'            , $stUnidade[1]);
                    $this->obTProgramaIndicadores->setDado('indice_recente'          , stripslashes($arParam['arIndiceRecente'][$x]));
                    $this->obTProgramaIndicadores->setDado('dt_indice_recente'       , stripslashes($arParam['arDtIndiceRecente'][$x]));
                    $this->obTProgramaIndicadores->setDado('descricao'               , stripslashes($arParam['arDescIndicador'][$x]));
                    $this->obTProgramaIndicadores->setDado('fonte'                   , stripslashes($arParam['arFonteIndice'][$x]));
                    $this->obTProgramaIndicadores->setDado('forma_calculo'           , stripslashes($arParam['arFormaCalculo'][$x]));
                    $this->obTProgramaIndicadores->setDado('base_geografica'         , stripslashes($arParam['arBaseGeografica'][$x]));

                    if ($arParam['arIndiceDesejado'][$x]) {
                        $this->obTProgramaIndicadores->setDado('indice_desejado',$arParam['arIndiceDesejado'][$x]);
                    } else {
                        $this->obTProgramaIndicadores->setDado('indice_desejado',0);
                    }

                    $obErro = $this->obTProgramaIndicadores->inclusao($boTransacao);
                }
            }

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso("erro indicador" ,'n_incluir','aviso');
            }

            if ($arParam['boNatureza'] == 'f') {
                if (empty($arParam['stDataInicial']) || empty($arParam['stDataFinal'])) {
                    SistemaLegado::LiberaFrames(true,false);

                    return sistemaLegado::exibeAviso("Informe a data início e término no campo Natureza Temporal" ,'n_incluir','aviso');
                } else {
                    $this->obTProgramaTemporarioVigencia->setDado('cod_programa'            , $arParam['inCodPrograma']);
                    $this->obTProgramaTemporarioVigencia->setDado('timestamp_programa_dados', $timestamp);
                    $this->obTProgramaTemporarioVigencia->setDado('dt_inicial'              , $arParam['stDataInicial']);
                    $this->obTProgramaTemporarioVigencia->setDado('dt_final'                , $arParam['stDataFinal']);
//                    $this->obTProgramaTemporarioVigencia->setDado('valor_global'            , $arParam['flValorGlobal']);
                    $this->obTProgramaTemporarioVigencia->setDado('valor_global'            , 0.00);
                    $obErro = $this->obTProgramaTemporarioVigencia->inclusao($boTransacao);
                }
            }

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            }

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            }

            $stExercicioPPA = $arParam['inAnoInicioPPA'];
            
            while ($stExercicioPPA <= $arParam['inAnoFinalPPA']) {
                $this->obTOrcamentoPrograma->setDado('exercicio'   , $stExercicioPPA);
                $this->obTOrcamentoPrograma->setDado('cod_programa', $arParam['inCodPrograma']);
                $this->obTOrcamentoPrograma->setDado('descricao'   , stripslashes($arParam['inIdPrograma']));
                $this->obTOrcamentoPrograma->inclusao($boTransacao);
                
                $this->obTOrcamentoProgramaPPAPrograma->setDado('cod_programa'    , $arParam['inCodPrograma']);
                $this->obTOrcamentoProgramaPPAPrograma->setDado('cod_programa_ppa', $arParam['inCodPrograma']);
                $this->obTOrcamentoProgramaPPAPrograma->setDado('exercicio'       , $stExercicioPPA);
                $this->obTOrcamentoProgramaPPAPrograma->inclusao($boTransacao);
                $stExercicioPPA++;
            }
            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);
                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            } else {
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPrograma);
                SistemaLegado::LiberaFrames(true,false);
                if(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 15){
                    $obErro = $this->salvarObjetivoMilenio($boTransacao,$arParam);
                }
                if (isset($arParam['hdnProgramaOrcamento']) && $arParam['hdnProgramaOrcamento'] == 'true') {
                    $stFiltro = "";
                    if ($arParam) {
                        foreach ($arParam as $stCampo => $stValor) {
                            $stFiltro .= $stCampo."=".str_replace(array("\n", "\r"), "\\n", $stValor)."&";
                        }
                    }
                    $stFiltro .= "pg=".Sessao::read('pg')."&";
                    $stFiltro .= "pos=".Sessao::read('pos')."&";
                    $stFiltro .= "stAcao=".$stAcao;

                    return sistemaLegado::alertaAviso('LSManterPrograma.php'."?".Sessao::getId()."&".$stFiltro,$arParam['inCodPrograma'],'incluir','aviso',Sessao::getId());
                } else {
                    return sistemaLegado::alertaAviso('FMManterPrograma.php'."?".Sessao::getId(),$arParam['inNumPrograma'],'incluir','aviso',Sessao::getId());

                }
            }
        }
    }
    //TCEPB
    public function salvarObjetivoMilenio($boTransacao, $arParam){
        $stExercicioPPA = $arParam['inAnoInicioPPA'];
        
        while ($stExercicioPPA <= $arParam['inAnoFinalPPA']) {
            $this->obTCEPBProgramaObjetivoMilenio->setDado('exercicio'        , $stExercicioPPA          );
            $this->obTCEPBProgramaObjetivoMilenio->setDado('cod_programa'     , $arParam['inCodPrograma']);
            $this->obTCEPBProgramaObjetivoMilenio->recuperaPorChave($rsObjetivoMilenio);

            if($rsObjetivoMilenio->getNumLinhas() > 0){
                $this->obTCEPBProgramaObjetivoMilenio->setDado('cod_tipo_objetivo', $arParam['inObjMilenio'] );
                $obErro = $this->obTCEPBProgramaObjetivoMilenio->alteracao($boTransacao);
            }else{
                $this->obTCEPBProgramaObjetivoMilenio->setDado('cod_tipo_objetivo', $arParam['inObjMilenio'] );
                $obErro = $this->obTCEPBProgramaObjetivoMilenio->inclusao($boTransacao);
            }
            $stExercicioPPA++;
        }
        
        if ( $obErro->ocorreu() ) {
                SistemaLegado::LiberaFrames(true,false);
                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            }
        return $obErro;
    }

    public function getProximoCodPrograma($boTransacao)
    {
        $boPrograma = false;
        $this->obTPrograma->recuperaCodigosPrograma($obRSPrograma, '', '', $boTransacao);
        $arCodigos = array();
        if ($obRSPrograma->getNumLinhas() > -1) {
            while (!$obRSPrograma->EOF()) {
                $arCodigos[] = $obRSPrograma->getCampo('cod_programa');
                $obRSPrograma->proximo();
            }
        }
        $inCodPrograma = 1;
        $obRSPrograma = new RecordSet;
        while (!$boPrograma) {
            if (in_array($inCodPrograma, $arCodigos)) {
                $inCodPrograma++;
            } else {
                $boPrograma = true;
                $obRSPrograma->setCampo('cod_programa', str_pad($inCodPrograma, 4, 0, STR_PAD_LEFT));
            }
        }

        return $obRSPrograma;
    }

    public function alterar($arParam)
    {
        global $request;
        if (is_array($arParam['arDescIndicador'])) {
            foreach ($arParam['arDescIndicador'] as $stDescIndicador) {
                if ($stDescIndicador == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Descrição do Indicador não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arUnidadeMedida'])) {
            foreach ($arParam['arUnidadeMedida'] as $stUnidadeMedida) {
                if ($stUnidadeMedida == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Unidade de Medida não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arIndiceRecente'])) {
            foreach ($arParam['arIndiceRecente'] as $inIndiceRecente) {
                if ($inIndiceRecente == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Indice Recente não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arDtIndiceRecente'])) {
            foreach ($arParam['arDtIndiceRecente'] as $dtIndiceRecente) {
                if ($dtIndiceRecente == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Indice Recente não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arIndiceDesejado'])) {
            foreach ($arParam['arIndiceDesejado'] as $inIndiceDesejado) {
                if ($inIndiceDesejado == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Indice Desejado não pode ser nulo.', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arFonteIndice'])) {
            foreach ($arParam['arFonteIndice'] as $stFonteIndice) {
                if ($stFonteIndice == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Fonte do Índice não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arPeriodicidade'])) {
            foreach ($arParam['arPeriodicidade'] as $stPeriodicidade) {
                if ($stPeriodicidade == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Periodicidade não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arBaseGeografica'])) {
            foreach ($arParam['arBaseGeografica'] as $stBaseGeografica) {
                if ($stBaseGeografica == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Base Geográfica não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if (is_array($arParam['arFormaCalculo'])) {
            foreach ($arParam['arFormaCalculo'] as $stFormaCalculo) {
                if ($stFormaCalculo == '') {
                    SistemaLegado::LiberaFrames(true,false);

                    return SistemaLegado::exibeAviso('Campo Forma de Cálculo não pode ser nulo', 'erro', 'erro');
                }
            }
        }

        if ($arParam['boNatureza'] == 'f') {
            if (empty($arParam['stDataInicial']) || empty($arParam['stDataFinal'])) {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Informe a data início e término no campo Natureza Temporal', 'erro', 'erro');
            }
        }

        if ($arParam['inIdPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Identificação do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['stJustificativa'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Justificativa do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inDigPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Diagnóstico do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inObjPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Objetivos do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inDirPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Diretrizes do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inPublicoAlvo'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Campo Público-Alvo do Programa não pode ser nulo.','n_incluir','aviso');
        }

        if ($arParam['inCodPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Informe o código do Programa.','n_incluir','aviso');
        }

        if ($arParam['inCodTipoPrograma'] == '') {
             SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Selecione um Tipo Programa.','n_incluir','aviso');
        }

        if (!$arParam['inCodOrgao']) {
            SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Deve ter pelo menos um órgão responsável para este programa.' , 'n_incluir' , 'aviso' );
        }

        if ($arParam['inCodUnidade'] == '') {
            SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Deve ter pelo menos uma unidade responsável para este programa.' , 'n_incluir' , 'aviso' );
        }
        
        if(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 15){
            if ($arParam['inObjMilenio'] == '') {
                SistemaLegado::LiberaFrames(true,false);
    
                return sistemaLegado::exibeAviso('Objetivo do Milenio não pode ser nulo.' , 'n_incluir' , 'aviso' );
            }
        }

        if (($arParam['inCodTipoPrograma'] == 1) AND (!is_array($arParam['arDescIndicador']))) {
            SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::exibeAviso('Os programas finalisticos devem ter no mínimo um indicador cadastrado.','n_incluir','aviso');
        } else {
            $timestamp = date('Y-m-d H:i:s');
            //Inicio das inclusões
            $obTransacao = new Transacao();
            $boFlagTransacao = false;
            $boTransacao = "";

            #INICIA TRANSAÇÂO
            $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            $this->setCodPrograma($arParam['inCodPrograma']);

            $this->obTPrograma->setDado('cod_programa',$this->getCodPrograma());
            $this->obTPrograma->setDado('num_programa',$arParam['inNumPrograma']);
            $this->obTPrograma->setDado('cod_setorial',$arParam['inCodProgramaSetorial']);
            $this->obTPrograma->setDado('ultimo_timestamp_programa_dados',$timestamp);
            $this->obTPrograma->setDado('ativo','t');

            $obErro = $this->obTPrograma->alteracao($boTransacao);

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso('Tabela Programa','n_incluir','aviso');
            }

            $this->obTProgramaDados->setDado('cod_programa'            , $this->getCodPrograma());
            $this->obTProgramaDados->setDado('timestamp_programa_dados', $timestamp);
            $this->obTProgramaDados->setDado('cod_tipo_programa'       , $arParam['inCodTipoPrograma']);
            $this->obTProgramaDados->setDado('identificacao'           , stripslashes($arParam['inIdPrograma']));
            $this->obTProgramaDados->setDado('justificativa'           , stripslashes($arParam['stJustificativa']));
            $this->obTProgramaDados->setDado('diagnostico'             , stripslashes($arParam['inDigPrograma']));
            $this->obTProgramaDados->setDado('objetivo'                , stripslashes($arParam['inObjPrograma']));
            $this->obTProgramaDados->setDado('diretriz'                , stripslashes($arParam['inDirPrograma']));
            $this->obTProgramaDados->setDado('continuo'                , stripslashes($arParam['boNatureza']));
            $this->obTProgramaDados->setDado('publico_alvo'            , stripslashes($arParam['inPublicoAlvo']));
            $this->obTProgramaDados->setDado('exercicio_unidade'       , Sessao::getExercicio());
            $this->obTProgramaDados->setDado('num_unidade'             , $arParam['inCodUnidade']);
            $this->obTProgramaDados->setDado('num_orgao'               , $arParam['inCodOrgao']);
            $obErro = $this->obTProgramaDados->inclusao($boTransacao);

            $stExercicioPPA = $arParam['inAnoInicioPPA'];
            while ($stExercicioPPA <= $arParam['inAnoFinalPPA']) {
                $this->obTOrcamentoPrograma->setDado('exercicio'   , $stExercicioPPA);
                $this->obTOrcamentoPrograma->setDado('cod_programa', $arParam['inCodPrograma']);
                $this->obTOrcamentoPrograma->setDado('descricao'   , stripslashes($arParam['inIdPrograma']));
                $this->obTOrcamentoPrograma->alteracao($boTransacao);
                $stExercicioPPA++;
            }

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            }

            $rsIndicadores = new RecordSet();
            $stFiltro      = ' WHERE cod_programa = ' . $this->getCodPrograma();
            $this->obTProgramaIndicadores->recuperaTodos($rsIndicadores, $stFiltro, null, $boTransacao);

            while (!$rsIndicadores->eof()) {
                $this->obTProgramaIndicadores->setDado('cod_indicador'           , $rsIndicadores->getCampo('cod_indicador'));
                $this->obTProgramaIndicadores->setDado('cod_programa'            , $rsIndicadores->getCampo('cod_programa'));
                $this->obTProgramaIndicadores->setDado('timestamp_programa_dados', $rsIndicadores->getCampo('timestamp_programa_dados'));
                $obErro = $this->obTProgramaIndicadores->exclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    SistemaLegado::LiberaFrames(true,false);

                   return sistemaLegado::exibeAviso('Incluir Indicadores','n_incluir','aviso');
                }

                $rsIndicadores->proximo();
            }

            if (is_array($arParam['arDescIndicador'])) {
                $this->reordenaValoresLista($arParam);

                for ($x = 0;$x < count($arParam['arDescIndicador']);$x++) {
                    $stUnidade = explode('-', $arParam['arUnidadeMedida'][$x]);
                    $this->obTProgramaIndicadores->proximoCod($inCodIndicador, $boTransacao);
                    $this->obTProgramaIndicadores->setDado('cod_indicador'           , $inCodIndicador);
                    $this->obTProgramaIndicadores->setDado('cod_programa'            , $this->getCodPrograma());
                    $this->obTProgramaIndicadores->setDado('timestamp_programa_dados', $timestamp);
                    $this->obTProgramaIndicadores->setDado('cod_periodicidade'       , $arParam['arPeriodicidade'][$x]);
                    $this->obTProgramaIndicadores->setDado('cod_unidade'             , $stUnidade[0]);
                    $this->obTProgramaIndicadores->setDado('cod_grandeza'            , $stUnidade[1]);
                    $this->obTProgramaIndicadores->setDado('indice_recente'          , stripslashes($arParam['arIndiceRecente'][$x]));
                    $this->obTProgramaIndicadores->setDado('dt_indice_recente'       , stripslashes($arParam['arDtIndiceRecente'][$x]));
                    $this->obTProgramaIndicadores->setDado('descricao'               , stripslashes($arParam['arDescIndicador'][$x]));
                    $this->obTProgramaIndicadores->setDado('fonte'                   , stripslashes($arParam['arFonteIndice'][$x]));
                    $this->obTProgramaIndicadores->setDado('forma_calculo'           , stripslashes($arParam['arFormaCalculo'][$x]));
                    $this->obTProgramaIndicadores->setDado('base_geografica'         , stripslashes($arParam['arBaseGeografica'][$x]));

                    if ($arParam['arIndiceDesejado'][$x]) {
                        $this->obTProgramaIndicadores->setDado('indice_desejado',$arParam['arIndiceDesejado'][$x]);
                    } else {
                        $this->obTProgramaIndicadores->setDado('indice_desejado',0);
                    }

                    $obErro = $this->obTProgramaIndicadores->inclusao($boTransacao);
                }
            }

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso('Incluir Indicadores','n_incluir','aviso');
            }

            if ($arParam['boNatureza'] == 'f') {
                $this->obTProgramaTemporarioVigencia->setDado('cod_programa'            , $this->getCodPrograma());
                $this->obTProgramaTemporarioVigencia->setDado('timestamp_programa_dados', $timestamp);
                $this->obTProgramaTemporarioVigencia->setDado('dt_inicial'              , $arParam['stDataInicial']);
                $this->obTProgramaTemporarioVigencia->setDado('dt_final'                , $arParam['stDataFinal']);
//                $this->obTProgramaTemporarioVigencia->setDado('valor_global'            , $arParam['flValorGlobal']);
                $this->obTProgramaTemporarioVigencia->setDado('valor_global'            , 0.00);
                $obErro = $this->obTProgramaTemporarioVigencia->inclusao($boTransacao);
            }

            if ($obErro->ocorreu()) {
                SistemaLegado::LiberaFrames(true,false);

                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_incluir','aviso');
            }

            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTPrograma);
            $stAcao = $request->get('stAcao');
            
            //SOMENTE SE FOR TCEPB
            if(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 15){
                $obErro = $this->salvarObjetivoMilenio( $boTransacao, $arParam );
            }
            
            $stFiltro = "";
            if ($arParam) {
                foreach ($arParam as $stCampo => $stValor) {
                    $stFiltro .= $stCampo."=".str_replace(array("\n", "\r"), "\\n", $stValor)."&";
                }
            }
            $stFiltro .= "pg=".Sessao::read('pg')."&";
            $stFiltro .= "pos=".Sessao::read('pos')."&";
            $stFiltro .= "stAcao=".$stAcao;
            SistemaLegado::LiberaFrames(true,false);

            return sistemaLegado::alertaAviso('LSManterPrograma.php?'.$stFiltro,$arParam['inNumPrograma'],'alterar','aviso',Sessao::getId());
        }
    }

    public function excluir($arParam)
    {
        if ($arParam['boHomologado']) {
            return $this->desativarPrograma($arParam);
        } else {
            return $this->excluirPrograma($arParam);
        }
    }

    private function desativarPrograma($arParam)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        #INICIA TRANSAÇÂO
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        # Desativa ppa.programa
        $stFiltro = ' where cod_programa = ' . $arParam['inCodPrograma'];
        $this->obTPrograma->recuperaTodos($rsProgramas, $stFiltro, '', $boTransacao);

        while (!$rsProgramas->eof()) {

            # Recupera ppa.acao
            $this->obTAcao = new TPPAAcao();

            $stFiltro = ' where cod_programa = ' . $rsProgramas->getCampo('cod_programa');
            $this->obTAcao->recuperaTodos($rsAcoes, $stFiltro, '', $boTransacao);

            $obRegraAcao = new RPPAManterAcao();

            while (!$rsAcoes->eof()) {
                # desativa todas as ações vinculadas ao programa.
                $obErro = $obRegraAcao->desativaAcao($rsAcoes->getCampo('cod_acao'), null, $boTransacao);

                if ($obErro->ocorreu()) {
                    return sistemaLegado::exibeAviso("Erro ao alterar as Ações Relacionas ao Programa(".$arParam['inCodPrograma'].")",'n_excluir','aviso');
                }

                $rsAcoes->proximo();
            }

            $this->obTPrograma->setDado('cod_programa',$rsProgramas->getCampo('cod_programa'));
            $this->obTPrograma->setDado('cod_setorial',$rsProgramas->getCampo('cod_setorial'));
            $this->obTPrograma->setDado('ultimo_timestamp_programa_dados', $rsProgramas->getCampo('ultimo_timestamp_programa_dados'));
            $this->obTPrograma->setDado('ativo','f');

            $obErro = $this->obTPrograma->alteracao($boTransacao);

            if ($obErro->ocorreu()) {
                return sistemaLegado::exibeAviso($obErro->getDescricao(),'n_excluir','aviso');
            }

            $rsProgramas->proximo();
        }

        #FECHA TRANSAÇÃO
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPrograma);

        return sistemaLegado::alertaAviso('LSManterPrograma.php',$arParam['inCodPrograma'],'incluir','aviso',Sessao::getId());
    }

    private function excluirPrograma($arParam)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        #INICIA TRANSAÇÂO
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        # Apaga ppa.programa
        $stFiltro = ' where cod_programa = ' . $arParam['inCodPrograma'];
        $this->obTPrograma->recuperaTodos($rsProgramas, $stFiltro, '', $boTransacao);

        while (!$rsProgramas->eof()) {

            # Recupera ppa.acao
            $this->obTAcao = new TPPAAcao();

            $stFiltro = ' where cod_programa = ' . $rsProgramas->getCampo('cod_programa');
            $this->obTAcao->recuperaTodos($rsAcoes, $stFiltro, '', $boTransacao);

            if ($rsAcoes->getNumLinhas() < 1) {
                # Recupera ppa.programa_dados
                $stFiltro = ' where cod_programa = ' . $rsProgramas->getCampo('cod_programa');
                $this->obTProgramaDados->recuperaTodos($rsProgramaDados, $stFiltro, '', $boTransacao);

                while (!$rsProgramaDados->eof()) {
                    # Recupera todos os programa_indicadores para serem apagados.
                    $stFiltro = ' where cod_programa =' . $rsProgramaDados->getCampo('cod_programa');
                    $this->obTProgramaIndicadores->recuperaTodos($rsProgramaIndicadores, $stFiltro, '', $boTransacao);
                    $stTimestampProgramaDados = $rsProgramaDados->getCampo('timestamp_programa_dados');

                    # Apaga ppa.programa_indicadores
                    while (!$rsProgramaIndicadores->eof()) {
                        $this->obTProgramaIndicadores->setDado('cod_programa', $rsProgramas->getCampo('cod_programa'));
                        $this->obTProgramaIndicadores->setDado('cod_indicador', $rsProgramaIndicadores->getCampo('cod_indicador'));
                        $this->obTProgramaIndicadores->setDado('timestamp_programa_dados', $stTimestampProgramaDados);
                        $obErro = $this->obTProgramaIndicadores->exclusao($boTransacao);

                        if ($obErro->ocorreu()) {
                            return sistemaLegado::exibeAviso($obErro->getDescricao(),'erro','erro');
                        }

                        $rsProgramaIndicadores->proximo();
                    }

                    # Apaga ppa.programa_temporario_vigencia
                    $this->obTProgramaTemporarioVigencia->setDado('cod_programa', $rsProgramas->getCampo('cod_programa'));
                    $this->obTProgramaTemporarioVigencia->setDado('timestamp_programa_dados', $stTimestampProgramaDados);
                    $obErro = $this->obTProgramaTemporarioVigencia->exclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso($obErro->getDescricao(),'erro','erro');
                    }

                    # Apaga ppa.programa_dados
                    $this->obTProgramaDados->setDado('cod_programa', $rsProgramas->getCampo('cod_programa'));
                    $this->obTProgramaDados->setDado('timestamp_programa_dados', $stTimestampProgramaDados);
                    $obErro = $this->obTProgramaDados->exclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso($obErro->getDescricao(),'erro','erro');
                    }

                    $stExercicioPPA = $arParam['inAnoInicioPPA'];
                    while ($stExercicioPPA <= $arParam['inAnoFinalPPA']) {
                        $this->obTCEPBProgramaObjetivoMilenio->setDado('exercicio'        , $stExercicioPPA          );
                        $this->obTCEPBProgramaObjetivoMilenio->setDado('cod_programa'     , $arParam['inCodPrograma']);
                        $obErro = $this->obTCEPBProgramaObjetivoMilenio->exclusao($boTransacao);
                        
                        $this->obTOrcamentoProgramaPPAPrograma->setDado('exercicio'   , $stExercicioPPA);
                        $this->obTOrcamentoProgramaPPAPrograma->setDado('cod_programa', $arParam['inCodPrograma']);
                        $obErro = $this->obTOrcamentoProgramaPPAPrograma->exclusao($boTransacao);

                        $this->obTOrcamentoPrograma->setDado('exercicio'   , $stExercicioPPA);
                        $this->obTOrcamentoPrograma->setDado('cod_programa', $arParam['inCodPrograma']);
                        $obErro = $this->obTOrcamentoPrograma->exclusao($boTransacao);
                        $stExercicioPPA++;
                    }

                    $rsProgramaDados->proximo();
                }

                if (!$obErro->ocorreu()) {
                    # Apaga ppa.programa
                    $this->obTPrograma->setDado('cod_programa', $rsProgramas->getCampo('cod_programa'));
                    $obErro = $this->obTPrograma->exclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso($obErro->getDescricao(),'erro','erro');
                    }
                }
            } else {
                return sistemaLegado::exibeAviso("Este Programa não pode ser excluído pois existem ações vinculados a ele (".$rsProgramas->getCampo('cod_programa').")",'erro','erro');
            }

            $rsProgramas->proximo();
        }

        #FECHA TRANSAÇÃO
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPrograma);

        return sistemaLegado::alertaAviso('LSManterPrograma.php',$arParam['inNumPrograma'],'excluir','aviso',Sessao::getId());
    }

    private function excluirVinculos($stObject, $boTransacao)
    {
        $this->$stObject->setDado('cod_programa', $this->getCodPrograma());

        $obErro = $this->$stObject->exclusao($boTransacao);

        return $obErro;
    }

    private function reordenaValoresLista(&$arParams)
    {
        // Reordenar as chaves do array (são desordenadas quando há exclusão de item da lista)
        $arParams['arIndiceRecente']   = array_values($arParams['arIndiceRecente']);
        $arParams['arDtIndiceRecente'] = array_values($arParams['arDtIndiceRecente']);
        $arParams['arDescIndicador']   = array_values($arParams['arDescIndicador']);
        $arParams['arIndiceDesejado']  = array_values($arParams['arIndiceDesejado']);
        $arParams['arUnidadeMedida']   = array_values($arParams['arUnidadeMedida']);
        $arParams['arPeriodicidade']   = array_values($arParams['arPeriodicidade']);
        $arParams['arFonteIndice']     = array_values($arParams['arFonteIndice']);
        $arParams['arFormaCalculo']    = array_values($arParams['arFormaCalculo']);
        $arParams['arBaseGeografica']  = array_values($arParams['arBaseGeografica']);

        return $arParams;
    }

    public function buscaPPAHomologado($codPPA, $boTransacao = '')
    {
        $obRSNorma = new RecordSet;
        $this->obTPPA->recuperaPPA($obRSNorma," WHERE ppa.fn_verifica_homologacao(".$codPPA.") = true", '', $boTransacao);

        return $obRSNorma;
    }

    public function getFuncionalidadePrograma($stFiltro)
    {
        if ($stFiltro != '') {
            $criterio = " WHERE ".$stFiltro;
        }

        $obRSPrograma = new RecordSet;
        $this->obTPrograma->recuperaFuncionalidadePrograma($obRSPrograma,$criterio);

        return $obRSPrograma;
    }
}
?>
