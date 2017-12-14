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
    * Página de Formulario de Inclusao de Autorização de Notas Fiscais

    * Data de Criação   : 27/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: FMManterImpressao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php"                                        );
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php"                                   );
include_once( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php"                              );

$stAcao = $request->get('stAcao');
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) { $stAcao = "incluir"; }

//Define o nome dos arquivos PHP
$stPrograma = "ManterImpressao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include ($pgJs);
//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

//Inscricao Economica
$obIPopUpEmpresa = new IPopUpEmpresa;
$obIPopUpEmpresa->obInnerEmpresa->setNull ( false                                                  );
$obIPopUpEmpresa->obInnerEmpresa->setTitle( "Informe o código da inscrição econômica de empresa." );

$obTxtSerie = new TextBox;
$obTxtSerie->setName  ( "stSerie"                    );
$obTxtSerie->setSize  ( "10"                         );
$obTxtSerie->setRotulo( "Série"                      );
$obTxtSerie->setTitle ( "Informe a série das notas." );
$obTxtSerie->setNull  ( false                        );

$obIntQuantidadeTaloes = new TextBox;
$obIntQuantidadeTaloes->setName   ( "inQuantidadeTaloes"                  );
$obIntQuantidadeTaloes->setSize   ( "10"                                  );
$obIntQuantidadeTaloes->setRotulo ( "Quantidade de Talões ou Formulários" );
$obIntQuantidadeTaloes->setTitle  ( "Informe a quantidade de Talões ou Formulários."     );
$obIntQuantidadeTaloes->setInteiro( true                                  );
$obIntQuantidadeTaloes->setNull   ( false                                 );

$obIntNotaFiscalInicial = new TextBox;
$obIntNotaFiscalInicial->setName   ( "inNotaFiscalInicial"                      );
$obIntNotaFiscalInicial->setSize   ( "10"                                       );
$obIntNotaFiscalInicial->setRotulo ( "Nota Inicial"                             );
$obIntNotaFiscalInicial->setTitle  ( "Informe o número da nota fiscal inicial." );
$obIntNotaFiscalInicial->setInteiro( true                                       );
$obIntNotaFiscalInicial->setNull   ( false                                      );
$obIntNotaFiscalInicial->obEvento->setOnChange("verificaMaior();");

$obIntNotaFiscalFinal = new TextBox;
$obIntNotaFiscalFinal->setName   ( "inNotaFiscalFinal"                      );
$obIntNotaFiscalFinal->setSize   ( "10"                                     );
$obIntNotaFiscalFinal->setRotulo ( "Nota Final"                             );
$obIntNotaFiscalFinal->setTitle  ( "Informe o número da nota fiscal final." );
$obIntNotaFiscalFinal->setInteiro( true                                     );
$obIntNotaFiscalFinal->setNull   ( false                                    );
$obIntNotaFiscalFinal->obEvento->setOnChange("verificaMaior();");

$obIntQuantidadeVias = new TextBox;
$obIntQuantidadeVias->setName   ( "inQuantidadeVias"                           );
$obIntQuantidadeVias->setSize   ( "10"                                         );
$obIntQuantidadeVias->setRotulo ( "Quantidade de Vias"                         );
$obIntQuantidadeVias->setTitle  ( "Informe a quantidade de vias de cada nota." );
$obIntQuantidadeVias->setInteiro( true                                         );
$obIntQuantidadeVias->setNull   ( false                                        );

$obTxtObservacoes = new TextArea;
$obTxtObservacoes->setName  ( "stObservacoes"                  );
$obTxtObservacoes->setId    ( "stObservacoes"                  );
$obTxtObservacoes->setTitle ( "Observações"                    );
$obTxtObservacoes->setRotulo( "Observações" );

//DOCUMENTOS
$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo( "Documento" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setTitle( "Selecione o Documento que comprova a autorização de impressão." );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL  ( false       );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc               );
$obForm->settarget ( "oculto"              );
$obForm->setEncType( "multipart/form-data" );

$obBscGrafica = new IPopUpCGMVinculado( $obForm );
$obBscGrafica->setTabelaVinculo       ( 'fiscalizacao.grafica' );
$obBscGrafica->setFiltroVinculado     ( "AND tabela_vinculo.ativo='t'");
$obBscGrafica->setCampoVinculo        ( 'numcgm'               );
$obBscGrafica->setNomeVinculo         ( 'Gráfica'              );
$obBscGrafica->setRotulo              ( 'Gráfica'              );
$obBscGrafica->setName                ( 'stNomCGM'             );
$obBscGrafica->setId                  ( 'stNomCGM'             );
$obBscGrafica->obCampoCod->setName    ( "inCGM"                );
$obBscGrafica->obCampoCod->setId      ( "inCGM"                );
$obBscGrafica->obCampoCod->setNull    ( false                  );
$obBscGrafica->setNull                ( false                  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                  ( "Dados para Impressão"  );
$obFormulario->addForm                    ( $obForm                 );
$obFormulario->addHidden                  ( $obHdnAcao              );
$obFormulario->addHidden                  ( $obHdnCtrl              );
$obIPopUpEmpresa->geraFormulario          ( $obFormulario           );
$obFormulario->addComponente              ( $obBscGrafica           );
$obFormulario->addComponente              ( $obTxtSerie             );
$obFormulario->addComponente              ( $obIntQuantidadeTaloes  );
$obFormulario->addComponente              ( $obIntNotaFiscalInicial );
$obFormulario->addComponente              ( $obIntNotaFiscalFinal   );
$obFormulario->addComponente              ( $obIntQuantidadeVias    );
$obFormulario->addComponente              ( $obTxtObservacoes       );
$obITextBoxSelectDocumento->geraFormulario( $obFormulario           );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
