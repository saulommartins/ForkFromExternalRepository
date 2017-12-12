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
* Gerar o componente tipo text que formate seu valor como data
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML do text de Data

    * @package framework
    * @subpackage componentes
*/
class Data extends TextBox
{
/**
    * Método Construtor
    * @access Public
*/
function Data()
{
    parent::TextBox();
    $this->setName      ( "data" );
    $this->setInteiro   ( false );
    $this->setMaxLength ( 10 );
    $this->setSize      ( 10 );
    $this->setDefinicao ( "data" );
    $this->obEvento->setOnKeyUp("mascaraData(this, event);");
    $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress()."return inteiro( event );" );
    $this->obEvento->setOnBlur("if ( !verificaData( this ) ) { this.value = '';}");
}

}
?>
