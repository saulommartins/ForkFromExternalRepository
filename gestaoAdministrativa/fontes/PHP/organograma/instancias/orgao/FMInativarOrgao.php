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
 * Arquivo de instância para manutenção de orgao
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 * $Id: FMInativarOrgao.php 59612 2014-09-02 12:00:51Z gelson $

 Casos de uso: uc-01.05.02

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

$stPrograma = "InativarOrgao";
$pgFilt		= "FL".$stPrograma.".php";
$pgList		= "LS".$stPrograma.".php";
$pgForm		= "FM".$stPrograma.".php";
$pgProc		= "PR".$stPrograma.".php";
$pgOcul		= "OC".$stPrograma.".php";
$pgJs		= "JS".$stPrograma.".js";

include_once( $pgJs );

$arDados = Sessao::read('filtro');
if ($arDados) {
    $stFiltro = '';
    foreach ($arDados as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro."&inCodOrganograma=".$_REQUEST['inCodOrganograma'];

$rsOrganograma  = new RecordSet;
$rsNivel        = new RecordSet;
$rsOrgaoSuperior= new RecordSet;
$rsCalendario   = new RecordSet;
$rsTipoNorma    = new RecordSet;
$rsNorma        = new RecordSet;

$obRegra     = new ROrganogramaOrgao;

$stAcao = $request->get('stAcao');
$inCodOrgao = $_REQUEST['inCodOrgao'];

$obRegra->setCodOrgao( $_REQUEST['inCodOrgao'] );
$obRegra->obROrganograma->setCodOrganograma( $_REQUEST['inCodOrganograma'] );

$obRegra->listarOrgaosInferiores( $rsOrgaosInferiores );
$obRegra->consultar();

//Verifica se existe algum órgão inferior a este, que não tenha sido inativado.
//Caso exista, volta para a página de lista mostrando a mensagem.
if ( !$rsOrgaosInferiores->eof() ) {
    $boVerifica = false;
    while ( !$rsOrgaosInferiores->eof() ) {
        if ( !$rsOrgaosInferiores->getCampo('inativacao') ) {
            $boVerifica = true;
            break;
        }

        $rsOrgaosInferiores->proximo();
    }
    if ($boVerifica) {
        sistemaLegado::alertaAviso($stLocation,"Órgão: ".$obRegra->getDescricao()." possui órgãos inferiores que não foram inativados. ","","aviso", Sessao::getId(), "../");
        exit;
    }
}

$stDescricao          = $obRegra->getDescricao();
$stDataCriacao        = $obRegra->getCriacao();
$inNumCGMResponsavel  = $obRegra->obRCgmPF->getNumCGM();
$inNumCGMOrgao        = $obRegra->obRCgmPJ->getNumCGM();
$inCodNivel           = $obRegra->obRNivel->getCodNivel();
$obRegra->obROrganograma->obRNivel->setCodNivel( $inCodNivel );
$obRegra->obROrganograma->consultarNivel();
$stDescNivel          = $obRegra->obROrganograma->obRNivel->getDescricao();
$inCodCalendario      = $obRegra->obRCalendario->getCodCalendar();
$stDescCalendario     = $obRegra->obRCalendario->getDescricao();
$inCodOrganograma     = $obRegra->obROrganograma->getCodOrganograma();
$stDataImplantacao    = $obRegra->obROrganograma->getDtImplantacao();
$inCodOrgaoSuperior   = $obRegra->getCodOrgaoSuperior();
$inCodNorma           = $obRegra->obRNorma->getCodNorma();
$stDescNorma          = $obRegra->obRNorma->getDescricaoNorma();
$inCodTipoNorma       = $obRegra->obRNorma->obRTipoNorma->getCodTipoNorma();
$stDescTipoNorma      = $obRegra->obRNorma->obRTipoNorma->getNomeTipoNorma();

//Caso exista órgão superior
if ($inCodOrgaoSuperior) {
    $obRegra->setCodOrgao( $inCodOrgaoSuperior );
    $obRegra->consultar();
    $stDescOrgaoSuperior = $obRegra->getDescricao();
}

$obRegra->obRCgmPF->setNumCGM( $inNumCGMResponsavel );
$obRegra->obRCgmPF->consultarCGM( $rsCGMPF );
$stNomCGMResponsavel = $rsCGMPF->getCampo('nom_cgm');

if ($inNumCGMOrgao !="") {
    $obRegra->obRCgmPJ->setNumCGM( $inNumCGMOrgao );
    $obRegra->obRCgmPJ->consultarCGM( $rsCGMPJ );
    $stNomCGMOrgao = $rsCGMPJ->getCampo('nom_cgm');
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodOrgao = new Hidden;
$obHdnCodOrgao->setName( "inCodOrgao" );
$obHdnCodOrgao->setValue( $inCodOrgao );

$obHdnValidaData = new HiddenEval;
$obHdnValidaData->setName( "stValidaData" );
$obHdnValidaData->setValue( "if ( !ValidaData( '$stDataCriacao',document.frm.stDataInativacao.value) ) {erro = true; mensagem += '@Campo Data de inativação deve ser maior que a Data de Criação!()';}" );

/////////////////////////////////////////////////////////
//    Dados do órgão
/////////////////////////////////////////////////////////
$obLblCodOrgao = new Label;
$obLblCodOrgao->setRotulo ( "Codigo" );
$obLblCodOrgao->setValue  ( $_REQUEST['inCodOrgao']);

$obLblDescricao = new Label;
$obLblDescricao->setRotulo        ( "Descrição" );
$obLblDescricao->setValue         ( $stDescricao  );

//Organograma
$obLblOrganograma = new Label;
$obLblOrganograma->setRotulo( "Organograma" );
$obLblOrganograma->setValue ( "$inCodOrganograma - $stDataImplantacao" );
$obHdnCodOrganograma = new Hidden;
$obHdnCodOrganograma->setName("inCodOrganograma");
$obHdnCodOrganograma->setValue($inCodOrganograma);

//Nível
$obLblNivel = new Label;
$obLblNivel->setRotulo        ( "Nivel" );
$obLblNivel->setValue         ( "$inCodNivel - $stDescNivel");

//OrgaoSuperior
$obLblOrgaoSuperior = new Label;
$obLblOrgaoSuperior->setRotulo        ( "Órgão Superior" );
$obLblOrgaoSuperior->setValue         ( "$inCodOrgaoSuperior - $stDescOrgaoSuperior" );

//DataCriacao
$obLblDataCriacao = new Label;
$obLblDataCriacao->setRotulo        ( "Data de Criação" );
$obLblDataCriacao->setValue         ( $stDataCriacao  );

//Calendario
$obLblCalendario = new Label;
$obLblCalendario->setRotulo        ( "Calendário" );
$obLblCalendario->setValue         ( "$inCodCalendario - $stDescCalendario" );

//TipoNorma
$obLblTipoNorma = new Label;
$obLblTipoNorma->setRotulo        ( "Tipo Norma" );
$obLblTipoNorma->setValue         ( "$inCodTipoNorma - $stDescTipoNorma" );

//Norma
$obLblNorma = new Label;
$obLblNorma->setRotulo        ( "Norma" );
$obLblNorma->setValue         ( "$inCodNorma - $stDescNorma" );

//DataInativacao
$obTxtDataInativacao = new Data;
$obTxtDataInativacao->setRotulo        ( "Data de Inativação" );
$obTxtDataInativacao->setName          ( "stDataInativacao" );
$obTxtDataInativacao->setValue         ( $stDataInativacao );
$obTxtDataInativacao->setNull          ( false );

/////////////////////////////////////////////////////////
//    Reponsável pelo órgão
/////////////////////////////////////////////////////////

//CGM do responsável
$obLblCgmResponsavel = new Label;
$obLblCgmResponsavel->setRotulo        ( "Cgm do Responsável" );
$obLblCgmResponsavel->setValue         ( "$inNumCGMResponsavel - $stNomCGMResponsavel" );

//Telefone Comercial
$obLblTelefoneComercial = new Label;
$obLblTelefoneComercial->setRotulo        ( "Telefone Comercial"  );
$obLblTelefoneComercial->setValue         ( $inTelefoneComercial );

//Telefone Residencial
$obLblTelefoneResidencial = new Label;
$obLblTelefoneResidencial->setRotulo        ( "Telefone Residencial"  );
$obLblTelefoneResidencial->setValue         ( $inTelefoneResidencial  );

//Email
$obLblEmailResponsavel = new Label;
$obLblEmailResponsavel->setRotulo        ( "Email"   );
$obLblEmailResponsavel->setValue         ( $stEmailResponsavel  );

/////////////////////////////////////////////////////////
//    Dados de CGM
/////////////////////////////////////////////////////////

$descricaoCgmOrgao = "";
if ( ($inNumCGMOrgao != "") && ($stNomCGMOrgao!="")) {
    $descricaoCgmOrgao = $inNumCGMOrgao." - ".$stNomCGMOrgao;
}

//CGM do órgão
$obLblCgmOrgao = new Label;
$obLblCgmOrgao->setRotulo        ( "Cgm do Órgão" );
$obLblCgmOrgao->setValue         ( $descricaoCgmOrgao );

//Telefone
$obLblTelefone = new Label;
$obLblTelefone->setRotulo        ( "Telefone" );
$obLblTelefone->setValue         ( $inTelefone);

//Endereço
$obLblEndereco = new Label;
$obLblEndereco->setRotulo        ( "Endereço"  );
$obLblEndereco->setValue         ( $stEndereco );

//Numero
$obLblNumero = new Label;
$obLblNumero->setRotulo        ( "Número"  );
$obLblNumero->setValue         ( $inNumero  );

//Email
$obLblEmailOrgao = new Label;
$obLblEmailOrgao->setRotulo        ( "Email "   );
$obLblEmailOrgao->setValue         ( $stEmailOrgao  );

$obOk     = new Ok;
$obVoltar = new Button;
$obVoltar->setName  ( "Voltar" );
$obVoltar->setValue ( "Voltar" );
$obVoltar->setStyle ( "width: 80px" );
$obVoltar->obEvento->setOnClick("Cancelar('$stLocation');");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->setAjuda             ( 'UC-01.05.02' );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnValidaData , true);
$obFormulario->addHidden            ( $obHdnCodOrgao );
$obFormulario->addHidden            ( $obHdnCodOrganograma );

$obFormulario->addTitulo            ( "Dados do órgão" );
$obFormulario->addComponente        ( $obLblCodOrgao );
$obFormulario->addComponente        ( $obLblDescricao );
$obFormulario->addComponente        ( $obLblOrganograma );
$obFormulario->addComponente        ( $obLblNivel );
$obFormulario->addComponente        ( $obLblOrgaoSuperior );
$obFormulario->addComponente        ( $obLblDataCriacao );
$obFormulario->addComponente        ( $obLblCalendario );
$obFormulario->addComponente        ( $obLblTipoNorma );
$obFormulario->addComponente        ( $obLblNorma );
$obFormulario->addComponente        ( $obTxtDataInativacao );

$obFormulario->addTitulo            ( "Responsável pelo órgão" );
$obFormulario->addComponente        ( $obLblCgmResponsavel );

$obFormulario->addTitulo            ( "Dados de CGM" );
$obFormulario->addComponente        ( $obLblCgmOrgao );

$obFormulario->defineBarra          ( array($obOk, $obVoltar) );
$obFormulario->show                 ();

include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
