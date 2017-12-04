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
    * Pagina de filtro para o relatório Metas de execução orçamentaria
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: FMReciboDespesaExtra.php 63736 2015-10-02 17:03:15Z franver $

    * Casos de uso: uc-02.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpDotacao.class.php';
include_once CAM_GF_EMP_COMPONENTES.'IPopUpCredor.class.php';
include_once CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php';
include_once CAM_FW_HTML.'MontaOrgaoUnidade.class.php';
include_once CLA_IAPPLETTERMINAL;

//Define o nome dos arquivos PHP
$stPrograma = "ReciboDespesaExtra";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
include_once($pgJS);

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

/// Entidade
$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->obTextBox->obEvento->setOnChange( 'getIMontaAssinaturas()' );
$obEntidadeUsuario->obSelect->obEvento->setOnChange( 'getIMontaAssinaturas()' );
$obEntidadeUsuario->obSelect->obEvento->setOnChange( "buscaValor('preencheDataEmissao')"  );
$obEntidadeUsuario->obTextBox->obEvento->setOnChange( "buscaValor('preencheDataEmissao')" );

///Data Emissão
$obTextData = new Data;
$obTextData->setName   ( 'dtDataEmissao'              );
$obTextData->setID     ( 'dtDataEmissao'              );
$obTextData->setRotulo ( 'Data Emissão'               );
$obTextData->setNull   ( false                        );
$obTextData->setTitle  ( 'Informe a data de emissão.' );

// Busca de Credor
$obPopUpCredor = new IPopUpCredor( $obForm );
$obPopUpCredor->setNull ( true );

/// Busca de recurso
include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

/// busca de Conta Caixa Banco TODO: tem que ver com o kaco o parametro pra esta instancia
$obPopUpContaCaixaBanco = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect );
$obPopUpContaCaixaBanco->setID               ( 'innerContaBanco'                  );
$obPopUpContaCaixaBanco->setName             ( 'innerContaBanco'                  );
$obPopUpContaCaixaBanco->obCampoCod->setName ("inCodContaBanco"                   );
$obPopUpContaCaixaBanco->setRotulo           ( 'Conta Caixa/Banco'                );
if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao ) == 11 && SistemaLegado::pegaConfiguracao('cod_municipio', 2, Sessao::getExercicio(), $boTransacao ) == 79 && SistemaLegado::comparaDatas($stDataFinalAno, $stDataAtual, true))
$obPopUpContaCaixaBanco->setTipoBusca        ( 'tes_pagamento_extra_caixa_banco_recurso_fixo'  );
else
$obPopUpContaCaixaBanco->setTipoBusca        ( 'tes_pagamento_extra_caixa_banco'  );

/// busca de conta Despesa
$obPopUpContaDespesa = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect );
$obPopUpContaDespesa->setID              ( 'innerContaDespesa'           );
$obPopUpContaDespesa->setName            ( 'innerContaDespesa'           );
$obPopUpContaDespesa->obCampoCod->setName( "inCodContaDespesa"           );
$obPopUpContaDespesa->setRotulo          ( 'Conta de Despesa'            );
$obPopUpContaDespesa->setTipoBusca       ( 'tes_pagamento_extra_despesa' );
$obPopUpContaDespesa->setNull            ( false                         );

///Valor do enpenho
$obTextValor = new Moeda;
$obTextValor->setName   ( 'txtValor'         );
$obTextValor->setID     ( 'txtValor'         );
$obTextValor->setRotulo ( 'Valor'            );
$obTextValor->setNull   ( false              );
$obTextValor->setTitle  ( 'Informe o valor.' );

/// texto de historico
$obTextHistorico = new TextArea;
$obTextHistorico->setName  ( 'txtHistorico'        );
$obTextHistorico->setID    ( 'txtHistorico'        );
$obTextHistorico->setRotulo( 'Histórico'           );
$obTextHistorico->setTitle ( 'Informe o histórico' );
$obTextHistorico->setCols  ( 10 );
$obTextHistorico->setRows  ( 3 );
$obTextHistorico->setStyle ( "width: 48%" );
$obTextHistorico->setMaxCaracteres(445);

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obIApplet = new IAppletTerminal( $obForm );

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->definePapeisDisponiveis('recibo_despesa_extra');
$obMontaAssinaturas->setOpcaoAssinaturas( false );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                 );
$obFormulario->addHidden     ( $obHdnAcao              );
$obFormulario->addHidden     ( $obHdnCtrl              );
$obFormulario->addHidden     ( $obIApplet              );
$obFormulario->addComponente ( $obEntidadeUsuario      );
$obFormulario->addComponente ( $obTextData             );
$obFormulario->addComponente ( $obPopUpCredor          );
/*
 * Verificando se o munícipio Bom Despacho e se a data é menor ou igual a 31/12/2015
 * Se essas condições não forem verdadeiras será mostrado o campo de Recurso para os outros munícipios
 * Após o dia 31/12/2015 será mostrado para Bom Despacho o Campo Recurso
 *
 **/
$stDataFinalAno = '31/12/2015';
$stDataAtual    = date('d/m/Y');
if ( !(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao ) == 11 && SistemaLegado::pegaConfiguracao('cod_municipio', 2, Sessao::getExercicio(), $boTransacao ) == 79 && SistemaLegado::comparaDatas($stDataFinalAno, $stDataAtual, true)))
$obIMontaRecursoDestinacao->geraFormulario (  $obFormulario );
$obFormulario->addComponente ( $obPopUpContaCaixaBanco );
$obFormulario->addComponente ( $obPopUpContaDespesa    );
$obFormulario->addComponente ( $obTextValor            );
$obFormulario->addComponente ( $obTextHistorico        );

$stOnclickOkJs = " if ( Valida() ){
                        document.frm.Ok.disabled = true;
                        BloqueiaFrames(true,false);
                        document.frm.submit();
                   }";

$obOk  = new Ok;
$obOk->setId   ("Ok");
$obOk->setName ("Ok");
$obOk->obEvento->setOnClick($stOnclickOkJs);

$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->setId   ( "limpar" );
$obLimpar->setName ( "limpar" );
$obLimpar->obEvento->setOnClick( "frm.reset(); frm.inCodEntidade.focus(); document.frm.Ok.disabled = false;" );

$obMontaAssinaturas->geraFormulario ( $obFormulario );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );

$obFormulario->show();

if ( $obMontaAssinaturas->getOpcaoAssinaturas() ) {
    echo $obMontaAssinaturas->disparaLista();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
