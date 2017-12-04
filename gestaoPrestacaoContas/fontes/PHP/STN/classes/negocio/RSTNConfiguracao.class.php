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
 * Classe de regra de cheque
 *
 * @category    Urbem
 * @package     STN
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: RSTNConfiguracao.class.php 66478 2016-09-01 17:09:26Z lisiane $
 */

include_once CAM_FW_INCLUDE         . 'valida.inc.php';
include_once CAM_GF_ORC_NEGOCIO     . 'ROrcamentoEntidade.class.php';
include_once CAM_GF_PPA_MAPEAMENTO  . 'TPPA.class.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNReceitaCorrenteLiquida.class.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNDespesaPessoal.class.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNRREOAnexo13.class.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNRiscosFiscais.class.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNProvidencias.class.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNVinculoSTNReceita.class.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNTipoVinculoSTNReceita.class.php';
include_once CAM_GA_ADM_MAPEAMENTO  . 'TAdministracaoConfiguracao.class.php';

class RSTNConfiguracao
{
    public  $obTransacao,
            $obROrcamentoEntidade,
            $obTPPA,
            $obTSTNReceitaCorrenteLiquida,
            $obTSTNDespesaPessoal,
            $obTSTNRREOAnexo13,
            $obTSTNRiscosFiscais,
            $obTSTNProvidencias,
            $obTSTNVinculoSTNReceita,
            $obTSTNTipoVinculoSTNReceita,
            $obTAdministracaoConfiguracao,
            $stDataImplantacao,
            $inMes,
            $inAno,
            $flValor,
            $flReceitaPrevidenciaria,
            $flDespesaPrevidenciaria,
            $flSaldoFinanceiro,
            $stExercicio,
            $inCodEntidade,
            $stDescricao,
            $inCodRisco,
            $inCodIdentificador,
            $flValorProvidencia,
            $inCodProvidencia,
            $inCodReceita,
            $boIRRF,
            $inCodTipoReceita,
            $nuValorPessoalAtivo,
            $nuValorPessoalInativo,
            $nuValorOutrasDespesas,
            $nuValorIndenizacoes,
            $nuValorDecisaoJudicial,
            $nuValorExercicioAnterior,
            $nuValorInativosPensionista,
            $nuValorReceitaTributaria,
            $obNuValorIptu,
            $obNuValorIss,
            $obNuValorItbi,
            $obNuValorIrrf,
            $obNuValorOutrasReceitasTributarias,
            $nuValorReceitaContribuicoes,
            $nuValorReceitaPatrimonial,
            $nuValorReceitaAgropecuaria,
            $nuValorReceitaIndustrial,
            $nuValorReceitaServicos,
            $nuValorTransferenciaCorrente,
            $obNuValorCotaParteFPM,
            $obNuValorCotaParteICMS,
            $obNuValorCotaParteIPVA,
            $obNuValorCotaParteITR,
            $obNuValorTransferenciaLC871996,
            $obNuValorTransferenciaLC611989,
            $obnuValorTransferenciasFundeb,
            $obnuValorOutrasTransferenciasCorrentes,
            $nuValorOutrasReceitas,
            $nuValorContribPlanoSSS,
            $nuValorCompensacaoFinanceira,
            $nuValorDeducaoFundeb;

    /**
     * Metodo contrutor, instancia as classes necessarias.
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        $this->obTransacao                  = new Transacao();
        $this->obROrcamentoEntidade         = new ROrcamentoEntidade();
        $this->obTPPA                       = new TPPA();
        $this->obTSTNReceitaCorrenteLiquida = new TSTNReceitaCorrenteLiquida();
        $this->obTSTNDespesaPessoal         = new TSTNDespesaPessoal();
        $this->obTSTNRREOAnexo13            = new TSTNRREOAnexo13();
        $this->obTSTNRiscosFiscais          = new TSTNRiscosFiscais();
        $this->obTSTNProvidencias           = new TSTNProvidencias();
        $this->obTSTNVinculoSTNReceita      = new TSTNVinculoSTNReceita();
        $this->obTSTNTipoVinculoSTNReceita  = new TSTNTipoVinculoSTNReceita();
        $this->obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    }

    public function incluirDataImplantacao($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTAdministracaoConfiguracao->setDado('exercicio' , Sessao::getExercicio());
            $this->obTAdministracaoConfiguracao->setDado('cod_modulo', 36);
            $this->obTAdministracaoConfiguracao->setDado('parametro' , 'data_implantacao');
            $this->obTAdministracaoConfiguracao->setDado('valor'     , $this->stDataImplantacao);

            $this->obTAdministracaoConfiguracao->recuperaPorChave($rsDataImplantacao);
            if ($rsDataImplantacao->getNumLinhas() <= 0) {
                $obErro = $this->obTAdministracaoConfiguracao->inclusao($boTransacao);
            }

            return $obErro;
        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTesourariaCheque);
    }

    /**
     * Metodo que inclui os periodos na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function vincularReceitaCorrenteLiquida($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNReceitaCorrenteLiquida->setDado ('exercicio'   , $this->obROrcamentoEntidade->stExercicio);
            $this->obTSTNReceitaCorrenteLiquida->setDado ('cod_entidade', $this->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTSTNReceitaCorrenteLiquida->setDado ('mes'         , $this->inMes);
            $this->obTSTNReceitaCorrenteLiquida->setDado ('ano'         , $this->inAno);
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_receita_tributaria'              , $this->nuValorReceitaTributaria );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_iptu'                            , $this->nuValorIptu );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_iss'                             , $this->nuValorIss );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_itbi'                            , $this->nuValorItbi );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_irrf'                            , $this->nuValorIrrf );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_outras_receitas_tributarias'     , $this->nuValorOutrasReceitasTributarias );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_receita_contribuicoes'           , $this->nuValorReceitaContribuicoes );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_receita_patrimonial'             , $this->nuValorReceitaPatrimonial );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_receita_agropecuaria'            , $this->nuValorReceitaAgropecuaria );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_receita_industrial'              , $this->nuValorReceitaIndustrial );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_receita_servicos'                , $this->nuValorReceitaServicos );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_transferencias_correntes'        , $this->nuValorTransferenciaCorrente );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_cota_parte_fpm'                  , $this->nuValorCotaParteFPM );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_cota_parte_icms'                 , $this->nuValorCotaParteICMS );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_cota_parte_ipva'                 , $this->nuValorCotaParteIPVA );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_cota_parte_itr'                  , $this->nuValorCotaParteITR );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_transferencias_lc_871996'        , $this->nuValorTransferenciaLC871996 );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_transferencias_lc_611989'        , $this->nuValorTransferenciaLC611989 );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_transferencias_fundeb'           , $this->nuValorTransferenciasFundeb );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_outras_transferencias_correntes' , $this->nuValorOutrasTransferenciasCorrentes );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_outras_receitas'                 , $this->nuValorOutrasReceitas );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_contrib_plano_sss'               , $this->nuValorContribPlanoSSS );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_compensacao_financeira'          , $this->nuValorCompensacaoFinanceira );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor_deducao_fundeb'                  , $this->nuValorDeducaoFundeb );
            $this->obTSTNReceitaCorrenteLiquida->setDado ('valor' , $this->flValor);

            //Verifica se ja existe o registro na base
            $this->obTSTNReceitaCorrenteLiquida->recuperaPorChave($rsValor);
            if ($rsValor->getNumLinhas() != 1) {
                $obErro = $this->obTSTNReceitaCorrenteLiquida->inclusao($boTransacao);
            } else {
                $obErro = $this->obTSTNReceitaCorrenteLiquida->alteracao($boTransacao);
            }

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNReceitaCorrenteLiquida);

        return $obErro;
    }

    /**
     * Metodo que deleta o periodo da base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function excluirReceitaCorrenteLiquida($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNReceitaCorrenteLiquida->setDado           ('exercicio'   , $this->obROrcamentoEntidade->stExercicio);
            $this->obTSTNReceitaCorrenteLiquida->setDado           ('cod_entidade', $this->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTSTNReceitaCorrenteLiquida->setDado           ('mes'         , $this->inMes);
            $this->obTSTNReceitaCorrenteLiquida->setDado           ('ano'         , $this->inAno);

            $obErro = $this->obTSTNReceitaCorrenteLiquida->exclusao($boTransacao);

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNReceitaCorrenteLiquida);

        return $obErro;
    }

     /**
     * Metodo que inclui os periodos na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function vincularDespesaPessoal($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNDespesaPessoal->setDado           ('exercicio'   , $this->obROrcamentoEntidade->stExercicio);
            $this->obTSTNDespesaPessoal->setDado           ('cod_entidade', $this->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTSTNDespesaPessoal->setDado           ('mes'         , $this->inMes);
            $this->obTSTNDespesaPessoal->setDado           ('ano'         , $this->inAno);
            $this->obTSTNDespesaPessoal->setDado           ('valor_pessoal_ativo'        , $this->nuValorPessoalAtivo );
            $this->obTSTNDespesaPessoal->setDado           ('valor_pessoal_inativo'      , $this->nuValorPessoalInativo );
            $this->obTSTNDespesaPessoal->setDado           ('valor_terceirizacao'        , $this->nuValorOutrasDespesas );
            $this->obTSTNDespesaPessoal->setDado           ('valor_indenizacoes'         , $this->nuValorIndenizacoes );
            $this->obTSTNDespesaPessoal->setDado           ('valor_decisao_judicial'     , $this->nuValorDecisaoJudicial );
            $this->obTSTNDespesaPessoal->setDado           ('valor_exercicios_anteriores', $this->nuValorExercicioAnterior );
            $this->obTSTNDespesaPessoal->setDado           ('valor_inativos_pensionistas', $this->nuValorInativosPensionista );
            $this->obTSTNDespesaPessoal->setDado           ('valor'                      , $this->flValor );

            //Verifica se ja existe o registro na base
            $this->obTSTNDespesaPessoal->recuperaPorChave($rsValor);
            if ($rsValor->getNumLinhas() != 1) {
                $obErro = $this->obTSTNDespesaPessoal->inclusao($boTransacao);
            } else {
                $obErro = $this->obTSTNDespesaPessoal->alteracao($boTransacao);
            }

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNDespesaPessoal);

        return $obErro;
    }

    /**
     * Metodo que deleta o periodo da base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function excluirDespesaPessoal($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNDespesaPessoal->setDado           ('exercicio'   , $this->obROrcamentoEntidade->stExercicio);
            $this->obTSTNDespesaPessoal->setDado           ('cod_entidade', $this->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTSTNDespesaPessoal->setDado           ('mes'         , $this->inMes);
            $this->obTSTNDespesaPessoal->setDado           ('ano'         , $this->inAno);

            $obErro = $this->obTSTNDespesaPessoal->exclusao($boTransacao);

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNDespesaPessoal);

        return $obErro;
    }

    /**
     * Metodo que inclui os periodos na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function vincularParametrosRREO13($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {

            $this->obTSTNRREOAnexo13->setDado           ('exercicio'                , $this->obROrcamentoEntidade->stExercicio);
            $this->obTSTNRREOAnexo13->setDado           ('cod_entidade'             , $this->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTSTNRREOAnexo13->setDado           ('ano'                      , $this->inAno);
            $this->obTSTNRREOAnexo13->setDado           ('vl_receita_previdenciaria', $this->flReceitaPrevidenciaria);
            $this->obTSTNRREOAnexo13->setDado           ('vl_despesa_previdenciaria', $this->flDespesaPrevidenciaria);
            $this->obTSTNRREOAnexo13->setDado           ('vl_saldo_financeiro'      , $this->flSaldoFinanceiro);

            //Verifica se ja existe o registro na base
            $this->obTSTNRREOAnexo13->recuperaPorChave($rsValor);
            if ($rsValor->getNumLinhas() != 1) {
                $obErro = $this->obTSTNRREOAnexo13->inclusao($boTransacao);
            } else {
                $obErro = $this->obTSTNRREOAnexo13->alteracao($boTransacao);
            }
        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNReceitaCorrenteLiquida);

        return $obErro;
    }

    /**
     * Metodo que deleta o periodo da base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function excluirParametrosRREO13($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNRREOAnexo13->setDado           ('exercicio'   , $this->obROrcamentoEntidade->stExercicio);
            $this->obTSTNRREOAnexo13->setDado           ('cod_entidade', $this->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTSTNRREOAnexo13->setDado           ('ano'         , $this->inAno);
            $obErro = $this->obTSTNRREOAnexo13->exclusao($boTransacao);
        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNRREOAnexo13);

        return $obErro;
    }

    /**
     * Metodo que recupera a data de implantacao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param string $stDataImplantacao
     *
     * @return object $obErro
     */
    public function recuperaDataImplantacao($stDataImplantacao)
    {
        //Verifica se ja foi cadastrado uma data de implantacao
        $stFiltro = " WHERE cod_modulo = 36
                        AND parametro  = 'data_implantacao' ";
        $obErro = $this->obTAdministracaoConfiguracao->recuperaTodos($rsDataImplantacao, $stFiltro);
        $stDataImplantacao = $rsDataImplantacao->getcampo('valor');

        return $obErro;
    }

    /**
     * Metodo que recupera os valores da RCL
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listValorRCL(&$rsPeriodo)
    {
        //Aplica os filtros
        if ($this->obROrcamentoEntidade->stExercicio != '') {
            $stFiltro .= " AND receita_corrente_liquida.exercicio <= '" . $this->obROrcamentoEntidade->stExercicio . "' ";
        }
        if ($this->obROrcamentoEntidade->inCodigoEntidade != '') {
            $stFiltro .= " AND receita_corrente_liquida.cod_entidade = " . $this->obROrcamentoEntidade->inCodigoEntidade . " ";
        }
        if ($this->inMes != '') {
            $stFiltro .= " AND receita_corrente_liquida.mes = " . $this->inMes . " ";
        }

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . substr($stFiltro,4);
        }

        $stOrder = ' ORDER BY mes DESC, ano DESC';

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNReceitaCorrenteLiquida->listValorPeriodo($rsPeriodo, $stFiltro, $stOrder);

        return $obErro;
    }

    /**
     * Metodo que recupera os valores da DP
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listValorDP(&$rsPeriodo)
    {
        //Aplica os filtros
        if ($this->obROrcamentoEntidade->stExercicio != '') {
            $stFiltro .= " AND despesa_pessoal.exercicio <= '" . $this->obROrcamentoEntidade->stExercicio . "' ";
        }
        if ($this->obROrcamentoEntidade->inCodigoEntidade != '') {
            $stFiltro .= " AND despesa_pessoal.cod_entidade = " . $this->obROrcamentoEntidade->inCodigoEntidade . " ";
        }
        if ($this->inMes != '') {
            $stFiltro .= " AND despesa_pessoal.mes = " . $this->inMes . " ";
        }

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . substr($stFiltro,4);
        }

        $stOrder = ' ORDER BY mes DESC, ano DESC';

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNDespesaPessoal->listValorPeriodo($rsPeriodo, $stFiltro, $stOrder);

        return $obErro;
    }

    /**
     * Metodo que recupera os valores da RREO13
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listValorRREO13(&$rsPeriodo)
    {
        //Aplica os filtros
        if ($this->obROrcamentoEntidade->stExercicio != '') {
            $stFiltro .= " AND rreo_anexo_13.exercicio <= '" . $this->obROrcamentoEntidade->stExercicio . "' ";
        }
        if ($this->obROrcamentoEntidade->inCodigoEntidade != '') {
            $stFiltro .= " AND rreo_anexo_13.cod_entidade = " . $this->obROrcamentoEntidade->inCodigoEntidade . " ";
        }
        if ($this->inAno != '') {
            $stFiltro .= " AND rreo_anexo_13.ano = " . $this->inAno . " ";
        }

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . substr($stFiltro,4);
        }

        $stOrder = ' ORDER BY ano DESC';

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNRREOAnexo13->recuperaTodos($rsPeriodo, $stFiltro, $stOrder);

        return $obErro;
    }

     /**
     * Metodo que inclui os riscos fiscais na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function incluirRiscosFiscais($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            if ($this->inCodRisco) {
                $inCodRisco = $this->inCodRisco;
            } else {
                $this->obTSTNRiscosFiscais->proximoCod($inCodRisco);
                $this->inCodRisco = $inCodRisco;
            }
            $this->obTSTNRiscosFiscais->setDado('cod_risco'        , $inCodRisco);
            $this->obTSTNRiscosFiscais->setDado('cod_entidade'     , $this->inCodEntidade);
            $this->obTSTNRiscosFiscais->setDado('exercicio'        , $this->stExercicio);
            $this->obTSTNRiscosFiscais->setDado('descricao'        , $this->stDescricao);
            $this->obTSTNRiscosFiscais->setDado('valor'            , $this->flValor);
            $this->obTSTNRiscosFiscais->setDado('cod_identificador', $this->inCodIdentificador ? $this->inCodIdentificador : "null" );

            //Verifica se ja existe o registro na base
            $this->obTSTNRiscosFiscais->recuperaPorChave($rsRiscos);
            if ($rsRiscos->getNumLinhas() != 1) {
                $obErro = $this->obTSTNRiscosFiscais->inclusao($boTransacao);
            } else {
                $obErro = $this->obTSTNRiscosFiscais->alteracao($boTransacao);
            }

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNRiscosFiscais);

        return $obErro;
    }

     /**
     * Metodo que inclui as providencias na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function incluirProvidencias($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNProvidencias->setDado('cod_risco'      , $this->inCodRisco);
            $this->obTSTNProvidencias->setDado('cod_entidade'   , $this->inCodEntidade);
            $this->obTSTNProvidencias->setDado('exercicio'      , $this->stExercicio);
            $this->obTSTNProvidencias->setDado('cod_providencia', $this->inCodProvidencia);
            $this->obTSTNProvidencias->setDado('descricao'      , $this->stDescricao);
            $this->obTSTNProvidencias->setDado('valor'          , $this->flValorProvidencia);

            //Verifica se ja existe o registro na base
            $this->obTSTNProvidencias->recuperaPorChave($rsProvidencias);
            if ($rsProvidencias->getNumLinhas() != 1) {
                $obErro = $this->obTSTNProvidencias->inclusao($boTransacao);
            } else {
                $obErro = $this->obTSTNProvidencias->alteracao($boTransacao);
            }

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNProvidencias);

        return $obErro;
    }

    /**
     * Metodo que deleta as providencias
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function excluirProvidencias($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNProvidencias->setDado('cod_providencia', $this->inCodProvidencia);
            $this->obTSTNProvidencias->setDado('cod_risco'      , $this->inCodRisco);
            $this->obTSTNProvidencias->setDado('cod_entidade'   , $this->inCodEntidade);
            $this->obTSTNProvidencias->setDado('exercicio'      , $this->stExercicio);

            $obErro = $this->obTSTNProvidencias->exclusao($boTransacao);

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNProvidencias);

        return $obErro;
    }

    /**
     * Metodo que deleta os riscos fiscais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array   $arParam
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function excluirRiscosFiscais($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTSTNRiscosFiscais->setDado('cod_risco'      , $this->inCodRisco);
            $this->obTSTNRiscosFiscais->setDado('cod_entidade'   , $this->inCodEntidade);
            $this->obTSTNRiscosFiscais->setDado('exercicio'      , $this->stExercicio);

            $obErro = $this->obTSTNRiscosFiscais->exclusao($boTransacao);

        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTSTNRiscosFiscais);

        return $obErro;
    }

    /**
     * Metodo que recupera o ppa do exerc?cio passado
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param string $stExercicio
     *
     * @return object $obErro
     */
    public function recuperaPPAExercicio(&$rsPPA)
    {
        $stExercicio = $this->stExercicio;
        $stExercicioAnterior = $stExercicio - 3;
        $stFiltro  = " WHERE ano_inicio BETWEEN '".$stExercicioAnterior."' ";
        $stFiltro .= "                      AND '".$stExercicio."' ";
        $obErro = $this->obTPPA->recuperaTodos($rsPPA, $stFiltro);

        return $obErro;
    }

    /**
     * Metodo que recupera os riscos fiscais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listRiscosFiscais(&$rsRiscosFiscais)
    {
        //Aplica os filtros
        if ($this->stExercicio != '') {
            $stFiltro .= " AND riscos_fiscais.exercicio <= '".$this->stExercicio."' ";
        }
        if ($this->inCodEntidade != '') {
            $stFiltro .= " AND riscos_fiscais.cod_entidade = ".$this->inCodEntidade." ";
        }
        if ($this->inCodRisco != '') {
            $stFiltro .= " AND riscos_fiscais.cod_risco = ".$this->inCodRisco." ";
        }

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . substr($stFiltro,4);
        }

        $stOrder = ' ORDER BY riscos_fiscais.cod_risco, riscos_fiscais.cod_entidade, riscos_fiscais.exercicio ';

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNRiscosFiscais->listRiscosFiscais($rsRiscosFiscais, $stFiltro, $stOrder);

        return $obErro;
    }
    
    /**
     * Metodo que recupera risco fiscal - Retorna um registo unico
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function buscaRiscoFiscal(&$rsRiscoFiscal)
    {
        //Aplica os filtros
        if ($this->stExercicio != '') {
            $stFiltro .= " AND riscos_fiscais.exercicio = '".$this->stExercicio."' ";
        }
        if ($this->inCodEntidade != '') {
            $stFiltro .= " AND riscos_fiscais.cod_entidade = ".$this->inCodEntidade." ";
        }
        if ($this->inCodRisco != '') {
            $stFiltro .= " AND riscos_fiscais.cod_risco = ".$this->inCodRisco." ";
        }

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . substr($stFiltro,4);
        }

        $stOrder = ' ORDER BY riscos_fiscais.cod_risco, riscos_fiscais.cod_entidade, riscos_fiscais.exercicio ';

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNRiscosFiscais->buscaRiscoFiscal($rsRiscoFiscal, $stFiltro, $stOrder);

        return $obErro;
    }
    

    /**
     * Metodo que recupera as providências dos riscos fiscais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listProvidencias(&$rsProvidencias)
    {
        //Aplica os filtros
        if ($this->stExercicio != '') {
            $stFiltro .= " AND providencias.exercicio <= '".$this->stExercicio."' ";
        }
        if ($this->inCodEntidade != '') {
            $stFiltro .= " AND providencias.cod_entidade = ".$this->inCodEntidade." ";
        }
        if ($this->inCodRisco != '') {
            $stFiltro .= " AND providencias.cod_risco = ".$this->inCodRisco." ";
        }
        if ($this->inCodProvidencia != '') {
            $stFiltro .= " AND providencias.cod_providencia = ".$this->inCodProvidencia." ";
        }

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . substr($stFiltro,4);
        }

        $stOrder = ' ORDER BY providencias.cod_providencia, providencias.cod_risco, providencias.cod_entidade, providencias.exercicio ';

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNProvidencias->listProvidencias($rsProvidencias, $stFiltro, $stOrder);

        return $obErro;
    }

    /**
     * Metodo que inclui o vinculo das receitas na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function incluirReceitaAnexo3($boFlagTransacao = true, $boTransacao = '')
    {
        $this->obTSTNVinculoSTNReceita->setDado('cod_receita', $this->inCodReceita);
        $this->obTSTNVinculoSTNReceita->setDado('exercicio'  , $this->stExercicio);
        $this->obTSTNVinculoSTNReceita->setDado('cod_tipo'   , $this->inCodTipoReceita);

        $obErro = $this->obTSTNVinculoSTNReceita->inclusao($boTransacao);

        return $obErro;
    }

    /**
     * Metodo que exclui o vinculo das receitas na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function alterarConfiguracaoReceitaAnexo3($boFlagTransacao = true, $boTransacao = '')
    {
        $this->obTAdministracaoConfiguracao->setDado('exercicio' , $this->stExercicio);
        $this->obTAdministracaoConfiguracao->setDado('cod_modulo', 36);
        $this->obTAdministracaoConfiguracao->setDado('parametro', 'deduzir_irrf_anexo_3');
        if ($this->boIRRF == 0) {
            $this->obTAdministracaoConfiguracao->setDado('valor', 'false');
        } else {
            $this->obTAdministracaoConfiguracao->setDado('valor', 'true');
        }

        $obErro = $this->obTAdministracaoConfiguracao->alteracao($boTransacao);

        return $obErro;
    }

/**
     * Metodo que grava a configuracao do anexo 3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function excluirReceitaAnexo3($boFlagTransacao = true, $boTransacao = '')
    {
        $this->obTSTNVinculoSTNReceita->setDado('cod_receita', $this->inCodReceita);
        $this->obTSTNVinculoSTNReceita->setDado('exercicio'  , $this->stExercicio);
        $this->obTSTNVinculoSTNReceita->setDado('cod_tipo'   , $this->inCodTipoReceita);

        $obErro = $this->obTSTNVinculoSTNReceita->exclusao($boTransacao);

        return $obErro;
    }

    /**
     * Mwtodo que recupera as receitas vinculadas ao anexo 3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listReceitasAnexo3(&$rsReceitas)
    {
        //Aplica os filtros
        if ($this->inCodTipoReceita != '') {
            $stFiltro = " WHERE cod_tipo = " . $this->inCodTipoReceita . " ";
        }

        $stOrder = " ORDER BY tipo_vinculo_stn_receita.descricao, cod_receita ";

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNVinculoSTNReceita->listReceitas($rsReceitas, $stFiltro, $stOrder);

        return $obErro;
    }

    /**
     * Metodo que recupera as tipos de receitas vinculadas ao anexo 3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listTipoReceitasAnexo3(&$rsTipo)
    {
        //Aplica os filtros
        if ($this->inCodTipoReceita != '') {
            $stFiltro = " WHERE cod_tipo = " . $this->inCodTipoReceita . " ";
        }

        $stOrder = " ORDER BY descricao ";

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNTipoVinculoSTNReceita->recuperaTodos($rsTipo, $stFiltro, $stOrder);

        return $obErro;
    }

}
