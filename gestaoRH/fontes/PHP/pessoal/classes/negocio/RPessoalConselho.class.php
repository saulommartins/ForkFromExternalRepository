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
* Classe de regra de negócio para Conselho
* Data de Criação: 10/08/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Andre Almeida

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.42
                uc-00.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalConselho.class.php" );

/**
    * Classe de Regra de Negócio Pesssoal Conselho
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

*/

class RPessoalConselho
{
    /**
    * @var Integer
    * @access Private
    */
    public $inCodConselho;

    /**
    * @var String
    * @access Private
    */
    public $stDescricao;

    /**
    * @var String
    * @access Private
    */
    public $stSigla;

    /**
    * @var Boolean
    * @access Private
    */
    public $boFlagTransacao;

    /**
    * @var Boolean
    * @access Private
    */
    public $boTransacao;

    /**
    * @var Object
    * @access Private
    */
    public $obTPessoalConselho;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodConselho($valor) { $this->inCodConselho         = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setDescricao($valor) { $this->stDescricao           = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setSigla($valor) { $this->stSigla               = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao           = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalConselho($valor) { $this->obTPessoalConselho    = $valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getCodConselho() { return $this->inCodConselho;            }

    /**
        * @access Public
        * @return String
    */
    public function getSigla() { return $this->stSigla;                  }
    /**
        * @access Public
        * @return String
    */
    public function getDescricao() { return $this->stDescricao;              }

    public function RPessoalConselho()
    {
        $this->setTPessoalConselho           ( new TPessoalConselho            );
        $this->setTransacao                  ( new Transacao                   );
    }

    /**
        * Inclui dados do conselho no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirConselho($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTPessoalConselho->proximoCod( $this->inCodConselho, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalConselho->setDado("cod_conselho", $this->getCodConselho() );
                $this->obTPessoalConselho->setDado("sigla", $this->getSigla() );
                $this->obTPessoalConselho->setDado("descricao", $this->getDescricao() );
                $obErro = $this->obTPessoalConselho->inclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalConselho );

        return $obErro;
    }

    /**
        * Altera os dados do conselho no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function alterarConselho($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalConselho->setDado("cod_conselho", $this->getCodConselho() );
            $this->obTPessoalConselho->setDado("sigla", $this->getSigla() );
            $this->obTPessoalConselho->setDado("descricao", $this->getDescricao() );
            $obErro = $this->obTPessoalConselho->alteracao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

        return $obErro;
    }

    /**
        * Exclui o conselho no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function excluirConselho($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalConselho->setDado("cod_conselho", $this->getCodConselho() );
            $obErro = $this->obTPessoalConselho->validaExclusao("", $boTransacao);
                if ( !$obErro->ocorreu () )
                    $obErro = $this->obTPessoalConselho->exclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

        return $obErro;
    }

    /**
        * Executa um recuperaLista na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarConselho(&$rsRecordSet, $stOrder = "", $boTransacao = "")
    {
        //DESCONSIDERA REGISTRO 'NÃO INFORMADO'
        $stFiltro = ' WHERE cod_conselho > 0 ';
        if ( $this->getDescricao() ) {
            $stFiltro .= "AND UPPER(descricao) LIKE UPPER('".$this->getDescricao()."%')";
        }
        if ( $this->getSigla() ) {
            $stFiltro .= "AND UPPER(sigla) like UPPER('".$this->getSigla()."%')";
        }
        $obErro = $this->obTPessoalConselho->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }
}
