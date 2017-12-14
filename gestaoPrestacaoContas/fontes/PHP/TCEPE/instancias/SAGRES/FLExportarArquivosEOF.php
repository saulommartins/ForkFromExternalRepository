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
  * Página de Formulario de Filtro para ação EOF
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: FLExportarArquivosEOF.php 60426 2014-10-21 11:54:26Z gelson $
  * $Date: 2014-10-21 09:54:26 -0200 (Tue, 21 Oct 2014) $
  * $Author: gelson $
  * $Rev: 60426 $
  *
*/
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportarArquivosEOF";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once($pgJs);

// Busca as entidades, para montar o comboBox
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$stOrdem = "ORDER BY cod_entidade";
$obROrcamentoEntidade->listarEntidades( $rsEntidades, $stOrdem );


//Instancia o formulário
$obForm = new Form;
$obForm->setAction( CAM_GPC_TCEPE_INSTANCIAS."SAGRES/PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnPaginaExportacao = new Hidden;
$obHdnPaginaExportacao->setName ( "hdnPaginaExportacao" );
$obHdnPaginaExportacao->setValue( CAM_GPC_TCEPE_INSTANCIAS."SAGRES/".$pgProc );

// Define Objeto TextBox para Codigo da Entidade
$obTxtCodEntidade = new TextBox;
$obTxtCodEntidade->setName('inCodEntidade');
$obTxtCodEntidade->setId  ('inCodEntidade');
$obTxtCodEntidade->setRotulo ('Entidade');
$obTxtCodEntidade->setTitle  ('Selecione a entidade.');
$obTxtCodEntidade->setInteiro(true);
$obTxtCodEntidade->setNull   (false);

// Define Objeto Select para Nome da Entidade
$obCmbNomEntidade = new Select;
$obCmbNomEntidade->setName      ('stNomEntidade');
$obCmbNomEntidade->setId        ('stNomEntidade');
$obCmbNomEntidade->setValue     ($inCodEntidade);
$obCmbNomEntidade->addOption    ('', 'Selecione');
$obCmbNomEntidade->setCampoId   ('cod_entidade');
$obCmbNomEntidade->setCampoDesc ('nom_cgm');
$obCmbNomEntidade->setStyle     ('width: 520');
$obCmbNomEntidade->preencheCombo($rsEntidades);
$obCmbNomEntidade->setNull      (false);

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

$obCmbCompetencia = new Select();
$obCmbCompetencia->setId    ("inCodCompetencia");
$obCmbCompetencia->setName  ("inCodCompetencia");
$obCmbCompetencia->setRotulo("Competência");
$obCmbCompetencia->setTitle ("Selecione a competência.");
$obCmbCompetencia->addOption( "","Selecione");
$obCmbCompetencia->addOption( 1,"Janeiro");
$obCmbCompetencia->addOption( 2,"Fevereiro");
$obCmbCompetencia->addOption( 3,"Março");
$obCmbCompetencia->addOption( 4,"Abril");
$obCmbCompetencia->addOption( 5,"Maio");
$obCmbCompetencia->addOption( 6,"Junho");
$obCmbCompetencia->addOption( 7,"Julho");
$obCmbCompetencia->addOption( 8,"Agosto");
$obCmbCompetencia->addOption( 9,"Setembro");
$obCmbCompetencia->addOption(10,"Outubro");
$obCmbCompetencia->addOption(11,"Novembro");
$obCmbCompetencia->addOption(12,"Dezembro");
$obCmbCompetencia->addOption(13,"Final Anual");
$obCmbCompetencia->setNull  (false);
$obCmbCompetencia->obEvento->setOnChange("montaParametrosGET('montaMultipleSelect');");

$obSpnArquivosSelectMultiple = new Span();
$obSpnArquivosSelectMultiple->setId("obCmbArquivos");

$obBtnOk = new Ok();
$obBtnOk->setName             ( "btOk" );
$obBtnOk->setValue            ( "Ok" );
$obBtnOk->obEvento->setOnClick( "if(validaArquivos()) {selecionaArquivos(true);Salvar();}" );

$obBtnLimpar = new Limpar();
$obBtnLimpar->setName             ( "btLimpar" );
$obBtnLimpar->setValue            ( "Limpar" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm    );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnPaginaExportacao );
$obFormulario->addTitulo ( "Dados para geração dos arquivos de Execução Orçamentária e Financeira do Município." );
$obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
$obFormulario->agrupaComponentes ( array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado) );
$obFormulario->addComponente($obCmbCompetencia);
$obFormulario->addSpan ($obSpnArquivosSelectMultiple);
$obFormulario->defineBarraAba(array($obBtnOk, $obBtnLimpar));
$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>