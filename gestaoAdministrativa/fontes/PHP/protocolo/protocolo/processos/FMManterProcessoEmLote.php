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
    * Página de Listagem para Arquivar Processo em Lote.
    * Data de Criação: 23/04/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: LSManterProcessoEmLote.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php";
include_once CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcessoEmLote";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

//CONSULTA PROCESSOS
$obTProcesso = new TProcesso();
//CONSULTA TIPOS DE PROCESSOS
$obTProcesso->recuperaSituacaoArquivamentoProcesso($rsSituacaoProcesso, "", "", "");
//CONSULTA TIPOS DE ARQUIVAMENTO
$obTProcesso->recuperaHistoricoArquivamentoProcesso($rsHistorico, "", "", "");


//DEFINICAO DO FORM
$obForm = new Form();
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define a tabela dos processos lançados
$obSpanLancamentos = new Span;
$obSpanLancamentos->setId ( "spnLancamentos" );

//Hidden para o Processo de Lote
$obHdnChaveProcesso = new Hidden;
$obHdnChaveProcesso->setName ( "hdnChaveProcesso" );
$obHdnChaveProcesso->setId   ( "hdnChaveProcesso" );

//Select para buscar os processos individualmente
$obIPopUpProcesso = new IPopUpProcesso($obForm);
$obIPopUpProcesso->setValidar ( true  );
$obIPopUpProcesso->setNull    ( true );
$obIPopUpProcesso->setRotulo  ( "*Processo" );
$obIPopUpProcesso->setTipo    ( "recebido" );

//Ordenacao da listagem
$obCmbTipoArquivamento = new Select;
$obCmbTipoArquivamento->setName       ( "stTipo"                      	    );
$obCmbTipoArquivamento->setId         ( "stTipo"                            );
$obCmbTipoArquivamento->setValue      ( $stTipo                   		    );
$obCmbTipoArquivamento->setNull		  ( false								);
$obCmbTipoArquivamento->setRotulo     ( "Arquivamento"                      );
$obCmbTipoArquivamento->setTitle      ( "Selecione a forma de arquivamento" );
$obCmbTipoArquivamento->addOption     ( "", "Selecione"    			        );
$obCmbTipoArquivamento->setCampoId	  ( "cod_situacao"					    );
$obCmbTipoArquivamento->setCampoDesc  ( "nom_situacao" 					    );
$obCmbTipoArquivamento->preencheCombo ( $rsSituacaoProcesso				    );

$obCmbTipoHistorico = new Select;
$obCmbTipoHistorico->setName       ( "stHistorico"                        );
$obCmbTipoHistorico->setId         ( "stHistorico"                        );
$obCmbTipoHistorico->setValue      ( $stHistorico                   	  );
$obCmbTipoHistorico->setNull	   ( false								  );
$obCmbTipoHistorico->setRotulo     ( "Motivo do Arquivamento"             );
$obCmbTipoHistorico->setTitle      ( "Selecione o Motivo do arquivamento" );
$obCmbTipoHistorico->addOption     ( "", "Selecione"    			      );
$obCmbTipoHistorico->setCampoId	   ( "cod_historico"					  );
$obCmbTipoHistorico->setCampoDesc  ( "nom_historico" 					  );
$obCmbTipoHistorico->preencheCombo ( $rsHistorico					 	  );

$obTxtLocalizacaoFisica = new TextBox();
$obTxtLocalizacaoFisica->setId        ( 'stLocalizacaoFisica'                );
$obTxtLocalizacaoFisica->setName      ( 'stLocalizacaoFisica'                );
$obTxtLocalizacaoFisica->setRotulo    ( 'Localização Física do Arquivamento' );
$obTxtLocalizacaoFisica->setSize      ( 80                                   );
$obTxtLocalizacaoFisica->setMaxLength ( 80                                   );

$obTxtComplementar = new TextArea;
$obTxtComplementar->setName	  ( "txtComplementar"	 );
$obTxtComplementar->setId     ( "txtComplementar"    );
$obTxtComplementar->setNull	  ( true 				 );
$obTxtComplementar->setRotulo ( "Texto Complementar" );

$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btIncluirProcesso"                                       );
$obBtnIncluir->setId                ( "btIncluirProcesso"                                       );
$obBtnIncluir->setValue             ( "Incluir"                                                 );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirProcesso');"                  );
$obBtnIncluir->setTitle             ( "Clique para incluir um processo no arquivamento em lote" );

//ADICIONANDO OS COMPONENTES AO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm		 ( $obForm				   );
$obFormulario->addHidden     ( $obHdnChaveProcesso     );

$obFormulario->addComponente ( $obCmbTipoArquivamento  );
$obFormulario->addComponente ( $obCmbTipoHistorico	   );
$obFormulario->addComponente ( $obTxtLocalizacaoFisica );
$obFormulario->addComponente ( $obTxtComplementar	   );
$obFormulario->addComponente ( $obIPopUpProcesso       );
$obFormulario->addComponente ( $obBtnIncluir           );

$obFormulario->addSpan       ( $obSpanLancamentos      );

$obFormulario->Ok(true);
$obFormulario->show();
 
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>