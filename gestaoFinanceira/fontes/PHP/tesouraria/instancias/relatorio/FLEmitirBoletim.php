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
    * Página de Formulario de relatório Emitir Boletim
    * Data de Criação   : 25/11/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31197 $
    $Name$
    $Autor:$
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.04.07
*/

/*
$Log$
Revision 1.10  2007/02/16 17:08:30  cako
Bug #8400#

Revision 1.9  2007/01/12 11:53:54  luciano
Bug #7781#

Revision 1.8  2006/07/05 20:39:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioEmitirBoletim.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"          );

//Define o nome dos arquivos PHP
$stPrograma = "EmitirBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$rsEntidadesDisponiveis = $rsEntidadesSelecionadas = new recordSet;
$obRTesourariaRelatorioEmitirBoletim  = new RTesourariaRelatorioEmitirBoletim;
$obRTesourariaRelatorioEmitirBoletim->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis, " ORDER BY cod_entidade");
$arFiltro = array();
while ( !$rsEntidadesDisponiveis->eof() ) {
    $arFiltro['entidade'][$rsEntidadesDisponiveis->getCampo( 'cod_entidade' )] = $rsEntidadesDisponiveis->getCampo( 'nom_cgm' );
    $rsEntidadesDisponiveis->proximo();
}
Sessao::write('filtroRelatorio',$arFiltro);
$rsEntidadesDisponiveis->setPrimeiroElemento();

$stEval = "
     stCampo = document.frm.inCodTerminal;
     if ( !isInt( stCampo.value ) ) {
         erro = true;
         mensagem += '@Campo Nr. Terminal de Caixa inválido!('+stCampo.value+')';
     }
     stCampo = document.frm.inCodTerminal;
     if ( trim( stCampo.value ) == '' ) {
         erro = true;
         mensagem += '@Campo Nr. Terminal de Caixa inválido!()';
     }
";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnEval = new HiddenEval();
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stEval );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."relatorio/OCEmitirBoletim.php" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define Objeto Select para armazenar entidades
$obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;

//Define objeto de select multiplo de entidade por usuários
$obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidadeUsuario->setNull(false);
$obISelectEntidadeUsuario->obSelect->setNull(false);
$obISelectEntidadeUsuario->obTextBox->setNull(false);
$obISelectEntidadeUsuario->obSelect->obEvento->setOnChange( "getIMontaAssinaturas(); montaParametrosGET('buscaBoletimPorData', 'inCodEntidade, stDtBoletim');" );
$obISelectEntidadeUsuario->obTextBox->obEvento->setOnChange( "getIMontaAssinaturas(); montaParametrosGET('buscaBoletimPorData', 'inCodEntidade, stDtBoletim');" );

// Define Objeto Select para tipo de emissão
$obCmbEmitir = new Select();
$obCmbEmitir->setRotulo( "Emitir"                           );
$obCmbEmitir->setTitle ( "Selecione o tipo de Relatório a ser emitido" );
$obCmbEmitir->setName  ( "stTipoEmissao"                    );
$obCmbEmitir->setId    ( "stTipoEmissao"                    );
$obCmbEmitir->setNull  ( false                              );
$obCmbEmitir->addOption( "caixa"  , "Demonstrativo Caixa"   );
$obCmbEmitir->addOption( "boletim", "Demonstrativo Boletim" );
//$obCmbEmitir->obEvento->setOnChange( "trocaObrigatoriedade(this.value);" );
$obCmbEmitir->obEvento->setOnChange("montaParametrosGET('habilitaDesabilitaContasSemMovimento');");

//Define Objeto Text para o terminal caixa
$obTxtTerminal = new TextBox;
$obTxtTerminal->setName      ( "inCodTerminal"                       );
$obTxtTerminal->setValue     ( $inCodTerminal                        );
//$obTxtTerminal->setRotulo    ( "<span id='rotuloTerminal'>*Nr. Terminal de Caixa<span>"               );
$obTxtTerminal->setRotulo    ( "Nr. Terminal de Caixa"               );
$obTxtTerminal->setTitle     ( "Informe o Número do Terminal Caixa"  );
$obTxtTerminal->setMaxLength ( 6                                     );
$obTxtTerminal->setSize      ( 6                                     );
$obTxtTerminal->setInteiro   ( true                                  );

// Define Objeto Data para data do terminal
$obTxtDtBoletim = new Data;
$obTxtDtBoletim->setRotulo( "Data do Boletim"           );
$obTxtDtBoletim->setTitle ( "Informe a Data do Boletim" );
$obTxtDtBoletim->setName  ( "stDtBoletim"               );
$obTxtDtBoletim->setId    ( "stDtBoletim"               );
$obTxtDtBoletim->setNull  ( false                       );
$obTxtDtBoletim->obEvento->setOnChange("montaParametrosGET('buscaBoletimPorData', 'inCodEntidade, stDtBoletim');");

//Define Objeto Text para número do boletim
$obTxtCodBoletim = new TextBox;
$obTxtCodBoletim->setId        ( "inCodBoletim"                );
$obTxtCodBoletim->setName      ( "inCodBoletim"                );
$obTxtCodBoletim->setValue     ( $inCodBoletim                 );
$obTxtCodBoletim->setRotulo    ( "Número Boletim"              );
$obTxtCodBoletim->setTitle     ( "Número do Boletim" );
$obTxtCodBoletim->setNull      ( true                          );
$obTxtCodBoletim->setMaxLength ( 10                            );
$obTxtCodBoletim->setSize      ( 15                            );
$obTxtCodBoletim->setInteiro   ( true                          );
$obTxtCodBoletim->setReadOnly  ( true   );

// Define objeto BuscaInner para cgm
$obBscCGM = new BuscaInner();
$obBscCGM->setRotulo                 ( "Usuário"                         );
$obBscCGM->setTitle                  ( "Informe o Usuário de Terminal"   );
$obBscCGM->setId                     ( "stNomCgm"                        );
$obBscCGM->setValue                  ( $stNomCgm                         );
$obBscCGM->setNull                   ( true                              );
$obBscCGM->obCampoCod->setName       ( "inNumCgm"                        );
$obBscCGM->obCampoCod->setSize       ( 10                                );
$obBscCGM->obCampoCod->setMaxLength  ( 8                                 );
$obBscCGM->obCampoCod->setValue      ( $inNumCgm                         );
$obBscCGM->obCampoCod->setAlign      ( "left"                            );
$obBscCGM->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm','stNomCgm','usuario','".Sessao::getId()."','800','550');");
$obBscCGM->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

$obSimNaoMovimentacaoConta = new SimNao();
$obSimNaoMovimentacaoConta->setRotulo ( "Listar Contas Sem Movimentação" );
$obSimNaoMovimentacaoConta->setName   ( 'boMovimentacaoConta'      );
$obSimNaoMovimentacaoConta->setNull   ( true                       );
$obSimNaoMovimentacaoConta->setTitle  ( "Informe se devem ser listadas contas sem movimentação");
$obSimNaoMovimentacaoConta->setChecked( 'SIM'                      );
$obSimNaoMovimentacaoConta->obRadioSim->setId('boMovimentacaoContaSim');
$obSimNaoMovimentacaoConta->obRadioNao->setId('boMovimentacaoContaNao');
$obSimNaoMovimentacaoConta->obRadioSim->setDisabled( true );
$obSimNaoMovimentacaoConta->obRadioNao->setDisabled( true );

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setOpcaoAssinaturas( false );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCaminho         );
$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addHidden        ( $obHdnAcao            );
//$obFormulario->addHidden        ( $obHdnEval, true      );
$obFormulario->addTitulo        ( "Dados para Filtro"   );
$obFormulario->addComponente    ( $obISelectEntidadeUsuario );
$obFormulario->addComponente    ( $obCmbEmitir          );
$obFormulario->addComponente    ( $obTxtTerminal        );
$obFormulario->addComponente    ( $obTxtDtBoletim       );
$obFormulario->addComponente    ( $obTxtCodBoletim      );
$obFormulario->addComponente    ( $obBscCGM             );
$obFormulario->addComponente    ( $obSimNaoMovimentacaoConta             );

$obMontaAssinaturas->geraFormulario ( $obFormulario );

$obFormulario->Ok();

$obFormulario->show();

if ( $obMontaAssinaturas->getOpcaoAssinaturas() ) {
    echo $obMontaAssinaturas->disparaLista();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
