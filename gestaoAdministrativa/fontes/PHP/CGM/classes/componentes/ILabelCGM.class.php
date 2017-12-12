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
    * Arquivo de label de CGM
    * Data de Criação: 07/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Revision: 10898 $
    $Name$
    $Author: diego $
    $Date: 2006-06-07 10:46:53 -0300 (Qua, 07 Jun 2006) $

    * Casos de uso: uc-01.02.92
*/

include_once ( CLA_LABEL );

class  ILabelCGM extends Label
{
    public $inNumCGM;

    public function ILabelCGM()
    {
        parent::Label();
        $this->setRotulo('CGM');
    }
    public function setNumCGM($inValor) { $this->inNumCGM = $inValor ; }

    public function montaHTML()
    {
        if ( !$this->getValue() ) {
            include_once(TCGM."TCGM.class.php");
            $obTCGM = new TCGM();
            $obTCGM->setDado('numcgm', $this->inNumCGM );
            $obTCGM->recuperaPorChave($rsRecordSet);

            $this->setValue( $rsRecordSet->getCampo('numcgm').' - '.$rsRecordSet->getCampo('nom_cgm') );
        }

        parent::montaHTML();
    }
}
?>
