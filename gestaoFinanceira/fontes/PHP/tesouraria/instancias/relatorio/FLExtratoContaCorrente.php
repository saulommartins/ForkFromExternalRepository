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
    *
    * Página de Formulario de relatório de Extrato de Conta C/c
    * Data de Criação   : 21/07/2014
    *
    * @author Desenvolvedor: Carolina Schwaab Marçal
    *
    * @ignore
    *
    * $id:$
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioExtratoBancario.class.php" );
include_once ( CAM_GF_CONT_COMPONENTES. "IIntervaloPopUpContaBanco.class.php" );
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";
include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ExtratoContaCorrente";
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


$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."relatorio/OCExtratoContaCorrente.php" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setRotulo ( "Entidade" );
$obCmbEntidades->setTitle  ( "Entidade" );
$obCmbEntidades->setNull   ( false );


if ($rsUsuariosDisponiveis->getNumLinhas()==1) {
       $rsUsuariosSelecionados = $rsUsuariosDisponiveis;
       $rsUsuariosDisponiveis  = new RecordSet;
}

$obCmbEntidades->SetNomeLista1 ('inCodigoEntidadesDisponiveis');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsUsuariosDisponiveis );

$obCmbEntidades->SetNomeLista2 ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsUsuariosSelecionados );

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

$obBscEntidade = new IIntervaloPopUpContaBanco;

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->listar( $rsSistemaContabil );
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->listar( $rsClassificacaoContabil );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->listar( $rsRecurso );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->recuperaMascaraRecurso( $stMascaraRecurso );
$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );

$arBancos = $rsBanco->getElementos();
foreach ($arBancos as $arBanco) {
    if ($arBanco['cod_banco'] != 0) {
        $arNewBancos[] = $arBanco;
    }
}
$rsBanco->setElementos( $arNewBancos );
$rsBanco->setNumLinhas( count( $arNewBancos ) );

$obTxtBanco = new TextBox;
$obTxtBanco->setName     ( "inNumBanco"        );
$obTxtBanco->setId       ( "inNumBanco"        );
$obTxtBanco->setValue    ( $_REQUEST['inNumBanco']         );
$obTxtBanco->setRotulo   ( "Banco"            );
$obTxtBanco->setMaxlength( 5                   );
$obTxtBanco->setTitle    ( "Selecione o banco" );
$obTxtBanco->setDisabled ( $boDisabled         );
$obTxtBanco->setInteiro  ( true                );
$obTxtBanco->obEvento->setOnChange  ( " if(this.value != '') montaParametrosGET('MontaAgencia');
                                        else {
                                            document.getElementById('inCodBanco').value = '';
                                            document.getElementById('inCodAgencia').value = '';
                                            document.getElementById('stContaCorrente').value = '';
                                        }
                                    ");

$obHdnBanco = new Hidden;
$obHdnBanco->setName('inCodBanco');
$obHdnBanco->setId ('inCodBanco');
$obHdnBanco->setValue ( $_REQUEST['inCodBanco'] );

$obCmbBanco = new Select;
$obCmbBanco->setName      ( "stNomeBanco"   );
$obCmbBanco->setId        ( "stNomeBanco"   );
$obCmbBanco->setValue     ( $_REQUEST['inNumBanco']   );
$obCmbBanco->setDisabled  ( $boDisabled     );
$obCmbBanco->addOption    ( "", "Selecione" );
$obCmbBanco->setCampoId   ( "num_banco"     );
$obCmbBanco->setCampoDesc ( "nom_banco"     );
$obCmbBanco->preencheCombo( $rsBanco        );
$obCmbBanco->setNull(true);
$obCmbBanco->obEvento->setOnChange  ( " montaParametrosGET('MontaAgencia');");

$obTxtAgencia = new TextBox;
$obTxtAgencia->setName     ( "inNumAgencia"        );
$obTxtAgencia->setId       ( "inNumAgencia"        );
$obTxtAgencia->setValue    ( $_REQUEST['inNumAgencia'] );
$obTxtAgencia->setRotulo   ( "Agência"            );
$obTxtAgencia->setMaxLength( 10                    );
$obTxtAgencia->setTitle    ( "Selecione a agência" );
$obTxtAgencia->setDisabled ( $boDisabled           );
$obTxtAgencia->setNull(true);
$obTxtAgencia->obEvento->setOnChange  ( " montaParametrosGET('MontaContaCorrente'); ");

$obHdnAgencia = new Hidden;
$obHdnAgencia->setName ( 'inCodAgencia' );
$obHdnAgencia->setId ( 'inCodAgencia' );
$obHdnAgencia->setValue ( $_REQUEST['inCodAgencia'] );

$obCmbAgencia = new Select;
$obCmbAgencia->setName      ( "stNomeAgencia"  );
$obCmbAgencia->setId        ( "stNomeAgencia"  );
$obCmbAgencia->setValue     ( $_REQUEST['inNumAgencia']  );
$obCmbAgencia->addOption    ( "", "Selecione"  );
$obCmbAgencia->setDisabled  ( $boDisabled      );
$obCmbAgencia->setNull(true);
$obCmbAgencia->obEvento->setOnChange( " montaParametrosGET('MontaContaCorrente'); ");

$obHdnContaCorrente = new Hidden();
$obHdnContaCorrente->setName( 'inContaCorrente');
$obHdnContaCorrente->setId  ( 'inContaCorrente');
$obHdnContaCorrente->setValue( $_REQUEST['inContaCorrente']);

$obCmbContaCorrente = new Select();
$obCmbContaCorrente->setRotulo	 ( "Conta Corrente");
$obCmbContaCorrente->setName      ( "stContaCorrente"    );
$obCmbContaCorrente->setId        ( "stContaCorrente"    );
$obCmbContaCorrente->setValue     ( $_REQUEST['stContaCorrente']   );
$obCmbContaCorrente->addOption    ( "", "Selecione"          );
$obCmbContaCorrente->setCampoId   ( "num_conta_corrente"     );
$obCmbContaCorrente->setCampoDesc ( "num_conta_corrente"     );
$obCmbContaCorrente->setDisabled  ( $boDisabled );
$obCmbContaCorrente->setNull(true);
$obCmbContaCorrente->obEvento->setOnChange  ( " montaParametrosGET('BuscaContaCorrente'); ");

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

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

$obDemonstrarCredorS = new Radio;
$obDemonstrarCredorS->setRotulo ( "Demonstrar Credor" );
$obDemonstrarCredorS->setName   ( "stDemonstrarCredor" );
$obDemonstrarCredorS->setChecked( true );
$obDemonstrarCredorS->setValue  ( "sim" );
$obDemonstrarCredorS->setLabel  ( "Sim" );
$obDemonstrarCredorS->setNull   ( true );

$obDemonstrarCredorN = new Radio;
$obDemonstrarCredorN->setName   ( "stDemonstrarCredor" );
$obDemonstrarCredorN->setChecked( true );
$obDemonstrarCredorN->setValue  ( "nao" );
$obDemonstrarCredorN->setLabel  ( "Não" );
$obDemonstrarCredorN->setNull   ( true );

$arDemonstrarCredor = array($obDemonstrarCredorS, $obDemonstrarCredorN);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setOpcaoAssinaturas( false );
$obMontaAssinaturas->setCampoEntidades( 'inCodigoEntidadesSelecionadas' );
$obMontaAssinaturas->setFuncaoJS();
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCaminho         );
$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addHidden        ( $obHdnAcao            );

$obFormulario->addHidden( $obHdnBanco );
$obFormulario->addHidden( $obHdnAgencia );
$obFormulario->addHidden( $obHdnContaCorrente );

$obFormulario->addTitulo        ( "Dados para Filtro"   );
$obFormulario->addComponente    ( $obCmbEntidades        );
$obFormulario->addComponente    ( $obTxtExercicio       );
$obFormulario->addComponente    ( $obPeriodo         );
$obFormulario->addComponente    ( $obBscEntidade        );
$obFormulario->addComponenteComposto( $obTxtBanco  , $obCmbBanco   );
$obFormulario->addComponenteComposto( $obTxtAgencia, $obCmbAgencia );
$obFormulario->addComponente( $obCmbContaCorrente );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->agrupaComponentes ( $arDemonstrarCredor );
$obFormulario->agrupaComponentes ( $arRdSemMovimentacao );

$obMontaAssinaturas->geraFormulario ( $obFormulario );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
