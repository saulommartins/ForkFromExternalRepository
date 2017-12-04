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
* Página de Formulário Lista Candidato
* Data de Criação: 30/06/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php" 	);

$sessao->transf["boCarregada"]= 'false';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCandidato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho    = CAM_GRH."PHP/concurso/instancias/candidato/";
$obRCandidato = new RConcursoCandidato;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar':     $pgProx = $pgForm; break;
    case 'reclassifi':  $pgProx = $pgProc; break;
    case 'baixar' :     $pgProx = $pgBaix; break;
    case 'excluir':     $pgProx = $pgProc; break;
    DEFAULT       :     $pgProx = $pgForm;
}

if ( is_array($sessao->link) ) {
    $_REQUEST = $sessao->link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $sessao->link[$key] = $valor;
    }
}

if ($request->get('inNumCGM')) {
    $obRCandidato->setNumCGM ( $request->get('inNumCGM') );
    $obRCandidato->consultarCGM($rsCGM);
    $stLink .= '&inNumCGM='.$request->get('inNumCGM');
}

$obLblNome = new Label;
$obLblNome->setRotulo('Nome');
$obLblNome->setValue($obRCandidato->getNumCGM() . " - " . $obRCandidato->getNomCGM());

$obSpnLista = new Span;
$obSpnLista->setId ('spnLista' );

$obFormulario = new Formulario;

$stLink .= "&stAcao=".$stAcao;
$obRCandidato->setNumCGM($request->get('inNumCGM'));

if ($stAcao=='alterar') {
    $arVal         = preg_split( "/[^a-zA-Z0-9]/", $request->get('inCodConcurso'));
    $obRCandidato->obRConcursoConcurso->setCodEdital($arVal[0]);
} else {
    if ($stAcao != 'reclassifi') {
        $obFormulario->addTitulo    ( "Consultar candidato"            );
        $obFormulario->addComponente        ( $obLblNome );
    }
}

$obRCandidato->obRConcursoConcurso->setCodEdital( $request->get('inCodEdital') );
$obRCandidato->obRConcursoConcurso->consultarConcurso( $rsNorma, $rsCargos );

$obTxtCodEdital = new TextBox;
$obTxtCodEdital->setRotulo("Edital");
$obTxtCodEdital->setTitle ("Selecione o concurso".Sessao::getEntidade()."");
$obTxtCodEdital->setName  ("inCodEdital");
$obTxtCodEdital->setValue ($request->get('inCodEdital'));
$obTxtCodEdital->setStyle    ("color: #000000");
$obTxtCodEdital->setMaxLength(5    );
$obTxtCodEdital->setSize     (10   );
$obTxtCodEdital->setDisabled (true );

$obLblEdital = new Label;
$obLblEdital->setRotulo( "Edital");
$obLblEdital->setValue( $request->get('inCodEdital') ." - ". $rsNorma->getCampo("nom_norma") );

$obCmbEdital = new TextBox;
$obCmbEdital->setRotulo   ("Edital");
$obCmbEdital->setValue    ($rsNorma->getCampo("nom_norma"));
$obCmbEdital->setStyle    ("color: #000000" );
$obCmbEdital->setSize     (30  );
$obCmbEdital->setMaxLength(30  );
$obCmbEdital->setDisabled (true);

if (($stAcao == "alterar") || ($stAcao == "reclassifi")) {
    $obRCandidato->listarCandidatoPorEdital( $rsLista );
} else {
    if ($sessao->transf["boCarregada"] != 'true') {
        $sessao->transf4["reclassificados"]= array();
        $sessao->transf["boCarregada"]= 'true';
        $obRCandidato->listarCandidatoPorCodigo( $rsLista );
        $sessao->transf4["reclassificados"] = $rsLista->getElementos();
    }
}
$obLista = new Lista;

if ($stAcao == 'reclassifi') {
    $obLista->setTitulo("Candidatos aprovados");
} else {
    $obLista->setTitulo("Registro de concurso".Sessao::getEntidade()."s");
}

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet($rsLista);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Candidato");
$obLista->ultimoCabecalho->setWidth(8);
$obLista->commitCabecalho();

if ($stAcao == 'alterar' || $stAcao == 'reclassifi') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->ultimoCabecalho->setWidth(40);
    $obLista->commitCabecalho();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Edital");
    $obLista->ultimoCabecalho->setWidth(30);
    $obLista->commitCabecalho();
}

if ($stAcao == 'consultar' || $stAcao == 'reclassifi') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Classificação");
    $obLista->ultimoCabecalho->setWidth(10);
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Média");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

if ($stAcao == 'alterar' || $stAcao == 'reclassifi') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth(5);
    $obLista->commitCabecalho();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo("cod_candidato");
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();

if ($stAcao == 'alterar' || $stAcao == 'reclassifi') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo("nom_cgm");
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo("cod_edital");
    $obLista->ultimoDado->setAlinhamento('DIREITA');
    $obLista->commitDado();
}

if ($stAcao == 'consultar' || $stAcao == 'reclassifi') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo("classificacao");
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo( "media");
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo('[situacao][reclassificacao]');
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();

if (($stAcao == "alterar") || ($stAcao == "reclassifi")) {
    $obLista->addAcao();
    $obLista->ultimaAcao->addCampo  ( "&stDescQuestao" , "nom_cgm");
    $obLista->ultimaAcao->setAcao	( ($stAcao=='reclassifi') ? "popup_".$stAcao : $stAcao  );
    $obLista->ultimaAcao->addCampo	( "&inCodCandidato"     , "cod_candidato" 	            );
    $obLista->ultimaAcao->addCampo	( "&inCodEdital"        , "cod_edital"	                );
    $obLista->ultimaAcao->addCampo	( "&inNumCGM"           , "numcgm"	                    );
    $obLista->ultimaAcao->addCampo	( "&boReclassificar"    , "reclassificado"              );
    $obLista->addDado();
    if ($stAcao == "reclassifi") {
        $obLista->ultimaAcao->setLink   ( $stCaminho.$pgProx."?".Sessao::getId() . $stLink );
    } else {
        $obLista->ultimaAcao->setLink   ( $pgProx."?".Sessao::getId() . $stLink     );
    }

    $obLista->commitAcao();
}

if ($stAcao == "alterar") {
    $obFormulario->addTitulo    ( "Classificar candidato"            );
    $obFormulario->addComponente( $obLblEdital );
}

if ($stAcao == "reclassifi") {
    $obFormulario->addTitulo    ( "Reclassificar candidato"            );
    $obFormulario->addComponente( $obLblEdital );
}

if ($stAcao == 'reclassifi') {
    $obForm = new Form;
    $obForm->setAction($pgProc);
    $obForm->setTarget("oculto");
    $obForm->setEncType("multipart/form-data");

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName("stCtrl");
    $obHdnCtrl->setValue("");

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName("stAcao");
    $obHdnAcao->setValue($stAcao);

    $obFormulario->addForm($obForm);
    $obFormulario->addHidden($obHdnCtrl);
    $obFormulario->addHidden($obHdnAcao);
}

$obFormulario->show();
$obLista->show();

SistemaLegado::executaFramePrincipal( $stJs );
include( $pgJs );

?>