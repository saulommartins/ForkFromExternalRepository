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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterClassificacao";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$inCodClassificacao  = $request->get('inCodClassificacao');
$stNomeClassificacao = $request->get('stNomeClassificacao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

# Busca da configuração do Protocolo se deve gerar o código de classificação automático ou manual.
$boGeraCodigo = SistemaLegado::pegaConfiguracao("tipo_numeracao_classificacao_assunto", 5);

if (!empty($boGeraCodigo) && $boGeraCodigo == 'manual' && $stAcao == "incluir") {
    $obCodClassificacao = new TextBox;
    $obCodClassificacao->setRotulo    ( "Código" );
    $obCodClassificacao->setId        ( "inCodClassificacao" );
    $obCodClassificacao->setName      ( "inCodClassificacao" );
    $obCodClassificacao->setValue     ( $inCodClassificacao );
    $obCodClassificacao->setSize      ( 5 );
    $obCodClassificacao->setMaxLength ( 3 );
    $obCodClassificacao->setInteiro   ( true  );
    $obCodClassificacao->setTitle     ( "Informe o código da classificação" );
    $obCodClassificacao->setNull      ( false );
} else {
    $obHdnCodClassificacao = new Hidden;
    $obHdnCodClassificacao->setName( "inCodClassificacao" );
    $obHdnCodClassificacao->setValue( $inCodClassificacao );

    $obLabelClassificacao = new Label;
    $obLabelClassificacao->setRotulo('Código');
    $obLabelClassificacao->setValue($inCodClassificacao);
    $obLabelClassificacao->setName('');
}

$obNomeClassificacao = new TextBox;
$obNomeClassificacao->setRotulo    ( "Descrição" );
$obNomeClassificacao->setTitle     ( "Informe a descrição da classificação" );
$obNomeClassificacao->setName      ( "stNomeClassificacao" );
$obNomeClassificacao->setValue     ( $stNomeClassificacao  );
$obNomeClassificacao->setSize      ( 60 );
$obNomeClassificacao->setMaxLength ( 60 );
$obNomeClassificacao->setNull      ( false );


$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Dados da Classificação");
$obFormulario->addHidden($obHdnAcao);

if (!empty($boGeraCodigo) && $boGeraCodigo == 'manual' && $stAcao == "incluir")  {
    $obFormulario->addComponente($obCodClassificacao);
}

if ($stAcao == 'alterar') {
    $obFormulario->addHidden($obHdnCodClassificacao);
    $obFormulario->addComponente($obLabelClassificacao);
}

$obFormulario->addComponente($obNomeClassificacao);
$obFormulario->OK();

$obFormulario->show();

$jsOnLoad = "jQuery('#');'";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>