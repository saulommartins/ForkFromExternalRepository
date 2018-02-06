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
    * Data de Criação  : 13/12/2017

    * @author Analista/Desenvolvedor Jhonatan H. D. Machado

    * @package URBEM
    * @subpackage

    * $Id: ISelectEntidade.class.php

    * Casos de uso: --
*/

require_once CLA_SELECT;

class ISelectEntidade extends Select
{
    public $stExercicio;

    public function ISelectEntidade()
    {
        parent::Select();

        $this->setName      ( 'inCodEntidade' );
        $this->setId        ( 'inCodEntidade' );
        $this->setValue     ( '' );
        $this->setRotulo    ( 'Entidade' );
        $this->setTitle     ( 'Selecione a entidade.' );
        $this->setNull      ( true );
        $this->setCampoId   ( "num_entidade" );
        $this->setCampoDesc ( "[num_entidade] - [nom_entidade]" );
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

        $rsEntidade = new RecordSet;
        require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" ;
        $obTOrcamentoEntidade = new TOrcamentoEntidade;
        $obTOrcamentoEntidade->setDado('exercicio', $this->getExercicio() );
        $obTOrcamentoEntidade->recuperaDadosExercicio( $rsEntidade, '', 'orcamento.entidade.num_entidade' );
        $this->preencheCombo( $rsEntidade );
        parent::montaHTML();
    }

}
?>
