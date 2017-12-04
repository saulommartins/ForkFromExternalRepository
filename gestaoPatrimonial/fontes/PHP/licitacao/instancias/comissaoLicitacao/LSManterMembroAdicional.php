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
    * Página de Lista de Licitacoes
    * Data de Criação   : 09/04/2014
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoMembroAdicional.class.php");

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
} else {
    $_REQUEST['stAcao'] = "alterar";
}

$arMembrosAdicionais = Sessao::remove('arMembrosAdicionais');

$stPrograma = "ManterMembroAdicional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$boTransacao = new Transacao();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget('telaPrincipal');

$stFiltroMembro = "";

if ($request->get('stExercicioLicitacao'))
    $stFiltroMembro .= " AND ll.exercicio = '".$request->get('stExercicioLicitacao')."' \n";

if ($request->get('inCodEntidade'))
    $stFiltroMembro .= " AND ll.cod_entidade in (".implode(",", $request->get('inCodEntidade')).") \n";
    
if ($request->get('inCodModalidade'))
    $stFiltroMembro .= " AND ll.cod_modalidade = ".$request->get('inCodModalidade')." \n";

if ($request->get('inCodLicitacao'))
    $stFiltroMembro .= " AND ll.cod_licitacao = ".$request->get('inCodLicitacao')." \n";
    
$stOrderMembro = " ORDER BY ll.cod_entidade
                    , ll.cod_licitacao
                    , ll.cod_modalidade";

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->recuperaLicitacaoMembro($rsLicitacao, $stFiltroMembro, $stOrderMembro, $boTransacao);

$inCodLicitacao  = $rsLicitacao->getCampo('cod_licitacao');
$inCodModalidade = $rsLicitacao->getCampo('cod_modalidade');

if (!empty($inCodLicitacao) && !empty($inCodModalidade)) {
    $stFiltro  = " AND MA.cod_licitacao  = ".$inCodLicitacao;
    $stFiltro .= " AND MA.cod_modalidade = ".$inCodModalidade;
    $stOrder   = " ORDER BY cgm.nom_cgm ";
}

$obTLicitacaoMembroAdicional = new TLicitacaoMembroAdicional;
$obTLicitacaoMembroAdicional->setDado('exercicio', $request->get('stExercicioLicitacao'));
$obTLicitacaoMembroAdicional->recuperaMembroAdicional($rsMembrosAdicionais, $stFiltro, $stOrder,$boTransacao);

$arLicitacao = ($rsLicitacao->arElementos);

//Instancia uma TableTree para demonstrar os programas
$obTableTree = new TableTree;

if ($rsMembrosAdicionais->getNumLinhas() < 0){
    $obTableTree->setRecordset ($rsMembrosAdicionais);
} else {
    $obTableTree->setRecordset ($rsLicitacao);
}

$obTableTree->setArquivo               ($pgOcul);
$obTableTree->setParametros            (array('cod_licitacao' => 'cod_licitacao','cod_entidade'=>'cod_entidade','cod_modalidade'=>'cod_modalidade','exercicio' => 'exercicio'));
$obTableTree->setComplementoParametros ('stCtrl=montaAcoes'  );
$obTableTree->setSummary               ('Lista de Licitações');
$obTableTree->Head->addCabecalho       ('Licitação' ,8);
$obTableTree->Head->addCabecalho       ('Entidade'  ,25);
$obTableTree->Head->addCabecalho       ('Modalidade',15);
$obTableTree->Body->addCampo           ('[cod_licitacao]/[exercicio]','D');
$obTableTree->Body->addCampo           ('entidade','E'); 
$obTableTree->Body->addCampo           ('modalidade', 'E');
$obTableTree->montaHTML();

//Instancia um span para os programas
$obSpnMembros = new Span();
$obSpnMembros->setId   ('spnMembros');
$obSpnMembros->setValue($obTableTree->getHTML());

$obBtnOk = new Ok();
$obBtnOk->setName ( "btnOk" );
$obBtnOk->setValue( "Ok" );

$obBtnCancelar = new Cancelar();
$obBtnCancelar->setName ( "btnCancelar" );
$obBtnCancelar->setValue( "Cancelar" );
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$pgFilt."','telaPrincipal');");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm       );
$obFormulario->addHidden    ( $obHdnAcao    );
$obFormulario->addHidden    ( $obHdnCtrl    );
$obFormulario->addSpan      ( $obSpnMembros );

if ($rsMembrosAdicionais->getNumLinhas() > 0){
    $obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar) );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>