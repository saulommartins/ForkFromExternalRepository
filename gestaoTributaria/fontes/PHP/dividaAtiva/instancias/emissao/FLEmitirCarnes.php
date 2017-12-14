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
  * Página de Filtro de Emissão de Carnês
  * Data de criação : 13/04/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: FLEmitirCarnes.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.04
**/

/*
$Log$
Revision 1.2  2007/07/02 20:45:28  cercato
retirando filtro por exercicio.

Revision 1.1  2007/04/16 18:11:29  cercato
adicionando funcoes para emitir carne pela divida.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDivida.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpCobranca.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarnes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php"; $pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "emitir";
}
Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obHdnCampoNome  = new Hidden;
$obHdnCampoNome->setName   ( "stNome" );

$obTxtNumeracao = new TextBox;
$obTxtNumeracao->setId('inNum');
$obTxtNumeracao->setName('inNum');
$obTxtNumeracao->obEvento->setOnChange("buscaValor('buscaCredito');");
$obTxtNumeracao->setRotulo('Numeração');
$obTxtNumeracao->setTitle('Numeração do Carnê que deseja Emitir <hr /> <i>Somente Numeros Inteiros <br /> Ex: 432101002040 </i>');
$obTxtNumeracao->setMaxLength(20);
$obTxtNumeracao->setSize(30);
$obTxtNumeracao->setInteiro(true);
$obTxtNumeracao->obEvento->setOnChange("montaParametrosGET( ?verificaNumeracao?, ?inNumAnterior? );");

//inscricao imobiliaria
$obIPopUpImovel = new IPopUpImovel;
$obIPopUpImovel->obInnerImovel->setNull ( true );
$obIPopUpImovel->obInnerImovel->setTitle ( "Informe o código da inscrição imobiliária." );

//inscricao economica
$obIPopUpEmpresa = new IPopUpEmpresa;
$obIPopUpEmpresa->obInnerEmpresa->setNull ( true );
$obIPopUpEmpresa->obInnerEmpresa->setTitle ( "Informe o código da inscrição econômica." );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "Informe o número do CGM." );

$obIPopUpDivida = new IPopUpDivida;
$obIPopUpDivida->obInnerDivida->setNull( true );

$obIPopUpCobranca = new IPopUpCobranca;
$obIPopUpCobranca->obInnerCobranca->setNull( true );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick    ( "submeteFiltro();" );

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "
    if ( (ifila) == fila.length ) {
        limpaFormulario();
    } else {
        alertaAviso('Aguarde todos os processos concluírem.','form','erro', '<?=Sessao::getId();?>' );
    }"
);

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnCampoNome );
$obFormulario->addTitulo( "Dados para o Filtro"  );
$obFormulario->addComponente( $obTxtNumeracao );
$obFormulario->addComponente( $obPopUpCGM  );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
$obIPopUpDivida->geraFormulario ( $obFormulario );
$obIPopUpCobranca->geraFormulario ( $obFormulario );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->setFormFocus($obTxtNumeracao->getId());
$obFormulario->Show();
