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
    * Página do Oculto do relatorio de configuração de lançamento de receita
    * Data de CriaÃ§Ã£o   : 17/11/2011

    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Davi Aroldi

    * @ignore

    $Id:$

    * Casos de uso: uc-02.03.18

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioConfiguracaoLancamentoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obROrcamentoReceita = new ROrcamentoReceita;

switch ($_GET['stCtrl']) {
    case 'mascaraClassificacao':
        //monta mascara da RUBRICA DE DESPESA
        $inCodReceita=str_replace(".", "",$_POST['inCodReceita']);
        $mascara=$_POST['stMascClassificacao'];
        $inCodReceita = Mascara::geraMascara($mascara, $inCodReceita);
        $arMascClassificacao = Mascara::validaMascaraDinamica( $mascara , $inCodReceita);
        $js .= "f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";
        $codEstruturalInvalido=$arMascClassificacao[1];
        $invalido=false;

        //busca DESCRICAO DA RUBRICA DE DESPESA
        if ($arMascClassificacao[1]!='0.0.0.0.00.00.00.00.00'&&$arMascClassificacao[1]!='') {
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascara          ( $_POST['stMascClassificacao'] );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $arMascClassificacao[1]       );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodEstrutural	( $arMascClassificacao[1]       );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaDescricaoReceitaFiltrada( $rsLista );

        if ($rsLista->inNumLinhas==1) {
            $stDescricao=$rsLista->getCampo('descricao');
        } else {
            $inCodReceita=str_replace(".", "",$_POST['inCodReceita']);
            $mascara='9.'.$_POST['stMascClassificacao'];
            $inCodReceita = Mascara::geraMascara($mascara, $inCodReceita);
            $arMascClassificacao = Mascara::validaMascaraDinamica( $mascara , $inCodReceita);
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodEstrutural	( $arMascClassificacao[1]       );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaDescricaoReceitaFiltrada( $rsLista );

            if ($rsLista->inNumLinhas==1) {
                $stDescricao=$rsLista->getCampo('descricao');
                $js .= "f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";
            }
        }

        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoReceita").innerHTML = "'.$stDescricao.'";';
        } else {
            $invalido=true;
        }

        }

        if ($invalido==true||($arMascClassificacao[1]=='0.0.0.0.00.00.00.00.00'||$arMascClassificacao[1]=='')) {
        $null = "&nbsp;";
        $js .= 'f.inCodReceita.value = "";';
        $js .= 'd.getElementById("stDescricaoReceita").innerHTML = "'.$null.'";';
        $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }

        SistemaLegado::executaFrameOculto( $js );
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
