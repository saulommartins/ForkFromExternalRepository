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
    * Página de Formulario de relatório de Extrato de Conta
    * Data de Criação   : 11/11/2005

    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31627 $
    $Name$
    $Autor: $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.04.10
*/

/*
$Log$
Revision 1.10  2007/07/04 18:11:31  leandro.zis
Bug #9362#

Revision 1.9  2007/05/30 19:25:14  bruce
Bug #9116#

Revision 1.8  2006/07/05 20:39:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioExtratoBancario.class.php" );

include_once ( CAM_GF_CONT_COMPONENTES. "IIntervaloPopUpContaBanco.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ExtratoBancario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;

$obRTesourariaRelatorioExtratoBancario  = new RTesourariaRelatorioExtratoBancario;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;
$stOrdem               = " ORDER BY C.nom_cgm";

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRTesourariaRelatorioExtratoBancario->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade($rsUsuariosDisponiveis, " ORDER BY cod_entidade");

$arFiltro = array();
while ( !$rsUsuariosDisponiveis->eof() ) {
    $arFiltro['entidade'][$rsUsuariosDisponiveis->getCampo( 'cod_entidade' )] = $rsUsuariosDisponiveis->getCampo( 'nom_cgm' );
    $rsUsuariosDisponiveis->proximo();
}
Sessao::write('filtroRelatorio',$arFiltro);
$rsUsuariosDisponiveis->setPrimeiroElemento();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."relatorio/OCExtratoBancario.php" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setRotulo ( "Entidade" );
$obCmbEntidades->setTitle  ( "Entidade" );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsUsuariosDisponiveis->getNumLinhas()==1) {
       $rsUsuariosSelecionados = $rsUsuariosDisponiveis;
       $rsUsuariosDisponiveis  = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodigoEntidadesDisponiveis');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsUsuariosDisponiveis );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsUsuariosSelecionados );

//Define Objeto Text para o Exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ( "stExercicio"                                  );
$obTxtExercicio->setValue     ( Sessao::getExercicio()                             );
$obTxtExercicio->setRotulo    ( "Exercício"                                    );
$obTxtExercicio->setTitle     ( "Informe o Exercício para o Extrato de Conta"  );
$obTxtExercicio->setNull      ( false                                          );
$obTxtExercicio->setMaxLength ( 4                                              );
$obTxtExercicio->setSize      ( 5                                              );

$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setNull            ( false );
$obPeriodo->setValue           ( 4 );

//Define o objeto INNER para armazenar a Conta Banco
$obBscEntidade = new IIntervaloPopUpContaBanco;

//Radios de Imprimir Contas sem Movimentação
$obRdSemMovimentacaoS = new Radio;
$obRdSemMovimentacaoS->setRotulo ( "Imprimir Contas sem Movimentação" );
$obRdSemMovimentacaoS->setName   ( "stImprimirSemMovimentacao" );
$obRdSemMovimentacaoS->setChecked( true );
$obRdSemMovimentacaoS->setValue  ( "sim" );
$obRdSemMovimentacaoS->setLabel  ( "Sim" );
$obRdSemMovimentacaoS->setNull   ( false );

$obRdSemMovimentacaoN = new Radio;
$obRdSemMovimentacaoN->setName   ( "stImprimirSemMovimentacao" );
$obRdSemMovimentacaoN->setValue  ( "nao" );
$obRdSemMovimentacaoN->setLabel  ( "Não" );
$obRdSemMovimentacaoN->setNull   ( false );

$arRdSemMovimentacao = array($obRdSemMovimentacaoS, $obRdSemMovimentacaoN);

//Radios de Quebrar Página por Conta
$obRdQuebraPagPorContaS = new Radio;
$obRdQuebraPagPorContaS->setRotulo ( "Quebrar Pagina por Conta" );
$obRdQuebraPagPorContaS->setName   ( "stQuebraPagPorConta" );
$obRdQuebraPagPorContaS->setChecked( true );
$obRdQuebraPagPorContaS->setValue  ( "sim" );
$obRdQuebraPagPorContaS->setLabel  ( "Sim" );
$obRdQuebraPagPorContaS->setNull   ( false );

$obRdQuebraPagPorContaN = new Radio;
$obRdQuebraPagPorContaN->setName   ( "stQuebraPagPorConta" );
$obRdQuebraPagPorContaN->setValue  ( "nao" );
$obRdQuebraPagPorContaN->setLabel  ( "Não" );
$obRdQuebraPagPorContaN->setNull   ( false );

$arRdQuebraPagPorConta = array($obRdQuebraPagPorContaS, $obRdQuebraPagPorContaN);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setOpcaoAssinaturas( false );
$obMontaAssinaturas->setCampoEntidades( 'inCodigoEntidadesSelecionadas' );
$obMontaAssinaturas->setFuncaoJS();
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCaminho         );
$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addHidden        ( $obHdnAcao            );

$obFormulario->addTitulo        ( "Dados para Filtro"   );
$obFormulario->addComponente    ( $obCmbEntidades        );
$obFormulario->addComponente    ( $obTxtExercicio       );
$obFormulario->addComponente    ( $obPeriodo         );
$obFormulario->addComponente    ( $obBscEntidade        );
$obFormulario->agrupaComponentes ( $arRdSemMovimentacao );
$obFormulario->agrupaComponentes ( $arRdQuebraPagPorConta );

$obMontaAssinaturas->geraFormulario ( $obFormulario );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
