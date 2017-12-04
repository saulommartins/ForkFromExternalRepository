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

    $Id: FLExportarBalanceteContabil.php 62872 2015-07-01 20:16:55Z franver $
    */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarBalanceteContabil" ;
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

/* ComboBox dos entidades */
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();

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
    'IDE.csv',
    'BALANCETE.csv',
    'CONSID.csv'
);

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
$obFormulario->addComponente($obMes);
$obFormulario->agrupaComponentes(array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado));
$obFormulario->addComponente($obCmbArquivos);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
