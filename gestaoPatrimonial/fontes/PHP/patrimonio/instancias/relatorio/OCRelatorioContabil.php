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
  * Página de frame oculto para relatório de contabil
  * Data de Criação: 23/04/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    $Id: OCRelatorioContabil.php 62331 2015-04-24 14:39:45Z michel $

    * Casos de uso: uc-03.01.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function validaExercicio($stExercicio){
    if($stExercicio){        
        if($stExercicio > Sessao::getExercicio()){
            $stJs  = "jq('#stExercicio').val('".Sessao::getExercicio()."');\n";
            $stJs .= "alertaAviso('@Campo Exercício Inválido()','form','erro','".Sessao::getId()."');";
        }
    }else{
        $stJs  = "jq('#stExercicio').val('".Sessao::getExercicio()."');\n";
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {

    case 'validaExercicio':
        $stJs  = validaExercicio($_REQUEST['stExercicio']);
    break;
}

if($stJs)
    echo $stJs;
