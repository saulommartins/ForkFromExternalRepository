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
    * Página de Filtro - Exportação Arquivos GF
    * Data de Criação   : 18/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php";
include_once(CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterExportacao"	;
$pgFilt 	= "FL".$stPrograma.".php"	;
$pgList 	= "LS".$stPrograma.".php"	;
$pgForm 	= "FM".$stPrograma.".php"	;
$pgProc 	= "PR".$stPrograma.".php"	;
$pgOcul 	= "OC".$stPrograma.".php"	;
$pgJS   	= "JS".$stPrograma.".js"	;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('link');

$rsArqExport = $rsAtributos = new RecordSet;
$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( "../../../TCMPA/instancias/exportacao/".$pgOcul );

$obISelectEntidade = new ISelectMultiploEntidadeGeral();

// Monta select multiplo dos arquivos
$arNomeArquivos = array(
                              'FolhasPagamento.txt'
/*
                              'IdentificacaoInformacoes.txt'
                            , 'UnidadeGestora.txt'
                            , 'UnidadesOrcamentarias.txt'
                            , 'Lotacionograma.txt'
                            , 'FuncionariosAgentesPoliticos.txt'
                            , 'InformacoesPagamento.txt'
                            , 'Diarias.txt'
*/
                       );

for ($inCounter=0; $inCounter < count($arNomeArquivos); $inCounter++) {
    $arElementosArq[$inCounter]['Arquivo'  ]   = $arNomeArquivos[$inCounter]  ;
    $arElementosArq[$inCounter]['Nome'     ]   = $arNomeArquivos[$inCounter]  ;
}

$rsArqSelecionados = new RecordSet;
$rsArqDisponiveis = new RecordSet;
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

// Text do código do TCM - órgão responsavel pelas informações
$obTxtOrgaoResponsavel = new TextBox();
$obTxtOrgaoResponsavel->setRotulo     ( 'Órgão Responsável' );
$obTxtOrgaoResponsavel->setTitle      ( 'Informe o código do TCM do órgão responsável.' );
$obTxtOrgaoResponsavel->setName       ( 'inCodOrgaoResponsavel' );
$obTxtOrgaoResponsavel->setId         ( 'inCodOrgaoResponsavel' );
$obTxtOrgaoResponsavel->setInteiro    ( true );
$obTxtOrgaoResponsavel->setMaxLength  ( 7 );
$obTxtOrgaoResponsavel->setSize       ( 7 );
$obTxtOrgaoResponsavel->setNull       ( false );

// Radio para selecionar o quadrimestre
$obRdbPrimeiroQuadrimestreCompetencia = new Radio;
$obRdbPrimeiroQuadrimestreCompetencia->setName   ( "inQuadrimestre" );
$obRdbPrimeiroQuadrimestreCompetencia->setLabel  ( "Primeiro Quadrimestre" );
$obRdbPrimeiroQuadrimestreCompetencia->setValue  ( "01" );
$obRdbPrimeiroQuadrimestreCompetencia->setRotulo ( "Quadrimestre da Competência" );
$obRdbPrimeiroQuadrimestreCompetencia->setTitle  ( "Informe o quadrimestre da competência." );
$obRdbPrimeiroQuadrimestreCompetencia->setNull   ( false );
if (date('m') < 5)
    $obRdbPrimeiroQuadrimestreCompetencia->setChecked( true );

$obRdbSegundoQuadrimestreCompetencia = new Radio;
$obRdbSegundoQuadrimestreCompetencia->setName  ( "inQuadrimestre" );
$obRdbSegundoQuadrimestreCompetencia->setLabel ( "Segundo Quadrimestre" );
$obRdbSegundoQuadrimestreCompetencia->setValue ( "02" );
if (( date('m') > 4 ) && ( date('m') < 9 ))
    $obRdbSegundoQuadrimestreCompetencia->setChecked( true );

$obRdbTerceiroQuadrimestreCompetencia = new Radio;
$obRdbTerceiroQuadrimestreCompetencia->setName  ( "inQuadrimestre" );
$obRdbTerceiroQuadrimestreCompetencia->setLabel ( "Terceiro Quadrimestre" );
$obRdbTerceiroQuadrimestreCompetencia->setValue ( "03" );
if (date('m') > 8)
    $obRdbTerceiroQuadrimestreCompetencia->setChecked( true );

// Radio para selecionar a retificadora
$obRdbRetificadoraSim = new Radio;
$obRdbRetificadoraSim->setName   ( "inRetificadora" );
$obRdbRetificadoraSim->setLabel  ( "Sim" );
$obRdbRetificadoraSim->setValue  ( "1" );
$obRdbRetificadoraSim->setRotulo ( "Retificadora" );
$obRdbRetificadoraSim->setTitle  ( "Informe a retificadora." );
$obRdbRetificadoraSim->setChecked( true );
$obRdbRetificadoraSim->setNull   ( false );

$obRdbRetificadoraNao = new Radio;
$obRdbRetificadoraNao->setName  ( "inRetificadora" );
$obRdbRetificadoraNao->setLabel ( "Não" );
$obRdbRetificadoraNao->setValue ( "2" );

// Text para o uso do órgão
$obTxtUsoOrgao = new TextBox();
$obTxtUsoOrgao->setRotulo     ( 'Uso do Órgao' );
$obTxtUsoOrgao->setTitle      ( 'Espaço reservado para o uso do órgão responsavel pelas informações.' );
$obTxtUsoOrgao->setName       ( 'stUsoOrgao' );
$obTxtUsoOrgao->setId         ( 'stUsoOrgao' );
$obTxtUsoOrgao->setMaxLength  ( 50 );
$obTxtUsoOrgao->setSize       ( 60 );

// Radio para selecionar tipo de exportacao
// Tipo Arquivo Individual
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"          );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais"  );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"           );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"   );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"    );
$obRdbTipoExportArqIndividual->setChecked(true                     );

// Tipo Arquivo Compactado
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport"    );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"     );
$obRdbTipoExportArqCompactado->setValue ( "compactados"     );

/*
$stFiltro  = " AND entidade.exercicio = ".Sessao::getExercicio();
$stFiltro .= " AND usuario_entidade.numcgm = ".Sessao::read('numCgm');
$obTEntidade->recuperaEntidadesUsuarios($rsEntidades,$stFiltro);

$obCmbEntidade = new Select;
$obCmbEntidade->setRotulo                        ( "Entidade"                                                            );
$obCmbEntidade->setTitle                         ( "Selecione a entidade para trabalho."                                 );
$obCmbEntidade->setName                          ( "inCodEntidade"                                                       );
$obCmbEntidade->setValue                         ( $inCodEntidade                                                  );
$obCmbEntidade->setStyle                         ( "width: 400px"                                                        );
$obCmbEntidade->addOption                        ( "", "Selecione"                                                       );
$obCmbEntidade->setNull(false);
$obCmbEntidade->setCampoId("cod_entidade");
$obCmbEntidade->setCampoDesc("nom_cgm");
*/

//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( "../../../exportacao/instancias/processamento/PRExportador.php"   );
$obForm->setTarget      ( "telaPrincipal"                       ); //oculto - telaPrincipal

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm );
$obFormulario->addTitulo        ( "Dados para geração de arquivos" );
$obFormulario->addHidden        ( $obHdnAcao            );
$obFormulario->addComponente    ( $obTxtOrgaoResponsavel );
$obFormulario->agrupaComponentes( array($obRdbPrimeiroQuadrimestreCompetencia ,$obRdbSegundoQuadrimestreCompetencia ,$obRdbTerceiroQuadrimestreCompetencia) );
$obFormulario->agrupaComponentes( array($obRdbRetificadoraSim, $obRdbRetificadoraNao) );
$obFormulario->agrupaComponentes( array($obRdbTipoExportArqIndividual, $obRdbTipoExportArqCompactado) );
$obFormulario->addComponente    ( $obTxtUsoOrgao );
$obFormulario->addComponente    ($obISelectEntidade);
$obFormulario->addComponente    ($obCmbArquivos);

$obFormulario->OK();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
