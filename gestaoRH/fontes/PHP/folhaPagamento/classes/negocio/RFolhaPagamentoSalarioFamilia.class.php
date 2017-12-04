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
* Classe de regra de negócio para Folha de Pagamento - Salário Família
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

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFaixaPagamento.class.php"                        );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEventoSalarioFamilia.class.php"                  );

/**
    * Classe de regra de negócio para Folha de Pagamento - Salário Família
    * Data de Criação: 19/04/2006

    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra
*/
class RFolhaPagamentoSalarioFamilia
{
    /**
        * @access Private
        * @var String
    */
    public $stTimestamp;
    /**
        * @access Private
        * @var String
    */
    public $stVigencia;
    /**
        * @access Private
        * @var Integer
    */
    public $inIdadeLimite;
    /**
        * @access Private
        * @var Array
    */
    public $arRFolhaPagamentoFaixaPagamento;
    /**
        * @access Private
        * @var Object
    */
    public $roRFolhaPagamentoFaixaPagamento;
    /**
        * @access Private
        * @var Array
    */
    public $arRFolhaPagamentoEvento;
    /**
        * @access Private
        * @var Object
    */
    public $roRFolhaPagamentoEvento;
    /**
        * @access Private
        * @var Array
    */
    public $arRFolhaPagamentoEventoSalarioFamilia;
    /**
        * @access Private
        * @var Object
    */
    public $roRFolhaPagamentoEventoSalarioFamilia;
    /**
        * @access Private
        * @var Object
    */
    public $obRFolhaPagamentoPrevidencia;

    /**
        * @access Public
        * @param String $valor
    */
    public function setTimestamp($valor) { $this->stTimestamp = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setVigencia($valor) { $this->stVigencia = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setIdadeLimite($valor) { $this->inIdadeLimite = $valor; }

    /**
        * @access Public
        * @return String
    */
    public function getTimestamp() { return $this->stTimestamp; }
    /**
        * @access Public
        * @return String
    */
    public function getVigencia() { return $this->stVigencia; }
    /**
        * @access Public
        * @return String
    */
    public function getIdadeLimite() { return $this->inIdadeLimite; }

    /**
        * Método construtor
        * @access Private
    */
    public function RFolhaPagamentoSalarioFamilia()
    {
        $this->obRFolhaPagamentoPrevidencia = new RFolhaPagamentoPrevidencia;
    }

    public function addRFolhaPagamentoFaixaPagamento()
    {
        $this->arRFolhaPagamentoFaixaPagamento[] = new RFolhaPagamentoFaixaPagamento( $this );
        $this->roRFolhaPagamentoFaixaPagamento   = &$this->arRFolhaPagamentoFaixaPagamento[count($this->arRFolhaPagamentoFaixaPagamento)-1];
    }

    public function addRFolhaPagamentoEvento()
    {
        $this->arFolhaPagamentoEvento[] = new RFolhaPagamentoEvento;
        $this->roFolhaPagamentoEvento   = &$this->arFolhaPagamentoEvento[count($this->arFolhaPagamentoEvento)-1];
        $this->arRFolhaPagamentoEventoSalarioFamilia[] = new RFolhaPagamentoEventoSalarioFamilia( $this, $this->arFolhaPagamentoEvento[count($this->arFolhaPagamentoEvento)-1] );
        $this->roRFolhaPagamentoEventoSalarioFamilia   =  &$this->arRFolhaPagamentoEventoSalarioFamilia[count($this->arRFolhaPagamentoEventoSalarioFamilia)-1];
    }

    public function incluirSalarioFamilia()
    {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php" );

        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoSalarioFamilia = new TFolhaPagamentoSalarioFamilia;
            $obTFolhaPagamentoSalarioFamilia->setDado( "cod_regime_previdencia" , $this->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() );
            $obTFolhaPagamentoSalarioFamilia->setDado( "vigencia" , $this->getVigencia() );
            $obTFolhaPagamentoSalarioFamilia->setDado( "idade_limite" , $this->getIdadeLimite() );
            $obErro = $obTFolhaPagamentoSalarioFamilia->inclusao( $boTransacao );
            $obTFolhaPagamentoSalarioFamilia->recuperaNow3( $stTimestamp, $boTransacao );
            $this->setTimestamp($stTimestamp);
        }
        if ( !$obErro->ocorreu() ) {
            for ( $i=0 ; $i<count($this->arRFolhaPagamentoEventoSalarioFamilia) ; $i++ ) {
                $this->arRFolhaPagamentoEventoSalarioFamilia[$i]->incluirEventoSalarioFamilia();
            }
        }
        if ( !$obErro->ocorreu() ) {
            for ( $i=0 ; $i<count($this->arRFolhaPagamentoFaixaPagamento) ; $i++ ) {
                $this->arRFolhaPagamentoFaixaPagamento[$i]->incluirFaixaPagamento();
            }
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoSalarioFamilia );

        return $obErro;
    }

    public function excluirSalarioFamilia($boTransacao = "")
    {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php" );
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoSalarioFamilia = new TFolhaPagamentoSalarioFamilia;
            $obTFolhaPagamentoSalarioFamilia->setDado("cod_regime_previdencia", $this->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() );
            $obRFolhaPagamentoFaixaPagamento = new RFolhaPagamentoFaixaPagamento( $this );
            $obErro = $obRFolhaPagamentoFaixaPagamento->excluirFaixaPagamento( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoEventoSalarioFamilia = new RFolhaPagamentoEventoSalarioFamilia( $this, new RFolhaPagamentoEvento );
            $obErro = $obRFolhaPagamentoEventoSalarioFamilia->excluirEventoSalarioFamilia( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTFolhaPagamentoSalarioFamilia->exclusao( $boTransacao );
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

        return $obErro;
    }

    public function listarSalarioFamilia(&$rsRecordSet, $boTransacao = "")
    {
        $inCodRegimePrevidencia = $this->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia();

        include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php" );

        $obTFolhaPagamentoSalarioFamilia = new TFolhaPagamentoSalarioFamilia;

        $stOrdem = " ORDER BY fsf.cod_regime_previdencia, fsf.vigencia ";

        if ( $this->getVigencia() ) {
            $obTFolhaPagamentoSalarioFamilia->setDado("vigencia", $this->getVigencia() );
            if ($inCodRegimePrevidencia != '') {
                $obTFolhaPagamentoSalarioFamilia->setDado("cod_regime_previdencia", $this->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() );
            }
            $obErro = $obTFolhaPagamentoSalarioFamilia->recuperaSalarioFamiliaVigencia( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        } else {
            if ($inCodRegimePrevidencia != '') {
                $stFiltro  = " WHERE fsf.cod_regime_previdencia = ".$inCodRegimePrevidencia;
            }
            $obErro = $obTFolhaPagamentoSalarioFamilia->recuperaSalarioFamiliaMaxTimestamp( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        }

        return $obErro;
    }

    public function consultarSalarioFamilia($boTransacao = "")
    {
        include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php" );

        $stFiltro  = " WHERE fsf.cod_regime_previdencia = ".$this->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia();
        $stFiltro .= " AND fsf.timestamp = '".$this->getTimestamp()."'";
        $obTFolhaPagamentoSalarioFamilia = new TFolhaPagamentoSalarioFamilia;
        $obErro = $obTFolhaPagamentoSalarioFamilia->recuperaRelacionamento( $rsSalarioFamilia, $stFiltro, "", $boTransacao );

        $this->setVigencia   ( $rsSalarioFamilia->getCampo("vigencia")     );
        $this->setIdadeLimite( $rsSalarioFamilia->getCampo("idade_limite") );

        $this->obRFolhaPagamentoPrevidencia->setRegimePrevidenciario( $rsSalarioFamilia->getCampo("descricao_regime_previdencia") );

        $obRFolhaPagamentoEventoSalarioFamilia = new RFolhaPagamentoEventoSalarioFamilia( $this,  new RFolhaPagamentoEvento );
        $obErro = $obRFolhaPagamentoEventoSalarioFamilia->listar( $rsEventosSalarioFamilia, $boTransacao = "" );

        $rsEventosSalarioFamilia->setPrimeiroElemento();
        for ( $i=0 ; $i<$rsEventosSalarioFamilia->getNumLinhas() ; $i++ ) {
            $this->addRFolhaPagamentoEvento();
            $this->roRFolhaPagamentoEventoSalarioFamilia->setCodTipoEventoSalarioFamilia( $rsEventosSalarioFamilia->getCampo("cod_tipo") );
            $this->roFolhaPagamentoEvento->setCodEvento ( $rsEventosSalarioFamilia->getCampo("cod_evento")       );
            $this->roFolhaPagamentoEvento->setCodigo    ( $rsEventosSalarioFamilia->getCampo("codigo")           );
            $this->roFolhaPagamentoEvento->setDescricao ( $rsEventosSalarioFamilia->getCampo("descricao_evento") );
            $rsEventosSalarioFamilia->proximo();
        }

        $obRFolhaPagamentoFaixaPagamento = new RFolhaPagamentoFaixaPagamento( $this );
        $obErro = $obRFolhaPagamentoFaixaPagamento->listar( $rsFaixasPagamento, $boTransacao = "" );

        $rsFaixasPagamento->setPrimeiroElemento();
        for ( $i=0 ; $i<$rsFaixasPagamento->getNumLinhas() ; $i++ ) {
            $this->addRFolhaPagamentoFaixaPagamento();
            $this->roRFolhaPagamentoFaixaPagamento->setCodFaixa    ( $rsFaixasPagamento->getCampo("cod_faixa")    );
            $this->roRFolhaPagamentoFaixaPagamento->setVlInicial   ( $rsFaixasPagamento->getCampo("vl_inicial")   );
            $this->roRFolhaPagamentoFaixaPagamento->setVlFinal     ( $rsFaixasPagamento->getCampo("vl_final")     );
            $this->roRFolhaPagamentoFaixaPagamento->setVlPagamento ( $rsFaixasPagamento->getCampo("vl_pagamento") );
            $rsFaixasPagamento->proximo();
        }

        return $obErro;
    }
}
?>
