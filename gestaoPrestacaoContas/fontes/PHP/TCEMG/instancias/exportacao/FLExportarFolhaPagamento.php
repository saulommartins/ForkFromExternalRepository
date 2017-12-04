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
   /*
    * Formulario de Filtro para a geracao dos arquivos do TCM/MG
    * Data de Criação   : 15/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: FLExportarFolhaPagamento.php 65902 2016-06-28 17:07:30Z evandro $
    */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExportarFolhaPagamento" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( "PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnPaginaExportacao = new Hidden;
$obHdnPaginaExportacao->setName ('hdnPaginaExportacao');
$obHdnPaginaExportacao->setValue("../exportacao/".$pgOcul);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//SELECT MULTIPLO ENTIDADES
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();

if(SistemaLegado::isTCEMG($boTransacao) && Sessao::getExercicio() <= '2016'){
    /* ComboBox do Exercício -
     * Devido a uma necessidade do exportador para o modulo de Folha.
     * Será necessário criar esse campo para gerar arquivo de exportação do ano de 2013 até 2016, devido uma determinação do TCE-MG
     **/
    $obExercicio = new Select();
    $obExercicio->setRotulo("Exercício para exportação");
    $obExercicio->setTitle("Informe o Exercício para a exportação dos arquivo do modulo Folha de Pagamento");
    $obExercicio->setId("stExercicioExportador");
    $obExercicio->setName("stExercicioExportador");
    $obExercicio->addOption("2013","2013");
    $obExercicio->addOption("2014","2014");
    $obExercicio->addOption("2015","2015");
    $obExercicio->addOption("2016","2016");
    $obExercicio->setValue(Sessao::getExercicio());
    $obExercicio->setNull(false);
}
/* ComboBox dos meses */
$obMes = new Mes();
$obMes->setNull(false);

/* Radio para selecionar tipo de exportacao*/
/* Tipo Arquivo Individual */
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"         );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais" );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"          );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"  );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"   );
$obRdbTipoExportArqIndividual->setChecked(true                    );
/* Tipo Arquivo Compactado */
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport" );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"  );
$obRdbTipoExportArqCompactado->setValue ( "compactados"  );

$arNomeArquivos = array(
    'IDE.csv'
    ,'PESSOA.csv'
    ,'TEREM.csv'
    ,'RESPINF.csv'
    ,'FLPGO.csv'
    ,'CONSID.csv'
);

sort($arNomeArquivos);

// Preenche array
for ($inCounter=0;$inCounter < count($arNomeArquivos);$inCounter++) {
    $arElementosArq[$inCounter]['Arquivo']   = $arNomeArquivos[$inCounter];
    $arElementosArq[$inCounter]['Nome'   ]   = $arNomeArquivos[$inCounter];
}

$rsArqSelecionados = new RecordSet;
$rsArqDisponiveis  = new RecordSet;
$rsArqDisponiveis->preenche($arElementosArq);

$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName  ( 'arArquivosSelecionados' );
$obCmbArquivos->setRotulo( "Arquivos" );
$obCmbArquivos->setNull  ( false );
$obCmbArquivos->setTitle ( 'Arquivos Disponiveis' );

// lista de ARQUIVOS disponiveis
$obCmbArquivos->SetNomeLista1( 'arCodArqDisponiveis' );
$obCmbArquivos->setCampoId1  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc1( 'Nome' );
$obCmbArquivos->SetRecord1   ( $rsArqDisponiveis   );

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivosSelecionados' );
$obCmbArquivos->setCampoId2  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc2( 'Nome' );
$obCmbArquivos->SetRecord2   ( $rsArqSelecionados );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Dados para geração de arquivos");
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnPaginaExportacao);
$obFormulario->addComponente($obISelectMultiploEntidadeUsuario);
if(SistemaLegado::isTCEMG($boTransacao) && Sessao::getExercicio() <= '2016'){
    $obFormulario->addComponente($obExercicio);
}
$obFormulario->addComponente($obMes);
$obFormulario->agrupaComponentes(array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado));
$obFormulario->addComponente($obCmbArquivos);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
