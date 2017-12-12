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
* Página de Filtro Grupo
* Data de Criação: 06/04/2005

* @author Analista: ???
* @author Desenvolvedor: Marcelo Boezzio Paulino

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-13 09:55:05 -0300 (Qua, 13 Jun 2007) $

* Casos de uso: uc-04.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_CON_NEGOCIO."RConcursoConcurso.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma."Filtro.php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obRConcursoConcurso = new RConcursoConcurso;
$obRConcursoConcurso->recuperaConfiguracao( $arConfiguracao );
foreach ($arConfiguracao as $key => $valor) {
    if ( $key == 'mascara_concurso'.Sessao::getEntidade() ) {
        $stMascaraConcurso = $valor;
    }
    if ( $key == 'tipo_portaria_edital'.Sessao::getEntidade() ) {
        $inTipoNormaEdital = $valor;
    }
}

//destroi arrays de sessao que armazenam os dados do FILTRO
unset( $sessao->filtro );
unset( $sessao->link );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto COMBO para armazenar a NORMA que cria o CONCURSO
if ($inTipoNormaEdital) {
    $stFiltro = " where N.cod_tipo_norma=".$inTipoNormaEdital;
}
$obRConcursoConcurso->recuperaExercicio( $rsExercicio, $stFiltro );

$obCmbExercicio = new Select;
$obCmbExercicio->setName      ( "inExercicio" );
$obCmbExercicio->setRotulo    ( "Exercício" );
$obCmbExercicio->setValue     ( $inExercicio );
$obCmbExercicio->setStyle     ( "width: 100px" );
$obCmbExercicio->addOption    ( "", "Selecione" );
$obCmbExercicio->setCampoId   ( "exercicio" );
$obCmbExercicio->setCampoDesc ( "exercicio" );
$obCmbExercicio->preencheCombo( $rsExercicio );
$obCmbExercicio->setNull      ( false );
$obCmbExercicio->setTitle     ( 'Exercícios de concurso. realizados' );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addTitulo( "Dados para filtro"  );
$obFormulario->addComponente( $obCmbExercicio    );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
