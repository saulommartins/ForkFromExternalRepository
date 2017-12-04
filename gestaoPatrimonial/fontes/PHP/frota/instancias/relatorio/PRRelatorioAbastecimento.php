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
    * Data de Criação: 10/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRRelatorioAbastecimento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "RelatorioAbastecimento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

if ($_REQUEST['slTipoRelatorio'] != 3) {
    if ($_REQUEST['stDataInicial'] == '' OR $_REQUEST['stDataFinal'] == '') {
        $stMensagem = 'Preencha o campo periodicidade!';
    }
}

if (!$stMensagem) {
    foreach ($_REQUEST as $stKey=>$stValue) {
        if (is_array($stValue) ) {
                foreach ($stValue as $chave => $valor) {
                        $stURL .=$stKey.'['.$chave.']='.$valor.'&';
                }
        } else {
                $stURL .= $stKey.'='.$stValue.'&';
        }
    }
    sistemaLegado::alertaAviso($pgGera."?".substr($stURL,0,-1),'',"incluir", "aviso", Sessao::getId(),"");
} else {
    SistemaLegado::exibeAviso(urlencode($stMensagem), "erro", "erro" );
}
