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
* Página de Formulario de Oculto de Empresa Transporte
* Data de Criação   : 26/11/2004

* @author Analista: ???
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 31488 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSindicato.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                             );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"               );

$obRSindicato         = new RFolhaPagamentoSindicato;
$obRCGMPessoaJuridica = new RCGMPessoaJuridica;
$obRCGM               = new RCGM;

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ManterSindicato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

function preencherEvento($boExecuta=false)
{
    include_once ( CAM_GRH_FOL_NEGOCIO . 'RFolhaPagamentoEvento.class.php' );

    $obEvento = new RFolhaPagamentoEvento;

    $obEvento->setCodigo ( $_GET['inCodTipo']  );
    $obEvento->setNatureza  ( $_GET['stNatureza'] );
    $obEvento->listarEvento ( $rsEvento           );

    if ( $rsEvento->getNumLinhas() > 0 ) {
        $stJs .= " f.inCodEvento.value = '".$rsEvento->getCampo('codigo')."';                        \n";
        $stJs .= " d.getElementById('stEvento').innerHTML = '".$rsEvento->getCampo('descricao')."';  \n";
    } else {
        $stJs .= " f.inCodEvento.value = '';                \n";
        $stJs .= " d.getElementById('stEvento').innerHTML = '&nbsp;';    \n";
        $stJs .= "alertaAviso('@Código não encontrado. (".$_GET["inCodTipo"].")','form','erro','".Sessao::getId()."');";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

/*
Função: buscaFunção;
Objetivo: procurar na tabela um registro na tabela Funções por codigo e se
          encontra-lo preencher o busca inner com a descrição da função
data: 02/03/2006
autor Bruce
*/
function buscaFuncao($boExecuta = false)
{
    if ($_POST["inCodFuncao"]) {
        $arCodFuncao = explode('.',$_POST["inCodFuncao"]);
        $obRFuncao = new RFuncao;

        $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );

        $obRFuncao->consultar();
        $stNomeFuncao = $obRFuncao->getNomeFuncao();

        if ( !empty($stNomeFuncao) ) {
            $stJs .= "d.getElementById('stFuncao').innerHTML = '".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodFuncao.value = '';          \n";
            $stJs .= "f.inCodFuncao.focus();             \n";
            $stJs .= "d.getElementById('stFuncao').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Valor inválido. (".$_POST["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
        }
    }

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }

}

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
    case "buscaCGM":
    if ($_POST["inNumCGM"] != "") {
        $obRCGMPessoaJuridica->setNumCGM ( $_POST["inNumCGM"] );
        $stWhere = " numcgm = ".$obRCGMPessoaJuridica->getNumCGM();
        $null = "&nbsp;";
        $obRCGMPessoaJuridica->consultarCGM($rsCgm, $stWhere);
        $inNumLinhas = $rsCgm->getNumLinhas();
        if ($inNumLinhas <= 0) {
            $js .= 'f.inNumCGM.value = "";';
            $js .= 'f.inNumCGM.focus();';
            $js .= 'd.getElementById("campoInner").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomCgm = $rsCgm->getCampo("nom_cgm");
            $js .= 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
        }
        sistemaLegado::executaFrameOculto($js);
    }
    break;
    case 'buscaFuncao':
        buscaFuncao ( true );
    break;
    case 'preencherEvento':
       preencherEvento(true);
    break;

}

if ($js) {
    SistemaLegado::executaFrameOculto($js);
}

?>
