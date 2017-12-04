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
    * Página Oculta de Consulta de Saldos da Dotação
    * Data de Criação   : 21/06/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.26
*/

/*
$Log$
Revision 1.9  2006/07/19 18:59:48  leandro.zis
Bug #6415#

Revision 1.8  2006/07/14 19:51:31  leandro.zis
Bug #6415#

Revision 1.7  2006/07/05 20:42:50  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"            );

$obRegra            = new ROrcamentoDespesa;

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaFiltroEntidades()
{
    $obRegra            = new ROrcamentoDespesa;
    $obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
    $rsRecordset = new RecordSet;

    // Define SELECT multiplo para codigo da entidade
    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName   ('inCodEntidade');
    $obCmbEntidades->setRotulo ( "Entidades" );
    $obCmbEntidades->setTitle  ( "Selecione as entidades." );
    $obCmbEntidades->setNull   ( false );

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    if ($rsEntidades->getNumLinhas()==1) {
          $rsRecordset = $rsEntidades;
          $rsEntidades = new RecordSet;
    }

    // lista de atributos disponiveis
    $obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
    $obCmbEntidades->setCampoId1   ( 'cod_entidade' );
    $obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
    $obCmbEntidades->SetRecord1    ( $rsEntidades );
    // lista de atributos selecionados
    $obCmbEntidades->SetNomeLista2 ('inCodEntidade');
    $obCmbEntidades->setCampoId2   ('cod_entidade');
    $obCmbEntidades->setCampoDesc2 ('nom_cgm');
    $obCmbEntidades->SetRecord2    ( $rsRecordset );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbEntidades );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $js1 = "d.getElementById('spnPesquisa').innerHTML = '".$stHtml."';";
    $js1.="d.getElementById('stEval').value = 'selecionaTodosSelect(document.frm.inCodEntidade)';";

    return $js1;
}

function montaFiltroDotacao()
{
    $obRegra            = new ROrcamentoDespesa;
    $obRegra->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
    $stMascaraRubrica = $obRegra->obROrcamentoClassificacaoDespesa->recuperaMascara();
    $obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

    // Define Objeto Select para Nome da Entidade
    $obCmbNomeEntidade = new Select;
    $obCmbNomeEntidade->setRotulo        ( "Entidade" );
    $obCmbNomeEntidade->setTitle         ( "Selecione a entidade." );
    $obCmbNomeEntidade->setName          ( "inCodEntidade"               );
    $obCmbNomeEntidade->setId            ( "inCodEntidade"               );
    $obCmbNomeEntidade->setValue         ( $inCodigoEntidade              );
    // Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
    // Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
    if ($rsEntidades->getNumLinhas()>1) {
        $obCmbNomeEntidade->addOption              ( "", "Selecione"               );
    }
    $obCmbNomeEntidade->obEvento->setOnChange( "limparCampos();"          );
    $obCmbNomeEntidade->setCampoId       ( "cod_entidade"                 );
    $obCmbNomeEntidade->setCampoDesc     ( "nom_cgm"                      );
    $obCmbNomeEntidade->preencheCombo    ( $rsEntidades                   );
    $obCmbNomeEntidade->setNull          ( false                          );

    // Define Objeto BuscaInner para Dotacao Redutoras
    $obBscDespesa = new BuscaInner;
    $obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
    $obBscDespesa->setTitle  ( "Informe uma dotação orçamentária." );
    $obBscDespesa->setNull   ( true                     );
    $obBscDespesa->setId     ( "stNomDotacao"           );
    $obBscDespesa->setValue  ( $stNomDotacao            );
    $obBscDespesa->obCampoCod->setName ( "inCodDotacao" );
    $obBscDespesa->obCampoCod->setId   ( "inCodDotacao" );
    //Linha baixo utilizada para seguir um tamanho padrão de campo de acordo com o elemento da despesa
    //Utilizado somente nesta interface
    $obBscDespesa->obCampoCod->setSize      ( strlen($stMascaraRubrica)  );
    $obBscDespesa->obCampoCod->setMaxLength ( 5                          );
    $obBscDespesa->obCampoCod->setValue     ( $inCodDotacao              );
    $obBscDespesa->obCampoCod->setAlign     ("left"                      );
    $obBscDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('buscaDotacao','".$pgOcul."','".
    $pgList."','','".Sessao::getId()."'); }");
    $obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacao','stNomDotacao','inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbNomeEntidade);
    $obFormulario->addComponente( $obBscDespesa   );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $js1 = "d.getElementById('spnPesquisa').innerHTML = '".$stHtml."';";
    $js1.=" d.getElementById('stEval').value = '';";

    return $js1;
}

function montaFiltroRecurso()
{
    $obRegra            = new ROrcamentoDespesa;
    $obRegra->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
    $stMascaraRubrica = $obRegra->obROrcamentoClassificacaoDespesa->recuperaMascara();
    $obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

    $obRegra->obROrcamentoRecurso->setExercicio( Sessao::getExercicio() );
    $obRegra->obROrcamentoRecurso->listar( $rsRecurso );

    // Define Objeto Select para Nome da Entidade
    $obCmbNomeEntidade = new Select;
    $obCmbNomeEntidade->setRotulo        ( "Entidade" );
    $obCmbNomeEntidade->setTitle         ( "Selecione a entidade." );
    $obCmbNomeEntidade->setName          ( "inCodEntidade"               );
    $obCmbNomeEntidade->setId            ( "inCodEntidade"               );
    $obCmbNomeEntidade->setValue         ( $inCodigoEntidade              );

    // Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
    // Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
    if ($rsEntidades->getNumLinhas()>1) {
        $obCmbNomeEntidade->addOption              ( "", "Selecione"               );
    }
    $obCmbNomeEntidade->obEvento->setOnChange( "limparCampos();"          );
    $obCmbNomeEntidade->setCampoId       ( "cod_entidade"                 );
    $obCmbNomeEntidade->setCampoDesc     ( "nom_cgm"                      );
    $obCmbNomeEntidade->preencheCombo    ( $rsEntidades                   );
    $obCmbNomeEntidade->setNull          ( false                          );

    //RECURSO
/*  $obTxtRecurso = new TextBox;
    $obTxtRecurso->setRotulo              ( "Recurso"                       );
    $obTxtRecurso->setTitle               ( "Informe o Recurso Vinculado que deseja pesquisar." );
    $obTxtRecurso->setName                ( "inCodRecursoTxt"               );
    $obTxtRecurso->setValue               ( $inCodRecursoTxt                );
    $obTxtRecurso->setSize                ( 6                               );
    $obTxtRecurso->setMaxLength           ( 4                               );
    $obTxtRecurso->setInteiro             ( true                            );

    $obCmbRecurso = new Select;
    $obCmbRecurso->setRotulo              ( "Recurso"                       );
    $obCmbRecurso->setName                ( "inCodRecurso"                  );
    $obCmbRecurso->setValue               ( $inCodRecurso                   );
    $obCmbRecurso->setStyle               ( "width: 200px"                  );
    $obCmbRecurso->setCampoID             ( "cod_recurso"                     );
    $obCmbRecurso->setCampoDesc           ( "nom_recurso"                     );
    $obCmbRecurso->addOption              ( "", "Selecione"                 );
    $obCmbRecurso->preencheCombo          ( $rsRecurso                      ); */
    include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
    $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
    $obIMontaRecursoDestinacao->setFiltro ( true );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbNomeEntidade);
    $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
    //$obFormulario->addComponenteComposto( $obTxtRecurso,$obCmbRecurso );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $js1 = "d.getElementById('spnPesquisa').innerHTML = '".$stHtml."';";
    $js1.=" d.getElementById('stEval').value = '';";

    return $js1;
}

switch ($stCtrl) {
    case 'montaFiltroEntidades':
        $js = montaFiltroEntidades();
       SistemaLegado::executaFrameOculto( $js );
    break;
    case 'montaFiltroDotacao':
        $js = montaFiltroDotacao();
        SistemaLegado::executaFrameOculto( $js );
    break;
    case 'montaFiltroRecurso':
        $js = montaFiltroRecurso();
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodDespesa'] );
        $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obRegra->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
        $obRegra->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
        $obRegra->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'f.inCodDespesa.focus();';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
     break;

     case 'buscaDotacao':
            $obRegra->setCodDespesa( $_REQUEST["inCodDotacao"] );
            $obRegra->setExercicio( Sessao::getExercicio() );
            $obRegra->listarDespesaUsuario( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo( "descricao" );
            if (!$stNomDespesa) {
                $js .= 'f.inCodDotacao.value = "";';
                $js .= 'f.inCodDotacao.focus();';
                $js .= 'd.getElementById("stNomDotacao").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDotacao"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomDespesa = $rsDespesa->getCampo( "descricao" );
                $js .= 'd.getElementById("stNomDotacao").innerHTML = "'.$stNomDespesa.'";';
            }

        SistemaLegado::executaFrameOculto($js);
     break;

    case "validaData":
        $validaDataInicial = explode("/",$_REQUEST["stDataInicial"]);
        $validaDataFinal = explode("/",$_REQUEST["stDataFinal"]);

        if ($_POST['stDataInicial']) {
            if (substr($_POST['stDataInicial'],6,4) <> $_REQUEST["stExercicio"]) {
                SistemaLegado::exibeAviso(urlencode("A Data Inicial deve ser do ano '".$_REQUEST["stExercicio"] . "'!"),"","erro");
                $js .="f.stDataInicial.value = '' ;\n" ;
                $js .= "f.stDataInicial.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
            if ($validaDataInicial[1] > 12) {
                SistemaLegado::exibeAviso(urlencode("O mês deve ser inferior a 12  !"),"","erro");
                $js .="f.stDataInicial.value = '' ;\n" ;
                $js .= "f.stDataInicial.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
            if ($validaDataInicial[0] > 31) {
                SistemaLegado::exibeAviso(urlencode("O dia deve ser inferior a 31  !"),"","erro");
                $js .="f.stDataInicial.value = '' ;\n" ;
                $js .= "f.stDataInicial.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
        }
        if ($_POST['stDataFinal']) {
            if (substr($_POST['stDataFinal'],6,4) <> $_REQUEST["stExercicio"]) {
                SistemaLegado::exibeAviso(urlencode("A Data Final deve ser do ano '".$_REQUEST["stExercicio"] . "'!"),"","erro");
                $js .="f.stDataFinal.value = '' ;\n" ;
                $js .= "f.stDataFinal.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
            if ($validaDataInicial[1] == $validaDataFinal[1]) {
                if ($validaDataInicial[0] > $validaDataFinal[0]) {
                    SistemaLegado::exibeAviso(urlencode("A Data final deve ser maior que a data inicial dia, ".$validaDataFinal[0]."/".$validaDataFinal[1]."/".$validaDataFinal[2]." é menor que ".$validaDataInicial[0]."/".$validaDataInicial[1]."/".$validaDataInicial[2]."  !"),"","erro");
                    $js .="f.stDataFinal.value = '' ;\n" ;
                    $js .= "f.stDataFinal.focus(); \n";
                    SistemaLegado::executaFrameOculto($js);
                }
            }
            if ($validaDataInicial[1] > $validaDataFinal[1]) {
                SistemaLegado::exibeAviso(urlencode("A Data final deve ser maior que a data inicial dia, ".$validaDataFinal[0]."/".$validaDataFinal[1]."/".$validaDataFinal[2]." é menor que ".$validaDataInicial[0]."/".$validaDataInicial[1]."/".$validaDataInicial[2]."  !"),"","erro");
                $js .="f.stDataFinal.value = '' ;\n" ;
                $js .= "f.stDataFinal.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
            if ($validaDataFinal[1] > 12) {
                SistemaLegado::exibeAviso(urlencode("O mês deve ser inferior a 12  !"),"","erro");
                $js .="f.stDataFinal.value = '' ;\n" ;
                $js .= "f.stDataFinal.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
            if ($validaDataFinal[0] > 31) {
                SistemaLegado::exibeAviso(urlencode("O dia deve ser inferior a 31  !"),"","erro");
                $js .="f.stDataFinal.value = '' ;\n" ;
                $js .= "f.stDataFinal.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }

        }
    break;

}

?>
