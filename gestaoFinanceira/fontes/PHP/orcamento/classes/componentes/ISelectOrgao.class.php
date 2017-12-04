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
    * Arquivo de textbox e select orgao
    * Data de Criação  : 15/05/2008

    * @author Analista Gelson W. Golçalves
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * $Id: ISelectOrgao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: --
*/

require_once CLA_SELECT;

class ISelectOrgao extends Select
{
    public $stExercicio;

    public function ISelectOrgao()
    {
        parent::Select();

        $this->setName      ( 'inCodOrgao' );
        $this->setId        ( 'inCodOrgao' );
        $this->setValue     ( '' );
        $this->setRotulo    ( 'Órgao' );
        $this->setTitle     ( 'Selecione o órgao.' );
        $this->setNull      ( true );
        $this->setCampoId   ( "num_orgao" );
        $this->setCampoDesc ( "[num_orgao] - [nom_orgao]" );
        $this->addOption    ( "", "Selecione" );
    }

    public function setExercicio($stExercicio)
    {
        $this->stExercicio = $stExercicio;
    }

    public function getExercicio()
    {
       return $this->stExercicio;
    }

    public function montaHTML()
    {

        $rsOrgao = new RecordSet;
        require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php" ;
        $obTOrcamentoOrgao = new TOrcamentoOrgao;
        $obTOrcamentoOrgao->setDado('exercicio', $this->getExercicio() );
        $obTOrcamentoOrgao->recuperaDadosExercicio( $rsOrgao, '', 'orcamento.orgao.num_orgao' );
        $this->preencheCombo( $rsOrgao );
        parent::montaHTML();
    }

}
?>
