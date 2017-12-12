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
    * Interface de Alteração das configuração do orçamento
    * Data de Criação   : 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    $Id: OCConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.01
*/

/*
$Log: OCConfiguracao.php,v $
Revision 1.4  2006/07/05 20:42:45  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once( "../../../bibliotecas/mascaras.lib.php"       );
//include_once( CAM_INCLUDES."IncludeClasses.inc.php"         );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"  );

/**
* Define o nome dos arquivos PHP
*/
$stPrograma = "Configuracao";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once( $pgJS );

function testaCampos($idPrincipal)
{
    $boErro = "";
    $js		= "";
    $arPrincipal = explode(",",$_POST[$idPrincipal]);
    $arIdProjeto = explode(",",$_POST["inDigitoIDProjeto"]);
    $arIdAtividade = explode(",",$_POST["inDigitoIDAtividade"]);
    $arIdNaoOrcamentarios = explode(",",$_POST["inDigitoIDNaoOrcamentarios"]);
    $arIdEspecial = explode(",",$_POST['inDigitoIDEspecial']);

    switch ($idPrincipal) {
        case "inDigitoIDProjeto":
            if (array_intersect($arPrincipal, $arIdNaoOrcamentarios)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdAtividade)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdEspecial)) {
                $boErro = true;
            }
        break;

        case "inDigitoIDAtividade":
            if (array_intersect($arPrincipal, $arIdProjeto)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdNaoOrcamentarios)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdEspecial)) {
                $boErro = true;
            }
        break;

        case "inDigitoIDNaoOrcamentarios":
            if (array_intersect($arPrincipal, $arIdProjeto)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdEspecial)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdAtividade)) {
                $boErro = true;
            }
        break;

        case "inDigitoIDEspecial":
            if (array_intersect($arPrincipal, $arIdProjeto)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdAtividade)) {
                $boErro = true;
            }
            if (array_intersect($arPrincipal, $arIdNaoOrcamentarios)) {
                $boErro = true;
            }
        break;
    }

    if ($boErro != "") {
        $boErro = substr($boErro, 1);
        $js .= 'f.inDigitoIDProjeto.focus();';
        $js .= "alertaAviso('@Valor inválido. ( Os valores não podem se repetir entre os Dígitos de Identificação)','form','erro','".Sessao::getId()."');";
    }

    return $js;
}

/**
* Valida campo
*/

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio( Sessao::getExercicio() );
$obRConfiguracaoOrcamento->consultarConfiguracao();

switch ($_REQUEST["stCtrl"]) {
    case "montaRecurso":
        $obFormulario = new Formulario;
        $obFormulario->setLarguraRotulo         ( 22 );
        $obTxtMascaraRecurso = new TextBox;
        $obTxtMascaraRecurso->setName      ( "stMascRecurso" );
        $obTxtMascaraRecurso->setId        ( "stMascRecurso" );
        $obTxtMascaraRecurso->setValue     ( $obRConfiguracaoOrcamento->getMascRecurso() );
        $obTxtMascaraRecurso->setRotulo    ( "Máscara do Recurso" );
        $obTxtMascaraRecurso->setTitle     ( "Informe a máscara do recurso." );
        $obTxtMascaraRecurso->setSize      ( 20 );
        $obTxtMascaraRecurso->setMaxLength ( "" );
        $obTxtMascaraRecurso->setNull      ( false );
        $obTxtMascaraRecurso->setDecimais  ( 0 );
        $obTxtMascaraRecurso->setInteiro   ( true  );
        $obTxtMascaraRecurso->obEvento->setOnKeyPress("return validaExpressao( this, event, '[9.]');");
        $obFormulario->addComponente( $obTxtMascaraRecurso);
        $obFormulario->montaInnerHTML();

        echo "document.getElementById('spnRec').innerHTML = '".$obFormulario->getHTML()."';";

    break;
    case "montaDestinacaoRecurso":
        $obFormulario = new Formulario;
        $obFormulario->setLarguraRotulo         ( 22 );
        $obTxtMascaraDestinacao = new TextBox;
        $obTxtMascaraDestinacao->setName      ( "stMascDestinacaoRecurso" );
        $obTxtMascaraDestinacao->setId        ( "stMascDestinacaoRecurso" );
        $obTxtMascaraDestinacao->setValue     ( $obRConfiguracaoOrcamento->getMascDestinacaoRecurso() );
        $obTxtMascaraDestinacao->setRotulo    ( "Máscara da Destinação de Recursos" );
        $obTxtMascaraDestinacao->setTitle     ( "Informe a máscara da Destinação de Recursos." );
        $obTxtMascaraDestinacao->setSize      ( 20 );
        $obTxtMascaraDestinacao->setMaxLength ( "" );
        $obTxtMascaraDestinacao->setNull      ( false );
        $obTxtMascaraDestinacao->setDecimais  ( 0 );
        $obTxtMascaraDestinacao->setLabel     ( true  );
        $obTxtMascaraDestinacao->obEvento->setOnKeyPress("return validaExpressao( this, event, '[9.]');");
        $obFormulario->addComponente( $obTxtMascaraDestinacao );
        $obFormulario->montaInnerHTML();

        echo "document.getElementById('spnRec').innerHTML = '".$obFormulario->getHTML()."';";

    break;

    case "PosicaoID":
    $obErro = 0;
    if ( ($_POST["inPosicaoDigitoID"] == 0) OR ($_POST["inPosicaoDigitoID"] > 4) ) {
        $obErro++;
    }
    if ($obErro != 0) {
        $js .= 'f.inPosicaoDigitoID.value = "";';
        $js .= 'f.inPosicaoDigitoID.focus();';
        $js .= "alertaAviso('@Valor inválido. (".$_POST["inPosicaoDigitoID"]." - O campo Posição do Dígito de Identificação do PAO deve ter um caractere de 1 a 4)','form','erro','".Sessao::getId()."');";
    }
    executaFrameOculto($js);
    break;

    case "IDProjeto":
    $js     = "";
    $obErro = 0;
    $digitos_id_projeto_explode = explode(",",$_POST['inDigitoIDProjeto']);
    while (list($chave,$valor) = each($digitos_id_projeto_explode)) {
        if ($valor > 9) {
            $obErro++;
        }
    }
    if ($obErro != 0) {
        $js .= 'f.inDigitoIDProjeto.value = "";';
        $js .= 'f.inDigitoIDProjeto.focus();';
        $js .= "alertaAviso('@Valor inválido. (".$_POST["inDigitoIDProjeto"]." - Os valores do campo Digito de Identificação do Projeto devem ser separados por vírgula e ser de 0 a 9)','form','erro','".Sessao::getId()."');";
    }
    reset($digitos_id_projeto_explode);
    $inRepetidos = testaCampos('inDigitoIDProjeto');
    $js .= $inRepetidos;
    SistemaLegado::executaFrameOculto($js);
    break;

    case "IDAtividade":
    $obErro = 0;
    $js		= "";
    $digitos_id_atividade_explode = explode(",",$_POST['inDigitoIDAtividade']);
    while (list($chave,$valor) = each($digitos_id_atividade_explode)) {
        if ($valor > 9) {
            $obErro++;
        }
    }
    if ($obErro != 0) {
        $js .= 'f.inDigitoIDAtividade.value = "";';
        $js .= 'f.inDigitoIDAtividade.focus();';
        $js .= "alertaAviso('@Valor inválido. (".$_POST["inDigitoIDAtividade"]." - Os valores do campo Digito de Identificação de Atividade devem ser separados por vírgula e ser de 0 a 9)','form','erro','".Sessao::getId()."');";
    }
    reset($digitos_id_atividade_explode);
    $inRepetidos = testaCampos('inDigitoIDAtividade');
    $js .= $inRepetidos;
    SistemaLegado::executaFrameOculto($js);
    break;

    case "IDEspecial":
    $obErro = 0;
    $js		= "";
    $digitos_id_oper_especiais_explode = explode(",",$_POST['inDigitoIDEspecial']);
    while (list($chave,$valor) = each($digitos_id_oper_especiais_explode)) {
        if ($valor > 9) {
            $obErro++;
        }
    }
    if ($obErro != 0) {
        $js .= 'f.inDigitoIDEspecial.value = "";';
        $js .= 'f.inDigitoIDEspecial.focus();';
        $js .= "alertaAviso('@Valor inválido. (".$_POST["inDigitoIDEspecial"]." - Os valores do campo Digito de Identificação de Atividade devem ser separados por vírgula e ser de 0 a 9)','form','erro','".Sessao::getId()."');";
    }
    reset($digitos_id_oper_especiais_explode);
    $inRepetidos = testaCampos('inDigitoIDEspecial');
    $js .= $inRepetidos;
    SistemaLegado::executaFrameOculto($js);
    break;

    case "IDNaoOrcamentarios":
    $obErro = 0;
    $js		= "";
    $digitos_id_nao_orcamentarios_explode = explode(",",$_POST['inDigitoIDNaoOrcamentarios']);
    while (list($chave,$valor) = each($digitos_id_nao_orcamentarios_explode)) {
        if ($valor > 9) {
            $obErro++;
        }
    }
    if ($obErro != 0) {
        $js .= 'f.inDigitoIDNaoOrcamentarios.value = "";';
        $js .= 'f.inDigitoIDNaoOrcamentarios.focus();';
        $js .= "alertaAviso('@Valor inválido. (".$_POST["inDigitoIDNaoOrcamentarios"]." - Os valores do campo Digito de Identificação de Não Orçamentários devem ser separados por vírgula e ser de 0 a 9)','form','erro','".Sessao::getId()."');";
    }
    reset($digitos_id_nao_orcamentarios_explode);
    $inRepetidos = testaCampos('inDigitoIDNaoOrcamentarios');
    $js .= $inRepetidos;
    SistemaLegado::executaFrameOculto($js);
    break;

    case "validaMascaraReceita":
    $arBlocos = preg_split( "/[^0-9]/", $_POST['stMascReceita'] );
    sort($arBlocos);
    foreach ($arBlocos as $inKey=>$inValor) {
        if ($inValor == "") {
            $js .= 'f.stMascReceita.focus();';
            $js .= "alertaAviso('@Valor inválido. ( Valores do campo - Máscara de Classificação da Receita devem ser números(9) e pontos(.) )','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
            exit();
        }
    }
    break;

    case "validaMascaraReceita":
    $arBlocos = preg_split( "/[^0-9]/", $_POST['stMascReceitaDedutora'] );
    sort($arBlocos);
    foreach ($arBlocos as $inKey=>$inValor) {
        if ($inValor == "") {
            $js .= 'f.stMascReceitaDedutora.focus();';
            $js .= "alertaAviso('@Valor inválido. ( Valores do campo - Máscara de Classificação da Receita Dedutora devem ser números(9) e pontos(.) )','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
            exit();
        }
    }
    break;

    case "validaMascaraDespesa":
    $arBlocos = preg_split( "/[^0-9]/", $_POST['stMascPosicaoDespesa'] );
    sort($arBlocos);
    foreach ($arBlocos as $inKey=>$inValor) {
        if ($inValor == "") {
            $js .= 'f.stMascPosicaoDespesa.focus();';
            $js .= "alertaAviso('@Valor inválido. ( Valores do campo - Máscara da Rúbrica da Despesa devem ser números(9) e pontos(.) )','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
            exit();
        }
    }
    break;

    case "validaMascDespesa":
    $arBlocos = preg_split( "/[^0-9]/", $_POST['stMascDespesa'] );
    sort($arBlocos);
    foreach ($arBlocos as $inKey=>$inValor) {
        if ($inValor == "") {
            $js .= 'f.stMascDespesa.focus();';
            $js .= "alertaAviso('@Valor inválido. ( Valores do campo - Máscara da Despesa devem ser números(9) e pontos(.) )','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
            exit();
        }
    }
    break;
}
?>
