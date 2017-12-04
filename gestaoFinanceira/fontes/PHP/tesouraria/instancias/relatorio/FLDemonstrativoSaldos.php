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
    * Página de Formulario de relatório Demostrativo de Saldos
    * Data de Criação   : 21/08/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2008-04-14 16:26:52 -0300 (Seg, 14 Abr 2008) $

    * Casos de uso: uc-02.04.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );
include_once( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpContaAnalitica.class.php"  );
include_once( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpEstruturalPlano.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "DemonstrativoSaldos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once $pgJs;
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$inCodEntidade  = '';
$stOrdenacao    = '';

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$inCodUF = SistemaLegado::pegaConfiguracao('cod_uf',2,Sessao::getExercicio(),$boTransacao);

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."relatorio/OCDemonstrativoSaldos.php" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnNomeEntidade = new Hidden;
$obHdnNomeEntidade->setId   ( "stEntidade" );
$obHdnNomeEntidade->setName ( "stEntidade" );
$obHdnNomeEntidade->setValue( "" );

$obROrcamentoEntidade = new ROrcamentoEntidade();
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$obTxtEntidade = new TextBox;
$obTxtEntidade->setName   ( "inCodEntidade"         );
$obTxtEntidade->setId     ( "inCodEntidade"         );
$obTxtEntidade->setValue  ( $inCodEntidade          );
$obTxtEntidade->setRotulo ( "Entidade"              );
$obTxtEntidade->setTitle  ( "Selecione a entidade." );
$obTxtEntidade->setInteiro( true                    );
$obTxtEntidade->setNull   ( false );

$obCmbEntidade = new Select;
$obCmbEntidade->setName      ( "stNomeEntidade" );
$obCmbEntidade->setId        ( "stNomeEntidade" );
$obCmbEntidade->setValue     ( $stNomeEntidade  );
$obCmbEntidade->addOption    ( "", "Selecione"  );
$obCmbEntidade->setCampoId   ( "cod_entidade"   );
$obCmbEntidade->setCampoDesc ( "nom_cgm"        );
$obCmbEntidade->preencheCombo( $rsEntidade      );
$obCmbEntidade->setNull      ( false );

$obPeriodo = new Periodicidade();
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setNull            ( false );
$obPeriodo->setValue           ( 4 );
$obPeriodo->obMes->setValue    ( date("m"));

SistemaLegado::executaFramePrincipal('preencheMes(1);');

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setRotulo  ( "Ordenação"        );
$obCmbOrdenacao->setName    ( "stOrdenacao"      );
$obCmbOrdenacao->setId      ( "stOrdenacao"      );
$obCmbOrdenacao->setValue   ( $stOrdenacao       );
$obCmbOrdenacao->addOption  ( "estrutural","Código Estrutural" );
$obCmbOrdenacao->addOption  ( "reduzido", "Código Reduzido"    );
$obCmbOrdenacao->addOption  ( "recurso", "Código Recurso"      );
if ( $inCodUF == 11 ) {
    $obCmbOrdenacao->addOption  ( "conta_corrente", "Conta Corrente" );
    $obCmbOrdenacao->obEvento->setOnChange(" if (jQuery('#stOrdenacao').val() == 'conta_corrente'){ verificaAgruparContaCorrente(true); } ");
}

$obSimNaoMovimentacaoConta = new SimNao();
$obSimNaoMovimentacaoConta->setRotulo ( "Listar Contas Sem Movimentação" );
$obSimNaoMovimentacaoConta->setName   ( 'boMovimentacaoConta'      );
$obSimNaoMovimentacaoConta->setNull   ( true                       );
$obSimNaoMovimentacaoConta->setChecked( 'SIM'                      );

//Agrupamento somente para o estado o de Minas Gerais MG
if ( $inCodUF == 11 ) {
    $obRadboAgruparContaCorrenteSim = new Radio();
    $obRadboAgruparContaCorrenteSim->setId    ('boAgruparContaCorrente');
    $obRadboAgruparContaCorrenteSim->setName  ('boAgruparContaCorrente');
    $obRadboAgruparContaCorrenteSim->setValue ('S');
    $obRadboAgruparContaCorrenteSim->setRotulo('Agrupar por Conta Corrente');
    $obRadboAgruparContaCorrenteSim->setLabel ('Sim');
    $obRadboAgruparContaCorrenteSim->obEvento->setOnChange("verificaAgruparContaCorrente(true);");

    $obRadboAgruparContaCorrenteNao = new Radio();
    $obRadboAgruparContaCorrenteNao->setId    ('boAgruparContaCorrente');
    $obRadboAgruparContaCorrenteNao->setName  ('boAgruparContaCorrente');
    $obRadboAgruparContaCorrenteNao->setValue ('N');
    $obRadboAgruparContaCorrenteNao->setRotulo('Agrupar por Conta Corrente');
    $obRadboAgruparContaCorrenteNao->setLabel ('Não');
    $obRadboAgruparContaCorrenteNao->setChecked(true);
    $obRadboAgruparContaCorrenteNao->obEvento->setOnChange("verificaAgruparContaCorrente(false);");

    // Agrupa os Radios num array()
    $arRadAgrupaContaCorrente = array($obRadboAgruparContaCorrenteSim, $obRadboAgruparContaCorrenteNao);    
}


include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setOpcaoAssinaturas( false );
$obMontaAssinaturas->setCampoEntidades( 'inCodEntidade' );
$obMontaAssinaturas->setFuncaoJS();
$obCmbEntidade->obEvento->setOnChange("getIMontaAssinaturas(); jQuery('#stEntidade').val(jQuery('#stNomeEntidade').find('option:selected').text());");
$obTxtEntidade->obEvento->setOnChange("getIMontaAssinaturas(); jQuery('#stEntidade').val(jQuery('#stNomeEntidade').find('option:selected').text());");

$obIIntervaloPopUpContaAnalitica = new IIntervaloPopUpContaAnalitica($obCmbEntidade);
$obIIntervaloPopUpContaAnalitica->setTipoBusca('codigoReduzidoBanco');

$obIIntervaloPopUpEstruturalPlano = new IIntervaloPopUpEstruturalPlano();

$obFormulario = new Formulario();
$obFormulario->addForm                      ( $obForm );
$obFormulario->addHidden                    ( $obHdnCaminho );
$obFormulario->addHidden                    ( $obHdnAcao );
$obFormulario->addHidden                    ( $obHdnCtrl );
$obFormulario->addHidden                    ( $obHdnNomeEntidade );
$obFormulario->addTitulo                    ( "Dados para Filtro" );
$obFormulario->addComponenteComposto        ( $obTxtEntidade, $obCmbEntidade );
$obFormulario->addComponente                ( $obPeriodo );
$obFormulario->addComponente                ( $obIIntervaloPopUpContaAnalitica );
$obFormulario->addComponente                ( $obIIntervaloPopUpEstruturalPlano );
$obIMontaRecursoDestinacao->geraFormulario  ( $obFormulario );
$obFormulario->addComponente                ( $obCmbOrdenacao );
$obFormulario->addComponente                ( $obSimNaoMovimentacaoConta );

if ( $inCodUF == 11 )
    $obFormulario->agrupaComponentes            ( $arRadAgrupaContaCorrente  );

$obMontaAssinaturas->geraFormulario ( $obFormulario );

$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
