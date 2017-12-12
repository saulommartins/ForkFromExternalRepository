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
    * Página de Filtro - Exportação Arquivos Principais

    * Data de Criação   : 31/01/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-17 11:32:12 -0300 (Seg, 17 Jul 2006) $

    * Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.10  2006/07/17 14:30:48  cako
Bug #6013#

Revision 1.9  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"              );

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoPrincipais"	;
$pgFilt 	= "FL".$stPrograma.".php"	;
$pgList 	= "LS".$stPrograma.".php"	;
$pgForm 	= "FM".$stPrograma.".php"	;
$pgProc 	= "PR".$stPrograma.".php"	;
$pgOcul 	= "OC".$stPrograma.".php"	;
$pgJS   	= "JS".$stPrograma.".js"	;

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
unset( $sessao->link );

$rsArqExport 	= $rsAtributos = new RecordSet;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$sessao = $_SESSION ['sessao'];
//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( "../tceRS/".$pgOcul );

$obTxtPeriodoExport = new Select();
$obTxtPeriodoExport->setRotulo              ( "*Periodo"        );
$obTxtPeriodoExport->setName                ( "inPeriodo"       );
$obTxtPeriodoExport->setId                  ( "inPeriodo"       );
$obTxtPeriodoExport->obEvento->setOnChange  ( 'rd_extra();'     );

// Monta array de bimestres e cria laço que adiciona ao select
$arBimExport = array(1=>"1o Bimestre",2=>"2o Bimestre",3=>"3o Bimestre",4=>"4o Bimestre",5=>"5o Bimestre",6=>"6o Bimestre");
for ($inContandorOpt=1;$inContandorOpt <= 6;$inContandorOpt++) {
        $obTxtPeriodoExport->addOption($inContandorOpt,$arBimExport[$inContandorOpt]);
    }
/*
/* TextBox unidade de Orgao/Unidade
$obTxtOrgUnidade = new TextBox;
$obTxtOrgUnidade->setRotulo          ( "*Orgão / Unidade");
$obTxtOrgUnidade->setName            ( "inOrgaoUnidade"  );
$obTxtOrgUnidade->setId              ( "inOrgaoUnidade"  );
$obTxtOrgUnidade->setSize            ( 4                 );
$obTxtOrgUnidade->setMaxLength       ( 4                 );
$obTxtOrgUnidade->setMinLength       ( 4                 );
$obTxtOrgUnidade->setAlfaNumerico    ( true              );
*/
/* Recordset de entidades */

$obEntidade = new ROrcamentoEntidade;
$obEntidade->obRCGM->setNumCGM ( Sessao::read('numCgm') );
$rsEntidadesDisponiveis  = new RecordSet;
$rsEntidadesSelecionadas = new RecordSet;
$obEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis , " ORDER BY cod_entidade" );
$obEntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

// select com setores do governo
$obTxtSetorGoverno = new Select();
$obTxtSetorGoverno->setRotulo       ( "Setor do Governo"            );
$obTxtSetorGoverno->setName         ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setId           ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setCampoID      ( "[cnpj]|[nom_cgm]"            );
$obTxtSetorGoverno->setCampoDesc    ( "nom_cgm"                     );
$obTxtSetorGoverno->preencheCombo   ( $rsEntidadesDisponiveisCnpj   );

// Lista ENTIDADES para Selecionar
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ( 'arEntidadesSelecionadas' );
$obCmbEntidades->setRotulo( "Entidade" );
$obCmbEntidades->setNull  ( false );
$obCmbEntidades->setTitle ( 'Entidades Disponiveis' );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidadesDisponiveis->getNumLinhas()==1) {
       $rsEntidadesSelecionadas = $rsEntidadesDisponiveis;
       $rsEntidadesDisponiveis = new RecordSet;
}

// Lista de ENTIDADES disponiveis
$obCmbEntidades->SetNomeLista1( 'arEntidadesDisponiveis' );
$obCmbEntidades->setCampoId1  ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1( '[cod_entidade] - [nom_cgm]' );
$obCmbEntidades->SetRecord1   ( $rsEntidadesDisponiveis   );

// lista de ENTIDADES selecionadas
$obCmbEntidades->SetNomeLista2( 'arEntidadesSelecionadas' );
$obCmbEntidades->setCampoId2  ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc2( '[cod_entidade] - [nom_cgm]' );
$obCmbEntidades->SetRecord2   ( $rsEntidadesSelecionadas );

/* Radio para selecionar tipo de exportacao*/
/* Tipo Arquivo Individual */
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"                        );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais"                );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"                         );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"   );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"    );
$obRdbTipoExportArqIndividual->setChecked(true                                   );
/* Tipo Arquivo Compactado */
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport"    );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"     );
$obRdbTipoExportArqCompactado->setValue ( "compactados"     );

/* Lista Arquivos para Selecionar */
/* Elementos no array*/
// Array com o nomes
$arNomeArquivos = array('EMPENHO.TXT','LIQUIDAC.TXT','PAGAMENT.TXT','BAL_REC.TXT','RECEITA.TXT','BAL_DESP.TXT','DECRETO.TXT','BAL_VER.TXT');
// Preenche array
for ($inCounter=0;$inCounter < count($arNomeArquivos);$inCounter++) {
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

SistemaLegado::executaFramePrincipal("d.frm.stCnpjSetor.selected[0]=true");

//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( "../processamento/PRExportador.php"   );
$obForm->setTarget      ( "telaPrincipal"                       ); //oculto - telaPrincipal

//$obForm->setName    ( 'obFrmArquivosPrincipais');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addTitulo            ( "Dados para arquivos" );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addComponente        ( $obTxtPeriodoExport   );
$obFormulario->addComponente        ( $obTxtSetorGoverno    );
$obFormulario->addComponente        ( $obCmbEntidades       );
//$obFormulario->addComponente        ( $obTxtOrgUnidade      );
$obFormulario->addComponente        ( $obRdbTipoExportArqIndividual  );
//$obFormulario->agrupaComponentes    (array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado));

$obFormulario->addComponente        ($obCmbArquivos);
$obFormulario->setFormFocus         ($obTxtPeriodoExport->getId());

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
