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
    * Data de Criação: 19/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage

    $Revision: 12532 $
    $Name$
    $Author: tonismar $
    $Date: 2006-07-12 14:33:26 -0300 (Qua, 12 Jul 2006) $

    * Casos de uso: uc-03.03.16
*/

/*
$Log$
Revision 1.4  2006/07/12 17:33:26  tonismar
adicionado o método, setMostraCodigo para mostrar ou não o código ao lado do nome do almoxarifado

Revision 1.3  2006/07/06 14:04:38  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:09:20  diego

*/

include_once( CLA_LABEL );

class ILabelAlmoxarifado extends Label
{
    public $obForm;
    public $inCodAlmoxarifado;
    public $obHdnCodAlmoxarifado;
    public $boMostraCodigo = false;

    public function ILabelAlmoxarifado($obForm)
    {
        parent::Label();

        $this->obForm = $obForm;
        $this->obHdnCodAlmoxarifado = new Hidden;
        $this->setRotulo ("Almoxarifado" );
        $this->setName   ("stAlmoxarifado" );
        $this->setId     ("stAlmoxarifado" );
    }

    public function setCodAlmoxarifado($value)
    {
        $this->inCodAlmoxarifado = $value;
    }

    public function getCodAlmoxarifado()
    {
        return $this->inCodAlmoxarifado;
    }

    public function setMostraCodigo($valor)
    {
        $this->boMostraCodigo = $valor;
    }

    public function montaHTML()
    {
        include_once(CAM_GP_ALM_MAPEAMENTO . "TAlmoxarifadoAlmoxarifado.class.php");
        $obTMapeamento          = new TAlmoxarifadoAlmoxarifado();
        $rsRecordSet            = new Recordset;
        $stFiltro               = '';

        if ( $this->getCodAlmoxarifado() ) {
            $stFiltro = " and cod_almoxarifado = ".$this->getCodAlmoxarifado();
        }

        $obTMapeamento->recuperaAlmoxarifados($rsRecordSet, $stFiltro,' ORDER BY cod_almoxarifado');

        if ($this->boMostraCodigo) {
            $this->setValue ( $rsRecordSet->getCampo('cod_almoxarifado').'-'.$rsRecordSet->getCampo('nom_cgm') );
        } else {
            $this->setValue ( $rsRecordSet->getCampo('nom_cgm') );
        }
        $this->obHdnCodAlmoxarifado->setValue( $rsRecordSet->getCampo( 'cod_almoxarifado' ) );
        $this->obHdnCodAlmoxarifado->setName ( 'inCodAlmoxarifado' );

        parent::montaHTML();

    }
}
?>
