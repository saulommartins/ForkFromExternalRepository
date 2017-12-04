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

  * Classe de Regra do Relatório de Apuração de Superavit/Deficit Financeiro
  * Data de Criação: 11/12/2015

  * @author Analista:      Valtair
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  * $Id: RContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro.class.php 64186 2015-12-11 20:36:20Z franver $
  * $Date: 2015-12-11 18:36:20 -0200 (Fri, 11 Dec 2015) $
  * $Author: franver $
  * $Rev: 64186 $
   *
*/

require_once TCON.'TContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro.class.php';

class RContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro {
    /**
    * @var Array
    * @access Private
    */
    var $arCodEntidades;
    /**
    * @var String
    * @access Private
    */
    var $stExercicio;
    public function getCodEntidades() { return $this->arCodEntidades; }
    public function setCodEntidades( $arCodEntidades ) { $this->arCodEntidades = $arCodEntidades; }

    public function getExercicio() { return $this->stExercicio; }
    public function setExercicio( $stExercicio ) { $this->stExercicio = $stExercicio; }

    /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
        
    }

    /**
    * Método abstrato
    * @access Public
    */
    function geraRecordSet(&$rsRecordSet , $stOrder = "")
    {
        $rsApuracaoContabilidade = new RecordSet();
        $rsApuracaoExecucao      = new RecordSet();
        
        $obApuracaoSuperavitDeficit = new TContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro();
        $obApuracaoSuperavitDeficit->setDado('exercicio'    , $this->getExercicio()   );
        $obApuracaoSuperavitDeficit->setDado('cod_entidades', $this->getCodEntidades());
        $obApuracaoSuperavitDeficit->recuperaApuracaoContabilidade($rsApuracaoContabilidade, $stFiltro, $stOrdem, $boTransacao);
        $obApuracaoSuperavitDeficit->recuperaApuracaoExecucao($rsApuracaoExecucao, $stFiltro, $stOrdem, $boTransacao);
        
        $rsRecordSet['arApuracaoContabilidade'] = $rsApuracaoContabilidade->getElementos();
        $rsRecordSet['arApuracaoExecucao']      = $rsApuracaoExecucao->getElementos();
        
        // Somatório de todos os Recursos por coluna.
        $vlSomatorioAtivo     = $rsApuracaoExecucao->getSomaCampo('valor_ativo');
        $vlSomatorioPassivo   = $rsApuracaoExecucao->getSomaCampo('valor_passivo');
        $vlSomatorioSuperavit = $rsApuracaoExecucao->getSomaCampo('superavit');
        $vlSomatorioDeficit   = $rsApuracaoExecucao->getSomaCampo('deficit');
        
        $rsRecordSet['arApuracaoExecucaoTotal'] = array( 'valor_ativo'   => $vlSomatorioAtivo['valor_ativo'],
                                                         'valor_passivo' => $vlSomatorioPassivo['valor_passivo'],
                                                         'superavit'     => $vlSomatorioSuperavit['superavit'],
                                                         'deficit'       => $vlSomatorioDeficit['deficit']);
    }
}

?>