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
    * Arquivo de popup de busca de CGM
    * Data de Criação: 29/08/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage

    * $Id: ILabelContaBanco.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.00, uc-02.04.04
*/

include_once( CLA_LABEL );

class ILabelContaBanco extends Label
{
    public $obForm;
    public $inCodPlano;
    public $stExercicio;
    public $obHdnCodPlano;
    public $boMostraCodigo = false;

    public function ILabelContaBanco($obForm)
    {
        parent::Label();

        $this->obForm = $obForm;
        $this->obHdnCodPlano = new Hidden;
        $this->setRotulo ("Conta");
        $this->setName   ("stConta" );
        $this->setId     ("stConta" );
        $this->stExercicio = Sessao::getExercicio();
    }

    public function setCodPlano($value)
    {
        $this->inCodPlano = $value;
    }

    public function getCodPlano()
    {
        return $this->inCodPlano;
    }

    public function setExercicio($value)
    {
        $this->stExercicio = $value;
    }

    public function getExercicio()
    {
        return $this->stExercicio;
    }

    public function setMostraCodigo($valor)
    {
        $this->boMostraCodigo = $valor;
    }

    public function montaHTML()
    {
        include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php"    );
        $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

        if ( $this->getCodPlano() ) {
            $obRContabilidadePlanoBanco->setCodPlano($this->getCodPlano());
        }
        if ( $this->getExercicio() ) {
            $obRContabilidadePlanoBanco->setExercicio($this->getExercicio());
        }

        $obRContabilidadePlanoBanco->listarContasBancos($rsRecordSet);

        if ($this->boMostraCodigo) {
            $this->setValue ( $rsRecordSet->getCampo('cod_plano').' - '.$rsRecordSet->getCampo('nom_conta') );
        } else {
            $this->setValue ( $rsRecordSet->getCampo('nom_conta') );
        }
        $this->obHdnCodPlano->setValue( $rsRecordSet->getCampo( 'cod_plano' ) );
        $this->obHdnCodPlano->setName ( 'inCodPlano' );

        parent::montaHTML();
    }
}
?>
