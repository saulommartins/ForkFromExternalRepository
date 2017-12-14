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
    * Página de Oculto de Replicar Função
    * Data de Criação: 19/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-01.03.95

    $Id: OCReplicarFuncao.php 63910 2015-11-05 16:45:47Z evandro $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"                              );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function preencherBiblioteca($stExtencao="")
{
    if (!empty($_REQUEST['inCodModulo'.$stExtencao])) {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoBiblioteca.class.php");
        $obTAdminsitracaoBiblioteca = new TAdministracaoBiblioteca();
        $stFiltro = " WHERE cod_modulo = ".$_REQUEST['inCodModulo'.$stExtencao];
        $obTAdminsitracaoBiblioteca->recuperaTodos($rsBiblioteca,$stFiltro,"nom_biblioteca");        
        $stJs .= " jq(\"#inCodBiblioteca".$stExtencao."\").empty().append(new Option(\"Selecione\",\"\") ); \n";
        foreach ($rsBiblioteca->getElementos() as $value) {
            $stJs .= " jq(\"#inCodBiblioteca".$stExtencao."\").append(new Option(\"".$value['nom_biblioteca']."\",\"".$value['cod_biblioteca']."\") ); \n";
        }

        return $stJs;
    }
}

function buscaFuncao($boExecuta = false, $stCodFuncao = '')
{
    $stJs = '';

    if ($stCodFuncao) {
        $arCodFuncao = explode('.', $stCodFuncao );
    } else {
        if ($_POST['inCodFuncao']) {
            $arCodFuncao = explode('.',$_POST['inCodFuncao'])  ;
        }
    }

    if ($arCodFuncao && $arCodFuncao[0]==$_REQUEST['inCodModulo'] && $arCodFuncao[1]==$_REQUEST['inCodBiblioteca']) {
       $obRFuncao = new RFuncao;
       $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
       $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
       $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
       $obRFuncao->consultar();
       $stNomeFuncao = $obRFuncao->getNomeFuncao();
       if ( !empty($stNomeFuncao) ) {
           $stJs .= "d.getElementById('stFuncao').innerHTML = '".$stNomeFuncao."';\n";
           if ($stCodFuncao) {
               $stJs .= "f.inCodFuncao.value = '$stCodFuncao';\n";
           }
       } else {
           $stJs .= "f.inCodFuncao.value = '';\n";
           $stJs .= "f.inCodFuncao.focus();\n";
           $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '&nbsp;';\n";
           $stJs .= "alertaAviso('@Função informada não existe. (".$_POST['inCodFuncao'].")','form','erro','".Sessao::getId()."');";
       }
    } else {
           $stJs .= "f.inCodFuncao.value = '';\n";
           $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '&nbsp;';\n";
           $stJs .= "alertaAviso('@O Módulo (".$arCodFuncao[0].") informado ou a Biblioteca (".$arCodFuncao['1'].") informada na Função Origem são diferentes dos campos Módulo Origem e Biblioteca Origem.','form','erro','".Sessao::getId()."');";

    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

// Acoes por pagina
switch ($stCtrl) {
    case "preencherBiblioteca":
            $stJs = preencherBiblioteca();
        break;
    case "preencherBibliotecaC":
            $stJs = preencherBiblioteca("C");
        break;
   case "buscaFuncao":
            $stJs = buscaFuncao(true,$stFuncao=$_REQUEST['inCodFuncao']);
        break;
}

if ($stJs != "") {
    echo $stJs;
}

?>
