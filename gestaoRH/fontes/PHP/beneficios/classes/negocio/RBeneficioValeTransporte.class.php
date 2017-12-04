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
* Classe de regra de negócio da tabela BENEFICIO.VALE_TRANSPORTE
* Data de Criação: 07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage regra de negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioValeTransporte.class.php"       );
include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioCusto.class.php"                );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioLinha.class.php"                     );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioFornecedorValeTransporte.class.php"  );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioItinerario.class.php"                );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioVigencia.class.php"                  );

class RBeneficioValeTransporte
{
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTBeneficioValeTransporte;
/**
    * @access Private
    * @var Object
*/
var $obTBeneficioCusto;
/**
    * @access Private
    * @var Object
*/
var $obRBeneficioLinha;
/**
    * @access Private
    * @var Object
*/
var $obRBeneficioFornecedorValeTransporte;
/**
    * @access Private
    * @var Object
*/
var $obRBeneficioItinerario;
/**
    * @access Private
    * @var Numeric
*/
var $flCusto;
/**
    * @access Private
    * @var Data
*/
var $dtInicioVigencia;
/**
    * @access Private
    * @var Integer
*/
var $inCodValeTransporte;

/**
    * @access Public
    * @param Object $Valor
*/
function setTBeneficioValeTransporte($valor) { $this->obTBeneficioValeTransporte   = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTBeneficioCusto($valor) { $this->obTBeneficioCusto            = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRBeneficioLinha($valor) { $this->obRBeneficioLinha            = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRBeneficioFornecedorValeTransporte($valor) { $this->obRBeneficioFornecedorValeTransporte   = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRBeneficioVigencia($valor)
{
    $this->obRBeneficioVigencia   = $valor  ;
    $this->obRBeneficioVigencia->addBeneficioFaixaDesconto();
}
/**
    * @access Public
    * @param Object $Valor
*/
function setRBeneficioItinerario(&$valor) { $this->obRBeneficioItinerario       = &$valor; }
/**
    * @access Public
    * @param Numeric $Valor
*/
function setCusto($valor) { $this->flCusto                      = $valor; }
/**
    * @access Public
    * @param Data $Valor
*/
function setInicioVigencia($valor) { $this->dtInicioVigencia             = $valor ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodValeTransporte($valor) { $this->inCodValeTransporte          = $valor ; }

/**
    * @access Public
    * @return Object
*/
function getTBeneficioValeTransporte() { return $this->obTBeneficioValeTransporte    ; }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioCusto() { return $this->obTBeneficioCusto             ; }
/**
    * @access Public
    * @return Object
*/
function getRBeneficioLinha() { return $this->obRBeneficioLinha             ; }
/**
    * @access Public
    * @return Object
*/
function getRBeneficioFornecedorValeTransporte() { return $this->obRBeneficioFornecedorValeTransporte    ; }
/**
    * @access Public
    * @return Object
*/
function getRBeneficioVigencia() { return $this->obRBeneficioVigencia          ; }
/**
    * @access Public
    * @return Numeric
*/
function getCusto() { return $this->flCusto                       ; }
/**
    * @access Public
    * @return Data
*/
function getInicioVigencia() { return $this->dtInicioVigencia              ; }
/**
    * @access Public
    * @return Integer
*/
function getCodValeTransporte() { return $this->inCodValeTransporte           ; }

/**
     * Método construtor
     * @access Private
*/
function RBeneficioValeTransporte()
{
    $this->setTBeneficioValeTransporte              ( new TBeneficioValeTransporte              );
    $this->setTBeneficioCusto                       ( new TBeneficioCusto                       );
    $this->setRBeneficioLinha                       ( new RBeneficioLinha                       );
    $this->setRBeneficioFornecedorValeTransporte    ( new RBeneficioFornecedorValeTransporte    );
    $this->setRBeneficioVigencia                    ( new RBeneficioVigencia                    );
    $this->obRBeneficioItinerario                   = new RBeneficioItinerario( $this );
    $this->obTransacao = new Transacao;
}

/**
    * Inclui dados do vale transporte no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro =  $this->obTBeneficioValeTransporte->proximoCod( $inCodValeTransporte , $boTransacao );
        $this->setCodValeTransporte( $inCodValeTransporte );
        if ( !$obErro->ocorreu() ) {
            $this->obTBeneficioValeTransporte->setDado("cod_vale_transporte"                           , $this->getCodValeTransporte() );
            $this->obTBeneficioValeTransporte->setDado("fornecedor_vale_transporte_fornecedor_numcgm"  , $this->obRBeneficioFornecedorValeTransporte->getNumCGM() );
            $obErro = $this->obTBeneficioValeTransporte->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTBeneficioCusto->setDado("inicio_vigencia"                     , $this->getInicioVigencia()    );
                $this->obTBeneficioCusto->setDado("valor"                               , $this->getCusto()             );
                $this->obTBeneficioCusto->setDado("vale_transporte_cod_vale_transporte" , $this->getCodValeTransporte() );
                $obErro = $this->obTBeneficioCusto->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->obRBeneficioItinerario->incluirItinerario( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Altera dados do vale transporte no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioCusto->setDado("inicio_vigencia"                     , $this->getInicioVigencia()    );
        $this->obTBeneficioCusto->setDado("valor"                               , $this->getCusto()             );
        $this->obTBeneficioCusto->setDado("vale_transporte_cod_vale_transporte" , $this->getCodValeTransporte() );
        $obErro = $this->obTBeneficioCusto->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Exclui dados do vale transporte no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $rsValeTransporte = new recordset;
        $obErro = $this->listarValeTransporteConcessao($rsValeTransporte,$boTransacao);

        if ( !$obErro->ocorreu() and $rsValeTransporte->getNumLinhas() < 0 ) {
            $this->obTBeneficioValeTransporte->setDado("cod_vale_transporte"                           , $this->getCodValeTransporte() );
            $this->obRBeneficioItinerario->setCodItinerario       ( $this->getCodValeTransporte() );
            $obErro = $this->obRBeneficioItinerario->excluirItinerario( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTBeneficioCusto->setDado("vale_transporte_cod_vale_transporte" , $this->getCodValeTransporte() );
                $obErro = $this->obTBeneficioCusto->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->obTBeneficioValeTransporte->exclusao( $boTransacao );
                }
            }
        } elseif ( $rsValeTransporte->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Esse Vale-Transporte está sendo utilizado pela Concessão de Vale-Transporte.");
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Executa um listar vale transporte na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarValeTransporte(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    if ( $this->obRBeneficioFornecedorValeTransporte->getNumCGM() ) {
        $stFiltro .= " AND fornecedor.cgm_fornecedor = ".$this->obRBeneficioFornecedorValeTransporte->getNumCGM();
    }
    if ( $this->getCodValeTransporte() ) {
        $stFiltro .= " AND vale_transporte.cod_vale_transporte = ".$this->getCodValeTransporte();
    }
    $obErro = $this->obTBeneficioValeTransporte->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um listar custos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCusto(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    if ( $this->getCodValeTransporte() ) {
        $stFiltro .= " WHERE vale_transporte_cod_vale_transporte = ".$this->getCodValeTransporte();
    }
    $obErro = $this->obTBeneficioCusto->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um listar Vale-Transporte para relatório na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarValeTransporteRelatorio(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTBeneficioValeTransporte->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um listar custos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarValeTransporteConcessao(&$rsRecordSet, $boTransacao = "")
{
    if ( $this->getCodValeTransporte() ) {
        $stFiltro .= " AND vale_transporte.cod_vale_transporte = ".$this->getCodValeTransporte();
    }
    $obErro = $this->obTBeneficioValeTransporte->recuperaRelacionamentoConcessao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
