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
    * Página de Formulário - Gerar Restos a Pagar

    * Data de Criação   : 20/12/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Id: FLGerarRestosAPagar.php 66131 2016-07-20 17:32:50Z michel $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "GerarRestosAPagar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//****************************************//
// Define COMPONENTES DO FORMULARIO
//****************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( $stEval );

$obRConfiguracao = new RConfiguracaoConfiguracao;

if (Sessao::getExercicio() >= '2013') {
    $arCodEntidade = array();

    $stFiltro  = " WHERE parametro like 'virada_GF_entidade%'";
    $stFiltro .= "   AND exercicio = '".Sessao::getExercicio()."'";
    $stFiltro .= "   AND cod_modulo = 10";

    $obRConfiguracao->obTConfiguracao->recuperaTodos($rsConfiguracao, $stFiltro);
    foreach ($rsConfiguracao->arElementos as $index => $value) {
        if ($value['valor'] == 'T') {
            $arCodEntidade[] = substr($value['parametro'],19);
        }
    }

    Sessao::write('arCodEntidade',$arCodEntidade);

    //Define objeto de select multiplo de entidade por usuários
    $obISelectEntidadeUsuarioCredito = new ITextBoxSelectEntidadeGeral ();
    $obISelectEntidadeUsuarioCredito->obTextBox->setId    ( "inCodEntidadeCredito" );
    $obISelectEntidadeUsuarioCredito->obTextBox->setName  ( "inCodEntidadeCredito" );
    $obISelectEntidadeUsuarioCredito->obSelect->setName   ( "stNomEntidadeCredito" );
    $obISelectEntidadeUsuarioCredito->obSelect->setId     ( "stNomEntidadeCredito" );
    $obISelectEntidadeUsuarioCredito->setNull             ( false                  );
    $obISelectEntidadeUsuarioCredito->obSelect->obEvento->setOnChange("montaParametrosGET('verificaEntidade','inCodEntidadeCredito');");

    $stObs = "Este processo é lento devido aos cálculos de restos a pagar.<BR>Recomenda-se que o mesmo seja executado após o término do expediente.";

    $jsOnLoad = "jq('#Ok').attr('disabled',true);";
} else {
    $obRConfiguracao = new RConfiguracaoConfiguracao;
    $obRConfiguracao->setParametro('virada_GF');
    $obRConfiguracao->setExercicio( Sessao::getExercicio());
    $obRConfiguracao->setCodModulo( 10 );
    $obRConfiguracao->consultar();

    if ( $obRConfiguracao->getValor() == 'T' ) {
        $stObs = "Este processo já foi  executado! Se deseja prosseguir faça a Anulação de Restos à pagar primeiro!";
    } else {
        $stObs = "Este processo é lento devido aos cálculos de restos a pagar.<BR>Recomenda-se que o mesmo seja executado após o término do expediente.";
    }
}

$obLblObs = new Label;
$obLblObs->setId ("lblObs");
$obLblObs->setValue   ( $stObs );
$obLblObs->setRotulo  ( "Observação: " );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addTitulo( "Gerar Restos a Pagar para o próximo exercício"        );

$obBtnOk = new Ok();

if (Sessao::getExercicio() >= '2013') {
    $obFormulario->addComponente ( $obISelectEntidadeUsuarioCredito );
    $obFormulario->addComponente($obLblObs);
    $obFormulario->defineBarra( array($obBtnOk) );
}

if (Sessao::getExercicio() < '2013') {
    $obFormulario->addComponente($obLblObs);

    if ( $obRConfiguracao->getValor() != 'T') {
        $obFormulario->defineBarra( array($obBtnOk) );
    }
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
