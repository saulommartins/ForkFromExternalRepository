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
    * Página de Formulário da Configuração do modulo arrecadação
    * Data de Criação   : 24/07/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FMManterConfiguracaoGrupos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.01

*/

/*
$Log$
Revision 1.6  2007/07/19 16:05:03  cercato
Bug #9687#

Revision 1.5  2007/07/19 15:44:07  cercato
Bug #9687#

Revision 1.4  2006/10/23 17:41:36  fabio
adicionado grupo de credito para escrituracao de receita

Revision 1.3  2006/09/15 11:02:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

//include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRARRConfiguracao = new RARRConfiguracao;

$obErro = $obRARRConfiguracao->consultar();

$stMascara = "";
$obRARRGrupo = new RARRGrupo;
$obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
$stMascara .= "/9999";

$obRARRGrupo->listarGrupos( $rsListaGrupos );

$rsListaGruposSelecionados = $obRARRConfiguracao->getRSSuperSimples();
$arSelecionados = $rsListaGruposSelecionados->getElementos();
$arListaGrupos = $rsListaGrupos->getElementos();
for ( $inX=0; $inX<count( $arSelecionados ); $inX++ ) {
    for ( $inY=0; $inY<count( $arListaGrupos ); $inY++ ) {
        if ( ($arSelecionados[$inX]["cod_grupo"] == $arListaGrupos[$inY]["cod_grupo"]) && ($arSelecionados[$inX]["ano_exercicio"] == $arListaGrupos[$inY]["ano_exercicio"]) ) {
            $arSelecionados[$inX]["descricao"] = $arListaGrupos[$inY]["descricao"];
            for ( $inZ=$inY; $inZ<count( $arListaGrupos ) - 1; $inZ++ ) {
                $arListaGrupos[$inZ] = $arListaGrupos[$inZ+1];
            }

            array_pop($arListaGrupos);
            break;
        }
    }
}

$rsListaGrupos->preenche( $arListaGrupos );
$rsListaGruposSelecionados->preenche( $arSelecionados );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) || $stAcao == "alterar" ) {
    $stAcao = "alterarGrupos";
}

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

//grupo de creditos para ITBI
$obBscITBI = new BuscaInner;
$obBscITBI->setRotulo    ( "Transferência de Imóveis" );
$obBscITBI->setTitle     ( "Grupo de créditos para o cálculo de ITBI." );
$obBscITBI->setId        ( "stNomCredito" );
$obBscITBI->setNull      ( false );
$obBscITBI->obCampoCod->setName  ( "inNumCredito" );
$obBscITBI->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoCreditoITBI() );
$obBscITBI->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscITBI->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumCredito','stNomCredito','todos','".Sessao::getId()."','800','550');" );
$obBscITBI->obCampoCod->setMascara( $stMascara );

//Grupo de creditos IPTU
$obBscIPTU = new BuscaInner;
$obBscIPTU->setRotulo    ( "IPTU" );
$obBscIPTU->setTitle     ( "Grupo de créditos para o cálculo de IPTU." );
$obBscIPTU->setId        ( "stNomCreditoIPTU" );
$obBscIPTU->setNull      ( false );
$obBscIPTU->obCampoCod->setName  ( "inNumCreditoIPTU" );
$obBscIPTU->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoCreditoIPTU() );
$obBscIPTU->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscIPTU->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumCreditoIPTU','stNomCreditoIPTU','todos','".Sessao::getId()."','800','550');" );
$obBscIPTU->obCampoCod->setMascara( $stMascara );

//grupo de creditos para Escrituracao de Receitas
$obBscEscrituracao = new BuscaInner;
$obBscEscrituracao->setRotulo    ( "Escrituração de Receitas" );
$obBscEscrituracao->setTitle     ( "Grupo de créditos para a Escrituração de Receitas." );
$obBscEscrituracao->setId        ( "stNomGrupoEscrituracao" );
$obBscEscrituracao->setNull      ( true );
$obBscEscrituracao->obCampoCod->setName  ( "inNumGrupoEscrituracao" );
$obBscEscrituracao->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao() );
$obBscEscrituracao->obCampoCod->obEvento->setOnChange("buscaValor('buscaEscrituracaoReceita');");
$obBscEscrituracao->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumGrupoEscrituracao','stNomGrupoEscrituracao','todos','".Sessao::getId()."','800','550');" );
$obBscEscrituracao->obCampoCod->setMascara( $stMascara );

//grupo de creditos para Nota Avulsa
$obBscNotaAvulsa = new BuscaInner;
$obBscNotaAvulsa->setRotulo    ( "Nota Avulsa" );
$obBscNotaAvulsa->setTitle     ( "Grupo de créditos para a Nota Avulsa." );
$obBscNotaAvulsa->setId        ( "stNomGrupoNotaAvulsa" );
$obBscNotaAvulsa->setNull      ( true );
$obBscNotaAvulsa->obCampoCod->setName  ( "inNumGrupoNotaAvulsa" );
$obBscNotaAvulsa->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoNotaAvulsa() );
$obBscNotaAvulsa->obCampoCod->obEvento->setOnChange("buscaValor('buscaNotaAvulsa');");
$obBscNotaAvulsa->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumGrupoNotaAvulsa','stNomGrupoNotaAvulsa','todos','".Sessao::getId()."','800','550');" );
$obBscNotaAvulsa->obCampoCod->setMascara( $stMascara );

//*Lançamento Automático Geral
$obBscLancAutGer = new BuscaInner;
$obBscLancAutGer->setRotulo    ( "Lançamento Automático Geral" );
$obBscLancAutGer->setTitle     ( "Grupo de créditos para lançamentos automáticos gerais." );
$obBscLancAutGer->setId        ( "stNomLancAutGerCredito" );
$obBscLancAutGer->setNull      ( true );
$obBscLancAutGer->obCampoCod->setName  ( "inNumLancAutGerCredito" );
$obBscLancAutGer->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoDiferencaGeral() );
$obBscLancAutGer->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreditoAutGer');");
$obBscLancAutGer->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumLancAutGerCredito','stNomLancAutGerCredito','todos','".Sessao::getId()."','800','550');" );
$obBscLancAutGer->obCampoCod->setMascara( $stMascara );

//*Lançamento Automático Imobiliário
$obBscLancAutImo = new BuscaInner;
$obBscLancAutImo->setRotulo    ( "Lançamento Automático Imobiliário" );
$obBscLancAutImo->setTitle     ( "Grupo de créditos para lançamentos referentes ao cadastro imobiliário." );
$obBscLancAutImo->setId        ( "stNomLancAutImoCredito" );
$obBscLancAutImo->setNull      ( true );
$obBscLancAutImo->obCampoCod->setName  ( "inNumLancAutImoCredito" );
$obBscLancAutImo->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoDiferencaImob() );
$obBscLancAutImo->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreditoAutImo');");
$obBscLancAutImo->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumLancAutImoCredito','stNomLancAutImoCredito','todos','".Sessao::getId()."','800','550');" );
$obBscLancAutImo->obCampoCod->setMascara( $stMascara );

//*Lançamento Automático Econômico
$obBscLancAutEco = new BuscaInner;
$obBscLancAutEco->setRotulo    ( "Lançamento Automático Econômico" );
$obBscLancAutEco->setTitle     ( "Grupo de créditos para lançamentos referentes ao cadastro econômico." );
$obBscLancAutEco->setId        ( "stNomLancAutEcoCredito" );
$obBscLancAutEco->setNull      ( true );
$obBscLancAutEco->obCampoCod->setName  ( "inNumLancAutEcoCredito" );
$obBscLancAutEco->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoDiferencaEcon() );
$obBscLancAutEco->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreditoAutEco');");
$obBscLancAutEco->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumLancAutEcoCredito','stNomLancAutEcoCredito','todos','".Sessao::getId()."','800','550');" );
$obBscLancAutEco->obCampoCod->setMascara( $stMascara );

//*Lançamento Automático Acréscimos Geral
$obBscLancAutAcrGeral = new BuscaInner;
$obBscLancAutAcrGeral->setRotulo    ( "Lançamento Automático Acréscimos Geral" );
$obBscLancAutAcrGeral->setTitle     ( "Grupo de créditos para lançamentos de acréscimos automáticos gerais." );
$obBscLancAutAcrGeral->setId        ( "stNomLancAutAcrGeralCredito" );
$obBscLancAutAcrGeral->setNull      ( true );
$obBscLancAutAcrGeral->obCampoCod->setName  ( "inNumLancAutAcrGeralCredito" );
$obBscLancAutAcrGeral->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoDiferencaAcrescimoGeral() );
$obBscLancAutAcrGeral->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreditoAcrGeral');");
$obBscLancAutAcrGeral->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumLancAutAcrGeralCredito','stNomLancAutAcrGeralCredito','todos','".Sessao::getId()."','800','550');" );
$obBscLancAutAcrGeral->obCampoCod->setMascara( $stMascara );

//*Lançamento Automático Acréscimos Imobiliário
$obBscLancAutAcrImob = new BuscaInner;
$obBscLancAutAcrImob->setRotulo    ( "Lançamento Automático Acréscimos Imobiliário" );
$obBscLancAutAcrImob->setTitle     ( "Grupo de créditos para lançamentos de acréscimos referentes ao cadastro imobiliário." );
$obBscLancAutAcrImob->setId        ( "stNomLancAutAcrImobCredito" );
$obBscLancAutAcrImob->setNull      ( true );
$obBscLancAutAcrImob->obCampoCod->setName  ( "inNumLancAutAcrImobCredito" );
$obBscLancAutAcrImob->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoDiferencaAcrescimoImob() );
$obBscLancAutAcrImob->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreditoAutAcrImob');");
$obBscLancAutAcrImob->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumLancAutAcrImobCredito','stNomLancAutAcrImobCredito','todos','".Sessao::getId()."','800','550');" );
$obBscLancAutAcrImob->obCampoCod->setMascara( $stMascara );

//*Lançamento Automático Acréscimos Econômico
$obBscLancAutAcrEco = new BuscaInner;
$obBscLancAutAcrEco->setRotulo    ( "Lançamento Automático Acréscimos Econômico" );
$obBscLancAutAcrEco->setTitle     ( "Grupo de créditos para lançamentos de acréscimos referentes ao cadastro econômico." );
$obBscLancAutAcrEco->setId        ( "stNomLancAutAcrEcoCredito" );
$obBscLancAutAcrEco->setNull      ( true );
$obBscLancAutAcrEco->obCampoCod->setName  ( "inNumLancAutAcrEcoCredito" );
$obBscLancAutAcrEco->obCampoCod->setValue ( $obRARRConfiguracao->getCodigoGrupoDiferencaAcrescimoEcon() );
$obBscLancAutAcrEco->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreditoAutAcrEco');");
$obBscLancAutAcrEco->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inNumLancAutAcrEcoCredito','stNomLancAutAcrEcoCredito','todos','".Sessao::getId()."','800','550');" );
$obBscLancAutAcrEco->obCampoCod->setMascara( $stMascara );

$obCmbOrdemEntrega = new SelectMultiplo();
$obCmbOrdemEntrega->setName   ('inCodAtributoSelecionados');
$obCmbOrdemEntrega->setRotulo ( "Parcelamento de mais de um exercício" );
$obCmbOrdemEntrega->setNull   ( true );
$obCmbOrdemEntrega->setTitle  ( "Grupos de créditos que podem ter parcelamentos em mais de um exercício." );

// lista de atributos disponiveis
$obCmbOrdemEntrega->SetNomeLista1 ('inCodOrdemDisponivel');
$obCmbOrdemEntrega->setCampoId1   ('[cod_grupo]/[ano_exercicio]');
$obCmbOrdemEntrega->setCampoDesc1 ('[cod_grupo]/[ano_exercicio] - [descricao]');
$obCmbOrdemEntrega->SetRecord1    ( $rsListaGrupos );

// lista de atributos selecionados
$obCmbOrdemEntrega->SetNomeLista2 ('inCodOrdemSelecionados');
$obCmbOrdemEntrega->setCampoId2   ('[cod_grupo]/[ano_exercicio]');
$obCmbOrdemEntrega->setCampoDesc2 ('[cod_grupo]/[ano_exercicio] - [descricao]');
$obCmbOrdemEntrega->SetRecord2    ( $rsListaGruposSelecionados );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ( "inExercicio" );

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"              );
$obBtnClean->setValue                   ( "Cancelar"              );
$obBtnClean->setTipo                    ( "button"                );
$obBtnClean->obEvento->setOnClick       ( "document.frm.reset();" );
$obBtnClean->setDisabled                ( false                   );

$obBtnOK = new Ok;
$botoesForm     = array ( $obBtnOK , $obBtnClean );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm );
$obFormulario->addHidden             ( $obHdnExercicio );
$obFormulario->addHidden             ( $obHdnCtrl );
$obFormulario->addHidden             ( $obHdnAcao );
$obFormulario->addTitulo             ( "Dados para Configuração de Grupos de Créditos" );
$obFormulario->addComponente         ( $obBscITBI );
$obFormulario->addComponente         ( $obBscIPTU );
$obFormulario->addComponente         ( $obBscEscrituracao );
$obFormulario->addComponente         ( $obBscNotaAvulsa);
$obFormulario->addComponente         ( $obBscLancAutGer );
$obFormulario->addComponente         ( $obBscLancAutImo );
$obFormulario->addComponente         ( $obBscLancAutEco );
$obFormulario->addComponente         ( $obBscLancAutAcrGeral );
$obFormulario->addComponente         ( $obBscLancAutAcrImob );
$obFormulario->addComponente         ( $obBscLancAutAcrEco );
$obFormulario->addComponente         ( $obCmbOrdemEntrega );

//$obFormulario->OK();
$obFormulario->defineBarra($botoesForm);
$obFormulario->show();

if ($obRARRConfiguracao->getCodigoGrupoCreditoITBI() ) {
    SistemaLegado::executaFrameOculto( "buscaValor('buscaTodos');" );
}
?>
