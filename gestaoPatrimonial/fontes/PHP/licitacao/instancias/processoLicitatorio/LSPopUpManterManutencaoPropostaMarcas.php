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
    * Página de Filtro de fornecedor
    * Data de Criação   : 06/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    * $Id: LSPopUpManterManutencaoPropostaMarcas.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$rsMarca = new RecordSet;
$arManterPropostas = sessao::read('arManterPropostas');

$rsMarca->preenche( $arManterPropostas['CadastrarMarcas'] );

$obLista = new Lista;

$obLista->setRecordSet( $rsMarca );
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( 'Marcas não cadastradas no Urbem' );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Marca");
$obLista->ultimoCabecalho->setWidth( 95 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "marca" );
$obLista->commitDado();

$obLista->show();

$obLista->getHTML();

$obLblAviso = new Label;
$obLblAviso->setName( 'aviso' );
$obLblAviso->setRotulo( 'Aviso' );
$obLblAviso->setValue( 'Para incluir automaticamente estas marcas e as propostas pressione OK.' );

if (Sessao::read('manutencaoPropostas')) {

    // usado para que salve a manutenção de propostas sem emitir o relatorio diretamente, fazendo assim o programa ficar mais danâmico
    $stJS = "	var stCtrl = window.opener.parent.frames['telaPrincipal'].document.frm.stCtrl.value;
                var stAcao      = window.opener.parent.frames['telaPrincipal'].document.frm.action;
                var stTarget    = window.opener.parent.frames['telaPrincipal'].document.frm.target;
                window.opener.parent.frames['telaPrincipal'].document.frm.target = 'oculto';
                window.opener.parent.frames['telaPrincipal'].document.getElementById('boIncluiMarca').value = true;
                window.opener.parent.frames['telaPrincipal'].document.frm.action = 'PRManterManutencaoProposta.php?".Sessao::getId()."';
                window.opener.parent.frames['telaPrincipal'].document.frm.submit();
                window.opener.parent.frames['telaPrincipal'].document.frm.target = stTarget;
                window.opener.parent.frames['telaPrincipal'].document.frm.action = stAcao;";

    //Redireciona a ação para o oculto assim pode-se apagar variaveis de controle tanto da sessão quanto dos arrays internos
    $stJSNao = "var stCtrl = 'cancelaInsercaoAutomaticaProposta';
                var stAcao      = window.opener.parent.frames['telaPrincipal'].document.frm.action;
                var stTarget    = window.opener.parent.frames['telaPrincipal'].document.frm.target;
                window.opener.parent.frames['telaPrincipal'].document.frm.stCtrl.value = stCtrl;
                window.opener.parent.frames['telaPrincipal'].document.frm.target = 'oculto';
                window.opener.parent.frames['telaPrincipal'].document.frm.action = 'OCManterManutencaoProposta.php?".Sessao::getId()."';
                window.opener.parent.frames['telaPrincipal'].document.frm.submit();
                window.opener.parent.frames['telaPrincipal'].document.frm.target = stTarget;
                window.opener.parent.frames['telaPrincipal'].document.frm.action = stAcao;";

} else {
    // gravar novas marcas imprimindo o relatorio apos
    $stJS = " window.opener.parent.frames['telaPrincipal'].location = 'PRManterManutencaoProposta.php?".Sessao::getId()."&boIncluiMarca=true';";
    $stJSNao =" window.opener.parent.frames['oculto'].location = 'OCManterManutencaoProposta.php?".Sessao::getId()."&stCtrl=cancelaInsercaoAutomaticaProposta';";
    $stJSNao.=" window.opener.parent.frames['telaPrincipal'].location = 'FMManterManutencaoProposta.php?".Sessao::getId()."';";
}

$btnOk = new Button;
$btnOk->setName( 'OK' );
$btnOk->setValue( 'Ok' );
$btnOk->setStyle( "width: 60px" );

$btnOk->obEvento->setOnClick("window.close();".$stJS);

$btnCancelar = new Button;
$btnCancelar->setName( 'Cancelar' );
$btnCancelar->setValue( 'Cancelar' );
$btnCancelar->setStyle( "width: 70px" );
$btnCancelar->obEvento->setOnClick("window.close();".$stJSNao);

$obFormulario = new Formulario;
$obFormulario->addComponente( $obLblAviso );
$obFormulario->defineBarra( array( $btnOk, $btnCancelar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
