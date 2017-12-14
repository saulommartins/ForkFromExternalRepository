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
    * Arquivo de filtro para alteração/exclusão de Solicitantes
    * Data de Criação: 11/02/2008

    * @author Analista: Gelson W
    * @author Luiz Felipe Prestes Teixeira

    * Casos de uso: uc-03.04.34

    $Id: FLManterSolicitante.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IPOPUPCGM);

//Define o nome dos arquivos PHP
$stPrograma = "ManterSolicitante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";

$rsSolicitante = new RecordSet;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

#sessao->link= "";
Sessao::write('link' , '');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obForm = new Form;
$obForm->setAction ( $pgList );

$obBscCGMSolicitante = new IPopUpCGM ($obForm);
$obBscCGMSolicitante->setRotulo('Solicitante');
$obBscCGMSolicitante->setTipo('fisica');
$obBscCGMSolicitante->setId('stNomCGMSolicitante');
$obBscCGMSolicitante->setNull( true  );
$obBscCGMSolicitante->setTitle( 'Informe o CGM do Solicitante.');
$obBscCGMSolicitante->setValue( $stNomCGMSolicitante  );
$obBscCGMSolicitante->obCampoCod->setValue( $inCGM     );
$obBscCGMSolicitante->obCampoCod->setSize(10);
$obBscCGMSolicitante->obCampoCod->setName( 'inCodCGMSolicitante' );

$obRdbTodos = new Radio;
$obRdbTodos->setTitle( "Informe o status do solicitante." );
$obRdbTodos->setRotulo ( "Status" );
$obRdbTodos->setName( "boAtivo" );
$obRdbTodos->setId( "boAtivo" );
$obRdbTodos->setValue( 'todos');
$obRdbTodos->setNull( 'true' );
$obRdbTodos->setLabel( "Todos"  );
$obRdbTodos->setChecked( $boAtivo ==''  );

$obRdbAtivo = new Radio;
$obRdbAtivo->setName( "boAtivo" );
$obRdbAtivo->setId( "boAtivo" );
$obRdbAtivo->setChecked( $boAtivo);
$obRdbAtivo->setValue( 'true' );
$obRdbAtivo->setLabel( "Ativo" );
$obRdbAtivo->setNull( 'true' );

$obRdbInativo = new Radio;
$obRdbInativo->setName( "boAtivo" );
$obRdbInativo->setId( "boAtivo" );
$obRdbInativo->setValue( 'false');
$obRdbInativo->setLabel( "Inativo");
$obRdbInativo->setChecked( $boAtivo == 'false');

$obFormulario = new Formulario;
$obFormulario->addTitulo( "Dados do Solicitante" );
$obFormulario->addForm( $obForm);
$obFormulario->setAjuda ("UC-03.04.34");
$obFormulario->addHidden( $obHdnAcao);
$obFormulario->addComponente( $obBscCGMSolicitante );
$obFormulario->agrupaComponentes( array($obRdbTodos, $obRdbAtivo, $obRdbInativo ) );

$obFormulario->Ok();
$obFormulario->Show();

?>
