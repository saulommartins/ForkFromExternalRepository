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
  * Página de Formulário para importção de arquivo de baixa de débitos
  * Data de criação : 24/03/2006

   * @autor Analista    : Fabio Bertoldi
   * @autor Programador : Marcelo B. Paulino

   Caso de uso : uc-05.03.10

    * $Id: FMManterBaixaAutomatica.php 59612 2014-09-02 12:00:51Z gelson $

 */

/*
$Log$
Revision 1.6  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterBaixaAutomatica";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

$obForm = new Form;
$obForm->setAction ( $pgProc               );
$obForm->setTarget ( "oculto"              );
$obForm->setEncType( "multipart/form-data" );

//Cria objetos ocultos
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

//Cria objeto FileBox para upload de arquivo
$obFilArquivoBaixa = new FileBox;
$obFilArquivoBaixa->setRotulo   ( "Arquivo" );
$obFilArquivoBaixa->setName     ( "stArquivoBaixa" );
$obFilArquivoBaixa->setSize     ( 40 );
$obFilArquivoBaixa->setTitle    ( "Arquivo de baixa." );
$obFilArquivoBaixa->setNull     ( false );
//$obFilArquivoBaixa->setReadOnly ( true );
$obFilArquivoBaixa->setMaxLength( 100 );
$obFilArquivoBaixa->setId       ( "stArquivoBaixa" );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados dos Arquivos" );
$obFormulario->addComponente( $obFilArquivoBaixa );
$obFormulario->setFormFocus ( $obFilArquivoBaixa->getid() );

$obFormulario->OK();
$obFormulario->show();

include_once( $pgJs );
