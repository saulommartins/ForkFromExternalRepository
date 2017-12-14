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

    * Casos de uso: uc-02.04.37
*/

/*
$Log$
Revision 1.5  2007/10/04 21:40:08  hwalves
Ticket#10069#

Revision 1.4  2007/09/25 21:12:54  hwalves
Ticket#10069#

Revision 1.3  2007/09/03 18:45:17  hboaventura
Ticket#9937#

Revision 1.2  2007/08/23 12:55:43  hboaventura
Bug#9937#, Bug#9938#, Bug#9940#

Revision 1.1  2007/08/08 14:07:33  hboaventura
uc_02-04-37

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioResumoReceita.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"         );
//include_once( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpEstruturalReceita.class.php" );
include_once( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpContaAnalitica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelacaoDespesaExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";

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
$obForm->setAction( $pgGera );
$obForm->setTarget( "telaPrincipal" );

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
$obCmbEntidades->setTitle  ( "Selecione a Entidade." );
$obCmbEntidades->setNull   ( true );

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
$obCmbTipoRelatorio->setTitle  ( "Selecione o Tipo de Relatório."     );

//Define Objeto Text para a Despesa Inicial
$obTxtDespesaInicial = new TextBox;
$obTxtDespesaInicial->setName      ( "inCodPlanoInicial"           );
$obTxtDespesaInicial->setRotulo    ( "Despesa"                     );
$obTxtDespesaInicial->setTitle     ( "Informe a Despesa Inicial e Final"   );
$obTxtDespesaInicial->setNull      ( true                          );
$obTxtDespesaInicial->setMaxLength ( 10                            );
$obTxtDespesaInicial->setSize      ( 11                            );
$obTxtDespesaInicial->setInteiro   ( true                          );

//Define Objeto Label Até
$obTxtDespesaAte = new Label;
$obTxtDespesaAte->setName      ( "stAte" );
$obTxtDespesaAte->setValue     ( "até"   );
$obTxtDespesaAte->setRotulo    ( ""      );

//Define Objeto Text para a Despesa Final
$obTxtDespesaFinal = new TextBox;
$obTxtDespesaFinal->setName      ( "inCodPlanoFinal"               );
$obTxtDespesaFinal->setRotulo    ( ""                              );
$obTxtDespesaFinal->setTitle     ( "Informe a Despesa Final"       );
$obTxtDespesaFinal->setNull      ( true                            );
$obTxtDespesaFinal->setMaxLength ( 10                              );
$obTxtDespesaFinal->setSize      ( 11                              );
$obTxtDespesaFinal->setInteiro   ( true                            );

//Define Objeto Text para a Conta Banco Inicial
$obTxtContaBancoInicial = new TextBox;
$obTxtContaBancoInicial->setName      ( "inContaBancoInicial"             );
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
$obTxtContaBancoFinal->setRotulo    ( ""                                  );
$obTxtContaBancoFinal->setTitle     ( "Informe a Conta Banco Final"       );
$obTxtContaBancoFinal->setNull      ( true                                );
$obTxtContaBancoFinal->setMaxLength ( 10                                  );
$obTxtContaBancoFinal->setSize      ( 11                                  );
$obTxtContaBancoFinal->setInteiro   ( true                                );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden         ( $obHdnCtrl            );
$obFormulario->addHidden         ( $obHdnAcao            );

$obFormulario->addTitulo         ( "Dados para Filtro"   );
$obFormulario->addComponente     ( $obCmbEntidades       );
$obFormulario->addComponente     ( $obPeriodo            );
$obFormulario->addComponente     ( $obCmbTipoRelatorio   );
$obFormulario->agrupaComponentes ( array ($obTxtDespesaInicial, $obTxtDespesaAte, $obTxtDespesaFinal) );
$obFormulario->agrupaComponentes ( array ($obTxtContaBancoInicial, $obTxtContaBancoAte, $obTxtContaBancoFinal) );
$obIMontaRecursoDestinacao->geraFormulario( $obFormulario );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
