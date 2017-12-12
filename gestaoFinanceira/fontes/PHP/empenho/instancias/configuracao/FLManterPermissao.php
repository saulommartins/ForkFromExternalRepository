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
    * Data de Criação   : 17/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: leandro.zis $
    $Date: 2006-07-14 17:59:57 -0300 (Sex, 14 Jul 2006) $

    * Casos de uso: uc-02.03.01
*/

/*
$Log$
Revision 1.7  2006/07/14 20:59:57  leandro.zis
Bug #6181#

Revision 1.6  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPermissao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProximo = $pgForm;

include_once ($pgJS);

Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

$obRegra = new RUsuario;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgForm );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName ( "inNumCGM"         );
$obHdnNumCGM->setValue( $_POST['inNumCGM']  );

//Define o objeto BuscaInner para CGM do Usuário
$obBscReduzido = new BuscaInner;
$obBscReduzido->setRotulo               ( "Usuário" );
$obBscReduzido->setTitle                ( "Informe o CGM do usuário" );
$obBscReduzido->setNulL                 ( false );
$obBscReduzido->setId                   ( "stNomCGM" );
$obBscReduzido->obCampoCod->setName     ( "inNumCGM" );
$obBscReduzido->obCampoCod->setAlign    ("left");
$obBscReduzido->obCampoCod->obEvento->setOnBlur("buscaDado('buscaUsuario');"); //verificar problema do javascript
$obBscReduzido->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."usuario/FLProcurarUsuario.php','frm','inNumCGM','stNomCGM','NumCGM','".Sessao::getId()."','800','550');" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo         ( "Dados para Permissão do Usuário" );

$obFormulario->addComponente     ( $obBscReduzido                    );
$obFormulario->addHidden         ( $obHdnAcao                        );
$obFormulario->addHidden         ( $obHdnCtrl                        );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
