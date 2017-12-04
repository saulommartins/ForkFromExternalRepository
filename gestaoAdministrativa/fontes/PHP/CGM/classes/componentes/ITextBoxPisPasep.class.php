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
    * Data de Criação: 03/03/2008

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

include_once ( CLA_TEXTBOX );

class  ITextBoxPisPasep extends TextBox
{
    public function ITextBoxPisPasep()
    {
        parent::TextBox();
        $this->setRotulo    ('PIS/PASEP');
        $this->setTitle     ('Informe o número do PIS/PASEP.');
        $this->setName      ( "inNumeroPisPasep" );
        $this->setId        ( "inNumeroPisPasep" );
        $this->setAlign     ("left");
        $this->setMascara   ( '999.99999.99-9' );

    }
    public function setNumero($inValor) { $this->setValue( $inValor ); }

    public function getNumero() { $this->getValue(); }
}
?>
