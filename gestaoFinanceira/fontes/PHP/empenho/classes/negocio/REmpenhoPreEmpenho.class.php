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
    * Classe de Regra de Pre Empenho
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoPreEmpenho.class.php 66483 2016-09-02 17:16:31Z michel $

    *Casos de uso: uc-02.01.23
                   uc-02.03.15
                   uc-02.03.02
                   uc-02.03.05
                   uc-02.03.03
                   uc-02.01.06
                   uc-02.03.30
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoTipoEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoHistorico.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoItemPreEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAtributoEmpenhoValor.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_GA_ADM_NEGOCIO."RUsuario.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php";
include_once CAM_GA_ADM_NEGOCIO."RUnidadeMedida.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPermissaoAutorizacao.class.php";
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoPreEmpenho.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoPreEmpenhoDespesa.class.php';
//INCLUDE DAS CLASSES PARA O TRATAMENTO DOS ATRIBUTOS DINAMICOS
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAtributoEmpenhoValor.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

class REmpenhoPreEmpenho
{
    /**
    * @access Private
    * @var Object
    **/
    public $obRCadastroDinamico;
    /**
    * @access Private
    * @var Object
    **/
    public $obREmpenhoTipoEmpenho;
    /**
    * @access Private
    * @var Object
    **/
    public $obREmpenhoPermissaoAutorizacao;
    /**
    * @access Private
    * @var Object
    **/
    public $obREmpenhoHistorico;
    /**
    * @access Private
    * @var Object
    **/
    public $obRCGM;
    /**
    * @access Private
    * @var Object
    **/
    public $obRUsuario;
    /**
    * @access Private
    * @var Object
    **/
    public $obROrcamentoDespesa;
    /**
    * @access Private
    * @var Object
    **/
    public $obROrcamentoClassificacaoDespesa;

    var $obErro;
    /**
    * @access Private
    * @var Reference Object
    **/
    public $roUltimoItemPreEmpenho;
    /**
    * @access Private
    * @var Reference Object
    **/
    public $roUltimoAtributoEmpenhoValor;
    /**
    * @access Private
    * @var Integer
    **/
    public $inCodPreEmpenho;
    /**
    * @access Private
    * @var Integer
    **/
    public $inCodMaterial;
    /**
    * @access Private
    * @var String
    **/
    public $stDescricao;
    /**
    * @access Private
    * @var String
    **/
    public $stNomMaterial;
    /**
    * @access Private
    * @var String
    **/
    public $stExercicio;
    /**
    * @access Private
    * @var Array
    **/
    public $arItemPreEmpenho;
    /**
    * @access Private
    * @var Array
    **/
    public $arAtributoEmpenhoValor;
    /**
    * @access Private
    * @var Boolean
    **/
    public $boImplantado;
    /**
    * @access Public;
    * @var Integer
    **/
    public $inCountDespesaExercicio;
    /**
    * @access Public;
    * @var String
    **/
    public $stTimestampAtributo;
    /**
    * @access Public;
    * @var Integer
    **/
    public $inCodDespesaFixa;
    /**
    * @access Public;
    * @var String
    **/
    public $stDataEmpenho;
    /**
    * @access Public;
    * @var Integer
    **/
    public $inCodEntidade;
    /**
    * @access Public;
    * @var String
    **/
    public $stTipoEmissao;
    
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setREmpenhoTipoEmpenho($valor) { $this->obREmpenhoTipoEmpenho = $valor; }
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setRCadastroDinamico($valor) { $this->obRCadastroDinamico   = $valor; }
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setREmpenhoHistorico($valor) { $this->obREmpenhoHistorico = $valor; }
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setREmpenhoPermissaoAutorizacao($valor) { $this->obREmpenhoPermissaoAutorizacao = $valor; }
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setRCGM($valor) { $this->obRCGM = $valor; }
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setRUsuario($valor) { $this->obRUsuario = $valor; }
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setROrcamentoDespesa($valor) { $this->obROrcamentoDespesa = $valor; }
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setROrcamentoClassificacaoDespesa($valor) { $this->obROrcamentoClassificacaoDespesa = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodPreEmpenho($valor) { $this->inCodPreEmpenho = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodMaterial($valor) { $this->inCodMaterial = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setDescricao($valor) { $this->stDescricao = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setNomMaterial($valor) { $this->stNomMaterial = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setExercicio($valor) { $this->stExercicio = $valor; }
    /**
    * @access Public
    * @param Array $Valor
    **/
    public function setItemPreEmpenho($valor) { $this->arItemPreEmpenho        = $valor; }
    /**
    * @access Public
    * @param Boolean $Valor
    **/
    public function setImplantado($valor) { $this->boImplantado = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCountDespesaExercicio($valor) { $this->inCountDespesaExercicio = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setTimestampAtributo($valor) { $this->stTimestampAtributo = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodDespesaFixa($valor) { $this->inCodDespesaFixa = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setDataEmpenho($valor) { $this->stDataEmpenho = $valor; }
    /**
    * @access Public
    * @param String $Valor
    * $valor = 'E': Comportamento padrão. Leva em consideração a data de empenho setada para a função.
    * $valor = 'R': Comprtamento para as consultas de reserva de saldo, que não levam em consideração a data de empenho setada, mas do exercicio todo.
    **/
    public function setTipoEmissao($valor) { $this->stTipoEmissao = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    function setCodEntidade($valor) { $this->inCodEntidade = $valor; }

    /**
    * @access Public
    * @return Object
    **/
    public function getREmpenhoTipoEmpenho() { return $this->obREmpenhoTipoEmpenho; }
    /**
    * @access Public
    * @return Object
    **/
    public function getRCadastroDinamico() { return $this->obRCadastroDinamico;   }
    /**
    * @access Public
    * @return Object
    **/
    public function getREmpenhoHistorico() { return $this->obREmpenhoHistorico; }
    /**
    * @access Public
    * @return Object
    **/
    public function getREmpenhoPermissaoAutorizacao() { return $this->obREmpenhoPermissaoAutorizacao; }
    /**
    * @access Public
    * @return Object
    **/
    public function getRCGM() { return $this->obRCGM; }
    /**
    * @access Public
    * @return Object
    **/
    public function getRUsuario() { return $this->obRUsuario; }
    /**
    * @access Public
    * @return Object
    **/
    public function getROrcamentoClassificacaoDespesa() { return $this->obROrcamentoClassificacaoDespesa; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getCodPreEmpenho() { return $this->inCodPreEmpenho; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getCodMaterial() { return $this->inCodMaterial; }
    /**
    * @access Public
    * @return String
    **/
    public function getDescricao() { return $this->stDescricao; }
    /**
    * @access Public
    * @return String
    **/
    public function getNomMaterial() { return $this->stNomMaterial; }
    /**
    * @access Public
    * @return String
    **/
    public function getExercicio() { return $this->stExercicio;        }
    /**
    * @access Public
    * @return Array
    **/
    public function getItemPreEmpenho() { return $this->arItemPreEmpenho; }
    /**
    * @access Public
    * @return Boolean
    **/
    public function getImplantado() { return $this->boImplantado; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getCountDespesaExercicio() { return $this->inCountDespesaExercicio; }
    /**
    * @access Public
    * @return String
    **/
    public function getTimestampAtributo() { return $this->stTimestampAtributo; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getCodDespesaFixa() { return $this->inCodDespesaFixa; }
    /**
    * @access Public
    * @return String
    **/
    public function getDataEmpenho() { return $this->stDataEmpenho; }
    /**
    * @access Public
    * @return String
    **/
    public function getTipoEmissao() { return $this->stTipoEmissao; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getCodEntidade() { return $this->inCodEntidade; }

    /**
    * Método construtor
    * @access Public
    **/
    public function REmpenhoPreEmpenho()
    {
        $this->obRCadastroDinamico               = new RCadastroDinamico;
        $this->obREmpenhoTipoEmpenho             = new REmpenhoTipoEmpenho;
        $this->obREmpenhoHistorico               = new REmpenhoHistorico;
        $this->obREmpenhoPermissaoAutorizacao    = new REmpenhoPermissaoAutorizacao;
        $this->obRCGM                            = new RCGM;
        $this->obRUsuario                        = new RUsuario;
        $this->obROrcamentoDespesa               = new ROrcamentoDespesa;
        $this->obROrcamentoClassificacaoDespesa  = new ROrcamentoClassificacaoDespesa;
        $this->obTransacao                       = new Transacao;
        $this->obRCadastroDinamico->setPersistenteValores  ( new TEmpenhoAtributoEmpenhoValor );
        $this->obRCadastroDinamico->setCodCadastro( 1 );
        $this->obRCadastroDinamico->obRModulo->setCodModulo( 10 );
    }

    /**
    * Método Para adicionar Itens Pre Empenho
    * @access Public
    **/
    public function addItemPreEmpenho()
    {
        $this->arItemPreEmpenho[] = new REmpenhoItemPreEmpenho( $this );
        $this->roUltimoItemPreEmpenho = &$this->arItemPreEmpenho[ count( $this->arItemPreEmpenho ) -1 ];
    }

    /**
    * Método para consultar saldo anterior de uma determinada Despesa durante o ano
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
    **/
    public function consultaSaldoAnterior(&$nuSaldoAnterior, $stOrder = "" , $boTransacao = "")
    {
        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;

        $obTEmpenhoPreEmpenho->setDado( "exercicio", $this->stExercicio );
        $obTEmpenhoPreEmpenho->setDado( "cod_despesa", $this->obROrcamentoDespesa->getCodDespesa() );

        if (date('Y') > Sessao::getExercicio() && Sessao::read('data_reserva_saldo_GF')) {
            $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnteriorDataAtual( $rsRecordSet, $stOrder, $boTransacao );
        } else {
            $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnterior( $rsRecordSet, $stOrder, $boTransacao );
        }
        
        if ( !$obErro->ocorreu() ) {
            $nuSaldoAnterior = $rsRecordSet->getCampo( "saldo_anterior" );
        }

        return $obErro;
    }
    
    /**
    * Método para consultar saldo anterior de uma determinada Despesa tendo seu valor sendo até a data setada
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
    **/
    public function consultaSaldoAnteriorDataEmpenho(&$nuSaldoAnterior, $stOrder = "" , $boTransacao = "")
    {
        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;

        $obTEmpenhoPreEmpenho->setDado( "exercicio"  , $this->stExercicio );
        $obTEmpenhoPreEmpenho->setDado( "cod_despesa", $this->obROrcamentoDespesa->getCodDespesa() );
        $obTEmpenhoPreEmpenho->setDado( "entidade"   , $this->inCodEntidade );
        $obTEmpenhoPreEmpenho->setDado( "dt_empenho" , $this->stDataEmpenho );
        
        if ( empty($this->stTipoEmissao) ){
            $this->stTipoEmissao = 'E';
        }

        $obTEmpenhoPreEmpenho->setDado( "tipo_emissao" , $this->stTipoEmissao );
        
        if (date('Y') > Sessao::getExercicio() && Sessao::read('data_reserva_saldo_GF')) {
            $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnteriorDataAtualEmpenho( $rsRecordSet, $stOrder, $boTransacao );
        } else {
            $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnteriorDataEmpenho( $rsRecordSet, $stOrder, $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $nuSaldoAnterior = $rsRecordSet->getCampo( "saldo_anterior" );
        }

        return $obErro;
    }
    
    /**
    * Método para checar Forma de Exercucao do Orcamento
    * @access public
    * @param Object $obTransacao
    * @return Object $obErro
    **/
    public function checarFormaExecucaoOrcamento(&$stFormaExecucao,  $boTransacao = "")
    {
        $obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
        $obROrcamentoConfiguracao->setCodModulo( 8 );
        $obErro = $obROrcamentoConfiguracao->consultarConfiguracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $obROrcamentoConfiguracao->getFormaExecucaoOrcamento() ) {
                $stFormaExecucao = true;
            } else {
                $stFormaExecucao = false;
            }
        }

        return $obErro;
    }

    /**
    * Lista todas as unidades de medida
    * @access Public
    * @param Object $rsRecordSet
    * @param Object $boTransacao
    * @return Object $obErro
    **/
    public function listarUnidadeMedida(&$rsRecordSet, $boTransacao = "")
    {
        $obErro = "";
        $stOrdem = "";
        $obUnidadeMedida = new RUnidadeMedida;
        $obUnidadeMedida->listar( $rsRecordSet, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
    * Lista Materiais
    * @access Public
    * @param Object $rsRecordSet
    * @param Object $boTransacao
    * @return Object $obErro
    **/
    public function listarMaterial(&$rsRecordSet, $boTransacao = "")
    {
        include_once ( CAM_GP_COM_MAPEAMENTO."VComprasSamlinkSiamMater.class.php"            );
        $obVSamlinkSiamMater               = new VSamlinkSiamMater;
        if( $this->inCodMaterial )
        $stFiltro .= " codigo = ".$this->inCodMaterial." AND ";
        if( $this->stNomMaterial )
        $stFiltro .= " LOWER(descricao) like LOWER('%".$this->stNomMaterial."%') AND ";
        $stFiltro = ( $stFiltro ) ? " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
        $stOrdem  = " ORDER BY codigo ";
        $obErro = $obVSamlinkSiamMater->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
    * Executa um listar para contar quantos despesas existem na empenho.pre_empenho_despesa no exercicio atual
    * @access public
    * @return Object $obErro
    **/
    function consultarExistenciaDespesa($boTransacao = "")
    {
        $obTEmpenhoPreEmpenhoDespesa       = new TEmpenhoPreEmpenhoDespesa;

        $obTEmpenhoPreEmpenhoDespesa->setDado( "exercicio", $this->stExercicio );
        $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_despesa", $this->obROrcamentoDespesa->getCodDespesa() );

        if($this->getCodEntidade())
            $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_entidade", $this->getCodEntidade() );
        $obErro = $obTEmpenhoPreEmpenhoDespesa->recuperaExistenciaDespesa( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->inCountDespesaExercicio = $rsRecordSet->getCampo( "total" );
        }

        return $obErro;
    }

    /**
    * Executa listar para achar os Itens de Pre Empenho e Seta seus dados
    * @access public
    * @param Object  $boTransacao Parâmetro de Transação
    * @return Object $obErro
    **/
    public function consultarItemPreEmpenho($boTransacao = "")
    {
        $obREmpenhoItemPreEmpenho = new REmpenhoItemPreEmpenho( $this );
        $stOrder = "num_item";
        $obErro = $obREmpenhoItemPreEmpenho->listar( $rsItemPreEmpenho, $stOrder, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            while ( !$rsItemPreEmpenho->eof() ) {
                $this->addItemPreEmpenho();
                $this->roUltimoItemPreEmpenho->setCodItemPreEmp ( $rsItemPreEmpenho->getCampo( "cod_item" )         );
                $this->roUltimoItemPreEmpenho->setNumItem       ( $rsItemPreEmpenho->getCampo( "num_item" )         );
                $this->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $rsItemPreEmpenho->getCampo( "cod_unidade" )                );
                $this->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $rsItemPreEmpenho->getCampo( "cod_grandeza")  );
                $this->roUltimoItemPreEmpenho->setNomUnidade    ( $rsItemPreEmpenho->getCampo( "nom_unidade" )      );
                $this->roUltimoItemPreEmpenho->setSiglaUnidade  ( $rsItemPreEmpenho->getCampo( "sigla_unidade" )    );
                $this->roUltimoItemPreEmpenho->setNomItem       ( $rsItemPreEmpenho->getCampo( "nom_item" )         );
                $this->roUltimoItemPreEmpenho->setQuantidade    ( $rsItemPreEmpenho->getCampo( "quantidade" )       );
                $this->roUltimoItemPreEmpenho->setValorTotal    ( $rsItemPreEmpenho->getCampo( "vl_total" )         );
                $this->roUltimoItemPreEmpenho->setComplemento   ( $rsItemPreEmpenho->getCampo( "complemento" )      );
                $this->roUltimoItemPreEmpenho->setCodCentroCusto( $rsItemPreEmpenho->getCampo( "cod_centro" )       );
                $this->roUltimoItemPreEmpenho->setCodigoMarca   ( $rsItemPreEmpenho->getCampo( "cod_marca" )        );                
                $this->roUltimoItemPreEmpenho->consultaCodMaterial( $boTransacao );

                if( $rsItemPreEmpenho->getCampo( "cod_centro" ) == '')
                    $this->roUltimoItemPreEmpenho->consultaCodCentroCusto( $boTransacao );

                $rsItemPreEmpenho->proximo();
            }
        }

        return $obErro;
    }

    /**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function consultar($boTransacao = "")
    {
        $obTEmpenhoPreEmpenhoDespesa       = new TEmpenhoPreEmpenhoDespesa;
        $obTEmpenhoPreEmpenho              = new TEmpenhoPreEmpenho;
        
        $obTEmpenhoPreEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho );
        $obTEmpenhoPreEmpenho->setDado( "exercicio"      , $this->stExercicio     );
        $obErro = $obTEmpenhoPreEmpenho->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->stDescricao  = $rsRecordSet->getCampo( "descricao" );
            $this->boImplantado = $rsRecordSet->getCampo( "implantado" );
            $this->obREmpenhoTipoEmpenho->setCodTipo    ( $rsRecordSet->getCampo( "cod_tipo" )          );
            $this->obREmpenhoHistorico->setCodHistorico ( $rsRecordSet->getCampo( "cod_historico" )     );
            $this->obRCGM->setNumCGM                    ( $rsRecordSet->getCampo( "cgm_beneficiario" )  );
            $obErro = $this->obRCGM->listar( $rsBeneficiario, '', $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRCGM->setNomCGM            ( $rsBeneficiario->getCampo('nom_cgm')      );
                $this->obRUsuario->obRCGM->setNumCGM( $rsRecordSet->getCampo( "cgm_usuario" )   );
                $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_pre_empenho", $this->inCodPreEmpenho    );
                $obTEmpenhoPreEmpenhoDespesa->setDado( "exercicio"      , $this->stExercicio        );
                $obErro = $obTEmpenhoPreEmpenhoDespesa->recuperaPorChave( $rsRecordSet, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obROrcamentoDespesa->setCodDespesa           ( $rsRecordSet->getCampo( "cod_despesa" )   );
                    $this->obROrcamentoDespesa->setExercicio            ( $this->stExercicio                        );
                    $this->obROrcamentoClassificacaoDespesa->setCodConta( $rsRecordSet->getCampo( "cod_conta" )     );
                    $obErro = $this->obREmpenhoTipoEmpenho->consultar( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obREmpenhoHistorico->setExercicio        ( $this->stExercicio                        );
                        $obErro = $this->obREmpenhoHistorico->consultar( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->obRCGM->consultar( $rsCGM, $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $obErro = $this->obRUsuario->consultar( $rsUsuario, $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    if ( !$obErro->ocorreu() and $this->boImplantado == 'f' ) {
                                        if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                                            $obErro = $this->obROrcamentoDespesa->listar( $rsDespesa, '' ,$boTransacao );
                                            if ( !$obErro->ocorreu() ) {
                                                $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setDescricao  ( $rsDespesa->getCampo("descricao")                                 );
                                                $this->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso              ( $rsDespesa->getCampo("cod_recurso")                               );
                                                $this->obROrcamentoDespesa->obROrcamentoRecurso->setMascRecurso             ( $rsDespesa->getCampo("masc_recurso")                              );
                                                $this->obROrcamentoDespesa->obROrcamentoRecurso->setNome                    ( $rsDespesa->getCampo("nom_recurso")                               );
                                                $this->obROrcamentoDespesa->setValorOriginal                                ( $rsDespesa->getCampo("vl_original")                               );
                                                $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsDespesa->getCampo("num_orgao")    );
                                                $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade( $rsDespesa->getCampo("num_unidade")                               );
                                                $this->obROrcamentoDespesa->setValorOriginal                                ( $rsDespesa->getCampo("vl_original")                               );
                                                $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto  ( $rsDespesa->getCampo('num_acao')                                  );
                                                $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->setExercicio      ( $this->stExercicio                                                );
                                                $obErro = $this->obROrcamentoClassificacaoDespesa->listar( $rsClassificacao, '', $boTransacao );
                                                if ( !$obErro->ocorreu() ) {
                                                    $this->obROrcamentoClassificacaoDespesa->setMascClassificacao   ( $rsClassificacao->getCampo("mascara_classificacao")   );
                                                    $this->obROrcamentoClassificacaoDespesa->setDescricao           ( $rsClassificacao->getCampo("descricao")               );
                                                    if ( !$obErro->ocorreu() ) {
                                                        $obErro = $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->consultarPorNumAcao( $rsPAO, $boTransacao );
                                                        if ( !$obErro->ocorreu() ) {
                                                            $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNome   ( $rsPAO->getCampo('nom_pao')               );
                                                            $this->obROrcamentoDespesa->obROrcamentoPrograma->setCodPrograma    ( $rsDespesa->getCampo( 'cod_programa' )    );
                                                            $this->obROrcamentoDespesa->obROrcamentoPrograma->setExercicio      ( $rsDespesa->getCampo( 'exercicio' )       );
                                                            $obErro = $this->obROrcamentoDespesa->obROrcamentoPrograma->consultar($rsPrograma, $boTransacao);
                                                            if ( !$obErro->ocorreu() ) {
                                                                $this->obROrcamentoDespesa->obROrcamentoPrograma->setDescricao  ( $rsPrograma->getCampo( 'descricao' )      );
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } elseif ( !$obErro->ocorreu() ) {
                                        include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoRestosPreEmpenho.class.php";
                                        $obTEmpenhoRestosPreEmpenho        = new TEmpenhoRestosPreEmpenho;
                                        //Restos
                                        $obTEmpenhoRestosPreEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho );
                                        $obTEmpenhoRestosPreEmpenho->setDado( "exercicio"      , $this->stExercicio     );
                                        $obErro = $obTEmpenhoRestosPreEmpenho->recuperaPorChave( $rsRestosPreEmpenho, $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            $this->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso              ( $rsRestosPreEmpenho->getCampo("recurso")                                  );
                                            $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsRestosPreEmpenho->getCampo("num_orgao")   );
                                            $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade( $rsRestosPreEmpenho->getCampo( 'num_unidade')                             );
                                            $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto  ( $rsRestosPreEmpenho->getCampo( 'num_pao' )                                );
                                            $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->setExercicio      ( $rsRestosPreEmpenho->getCampo( 'exercicio' )                              );
                                            $this->obROrcamentoClassificacaoDespesa->setCodEstrutural                   ( $rsRestosPreEmpenho->getCampo( 'cod_estrutural' )                         );
                                            $this->obROrcamentoClassificacaoDespesa->setExercicio                       ( $rsRestosPreEmpenho->getCampo( 'exercicio' )                              );
                                            $this->obROrcamentoDespesa->obROrcamentoPrograma->setCodPrograma            ( $rsRestosPreEmpenho->getCampo( 'cod_programa' )                           );
                                            $this->obROrcamentoDespesa->obROrcamentoPrograma->setExercicio              ( $rsRestosPreEmpenho->getCampo( 'exercicio' )                              );
                                            $obErro = $this->obROrcamentoClassificacaoDespesa->listar( $rsClassificacao, '', $boTransacao );
                                            if ( !$obErro->ocorreu() ) {
                                                $stMascaraClassificacao = $rsClassificacao->getCampo("mascara_classificacao");
                                                $stMascaraClassificacao = ($stMascaraClassificacao!='') ? $stMascaraClassificacao : $rsRestosPreEmpenho->getCampo( 'cod_estrutural' );
                                                $this->obROrcamentoClassificacaoDespesa->setMascClassificacao   ( $stMascaraClassificacao                       );
                                                $this->obROrcamentoClassificacaoDespesa->setDescricao           ( $rsClassificacao->getCampo("descricao")       );
                                                $obErro = $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->consultar( $rsPAO, $boTransacao );
                                                if ( !$obErro->ocorreu() ) {
                                                    $this->obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNome   ( $rsPAO->getCampo('nom_pao')           );
                                                    $obErro = $this->obROrcamentoDespesa->obROrcamentoPrograma->consultar($rsPrograma, $boTransacao);
                                                    if ( !$obErro->ocorreu() ) {
                                                        $this->obROrcamentoDespesa->obROrcamentoPrograma->setDescricao  ( $rsPrograma->getCampo( 'descricao' )  );   
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if ( !$obErro->ocorreu() ) {
                                    $obErro = $this->consultarItemPreEmpenho( $boTransacao );
                                }
                                if ( !$obErro->ocorreu() ) {
                                    $this->obREmpenhoPermissaoAutorizacao->setExercicio( $this->stExercicio );
                                    $obErro = $this->obREmpenhoPermissaoAutorizacao->consultar( $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;

        $stFiltro = "";
        if( $this->inCodPreEmpenho )
            $stFiltro .= " cod_pre_empenho = ".$this->inCodPreEmpenho."  AND ";
        if( $this->obREmpenhoTipoEmpenho->getCodTipo() )
            $stFiltro .= " cod_tipo = ".$this->obREmpenhoTipoEmpenho->getCodTipo()."  AND ";
        $inCodHistorico = $this->obREmpenhoHistorico->getCodHistorico();
        if( !empty($inCodHistorico) || $inCodHistorico === '0' )
            $stFiltro .= " cod_historico = ".$inCodHistorico."  AND ";
        if( $this->obRCGM->getNumCGM() )
            $stFiltro .= " cgm_beneficiado = ".$this->obRCGM->getNumCGM()."  AND ";
        if( $this->obRUsuario->obRCGM->getNumCGM() )
            $stFiltro .= " cgm_usuario = ".$this->obRUsuario->obRCGM->getNumCGM()."  AND ";
        if( $this->obROrcamentoDespesa->getCodDespesa() )
            $stFiltro .= " cod_despesa = ".$this->obROrcamentoDespesa->getCodDespesa()."  AND ";
        if($this->stExercicio)
            $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
        if( $this->stDescricao )
            $stFiltro .= " lower(descricao) like lower('%" . $this->stDescricao."%') AND ";
        $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
        $stOrder = ($stOrder) ? $stOrder : "cod_pre_empenho";
        $obErro = $obTEmpenhoPreEmpenho->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
    * Método para salvar a despesa e cod_conta em TPreEmpenhoDespesa
    * @access private
    * @param Object $obErro
    * @return Object obErro
    **/
    public function salvarDespesa($boTransacao = "")
    {
        $obTEmpenhoPreEmpenhoDespesa = new TEmpenhoPreEmpenhoDespesa;

        $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_pre_empenho", $this->inCodPreEmpenho );
        $obTEmpenhoPreEmpenhoDespesa->setDado( "exercicio"      , $this->stExercicio     );
        $obErro = $obTEmpenhoPreEmpenhoDespesa->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_despesa" , $this->obROrcamentoDespesa->getCodDespesa() );
            if ( $this->obROrcamentoClassificacaoDespesa->getCodConta() || $this->obROrcamentoClassificacaoDespesa->getCodConta() != '' ) {
                $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_conta" , $this->obROrcamentoClassificacaoDespesa->getCodConta() );
            } else if ( !$this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
                $obErro = $this->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsDespesa, '',$boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ( !$this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
                        $this->obROrcamentoClassificacaoDespesa->setCodConta( $rsDespesa->getCampo("cod_conta") );
                        $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_conta" , $this->obROrcamentoClassificacaoDespesa->getCodConta() );
                    }
                }
            } else {
                $obErro = $this->getCodConta( $inCodConta, $boTransacao );
                $this->obROrcamentoClassificacaoDespesa->setCodConta( $inCodConta );
                $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_conta" , $this->obROrcamentoClassificacaoDespesa->getCodConta() );
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $obTEmpenhoPreEmpenhoDespesa->inclusao( $boTransacao );
            }
        }

        return $obErro;
    }

    /**
    * Pega codigo da conta apartir do codigo da rubrica da despesa
    * @access private
    * @param Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function getCodConta(&$inCodConta, $boTransacao = "")
    {
        $inCodDespesa = $this->obROrcamentoDespesa->getCodDespesa();
        $this->obROrcamentoDespesa->setCodDespesa( "" );
        $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setDescricao( "" );
        $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() );
        $obErro = $this->obROrcamentoDespesa->listarCodEstruturalDespesa( $rsClassificacaoDespesa, '', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obROrcamentoDespesa->setCodDespesa( $inCodDespesa );
            $inCodConta = $rsClassificacaoDespesa->getCampo( "cod_conta" );
        }

        return $obErro;
    }

    /**
    * Incluir Pre Empenho
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function incluir($boTransacao = "")
    {
        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $obTEmpenhoPreEmpenho->setDado( "exercicio"       , $this->stExercicio );
            $obTEmpenhoPreEmpenho->proximoCod( $this->inCodPreEmpenho, $boTransacao );

            if( !$this->obREmpenhoHistorico->getCodHistorico() )
            $this->obREmpenhoHistorico->setCodHistorico( 0 );
            $obTEmpenhoPreEmpenho->setDado( "cod_pre_empenho" , $this->inCodPreEmpenho );
            $obTEmpenhoPreEmpenho->setDado( "cod_historico"   , $this->obREmpenhoHistorico->getCodHistorico() );
            $obTEmpenhoPreEmpenho->setDado( "cgm_beneficiario", $this->obRCGM->getNumCGM() );
            $obTEmpenhoPreEmpenho->setDado( "descricao"       , $this->stDescricao );
            $obTEmpenhoPreEmpenho->setDado( "cod_tipo"        , $this->obREmpenhoTipoEmpenho->getCodTipo() );
            $obTEmpenhoPreEmpenho->setDado( "cgm_usuario"     , $this->obRUsuario->obRCGM->getNumCGM() );

            $obErro = $obTEmpenhoPreEmpenho->inclusao( $boTransacao );
            
            if ( !$obErro->ocorreu() ) {
                if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                    $obErro = $this->salvarDespesa( $boTransacao );
                }
                
                if ( !$obErro->ocorreu() ) {
                    if ( sizeof( $this->arItemPreEmpenho ) > 0 ) {
                        $nuVlTotal = 0;
                        foreach ($this->arItemPreEmpenho as $obItemPreEmpenho) {
                            $obErro = $obItemPreEmpenho->incluir( $boTransacao);
                            $nuVlTotal += $obItemPreEmpenho->getValorTotal();
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                        if ( $this->obROrcamentoDespesa->getCodDespesa() and !$obErro->ocorreu() ) {

                            $this->consultaSaldoAnteriorDataEmpenho( $nuSaldoAnterior,'', $boTransacao );

                            $nuVlTotalReserva = number_format((float) $this->obROrcamentoReserva->getVlReserva(),2,'.',''); // usando number_format pois no if abaixo dava diferença. Foi identificado que, embora as variáveis
                            $nuSaldoAnterior = number_format((float) $nuSaldoAnterior,2,'.',''); // fossem demonstradas no var_dump como iguais, ficava oculto no ponto flutuante uma diferença
                            
                            if ($nuVlTotalReserva > $nuSaldoAnterior) {
                                $obErro->setDescricao( "Não há saldo disponível para esta dotação(".$this->obROrcamentoDespesa->getCodDespesa().")!" );
                            }
                            
                        }
                    } else {
                        $obErro->setDescricao( "É necessário cadastrar pelo menos um Item" );
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    if ($this->getTimestampAtributo()) {
                        $arChavePersistenteValores = array( "cod_pre_empenho" => $this->getCodPreEmpenho(),
                        "exercicio"       => $this->getExercicio(),
                        "timestamp"       => $this->getTimestampAtributo());
                    } else {
                        $arChavePersistenteValores = array( "cod_pre_empenho" => $this->getCodPreEmpenho(),
                        "exercicio"       => $this->getExercicio());
                    }
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
                    $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPreEmpenho );

        return $obErro;
    }

    /**
    * Alterar Pre Empenho
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function alterar($boTransacao = "")
    {
        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            if( !$this->obREmpenhoHistorico->getCodHistorico() )
            $this->obREmpenhoHistorico->setCodHistorico( 0 );
            $obTEmpenhoPreEmpenho->setDado( "exercicio"       , $this->stExercicio );
            $obTEmpenhoPreEmpenho->setDado( "cod_pre_empenho" , $this->inCodPreEmpenho );
            $obTEmpenhoPreEmpenho->setDado( "cod_historico"   , $this->obREmpenhoHistorico->getCodHistorico() );
            $obTEmpenhoPreEmpenho->setDado( "cgm_beneficiario", $this->obRCGM->getNumCGM() );
            $obTEmpenhoPreEmpenho->setDado( "descricao"       , $this->stDescricao );
            $obTEmpenhoPreEmpenho->setDado( "cod_tipo"        , $this->obREmpenhoTipoEmpenho->getCodTipo() );
            $obTEmpenhoPreEmpenho->setDado( "cgm_usuario"     , $this->obRUsuario->obRCGM->getNumCGM() );

            $obErro = $obTEmpenhoPreEmpenho->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                    $obErro = $this->salvarDespesa( $boTransacao );
                } else {
                    $obTEmpenhoPreEmpenhoDespesa       = new TEmpenhoPreEmpenhoDespesa;
                    $obTEmpenhoPreEmpenhoDespesa->setDado( "cod_pre_empenho", $this->inCodPreEmpenho );
                    $obTEmpenhoPreEmpenhoDespesa->setDado( "exercicio"      , $this->stExercicio     );
                    $obErro = $obTEmpenhoPreEmpenhoDespesa->exclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    if ( sizeof( $this->arItemPreEmpenho ) > 0 ) {
                        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenho.class.php';
                        $obTEmpenhoItemPreEmpenho = new TEmpenhoItemPreEmpenho;
                        $stFiltro  = ' WHERE cod_pre_empenho = '.$this->inCodPreEmpenho;
                        $stFiltro .= "   AND exercicio = '".$this->stExercicio."' ";
                        $obTEmpenhoItemPreEmpenho->recuperaTodos($rsItensPreEmpenho, $stFiltro, '', $boTransacao);

                        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenhoJulgamento.class.php';
                        $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento;
                        $stFiltro  = ' WHERE cod_pre_empenho = '.$this->inCodPreEmpenho;
                        $stFiltro .= "   AND exercicio = '".$this->stExercicio."' ";
                        $obTEmpenhoItemPreEmpenhoJulgamento->recuperaTodos($rsItensPreEmpenhoJulgamento, $stFiltro, '', $boTransacao);

                        if ($rsItensPreEmpenhoJulgamento->getNumLinhas() < 0) {
                            if ($rsItensPreEmpenho->getNumLinhas() > 0) {
                                while (!$rsItensPreEmpenho->eof()) {
                                    $obTEmpenhoItemPreEmpenho->setDado('cod_pre_empenho' , $rsItensPreEmpenho->getCampo('cod_pre_empenho'));
                                    $obTEmpenhoItemPreEmpenho->setDado('exercicio'       , $rsItensPreEmpenho->getCampo('exercicio'));
                                    $obTEmpenhoItemPreEmpenho->setDado('num_item'        , $rsItensPreEmpenho->getCampo('num_item'));

                                    $obErro = $obTEmpenhoItemPreEmpenho->exclusao($boTransacao);
                                    $rsItensPreEmpenho->proximo();
                                }
                            }

                            if (!$obErro->ocorreu()) {
                                foreach ($this->arItemPreEmpenho as $obItemPreEmpenho) {
                                    $obErro = $obItemPreEmpenho->incluir($boTransacao);
                                    if ($obErro->ocorreu()) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                if ($this->getTimestampAtributo()) {
                    $arChavePersistenteValores = array( "cod_pre_empenho" => $this->getCodPreEmpenho(),
                    "exercicio"       => $this->getExercicio(),
                    "timestamp"       => $this->getTimestampAtributo() );
                } else {
                    $arChavePersistenteValores = array( "cod_pre_empenho" => $this->getCodPreEmpenho(),
                    "exercicio"       => $this->getExercicio());
                }
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPreEmpenho );

        return $obErro;
    }

    /**
    * Incluir Pre Empenho
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function incluirItemEmpenhoDespesaFixa($boTransacao = "")
    {
        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoItemEmpenhoDespesasFixas.class.php");
        $obTEmpenhoPreEmpenho              = new TEmpenhoPreEmpenho;
        $obTEmpenhoItemEmpenhoDespesasFixas = new TEmpenhoItemEmpenhoDespesasFixas;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $obTEmpenhoPreEmpenho->setDado( "exercicio"       , $this->stExercicio );
            $obTEmpenhoPreEmpenho->proximoCod( $this->inCodPreEmpenho, $boTransacao );
            $this->obROrcamentoClassificacaoDespesa->setCodConta( $inCodConta );

            if( !$this->obREmpenhoHistorico->getCodHistorico() )
            $this->obREmpenhoHistorico->setCodHistorico( 0 );
            $obTEmpenhoPreEmpenho->setDado( "cod_pre_empenho" , $this->inCodPreEmpenho );
            $obTEmpenhoPreEmpenho->setDado( "cod_historico"   , $this->obREmpenhoHistorico->getCodHistorico() );
            $obTEmpenhoPreEmpenho->setDado( "cgm_beneficiario", $this->obRCGM->getNumCGM() );
            $obTEmpenhoPreEmpenho->setDado( "descricao"       , $this->stDescricao );
            $obTEmpenhoPreEmpenho->setDado( "cod_tipo"        , $this->obREmpenhoTipoEmpenho->getCodTipo() );
            $obTEmpenhoPreEmpenho->setDado( "cgm_usuario"     , $this->obRUsuario->obRCGM->getNumCGM() );

            $obErro = $obTEmpenhoPreEmpenho->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                    $obErro = $this->salvarDespesa( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    if ( sizeof( $this->arItemPreEmpenho ) > 0 ) {
                        $nuVlTotal = 0;
                        foreach ($this->arItemPreEmpenho as $obItemPreEmpenho) {
                            $obErro = $obItemPreEmpenho->incluir( $boTransacao );
                            $nuVlTotal += $obItemPreEmpenho->getValorTotal();
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'num_item',$obItemPreEmpenho->getNumItem() );
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'cod_pre_empenho', $this->inCodPreEmpenho );
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'exercicio', $this->stExercicio );
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'cod_despesa', $this->obROrcamentoDespesa->getCodDespesa() );
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'consumo', $obItemPreEmpenho->getQuantidade() );
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'dt_documento', $obItemPreEmpenho->getDataDocumento() );
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
                            $obTEmpenhoItemEmpenhoDespesasFixas->setDado( 'cod_despesa_fixa', $this->getCodDespesaFixa() );
                            $obTEmpenhoItemEmpenhoDespesasFixas->inclusao($boTransacao);
                        }
                        if ( $this->obROrcamentoDespesa->getCodDespesa() and !$obErro->ocorreu() ) {
                            
                            $this->consultaSaldoAnteriorDataEmpenho( $nuSaldoAnterior,'', $boTransacao );
                            $nuVlTotal = number_format((float) $nuVlTotal,2,'.',''); // usando number_format pois no if abaixo dava diferença. Foi identificado que, embora as variáveis
                            $nuSaldoAnterior = number_format((float) $nuSaldoAnterior,2,'.',''); // fossem demonstradas no var_dump como iguais, ficava oculto no ponto flutuante uma diferença
                            if ($nuVlTotal > $nuSaldoAnterior) {
                                $obErro->setDescricao( "Não há saldo disponível para esta dotação(".$this->obROrcamentoDespesa->getCodDespesa().")!" );
                            }

                        }
                    } else {
                        $obErro->setDescricao( "É necessário cadastrar pelo menos um Item" );
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    if ($this->getTimestampAtributo()) {
                        $arChavePersistenteValores = array( "cod_pre_empenho" => $this->getCodPreEmpenho(),
                        "exercicio"       => $this->getExercicio(),
                        "timestamp"       => $this->getTimestampAtributo());
                    } else {
                        $arChavePersistenteValores = array( "cod_pre_empenho" => $this->getCodPreEmpenho(),
                        "exercicio"       => $this->getExercicio());
                    }
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
                    $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPreEmpenho );

        return $obErro;
    }

}
