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
    * Página de Formulario de Filtro do Calculo ISS

    * Data de Criação   : 13/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLEscriturarReceita.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.22

*/

/*
$Log$
Revision 1.2  2007/10/02 18:47:46  cercato
Ticket#10305#

Revision 1.1  2006/10/26 14:04:28  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//RARREscriturarReceita
//Define o nome dos arquivos PHP
$stPrograma    = "EscriturarReceita";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::write( "link", "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

//Efetuar Escrituração por (servico)
$obRdbEscrituracaoServico = new Radio;
$obRdbEscrituracaoServico->setRotulo   ( "Efetuar Escrituração por" );
$obRdbEscrituracaoServico->setTitle    ( "Selecione opção de escrituração a ser lançada." );
$obRdbEscrituracaoServico->setName     ( "stEscrituracao" );
$obRdbEscrituracaoServico->setLabel    ( "Serviço" );
$obRdbEscrituracaoServico->setValue    ( "servico" );
$obRdbEscrituracaoServico->setNull     ( false );
$obRdbEscrituracaoServico->setChecked  ( true );

//Efetuar Escrituração por (nota)
$obRdbEscrituracaoNota = new Radio;
$obRdbEscrituracaoNota->setRotulo   ( "Efetuar Escrituração por" );
$obRdbEscrituracaoNota->setTitle    ( "Selecione opção de escrituração a ser lançada." );
$obRdbEscrituracaoNota->setName     ( "stEscrituracao" );
$obRdbEscrituracaoNota->setLabel    ( "Nota" );
$obRdbEscrituracaoNota->setValue    ( "nota" );
$obRdbEscrituracaoNota->setNull     ( false );

//Efetuar Escrituração por (sem movimento)
$obRdbEscrituracaoSemMovimento = new Radio;
$obRdbEscrituracaoSemMovimento->setRotulo   ( "Efetuar Escrituração por" );
$obRdbEscrituracaoSemMovimento->setTitle    ( "Selecione opção de escrituração a ser lançada." );
$obRdbEscrituracaoSemMovimento->setName     ( "stEscrituracao" );
$obRdbEscrituracaoSemMovimento->setLabel    ( "Sem Movimento" );
$obRdbEscrituracaoSemMovimento->setValue    ( "smov" ); //sem movimento
$obRdbEscrituracaoSemMovimento->setNull     ( false );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo         ( "CGM" );
$obBscCGM->setTitle          ( "Informe o CGM do proprietário da empresa." );
$obBscCGM->setId             ( "stCGM" );
$obBscCGM->obCampoCod->setName       ( "inCGM" );
$obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('PreencheCGM');" );
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

//inscricao economica
$obIPopUpEmpresa = new IPopUpEmpresa;
$obIPopUpEmpresa->setVerificaInscricao(true);
$obIPopUpEmpresa->obInnerEmpresa->setNull ( true );
$obIPopUpEmpresa->obInnerEmpresa->setTitle ( "Informe o código da inscrição econômica de empresa." );


//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.03.22" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->agrupaComponentes ( array( $obRdbEscrituracaoServico, $obRdbEscrituracaoNota, $obRdbEscrituracaoSemMovimento ) );
$obFormulario->addComponente ( $obBscCGM );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );

$obFormulario->Ok ();
$obFormulario->show();
