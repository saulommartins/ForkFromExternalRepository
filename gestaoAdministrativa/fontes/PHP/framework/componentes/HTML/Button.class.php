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
* Gerar o componente tipo button de acordo com os valores setados pelo usuário
* Data de Criação: 05/02/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de persistênsia que executa as querys mais comuns dinamicamente no banco de dados
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria
*/
class Button extends Componente
{
/**
    * Método Construtor
    * @access Public
*/
function Button()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setName      ( "button" );
    $this->setValue     ( "botao" );
    $this->setTipo      ( "button" );
    $this->setDefinicao ( "button" );
}
/**
    * Monta o HTML do Objeto Button
    * @access Protected
*/
function montaHtml()
{
    parent::montaHtml();
    $stHtml = $this->getHtml();
    $stHtml = substr( $stHtml, 0, strlen($stHtml) - 1 );
    $stHtml = $stHtml.">";
    $this->setHtml( $stHtml );
}

}

?>
