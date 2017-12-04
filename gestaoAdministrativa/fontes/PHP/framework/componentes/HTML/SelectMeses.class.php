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
* Classe Select com uma lista de Meses do ano
* Data de Criação: 04/11/2004

* @author Desenvolvedor: Eduardo Martins

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe responsável por conter atributos e métodos necessários à classe Lista
    * Data de Criação   : 02/04/2004
    * @author Diego Barbosa Victoria
*/
class SelectMeses extends Select
{
/**
    * Método Construtor
    * @access Public
*/
function SelectMeses()
{
    parent::Select();

    $arMes   = array ("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril",   "Maio",     "Junho",
                       "Julho",  "Agosto",    "Setembro",     "Outubro", "Novembro", "Dezembro");

    $this->addOption( "", "Selecione" );
    for ($inIndice = 0; $inIndice<=(count($arMes)-1); $inIndice++) {
        $this->addOption( ($inIndice+1), $arMes[$inIndice] );
    }
}
}
?>
