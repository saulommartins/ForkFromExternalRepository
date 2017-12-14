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

    * $Id: FMManterBaixaAutomatica.php 46939 2012-06-29 11:13:21Z tonismar $

 */

/*
$Log$
Revision 1.6  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

$stPrograma = "ManterImportacaoMensal";
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

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

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

$obCGMFornecedor = new IPopUpCGMVinculado($obForm);
$obCGMFornecedor->setTabelaVinculo('beneficio.layout_fornecedor');
$obCGMFornecedor->setCampoVinculo('cgm_fornecedor');
$obCGMFornecedor->setNomeVinculo('Fornecedor do plano');
$obCGMFornecedor->setRotulo('Fornecedor do plano');
$obCGMFornecedor->setName('stCGMFornecedor');
$obCGMFornecedor->setId('stCGMFornecedor');
$obCGMFornecedor->obCampoCod->setName('inCGMFornecedor');
$obCGMFornecedor->obCampoCod->setId('inCGMFornecedor');
$obCGMFornecedor->setNull(false);

//Cria objeto FileBox para upload de arquivo
$obFilArquivo = new FileBox;
$obFilArquivo->setRotulo   ( "Arquivo" );
$obFilArquivo->setName     ( "stArquivo" );
$obFilArquivo->setSize     ( 40 );
$obFilArquivo->setTitle    ( "Selecione o arquivo .csv com os dados para importação." );
$obFilArquivo->setNull     ( false );
$obFilArquivo->setMaxLength( 100 );
$obFilArquivo->setId       ( "stArquivo" );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo    ( "Dados dos Arquivos" );
$obFormulario->addComponente( $obCGMFornecedor );
$obFormulario->addComponente( $obFilArquivo );

$obFormulario->OK();
$obFormulario->show();

include_once( $pgJs );

?>