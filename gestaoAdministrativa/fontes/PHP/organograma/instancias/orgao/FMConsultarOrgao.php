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

 * $Id: FMConsultarOrgao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

 Casos de uso: uc-01.05.02

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

$stPrograma = "ConsultarOrgao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once($pgJs);

$arDados = Sessao::read('dados');

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

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

$rsOrganograma   = new RecordSet;
$rsNivel         = new RecordSet;
$rsOrgaoSuperior = new RecordSet;
$rsCalendario    = new RecordSet;
$rsTipoNorma     = new RecordSet;
$rsNorma         = new RecordSet;

$obRegra  = new ROrganogramaOrgao;

$stAcao = $request->get('stAcao');

$obRegra->setCodOrgao( $_REQUEST['inCodOrgao'] );
//$obRegra->obROrganograma->setCodOrganograma( $_REQUEST['inCodOrganograma'] );

$obRegra->consultar();

$stDescricao          = $obRegra->getDescricao();
$stDataCriacao        = $obRegra->getCriacao();
$inNumCGMResponsavel  = $obRegra->obRCgmPF->getNumCGM();
$inNumCGMOrgao        = $obRegra->obRCgmPJ->getNumCGM();
$inCodNivel           = $obRegra->obRNivel->getCodNivel();

$obRegra->obROrganograma->obRNivel->setCodNivel( $inCodNivel );

$obRegra->obROrganograma->setCodOrganograma ($_REQUEST["inCodOrganograma"]);
$obRegra->obROrganograma->consultarNivel();

$stDescNivel          = $obRegra->obROrganograma->obRNivel->getDescricao();
$inCodCalendario      = $obRegra->obRCalendario->getCodCalendar();
$stDescCalendario     = $obRegra->obRCalendario->getDescricao();
$inCodOrganograma     = $obRegra->obROrganograma->getCodOrganograma();
$stDataImplantacao    = $obRegra->obROrganograma->getDtImplantacao();
$inCodOrgaoSuperior   = $obRegra->getCodOrgaoSuperior();
$inCodNorma           = $obRegra->obRNorma->getCodNorma();
$stDescNorma          = $obRegra->obRNorma->getNomeNorma();
$inCodTipoNorma       = $obRegra->obRNorma->obRTipoNorma->getCodTipoNorma();
$stDescTipoNorma      = $obRegra->obRNorma->obRTipoNorma->getNomeTipoNorma();
$stDataInativacao     = $obRegra->getInativacao();

Sessao::write('obRegra', $obRegra);

?>
<script type="text/javascript">
<!--
ajaxJavaScript('<?=$pgOcul."?".Sessao::getId();?>','preencheInner');
//-->
</script>

<?php
//Caso exista órgão superior
if ($inCodOrgaoSuperior) {
    $obRegra->setCodOrgao( $inCodOrgaoSuperior );
    $obRegra->consultar();
    $stDescOrgaoSuperior = $obRegra->getDescricao();
}

$obRegra->obRCgmPF->setNumCGM( $inNumCGMResponsavel );
$obRegra->obRCgmPF->consultarCGM( $rsCGMPF );
$stNomCGMResponsavel = $rsCGMPF->getCampo('nom_cgm');

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
$obHdnValidaData->setValue( "if (document.frm.stDataInativacao.value<'$stDataCriacao') {erro = true; mensagem += '@Campo Data de inativação deve ser maior que a Data de criação!()';}" );

/////////////////////////////////////////////////////////
//    Dados do órgão
/////////////////////////////////////////////////////////
$obLblCodOrgao = new Label;
$obLblCodOrgao->setRotulo ( "Código" );
$obLblCodOrgao->setValue  ( $_REQUEST['inCodOrgao'] );

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
$obLblNivel->setRotulo        ( "Nível" );
$obLblNivel->setValue         ( "$inCodNivel - $stDescNivel");

//OrgaoSuperior
$obLblOrgaoSuperior = new Label;
$obLblOrgaoSuperior->setRotulo        ( "Órgao Superior" );
$obLblOrgaoSuperior->setValue         ( "$inCodOrgaoSuperior - $stDescOrgaoSuperior" );

//DataCriacao
$obLblDataCriacao = new Label;
$obLblDataCriacao->setRotulo        ( "Data de Criação" );
$obLblDataCriacao->setValue         ( $stDataCriacao    );

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
$obLblDataInativacao = new Label;
$obLblDataInativacao->setRotulo        ( "Data de Inativação" );
$obLblDataInativacao->setValue         ( $stDataInativacao );

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
$obLblTelefoneComercial->setId          ( "inTelefoneComercial" );
$obLblTelefoneComercial->setValue         ( $inTelefoneComercial  );

//Telefone Comercial Ramal
$obLblRamalComercial = new Label;
$obLblRamalComercial->setRotulo        ( "Ramal"  );
$obLblRamalComercial->setId            ( "inRamalComercial" );
$obLblRamalComercial->setValue         ( $inRamalComercial  );

//Telefone Celular
$obLblTelefoneCelular = new Label;
$obLblTelefoneCelular->setRotulo        ( "Telefone Celular"  );
$obLblTelefoneCelular->setId            ( "inTelefoneCelular" );
$obLblTelefoneCelular->setValue         ( $inTelefoneCelular  );

//Email
$obLblEmailResponsavel = new Label;
$obLblEmailResponsavel->setRotulo        ( "Email"   );
$obLblEmailResponsavel->setId          ( "stEmailResponsavel" );
$obLblEmailResponsavel->setValue         ( $stEmailResponsavel  );

/////////////////////////////////////////////////////////
//    Dados de CGM
/////////////////////////////////////////////////////////

//CGM do órgão
$obLblCgmOrgao = new Label;
$obLblCgmOrgao->setRotulo        ( "Cgm do Órgão" );
$obLblCgmOrgao->setId            ( "stCGMOrgao"   );

//Telefone
$obLblTelefone = new Label;
$obLblTelefone->setRotulo        ( "Telefone" );
$obLblTelefone->setValue         ( $inTelefone);
$obLblTelefone->setId            ( "inTelefone" );

//Ramal
$obLblRamal = new Label;
$obLblRamal->setRotulo        ( "Ramal" );
$obLblRamal->setId            ( "inRamal" );
$obLblRamal->setValue         ( $inRamal  );

//Endereço
$obLblEndereco = new Label;
$obLblEndereco->setRotulo        ( "Endereço"  );
$obLblEndereco->setValue         ( $stEndereco );
$obLblEndereco->setId            ( "stEndereco" );

//Numero
$obLblNumero = new Label;
$obLblNumero->setRotulo        ( "Número"  );
$obLblNumero->setValue         ( $inNumero  );
$obLblNumero->setId            ( "inNumero"  );

//Email
$obLblEmailOrgao = new Label;
$obLblEmailOrgao->setRotulo        ( "Email "   );
$obLblEmailOrgao->setValue         ( $stEmailOrgao  );
$obLblEmailOrgao->setId            ( "stEmailOrgao"  );

$obVoltar = new Button;
$obVoltar->setName  ( "Voltar" );
$obVoltar->setValue ( "Voltar" );
$obVoltar->setStyle ( "width: 80px" );
//$obVoltar->obEvento->setOnClick("VoltarTP();");
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
$obFormulario->addComponente        ( $obLblDataInativacao );

$obFormulario->addTitulo            ( "Responsável pelo órgão" );
$obFormulario->addComponente        ( $obLblCgmResponsavel );
$obFormulario->addComponente        ( $obLblTelefoneComercial );
$obFormulario->addComponente        ( $obLblRamalComercial );
$obFormulario->addComponente        ( $obLblTelefoneCelular );
$obFormulario->addComponente        ( $obLblEmailResponsavel );

$obFormulario->addTitulo            ( "Dados de CGM" );
$obFormulario->addComponente        ( $obLblCgmOrgao );
$obFormulario->addComponente        ( $obLblTelefone );
$obFormulario->addComponente        ( $obLblRamal );
$obFormulario->addComponente        ( $obLblEndereco );
$obFormulario->addComponente        ( $obLblNumero );
$obFormulario->addComponente        ( $obLblEmailOrgao );

$obFormulario->defineBarra          ( array($obVoltar) );
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
