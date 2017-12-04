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
* Página de Filtro Candidato
* Data de Criação: 30/06/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterCandidato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRCandidato = new RConcursoCandidato;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//destroi arrays de sessao que armazenam os dados do FILTRO
unset( $sessao->filtro );
unset( $sessao->link );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction		( $pgList			);
$obForm->setTarget		( "telaPrincipal" 	); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName		( "stAcao" 			);
$obHdnAcao->setValue	( $stAcao			);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName		( "stCtrl" 			);
$obHdnCtrl->setValue	( "" 				);

//Define os objetos para BUSCAR e EXIBIR dados do CANDIDATO
$obBscCgm = new BuscaInner;
$obBscCgm->setRotulo			( "Candidato" 				);
$obBscCgm->setTitle				( "Informe o CGM do Candidato" );
$obBscCgm->setNull				( false 					);
$obBscCgm->setId				( "nom_cgm" 				);
$obBscCgm->obCampoCod->setName	( "inNumCGM" 				);
$obBscCgm->obCampoCod->setValue	( $inNumCGM 				);
$obBscCgm->obCampoCod->obEvento->setOnBlur("buscaValor( 'buscaCGM','".$pgOcul."','".$pgList."','telaPrincipal','".Sessao::getId()."' );");
$obBscCgm->setFuncaoBusca(
"abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','nom_cgm','fisica','".Sessao::getId()."','800','550')");

//Define a busca pelo CONCURSO
$obTxtEdital = new TextBox;
$obTxtEdital->setRotulo   ( "Edital"       );
$obTxtEdital->setName     ( "inCodEdital"  );
$obTxtEdital->setValue    ( $inCodEdital   );
$obTxtEdital->setNull     ( false          );

$obRCandidato->obRConcursoConcurso->listarConcursoPorExercicio( $rsConcurso );

$obCmbEdital = new Select;
$obCmbEdital->setRotulo        ( "Edital" 					);
$obCmbEdital->setName          ( "inTxtEdital" 				);
$obCmbEdital->setStyle         ( "width: 200px"				);
$obCmbEdital->setTitle         ( "Selecione o edital"		);
$obCmbEdital->setCampoID       ( "cod_edital" 	);
$obCmbEdital->setCampoDesc     ( "nom_norma" 	);
$obCmbEdital->addOption        ( "", "Selecione" 				);
$obCmbEdital->setValue         ( $inCodEdital 				);
$obCmbEdital->setNull          ( false 						);
$obCmbEdital->preencheCombo    ( $rsConcurso 					);

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

if ($stAcao=='consultar') {
    $obFormulario->addTitulo	( "Busca de Candidato" 			     );
    $obFormulario->addComponente( $obBscCgm );
    $js="f.inNumCGM.focus();";
    SistemaLegado::executaFramePrincipal($js);
} elseif ($stAcao=='alterar' || $stAcao == 'reclassifi') {
    if( $stAcao == 'reclassifi')
        $obFormulario->addTitulo	( "Reclassificar candidato" 		     );
    else
        $obFormulario->addTitulo	( "Classificar candidato" 		     );

    $obFormulario->addComponenteComposto( $obTxtEdital, $obCmbEdital );
}

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
