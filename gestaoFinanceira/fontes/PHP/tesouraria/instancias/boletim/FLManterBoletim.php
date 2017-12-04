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
    * Filtro para Alteração de Terminais - Tesouraria
    * Data de Criação   : 03/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-07-06 12:35:31 -0300 (Sex, 06 Jul 2007) $

    * Casos de uso: uc-02.04.17 , uc-02.04.25
*/

/*
$Log$
Revision 1.15  2007/07/06 15:35:31  cako
Bug#8376#

Revision 1.14  2007/06/20 13:57:57  cako
Bug#8376#

Revision 1.13  2007/06/14 14:47:10  bruce
Bug #8376#

Revision 1.12  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.11  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$boMultiploBoletim = $obRTesourariaBoletim->multiploBoletim();
if ($boMultiploBoletim) {
    header ( "Location:".'FLReabrirMultiploBoletim.php?'.Sessao::getId().'&stAcao=reabrir' );
} else {
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once ( CLA_IAPPLETTERMINAL );
    SistemaLegado::BloqueiaFrames();

    //Define o nome dos arquivos PHP
    $stPrograma      = "ManterBoletim";
    $pgFilt          = "FL".$stPrograma.".php";
    $pgList          = "LS".$stPrograma.".php";
    $pgForm          = "FM".$stPrograma.".php";
    $pgProc          = "PR".$stPrograma.".php";
    $pgOcul          = "OC".$stPrograma.".php";
    $pgJs            = "JS".$stPrograma.".js";

    $stAcao = $request->get('stAcao');
    if ( empty( $stAcao ) ) {
        $stAcao = "alterar";
    }

    $rsBoletimAberto = new Recordset;
    $obRTesourariaBoletim->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

    include_once( $pgJs );

    // DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget ( "oculto" );
    $obForm->setTarget( "telaPrincipal");

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName  ( "stCtrl"            );
    $obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName  ( "stAcao"            );
    $obHdnAcao->setValue ( $stAcao );

    $obIApplet = new IAppletTerminal( $obForm );

    // Define Objeto Select para Entidade
    $obCmbEntidade = new Select();
    $obCmbEntidade->setRotulo    ( "Entidade"                 );
    $obCmbEntidade->setName      ( "inCodEntidade"            );
    $obCmbEntidade->setTitle     ( "Selecione a Entidade"     );
    $obCmbEntidade->setCampoId   ( "cod_entidade"             );
    $obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
    $obCmbEntidade->setValue     ( $inCodEntidade             );
    $obCmbEntidade->setNull      ( false                      );
    if ($rsEntidade->getNumLinhas() > 1) {
        $obCmbEntidade->addOption    ( ""            ,"Selecione" );
        $obCmbEntidade->obEvento->setOnChange( "buscaDado('buscaBoletimFechamento');" );
    } else $jsSL = "buscaDado('buscaBoletimFechamento');";
    $obCmbEntidade->preencheCombo( $rsEntidade                );

    $obSpam = new Span();
    $obSpam->setId( "spnBoletim" );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm      ( $obForm        );
    $obFormulario->addHidden    ( $obHdnCtrl     );
    $obFormulario->addHidden    ( $obHdnAcao     );
    $obFormulario->addHidden    ( $obIApplet     );
    $obFormulario->addComponente( $obCmbEntidade );
    $obFormulario->addSpan      ( $obSpam        );

    $obFormulario->Ok();
    $obFormulario->show();
    if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

}

    ?>
