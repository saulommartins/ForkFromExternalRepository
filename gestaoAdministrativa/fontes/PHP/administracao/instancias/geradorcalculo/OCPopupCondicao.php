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
    * Arquivo de instância para manutenção de funções
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: OCPopupCondicao.php 60978 2014-11-26 18:58:35Z carolina $

    Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

// Acoes por pagina
switch ($stCtrl) {

    case "MontaCondicao":
        $stSelecionado = $_GET['stSelecionado'];
        if (is_numeric(trim($stSelecionado)) || trim($stSelecionado)=='VERDADEIRO' || trim($stSelecionado)=='FALSO' || trim($stSelecionado)=='VAZIO'  || trim($stSelecionado)=='NULO') {
            $stSelecionado = trim($stSelecionado);
            if (is_numeric(trim($stSelecionado))) {
                $stSelecionado = $stSelecionado;
            }
        } elseif ($stSelecionado[0]=='-') {
            $stSelecionado = str_replace("-","#",$stSelecionado);
        } else {
            //$stSelecionado = str_replace(" ","&nbsp;",$stSelecionado);
            $stSelecionado = str_replace('"','',$stSelecionado);
            $stSelecionado = '"'.$stSelecionado.'"';
        }

        $Condicao = Sessao::read('Condicao');
        $Condicao[] = ' '.$stSelecionado;
        Sessao::write('Condicao', $Condicao);

        $stHtml = implode(" ",$Condicao);
        $stHtml = str_replace(' ','&nbsp;',$stHtml);
        $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
        $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";
    break;
    case "MontaCondicaoBotoes":

        $Condicao = Sessao::read('Condicao');

        if ($_GET['stSelecionado'] != 'DEL') {
            $Condicao[] = ' '.htmlspecialchars($_GET['stSelecionado']);
        } else {
            for ($inCount=0; $inCount< (count($Condicao)-1); $inCount++) {
                $arTmp[$inCount] = $Condicao[$inCount];
            }
            $Condicao = array("&nbsp;");
            if(is_array($arTmp))
                $Condicao = $arTmp;
        }

        Sessao::write('Condicao', $Condicao);

        $stHtml = implode(" ",$Condicao);
        $stHtml = str_replace(' ','&nbsp;',$stHtml);
        $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
        $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";
    break;
}
SistemaLegado::executaIFrameOculto($js);
?>
