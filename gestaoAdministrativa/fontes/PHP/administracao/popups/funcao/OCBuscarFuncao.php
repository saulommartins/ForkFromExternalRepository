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
    * Frame Oculto da Popup de busca de função
    * Data de Criação: 19/09/2006

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27011 $
$Name$
$Author: vitorhugo $
$Date: 2007-12-03 16:48:37 -0200 (Seg, 03 Dez 2007) $

Casos de uso: uc-01.03.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function preencherBiblioteca($stExtencao="")
{
    include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoBiblioteca.class.php");
    $obTAdminsitracaoBiblioteca = new TAdministracaoBiblioteca();

    if ($_REQUEST['inCodBiblioteca']) {
       $stFiltro = " WHERE cod_modulo IN ( ".$_GET['inCodModulo'.$stExtencao]." ) AND cod_biblioteca IN ( ".$_REQUEST['inCodBiblioteca']." )";
    } else {
       $stFiltro = " WHERE cod_modulo = ".$_GET['inCodModulo'.$stExtencao];
    }
    $rsBiblioteca = new RecordSet();
    if ($_GET['inCodModulo'.$stExtencao]) {
        $obTAdminsitracaoBiblioteca->recuperaTodos($rsBiblioteca,$stFiltro,"cod_biblioteca");
    }

    $stJs .= " jQuery(\"#inCodBiblioteca\").empty().append(new Option(\"Selecione\",\"\")); \n";
    
    foreach($rsBiblioteca->getElementos() as $value ) {
        $stJs .= "jQuery(\"#inCodBiblioteca\").append( new Option(\"".$value["nom_biblioteca"]."\", \"".$value["cod_biblioteca"]."\") ); \n";
    }
    $stJs .= " jQuery(\"#inCodBiblioteca\").val(\"".$_REQUEST['inCodBiblioteca']."\"); \n";

    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    case "preencherBiblioteca":
            $stJs = preencherBiblioteca();
    break;
}

if ($stJs != "") {
    echo $stJs;
}
?>
