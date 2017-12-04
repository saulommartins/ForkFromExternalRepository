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
    * Arquivo de textbox e select unidade
    * Data de Criação  : 15/05/2008

    * @author Analista Gelson W. Golçalves
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * $Id: ISelectUnidade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: --
*/

require_once CLA_SELECT;

class ISelectUnidade extends Select
{
    public $stExercicio;
    public $inCodUnidade;

    public function ISelectUnidade()
    {
        parent::Select();

        $this->setId        ( 'inCodUnidade' );
        $this->setName      ( 'inCodUnidade' );
        $this->setValue     ( '' );
        $this->setRotulo    ( 'Unidade' );
        $this->setTitle     ( 'Selecione a unidade.' );
        $this->setNull      ( true );
        $this->setCampoId   ( "num_unidade" );
        $this->setCampoDesc ( "[num_unidade] - [nom_unidade]" );
        $this->addOption    ( "", "Selecione" );
    }

    public function setExercicio($stExercicio) { $this->stExercicio = $stExercicio; }
    public function getExercicio() { return $this->stExercicio; }

    public function setNumOrgao($inNumOrgao) { $this->inNumOrgao = $inNumOrgao; }
    public function getNumOrgao() { return $this->inNumOrgao; }

    public function getRecordSet()
    {
        $rsUnidade = new RecordSet;
        if ($this->getExercicio() != "" && $this->getNUmOrgao() != "") {
            require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" ;
            $obTOrcamentoUnidade = new TOrcamentoUnidade;
            $stFiltro  = "    AND    orcamento.unidade.exercicio = '".$this->getExercicio()."'";
            $stFiltro .= "\n    AND  orcamento.orgao.num_orgao = ".$this->getNUmOrgao();
             $obTOrcamentoUnidade->recuperaRelacionamento( $rsUnidade, $stFiltro, 'orcamento.unidade.num_unidade' );
        }

        return $rsUnidade;
    }

    public function montaHTML()
    {

        $this->preencheCombo( $this->getRecordSet() );
        parent::montaHTML();
    }
}
?>
