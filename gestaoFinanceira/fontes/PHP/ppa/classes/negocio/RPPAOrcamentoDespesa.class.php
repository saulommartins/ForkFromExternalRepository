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
    * Classe de Regra de Negócio para Despesa
    * Data de Criação: 26/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_PPA_NEGOCIO."RPPAOrcamentoUnidadeOrcamentaria.class.php");

class ROrcamentoDespesa
{
var $obROrcamentoUnidadeOrcamentaria;

var $stExercicio;

function setROrcamentoUnidadeOrcamentaria($valor) {  $this->obROrcamentoUnidadeOrcamentaria  = $valor; }
function setExercicio($valor) {                      $this->stExercicio                      = $valor; }
function getROrcamentoUnidadeOrcamentaria() { return $this->obROrcamentoUnidadeOrcamentaria;           }
function getExercicio() {                     return $this->stExercicio;                               }

function ROrcamentoDespesa()
{
    $this->setROrcamentoUnidadeOrcamentaria( new ROrcamentoUnidadeOrcamentaria  );
    $this->setExercicio(                     Sessao::getExercicio()             );
}

}
