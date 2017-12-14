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
    * Página de Formulario de Inclusao/Alteracao de Receitas
    * Data de Criação   : 24/09/2008
    *
    * Inicia no menu: PPA => Receitas => Alterar Receita
    *
    *
    * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
    *
    * @ignore
    * $Id: $
    * Casos de uso: uc-02.09.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_ORC_COMPONENTES."IPopUpReceita.class.php";
include_once CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php";
include_once CAM_GF_LDO_COMPONENTES . 'IPopUpRubrica.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".php";

$sessao->link = "";

unset($sessao->filtro);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    die( 'Erro: ação não definida em FLManterReceita!');
}

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda("UC-02.09.05");

// PPA
$obITextBoxSelectPPA = new ITextBoxSelectPPA; // ID: inCodPPATxt
$obITextBoxSelectPPA->setNull(false);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//****************************************//
//Monta FORMULARIO
//****************************************//

$obFormulario->addHidden($obHdnAcao);

$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addComponente($obITextBoxSelectPPA);

// Receita
$obIPopUpRubrica = new IPopUpRubrica();
$obIPopUpRubrica->setTitle('Informa a Receita');
$obIPopUpRubrica->setRotulo('Receita');
$obIPopUpRubrica->setNull(true);
$obIPopUpRubrica->setDedutora(false);
$obIPopUpRubrica->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
