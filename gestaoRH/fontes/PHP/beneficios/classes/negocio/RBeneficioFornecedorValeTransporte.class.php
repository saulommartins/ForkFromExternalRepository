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
* Classe de regra de negócio da tabela BENEFICIO.FORNECEDORVALETRANSPORTE
* Data de Criação: 08/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage regra de negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioFornecedorValeTransporte.class.php" );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioFornecedor.class.php"                    );

class RBeneficioFornecedorValeTransporte extends RBeneficioFornecedor
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
var $obTBeneficioFornecedorValeTransporte;

/**
    * @access Public
    * @param Object $Valor
*/
function setTBeneficioFornecedorValeTransporte($valor) { $this->obTBeneficioFornecedorValeTransporte      = $valor  ; }

/**
    * @access Public
    * @return Object
*/
function getTBeneficioFornecedorValeTransporte() { return $this->obTBeneficioFornecedorValeTransporte      ; }

/**
     * Método construtor
     * @access Private
*/
function RBeneficioFornecedorValeTransporte()
{
    parent::RBeneficioFornecedor();
    $this->setTBeneficioFornecedorValeTransporte  ( new TBeneficioFornecedorValeTransporte  );
    $this->obTransacao         = new Transacao;
}

/**
    * Inclui dados do fornecedor_vale_transporte no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirFornecedorValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioFornecedor->setDado("numcgm"   , $this->getNumCGM() );
        $obErro = $this->obTBeneficioFornecedor->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTBeneficioFornecedorValeTransporte->setDado("fornecedor_numcgm"   , $this->getNumCGM() );
            $obErro = $this->obTBeneficioFornecedorValeTransporte->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

    return $obErro;
}

/**
    * Exclui dados do fornecedor_vale_transporte no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirFornecedorValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioFornecedorValeTransporte->setDado("fornecedor_numcgm"   , $this->getNumCGM() );
        $obErro = $this->obTBeneficioFornecedorValeTransporte->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTBeneficioFornecedor->setDado("numcgm"   , $this->getNumCGM() );
            $obErro = $this->obTBeneficioFornecedor->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

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
function listarFornecedorValeTransporte(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    if ( $this->getNumCGM() ) {
        $stFiltro .= " AND fornecedor.cgm_fornecedor = ". $this->getNumCGM();
    }
    $obErro = $this->obTBeneficioFornecedorValeTransporte->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarFornecedorValeTransporteRelatorio(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    if ( $this->getNumCGM() ) {
        $stFiltro .= " AND fornecedor.cgm_fornecedor = ". $this->getNumCGM();
    }
    $obErro = $this->obTBeneficioFornecedorValeTransporte->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
