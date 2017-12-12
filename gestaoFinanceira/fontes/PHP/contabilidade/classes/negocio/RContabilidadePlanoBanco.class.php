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
    * Classe de Regra de Plano Banco
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RContabilidadePlanoBanco.class.php 61444 2015-01-16 17:32:17Z franver $

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.02.02,uc-02.04.05,uc-02.04.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_MON_NEGOCIO.'RMONAgencia.class.php';
include_once CAM_GT_MON_NEGOCIO.'RMONBanco.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaAnalitica.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoRecurso.class.php';

/**
    * Classe de Regra de Plano Banco
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RContabilidadePlanoBanco extends RContabilidadePlanoContaAnalitica
{
/**
    * @access Private
    * @var Object
*/
var $obRMONAgencia;
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoRecurso;
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoEntidade;
/**
    * @access Private
    * @var Object
*/
var $obRMONBanco;
/**
    * @access Private
    * @var String
*/
var $stContaCorrente;
/**
    * @access Private
    * @var String
*/
var $stCodEntidade;
/**
    * @access Private
    * @var String
*/
var $inCodConta;
/**
    * @access Private
    * @var Integer
*/
var $inContaCorrente;

/**
    * @access Private
    * @var Integer
*/
var $inNumAgencia;

/**
    * @access Private
    * @var Integer
*/
var $inNumBanco;

/**
    * @access Private
    * @var Integer
*/
var $inTipoContaTCEPE;


/**
    * @access Public
    * @param Object $Valor
*/
function setRMONAgencia($valor) { $this->obRMONAgencia = $valor;     }
/**
    * @access Public
    * @param Object $Valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor;     }
/**
    * @access Public
    * @param Object $Valor
*/
function setRMONBanco($valor) { $this->obRMONBanco = $valor;     }
/**
    * @access Public
    * @param String $Valor
*/
function setContaCorrente($valor) { $this->stContaCorrente  = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setNumAgencia($valor){ $this->inNumAgencia = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setNumBanco($valor){ $this->inNumBanco = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setCodConta($valor) { $this->inCodConta  = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setCodigoEntidade($valor) { $this->stCodEntidade  = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodContaCorrente($valor) { $this->inContaCorrente  = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setTipoContaTCEPE($valor) { $this->inTipoContaTCEPE  = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setFiltroEncerrado($valor) { $this->filtroEncerrado  = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRMONAgencia() { return $this->obRMONAgencia; }
/**
    * @access Public
    * @return Object
*/
function getRMONBanco() { return $this->obRMONBanco; }
/**
    * @access Public
    * @return String
*/
function getContaCorrente() { return $this->stContaCorrente; }
/**
    * @access Public
    * @return String
*/
function getCodigoEntidade() { return $this->stCodEntidade; }
/**
    * @access Public
    * @return Integer
*/
function getCodContaCorrente() { return $this->inContaCorrente; }
/**
    * @access Public
    * @return Integer
*/
function getTipoContaTCEPE() { return $this->inTipoContaTCEPE; }
/**
    * @access Public
    * @return Integer
*/
function getFiltroEncerrado() { return $this->filtroEncerrado; }

/**
     * Método construtor
     * @access Public
*/
function RContabilidadePlanoBanco()
{
    parent::RContabilidadePlanoContaAnalitica();
    $this->obRMONAgencia         = new RMONAgencia;
    $this->obRMONBanco           = new RMONBanco;
    $this->obROrcamentoEntidade  = new ROrcamentoEntidade;
    $this->obROrcamentoRecurso   = new ROrcamentoRecurso;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $stFiltro = "";
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"       );
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;

    $obErro = parent::consultar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ($this->inCodPlano) {
            $obTContabilidadePlanoBanco->setDado( "cod_plano", $this->inCodPlano  );
            $obTContabilidadePlanoBanco->setDado( "exercicio", $this->stExercicio );
            if ( $this->obROrcamentoEntidade->getCodigoEntidade()) {
                $obTContabilidadePlanoBanco->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
            }
            $obErro = $obTContabilidadePlanoBanco->recuperaContaBanco( $rsRecordSet, $stFiltro, $boTransacao );

            if ( !$obErro->ocorreu() and  $rsRecordSet->getCampo( "conta_corrente" )) {
                $this->stContaCorrente = $rsRecordSet->getCampo( "conta_corrente" );
                $this->obRMONBanco->setCodBanco( $rsRecordSet->getCampo( "cod_banco" ) );
                $this->obRMONAgencia->setCodAgencia( $rsRecordSet->getCampo( "cod_agencia" ) );
                $this->obRMONAgencia->obRMONBanco->setCodBanco( $rsRecordSet->getCampo( "cod_banco" ) );
                $obErro = $this->obRMONAgencia->consultarAgencia( $rsAgencia, $boTransacao );
                if ( !$obErro->ocorreu() and  $rsRecordSet->getCampo( "cod_entidade")) {
                    $this->obROrcamentoEntidade->setExercicio( $this->stExercicio );
                    $this->obROrcamentoEntidade->setCodigoEntidade ( $rsRecordSet->getCampo( "cod_entidade" ) );
                    $this->obROrcamentoEntidade->consultarNomes($rsDummy, $boTransacao );
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
*/
function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"       );
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;

    if($this->inCodPlano)
        $stFiltro  = " cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stContaCorrente)
        $stFiltro .= " conta_corrente = '" . $this->stContaCorrente . "' AND ";
    if($this->stCodEntidade)
        $stFiltro .= " cod_entidade = " . $this->stCodEntidade . " AND ";
    if($this->obRMONBanco->getCodBanco())
        $stFiltro .= " cod_banco = " . $this->obRMONBanco->getCodBanco() . " AND ";
    if($this->obRMONAgencia->getCodAgencia())
        $stFiltro .= " cod_agencia = " . $this->obRMONAgencia->getCodAgencia() . " AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTContabilidadePlanoBanco->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um montaRelacionamento na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoContaPagamento(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stNomConta)
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND ";
    if( $this->stCodEstrutural )
        $stFiltro .= " pc.cod_estrutural like publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%' AND ";
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " CASE WHEN pb.cod_plano IS NOT NULL                                                      \n";
        $stFiltro .= "   THEN CASE WHEN pb.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." \n";
        $stFiltro .= "               THEN true                                                                 \n";
        $stFiltro .= "               ELSE false                                                                \n";
        $stFiltro .= "        END                                                                              \n";
        $stFiltro .= "   ELSE true                                                                             \n";
        $stFiltro .= " END AND \n";
    }
    $stFiltro .= "  pb.cod_banco is not null AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaSaldoContaBanco na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarSaldoBanco(&$nuVlSaldoContaBanco, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"       );
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;

    $obTContabilidadePlanoBanco->setDado( 'cod_plano', $this->inCodPlano  );
    $obTContabilidadePlanoBanco->setDado( 'exercicio', $this->stExercicio );
    $obErro = $obTContabilidadePlanoBanco->recuperaSaldoContaBanco( $rsRecordSet, '', $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $nuVlSaldoContaBanco = $rsRecordSet->getCampo( 'vl_saldo' );
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
*/
function listarContasBancos(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"       );
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;

    $stFiltro = "";
    if( $this->stExercicio )
        $stFiltro .= " pc.exercicio = '".$this->stExercicio."' AND ";
    if( $this->getCodPlano() )
        $stFiltro .= " pb.cod_plano = ".$this->getCodPlano()." AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " pb.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().") AND ";
    if( $this->getCodEstrutural() )
        $stFiltro .= " pc.cod_estrutural = '".$this->getCodEstrutural()."' AND ";
    if( $this->obRMONBanco->getNomBanco() )
        $stFiltro .= " UPPER(pc.nom_conta)  LIKE UPPER('%".$this->obRMONBanco->getNomBanco()."%') AND ";
    $stFiltro = ( $stFiltro ) ? " AND ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTContabilidadePlanoBanco->recuperaBancoDescricao( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarContasBancosAConciliar(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"       );
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;

    $stFiltro = "";
    if( $this->stExercicio )
        $stFiltro .= " pc.exercicio = '".$this->stExercicio."' AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " pb.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";
    if ( $this->getCodPlanoInicial()) {
        $stFiltro  .= " pb.cod_plano >= ".$this->getCodPlanoInicial() . " AND ";
    }
    if ( $this->getCodPlanoFinal()) {
        $stFiltro  .= " pb.cod_plano <= ".$this->getCodPlanoFinal() . " AND ";
    }
    $stFiltro = ( $stFiltro ) ? " AND ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTContabilidadePlanoBanco->recuperaBancoConciliacao( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

/**
    * Executa um montaRelacionamento na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoConta(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"       );
    $obTContabilidadePlanoAnalitica   = new TContabilidadePlanoAnalitica;

    if($this->stCodEntidade)
        $obTContabilidadePlanoAnalitica->setDado("cod_entidade",$this->stCodEntidade);
    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stCodEstrutural)
        $stFiltro .= " publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if($this->stNomConta)
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND "; $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";

    $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um montaRelacionamentoContaEntidade na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoContaEntidade(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"       );
    $obTContabilidadePlanoAnalitica   = new TContabilidadePlanoAnalitica;

    if($this->stCodEntidade)
        $stFiltro  .= " pb.cod_entidade IN ( ".$this->stCodEntidade.") AND ";
    if($this->inCodPlano)
        $stFiltro .= " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if ($this->stExercicio) {
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
        $obTContabilidadePlanoAnalitica->setDado('exercicio', $this->stExercicio );
    }
    if ($this->obROrcamentoRecurso->getCodRecurso())
    {
        $stFiltro .= " pr.cod_recurso = " . (int)$this->obROrcamentoRecurso->getCodRecurso() . " AND ";
    }
    
    // Quando ação de encerrar não traz na lista os já encerrados
    if ($this->filtroEncerrado == "encerrar"){
        $stFiltro .= " plano_conta_encerrada.cod_conta IS NULL AND";
    } elseif ($this->filtroEncerrado == "excluir"){
        $stFiltro .= " plano_conta_encerrada.cod_conta IS NOT NULL AND";
    }
    
    if ($this->inNumBanco)
        $stFiltro .= " mb.num_banco like '" . $this->inNumBanco . "' AND ";
    if ($this->inNumAgencia) 
        $stFiltro .= " ma.num_agencia like '" . $this->inNumAgencia . "' AND ";
    if ($this->stContaCorrente) 
        $stFiltro .= " pb.conta_corrente like '" . $this->stContaCorrente . "' AND ";
    if($this->stCodEstrutural)
        $stFiltro .= " publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if($this->stNomConta)
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND ";
        
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    
    $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamentoContaEntidade( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Salva dados do Plano de Conta Analitica no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"       );
    include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEPlanoBancoTipoConta.class.php'    );
    
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;
    $obTTCEPEPlanoBancoTipoConta  = new TTCEPEPlanoBancoTipoConta();

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::salvar( $boTransacao );
        
        if ( !$obErro->ocorreu()){
            //Setar plano em TCE-PE para exclusão, se existir
            if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
                $obTTCEPEPlanoBancoTipoConta->setDado( "cod_plano", $this->inCodPlano );
                $obTTCEPEPlanoBancoTipoConta->setDado( "exercicio", $this->stExercicio);
                $obErro = $obTTCEPEPlanoBancoTipoConta->exclusao( $boTransacao );
            }
            
            if ( !$obErro->ocorreu() and $this->stContaCorrente ) {
                if ($this->boContaAnalitica) {
                        $obTContabilidadePlanoBanco->setDado( "cod_plano"      , $this->inCodPlano                              );
                        $obTContabilidadePlanoBanco->setDado( "exercicio"      , $this->stExercicio                             );
                        $obTContabilidadePlanoBanco->setDado( "conta_corrente" , $this->stContaCorrente                         );
                        $obTContabilidadePlanoBanco->setDado( "cod_banco"      , $this->obRMONBanco->getCodBanco()     );
                        $obTContabilidadePlanoBanco->setDado( "cod_agencia"    , $this->obRMONAgencia->getCodAgencia() );
                        $obTContabilidadePlanoBanco->setDado( "cod_entidade"   , $this->obROrcamentoEntidade->getCodigoEntidade()     );
                        $obTContabilidadePlanoBanco->setDado( "cod_conta_corrente" , $this->inContaCorrente );
                        $obTContabilidadePlanoBanco->recuperaPorChave( $rsPlanoContaAnalitica, $boTransacao );
                        if( !$rsPlanoContaAnalitica->eof() )
                            $obErro = $obTContabilidadePlanoBanco->alteracao( $boTransacao );
                        else {
                            $obErro = $obTContabilidadePlanoBanco->inclusao( $boTransacao  );
                        }
                        
                        //Verificar existência de configuração de plano para TCE-PE #22239
                        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
                            if (!$obErro->ocorreu() && $this->getTipoContaTCEPE()) {
                                $obTTCEPEPlanoBancoTipoConta->setDado( "cod_plano"              , $this->inCodPlano         );
                                $obTTCEPEPlanoBancoTipoConta->setDado( "exercicio"              , $this->stExercicio        );
                                $obTTCEPEPlanoBancoTipoConta->setDado( "cod_tipo_conta_banco"   , $this->getTipoContaTCEPE());
                                
                                $obErro = $obTTCEPEPlanoBancoTipoConta->inclusao( $boTransacao  );                      
                            }
                        }
                } else $obErro->setDescricao("Apenas contas analíticas podem ser de banco");
            } elseif ( !$obErro->ocorreu() and $this->inCodPlano ) {
                $obTContabilidadePlanoBanco->setDado( "cod_plano", $this->inCodPlano  );
                $obTContabilidadePlanoBanco->setDado( "exercicio", $this->stExercicio );
    
                // verificação de consistência na tabela saldo_tesouraria para ajuste de mensagem de erro conforme bug #9745
                include_once(CAM_GF_TES_MAPEAMENTO."TTesourariaSaldoTesouraria.class.php");
                $obTTesourariaSaldoTesouraria = new TTesourariaSaldoTesouraria();
                $obTTesourariaSaldoTesouraria->setDado( 'cod_plano', $this->inCodPlano  );
                $obTTesourariaSaldoTesouraria->setDado( 'exercicio', Sessao::getExercicio() );
                $obErro = $obTTesourariaSaldoTesouraria->recuperaPorChave( $rsSaldo, $boTransacao );
    
                if ( $rsSaldo->getNumLinhas() > 0 ) {
                    $obErro->setDescricao("Conta ".$this->inCodPlano." já possui movimentação.");
                }
    
                if ( !$obErro->ocorreu() ) {
                    $obErro = $obTContabilidadePlanoBanco->exclusao( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Exclui dados do Plano Banco do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"          );
    include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEPlanoBancoTipoConta.class.php'       );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaEncerrada.class.php" );
    
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;
    $obTTCEPEPlanoBancoTipoConta  = new TTCEPEPlanoBancoTipoConta();

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        
        $obTContabilidadePlanoContaEncerrada = new TContabilidadePlanoContaEncerrada();
        $obTContabilidadePlanoContaEncerrada->setDado('cod_conta'      , $this->inCodConta  );
        $obTContabilidadePlanoContaEncerrada->setDado('exercicio'      , $this->stExercicio );
        $obErro = $obTContabilidadePlanoContaEncerrada->exclusao($boTransacao);
                
        if(!$obErro->ocorreu()){
            if ($this->inCodPlano) {
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
                    $obTTCEPEPlanoBancoTipoConta->setDado( "cod_plano", $this->inCodPlano );
                    $obTTCEPEPlanoBancoTipoConta->setDado( "exercicio", $this->stExercicio);
                    $obErro = $obTTCEPEPlanoBancoTipoConta->exclusao( $boTransacao );
                }
                
                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadePlanoBanco->setDado( "cod_plano", $this->inCodPlano );
                    $obTContabilidadePlanoBanco->setDado( "exercicio", $this->stExercicio  );
                    $obErro = $obTContabilidadePlanoBanco->exclusao( $boTransacao );
                }
            }
        }
        
        if( !$obErro->ocorreu() ){
            $obErro = parent::excluir( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Busca o próximo código estrutural do recurso
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function getProximoEstruturalRecurso(&$rsRecordSet, $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;

    $obTContabilidadePlanoBanco->setDado('cod_estrutural', $this->stCodEstrutural);

    $obErro = $obTContabilidadePlanoBanco->getProximoEstruturalRecurso($rsRecordSet, $boTransacao);

    return $obErro;
}

/**
    * Conta quantas contas contábeis do recurso tipo credor existem
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function countContasContabeisRecursoCredor(&$rsRecordSet, $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;

    $obTContabilidadePlanoBanco->setDado('exercicio', Sessao::getExercicio());

    $obErro = $obTContabilidadePlanoBanco->countContasContabeisRecursoCredor($rsRecordSet, $boTransacao);

    return $obErro;
}

/**
    * Conta quantas contas contábeis do recurso tipo devedor existem
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function countContasContabeisRecursoDevedor(&$rsRecordSet, $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;

    $obTContabilidadePlanoBanco->setDado('exercicio', Sessao::getExercicio());

    $obErro = $obTContabilidadePlanoBanco->countContasContabeisRecursoDevedor($rsRecordSet, $boTransacao);

    return $obErro;
}

/**
    * Busca as Contas Tipo devedor e credor de acordo com o recurso
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function getContasRecurso(&$rsRecordSet, $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;

    $obTContabilidadePlanoBanco->setDado('exercicio', Sessao::getExercicio());
    $obTContabilidadePlanoBanco->setDado('cod_recurso', $this->obROrcamentoRecurso->getCodRecurso());

    $obErro = $obTContabilidadePlanoBanco->getContasRecurso($rsRecordSet, $boTransacao);

    return $obErro;
}

/**
    * Busca as Contas Tipo devedor e credor de acordo com o recurso
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function getContasRecursoPagamentoTCEMS(&$rsRecordSet, $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;

    $obTContabilidadePlanoBanco->setDado('exercicio', Sessao::getExercicio());
    $obTContabilidadePlanoBanco->setDado('cod_recurso', $this->obROrcamentoRecurso->getCodRecurso());

    $obErro = $obTContabilidadePlanoBanco->getContasRecursoPagamentoTCEMS($rsRecordSet, $boTransacao);

    return $obErro;
}

/**
    * Busca recurso
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function getRecursoVinculoConta(&$rsRecordSet, $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;

    $obTContabilidadePlanoBanco->setDado('exercicio', Sessao::getExercicio());
    $obTContabilidadePlanoBanco->setDado('cod_plano', $this->getCodPlano());

    $obErro = $obTContabilidadePlanoBanco->getRecursoVinculoConta($rsRecordSet, $boTransacao);

    return $obErro;
}

}
