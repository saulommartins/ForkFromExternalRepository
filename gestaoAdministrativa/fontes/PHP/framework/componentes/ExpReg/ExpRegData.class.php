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
*
* Data de Criação: 26/01/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Lizandro Kirst da Silva

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/ExpReg/ExpReg.class.php';
class ExpRegData extends ExpReg
{
//METODO CONSTRUTOR
function ExpRegData($stContexto = "", $stFormato = "br")
{
    parent::ExpReg( '', $stContexto );
    $this->setFormato( $stFormato );
}

function setFormato($stFormato)
{
    $arExeReg['br'] = "[0-9][0-9]/[0-9]{2}/[0-9]{4}";
    $arExeReg['us'] = "[0-9]{4}/[0-9]{2}/[0-9][0-9]";
    $this->setExpReg( $arExeReg[$stFormato] );
}

/**
    * Modifica a data de formato brasileiro para o Americano
    * @return Array
*/
function br2us()
{
    $arData = $this->buscarOcorrencias();
    $stContexto = $this->getContexto();
    foreach ($arData AS $inIndice => $stData) {
       $arNovaData = explode("/", $stData);
       $stContexto = str_replace( $stData, $arNovaData[2]."-".$arNovaData[1]."-".$arNovaData[0], $stContexto );
    }

    return $stContexto;
}

function retornaContexto()
{
}
}
?>
