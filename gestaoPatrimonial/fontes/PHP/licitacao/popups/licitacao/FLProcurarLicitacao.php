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
* Página de Formulario de filtro do objeto
* Data de Criação   : 11/10/2006

* @author Desenvolvedor: Leandro André Zis

* Casos de uso :uc-03.05.22
*/

/*
$Log$
Revision 1.2  2006/11/28 10:25:37  leandro.zis
corrigido caso de uso

Revision 1.1  2006/11/01 19:58:21  leandro.zis
atualizado

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectModalidadeLicitacao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarLicitacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

Sessao::write('link', '');

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName('');
$obHdnCampoNom->setValue(2);

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $nomForm );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obExercicio = new Exercicio;

$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral;
$obITextBoxSelectEntidade->setObrigatorio(false);

$obTxtNumeroLicitacao = new Inteiro;
$obTxtNumeroLicitacao->setName      ( "nmLicitacao"              );
$obTxtNumeroLicitacao->setRotulo    ( "Licitação"                     );
$obTxtNumeroLicitacao->setTitle     ( "Informe o número da licitação");

$obISelectModalidadeLicitacao = new ISelectModalidadeLicitacao;

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden( $obHdnForm              );
$obFormulario->addHidden( $obHdnCampoNum          );
$obFormulario->addHidden( $obHdnCampoNom          );
$obFormulario->addHidden( $obHdnTipoBusca 		  );
$obFormulario->addTitulo     ( "Dados para filtro" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obTxtNumeroLicitacao );
$obFormulario->addComponente ( $obISelectModalidadeLicitacao );
$obFormulario->addComponente ( $obITextBoxSelectEntidade );
$obFormulario->OK();
$obFormulario->show();

?>
