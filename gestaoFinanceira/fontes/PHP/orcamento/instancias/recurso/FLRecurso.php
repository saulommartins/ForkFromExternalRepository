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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.01
                    uc-02.01.05
*/

/*
$Log$
Revision 1.8  2007/05/21 18:58:39  melo
Bug #9229#

Revision 1.7  2006/07/17 18:32:29  andre.almeida
Bug #6380#

Revision 1.6  2006/07/05 20:43:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stRecurso = "Recurso";
$pgFilt = "FL".$stRecurso.".php";
$pgList = "LS".$stRecurso.".php";
$pgForm = "FM".$stRecurso.".php";
$pgProc = "PR".$stRecurso.".php";
$pgOcul = "OC".$stRecurso.".php";
$pgJS   = "JS".$stRecurso.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtCodRecurso = new TextBox;
$obTxtCodRecurso->setName     ( "inCodRecurso" );
$obTxtCodRecurso->setValue    ( $inCodRecurso );
$obTxtCodRecurso->setRotulo   ( "Código" );
$obTxtCodRecurso->setSize     ( 20 );
$obTxtCodRecurso->setMaxLength( 20 );
$obTxtCodRecurso->setNull     ( true );
$obTxtCodRecurso->setTitle    ( 'Informe um código' );
$obTxtCodRecurso->setInteiro  ( true );

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDescRecurso = new TextBox;
$obTxtDescRecurso->setName     ( "stDescricao" );
$obTxtDescRecurso->setRotulo   ( "Descrição" );
$obTxtDescRecurso->setSize     ( 80 );
$obTxtDescRecurso->setMaxLength( 80 );
$obTxtDescRecurso->setNull     ( true );
$obTxtDescRecurso->setTitle    ( 'Informe uma descrição' );

$obCmbTipo = new Select;
$obCmbTipo->setRotulo        ( "Tipo de Recurso");
$obCmbTipo->setName          ( "stTipo"         );
$obCmbTipo->setStyle         ( "width: 100px"   );
$obCmbTipo->addOption        ( "", "Selecione"  );
$obCmbTipo->addOption        ( "V","Vinculado"  );
$obCmbTipo->addOption        ( "L","Livre"      );
$obCmbTipo->setValue         ( $stTipoNumeracao );
$obCmbTipo->setTitle         ( 'Informe o tipo do Recurso' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.05"           );

$obFormulario->addHidden( $obHdnAcao              );

$obFormulario->addTitulo( "Dados para Filtro"     );
$obFormulario->addComponente( $obTxtCodRecurso   );
$obFormulario->addComponente( $obCmbTipo         );
$obFormulario->addComponente( $obTxtDescRecurso  );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
