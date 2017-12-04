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
    * Página de filtro do relatório
    * Data de Criação   : 31/17/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-xx.xx.xx
*/

/*
$Log$
Revision 1.1  2007/08/14 21:07:59  hboaventura
uc_02-04-36

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioResumoReceita.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"         );
include_once( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpEstruturalReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelacaoReceitaOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;

$obRTesourariaRelatorioResumoReceita = new RTesourariaRelatorioResumoReceita;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRTesourariaRelatorioResumoReceita->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade($rsUsuariosDisponiveis, " ORDER BY cod_entidade");

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
$obForm->setAction( $pgOcul );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."relatorio/OCResumoReceita.php" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

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
$obTxtExercicio->setTitle     ( "Informe o Exercício para o Resumo da Receita"  );
$obTxtExercicio->setNull      ( false                                          );
$obTxtExercicio->setMaxLength ( 4                                              );
$obTxtExercicio->setSize      ( 5                                              );

$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setNull            ( false );
$obPeriodo->setValue           ( 4 );

//Define Objeto Select para o Tipo de Transferencia
$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo ( "Tipo de Relatório"                 );
$obCmbTipoRelatorio->setName   ( "stTipoRelatorio"                   );
$obCmbTipoRelatorio->addOption ( "","Selecione"                      );
$obCmbTipoRelatorio->addOption ( "B","por Banco"   );
$obCmbTipoRelatorio->addOption ( "E","por Entidade");
$obCmbTipoRelatorio->addOption ( "R","por Recurso" );
$obCmbTipoRelatorio->setValue  ( ""                                  );
$obCmbTipoRelatorio->setStyle  ( "width: 120px"                      );
$obCmbTipoRelatorio->setNull   ( true                                );
$obCmbTipoRelatorio->setTitle  ( "Selecione o Tipo de Relatório"     );

//Define o componente IIntervaloPopUpEstruturalReceita
$obIIntervaloPopUpEstruturalReceita = new IIntervaloPopUpEstruturalReceita();

//Define Objeto Label Até
$obTxtReceitaAte = new Label;
$obTxtReceitaAte->setName      ( "stAte" );
$obTxtReceitaAte->setValue     ( "até"   );
$obTxtReceitaAte->setRotulo    ( ""      );

//Define Objeto Text para a Conta Banco Inicial
$obTxtContaBancoInicial = new TextBox;
$obTxtContaBancoInicial->setName      ( "inContaBancoInicial"             );
$obTxtContaBancoInicial->setValue     ( $inContaBancoInicial              );
$obTxtContaBancoInicial->setRotulo    ( "Conta Banco"                     );
$obTxtContaBancoInicial->setTitle     ( "Informe a Conta Banco Inicial e Final"   );
$obTxtContaBancoInicial->setNull      ( true                              );
$obTxtContaBancoInicial->setMaxLength ( 10                                );
$obTxtContaBancoInicial->setSize      ( 11                                );
$obTxtContaBancoInicial->setInteiro   ( true                              );

//Define Objeto Label Até
$obTxtContaBancoAte = new Label;
$obTxtContaBancoAte->setName      ( "stAte" );
$obTxtContaBancoAte->setValue     ( "até"   );
$obTxtContaBancoAte->setRotulo    ( ""      );

//Define Objeto Text para a Conta Banco Final
$obTxtContaBancoFinal = new TextBox;
$obTxtContaBancoFinal->setName      ( "inContaBancoFinal"                 );
$obTxtContaBancoFinal->setValue     ( $inContaBancoFinal                 );
$obTxtContaBancoFinal->setRotulo    ( ""                                  );
$obTxtContaBancoFinal->setTitle     ( "Informe a Conta Banco Final"       );
$obTxtContaBancoFinal->setNull      ( true                                );
$obTxtContaBancoFinal->setMaxLength ( 10                                  );
$obTxtContaBancoFinal->setSize      ( 11                                  );
$obTxtContaBancoFinal->setInteiro   ( true                                );

// Define objeto BuscaInner para o Recurso
$obBscRecurso = new BuscaInner();
$obBscRecurso->setRotulo                 ( "Recurso"                                           );
$obBscRecurso->setTitle                  ( "Informe o Recurso"                                 );
$obBscRecurso->setId                     ( "stDescricaoRecurso"                                );
$obBscRecurso->setValue                  ( $stDescricaoRecurso                                 );
$obBscRecurso->setNull                   ( true                                                );
$obBscRecurso->obCampoCod->setName       ( "inCodRecurso"                                      );
$obBscRecurso->obCampoCod->setSize       ( 10                                                  );
$obBscRecurso->obCampoCod->setMaxLength  ( strlen($obRConfiguracaoOrcamento->getMascRecurso()) );
$obBscRecurso->obCampoCod->setValue      ( $inCodRecurso                                       );
$obBscRecurso->obCampoCod->setAlign      ( "left"                                              );
$obBscRecurso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$obRConfiguracaoOrcamento->getMascRecurso()."', this, event);");
$obBscRecurso->setFuncaoBusca   ("abrePopUp('".CAM_GF_ORC_POPUPS."recurso/FLRecurso.php','frm','inCodRecurso','stDescricaoRecurso','','".Sessao::getId()."','800','550');");
$obBscRecurso->setValoresBusca  ( CAM_GF_ORC_POPUPS.'recurso/OCRecurso.php?'.Sessao::getId(), $obForm->getName() );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden         ( $obHdnCaminho         );
$obFormulario->addHidden         ( $obHdnCtrl            );
$obFormulario->addHidden         ( $obHdnAcao            );

$obFormulario->addTitulo         ( "Dados para Filtro"   );
$obFormulario->addComponente     ( $obCmbEntidades       );
$obFormulario->addComponente     ( $obTxtExercicio       );
$obFormulario->addComponente     ( $obPeriodo            );
$obFormulario->addComponente     ( $obCmbTipoRelatorio   );
$obFormulario->addComponente 	 ( $obIIntervaloPopUpEstruturalReceita );
$obFormulario->agrupaComponentes ( array ($obTxtContaBancoInicial, $obTxtContaBancoAte, $obTxtContaBancoFinal) );
$obFormulario->addComponente     ( $obBscRecurso         );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
