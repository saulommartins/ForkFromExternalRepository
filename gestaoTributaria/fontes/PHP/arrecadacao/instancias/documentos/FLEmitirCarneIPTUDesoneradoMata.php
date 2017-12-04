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
  * Data de criação : 11/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

    * $Id: FLEmitirCarne.php 44157 2010-04-16 16:25:12Z davi.aroldi $

  Caso de uso: uc-05.03.11
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarneIPTUDesoneradoMata";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "reemitir";
}

Sessao::write( "link", "" );

$obRMONCredito = new RMONCredito;
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();
$obMontaGrupoCredito = new MontaGrupoCredito;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obHdnExercicioGrupo  = new Hidden;
$obHdnExercicioGrupo->setName   ( "inExercicioGrupo" );
$obHdnExercicioGrupo->setValue  ( $_REQUEST["inExercicioGrupo"]  );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setName ('inExercicio');
$obTxtExercicio->setValue(Sessao::getExercicio());

$obHdnCampoNome  = new Hidden;
$obHdnCampoNome->setName   ( "stNome" );

$obBscGrupoCredito = new BuscaInner;
$obBscGrupoCredito->setRotulo    ( "Grupo de Créditos"          );
$obBscGrupoCredito->setTitle     ( "Busca Grupo de Créditos"    );
$obBscGrupoCredito->setNull     ( false    );
$obBscGrupoCredito->setId        ( "stGrupo"        );
$obBscGrupoCredito->obCampoCod->setName      ("inCodGrupo"      );
$obBscGrupoCredito->obCampoCod->setValue     ( $inCodGrupo      );
$obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

$obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
$obBscInscricaoImobiliaria->setRotulo             ( "Inscrição Imobiliária"   );
$obBscInscricaoImobiliaria->obLabelIntervalo->setValue ( "até"          );
$obBscInscricaoImobiliaria->obCampoCod->setName   ("inNumInscricaoImobiliariaInicial"  );
$obBscInscricaoImobiliaria->obCampoCod->setValue  ( $inNumInscricaoImobiliariaInicial  );
$obBscInscricaoImobiliaria->obCampoCod->obEvento->setOnChange("buscaValor('buscaIImobiliariaInicio');");
$obBscInscricaoImobiliaria->setFuncaoBusca        ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
$obBscInscricaoImobiliaria->obCampoCod2->setName  ( "inNumInscricaoImobiliariaFinal" );
$obBscInscricaoImobiliaria->obCampoCod2->setValue ( $inNumInscricaoImobiliariaFinal  );
$obBscInscricaoImobiliaria->obCampoCod2->obEvento->setOnChange("buscaValor('buscaIImobiliariaFinal');");
$obBscInscricaoImobiliaria->setFuncaoBusca2       ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
$obBscInscricaoImobiliaria->setNull(false);

$obRCIMNivel = new RCIMNivel;
$obRCIMNivel->mascaraNivelVigenciaAtual($stMascara);

$obHdnEmissao =  new Hidden;
$obHdnEmissao->setName   ( "emissao_carnes" );
$obHdnEmissao->setValue  ( "local" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnEmissao );
$obFormulario->addHidden ( $obHdnExercicioGrupo );
$obFormulario->addHidden ( $obHdnCampoNome );
$obFormulario->addTitulo( "Dados para o Filtro"  );
$obFormulario->addComponente( $obTxtExercicio    );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );

$obFormulario->addComponente( $obBscInscricaoImobiliaria );

$obFormulario->Ok();
$obFormulario->setFormFocus($obTxtExercicio->getId());
$obFormulario->Show();
