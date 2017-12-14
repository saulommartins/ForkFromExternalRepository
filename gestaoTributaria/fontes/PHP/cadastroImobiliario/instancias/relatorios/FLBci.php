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
    * Arquivo paga Filtro do relatorio de BCI
    * Data de Criação: 22/08/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FLBci.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.26
*/

/*
$Log$
Revision 1.4  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once 'JSCadastroImobiliario.js';

if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

// MONTA MASCARA DE LOCALIZACAO
$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigencia );
$obMontaLocalizacao->obRCIMLocalizacao->setCodigoVigencia( $rsVigencia->getCampo( 'cod_vigencia' ));
$obMontaLocalizacao->obRCIMLocalizacao->listarNiveis( $rsRecordSet );
while ( !$rsRecordSet->eof() ) {
    $obMontaLocalizacao->stMascara .= $rsRecordSet->getCampo("mascara").".";
    $rsRecordSet->proximo();
}
$stMascaraLocalizacao = substr( $obMontaLocalizacao->getMascara(), 0 , strlen($obMontaLocalizacao->stMascara) - 1 );

//MASCARA INSCRICAO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();

Sessao::remove('sessao_transf5');

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCCadastroImobiliarioBoletim.php" );

$obHdnNomeGrupo = new Hidden;
$obHdnNomeGrupo->setName ( "stNomeGrupo" );
$obHdnNomeGrupo->setId ( "stNomeGrupo" );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName ( "inCodigoUF" );
$obHdnCodUF->setId ( "inCodigoUF" );

$obHdnCodMun = new Hidden;
$obHdnCodMun->setName ( "inCodigoMunicipio" );
$obHdnCodMun->setId ( "inCodigoMunicipio" );

$obBscLocalizacao = new BuscaInnerIntervalo;
$obBscLocalizacao->setRotulo ( "Localização" );
$obBscLocalizacao->setTitle ( "Informe um período.");
$obBscLocalizacao->obLabelIntervalo->setValue ( "até" );
$obBscLocalizacao->obCampoCod->setName ( "inCodInicioLocalizacao" );
$obBscLocalizacao->obCampoCod->setMascara ( $stMascaraLocalizacao );
$obBscLocalizacao->obCampoCod->setMaxLength ( strlen($stMascaraLocalizacao)+2 );
$obBscLocalizacao->obCampoCod->setMinLength ( strlen($stMascaraLocalizacao)+2 );
$obBscLocalizacao->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."localizacao/FLBuscaLocalizacao.php','frm','inCodInicioLocalizacao','stNomeGrupo','','".Sessao::getId()."','800','450');" );
$obBscLocalizacao->obCampoCod2->setName ( "inCodTerminoLocalizacao" );
$obBscLocalizacao->obCampoCod2->setMascara ( $stMascaraLocalizacao );
$obBscLocalizacao->obCampoCod2->setMaxLength ( strlen($stMascaraLocalizacao)+2 );
$obBscLocalizacao->obCampoCod2->setMinLength ( strlen($stMascaraLocalizacao)+2 );
$obBscLocalizacao->setFuncaoBusca2( "abrePopUp('".CAM_GT_CIM_POPUPS."localizacao/FLBuscaLocalizacao.php','frm','inCodTerminoLocalizacao','stNomeGrupo','','".Sessao::getId()."','800','450');" );

$obBscInscricao = new BuscaInnerIntervalo;
$obBscInscricao->setRotulo ( "Inscrição Imobiliária" );
$obBscInscricao->setTitle ( "Informe um período.");
$obBscInscricao->obLabelIntervalo->setValue ( "até" );
$obBscInscricao->obCampoCod->setName ( "inCodInicioInscricao" );
$obBscInscricao->obCampoCod->setMascara ( $stMascaraInscricao );
$obBscInscricao->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inCodInicioInscricao','stNomeGrupo','','".Sessao::getId()."','800','450');" );
$obBscInscricao->obCampoCod2->setName ( "inCodTerminoInscricao" );
$obBscInscricao->obCampoCod2->setMascara ( $stMascaraInscricao );
$obBscInscricao->setFuncaoBusca2( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inCodTerminoInscricao','stNomeGrupo','','".Sessao::getId()."','800','450');" );

$obBscLogradouro = new BuscaInnerIntervalo;
$obBscLogradouro->setRotulo ( "Logradouro" );
$obBscLogradouro->setTitle ( "Informe um período.");
$obBscLogradouro->obLabelIntervalo->setValue ( "até" );
$obBscLogradouro->obCampoCod->setName ( "inCodInicioLogradouro" );
$obBscLogradouro->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inCodInicioLogradouro','stNomeGrupo','','".Sessao::getId()."','800','450');" );
$obBscLogradouro->obCampoCod2->setName ( "inCodTerminoLogradouro" );
$obBscLogradouro->setFuncaoBusca2( "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inCodTerminoLogradouro','stNomeGrupo','','".Sessao::getId()."','800','450');" );

$obBscBairro = new BuscaInnerIntervalo;
$obBscBairro->setRotulo ( "Bairro" );
$obBscBairro->setTitle ( "Informe um período.");
$obBscBairro->obLabelIntervalo->setValue ( "até" );
$obBscBairro->obCampoCod->setName ( "inCodInicioBairro" );
$obBscBairro->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."bairroSistema/FLProcurarBairro.php','frm','inCodInicioBairro','stNomeGrupo','','".Sessao::getId()."','800','450');" );
$obBscBairro->obCampoCod2->setName ( "inCodTerminoBairro" );
$obBscBairro->setFuncaoBusca2( "abrePopUp('".CAM_GT_CIM_POPUPS."bairroSistema/FLProcurarBairro.php','frm','inCodTerminoBairro','stNomeGrupo','','".Sessao::getId()."','800','450');" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.23" );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addHidden( $obHdnNomeGrupo );
$obFormulario->addHidden( $obHdnCodUF );
$obFormulario->addHidden( $obHdnCodMun );

$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addComponente( $obBscInscricao );
$obFormulario->addComponente( $obBscLocalizacao );
$obFormulario->addComponente( $obBscLogradouro );
$obFormulario->addComponente( $obBscBairro );

$obFormulario->OK();
//$obFormulario->setFormFocus( $obCodInicioLocalizacao->getid() );
$obFormulario->show();

?>
