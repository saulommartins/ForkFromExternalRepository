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
 * Página de Filtro Permissão Autorização
 * Data de Criação   : 04/12/2004

 * @author Analista: Jorge B. Ribarr
 * @author Desenvolvedor: Gelson W. Gonçalves

 * @ignore

 * Casos de uso: uc-02.03.01

 $Id: FMManterPermissao.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPermissaoAutorizacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterPermissao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProximo = $pgProc ;

include_once $pgJS;

$obRegra = new REmpenhoPermissaoAutorizacao;

$obRegra->setExercicio ( Sessao::getExercicio() );
$obRegra->obRUsuario->obRCGM->setNumCGM( $_POST['inNumCGM'] );

$obRegra->obRUsuario->obRCGM->consultar( $rsCGM );
$stNomCGM = $obRegra->obRUsuario->obRCGM->getNomCGM();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "excluir";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProximo );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName ( "inNumCGM" );
$obHdnNumCGM->setValue( $_POST['inNumCGM']  );

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName ( "stNomCGM" );
$obHdnNomCGM->setValue( $stNomCGM );

$obHdnPerm = new Hidden;
$obHdnPerm->setName ( "stPerm" );
$obHdnPerm->setValue( $stPerm );

//Define o objeto Label Usuário
$obLblUsuario = new Label;
$obLblUsuario->setRotulo( "Usuário" );
$obLblUsuario->setValue( $_POST['inNumCGM']." - $stNomCGM" );

$obHdnOrgao = new Hidden;
$obHdnOrgao->setName ( "inCodOrgao" );
$obHdnOrgao->setValue( $inCodOrgao  );

//Define Span para DataGrid
$obSpnPermissoes = new Span;
$obSpnPermissoes->setId ( "spnListaPermissoes" );

Sessao::remove('arPermissoes');

SistemaLegado::executaFramePrincipal("BloqueiaFrames(true,false); buscaDado('montaListaPermissoes');");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao   );
$obFormulario->addHidden( $obHdnCtrl   );
$obFormulario->addHidden( $obHdnNumCGM );
$obFormulario->addHidden( $obHdnNomCGM );
$obFormulario->addHidden( $obHdnPerm   );

$obFormulario->addTitulo( "Definindo Permissões para o Usuário");
$obFormulario->addComponente( $obLblUsuario       );

$obFormulario->addSpan ( $obSpnPermissoes );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao ;
$obFormulario->Cancelar($stLocation);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
