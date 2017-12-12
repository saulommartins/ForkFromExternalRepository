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
* Classe de Exercicio
* Data de Criação: 08/02/2003

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera o HTML do Exercicio

    * @package framework
    * @subpackage componentes
*/

include_once(CLA_NUMERICO);
class ValorUnitario extends Numerico
{
/**
    * Método Construtor
    * @access Public
*/
function ValorUnitario()
{
    parent::Numerico();
    $this->setName     ( "nuVlUnitario" );
    $this->setId       ( "nuVlUnitario" );
    $this->setRotulo   ( "*Valor Unitario" );
    $this->setTitle    ( "Informe o valor unitário com até quatro dígitos." );
    $this->setNull     ( true );
    $this->setSize     ( 23 );
    $this->setMaxLength( 21 );

    $this->setDecimais(4);
}

}
?>
