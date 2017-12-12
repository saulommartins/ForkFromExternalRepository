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
    * Classe de Regra de Negócio Lançamento
    * Data de Criação   : 15/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeLancamentoValor.class.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-02.02.05, uc-02.02.04, uc-02.02.11, uc-02.02.21, uc-02.02.31, uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                  );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamento.class.php"           );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php"  );

class RContabilidadeLancamentoValor
{

    /**
        * @var Object
        * @access Private
    */
    public $obTransacao;
    /**
        * @var Object
        * @access Private
    */
    public $obRContabilidadeLancamento;
    /**
        * @var Object
        * @access Private
    */
    public $obRContabilidadePlanoContaAnalitica;
    /**
        * @var Integer
        * @access Private
    */
    public $inContaCredito;
    /**
        * @var Integer
        * @access Private
    */
    public $inContaDebito;
    /**
        * @var Integer
        * @access Private
    */
    public $nuValor;
    /**
        * @var String
        * @access Private
    */
    public $stTipoValor;

    /**
        * @var Array
        * @access Private
    */
    public $arImplantaSado;

    /**
        * @var Array
    */
    public $arAberturaOrcamento;

    /**
        * @access Private
        * @var String
    */
    public $stNomLogErros ;
    /**
        * @access Private
        * @var String
    */
    public $logErros;
    /**
        * @access Private
        * @var Boolean
    */
    public $boLogErros;

    /**
         * @access Public
         * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function setRContabilidadeLancamento($valor) { $this->obRContabilidadeLancamento  = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setRContabilidadePlanoContaAnalitica($valor) { $this->obRContabilidadePlanoContaAnalitica = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setContaCredito($valor) { $this->inContaCredito   = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setContaDebito($valor) { $this->inContaDebito = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setValor($valor) { $this->nuValor = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setTipoValor($valor) { $this->stTipoValor = $valor; }
    /**
         * @access Public
         * @param Array $valor
    */
    public function setImplantaSaldo($valor) { $this->arImplantaSaldo = $valor; }
    /**
         * @access Public
         * @param Array $valor
    */
    public function setAberturaOrcamento($valor) { $this->arAberturaOrcamento = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setNomLogErros($valor) { $this->stNomLogErros         = $valor; }

    /**
         * @access Public
         * @param Object $valor
    */
    public function getTransacao() { return $this->obTransacao; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function getRContabilidadeLancamento() { return $this->obRContabilidadeLancamento; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function getRcontabilidadePlanoContaAnalitica() { return $this->obRContabilidadePlanoContaAnalitica; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getContaCredito() { return $this->inContaCredito;   }
    /**
         * @access Public
         * @param Integer  $valor
    */
    public function getContaDebito() { return $this->inContaDebito; }
    /**
         * @access Public
         * @param Integer  $valor
    */
    public function getValor() { return $this->nuValor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getTipoValor() { return $this->stTipoValor; }
    /**
         * @access Public
         * @param Array $valor
    */
    public function getImplantaSaldo() { return $this->arImplantaSaldo; }
    /**
         * @access Public
         * @param Array $valor
    */
    public function getAberturaOrcamento() { return $this->arAberturaOrcamento; }
    /**
        * @access Public
        * @return String
    */

    public function getNomLogErros() { return $this->stNomLogErros; }

    /**
        * Método Construtor
        * @access Private
    */
    public function RContabilidadeLancamentoValor()
    {
        $this->obRContabilidadeLancamento          = new RContabilidadeLancamento;
        $this->obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
        $this->obTransacao                         = new Transacao;
        $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "M" );
        $this->arAberturaOrcamento = array();
    }

    /**
        * Busca Mes de processamento na tabela configuracao do banco
        * @access Private
        * @param String $inMes
        * @param Object $boTransacao
        * @return Object obErro
    */
    public function getMesProcessamento(&$inMes, $boTransacao = "")
    {
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        $obTAdministracaoConfiguracao   = new TAdministracaoConfiguracao;

        $obTAdministracaoConfiguracao->setDado( "parametro", 'mes_processamento' );
        $obTAdministracaoConfiguracao->setDado( "exercicio", $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
        $obErro = $obTAdministracaoConfiguracao->pegaConfiguracao( $inMes, $boTransacao );

        return $obErro;
    }

    /**
        * Método para validar se o grupo da conta não é do tipo 3 ou 4
        * @access Private
        * @param Integer $inCodPlano
        * @param Object $boTransacao
        * @return Object $obErro
    */
    public function validarGrupoConta($inCodPlano, $boTransacao = "")
    {
        $this->obRContabilidadePlanoContaAnalitica->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
        $this->obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodPlano );
        $obErro = $this->obRContabilidadePlanoContaAnalitica->listarPlanoConta( $rsRecordSet, '', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $inCodGrupo = substr( $rsRecordSet->getCampo('cod_estrutural'),0,1 );
            if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() != 'I' ) {
                if( $inCodGrupo == 3 or $inCodGrupo == 4 )
                    $obErro->setDescricao( 'Não é permitido realizar lancamentos manuais nas contas de grupo 3 ou 4 - ' );
            }
        }

        return $obErro;
    }

    /**
        * Inclui LancamentoValor no Banco de Dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluir($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $boFlagTransacao = false;
        $stAno = explode( '/',$this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $stAno[2] ==  $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ) {
                if( !$this->inContaDebito )
                    $this->inContaDebito = 0;
                if( !$this->inContaCredito )
                    $this->inContaCredito = 0;

                if( $this->obRContabilidadeLancamento->getBoComplemento() == 'true' and !$this->obRContabilidadeLancamento->getComplemento() )
                    $obErro->setDescricao( "Campo complemento é obrigatório!" );

                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadeLancamentoValor->setDado( "tipo"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                    $obTContabilidadeLancamentoValor->setDado( "exercicio"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                    $obTContabilidadeLancamentoValor->setDado( "cod_entidade"  , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLancamentoValor->setDado( "vl_lancamento" , $this->nuValor     );
                    $obTContabilidadeLancamentoValor->setDado( "cod_plano_deb" , $this->inContaDebito );
                    $obTContabilidadeLancamentoValor->setDado( "cod_plano_cred", $this->inContaCredito );
                    $obTContabilidadeLancamentoValor->setDado( "cod_historico" , $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                    $obTContabilidadeLancamentoValor->setDado( "complemento"   , $this->obRContabilidadeLancamento->getComplemento() );
                    $obTContabilidadeLancamentoValor->setDado( "nom_lote"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                    $obTContabilidadeLancamentoValor->setDado( "dt_lote"       , $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );

                    $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->consultar( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() ) {
                            $obTContabilidadeLancamentoValor->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
                        }
                        $obErro = $obTContabilidadeLancamentoValor->inclusaoPorPl( $rsRecordSet, $boTransacao );
                        $this->obRContabilidadeLancamento->setSequencia( $rsRecordSet->getCampo( "sequencia" ) );
                    }
                }

            } else {
                $obErro->setDescricao("A data do lote não corresponde ao exercicio");
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }

    /**
        * Inclui LancamentoPartidaDobrada no Banco de Dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirPartidaDobrada($boTransacao = "")
    {
        include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor = new TContabilidadeValorLancamento;

        $boFlagTransacao = false;
        $stAno = explode( '/', $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if (!$obErro->ocorreu) {
            if ( $stAno[2] == $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ) {
                if( !$this->inContaDebito )
                    $this->inContaDebito = 0;
                if( !$this->inContaCredito )
                    $this->inContaCredito = 0;

                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadeLancamentoValor->setDado( "cod_entidade"  , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLancamentoValor->setDado( "tipo"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                    $obTContabilidadeLancamentoValor->setDado( "exercicio"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                    $obTContabilidadeLancamentoValor->setDado( "nom_lote"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                    $obTContabilidadeLancamentoValor->setDado( "dt_lote"       , $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );

                    $arValoresDebito = Sessao::read('arValoresDebito');
                    foreach ($arValoresDebito as $key => $value) {
                        $obTContabilidadeLancamentoValor->setDado( "vl_lancamento" , $this->getValor($arValoresDebito[$key]['nuVlDebito']) );
                        $obTContabilidadeLancamentoValor->setDado( "cod_plano_deb" , $this->getContaDebito($arValoresDebito[$key]['inCodContaDebito']) );
                        $obTContabilidadeLancamentoValor->setDado( "cod_historico" , $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico($arValoresDebito[$key]['inCodHistoricoDebito']) );
                        $obTContabilidadeLancamentoValor->setDado( "complemento"   , $this->obRContabilidadeLancamento->getComplemento($arValoresDebito[$key]['stComplementoDebito']) );
                    }

                    $arValoresCredito = Sessao::read('arValoresCredito');
                    foreach ($arValoresCredito as $key => $value) {
                        $obTContabilidadeLancamentoValor->setDado( "vl_lancamento"  , $this->getValor($arValoresCredito[$key]['nuVlCredito']) );
                        $obTContabilidadeLancamentoValor->setDado( "cod_plano_cred" , $this->getContaCredito(($arValoresCredito[$key]['inCodContaCredito'])) ); // ERRADO($this->inContaCredito)
                        $obTContabilidadeLancamentoValor->setDado( "cod_historico"  , $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico($arValoresCredito[$key]['inCodHistoricoCredito']) );
                        $obTContabilidadeLancamentoValor->setDado( "complemento"    , $this->obRContabilidadeLancamento->getComplemento($arValoresCredito[$key]['stComplementoCredito']) );
                    }

                    $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->consultar( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() ) {
                            $obTContabilidadeLancamentoValor->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
                        }
                        $obErro = $obTContabilidadeLancamentoValor->inclusaoPorPl( $rsRecordSet, $boTransacao );
                        $this->obRContabilidadeLancamento->setSequencia( $rsRecordSet->getCampo( "sequencia" ) );
                    }
                }

            } else {
                $obErro->setDescricao("A data do lote não corresponde ao exercicio");
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }

    /**
        * Inclui LancamentoValor no Banco de Dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirLancamentoImplantacao($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $obErro = new Erro;
        $boFlagTransacao = false;
        $stAno = explode( '/',$this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $stAno[2] ==  $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ) {
                if( !$this->inContaDebito )
                    $this->inContaDebito = 0;
                if( !$this->inContaCredito )
                    $this->inContaCredito = 0;

                if( $this->obRContabilidadeLancamento->getBoComplemento() == 'true' and !$this->obRContabilidadeLancamento->getComplemento() )
                    $obErro->setDescricao( "Campo complemento é obrigatório!" );

                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadeLancamentoValor->setDado( "tipo"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                    $obTContabilidadeLancamentoValor->setDado( "exercicio"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                    $obTContabilidadeLancamentoValor->setDado( "cod_entidade"  , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLancamentoValor->setDado( "vl_lancamento" , $this->nuValor     );
                    $obTContabilidadeLancamentoValor->setDado( "cod_plano_deb" , $this->inContaDebito );
                    $obTContabilidadeLancamentoValor->setDado( "cod_plano_cred", $this->inContaCredito );
                    $obTContabilidadeLancamentoValor->setDado( "cod_historico" , $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                    $obTContabilidadeLancamentoValor->setDado( "complemento"   , $this->obRContabilidadeLancamento->getComplemento() );
                    $obTContabilidadeLancamentoValor->setDado( "nom_lote"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                    $obTContabilidadeLancamentoValor->setDado( "dt_lote"       , $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );

                    $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( 1 );

                    $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->consultar( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() ) {
                            $obTContabilidadeLancamentoValor->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
                        }
                        $obErro = $obTContabilidadeLancamentoValor->inclusaoPorPl( $rsRecordSet, $boTransacao );
                        $this->obRContabilidadeLancamento->setSequencia($rsRecordSet->getCampo( "sequencia" ) );
                        //exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor
                        $stFiltro = " WHERE exercicio    = '" . $obTContabilidadeLancamentoValor->getDado('exercicio') . "'
                                        AND cod_entidade = " . $obTContabilidadeLancamentoValor->getDado('cod_entidade') . "
                                        AND tipo         = '" . $obTContabilidadeLancamentoValor->getDado('tipo') . "'
                                        AND cod_lote     = " . $obTContabilidadeLancamentoValor->getDado('cod_lote') . "
                                        AND sequencia    = " . $rsRecordSet->getCampo('sequencia') . " ";

                        $obTContabilidadeLancamentoValor->recuperaTodos($rsLancamento,$stFiltro,'',$boTransacao);

                        $obTContabilidadeLancamentoValor->setDado('sequencia',$rsRecordSet->getCampo('sequencia'));
                        $obTContabilidadeLancamentoValor->setDado('tipo_valor',$rsLancamento->getCampo('tipo_valor'));
                    }
                }

            } else {
                $obErro->setDescricao("A data do lote não corresponde ao exercicio");
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }

    /**
        * Altera LancamentoValor no Banco de Dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function alterar($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaDebito.class.php"     );
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaCredito.class.php"    );
        $obTContabilidadeContaCredito        = new TContabilidadeContaCredito;
        $obTContabilidadeContaDebito         = new TContabilidadeContaDebito;
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->excluirConta( 'C', $boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->excluirConta( 'D', $boTransacao);
                if ( !$obErro->ocorreu() ) {
                    if ( $this->nuValor <> 0.00 ) {
                        $obTContabilidadeLancamentoValor->setDado( "sequencia"    , $this->obRContabilidadeLancamento->getSequencia()    );
                        $obTContabilidadeLancamentoValor->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
                        $obTContabilidadeLancamentoValor->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()      );
                        $obTContabilidadeLancamentoValor->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                        $obTContabilidadeLancamentoValor->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    
                        if ($this->inContaCredito and $this->nuValor) {
                            $obTContabilidadeLancamentoValor->setDado( "tipo_valor"   , 'C' );
                            $obTContabilidadeLancamentoValor->setDado( "vl_lancamento", ($this->nuValor * -1) );
                            $obErro = $obTContabilidadeLancamentoValor->inclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $obTContabilidadeContaCredito->setDado( "sequencia"   , $this->obRContabilidadeLancamento->getSequencia()    );
                                $obTContabilidadeContaCredito->setDado( "cod_lote"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
                                $obTContabilidadeContaCredito->setDado( "tipo"        , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()      );
                                $obTContabilidadeContaCredito->setDado( "exercicio"   , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                $obTContabilidadeContaCredito->setDado( "cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                $obTContabilidadeContaCredito->setDado( "tipo_valor"  , 'C' );
                                $obTContabilidadeContaCredito->setDado( "cod_plano"   , $this->inContaCredito );
                                $obErro = $obTContabilidadeContaCredito->inclusao( $boTransacao );
                            }
                        }
                        if ( !$obErro->ocorreu() and $this->inContaDebito and $this->nuValor ) {
                            $obTContabilidadeLancamentoValor->setDado( "tipo_valor"   , 'D' );
                            $obTContabilidadeLancamentoValor->setDado( "vl_lancamento", $this->nuValor );
                            $obErro = $obTContabilidadeLancamentoValor->inclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $obTContabilidadeContaDebito->setDado( "sequencia"   , $this->obRContabilidadeLancamento->getSequencia()    );
                                $obTContabilidadeContaDebito->setDado( "cod_lote"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
                                $obTContabilidadeContaDebito->setDado( "tipo"        , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()      );
                                $obTContabilidadeContaDebito->setDado( "exercicio"   , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                $obTContabilidadeContaDebito->setDado( "cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                $obTContabilidadeContaDebito->setDado( "tipo_valor"  , 'D' );
                                $obTContabilidadeContaDebito->setDado( "cod_plano"   , $this->inContaDebito   );
                                $obErro = $obTContabilidadeContaDebito->inclusao( $boTransacao );
                            }
                        }
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }

    /**
        * Excluir uma conta da tabela conta_debito ou conta_credito
        * @access Private
        * @param String $stTipoValor
        * @param Object $boTransacao
        * @return Object Objeto Erro
    **/
    public function excluirConta($stTipoValor, $boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaDebito.class.php"     );
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaCredito.class.php"    );
        $obTContabilidadeContaCredito        = new TContabilidadeContaCredito;
        $obTContabilidadeContaDebito         = new TContabilidadeContaDebito;
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $obTContabilidadeLancamentoValor->setDado("sequencia"   , $this->obRContabilidadeLancamento->inSequencia );
        $obTContabilidadeLancamentoValor->setDado("exercicio"   , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
        $obTContabilidadeLancamentoValor->setDado("cod_lote"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
        $obTContabilidadeLancamentoValor->setDado("tipo"        , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()      );
        $obTContabilidadeLancamentoValor->setDado("tipo_valor"   , $this->stTipoValor );
        $obTContabilidadeLancamentoValor->setDado("cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
        if ($stTipoValor == 'D') {
            $obTContabilidadeContaDebito->setDado("sequencia"   , $this->obRContabilidadeLancamento->inSequencia );
            $obTContabilidadeContaDebito->setDado("exercicio"   , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
            $obTContabilidadeContaDebito->setDado("cod_lote"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
            $obTContabilidadeContaDebito->setDado("tipo"        , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()      );
            $obTContabilidadeContaDebito->setDado("tipo_valor"  , 'D' );
            $obTContabilidadeContaDebito->setDado("cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
            $obErro = $obTContabilidadeContaDebito->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadeLancamentoValor->setDado( "tipo_valor", 'D' );
                $obErro = $obTContabilidadeLancamentoValor->exclusao( $boTransacao );
            }
        } elseif ($stTipoValor == 'C') {
            $obTContabilidadeContaCredito->setDado("sequencia"   , $this->obRContabilidadeLancamento->inSequencia );
            $obTContabilidadeContaCredito->setDado("exercicio"   , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
            $obTContabilidadeContaCredito->setDado("cod_lote"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()   );
            $obTContabilidadeContaCredito->setDado("tipo"        , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()      );
            $obTContabilidadeContaCredito->setDado("tipo_valor"  , 'C' );
            $obTContabilidadeContaCredito->setDado("cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
            $obErro = $obTContabilidadeContaCredito->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadeLancamentoValor->setDado( "tipo_valor", 'C' );
                $obErro = $obTContabilidadeLancamentoValor->exclusao( $boTransacao );
            }
        }

        return $obErro;
    }

    /**
        * Exclui dados do LancamentoValor do banco de dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function excluirImplantado($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $obErro = new Erro;
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico('1');
            $obErro = $this->obRContabilidadeLancamento->listar($rsLancamento, "" , $boTransacao);

            while ( !$obErro->ocorreu() && !$rsLancamento->eof() ) {
                $this->obRContabilidadeLancamento->setSequencia($rsLancamento->getCampo('sequencia'));
                $this->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio($rsLancamento->getCampo('exercicio'));
                $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($rsLancamento->getCampo('cod_lote'));
                $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo($rsLancamento->getCampo('tipo'));
                $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($rsLancamento->getCampo('cod_entidade'));
                
                $obErro = $this->excluirConta( 'C', $boTransacao );
                
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->excluirConta( 'D', $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->obRContabilidadeLancamento->excluirImplantado( $boTransacao );
                    }
                }
                
                if($obErro->ocorreu()){
                    break;
                }
                
                $rsLancamento->proximo();
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }

    /**
        * Exclui dados do LancamentoValor do banco de dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function excluir($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $obErro = $this->excluirConta( 'C', $boTransacao );
           if ( !$obErro->ocorreu() ) {
                $obErro = $this->excluirConta( 'D', $boTransacao );
                if ( !$obErro->ocorreu() ) {
                        $obErro = $this->obRContabilidadeLancamento->excluir( $boTransacao );
               }
           }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }

    /**
    * Exclui dados do Lancamento de Abertura de Orcamento Anual
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    //mesmo codigo que o outro $this->excluir foi copiado para encapsular funcoes genericas da acao
    public function excluirLancamento($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $obErro = $this->excluirConta( 'C', $boTransacao );
           if ( !$obErro->ocorreu() ) {
                $obErro = $this->excluirConta( 'D', $boTransacao );
                if ( !$obErro->ocorreu() ) {
                        $obErro = $this->obRContabilidadeLancamento->excluir( $boTransacao );
               }
           }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }
    
    /**
        * Lista todos os Lancamentos de acordo com o filtro
        * @access Public
        * @param  Object $rsLista Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listar(&$rsLista, $stOrder = "", $boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $stFiltro = "";

        if( $this->obRContabilidadeLancamento->getSequencia() )
            $stFiltro .= " l.sequencia = ".$this->obRContabilidadeLancamento->getSequencia()." AND ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() )
            $stFiltro .= " l.exercicio = '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()."' AND ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() )
            $stFiltro .= " l.cod_lote = ". $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()." AND  ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLoteInicial() )
            $stFiltro .= " l.cod_lote >= ". $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLoteInicial()." AND  ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLoteFinal() )
            $stFiltro .= " l.cod_lote <= ". $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLoteFinal()." AND  ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() )
            $stFiltro .= " UPPER(lo.nom_lote) like UPPER('%".$this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote()."%') AND ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() )
            $stFiltro .= " l.tipo = '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()."' AND ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " l.cod_entidade IN ( ".$this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() )
            $stFiltro .= " l.cod_historico = ".$this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico(). " AND ";
        if( $this->nuValor )
            $stFiltro .= " la.vl_lancamento = '%".$this->nuValor."%' AND ";
        if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial() and $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteTermino() ) {
            if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial() == $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteTermino() ) {
                $stFiltro .= " dt_lote = to_date('".$this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial()."', 'dd/mm/yyyy') AND ";
            } else {
                $stFiltro .= " dt_lote between to_date('".$this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial()."', 'dd/mm/yyyy') ";
                $stFiltro .= "and to_date('".$this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteTermino(). "', 'dd/mm/yyyy') AND ";
            }
        }
        $stFiltro = ($stFiltro)? " AND ".substr($stFiltro, 0, strlen($stFiltro)-4) : "";
        $obErro = $obTContabilidadeLancamentoValor->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }
    public function buscaSistemaContabilCreditoDebito(&$rsSistemaContabil, $stOrder = "", $boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;
        if( !$this->inContaDebito )
             $this->inContaDebito = 0;
        if( !$this->inContaCredito )
             $this->inContaCredito = 0;
        $obTContabilidadeLancamentoValor->setDado("cod_plano_debito"  , $this->getContaDebito());
        $obTContabilidadeLancamentoValor->setDado("cod_plano_credito" , $this->getContaCredito());
        $obTContabilidadeLancamentoValor->setDado("stExercicio"       , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio());

        $obErro = $obTContabilidadeLancamentoValor->recuperaSistemaContabilCreditoDebito( $rsSistemaContabil, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaPorChave na classe Persistente
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function consultar($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaDebito.class.php"     );
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaCredito.class.php"    );
        $obTContabilidadeContaCredito        = new TContabilidadeContaCredito;
        $obTContabilidadeContaDebito         = new TContabilidadeContaDebito;
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

        $obTContabilidadeLancamentoValor->setDado( "sequencia"    , $this->obRContabilidadeLancamento->inSequencia );
        $obTContabilidadeLancamentoValor->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()   );
        $obTContabilidadeLancamentoValor->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()     );
        $obTContabilidadeLancamentoValor->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()        );
        $obTContabilidadeLancamentoValor->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
        $obTContabilidadeLancamentoValor->setDado( "tipo_valor"   , $this->stTipoValor );

        $obErro = $obTContabilidadeLancamentoValor->recuperaPorChave( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->nuValor = $rsRecordSet->getCampo( "vl_lancamento" );
            $obErro = $this->obRContabilidadeLancamento->consultar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadeContaCredito->setDado( "sequencia"    , $this->obRContabilidadeLancamento->inSequencia );
                $obTContabilidadeContaCredito->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()   );
                $obTContabilidadeContaCredito->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()     );
                $obTContabilidadeContaCredito->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()        );
                $obTContabilidadeContaCredito->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeContaCredito->setDado( "tipo_valor"   , 'C' );
                $obErro = $obTContabilidadeContaCredito->recuperaPorChave( $rsContaCredito, $boTransacao );
                $this->inContaCredito = $rsContaCredito->getCampo( "cod_plano" );
                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadeContaDebito->setDado( "sequencia"    , $this->obRContabilidadeLancamento->inSequencia );
                    $obTContabilidadeContaDebito->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()   );
                    $obTContabilidadeContaDebito->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()     );
                    $obTContabilidadeContaDebito->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()        );
                    $obTContabilidadeContaDebito->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeContaDebito->setDado( "tipo_valor"   , 'D' );
                    $obErro = $obTContabilidadeContaDebito->recuperaPorChave( $rsContaDebito, $boTransacao );
                    $this->inContaDebito = $rsContaDebito->getCampo( "cod_plano" );
                }
            }
        }

        return $obErro;
    }

    /**
        * Executa Implanta Saldo
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function implantarSaldo($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;
        $obErro = new Erro;
        if (sizeof($this->arImplantaSaldo)) {
            foreach ($this->arImplantaSaldo as $inCodPlano_inCodSequencia => $nuValor) {
                $arTmp = explode( '-', $inCodPlano_inCodSequencia );
                $inCodPlano     = $arTmp[0] ;
                $inCodSequencia = $arTmp[1] ;
                $this->nuValor = $nuValor;
                $this->inContaCredito = 0;
                $this->inContaDebito  = 0;
                if ($this->nuValor < 0) {
                    $this->nuValor = ($nuValor * -1);
                    $this->inContaCredito = $inCodPlano;
                } else {
                    $this->inContaDebito  = $inCodPlano;
                }
                $obTContabilidadeLancamentoValor->setDado( "sequencia"    , $inCodSequencia );
                $obTContabilidadeLancamentoValor->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()   );
                $obTContabilidadeLancamentoValor->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()     );
                $obTContabilidadeLancamentoValor->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()        );
                $obTContabilidadeLancamentoValor->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeLancamentoValor->setDado( "tipo_valor"   , $this->stTipoValor );

                    $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote(1) ;
                    if ($inCodSequencia) {
                        $this->obRContabilidadeLancamento->setSequencia ( $inCodSequencia ) ;
                        $obErro = $this->alterar( $boTransacao );
                    } else {
                        if($this->nuValor <> 0.00)
                            $obErro = $this->incluirLancamentoImplantacao( $boTransacao );
                    }
                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }

        return $obErro;
    }
    /**
        * Executa um listarLoteImplantacao na classe de Tabela para Implantacao de Saldo
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarLoteImplantacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        $this->obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade($this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade);

        $obErro = $this->obRContabilidadePlanoContaAnalitica->listarLoteImplantacaoAux($rsRecordSet, "", $boTransacao);

        return $obErro;
    }

    /**
        * Executa um listarLoteImplantacaoPlanoBanco na classe de Tabela para Implantacao de Saldo
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarLoteImplantacaoPlanoBanco(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        $this->obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade($this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade);
        $obErro = $this->obRContabilidadePlanoContaAnalitica->listarLoteImplantacaoAuxPlanoBanco($rsRecordSet, "", $boTransacao);

        return $obErro;
    }

    /**
        * Executa um listarSaldoContaAnalitica - Utilizado para gerar saldos de balanço para o próximo exercicio
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listarSaldoContaAnalitica(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );
        $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;

        $obTContabilidadePlanoAnalitica->setDado( "exercicio"    , $this->obRContabilidadePlanoContaAnalitica->getExercicio()-1 );
        $obErro = $obTContabilidadePlanoAnalitica->recuperaSaldoContaAnalitica( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
        * Método para validar se ja foi realizada a implantação de saldos de balanço em um exercício informado
        * @access Private
        * @param Integer $inCodPlano
        * @param Object $boTransacao
        /* @return Object $obErro
    */
    public function verificaImplantacaoSaldos(&$boRetorno, $stExercicio, $boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;

        $obTContabilidadePlanoConta->setDado("exercicio", $stExercicio );
        $obErro = $obTContabilidadePlanoConta->recuperaVerificaImplantacaoSaldos($rsRetorno, '','',$boTransacao);
        if ( !$obErro->ocorreu() ) {
            $boRetorno = $rsRetorno->getCampo('retorno');
        }

        return $obErro;
    }

    /**
        * Método para verificar qual o cod_plano referente a um cod_estrutural em um exercicio especifico
        * @access Private
        * @param Integer $inCodPlano
        * @param Object $boTransacao
        /* @return Object $obErro
    */
    public function recuperaCodPlanoPorEstrutural(&$stRetorno, $stExercicio, $stCodEstrutural, $boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );
        $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;

        $obTContabilidadePlanoAnalitica->setDado("exercicio", $stExercicio );
        $obTContabilidadePlanoAnalitica->setDado("cod_estrutural", $stCodEstrutural );
        $obErro = $obTContabilidadePlanoAnalitica->recuperaCodPlanoPorEstrutural($rsRetorno, '','',$boTransacao);
        if ( !$obErro->ocorreu() ) {
            $stRetorno = $rsRetorno->getCampo('cod_plano');
        }

        return $obErro;
    }

    /**
        * Método para excluir a implantacao de saldos em um exercicio informado
        * @access Private
        * @param Integer $inCodPlano
        * @param Object $boTransacao
        * @return Object $obErro
    */
    public function excluirImplantacaoSaldos($stExercicio, $boTransacao = "")
    {
        $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('I');
        $this->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio($stExercicio);
        $obErro = $this->excluirImplantado( $boTransacao );

        return $obErro;
    }

    public function gerarSaldosBalanco($boTransacao = "")
    {
        $obErro = new Erro;
        //$boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $this->boLogErros = false ;
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->verificaImplantacaoSaldos($boRetorno, $this->obRContabilidadePlanoContaAnalitica->getExercicio(), $boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ($boRetorno=='true') {
                    $obErro = $this->excluirImplantacaoSaldos($this->obRContabilidadePlanoContaAnalitica->getExercicio(), $boTransacao);
                }
                if ( !$obErro->ocorreu() ) {
                    $obErro=$this->listarSaldoContaAnalitica( $rsSaldo,"",$boTransacao);
                    if ( !$obErro->ocorreu() ) {
                        while ( !$rsSaldo->eof() and !$obErro->ocorreu() ) {
                            $this->obRContabilidadePlanoContaAnalitica->setCodEstrutural( $rsSaldo->getCampo("cod_estrutural"));
                            //$this->obRContabilidadePlanoContaAnalitica->setCodPlano ( $rsSaldo->getCampo("cod_plano") );
                            $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $rsSaldo->getCampo("cod_entidade") );
                            $obErro=$this->listarLoteImplantacao( $rsContas,"",$boTransacao ) ;

                            if ( !$obErro->ocorreu() ) {
                                if ( !$rsContas->eof() ) {
                                    $arImplantaSaldo = array();
                                    while ( !$rsContas->eof() ) {
                                        if ( trim($rsSaldo->getCampo("saldo")) != '' ) {
                                            $obErro = $this->recuperaCodPlanoPorEstrutural($stCodPlano, $rsContas->getCampo("exercicio"),$rsContas->getCampo("cod_estrutural"), $boTransacao);
                                            if ( !$obErro->ocorreu() ) {
                                                $arImplantaSaldo[$stCodPlano."-".$rsContas->getCampo( "sequencia" )]= $rsSaldo->getCampo("saldo");
                                            }
                                        }
                                        $rsContas->proximo();
                                    }
                                    if ( !$obErro->ocorreu() ) {
                                        $this->setImplantaSaldo($arImplantaSaldo);
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $this->obRContabilidadePlanoContaAnalitica->getExercicio() );
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "01/01/". $this->obRContabilidadePlanoContaAnalitica->getExercicio() );
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($rsSaldo->getCampo("cod_entidade") );
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('I');
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Implantação de Saldo Automática');
                                        $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(1);
                                        $obErro = $this->implantarSaldo($boTransacao);
                                    }
                                    if ($obErro->ocorreu()) {
                                        break;
                                    }
                                } else {
                                    $stLogObs = " ".$rsSaldo->getCampo("cod_estrutural")."\t\t".$rsSaldo->getCampo("cod_entidade")."\t\t".$rsSaldo->getCampo("saldo");
                                    $this->logLinha( $stLogObs ) ;
                                }
                                $rsSaldo->proximo();
                            }
                        }
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );
        if ($this->nuErros > 0) {
            fclose($this->logErros) ;
        }
        echo "<script type='text/javascript'>LiberaFrames(true,false);</script>";

        return $obErro;
    }

    public function logCabec()
    {
        $stHoraLog = date( "dmYHis" );
        $this->setNomLogErros("logErros".$stHoraLog.".txt");
        $this->logErros = fopen( "../../tmp/".$this->getNomLogErros(), "w");

        fwrite($this->logErros, "+-------------------------------------------------------------------------+\n");
        fwrite($this->logErros, " URBEM\n");
        fwrite($this->logErros, " Gerar Saldos de Balanço do exercício anterior.\n");
        fwrite($this->logErros, " Log de erros\n");
        fwrite($this->logErros, "+-------------------------------------------------------------------------+\n\n");
        fwrite($this->logErros, " As contas abaixo não tiveram seu saldo implantado para o exercício atual.\n");
        fwrite($this->logErros, " Cadastre-as no exercício atual e refaça a geração dos Saldos de Balanço.\n\n");
        fwrite($this->logErros, " CONTA\t\t\t\tENTIDADE\t\tSALDO\n");
    }

    public function logLinha($stLogObs)
    {
        if (!$this->logErros) {
            $this->logCabec() ;
            $this->boLogErros = true ;
        }
        fwrite($this->logErros, $stLogObs."\n");
    }

    /**
        * Executa Abertura Orçamento
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function aberturaOrcamento($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;
        $obTContabilidadeLancamento          = new TContabilidadeLancamento;
        $obErro = new Erro;

        if (sizeof($this->arAberturaOrcamento)) {
            foreach ($this->arAberturaOrcamento as $index => $nuValor) {
                $arTmp = explode( '-', $index );
                $this->inContaDebito  = $arTmp[0];
                $this->inContaCredito = $arTmp[1];
                $inCodSequencia       = $arTmp[2];
                $this->nuValor        = $nuValor;
                
                
                $obTContabilidadeLancamentoValor->setDado( "sequencia"    , $inCodSequencia );
                $obTContabilidadeLancamentoValor->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()   );
                $obTContabilidadeLancamentoValor->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()     );
                $obTContabilidadeLancamentoValor->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()        );
                $obTContabilidadeLancamentoValor->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeLancamentoValor->setDado( "tipo_valor"   , $this->stTipoValor );

                if ($this->nuValor <> 0.00) {
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->incluirLancamentoAberturaOrcamento( $boTransacao );
                    }
                }
                
                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }

        return $obErro;
    }

    /**
        * Inclui Lancamento Valores de Abertura de Orçamento no Banco de Dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirLancamentoAberturaOrcamento($boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
        $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;
        $obErro = new Erro;
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( Sessao::getExercicio() ==  $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ) {
                if( !$this->inContaDebito )
                    $this->inContaDebito = 0;
                if( !$this->inContaCredito )
                    $this->inContaCredito = 0;

                if( $this->obRContabilidadeLancamento->getBoComplemento() == 'true' and !$this->obRContabilidadeLancamento->getComplemento() )
                    $obErro->setDescricao( "Campo complemento é obrigatório!" );

                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadeLancamentoValor->setDado( "tipo"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                    $obTContabilidadeLancamentoValor->setDado( "exercicio"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                    $obTContabilidadeLancamentoValor->setDado( "cod_entidade"  , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLancamentoValor->setDado( "vl_lancamento" , $this->nuValor );
                    $obTContabilidadeLancamentoValor->setDado( "cod_plano_deb" , $this->inContaDebito );
                    $obTContabilidadeLancamentoValor->setDado( "cod_plano_cred", $this->inContaCredito );
                    $obTContabilidadeLancamentoValor->setDado( "cod_historico" , $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                    $obTContabilidadeLancamentoValor->setDado( "complemento"   , $this->obRContabilidadeLancamento->getComplemento() );
                    $obTContabilidadeLancamentoValor->setDado( "nom_lote"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                    $obTContabilidadeLancamentoValor->setDado( "dt_lote"       , $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );

                    $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->consultar( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() ) {
                            $obTContabilidadeLancamentoValor->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                        }
                        
                        $obErro = $obTContabilidadeLancamentoValor->inclusaoPorPl( $rsRecordSet, $boTransacao );
                        $this->obRContabilidadeLancamento->setSequencia($rsRecordSet->getCampo( "sequencia" ) );
                        //exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor
                        $stFiltro = " WHERE exercicio    = '" . $obTContabilidadeLancamentoValor->getDado('exercicio') . "'
                                        AND cod_entidade = " . $obTContabilidadeLancamentoValor->getDado('cod_entidade') . "
                                        AND tipo         = '" . $obTContabilidadeLancamentoValor->getDado('tipo') . "'
                                        AND cod_lote     = " . $obTContabilidadeLancamentoValor->getDado('cod_lote') . "
                                        AND sequencia    = " . $rsRecordSet->getCampo('sequencia') . " ";

                        $obTContabilidadeLancamentoValor->recuperaTodos($rsLancamento,$stFiltro,'',$boTransacao);

                        $obTContabilidadeLancamentoValor->setDado('sequencia',$rsRecordSet->getCampo('sequencia'));
                        $obTContabilidadeLancamentoValor->setDado('tipo_valor',$rsLancamento->getCampo('tipo_valor'));
                    }
                }
            }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoValor );

        return $obErro;
    }

}
