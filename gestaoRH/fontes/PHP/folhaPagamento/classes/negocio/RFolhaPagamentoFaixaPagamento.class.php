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
* Classe de regra de negócio para Folha de Pagamento - Faixa de Pagamento
* Data de Criação: 19/04/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package URBEM
* @subpackage  Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Classe de regra de negócio para Folha de Pagamento - Faixa de Pagamento
    * Data de Criação: 19/04/2006

    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra
*/
class RFolhaPagamentoFaixaPagamento
{
    /**
        * @access Private
        * @var Integer
    */
    public $inCodFaixa;
    /**
        * @access Private
        * @var Numeric
    */
    public $nuVlInicial;
    /**
        * @access Private
        * @var Numeric
    */
    public $nuVlFinal;
    /**
        * @access Private
        * @var Numeric
    */
    public $nuVlPagamento;

    /**
        * @access Public
        * @param integer $valor
    */
    public function setCodFaixa($valor) { $this->inCodFaixa = $valor; }
    /**
        * @access Public
        * @param numeric $valor
    */
    public function setVlInicial($valor) { $this->nuVlInicial = $valor; }
    /**
        * @access Public
        * @param numeric $valor
    */
    public function setVlPagamento($valor) { $this->nuVlPagamento = $valor; }
    /**
        * @access Public
        * @param numeric $valor
    */
    public function setVlFinal($valor) { $this->nuVlFinal = $valor; }

    public function setObRFolhaPagamentoSalarioFamilia(&$valor) { $this->obRFolhaPagamentoSalarioFamilia = &$valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getCodFaixa() { return $this->inCodFaixa; }
    /**
        * @access Public
        * @return Numeric
    */
    public function getVlInicial() { return $this->nuVlInicial; }
    /**
        * @access Public
        * @return Numeric
    */
    public function getVlFinal() { return $this->nuVlFinal; }
    /**
        * @access Public
        * @return Numeric
    */
    public function getVlPagamento() { return $this->nuVlPagamento; }

    /**
        * Método construtor
        * @access Private
    */
    public function RFolhaPagamentoFaixaPagamento(&$obRFolhaPagamentoSalarioFamilia)
    {
        $this->setObRFolhaPagamentoSalarioFamilia( $obRFolhaPagamentoSalarioFamilia );
    }

    public function incluirFaixaPagamento()
    {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaPagamentoSalarioFamilia.class.php" );

        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoFaixaPagamentoSalarioFamilia = new TFolhaPagamentoFaixaPagamentoSalarioFamilia;

            if ( !$this->getCodFaixa() ) {
                $stComplementoChave = $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->getComplementoChave();
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setComplementoChave("");
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setCampoCod("cod_faixa");
                $obErro = $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->proximoCod( $this->inCodFaixa, $boTransacao );
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setComplementoChave($stComplementoChave);
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setCampoCod("");
            }

            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setDado( "cod_faixa"              , $this->getCodFaixa() );
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setDado( "timestamp"              , $this->obRFolhaPagamentoSalarioFamilia->getTimestamp() );
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setDado( "cod_regime_previdencia" , $this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() );
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setDado( "vl_inicial"             , $this->getVlInicial() );
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setDado( "vl_final"               , $this->getVlFinal() );
                $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setDado( "vl_pagamento"           , $this->getVlPagamento() );
                $obErro = $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->inclusao( $boTransacao );
            }
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoSalarioFamilia );

        return $obErro;
    }

    public function excluirFaixaPagamento($boTransacao = "")
    {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaPagamentoSalarioFamilia.class.php" );
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoFaixaPagamentoSalarioFamilia = new TFolhaPagamentoFaixaPagamentoSalarioFamilia;
            $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->setDado("cod_regime_previdencia", $this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() );
            $obErro = $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->exclusao( $boTransacao );
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

        return $obErro;
    }

    public function listar(&$rsRecordSet, $boTransacao = "")
    {
        include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaPagamentoSalarioFamilia.class.php" );

        if ( $this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() ) {
            $stFiltro  = " AND cod_regime_previdencia = ".$this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia();
        }
        if ( $this->obRFolhaPagamentoSalarioFamilia->getTimestamp() ) {
            $stFiltro .= " AND timestamp = '".$this->obRFolhaPagamentoSalarioFamilia->getTimestamp()."'";
        }

        if ( $stFiltro )
            $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));

        $obTFolhaPagamentoFaixaPagamentoSalarioFamilia = new TFolhaPagamentoFaixaPagamentoSalarioFamilia;
        $obErro = $obTFolhaPagamentoFaixaPagamentoSalarioFamilia->recuperaTodos( $rsRecordSet, $stFiltro, "", $boTransacao );

        return $obErro;
    }

}
?>
