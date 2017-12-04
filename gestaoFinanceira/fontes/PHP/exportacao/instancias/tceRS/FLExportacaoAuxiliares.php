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
    * Página de Filtro - Exportação Arquivos Auxiliares

    * Data de Criação   : 31/01/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego L. de Souza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.02
*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoAuxiliares";
//Filtro
$pgFilt = "FL".$stPrograma.".php";
//Lista
$pgList = "LS".$stPrograma.".php";
//Formulario
$pgForm = "FM".$stPrograma.".php";
//Processamento
$pgProc = "PR".$stPrograma.".php";
//Frame oculto
$pgOcul = "OC".$stPrograma.".php";
//Javascript
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a fun??o do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

$sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );
$sessao->link = array();

global $session;
$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

//$rsRecordset = new RecordSet();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( "../processamento/PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto de pagina exportação
$obHdnPaginaExportacao = new Hidden;
$obHdnPaginaExportacao->setName ("hdnPaginaExportacao");
$obHdnPaginaExportacao->setValue("../tceRS/".$pgOcul);

$obCmbPeriodoExport = new Select();
$obCmbPeriodoExport->setRotulo			( "*Periodo" 			)	;
$obCmbPeriodoExport->setName          	( "inPeriodo" 	        )	;
$obCmbPeriodoExport->setId          	( "inPeriodo" 	        )	;
// Monta array de bimestres e cria laço que adiciona ao select
$arBimExport = array(1=>"1o Bimestre",2=>"2o Bimestre",3=>"3o Bimestre",4=>"4o Bimestre",5=>"5o Bimestre",6=>"6o Bimestre");
for ($inContandorOpt=1;$inContandorOpt <= 6;$inContandorOpt++) {
        $obCmbPeriodoExport->addOption($inContandorOpt,$arBimExport[$inContandorOpt]);
    }

$obREntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

// select com setores do governo
$obTxtSetorGoverno = new Select();
$obTxtSetorGoverno->setRotulo       ( "Setor do Governo"            );
$obTxtSetorGoverno->setName         ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setId           ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setCampoID      ( "[cnpj]|[nom_cgm]"            );
$obTxtSetorGoverno->setCampoDesc    ( "nom_cgm"                     );
$obTxtSetorGoverno->preencheCombo   ( $rsEntidadesDisponiveisCnpj   );

// Define o objeto RADIO para selecionar o tipo de exportação
$obRdbTipoExportacao = new Radio();
$obRdbTipoExportacao->setRotulo("Tipo de Exportação");
$obRdbTipoExportacao->setName("boTipoExportacao");
$obRdbTipoExportacao->setValue(1);
$obRdbTipoExportacao->setLabel("Arquivos Individuais");
$obRdbTipoExportacao->setChecked(($boTipoExportacao == 1 OR !$boTipoExportacao));
$obRdbTipoExportacao->setNull( false );

$obRdbTipoExportacao2 = new Radio();
$obRdbTipoExportacao2->setRotulo("Tipo de Exportação");
$obRdbTipoExportacao2->setName("boTipoExportacao");
$obRdbTipoExportacao2->setValue(2);
$obRdbTipoExportacao2->setLabel("Arquivo Compactado");
$obRdbTipoExportacao2->setChecked(($boTipoExportacao == 2));
$obRdbTipoExportacao2->setNull( false );

//Define objeto SELECT para selecionar os arquivos
$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setname("inCodArquivoSelecionados");
$obCmbArquivos->setRotulo("Arquivos");
$obCmbArquivos->setNull( false );

//lista de arquivos
$arArquivos[0]['nome'] = "ORGAO.TXT";
$arArquivos[1]['nome'] = "UNIORCAM.TXT";
$arArquivos[2]['nome'] = "FUNCAO.TXT";
$arArquivos[3]['nome'] = "SUBFUNC.TXT";
$arArquivos[4]['nome'] = "PROGRAMA.TXT";
$arArquivos[5]['nome'] = "SUBPROG.TXT";
$arArquivos[6]['nome'] = "PROJATIV.TXT";
$arArquivos[7]['nome'] = "RUBRICA.TXT";
$arArquivos[8]['nome'] = "RECURSO.TXT";
$arArquivos[9]['nome'] = "CREDOR.TXT";
$rsArquivosDisponiveis = new RecordSet();
$rsArquivosDisponiveis->preenche($arArquivos);
$rsArquivosSelecionados = new RecordSet();

//lista de arquivos dispon?veis
$obCmbArquivos->setNomeLista1("arArquivosDisponiveis");
$obCmbArquivos->setCampoId1("nome");
$obCmbArquivos->setCampoDesc1("nome");
$obCmbArquivos->setRecord1($rsArquivosDisponiveis);

//lista de arquivos selecionados
$obCmbArquivos->setNomeLista2("arArquivosSelecionados");
$obCmbArquivos->setCampoId2("nome");
$obCmbArquivos->setCampoDesc2("nome");
$obCmbArquivos->setRecord2($rsArquivosSelecionados);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnPaginaExportacao  );

$obFormulario->addTitulo( "Dados para arquivos"   );
$obFormulario->addComponente($obCmbPeriodoExport  );
$obFormulario->addComponente($obTxtSetorGoverno   );
$obFormulario->addComponente($obRdbTipoExportacao );
//$obFormulario->addComponenteComposto( $obRdbTipoExportacao,$obRdbTipoExportacao2  );
$obFormulario->addComponente( $obCmbArquivos );
$obFormulario->setFormFocus($obCmbPeriodoExport->getId());
$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
