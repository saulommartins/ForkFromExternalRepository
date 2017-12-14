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
* Página de formulário para inclusão/alteração do Objeto
* Data de Criação: 04/07/2006

* @author Analista: Diego Victoria
* @author Desenvolvedor: Leandro André Zis

* Casos de uso: uc-03.04.07
*/

/*
$Log$
Revision 1.4  2007/08/15 16:03:50  hboaventura
Bug#9925#

Revision 1.3  2007/07/02 19:11:28  rodrigo_sr
Bug#8705#

Revision 1.2  2007/02/23 20:20:10  bruce
Bug #8184#
Bug #8262#

Revision 1.1  2007/02/09 17:16:34  hboaventura
Migração da Manutenção do Objeto para a Configuração

Revision 1.7  2007/02/07 15:50:11  bruce
Bug #8184#

Revision 1.6  2007/01/23 18:34:34  bruce
Bug #8170#

Revision 1.5  2006/11/30 17:08:54  hboaventura
bug #7707"

Revision 1.4  2006/11/13 20:17:17  rodrigo
Bug 7374,7378,7375

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego


*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php");

$stPrograma = "ManterObjeto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

if ($stAcao=="alterar") {
 $obHdnObjeto = new Hidden;
 $obHdnObjeto->setName( "inCodigo" );
 $obHdnObjeto->setValue($_REQUEST['inCodigo']);

 $obTComprasObjeto = new TComprasObjeto;
 $obTComprasObjeto->setDado( 'cod_objeto',  $_REQUEST['inCodigo'] );
 $obTComprasObjeto->recuperaObjeto( $rsObjeto );
 $stDescricao = $rsObjeto->arElementos[0]['descricao'];
}

//Define objeto TEXTAREA para armazenar a DESCRICAO DA OBJETO
$obTxtObjeto= new TextArea;
$obTxtObjeto->setRotulo        ( "Descrição"                 );
$obTxtObjeto->setTitle         ( "Informe a descrição."    );
$obTxtObjeto->setName          ( "stDescricao"      );
$obTxtObjeto->setId            ( "stDescricao"      );
$obTxtObjeto->setValue         ( stripslashes(  stripslashes  ($stDescricao))  );
$obTxtObjeto->setNull          (false);
$obTxtObjeto->setCols          ( 50                      );
$obTxtObjeto->setRows          ( 5                       );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.04.07');
$obFormulario->addForm              ( $obForm                   );
$obFormulario->addHidden            ( $obHdnAcao                );
if ($stAcao=="alterar") {
 $obFormulario->addHidden           ( $obHdnObjeto              );
}
$obFormulario->addTitulo            ( "Dados do Objeto"         );
$obFormulario->addComponente        ( $obTxtObjeto              );

$obBtnOk = new Ok();
$obBtnOk->setId( 'Ok' );

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick( "limpaFormulario(); $('stDescricao').focus();" );

if ($stAcao == 'alterar') {
   $obFormulario->Cancelar( $pgList."?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro );
} else {
   $obFormulario->defineBarra( array($obBtnOk, $obBtnLimpar) );
}

$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
