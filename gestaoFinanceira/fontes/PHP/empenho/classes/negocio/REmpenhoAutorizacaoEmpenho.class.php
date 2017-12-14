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
    * Classe de Regra de Tipo de Empenho
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoAutorizacaoEmpenho.class.php 65141 2016-04-27 20:10:02Z evandro $

    * Casos de uso: uc-02.03.02
                    uc-02.03.03
                    uc-02.03.18
                    uc-02.03.15
                    uc-02.01.23
                    uc-02.03.15
                    uc-02.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPreEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPermissaoAutorizacao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReserva.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReservaSaldos.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoContrapartidaAutorizacao.class.php";

/**
    * Classe de Regra de Tipo de Empenho
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class REmpenhoAutorizacaoEmpenho extends REmpenhoPreEmpenho
{
/**
    * @access Private
    * @var Object
*/
var $obTEmpenhoAutorizacaoReserva;
/**
    * @access Private
    * @var Object
*/
var $obTEmpenhoAutorizacaoAnulada;
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoEntidade;
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoReserva;
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoReservaSaldos;
/**
    * @access Private
    * @var Object
*/
var $obTEmpenhoContrapartidaAutorizacao;

/**
    * @access Private
    * @var Integer
*/
var $inCodAutorizacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodAutorizacaoInicial;
/**
    * @access Private
    * @var Integer
*/
var $inCodAutorizacaoFinal;
/**
    * @access Private
    * @var String
*/
var $stDtAutorizacao;
/**
    * @access Private
    * @var String
*/
var $stDtAutorizacaoInicial;
/**
    * @access Private
    * @var String
*/
var $stDtAutorizacaoFinal;
/**
    * @access Private
    * @var String
*/
var $stDtAnulacao;
/**
    * @access Private
    * @var String
*/
var $stMotivoAnulacao;
/**
    * @access Private
    * @var String
*/
var $stNumLicitacao;
/**
    * @access Private
    * @var String
*/
var $stDescricaoLicitacao;
/**
    * @access Private
    * @var String
*/
var $stTipoLicitacao;
/**
    * @access Private
    * @var Boolean
*/
var $boAlterar;
/**
    * @access Private
    * @var Boolean
*/
var $boAnuladaTotal;
/**
    * @var Integer
    * @access Private
*/
var $inSituacao;
/**
    * @var Timestamp
    * @access Private
*/
var $stHora;
/**
    * @var Timestamp
    * @access Private
*/
var $boAutViaPatrimonial;

/**
    * @var Integer
    * @access Private
*/
var $inCodCategoria;

/**
    * @var String
    * @access Private
*/
var $stNomCategoria;

/**
    * @var Boolean
    * @access Private
*/
var $boModuloEmpenho;

/**
    * @var Boolean
    * @access Private
*/
var $boEmpenhoCompraLicitacao;

/**
    * @var Integer
    * @access Private
*/
var $inCodModalidadeCompra;

/**
    * @var Integer
    * @access Private
*/
var $inCompraInicial;

/**
    * @var Integer
    * @access Private
*/
var $inCompraFinal;

/**
    * @var Integer
    * @access Private
*/
var $inCodModalidadeLicitacao;

/**
    * @var Integer
    * @access Private
*/
var $inLicitacaoInicial;

/**
    * @var Integer
    * @access Private
*/
var $inLicitacaoFinal;

/**
    * @var Integer
    * @access Private
*/
var $inCentroCusto;


/**
    * @access Public
    * @param Object $Valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setROrcamentoReserva($valor) { $this->obROrcamentoReserva = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setROrcamentoReservaSaldos($valor) { $this->obROrcamentoReservaSaldos = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodAutorizacao($valor) { $this->inCodAutorizacao = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodAutorizacaoInicial($valor) { $this->inCodAutorizacaoInicial = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodAutorizacaoFinal($valor) { $this->inCodAutorizacaoFinal = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtAutorizacao($valor) { $this->stDtAutorizacao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtAutorizacaoInicial($valor) { $this->stDtAutorizacaoInicial = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtAutorizacaoFinal($valor) { $this->stDtAutorizacaoFinal = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtAnulacao($valor) { $this->stDtAnulacao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setMotivoAnulacao($valor) { $this->stMotivoAnulacao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNumLicitacao($valor) { $this->stNumLicitacao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricaoLicitacao($valor) { $this->stDescricaoLicitacao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTipoLicitacao($valor) { $this->stTipoLicitacao = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setAlterar($valor) { $this->boAlterar = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setAnuladaTotal($valor) { $this->boAnuladaTotal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setSituacao($valor) { $this->inSituacao = $valor; }
/**
    * @access Public
    * @param Timestamp $valor
*/
function setHora($valor) { $this->stHora = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodCategoria($valor) { $this->inCodCategoria = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomCategoria($valor) { $this->stNomCategoria = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setBoModuloEmpenho($valor) { $this->boModuloEmpenho = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setBoEmpenhoCompraLicitacao($valor) { $this->boEmpenhoCompraLicitacao = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodModalidadeCompra($valor) { $this->inCodModalidadeCompra = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCompraInicial($valor) { $this->inCompraInicial = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCompraFinal($valor) { $this->inCompraFinal = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodModalidadeLicitacao($valor) { $this->inCodModalidadeLicitacao = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setLicitacaoInicial($valor) { $this->inLicitacaoInicial = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setLicitacaoFinal($valor) { $this->inLicitacaoFinal = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCentroCusto($valor) { $this->inCentroCusto = $valor; }

/**
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade; }
/**
    * @access Public
    * @return Integer
*/
function getCodAutorizacao() { return $this->inCodAutorizacao; }
/**
    * @access Public
    * @return Integer
*/
function getCodAutorizacaoInicial() { return $this->inCodAutorizacaoInicial; }
/**
    * @access Public
    * @return Integer
*/
function getCodAutorizacaoFinal() { return $this->inCodAutorizacaoFinal; }
/**
    * @access Public
    * @return Object
*/
function getROrcamentoReserva() { return $this->obROrcamentoReserva; }
/**
    * @access Public
    * @return Object
*/
function getROrcamentoReservaSaldos() { return $this->obROrcamentoReservaSaldos; }
/**
    * @access Public
    * @return String
*/
function getDtAutorizacao() { return $this->stDtAutorizacao; }
/**
    * @access Public
    * @return String
*/
function getDtAutorizacaoInicial() { return $this->stDtAutorizacaoInicial; }
/**
    * @access Public
    * @return String
*/
function getDtAutorizacaoFinal() { return $this->stDtAutorizacaoFinal; }
/**
    * @access Public
    * @return String
*/
function getDtAnulacao() { return $this->stDtAnulacao; }
/**
    * @access Public
    * @return String
*/
function getMotivoAnulacao() { return $this->stMotivoAnulacao; }
/**
    * @access Public
    * @return String
*/
function getNumLicitacao() { return $this->stNumLicitacao; }
/**
    * @access Public
    * @return String
*/
function getDescricaoLicitacao() { return $this->stDescricaoLicitacao; }
/**
    * @access Public
    * @return String
*/
function getTipoLicitacao() { return $this->stTipoLicitacao; }
/**
    * @access Public
    * @return Boolean
*/
function getAlterar() { return $this->boAlterar; }
/**
    * @access Public
    * @return Boolean
*/
function getAnuladaTotal() { return $this->boAnuladaTotal; }
/**
    * @access Public
    * @return Integer
*/
function getSituacao() { return $this->inSituacao; }
/**
    * @access Public
    * @return Timestamp
*/
function getHora() { return $this->stHora; }

/**
    * @access Public
    * @return Integer
*/
function getCodCategoria() { return $this->inCodCategoria; }

/**
    * @access Public
    * @return String
*/
function getNomCategoria() { return $this->stNomCategoria; }

/**
    * @access Public
    * @return String
*/
function getBoModuloEmpenho() { return $this->boModuloEmpenho; }

/**
    * @access Public
    * @return Boolean
*/
function getBoEmpenhoCompraLicitacao() { return $this->boEmpenhoCompraLicitacao; }


/**
    * @access Public
    * @return Integer
*/
function getCodModalidadeCompra() { return $this->inCodModalidadeCompra; }

/**
    * @access Public
    * @return Integer
*/
function getCompraInicial() { return $this->inCompraInicial; }

/**
    * @access Public
    * @return Integer
*/
function getCompraFinal() { return $this->inCompraFinal; }

/**
    * @access Public
    * @return Integer
*/
function getCodModalidadeLicitacao() { return $this->inCodModalidadeLicitacao; }

/**
    * @access Public
    * @return Integer
*/
function getLicitacaoInicial() { return $this->inLicitacaoInicial; }

/**
    * @access Public
    * @return Integer
*/
function getLicitacaoFinal() { return $this->inLicitacaoFinal; }


/**
     * Método construtor
     * @access Public
*/
function REmpenhoAutorizacaoEmpenho()
{
    parent::REmpenhoPreEmpenho();
    $this->obROrcamentoEntidade         = new ROrcamentoEntidade;
    $this->obROrcamentoReserva          = new ROrcamentoReserva;
    $this->obROrcamentoReservaSaldos    = new ROrcamentoReservaSaldos;
    $this->obTEmpenhoContrapartidaAutorizacao = new TEmpenhoContrapartidaAutorizacao;
}

/**
    * Pega mascara reduzida apartir do codigo da conta
    * @access private
    * @param Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function getMascaraDespesaReduzida($boTransacao = "")
{
    $this->obROrcamentoDespesa->consultar( $rsDespesa, $boTransacao );
    $inCodConta = $rsDespesa->getCampo( "cod_conta" );
    $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodConta( $inCodConta );
    $obErro = $this->obROrcamentoDespesa->listarContaDespesa( $rsClassificacaoDespesa , $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arMascara = explode('.',$rsClassificacaoDespesa->getCampo( "cod_estrutural" ));
        $inCount = sizeof( $arMascara )-1;
        for ($inCount ; $inCount >= 0; $inCount--) {
            if ($arMascara[$inCount] != 0) {
                $inPosicao = $inCount+1;
                break;
            }
        }
        if ( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
            $arClassificacao = explode('.', $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() );
            for ($inSize = 0; $inSize < $inPosicao; $inSize++) {
                if ($arClassificacao[$inSize] != $arMascara[$inSize]) {
                    $obErro->setDescricao( "Este código de classificacao não corresponde a despesa selecionada" );
                    break;
                }
            }
        }
    }

    return $obErro;
}

/**
    * Checa se usuário tem permissão
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function checarPermissaoAutorizacao($boTransacao = "")
{
    $obREmpenhoPermissaoAutorizacao = new REmpenhoPermissaoAutorizacao;
    $obREmpenhoPermissaoAutorizacao->setExercicio( $this->stExercicio );
    $obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade( $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() );
    $obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
    $obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM( $this->obRUsuario->obRCGM->getNumCGM() );
    $obErro = $obREmpenhoPermissaoAutorizacao->listar( $rsPermissao, '',$boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsPermissao->eof() ) {
            $obErro->setDescricao( "Este usuário não tem premissão para realizar esta operação" );
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
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoAnulada.class.php"  );
    include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenhoJulgamento.class.php';
    $obTEmpenhoAutorizacaoAnulada = new TEmpenhoAutorizacaoAnulada;
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento;

       $stFiltro  = " WHERE cod_pre_empenho = ".$this->inCodPreEmpenho;
       $stFiltro .= "   AND exercicio = '".$this->stExercicio."'";
       $obTEmpenhoItemPreEmpenhoJulgamento->recuperaTodos($rsPreEmpenhoItemJulgamento, $stFiltro, '', $boTransacao);

       // Verifica se a autorização foi emitida no módulo empenho ou foi emitida pela gestão patrimonial (módulo compra direta ou licitacao)
       if ($rsPreEmpenhoItemJulgamento->getNumLinhas() > -1) {
           $this->boModuloEmpenho = true;
       } else {
           $this->boModuloEmpenho = false;
       }

    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho                           );
    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_autorizacao", $this->inCodAutorizacao                          );
    $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio"      , $this->stExercicio                               );
    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_entidade"   , $this->obROrcamentoEntidade->getCodigoEntidade() );
    $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaPorChave( $rsRecordSet, $boTransacao );
    
    if ( !$obErro->ocorreu() ) {
        $this->stDtAutorizacao = $rsRecordSet->getCampo( "dt_autorizacao" );
        $this->stHora          = $rsRecordSet->getCampo( "hora" );
        $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsRecordSet->getCampo( "num_orgao" ) );
        $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade( $rsRecordSet->getCampo( "num_unidade" ) );
        $this->inCodCategoria  = $rsRecordSet->getCampo( "cod_categoria" );

        if ($this->getCodCategoria()) {
            include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoCategoriaEmpenho.class.php" );
            $obTEmpenhoCategoriaEmpenho = new TEmpenhoCategoriaEmpenho;
            $obTEmpenhoCategoriaEmpenho->setDado('cod_categoria',$this->getCodCategoria());
            $obErro = $obTEmpenhoCategoriaEmpenho->recuperaPorChave( $rsCategoria, $boTransacao );
            if (!$obErro->ocorreu()) {
                $this->stNomCategoria = $rsCategoria->getCampo('descricao');
            }
        }

        $obTEmpenhoAutorizacaoAnulada->setDado( "cod_autorizacao", $this->inCodAutorizacao                          );
        $obTEmpenhoAutorizacaoAnulada->setDado( "exercicio"      , $this->stExercicio                               );
        $obTEmpenhoAutorizacaoAnulada->setDado( "cod_entidade"   , $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $obTEmpenhoAutorizacaoAnulada->recuperaPorChave( $rsRecordSetAnulada, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$rsRecordSetAnulada->eof() ) {
                $this->stDtAnulacao = $rsRecordSetAnulada->getCampo( "dt_anulacao" );
                   $this->setMotivoAnulacao( $rsRecordSetAnulada->getCampo( "motivo" ) );
            }

            $this->obROrcamentoReserva->setExercicio( $this->stExercicio );
            $this->obROrcamentoReserva->setCodReserva( $this->obROrcamentoReserva->getCodReserva() );
            $obErro = $this->obROrcamentoReserva->consultar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obROrcamentoEntidade->setExercicio( $this->stExercicio );
                $obErro = $this->obROrcamentoEntidade->consultar( $rsEntidade, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = parent::consultar( $boTransacao );
                }
            }
        }

    }

    return $obErro;
}

function consultarItemMaterial($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );

    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;

    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho );
    $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio"      , $this->stExercicio     );
    $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaItemMaterial( $rsRecordSet, $boTransacao );

    if ($obErro->ocorreu()) {
        return $obErro;
    } else {
        $boItemMaterial = ($rsRecordSet->getNumLinhas() > 0) ? true : false;
    }

    return $boItemMaterial;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMaiorData(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoAutorizacaoEmpenho.class.php"                    );
    $obTEmpenhoAutorizacaoEmpenho                   =  new TEmpenhoAutorizacaoEmpenho;
    $stFiltro = null;

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";

    $obTEmpenhoAutorizacaoEmpenho->setDado('stExercicio',$this->stExercicio);

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaMaiorDataAutorizacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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

function listarLicitacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GP_COM_MAPEAMENTO."VComprasSamlinkSiamLicitac.class.php"         );
    $obVSamlinkSiamLicitac = new VSamlinkSiamLicitac;
    if( $this->stNumLicitacao )
        $stFiltro .= " numero = '".str_pad($this->stNumLicitacao,8,'0',STR_PAD_LEFT)."' AND ";
    if( $this->stTipoLicitacao )
        $stFiltro .= " tipo = '".$this->stTipoLicitacao."' AND ";
    if( $this->stDescricaoLicitacao )
        $stFiltro .= " LOWER(descricao) like LOWER('%".$this->stDescricaoLicitacao."%') AND ";
    if( $this->stExercicio )
        $stFiltro .= " TO_CHAR( dt_abertura, 'yyyy' ) = '".$this->stExercicio."' AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "tipo,numero";
    $obErro = $obVSamlinkSiamLicitac->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarItensLicitacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GP_COM_MAPEAMENTO."VComprasSamlinkSiamJulgam.class.php"          );
    $obVSamlinkSiamJulgam = new VSamlinkSiamJulgam;
    if( $this->stNumLicitacao )
        $stFiltro .= " J.numero = '".str_pad($this->stNumLicitacao,8,'0',STR_PAD_LEFT)."' AND ";
    if( $this->stTipoLicitacao )
        $stFiltro .= " J.tipo = '".$this->stTipoLicitacao."' AND ";
    if( $this->obRCGM->getNumCGM() )
        $stFiltro .= "J.numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    if( $this->obROrcamentoDespesa->getCodDespesa() )
        $stFiltro .= "J.dotacao = ".$this->obROrcamentoDespesa->getCodDespesa()." AND ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "J.tipo,J.numero";
    $obErro = $obVSamlinkSiamJulgam->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarTipoLicitacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GP_COM_MAPEAMENTO."VComprasSamlinkSiamCfLicita.class.php"        );
    $obVSamlinkSiamCfLicita = new VSamlinkSiamCfLicita;
    if( $this->stTipoLicitacao )
        $stFiltro .= " tipo = '".$this->stTipoLicitacao."' AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "tipo,descr";
    $obErro = $obVSamlinkSiamCfLicita->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarPorPreEmpenho(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND ae.cod_pre_empenho = ".$this->inCodPreEmpenho." ";

    if( $this->inCodAutorizacao )
        $stFiltro  .= " AND ae.cod_autorizacao = ".$this->inCodAutorizacao." ";

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND ae.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->stExercicio )
        $stFiltro .= " AND ae.exercicio = '".$this->stExercicio."' ";

    if( $this->stDtAutorizacao )
        $stFiltro .= " AND ae.dt_autorizacao = '".$this->stDtAutorizacao."' ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "ae.cod_entidade,ae.cod_autorizacao";
    $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamentoPorPreEmpenho( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarTodos(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    if( $this->obROrcamentoDespesa->getCodDespesa() )
        $stFiltro  = " AND tabela.cod_despesa = ".$this->obROrcamentoDespesa->getCodDespesa()." ";
    if ( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
        if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
            $stFiltro = " AND ( ".substr($stFiltro,4,strlen($stFiltro))." OR ";
        } else {
            $stFiltro .= " AND ";
        }
        $stFiltro .= " (publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%' OR ";
        $stFiltro .= "publico.fn_mascarareduzida(tabela.cod_estrutural_rubrica) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%') ";
        if( $this->obROrcamentoDespesa->getCodDespesa() )
            $stFiltro .= " ) ";
    }
    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodAutorizacao )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->inCodAutorizacao." ";

    if( $this->inCodAutorizacaoInicial )
        $stFiltro  .= " AND tabela.cod_autorizacao >= ".$this->inCodAutorizacaoInicial." ";
    if( $this->inCodAutorizacaoFinal )
        $stFiltro  .= " AND tabela.cod_autorizacao <= ".$this->inCodAutorizacaoFinal." ";

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->stDtAutorizacao )
        $stFiltro .= " AND tabela.dt_autorizacao = '".$this->stDtAutorizacao."' ";
    if ($this->stDtAutorizacaoInicial or $this->stDtAutorizacaoFinal) {
        $this->stDtAutorizacaoInicial = ( $this->stDtAutorizacaoInicial ) ? $this->stDtAutorizacaoInicial : '01/01/'.$this->stExercicio;
        $this->stDtAutorizacaoFinal = ( $this->stDtAutorizacaoFinal ) ? $this->stDtAutorizacaoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_autorizacao,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtAutorizacaoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtAutorizacaoFinal."','dd/mm/yyyy') ";
    }
    if( $this->obROrcamentoReserva->getAnulada() )
        $stFiltro .= " AND tabela.anulada = '".$this->obROrcamentoReserva->getAnulada()."' ";
    if ($this->boAlterar) {
        $stFiltro .= " AND NOT EXISTS ( SELECT 1                                           \n";
        $stFiltro .= "                    FROM empenho.empenho as ee                       \n";
        $stFiltro .= "                   WHERE ee.cod_pre_empenho = tabela.cod_pre_empenho \n";
        $stFiltro .= "                     AND ee.exercicio       = tabela.exercicio       \n";
        $stFiltro .= "                )                                                    \n";

    }
    
    if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND tabela.compra_cod_modalidade = ".$this->inCodModalidadeCompra." \n";

    if( $this->inCompraInicial )
        $stFiltro .= " AND tabela.cod_compra_direta >= ".$this->inCompraInicial." \n";
        
    if( $this->inCompraFinal )
        $stFiltro .= " AND tabela.cod_compra_direta <= ".$this->inCompraFinal." \n";
    
    if( $this->inCodModalidadeLicitacao )
        $stFiltro .= " AND tabela.licitacao_cod_modalidade = ".$this->inCodModalidadeLicitacao." \n";

    if( $this->inLicitacaoInicial )
        $stFiltro .= " AND tabela.cod_licitacao >= ".$this->inLicitacaoInicial." \n";
        
    if( $this->inLicitacaoFinal )
        $stFiltro .= " AND tabela.cod_licitacao <= ".$this->inLicitacaoFinal." \n";

    if( $this->inCentroCusto )
        $stFiltro .= " AND tabela.centro_custo = ".$this->inCentroCusto." \n";
    
    $obTEmpenhoAutorizacaoEmpenho->setDado( "numcgm"    , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio" , $this->stExercicio );
    
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_entidade,tabela.cod_autorizacao";
    
    if( $this->boEmpenhoCompraLicitacao ){
        $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamentoTodosCompraLicitacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );  
    } else {
        $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamentoTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
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
function listarConsulta(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    if( $this->obROrcamentoDespesa->getCodDespesa() )
        $stFiltro  = " AND tabela.cod_despesa = ".$this->obROrcamentoDespesa->getCodDespesa()." ";
    if ( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
        if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
            $stFiltro = " AND ( ".substr($stFiltro,4,strlen($stFiltro))." OR ";
        } else {
            $stFiltro .= " AND ";
        }
        $stFiltro .= " (publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%' OR ";
        $stFiltro .= "publico.fn_mascarareduzida(tabela.cod_estrutural_rubrica) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%') ";
        if( $this->obROrcamentoDespesa->getCodDespesa() )
            $stFiltro .= " ) ";
    }
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " AND tabela.num_orgao = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " AND tabela.num_unidade = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()." ";

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodAutorizacao )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->inCodAutorizacao." ";

    if( $this->inCodAutorizacaoInicial )
        $stFiltro  .= " AND tabela.cod_autorizacao >= ".$this->inCodAutorizacaoInicial." ";
    if( $this->inCodAutorizacaoFinal )
        $stFiltro  .= " AND tabela.cod_autorizacao <= ".$this->inCodAutorizacaoFinal." ";

    if( $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() )
        $stFiltro  .= " AND CD.cod_estrutural = '".$this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()."' ";

    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro  .= " AND D.cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." ";

    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() )
        $stFiltro  .= " AND rec.masc_recurso_red like '".$this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso()."%' ";

    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() )
        $stFiltro  .= " AND rec.cod_detalhamento = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento()." ";

    if( $this->obREmpenhoHistorico->getCodHistorico() )
        $stFiltro  .= " AND tabela.cod_historico = ".$this->obREmpenhoHistorico->getCodHistorico()." ";

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->stDtAutorizacao )
        $stFiltro .= " AND tabela.dt_autorizacao = '".$this->stDtAutorizacao."' ";
    if ($this->stDtAutorizacaoInicial or $this->stDtAutorizacaoFinal) {
        $this->stDtAutorizacaoInicial = ( $this->stDtAutorizacaoInicial ) ? $this->stDtAutorizacaoInicial : '01/01/'.$this->stExercicio;
        $this->stDtAutorizacaoFinal = ( $this->stDtAutorizacaoFinal ) ? $this->stDtAutorizacaoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_autorizacao,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtAutorizacaoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtAutorizacaoFinal."','dd/mm/yyyy') ";
    }

    if( $this->inSituacao == 1)
        $stFiltro  .= " AND tabela.situacao = 'Empenhada' ";
    if( $this->inSituacao == 2)
        $stFiltro  .= " AND tabela.situacao = 'Não Empenhada' ";
    if( $this->inSituacao == 3)
        $stFiltro  .= " AND tabela.situacao = 'Anulada' ";
    if( $this->inSituacao == 4)
        $stFiltro  .= " AND tabela.situacao <> 'Anulada' ";

    if (!($this->inSituacao<=3)) {
    if( $this->obROrcamentoReserva->getAnulada() )
        $stFiltro .= " AND tabela.anulada = '".$this->obROrcamentoReserva->getAnulada()."' ";
    else
        $stFiltro .= " AND tabela.anulada = 'f' ";
    }

    if ($this->boAlterar) {
        $stFiltro .= " AND NOT EXISTS ( SELECT ee.cod_pre_empenho                          \n";
        $stFiltro .= "                    FROM empenho.empenho as ee                       \n";
        $stFiltro .= "                   WHERE ee.cod_pre_empenho = tabela.cod_pre_empenho \n";
        $stFiltro .= "                     AND ee.exercicio       = tabela.exercicio       \n";
        $stFiltro .= "                )                                                    \n";
    }
    if ($this->boAnuladaTotal) {
        $stFiltro .= " AND tabela.vl_empenhado = (
                        SELECT
                            sum(EAI.vl_anulado)
                        FROM
                            empenho.empenho_anulado_item as EAI
                        WHERE
                            EAI.cod_pre_empenho = tabela.cod_pre_empenho and
                            EAI.exercicio = tabela.exercicio
                        ) \n";
    }
    
    if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND tabela.compra_cod_modalidade = ".$this->inCodModalidadeCompra." \n";

    if( $this->inCompraInicial )
        $stFiltro .= " AND tabela.cod_compra_direta >= ".$this->inCompraInicial." \n";
        
    if( $this->inCompraFinal )
        $stFiltro .= " AND tabela.cod_compra_direta <= ".$this->inCompraFinal." \n";
    
    if( $this->inCodModalidadeLicitacao )
        $stFiltro .= " AND tabela.licitacao_cod_modalidade = ".$this->inCodModalidadeLicitacao." \n";

    if( $this->inLicitacaoInicial )
        $stFiltro .= " AND tabela.cod_licitacao >= ".$this->inLicitacaoInicial." \n";
        
    if( $this->inLicitacaoFinal )
        $stFiltro .= " AND tabela.cod_licitacao <= ".$this->inLicitacaoFinal." \n";
    
    $obTEmpenhoAutorizacaoEmpenho->setDado( "numcgm"    , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio" , $this->stExercicio );
    
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_entidade, tabela.cod_autorizacao";
    
    if( $this->boEmpenhoCompraLicitacao ){
        $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamentoConsultaCompraLicitacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }else{
        $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamentoConsulta( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
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
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    if(empty($stFiltro))
        $stFiltro = "";
    if( $this->obROrcamentoDespesa->getCodDespesa() )
        $stFiltro  = " AND tabela.cod_despesa = ".$this->obROrcamentoDespesa->getCodDespesa()." ";
    if ( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
        if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
            $stFiltro = " AND ( ".substr($stFiltro,4,strlen($stFiltro))." OR ";
        } else {
            $stFiltro .= " AND ";
        }
        $stFiltro .= " (publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%' OR ";
        $stFiltro .= "publico.fn_mascarareduzida(tabela.cod_estrutural_rubrica) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%') ";
        if( $this->obROrcamentoDespesa->getCodDespesa() )
            $stFiltro .= " ) ";
    }
    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodAutorizacao )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->inCodAutorizacao." ";

    if( $this->inCodAutorizacaoInicial )
        $stFiltro  .= " AND tabela.cod_autorizacao >= ".$this->inCodAutorizacaoInicial." ";
    if( $this->inCodAutorizacaoFinal )
        $stFiltro  .= " AND tabela.cod_autorizacao <= ".$this->inCodAutorizacaoFinal." ";

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->stDtAutorizacao )
        $stFiltro .= " AND tabela.dt_autorizacao = '".$this->stDtAutorizacao."' ";
    if ($this->stDtAutorizacaoInicial or $this->stDtAutorizacaoFinal) {
        $this->stDtAutorizacaoInicial = ( $this->stDtAutorizacaoInicial ) ? $this->stDtAutorizacaoInicial : '01/01/'.$this->stExercicio;
        $this->stDtAutorizacaoFinal = ( $this->stDtAutorizacaoFinal ) ? $this->stDtAutorizacaoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_autorizacao,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtAutorizacaoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtAutorizacaoFinal."','dd/mm/yyyy') ";
    }
    if( $this->obROrcamentoReserva->getAnulada() )
        $stFiltro .= " AND tabela.anulada = '".$this->obROrcamentoReserva->getAnulada()."' ";
    else
        $stFiltro .= " AND tabela.anulada = 'f' ";
    if ($this->boAlterar) {
        $stFiltro .= " AND NOT EXISTS ( SELECT ee.cod_pre_empenho                          \n";
        $stFiltro .= "                    FROM empenho.empenho as ee                       \n";
        $stFiltro .= "                   WHERE ee.cod_pre_empenho = tabela.cod_pre_empenho \n";
        $stFiltro .= "                     AND ee.exercicio       = tabela.exercicio       \n";
        $stFiltro .= "                )                                                    \n";
    }
    
    if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND tabela.compra_cod_modalidade = ".$this->inCodModalidadeCompra." \n";

    if( $this->inCompraInicial )
        $stFiltro .= " AND tabela.cod_compra_direta >= ".$this->inCompraInicial." \n";
        
    if( $this->inCompraFinal )
        $stFiltro .= " AND tabela.cod_compra_direta <= ".$this->inCompraFinal." \n";
    
    if( $this->inCodModalidadeLicitacao )
        $stFiltro .= " AND tabela.licitacao_cod_modalidade = ".$this->inCodModalidadeLicitacao." \n";

    if( $this->inLicitacaoInicial )
        $stFiltro .= " AND tabela.cod_licitacao >= ".$this->inLicitacaoInicial." \n";
        
    if( $this->inLicitacaoFinal )
        $stFiltro .= " AND tabela.cod_licitacao <= ".$this->inLicitacaoFinal." \n";

    if( $this->inCentroCusto )
        $stFiltro .= " AND tabela.centro_custo = ".$this->inCentroCusto." \n";
        
    $obTEmpenhoAutorizacaoEmpenho->setDado( "numcgm"    , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio" , $this->stExercicio );
    
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "tabela.cod_entidade,tabela.cod_autorizacao";
    
    if( $this->boEmpenhoCompraLicitacao ){
        $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaEmpenhoCompraLicitacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    } else {
        $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
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
function listarReemitirAnulados(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    if ($this->stExercicio) {
        $stFiltro .= " AND ae.exercicio = '".$this->stExercicio."' ";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro  .= " AND ae.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().")  ";
    }
    if ($this->inCodAutorizacaoInicial) {
        $stFiltro  .= " AND ae.cod_autorizacao >= ".$this->inCodAutorizacaoInicial." ";
    }
    if ($this->inCodAutorizacaoFinal) {
        $stFiltro  .= " AND ae.cod_autorizacao <= ".$this->inCodAutorizacaoFinal." ";
    }
    if ($this->stDtAutorizacaoInicial or $this->stDtAutorizacaoFinal) {
        $this->stDtAutorizacaoInicial = ( $this->stDtAutorizacaoInicial ) ? $this->stDtAutorizacaoInicial : '01/01/'.$this->stExercicio;
        $this->stDtAutorizacaoFinal = ( $this->stDtAutorizacaoFinal ) ? $this->stDtAutorizacaoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  ae.dt_autorizacao between ";
        $stFiltro .= "TO_DATE('".$this->stDtAutorizacaoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtAutorizacaoFinal."','dd/mm/yyyy') ";
    }
    if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
        $stFiltro  = " AND ped.cod_despesa = ".$this->obROrcamentoDespesa->getCodDespesa()." ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro  .= " AND pe.cgm_beneficiario = ".$this->obRCGM->getNumCGM()." ";
    }
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "ae.cod_entidade,ae.cod_autorizacao,ae.exercicio,dt_anulacao,nom_cgm,valor";
    $obErro = $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamentoReemitirAnulados( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

           return $obErro;
}

/**
    * Método para gerar proximo codigo apartir da configuração
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
*/
function buscaProximoCod($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    $obREmpenhoConfiguracao = new REmpenhoConfiguracao;
    $obErro = $obREmpenhoConfiguracao->consultar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $obREmpenhoConfiguracao->getNumeracao() == 'P' ) {
            $obTEmpenhoAutorizacaoEmpenho->setComplementoChave( "cod_entidade, exercicio" );
            $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );
        } else {
            $obTEmpenhoAutorizacaoEmpenho->setComplementoChave( "exercicio" );
        }
        $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio" , $this->stExercicio );
        $obErro = $obTEmpenhoAutorizacaoEmpenho->proximoCod( $this->inCodAutorizacao, $boTransacao );
    }

    return $obErro;
}

/**
    * Incluir Ordem Pagamento
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php";
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarMaiorData( $rsMaiorData, "",$boTransacao);

        if (!$obErro->ocorreu()) {
            $stMaiorData = $rsMaiorData->getCampo( "data_autorizacao" );
            if (SistemaLegado::comparaDatas($stMaiorData,$this->stDtAutorizacao)) {
                $obErro->setDescricao( "Data da Autorização deve ser maior ou igual a '".$stMaiorData."'!" );
            }

            if ( !$obErro->ocorreu() ) {
                if (SistemaLegado::comparaDatas($this->stDtAutorizacao, date("d/m/Y"))) {
                    $obErro->setDescricao( "Data da Autorização deve ser menor ou igual a data atual!" );
                }

                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->checarFormaExecucaoOrcamento( $boDetalhadoExecucao , $boTransacao );

                    if ( $boDetalhadoExecucao and !$obErro->ocorreu() ) {
                        if( $this->obROrcamentoDespesa->getCodDespesa() and !$this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
                            $obErro->setDescricao( "Campo Desdobramento inválido!()" );
                    }

                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                            $obErro = $this->consultaSaldoAnteriorDataEmpenho($nuSaldoAnterior, '', $boTransacao);

                            if ( !$obErro->ocorreu() ) {
                                if ( $nuSaldoAnterior >= $this->obROrcamentoReserva->getVlReserva() )
                                    $obErro = $this->checarPermissaoAutorizacao( $boTransacao );
                                else
                                    $obErro->setDescricao( "Valor da autorização é superior ao da dotação ".$this->obROrcamentoDespesa->getCodDespesa().". Saldo da Dotação: ".number_format($nuSaldoAnterior,2,',','.'));
                            }
                        }

                        if ( !$obErro->ocorreu() ) {                            
                            $obErro = parent::incluir( $boTransacao );

                            if ( !$obErro->ocorreu() ) {
                                if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                                    $obErro = $this->getMascaraDespesaReduzida( $boTransacao );

                                    list( $dia,$mes,$ano ) = explode( '/', $this->obROrcamentoReserva->getDtValidadeFinal() );
                                    if ( $this->stExercicio.date('md') > "$ano$mes$dia" )
                                        $obErro->setDescricao("A data de validade final deve ser maior ou igual ao dia de hoje!");
                                }

                                if ( !$obErro->ocorreu() ) {
                                    $obErro = $this->buscaProximoCod($boTransacao );

                                    if ( !$obErro->ocorreu() ) {
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho                           );
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_autorizacao", $this->inCodAutorizacao                          );
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio"      , $this->stExercicio                               );
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_entidade"   , $this->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "dt_autorizacao" , $this->stDtAutorizacao                           );
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "num_orgao"      , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "num_unidade"    , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade());
                                        $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_categoria"  , $this->inCodCategoria                            );

                                        $obErro = $obTEmpenhoAutorizacaoEmpenho->inclusao( $boTransacao );

                                        if ( !$obErro->ocorreu() and $this->obROrcamentoDespesa->getCodDespesa() and !$this->boAutViaPatrimonial )
                                            $obErro = $this->reservarDotacao( $boTransacao );

                                        // Inclui a contrapartida se o empenho for de adiantamentos/subvencoes
                                        if ($this->inCodCategoria == 2 || $this->inCodCategoria == 3) {
                                            if (!$obErro->ocorreu()) {
                                                $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_autorizacao'       , $this->inCodAutorizacao );
                                                $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'exercicio'             , $this->stExercicio      );
                                                $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_entidade'          , $this->obROrcamentoEntidade->getCodigoEntidade() );
                                                $obErro = $this->obTEmpenhoContrapartidaAutorizacao->inclusao( $boTransacao );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoAutorizacaoEmpenho );

    return $obErro;
}

/**
    * Alterar Ordem Pagamento
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    list( $dia,$mes,$ano ) = explode( '/', $this->obROrcamentoReserva->getDtValidadeFinal() );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->checarPermissaoAutorizacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = parent::alterar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                    $obErro = $this->getMascaraDespesaReduzida( $boTransacao );

                   if ( !$obErro->ocorreu() ) {
                        $obErro = $this->consultaSaldoAnteriorDataEmpenho($nuSaldoAnterior, '', $boTransacao);
                        
                        if ( !$obErro->ocorreu() ) {
                            $nuNewVlReserva = str_replace('.','',$this->obROrcamentoReserva->getVlReserva());
                            $nuNewVlReserva = str_replace(',','.',$nuNewVlReserva);
                            $this->obROrcamentoReserva->setExercicio( $this->stExercicio );
                            if( $this->obROrcamentoReserva->getCodReserva() )
                                $obErro = $this->obROrcamentoReserva->consultar( $boTransacao );

                           if ( !$obErro->ocorreu() ) {
                                if ( $nuNewVlReserva > $this->obROrcamentoReserva->getVlReserva() ) {
                                    if ( ( $nuSaldoAnterior + $this->obROrcamentoReserva->getVlReserva() ) >= $nuNewVlReserva ) {
                                        $obErro = $this->checarPermissaoAutorizacao( $boTransacao );
                                    } else {
                                        $obErro->setDescricao( "Valor da autorização é superior ao da dotação." );
                                    }
                                }
                            }
                            $this->obROrcamentoReserva->setVlReserva( $nuNewVlReserva );
                        }
                   }
                    if ( $this->stExercicio.date('md') > "$ano$mes$dia" ) {
                        $obErro->setDescricao("A data de validade final deve ser maior ou igual ao dia de hoje!");
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho                           );
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_autorizacao", $this->inCodAutorizacao                          );
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio"      , $this->stExercicio                               );
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_entidade"   , $this->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "dt_autorizacao" , $this->stDtAutorizacao                           );
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "num_orgao"      , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "num_unidade"    , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade());
                    $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_categoria"  , $this->inCodCategoria                            );

                    $obErro = $obTEmpenhoAutorizacaoEmpenho->alteracao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obROrcamentoDespesa->getCodDespesa() ) {
                            $this->obROrcamentoReserva->setExercicio( $this->stExercicio );
                            $this->obROrcamentoReserva->setCodDespesa($this->obROrcamentoDespesa->getCodDespesa());
                            if ( $this->obROrcamentoReserva->getCodReserva() ) {
                                $this->obROrcamentoReservaSaldos->setCodReserva($this->obROrcamentoReserva->getCodReserva());
                                $this->obROrcamentoReservaSaldos->setExercicio( $this->stExercicio );
                                $this->obROrcamentoReservaSaldos->obROrcamentoDespesa->setCodDespesa( $this->obROrcamentoDespesa->getCodDespesa() );

                                $this->obROrcamentoReservaSaldos->setDtValidadeInicial( $this->obROrcamentoReserva->getDtValidadeInicial() );
                                $this->obROrcamentoReservaSaldos->setDtValidadeFinal( $this->obROrcamentoReserva->getDtValidadeFinal() );
                                $this->obROrcamentoReservaSaldos->setDtInclusao( $this->obROrcamentoReserva->getDtInclusao() );
                                $vlReserva = $this->obROrcamentoReserva->getVlReserva() ? $this->obROrcamentoReserva->getVlReserva() : "0";
                                $this->obROrcamentoReservaSaldos->setVlReserva( $vlReserva );

                                $obErro = $this->obROrcamentoReservaSaldos->alterar( $boTransacao );
                            } else {
                                $obErro = $this->reservarDotacao( $boTransacao );
                            }
                        }
                    }

                    // Inclui/Altera/Exclui a contrapartida
                    if (!$obErro->ocorreu()) {

                         $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_autorizacao'       , $this->inCodAutorizacao );
                         $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'exercicio'             , $this->stExercicio      );
                        $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_entidade'          , $this->obROrcamentoEntidade->getCodigoEntidade() );

                        if ($this->inCodCategoria == 2 || $this->inCodCategoria == 3) {

                            $this->obTEmpenhoContrapartidaAutorizacao->recuperaContrapartidaLancamento( $rsContrapartida ,'',$boTransacao );

                            if ( $rsContrapartida->getNumLinhas() > 0 ) {
                                $obErro = $this->obTEmpenhoContrapartidaAutorizacao->alteracao( $boTransacao );
                            } else {
                                $obErro = $this->obTEmpenhoContrapartidaAutorizacao->inclusao( $boTransacao );
                            }

                        } else {
                            $obErro = $this->obTEmpenhoContrapartidaAutorizacao->exclusao( $boTransacao );
                        }

                    }

                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoAutorizacaoEmpenho );

    return $obErro;
}

/**
    * Anula Autorização de Empenho
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function anular($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoAnulada.class.php"  );
    $obTEmpenhoAutorizacaoAnulada = new TEmpenhoAutorizacaoAnulada;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->checarPermissaoAutorizacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->consultar($boTransacao);
            if ( !$obErro->ocorreu() ) {
                if (SistemaLegado::comparaDatas($this->getDtAutorizacao(),$this->getDtAnulacao())) {
                    $obErro->setDescricao( "A data da anulação deve ser posterior ou igual à data da autorização." );
                }
                if ( !$obErro->ocorreu() ) {
                    $obTEmpenhoAutorizacaoAnulada->setDado( "cod_entidade"   , $this->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTEmpenhoAutorizacaoAnulada->setDado( "exercicio"      , $this->stExercicio                               );
                    $obTEmpenhoAutorizacaoAnulada->setDado( "cod_autorizacao", $this->inCodAutorizacao                          );
                    $obTEmpenhoAutorizacaoAnulada->setDado( "dt_anulacao"    , date('d/m/Y')                                    );
                    $obTEmpenhoAutorizacaoAnulada->setDado( "motivo"         , $this->stMotivoAnulacao                          );
                    $obErro = $obTEmpenhoAutorizacaoAnulada->inclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obROrcamentoReserva->setExercicio( $this->stExercicio );
                        $obErro = $this->obROrcamentoReserva->consultar( $boTransacao );
                        if ( !$obErro->ocorreu() and $this->obROrcamentoReserva->getCodReserva() ) {
                            $this->obROrcamentoReservaSaldos->setCodReserva($this->obROrcamentoReserva->getCodReserva());
                            $this->obROrcamentoReservaSaldos->setExercicio( $this->stExercicio );
                            $this->obROrcamentoReservaSaldos->setDtAnulacao( $this->stDtAnulacao );
                            $this->obROrcamentoReservaSaldos->setDtValidadeInicial($this->obROrcamentoReserva->getDtValidadeInicial()  );
                            $this->obROrcamentoReservaSaldos->setDtValidadeFinal($this->obROrcamentoReserva->getDtValidadeFinal() );
                            $this->obROrcamentoReservaSaldos->setMotivoAnulacao( $this->stMotivoAnulacao );

                            $obErro = $this->obROrcamentoReservaSaldos->anular( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoAutorizacaoAnulada );

    return $obErro;
}

/**
    * Reserva Dotação
    * @access Public
    * @param Object $boTransacao
    * @return Object $boTransacao
*/
function reservarDotacao($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoReserva.class.php"  );
    $obTEmpenhoAutorizacaoReserva = new TEmpenhoAutorizacaoReserva;

    $this->obROrcamentoReservaSaldos->setExercicio( $this->stExercicio );
    $this->obROrcamentoReservaSaldos->obROrcamentoDespesa->setCodDespesa( $this->obROrcamentoDespesa->getCodDespesa() );

    $this->obROrcamentoReservaSaldos->setDtValidadeInicial( $this->obROrcamentoReserva->getDtValidadeInicial() );
    $this->obROrcamentoReservaSaldos->setDtValidadeFinal( $this->obROrcamentoReserva->getDtValidadeFinal() );
    $this->obROrcamentoReservaSaldos->setDtInclusao( $this->obROrcamentoReserva->getDtInclusao() );
       $this->obROrcamentoReservaSaldos->setMotivo('Autorização de Empenho '.$this->getCodAutorizacao()."/".Sessao::getExercicio());

    $vlReserva = $this->obROrcamentoReserva->getVlReserva() ? $this->obROrcamentoReserva->getVlReserva() : "0";
    $this->obROrcamentoReservaSaldos->setVlReserva( $vlReserva );

    $obErro = $this->obROrcamentoReservaSaldos->incluir( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoAutorizacaoReserva->setDado( "exercicio"       , $this->stExercicio );
        $obTEmpenhoAutorizacaoReserva->setDado( "cod_entidade"    , $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obTEmpenhoAutorizacaoReserva->setDado( "cod_autorizacao" , $this->inCodAutorizacao );
        $obTEmpenhoAutorizacaoReserva->setDado( "cod_pre_empenho" , $this->inCodPreEmpenho );
        $obTEmpenhoAutorizacaoReserva->setDado( "cod_reserva"     , $this->obROrcamentoReservaSaldos->getCodReserva() );
        $obErro = $obTEmpenhoAutorizacaoReserva->inclusao( $boTransacao );
    }

    return $obErro;
}

/**
    * Duplicar Autorização / Reserva / Pre_empenho / Ítens Pre Empenho
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function duplicar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->consultar($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $arChaveAtributo =  array( "cod_pre_empenho" => $this->inCodPreEmpenho,
                                       "exercicio"       => $this->stExercicio       );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
            $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

            while (!$rsAtributos->eof()) {
                $this->obRCadastroDinamico->addAtributosDinamicos( $rsAtributos->getCampo("cod_atributo") , $rsAtributos->getCampo("valor") );
                $rsAtributos->proximo();
            }

            $obErro = parent::incluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->buscaProximoCod( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->listarMaiorData( $rsMaiorData, "",$boTransacao);
                        if ( !$obErro->ocorreu() ) {
                            $this->stDtAutorizacao = $rsMaiorData->getCampo( "data_autorizacao" );
                            $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
                            $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade());

                            $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho                           );
                            $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_autorizacao", $this->inCodAutorizacao                          );
                            $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio"      , $this->stExercicio                               );
                            $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_entidade"   , $this->obROrcamentoEntidade->getCodigoEntidade() );
                            $obTEmpenhoAutorizacaoEmpenho->setDado( "dt_autorizacao" , $this->stDtAutorizacao                           );
                            $obTEmpenhoAutorizacaoEmpenho->setDado( "num_orgao"      , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
                            $obTEmpenhoAutorizacaoEmpenho->setDado( "num_unidade"    , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade());
                            $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_categoria"  , $this->inCodCategoria                            );

                            $obErro = $obTEmpenhoAutorizacaoEmpenho->inclusao( $boTransacao );

                            if ( !$obErro->ocorreu() and $this->obROrcamentoReserva->getCodReserva()) {
                                $this->obROrcamentoReservaSaldos->setCodReserva($this->obROrcamentoReserva->getCodReserva());
                                $this->obROrcamentoReservaSaldos->setExercicio($this->stExercicio);
                                $this->obROrcamentoReservaSaldos->consultar($boTransacao);

                                $this->obROrcamentoReserva->setDtValidadeInicial($this->stDtAutorizacao);
                                $this->obROrcamentoReserva->setDtValidadeFinal($this->obROrcamentoReservaSaldos->getDtValidadeFinal());
                                $this->obROrcamentoReserva->setDtInclusao($this->stDtAutorizacao);
                                $this->obROrcamentoReserva->setVlReserva($this->obROrcamentoReservaSaldos->getVlReserva());

                                $obErro = $this->reservarDotacao( $boTransacao );
                            }

                            // Inclui a contrapartida se o empenho for de adiantamentos/subvencoes
                            if ($this->inCodCategoria == 2 || $this->inCodCategoria == 3) {
                                if (!$obErro->ocorreu()) {
                                    $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_autorizacao'       , $this->inCodAutorizacao );
                                    $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'exercicio'             , $this->stExercicio      );
                                    $this->obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_entidade'          , $this->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obErro = $this->obTEmpenhoContrapartidaAutorizacao->inclusao( $boTransacao );
                                }
                            }

                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoAutorizacaoEmpenho );

    return $obErro;
}

}
