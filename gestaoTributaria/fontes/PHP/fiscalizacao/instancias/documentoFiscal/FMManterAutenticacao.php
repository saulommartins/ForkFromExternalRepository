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
    * Página de Formulario de Inclusao de Autenticação de Livro Fiscal

    * Data de Criação   : 03/08/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: FMManterAutenticacao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php"                                        );
include_once( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php"                              );

$stAcao = $request->get('stAcao');
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) { $stAcao = "incluir"; }

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutenticacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

//Inscricao Economica
$obIPopUpEmpresa = new IPopUpEmpresa;
$obIPopUpEmpresa->obInnerEmpresa->setNull ( false                                      );
$obIPopUpEmpresa->obInnerEmpresa->setTitle( "Informe o código da inscrição econômica." );

$obIntNumeroLivro = new TextBox;
$obIntNumeroLivro->setName   ( "inNumeroLivro"                     );
$obIntNumeroLivro->setSize   ( "10"                                );
$obIntNumeroLivro->setRotulo ( "Número do Livro"                   );
$obIntNumeroLivro->setTitle  ( "Informe o número do livro fiscal." );
$obIntNumeroLivro->setInteiro( true                                );
$obIntNumeroLivro->setNull   ( false                               );

//DEFINICAO DO PERIODO
$obPeriodo = new Periodo;
$obPeriodo->setRotulo( "Período"                            );
$obPeriodo->setTitle ( "Informe o período do livro fiscal." );
$obPeriodo->setNull  ( false                                );

$obIntQuantidadePaginas = new TextBox;
$obIntQuantidadePaginas->setName   ( "inQuantidadePaginas"                              );
$obIntQuantidadePaginas->setSize   ( "10"                                               );
$obIntQuantidadePaginas->setRotulo ( "Quantidade de Páginas"                            );
$obIntQuantidadePaginas->setTitle  ( "Informe a quantidade de páginas do livro fiscal." );
$obIntQuantidadePaginas->setInteiro( true                                               );
$obIntQuantidadePaginas->setNull   ( false                                              );

$obTxtObservacoes = new TextArea;
$obTxtObservacoes->setName  ( "stObservacoes"                  );
$obTxtObservacoes->setId    ( "stObservacoes"                  );
$obTxtObservacoes->setTitle ( "Observações"                    );
$obTxtObservacoes->setRotulo( "Observações" );

//DOCUMENTOS
$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo( "Documento" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL  ( false       );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc               );
$obForm->settarget ( "oculto"              );
$obForm->setEncType( "multipart/form-data" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                  ( "Dados para Autenticação");
$obFormulario->addForm                    ( $obForm                  );
$obFormulario->addHidden                  ( $obHdnAcao               );
$obFormulario->addHidden                  ( $obHdnCtrl               );
$obIPopUpEmpresa->geraFormulario          ( $obFormulario            );
$obFormulario->addComponente              ( $obIntNumeroLivro        );
$obFormulario->addComponente              ( $obPeriodo               );
$obFormulario->addComponente              ( $obIntQuantidadePaginas  );
$obFormulario->addComponente              ( $obTxtObservacoes        );
$obITextBoxSelectDocumento->geraFormulario( $obFormulario            );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
