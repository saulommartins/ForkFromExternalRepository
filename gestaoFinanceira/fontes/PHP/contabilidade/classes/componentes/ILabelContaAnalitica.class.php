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
    * Data de Criação: 29/10/2007

    * @author Analista: Anderson Konze
    * @author Desenvolvedor: Leopoldo Braga Barreiro

    * $Id: ILabelContaAnalitica.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.35
*/

include_once( CLA_LABEL );

class ILabelContaAnalitica extends Label
{
    public $obForm;
    public $inCodPlano;
    public $stExercicio;
    public $obHdnCodPlano;
    public $boMostraCodigo = false;

    public function ILabelContaAnalitica($obForm)
    {
        parent::Label();

        $this->obForm = $obForm;
        $this->obHdnCodPlano = new Hidden;
        $this->setRotulo ("Conta Analítica");
        $this->setName ("stContaAnalitica" );
        $this->setId ("stContaAnalitica" );
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

    public function montaHTML($stNameField='')
    {
        include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;

        if ( $this->getCodPlano() ) {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $this->getCodPlano() );
        }
        if ( $this->getExercicio() ) {
            $obRContabilidadePlanoContaAnalitica->setExercicio( $this->getExercicio() );
        }

        //$obRContabilidadePlanoContaAnalitica->listarContasBancos( $rsRecordSet );
        $obRContabilidadePlanoContaAnalitica->listarPlanoConta( $rsRecordSet );

        if ($this->boMostraCodigo) {
            $this->setValue ( $rsRecordSet->getCampo('cod_plano').' - '.$rsRecordSet->getCampo('nom_conta') );
        } else {
            $this->setValue ( $rsRecordSet->getCampo('nom_conta') );
        }
        $this->obHdnCodPlano->setValue( $rsRecordSet->getCampo( 'cod_plano' ) );
        if ( strlen($stNameField) > 0 ) {
            $this->obHdnCodPlano->setName ( $stNameField );
        } else {
            $this->obHdnCodPlano->setName ( 'inCodPlano' );
        }
        parent::montaHTML();
    }
}
?>
