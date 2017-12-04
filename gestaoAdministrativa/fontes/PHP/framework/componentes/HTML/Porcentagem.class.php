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
* Gerar o componente tipo text que formate seu valor como moeda
* Data de Criação: 15/09/2006

* @author Desenvolvedor: Eduardo Martins

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    *  Classe que gera o HTML do text de Porcentagem

    * @package framework
    * @subpackage componentes
*/

class Porcentagem extends Numerico
{
/**
    * Método construtor
    * @access Public
*/
function Porcentagem()
{
    parent::Numerico();
    $this->setName      ( "porcentagem" );
    $this->setMaxLength ( 6 );
    $this->setSize      ( 4 );
    $this->setMinValue  ( 0 );
    $this->setDecimais  ( 2 );
    $this->setMaxValue  ( 100 );
    $this->setNegativo  ( false );
}

/**
    * Monta o HTML do Objeto Numerico
    * @access Protected
*/
function montaHTML()
{
    parent::montaHTML();
    $this->stHtml .= '%';
}

}
?>
