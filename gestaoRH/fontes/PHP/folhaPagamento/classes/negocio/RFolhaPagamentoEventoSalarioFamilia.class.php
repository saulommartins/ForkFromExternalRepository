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
* Classe de regra de negócio para Folha de Pagamento - Evento Salario Familia
* Data de Criação: 27/04/2006

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
    * Classe de regra de negócio para Folha de Pagamento - Evento Salario Familia
    * Data de Criação: 27/04/2006

    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra
*/
class RFolhaPagamentoEventoSalarioFamilia
{
    /**
        * @access Private
        * @var Integer
    */
    public $inCodTipoEventoSalarioFamilia;
    /**
        * @access Private
        * @var Object
    */
    public $obRFolhaPagamentoSalarioFamilia;
    /**
        * @access Private
        * @var Object
    */
    public $obRFolhaPagamentoEvento;

    /**
        * @access Public
        * @param integer $valor
    */
    public function setCodTipoEventoSalarioFamilia($valor) { $this->inCodTipoEventoSalarioFamilia = $valor; }
    /**
        * @access Public
        * @param object $valor
    */
    public function setObRFolhaPagamentoSalarioFamilia(&$valor) { $this->obRFolhaPagamentoSalarioFamilia = &$valor; }
    /**
        * @access Public
        * @param object $valor
    */
    public function setObRFolhaPagamentoEvento(&$valor) { $this->obRFolhaPagamentoEvento = &$valor; }

    /**
        * @access Public
        * @return integer
    */
    public function getCodTipoEventoSalarioFamilia() { return $this->inCodTipoEventoSalarioFamilia; }
    /**
        * @access Public
        * @return object
    */
    public function getObRFolhaPagamentoSalarioFamilia() { return $this->obRFolhaPagamentoSalarioFamilia; }
    /**
        * @access Public
        * @return object
    */
    public function getObRFolhaPagamentoEvento() { return $this->obRFolhaPagamentoEvento; }

    /**
        * Método construtor
        * @access private
    */
    public function RFolhaPagamentoEventoSalarioFamilia(&$obRFolhaPagamentoSalarioFamilia, &$obRFolhaPagamentoEvento)
    {
        $this->setObRFolhaPagamentoSalarioFamilia( $obRFolhaPagamentoSalarioFamilia );
        $this->setObRFolhaPagamentoEvento( $obRFolhaPagamentoEvento );
    }
    public function incluirEventoSalarioFamilia()
    {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamiliaEvento.class.php" );

        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoSalarioFamiliaEvento = new TFolhaPagamentoSalarioFamiliaEvento;
            $obTFolhaPagamentoSalarioFamiliaEvento->setDado( "cod_regime_previdencia" , $this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() );
            $obTFolhaPagamentoSalarioFamiliaEvento->setDado( "timestamp" , $this->obRFolhaPagamentoSalarioFamilia->getTimestamp() );
            $obTFolhaPagamentoSalarioFamiliaEvento->setDado( "cod_tipo" , $this->getCodTipoEventoSalarioFamilia() );
            $obTFolhaPagamentoSalarioFamiliaEvento->setDado( "cod_evento" , $this->obRFolhaPagamentoEvento->getCodEvento() );
            $obErro = $obTFolhaPagamentoSalarioFamiliaEvento->inclusao( $boTransacao );
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoSalarioFamilia );

        return $obErro;
    }

    public function excluirEventoSalarioFamilia($boTransacao = "")
    {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamiliaEvento.class.php" );
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoSalarioFamiliaEvento = new TFolhaPagamentoSalarioFamiliaEvento;
            $obTFolhaPagamentoSalarioFamiliaEvento->setDado("cod_regime_previdencia", $this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() );
            $obErro = $obTFolhaPagamentoSalarioFamiliaEvento->exclusao( $boTransacao );
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

        return $obErro;
    }

    public function listar(&$rsRecordSet, $boTransacao = "")
    {
        include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamiliaEvento.class.php" );

        if ( $this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia() ) {
            $stFiltro  = " AND fsfe.cod_regime_previdencia = ".$this->obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia();
        }
        if ( $this->obRFolhaPagamentoSalarioFamilia->getTimestamp() ) {
            $stFiltro .= " AND fsfe.timestamp = '".$this->obRFolhaPagamentoSalarioFamilia->getTimestamp()."'";
        }

        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));

        $obTFolhaPagamentoSalarioFamiliaEvento = new TFolhaPagamentoSalarioFamiliaEvento;
        $obErro = $obTFolhaPagamentoSalarioFamiliaEvento->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );

        return $obErro;
    }
}
?>
