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
    * Página Oculta de Funções
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

// Casos de uso: uc-03.03.05
    $Id: OCManterClassificacao.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php");

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_REQUEST['stCtrl'];

$obRegra = new RAlmoxarifadoCatalogoClassificacao;

function listaValores($rsRecordSet, $executa=true)
{
    global $obRegra;

    $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->consultar();

    if ($rsRecordSet->getNumLinhas() > 0) {
        $obFormulario = new Formulario();
        $obFormulario->addTitulo            ( "Classificação Mãe");

        while (!$rsRecordSet->EOF()) {
            if ($_REQUEST['stAcao'] == 'alterar') {
                $obLblCodigo= new Label;
                $obLblCodigo->setRotulo ( $rsRecordSet->getCampo('descricao_nivel') );
                $obLblCodigo->setValue  ( $rsRecordSet->getCampo('cod_nivel') . " - " . $rsRecordSet->getCampo('descricao') );

                $obFormulario->addComponente( $obLblCodigo );
            } else {
                $obRegra->obRAlmoxarifadoCatalogo->addCatalogoNivel();
                $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel($rsRecordSet->getCampo('nivel'));

                $obTxtCodClassificacao = new TextBox;
                $obTxtCodClassificacao->setRotulo              ( $rsRecordSet->getCampo('descricao')                  );
                $obTxtCodClassificacao->setTitle               ( "Selecione o " . $rsRecordSet->getCampo('descricao')."." );
                $obTxtCodClassificacao->setName                ( "inCodClassificacaoTxt_" . $rsRecordSet->getCampo('nivel'));
                $obTxtCodClassificacao->setValue               ( $inCodClassificacaoTxt                               );
                $obTxtCodClassificacao->setSize                ( 6                      );
                $obTxtCodClassificacao->setMaxLength           ( 3                      );
                $obTxtCodClassificacao->setInteiro             ( true                   );
                $obTxtCodClassificacao->setNull                ( false                  );

                if ($rsRecordSet->getCorrente() == 1) {
                    $obRegra->listarDetalhesClassificacao          ( $rsClassificacoes) ;
                }

                $obCmbCodClassificacao = new Select;
                $obCmbCodClassificacao->setRotulo              ( $rsRecordSet->getCampo('descricao')   );
                $obCmbCodClassificacao->setName                ( "inCodClassificacao_" . $rsRecordSet->getCampo('nivel'));
                $obCmbCodClassificacao->setValue               ( $inCodClassificacaoTxt               );
                $obCmbCodClassificacao->setStyle               ( "width: 200px"                       );
                $obCmbCodClassificacao->setCampoID             ( "cod_nivel"                  );
                $obCmbCodClassificacao->setCampoDesc           ( "descricao"                          );
                $obCmbCodClassificacao->addOption              ( "", "Selecione"                      );
                $obCmbCodClassificacao->setNull                ( false                                );

                if ($rsRecordSet->getCorrente() == 1) {
                    $obCmbCodClassificacao->preencheCombo          ( $rsClassificacoes                    );
                }

                if ($rsRecordSet->getCorrente() != $rsRecordSet->getNumLinhas()) {
                    $obCmbCodClassificacao->obEvento->setOnChange  ( "addNivel(this);" );
                } else {
                    $obCmbCodClassificacao->obEvento->setOnChange  ( "setCurrNivel(this);" );
                }

                $obFormulario->addComponenteComposto( $obTxtCodClassificacao, $obCmbCodClassificacao);
            }

            $rsRecordSet->proximo();
        }

        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stValida = $obFormulario->obJavaScript->getInnerJavaScript();
    }

    // preenche a lista com innerHTML
    $stJs  = "d.getElementById('spnListaClassificacao').innerHTML = '" . $stHtml . "';";
    $stJs .= "f.stValida.value = '" . $stValida . "';";

    SistemaLegado::executaFrameOculto($stJs);
}

function listaAtributosInclusao()
{
    global $obRegra;

    $rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;

    $obRegra->obRCadastroDinamico->setPersistenteAtributos ( new TAdministracaoAtributoDinamico );
    $obRegra->obRCadastroDinamico->recuperaAtributos( $rsAtributosDisponiveis );

    $obFormulario = new Formulario();

    // Componentes dos Atributos

    $obCmbAtributos = new SelectMultiplo();
    $obCmbAtributos->setName   ('inCodAtributos');
    $obCmbAtributos->setRotulo ( "Atributos" );
    $obCmbAtributos->setNull   ( true );
    $obCmbAtributos->setTitle  ( "Selecione os atributos." );

    // lista de atributos disponiveis
    $obCmbAtributos->SetNomeLista1 ('inCodAtributosDisponiveis');
    $obCmbAtributos->setCampoId1   ('cod_atributo');
    $obCmbAtributos->setCampoDesc1 ('nom_atributo');
    $obCmbAtributos->SetRecord1    ( $rsAtributosDisponiveis );

    // lista de atributos selecionados
    $obCmbAtributos->SetNomeLista2 ('inCodAtributosSelecionados');
    $obCmbAtributos->setCampoId2   ('cod_atributo');
    $obCmbAtributos->setCampoDesc2 ('nom_atributo');
    $obCmbAtributos->SetRecord2    ( $rsAtributosSelecionados );

    $obFormulario->addComponente ( $obCmbAtributos );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stJavaScript = $obFormulario->obJavaScript->getInnerJavaScript();

    // preenche a lista com innerHTML
    $stJs  = "d.getElementById('spnListaAtributos').innerHTML = '" . $stHtml . "';\n";
    $stJs .= "f.stValida.value = '" . $stJavaScript . "';";
    $stJs .= "d.getElementById('Ok').disabled = false;";

    SistemaLegado::executaFrameOculto($stJs);
}

function listaAtributosAlteracao()
{
    global $obRegra;
    $rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;
    $obRegra->obRCadastroDinamico->setChavePersistenteValores( array("cod_classificacao"=>$obRegra->getCodigo(),"cod_catalogo"=>$obRegra->obRAlmoxarifadoCatalogo->getCodigo() ) );
    $obRegra->obRCadastroDinamico->recuperaAtributosDisponiveis( $rsAtributosDisponiveis );

    $obRegra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados );
    $obFormulario = new Formulario();

    // Componentes dos Atributos

    $obCmbAtributos = new SelectMultiplo();
    $obCmbAtributos->setName   ('inCodAtributos');
    $obCmbAtributos->setRotulo ( "Atributos" );
    $obCmbAtributos->setNull   ( true );
    $obCmbAtributos->setTitle  ( "Selecione os atributos." );

    // lista de atributos disponiveis
    $obCmbAtributos->SetNomeLista1 ('inCodAtributosDisponiveis');
    $obCmbAtributos->setCampoId1   ('cod_atributo');
    $obCmbAtributos->setCampoDesc1 ('nom_atributo');
    $obCmbAtributos->SetRecord1    ( $rsAtributosDisponiveis );

    // lista de atributos selecionados
    $obCmbAtributos->SetNomeLista2 ('inCodAtributosSelecionados');
    $obCmbAtributos->setCampoId2   ('cod_atributo');
    $obCmbAtributos->setCampoDesc2 ('nom_atributo');
    $obCmbAtributos->SetRecord2    ( $rsAtributosSelecionados );

    $obFormulario->addComponente ( $obCmbAtributos );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stJavaScript = $obFormulario->obJavaScript->getInnerJavaScript();

    // preenche a lista com innerHTML
    $stJs  = "d.getElementById('spnListaAtributos').innerHTML = '" . $stHtml . "';\n";
    $stJs .= "f.stValida.value = '" . $stJavaScript . "';";
    $stJs .= "d.getElementById('Ok').disabled = false;";
    $stJs .= "LiberaFrames(true, true);";

    SistemaLegado::executaFrameOculto($stJs);
}
// Acoes por pagina

$arrayValores = Sessao::read('Valores');

switch ($stCtrl) {
    case "MontaValoresListaAltera":

    listaValores( $arrayValores );
    break;
    case "MontaValoresLista":

        $boAdiciona = true;
        $rsRecordSet = new Recordset;

        $obRAlmoxarifadoCatalogoClassificacao->obTAlmoxarifadoCatalogoClassificacao;

        if ($boAdiciona) {
            // Insere novos valores no vetor
            $arElementos['inId']            = $inProxId;
            $arElementos['nivel']           = $inProxNivel;
            $arElementos['mascara']         = $_REQUEST['stMascara'];
            $arElementos['descricao']       = $_REQUEST['stDescricaoNivel'];
            $arElementos['excluir']         = true;

            Sessao::write('Valores',$arElementos);
        }
        $arrayValores = Sessao::read('Valores');
        listaValores( $arrayValores );
    break;

    case "MontaNiveisCombo":

        $stJs = '';

        $boAdiciona = true;
        $rsRecordSet = new Recordset;

        if (!$_REQUEST['inCodCatalogoTxt']) {
            $stJs = "d.frm.reset();";
        } else {
            $stJs .= "limpaSelect(d.frm.inCodNivel ,0); \n";
            $stJs .= "d.frm.inCodNivel.options[0] = new Option; \n";
            $stJs .= "d.frm.inCodNivel.options[0].value = '' \n";
            $stJs .= "d.frm.inCodNivel.options[0].text = 'Selecione' \n";

            $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogoTxt']);

            $obErro = $obRegra->obRAlmoxarifadoCatalogo->listarNiveis($rsRecordSet);

            if (!$obErro->ocorreu()) {
                $inPosNivel = 1;

                while (!$rsRecordSet->EOF()) {
                    $stJs .= "d.frm.inCodNivel.options[$inPosNivel] = new Option;";
                    $stJs .= "d.frm.inCodNivel.options[$inPosNivel].value = " . $rsRecordSet->getCampo('nivel') . ";";
                    $stJs .= "d.frm.inCodNivel.options[$inPosNivel].text  = '" . $rsRecordSet->getCampo('nivel') . " - ". $rsRecordSet->getCampo('descricao') . "';";
                    if ( ($_REQUEST['inCodigoNivel'] == $rsRecordSet->getCampo('nivel')) && ($_REQUEST['preencheNivel'] == 'true') ) {
                        $stJs .= "d.frm.inCodNivel.options[$inPosNivel].selected  = 'true';";
                    }

                    $inPosNivel++;
                    $rsRecordSet->proximo();
                }
            }
        }

        $ultimoEstrutural = SistemaLegado::pegaDado("cod_estrutural", "almoxarifado.catalogo_classificacao", " where cod_catalogo=".$_REQUEST['inCodCatalogoTxt']." ORDER BY cod_estrutural DESC LIMIT 1");
        $tamanhoCampoCodEstrutural = strlen($ultimoEstrutural);

        SistemaLegado::executaFrameOculto($stJs);

    break;

    case "MontaClassificacaoCombo":

        $stJs = '';
        $stEstrutural = '';

        $boAdiciona = true;
        $rsRecordSet = new Recordset;

        $currCombo = $_REQUEST['inCurrCombo'];
        $nextCombo = $_REQUEST['inNextCombo'];
        $_REQUEST["inCodClassificacao_$nextCombo"] = '';

        foreach ($_REQUEST as $chv => $val) {
            if ((preg_match('/^inCodClassificacao\_[0-9]$/', $chv)) and ( $chv != "inCodClassificacao_".$nextCombo )  and ( $val != '') ) {
                $stEstrutural .= $val.'.';
            }
        }
        $stEstrutural = preg_replace('/\.$/', '', $stEstrutural);
        $obRegra = new RAlmoxarifadoCatalogoClassificacao;
        $obRegra->setEstrutural($stEstrutural);

        $stJs  =  "limpaSelect(d.frm.inCodClassificacao_$nextCombo ,0);";
        $stJs .=  "d.frm.inCodClassificacao_$nextCombo.options[0] = new Option;";
        $stJs .=  "d.frm.inCodClassificacaoTxt_$nextCombo.value = '';\n";
        $stJs .=  "d.frm.inCodClassificacao_$nextCombo.options[0].value = '';";
        $stJs .=  "d.frm.inCodClassificacao_$nextCombo.options[0].text = 'Selecione';";

        $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogoTxt']);
        $obRegra->obRAlmoxarifadoCatalogo->addCatalogoNivel();
        $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel($nextCombo);
        $obErro = $obRegra->listarDetalhesClassificacao($rsRecordSet);

        if (!$obErro->ocorreu()) {
            $inPosNivel = 1;

            while (!$rsRecordSet->EOF()) {
                $stJs .= "d.frm.inCodClassificacao_$nextCombo.options[$inPosNivel] = new Option;";
                $stJs .= "d.frm.inCodClassificacao_$nextCombo.options[$inPosNivel].value = " . $rsRecordSet->getCampo('cod_nivel') . ";";
                $stJs .= "d.frm.inCodClassificacao_$nextCombo.options[$inPosNivel].text  = '" . $rsRecordSet->getCampo('descricao') . "';";

                $inPosNivel++;
                $rsRecordSet->proximo();
            }
        }

        SistemaLegado::executaFrameOculto($stJs);

    break;

    case "MontaListaClassificacao":
        if ($_REQUEST['inCodCatalogo']) {
            $rsRecordSet = new Recordset;

            $obRegra->setCodigo($_REQUEST['inCodigo']);
            $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogo']);
            $obRegra->obRAlmoxarifadoCatalogo->addCatalogoNivel();

            if ($stAcao == 'alterar') {
                $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel($_REQUEST['inCodigoNivel']);
            } else {
                $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel($_REQUEST['inCodNivel']);
            }

            $obErro = $obRegra->obRAlmoxarifadoCatalogo->listarNiveisMae($rsRecordSet);

            if (!$obErro->ocorreu()) {
                listaValores( $rsRecordSet );
                if ($_REQUEST['stListaInclusao'] == 'true') {
                    listaAtributosInclusao();
                }
            }
        }

    break;

    case "MontaListaClassificacaoAlteracao":
        if ($_REQUEST['inCodCatalogo']) {
            $rsRecordSet = new Recordset;
            $arRecordSet = array();

            $obRegra->setCodigo($_REQUEST['inCodigoClassificacao']);
            $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogo']);
            $obErro = $obRegra->consultar();
            $obErro = $obRegra->listarClassificacaoMae($rsRecordSet);

            if (!$obErro->ocorreu()) {
                listaValores( $rsRecordSet );

                if ($_REQUEST['stListaInclusao']) {
                    listaAtributosInclusao();
                    $stJs .= "d.getElementById('Ok').disabled = false;";

                } else {
                    listaAtributosAlteracao();
                    $stJs .= "LiberaFrames(true, true);";
                }
            }
        }
    break;

    case "MontaProxCombo":

            $rsRecordSet = new Recordset;

            $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogo']);
            $obRegra->obRAlmoxarifadoCatalogo->addCatalogoNivel();

            $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel($_REQUEST['inCurrCombo']);
            $obErro = $obRegra->listarDetalhesClassificacao($rsRecordSet);

            if (!$obErro->ocorreu()) {
                listaCombo( $rsRecordSet );
            }

    break;

    case "excluiCatalogo":
        $boVerifica = settype($_GET['boDesabilitaBotao'], 'bool');
        $arVariaveis = $arTMP = array();
        $id = $_GET['inId'];
        $inCount = 0;

        foreach ($arrayValores as $campo => $valor) {
            if ( ($arrayValores[$campo]['inId'] == $id) && (!$arrayValores[$campo]['excluir']) ) {
                $boVerifica = true;
                $stMensagem = "Não é possível excluir este valor da lista.";
            }

            if ( $id != (count($arrayValores)) ) {
                $boVerifica = true;
                $stMensagem = "Somente é possível excluir o último nível da lista.";
            }
        }

        if ($boVerifica == false) {
            // Lista o Array e remonta sem o ID selecionado
            foreach ($arrayValores as $campo => $valor) {
                if ($arrayValores[$campo]["inId"] != $id) {

                    $arElementos['inId']        = $inCount + 1;
                    $arElementos['nivel']       = $inCount + 1;
                    $arElementos['mascara']     = $arrayValores[$campo]["mascara"];
                    $arElementos['descricao']   = $arrayValores[$campo]["descricao"];
                    $arElementos['excluir']     = $arrayValores[$campo]["excluir"];

                    $inCount++;

                    $arTMP[] = $arElementos;
                 }
            }

            Sessao::write('Valores',$arTMP);
            $stJs  = listaValores( $arTMP );
            $stJs .= "f.btnAlterar.disabled = true;";
        } else {
            $stJs = "alertaAviso('@($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;

    case "excluiValor":

        $boVerifica = false;
        $arVariaveis = $arTMP = array();
        $id = $_GET['inId'];
        $inCount = 0;

        if ( $arrayValores[$id]['inId'] != (count($arrayValores) - 1) ) {
            $boVerifica = true;
            $stMensagem = "Somente é possível excluir o último nível da lista.";
        }

        if ($boVerifica == false) {
            // Lista o Array e remonta sem o ID selecionado
            foreach ($arrayValores as $campo => $valor) {
                if ($arrayValores[$campo]["inId"] != $id) {
                    $arElementos['inId']        = $inCount;
                    $arElementos['nivel']       = ($arrayValores[$campo]["nivel"]) ? $arrayValores[$campo]["nivel"] : ($inCount + 1);
                    $arElementos['mascara']     = $arrayValores[$campo]["mascara"];
                    $arElementos['descricao']   = $arrayValores[$campo]["descricao"];
                    $arElementos['excluir']     = $arrayValores[$campo]["excluir"];

                    $inCount++;

                    $arTMP[] = $arElementos;
                }
            }

            Sessao::write('Valores',$arTMP);

            $stJs  = listaValores( $arTMP );
            $stJs .= "f.btnAlterar.disabled = true;";
        } else {
            $stJs = "alertaAviso('@($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "MontaAlteracaoValor":

        $id = $_GET['inId'];

        foreach ($arrayValores as $campo => $valor) {
            if ($arrayValores[$campo]["inId"] == $id) {
                $stJs  = "f.stMascara.value='".$arrayValores[$campo]["mascara"]."';";

                $stJs .= "f.stDescricaoNivel.value='".$arrayValores[$campo]["descricao"]."';";
                $stJs .= "f.inIDPos.value='".$id."';";
            }
        }

        $stJs .= listaValores( $arrayValores , false);
        $stJs .= "f.btnAlterar.disabled = false;";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "alteraValor":

        $boVerifica = false;
        $arVariaveis = $arTMP = array();
        $id = $_REQUEST['inIDPos'];

        $obRegra->setCodigo($_REQUEST['inCodigo']);

        foreach ($arrayValores as $campo => $valor) {
            if ($arrayValores[$campo]["inId"] == $id) {
                 if ($_REQUEST['stAcao'] == 'alterar') {
                    $obRegra->addCatalogoNivel();
                    $obRegra->roCatalogoNivel->setNivel($arrayValores[$campo]['nivel']);
                    $obRegra->roCatalogoNivel->setMascara($_REQUEST['stMascara']);

                    $obErro = $obRegra->validarNiveis($obErroValidar);

                    if ($obErroValidar->ocorreu()) {
                        $obRegra->roCatalogoNivel->setMascara($arrayValores[$campo]['stMascara']);

                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                    } else {
                        $obRegra->roCatalogoNivel->setMascara($_REQUEST['stMascara']);

                        Sessao::write('Valores['.$campo.'][mascara]',$_REQUEST['stMascara']);
                        Sessao::write('Valores['.$campo.'][descricao]',$_REQUEST['stDescricaoNivel']);

                    }
                 }
             }
        }

        $arrayValores = Sessao::read('Valores');

        $stJs  = listaValores( $arrayValores , false);
        $stJs .= "f.btnAlterar.disabled = true;";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case 'preencheInner':
        if ($_REQUEST['inCodTipoAtributo']==3 || $_REQUEST['inCodTipoAtributo']==4) {
            $stJs  = listaValores( $arrayValores );

        } else {
            $stJs = "f.stValorPadrao.value = '".$arrayValores[0]['valor']."';";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
}
?>
