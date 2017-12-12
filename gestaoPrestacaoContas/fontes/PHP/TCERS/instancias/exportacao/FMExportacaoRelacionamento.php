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
    * Página Formulário - Parâmetros do Arquivo UNIORCAM.
    * Data de Criação   : 16/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.05
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.4  2006/07/17 14:30:48  cako
Bug #6013#

Revision 1.3  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoRelacionamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

foreach ($_POST['arEntidadesSelecionadas'] as $key => $valor) {

    $stEntidadesSelecionadas .= $valor."#";
}

$stEntidadesSelecionadas = substr($stEntidadesSelecionadas, 0, strlen($stEntidadesSelecionadas)-1);

foreach ($_POST['arArquivosSelecionados'] as $key => $valor) {

    $stArquivosSelecionados .= $valor."#";
}

$stArquivosSelecionados = substr($stArquivosSelecionados, 0, strlen($stArquivosSelecionados)-1);

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( "../processamento/PRExportador.php" );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( $_POST['hdnPaginaExportacao']  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnPeriodo = new Hidden;
$obHdnPeriodo->setName ( "inPeriodo" );
$obHdnPeriodo->setValue( $_POST['inPeriodo']  );

$obHdnCnpjSetor = new Hidden;
$obHdnCnpjSetor->setName ( "stCnpjSetor" );
$obHdnCnpjSetor->setValue( $_POST['stCnpjSetor']  );

$obHdnTipoExportacao = new Hidden;
$obHdnTipoExportacao->setName ( "boTipoExportacao" );
$obHdnTipoExportacao->setValue( $_POST['boTipoExportacao']  );

$obHdnEntidadesSelecionadas = new Hidden;
$obHdnEntidadesSelecionadas->setName ( "stEntidadesSelecionadas" );
$obHdnEntidadesSelecionadas->setValue( $stEntidadesSelecionadas  );

$obHdnArquivosSelecionados = new Hidden;
$obHdnArquivosSelecionados->setName ( "stArquivosSelecionados" );
$obHdnArquivosSelecionados->setValue( $stArquivosSelecionados  );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados de Relacionamento de Contas" );

$obFormulario->addHidden( $obHdnAcao                  );
$obFormulario->addHidden( $obHdnCtrl                  );
$obFormulario->addHidden( $obHdnPeriodo               );
$obFormulario->addHidden( $obHdnCnpjSetor             );
//$obFormulario->addHidden( $obHdnOrgaoUnidade          );
$obFormulario->addHidden( $obHdnTipoExportacao        );
$obFormulario->addHidden( $obHdnEntidadesSelecionadas );
$obFormulario->addHidden( $obHdnArquivosSelecionados  );

//$obFormulario->addSpan( $obSpnExportacaoRelacionamento );

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();
SistemaLegado::executaFrameOculto( "buscaDado('ajustaOrgaoUnidade');" );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
