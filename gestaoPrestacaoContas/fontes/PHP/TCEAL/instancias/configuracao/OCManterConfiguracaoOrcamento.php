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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 22984 $
$Name$
$Author: andre.almeida $
$Date: 2007-05-30 18:19:19 -0300 (Qua, 30 Mai 2007) $
$Id: OCManterConfiguracaoOrcamento.php 60639 2014-11-05 12:33:42Z michel $
Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GA_NORMAS_NEGOCIO . "RNorma.class.php"    );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
include_once (CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RNorma;
$rsAtributos = new RecordSet;

// Acoes por pagina
switch ($stCtrl) {
    case "PreencheNorma":
        $obTNorma = new TNorma;

        if (is_numeric($_REQUEST['inCodNorma']) && isset($_REQUEST['inCodNorma'])) {

            $stFiltro = ' WHERE N.cod_norma='.$_REQUEST['inCodNorma'].' ';
            $obTNorma->recuperaNormasDecreto($rsNorma, $stFiltro);
            
            if ( $rsNorma->getNumLinhas()>0 ) {
                $stJs = "f.inCodNorma.value = '". $rsNorma->getCampo("cod_norma")."';\n";
                $stJs .= "f.stComplementacaoLoa.focus();\n";
                $stJs .= "d.getElementById('stNomTipoNorma').innerHTML ='".$rsNorma->getCampo('nom_tipo_norma').' '.$rsNorma->getCampo('num_norma_exercicio').' - '.$rsNorma->getCampo('nom_norma')."';\n";
            } else {
                $stJs = "f.inCodNorma.value = '';\n";
                $stJs .= "d.getElementById('stNomTipoNorma').innerHTML ='&nbsp;';\n";
                $stJs .= "alertaAviso('@Código da Lei/Decreto informado não existe. (".$_REQUEST['inCodNorma'].")','form','erro','".Sessao::getId()."');\n";
            }
        } else {
             $stJs = "f.inCodNorma.value = '';\n";
             $stJs .= "d.getElementById('stNomTipoNorma').innerHTML ='&nbsp;';\n";
        }
       sistemaLegado::executaFrameOculto($stJs); 
    break;
}
?>
