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
    * Página de Formulário para informar os veiculos de publicacao que serao publicados no edital
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Thiago La Delfa Cabelleira

    * @ignore

    * $Id: FMManterHabilitacaoParticipante.php 62270 2015-04-15 20:13:46Z arthur $

    * Casos de uso : uc-03.05.17
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include do componente da popup do edital
include_once( CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php" );

//Definições padrões do framework
$stPrograma = "ManterHabilitacaoParticipante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao = $request->get('stAcao');
$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$stAcao;

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );
//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );
//Define o Hidde de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Carrega dados apartir do exibeParticipante mandando os valores por Request no carregamento da página
$jsOnload = "executaFuncaoAjax('exibeParticipante','"."&numEdital=".$request->get('stNumEdital').".&inCodModalidade=".$request->get('inCodModalidade')."&stExercicioLicitacao=".$request->get('stExercicioLicitacao')."&inCodLicitacao=".$request->get('inCodLicitacao')."&inCodEntidade=".$request->get('inCodEntidade')."');";

//Numero da licitação
$obSpnNumLicitacao = new Span();
$obSpnNumLicitacao->setId( 'spnNumeroLicitacao' );

//Objeto do processo licitatorio
$obSpnObjetoProcLic = new Span();
$obSpnObjetoProcLic->setId( 'spnProcessoLicitatorio' );

// Define objeto span para lista de participantes
$obSpnListaParticipante = new Span();
$obSpnListaParticipante->setId("spnListaParticipante");

// Define objeto span para alterar documento
$obSpnAlterarDocumentoParticipante = new Span();
$obSpnAlterarDocumentoParticipante->setId("spnAlterarDocumentoParticipante");

// Define objeto span para lista de documentos do participante selecionado
$obSpnListaDocumentoParticipante = new Span();
$obSpnListaDocumentoParticipante->setId("spnListaDocumentoParticipante");

//Define o Hidden do cod licitacao
$obHdnCodLicitacao = new Hidden;
$obHdnCodLicitacao->setName( "cod_licitacao" );
$obHdnCodLicitacao->setValue(" ");

//Define o Hidden do codigo do documento
$obHdnCodDocumento = new Hidden;
$obHdnCodDocumento->setName( "cod_documento" );
$obHdnCodDocumento->setValue( " " );

//Define o Hidden do cgm
$obHdnCgm = new Hidden;
$obHdnCgm->setName( "numcgm" );
$obHdnCgm->setValue( " " );

//Define o Hidden do cod modalidade
$obHdnCodModalidade = new Hidden;
$obHdnCodModalidade->setName( "cod_modalidade" );
$obHdnCodModalidade->setValue( " " );

//Define o Hidden do cod entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "cod_entidade" );
$obHdnCodEntidade->setValue( " " );

//Define o Hidden do exercicio
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "exercicio" );
$obHdnExercicio->setValue( " " );

//Definição do formulário
$obFormulario = new Formulario;
//carrega a lista por padrao
$obFormulario->addForm          ( $obForm                        );
//Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->setAjuda         ("UC-03.05.19"                   );
$obFormulario->addHidden        ( $obHdnCtrl                     );
$obFormulario->addHidden        ( $obHdnAcao                     );

$obFormulario->addHidden        ( $obHdnCodLicitacao             );
$obFormulario->addHidden        ( $obHdnCodDocumento             );
$obFormulario->addHidden        ( $obHdnCgm                      );
$obFormulario->addHidden        ( $obHdnCodModalidade            );
$obFormulario->addHidden        ( $obHdnCodEntidade              );
$obFormulario->addHidden        ( $obHdnExercicio                );

$obFormulario->addTitulo        ( "Habilitação dos Participantes da Licitação");
$obFormulario->addSpan          ( $obSpnNumLicitacao             );

//$obFormulario->addTitulo        ( "Participantes"                );
$obFormulario->addSpan          ( $obSpnListaParticipante        );
$obFormulario->addSpan		( $obSpnAlterarDocumentoParticipante );
//$obFormulario->addTitulo        ( "Documentos do Participante"   );
$obFormulario->addSpan          ( $obSpnListaDocumentoParticipante);
$obFormulario->Cancelar         ($pgFilt.'?'.Sessao::getId().'&'.http_build_query($_REQUEST));
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
