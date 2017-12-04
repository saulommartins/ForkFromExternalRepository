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
    * Página de Formulário para Autenticação
    * Data de Criação   : 04/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Id: FMManterAutenticacao.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 31168 $
    $Name$
    $Autor:$
    $Date: 2007-07-25 11:49:14 -0300 (Qua, 25 Jul 2007) $

    * Casos de uso: uc-02.04.15

*/

ob_start();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETAUTENTICACAO );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaAutenticacao.class.php"                                  );

$stCabecalho = preg_replace("/<script.*jquery.*/i",'',ob_get_contents());
$stCabecalho = preg_replace("/jq = jQuery.*/i",'',$stCabecalho);

ob_clean();
echo $stCabecalho;

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutenticacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgVolta= $request->get('pg_volta');

$stFiltro = (isset($stFiltro)) ? $stFiltro : '';
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$arFiltro = Sessao::read('filtro');

if ( !count($arFiltro) > 0 ) {
    Sessao::write('filtro', $_REQUEST);
} else {
    $_REQUEST = $arFiltro;
}

if (isset($arFiltro['inCodEntidade'])&&array_key_exists('inCodEntidade',$arFiltro)) {
    $stFiltro .= "&inCodEntidade=".$arFiltro['inCodEntidade'];
}

foreach ($_GET as $key => $value) {
    $stFiltro .= "&".$key."=".$value;
}

//Parametro para executar bloqueiamento de campo na volta do arquivo FMManterArrecadacaoReceita.php
$stFiltro .= "&inCodigoEntidade=".$arFiltro['inCodEntidade'];

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addTitulo    ( "Dados para Autenticação" );

$arRetencoes = Sessao::read('retencoes');
$arPagamento = Sessao::read('pagamento');
$arDescricao = Sessao::read('stDescricao');

if (count($arRetencoes)>0 OR count($arPagamento) > 0) {
    $inCount = 0;
    foreach ($arDescricao as $key => $item) {
        $$inCount = new IAppletAutenticacao($obForm);
        $$inCount->setTextoImpressao       ($item['stDescricao']['texto']);
        $$inCount->setHeight               (20);
        $$inCount->setRotulo               ($item['stDescricao']['acao']);
        $$inCount->setName                 ($item['stDescricao']['acao']);
        $$inCount->setId                   ($item['stDescricao']['acao']);

        $obFormulario->addComponente($$inCount);

        $inCount++;
    }
} else {
    $obIAppletAutenticacao = new IAppletAutenticacao( $obForm );
    $obIAppletAutenticacao->setTextoImpressao( $arDescricao['stDescricao'] );
    $obIAppletAutenticacao->setHeight( 20 );

    $obFormulario->addComponente ( $obIAppletAutenticacao  );
}

$stLocation = $pgVolta.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obVoltar = new Button;
$obVoltar->setName  ( "Voltar" );
$obVoltar->setValue ( "Voltar" );
$obVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");
$obFormulario->defineBarra( array($obVoltar ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
