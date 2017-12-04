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
    * Página de Formulario de Inclusao/Alteracao de Atividade
    * Data de Criação   : 19/11/2004

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.07

*/

/*
$Log$
Revision 1.8  2006/09/15 14:32:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtividade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMAtividade = new RCEMAtividade;

$obRCEMAtividade->listarNiveisVigencia( $rsNivel );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnChaveServico = new Hidden;
$obHdnChaveServico->setName ("stChaveServico" );
$obHdnChaveServico->setValue( $stChaveServico );

$obCmbNivel = new Select;
$obCmbNivel->setName       ( "stChaveNivel"  );
$obCmbNivel->setRotulo     ( "Nível"         );
$obCmbNivel->setNull       ( false           );
$obCmbNivel->setCampoId    ( "[cod_vigencia]-[cod_nivel]" );
$obCmbNivel->setCampoDesc  ( "nom_nivel"     );
$obCmbNivel->setStyle      ( "width:250px"   );
$obCmbNivel->setId         ( "ChaveNivel" );
$obCmbNivel->addOption     ( "", "Selecione" );
$obCmbNivel->preencheCombo ( $rsNivel        );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgFormNivel );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.07");
$obFormulario->addTitulo     ( "Dados para Nível" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obCmbNivel );

$obFormulario->setFormFocus( $obCmbNivel->getid() );

$obFormulario->OK();
$obFormulario->show();
?>
