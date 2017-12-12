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
* Montar o HTML de um innerformulario de acordo com os valores setados pelo usuário
* Data de Criação: 10/03/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once '../../../includes/Constante.inc.php';
include_once ( CLA_TABELA );
include_once ( CLA_FORM );

/**
    * Classe que gera o HTML do innerformulario

    * @package framework
    * @subpackage componentes
*/
class InnerFormulario extends Formulario
{
/**
    * Método construtor
    * @access Public
*/
function InnerFormulario()
{
    parent::Formulario();
}

/**
    * Monta o HTML do Objeto InnerFormulario
    * @access Protected
*/
function montaHTML()
{
    $stHtml = "";
    $arHidden = $this->getHidden();
    if ( count( $arHidden ) ) {
        foreach ($arHidden as $obHidden) {
            $obHidden->montaHTML();
            $stHtml .= $obHidden->getHTML()."\n";
        }
    }
    parent::montaHTML();
    $stHtml .= parent::getHTML();
    parent::setHTML( $stHtml );
}

}
?>
