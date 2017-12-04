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
    * Classe de regra de negócio das ações do assentamento
    * Data de Criação: 24/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra

    $Id: RPessoalAssentamentoMotivo.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RPessoalAssentamentoMotivo
{
    /**
        * @access Private
        * @var Integer
    */
    public $inCodMotivo;
    /**
        * @access Private
        * @var String
    */
    public $stDescricao;

    /**
        * @access Private
        * @var Integer
    */
    public $inQuantDiasOnusEmpregador;
    /**
        * @access Private
        * @var Integer
    */
    public $inQuantDiasLicencaPremio;

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodMotivo($valor) { $this->inCodMotivo = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setDescricao($valor) { $this->stDescricao = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setQuantDiasOnusEmpregador($valor) { $this->inQuantDiasOnusEmpregador = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setQuantDiasLicencaPremio($valor) { $this->inQuantDiasLicencaPremio = $valor; }
    /**
        * @access Public
        * @return Integer
    */
    public function getCodMotivo() { return $this->inCodMotivo; }
    /**
        * @access Public
        * @return String
    */
    public function getDescricao() { return $this->stDescricao; }
    /**
        * @access Public
        * @return Integer
    */
    public function getQuantDiasOnusEmpregador() { return $this->inQuantDiasOnusEmpregador; }
    /**
        * @access Public
        * @return Integer
    */
    public function getQuantDiasLicencaPremio() { return $this->inQuantDiasLicencaPremio; }

    public function RPessoalAssentamentoMotivo()
    {
    }

    public function listar(&$rsAcoes, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoMotivo.class.php" );
        $obTPessoalAssentamentoMotivo = new TPessoalAssentamentoMotivo;
        $obErro = $obTPessoalAssentamentoMotivo->recuperaTodos( $rsAcoes, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarAssentamentoMotivo(&$AssentamentoMotivo, $boTransacao = "")
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoMotivo.class.php" );
        if ( $this->getCodMotivo() ) {
            $stFiltro = " AND cod_motivo = ".$this->getCodMotivo();
        }
        if ( $this->getDescricao() ) {
            $stFiltro = " AND descricao = ".$this->getDescricao();
        }

        $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";

        $obTPessoalAssentamentoMotivo = new TPessoalAssentamentoMotivo;
        $obErro = $this->listar($AssentamentoMotivo, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }
}
?>
