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

 * $Id: FMManterOrgao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

 * Casos de uso: uc-01.05.02
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

$stPrograma = "ManterOrgao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;

$arFiltro = Sessao::read('filtro');

if (is_array($arFiltro)) {

    if (!empty($arFiltro['inCodOrganograma'])) {
        $stFiltro = " AND cod_organograma = ".$arFiltro['inCodOrganograma'];
    }

    Sessao::write('filtro', $arFiltro);
}

$rsOrganograma   = new RecordSet;
$rsNivel         = new RecordSet;
$rsOrgaoSuperior = new RecordSet;
$rsCalendario    = new RecordSet;
$rsTipoNorma     = new RecordSet;
$rsNorma         = new RecordSet;
$obRegra         = new ROrganogramaOrgao;

$stAcao = $request->get('stAcao');

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$stAcao."&".$stFiltro;

$stOrdem = " ORDER BY TO_DATE(implantacao::text, 'yyyy-mm-dd') DESC ";

$obRegra->obROrganograma->listar( $rsOrganograma, $stFiltro, $stOrdem );
$obRegra->obRCalendario->listar( $rsCalendario );
$obRegra->obRNorma->obRTipoNorma->listar( $rsTipoNorma );

if (empty($stAcao) || $stAcao == "incluir") {
    $stAcao = "incluir";

    # Recurso Variável Variáveis do PHP, cria a variável com o nome do campo
    # submetido e atribui o último valor a ela.
    foreach ($_REQUEST as $key => $value) {
        $$key = $value;
    }

} elseif ($stAcao) {
    $inCodOrgao =  $_REQUEST['inCodOrgao'];
    $obRegra->setCodOrgao                       ( $_REQUEST['inCodOrgao'] );
    $obRegra->obROrganograma->setCodOrganograma ( $_REQUEST['inCodOrganograma'] );
    $obErro =  $obRegra->consultar();
    $stSigla              = $obRegra->getSigla();
    $stDescricao          = $obRegra->getDescricao();
    $stDataCriacao        = $obRegra->getCriacao();
    $inNumCGMResponsavel  = $obRegra->obRCgmPF->getNumCGM();
    $inNumCGMOrgao        = $obRegra->obRCgmPJ->getNumCGM();
    $inCodNivel           = $obRegra->obRNivel->getCodNivel();
    $inCodCalendario      = $obRegra->obRCalendario->getCodCalendar();
    $inCodOrganograma     = $obRegra->obROrganograma->getCodOrganograma();
    $stDataImplantacao    = $obRegra->obROrganograma->getDtImplantacao();
    $inCodOrgaoSuperior   = $obRegra->getCodOrgaoSuperior();
    $inCodNorma           = $obRegra->obRNorma->getCodNorma();
    $inCodTipoNorma       = $obRegra->obRNorma->obRTipoNorma->getCodTipoNorma();
    $stDescOrgaoSuperior  = $obRegra->obROrganograma->setCodOrganograma($_REQUEST['inCodOrganograma']);

    $obRegra->obROrganograma->obRNivel->setCodNivel( $inCodNivel );
    $obRegra->obROrganograma->consultarNivel();
    $stDescNivel = $obRegra->obROrganograma->obRNivel->getDescricao();

    $obRegra->obROrganograma->setCodOrganograma( $inCodOrganograma );
    $obRegra->obRNivel->setCodNivel( $inCodNivel );
    $obRegra->listarOrgaosSuperiores( $rsOrgaoSuperior );

    if ($rsOrgaoSuperior->getNumLinhas() > 0) {
        while (!$rsOrgaoSuperior->eof()) {
            $inCount++;
            $inId   = $rsOrgaoSuperior->getCampo("cod_orgao");
            $stDesc = $rsOrgaoSuperior->getCampo("descricao");
            if ($inCodOrgaoSuperior  == $inId) {
                $stOrgaoSuperior = $inId." - ".$stDesc;
            }
            $rsOrgaoSuperior->proximo();
        }
    }

    $stGet  = Sessao::getId()."&inNumCGMResponsavel=".$inNumCGMResponsavel."&inNumCGMOrgao=".$inNumCGMOrgao;
    $stGet .= "&inCodOrgaoSuperiorTxt=".$inCodOrgaoSuperior."&inCodNormaTxt=".$inCodNorma;
    $stGet .= "&inCodTipoNorma=".$inCodTipoNorma."&inCodOrganograma=".$inCodOrganograma;
    $stGet .= "&inCodNivelTxt=".$inCodNivel."&inCodTipoNormaAux=".$inCodTipoNorma;

$stHeredoc = <<<HEREDOC
    <script type='text/javascript'>
        <!--
            ajaxJavaScript('$pgOcul?$stGet','preencheInner');
        //-->
    </script>
HEREDOC;
    echo $stHeredoc;
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obHdnCodOrgao = new Hidden;
$obHdnCodOrgao->setName  ( "inCodOrgao" );
$obHdnCodOrgao->setValue ( $inCodOrgao );

/////////////////////////////////////////////////////////
//    Dados do Órgão
/////////////////////////////////////////////////////////

# Label com o ID do órgão.
$obLblCodOrgao = new Label;
$obLblCodOrgao->setId     ('stCodOrgao');
$obLblCodOrgao->setName   ('stCodOrgao');
$obLblCodOrgao->setRotulo ('Código');
$obLblCodOrgao->setValue  ($inCodOrgao);

// Sigla do Órgão
$obTxtSigla = new TextBox;
$obTxtSigla->setRotulo      ( "Sigla" );
$obTxtSigla->setTitle       ( "Sigla do órgão");
$obTxtSigla->setName        ( "stSigla" );
$obTxtSigla->setValue       ( $stSigla  );
$obTxtSigla->setSize        ( 15 );
$obTxtSigla->setNull        ( true );
$obTxtSigla->setMaxLength   ( 10 );

// Descrição
$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo        ( "Descrição" );
$obTxtDescricao->setTitle         ( "Descrição do órgão");
$obTxtDescricao->setName          ( "stDescricao" );
$obTxtDescricao->setValue         ( $stDescricao  );
$obTxtDescricao->setSize          ( 40 );
$obTxtDescricao->setNull          ( false );
$obTxtDescricao->setMaxLength     ( 100 );

$obHdnDescricao = new Hidden;
$obHdnDescricao->setName  ( "stHdnDescricao" );
$obHdnDescricao->setValue ( $stDescricao  );

//Organograma
//    Inclusão
$obTxtOrganograma = new TextBox;
$obTxtOrganograma->setRotulo        ( "Organograma" );
$obTxtOrganograma->setTitle         ( "Seleção do organograma pela data de implantação");
$obTxtOrganograma->setName          ( "inCodOrganogramaTxt" );
$obTxtOrganograma->setValue         ( $inCodOrganograma     );
$obTxtOrganograma->setSize          ( 5 );
$obTxtOrganograma->setMaxLength     ( 5 );
$obTxtOrganograma->setInteiro       ( true  );
$obTxtOrganograma->setNull          ( false );
$obTxtOrganograma->obEvento->setOnChange("montaParametrosGET('MontaNivel');");

$obCmbOrganograma = new Select;
$obCmbOrganograma->setRotulo        ( "Organograma" );
$obCmbOrganograma->setTitle         ( "Seleção do organograma pela data de implantação");
$obCmbOrganograma->setName          ( "inCodOrganograma" );
$obCmbOrganograma->setValue         ( $inCodOrganograma  );
$obCmbOrganograma->setStyle         ( "width: 200px");
$obCmbOrganograma->setCampoID       ( "cod_organograma" );
$obCmbOrganograma->setCampoDesc     ( "implantacao" );
$obCmbOrganograma->addOption        ( "", "Selecione" );
$obCmbOrganograma->setNull          ( false );
$obCmbOrganograma->preencheCombo    ( $rsOrganograma );
$obCmbOrganograma->obEvento->setOnChange("montaParametrosGET('MontaNivel');");

//    Alteração
$obLblOrganograma = new Label;
$obLblOrganograma->setRotulo( "Organograma" );
$obLblOrganograma->setTitle ( "Seleção do organograma pela data de implantação");
$obLblOrganograma->setValue ( "$inCodOrganograma - $stDataImplantacao" );

$obHdnCodOrganograma = new Hidden;
$obHdnCodOrganograma->setName("inCodOrganograma");
$obHdnCodOrganograma->setValue($inCodOrganograma);

$obHdnCodOrganogramaAux = new Hidden;
$obHdnCodOrganogramaAux->setName  ("inCodOrganogramaAux");
$obHdnCodOrganogramaAux->setValue ($inCodOrganograma);

//Nível
//    Inclusão
$obTxtNivel = new TextBox;
$obTxtNivel->setRotulo        ( "Nível" );
$obTxtNivel->setTitle         ( "Nível do órgão" );
$obTxtNivel->setName          ( "inCodNivelTxt" );
$obTxtNivel->setId            ( "inCodNivelTxt" );
$obTxtNivel->setValue         ( $inCodNivel     );
$obTxtNivel->setSize          ( 5 );
$obTxtNivel->setMaxLength     ( 5 );
$obTxtNivel->setInteiro       ( true  );
$obTxtNivel->setNull          ( false );
if ($stAcao == "incluir") {
    $obTxtNivel->obEvento->setOnChange("montaParametrosGET('MontaOrgaoSuperior');");
}

$obCmbNivel = new Select;
$obCmbNivel->setRotulo        ( "Nível" );
$obCmbNivel->setName          ( "inCodNivel" );
$obCmbNivel->setId            ( "inCodNivel" );
$obCmbNivel->setValue         ( $inCodNivel  );
$obCmbNivel->setStyle         ( "width: 200px");
$obCmbNivel->setCampoID       ( "cod_nivel" );
$obCmbNivel->setCampoDesc     ( "descricao" );
$obCmbNivel->addOption        ( "", "Selecione" );
$obCmbNivel->setNull          ( false );
$obCmbNivel->preencheCombo    ( $rsNivel );
if ($stAcao == "incluir") {
    $obCmbNivel->obEvento->setOnChange("montaParametrosGET('MontaOrgaoSuperior');");
}

$obHdnCodNivelAux = new Hidden;
$obHdnCodNivelAux->setId    ( "inCodNivelAux" );
$obHdnCodNivelAux->setName  ( "inCodNivelAux" );
$obHdnCodNivelAux->setValue ( $inCodNivel );

//    Alteração
$obLblNivel = new Label;
$obLblNivel->setRotulo( "Nível" );
$obLblNivel->setValue ( "$inCodNivel - $stDescNivel" );

$obHdnCodNivel = new Hidden;
$obHdnCodNivel->setName("inCodNivel");
$obHdnCodNivel->setValue($inCodNivel);

$obHdnCodNivelTxt = new Hidden;
$obHdnCodNivelTxt->setName("inCodNivelTxt");
$obHdnCodNivelTxt->setValue($inCodNivel);

if ($stAcao == "incluir") {
    //OrgaoSuperior
    $obTxtOrgaoSuperior = new TextBox;
    $obTxtOrgaoSuperior->setRotulo        ( "*Órgão Superior" );
    $obTxtOrgaoSuperior->setTitle         ( "Órgão hierarquicamente superior");
    $obTxtOrgaoSuperior->setName          ( "inCodOrgaoSuperiorTxt" );
    $obTxtOrgaoSuperior->setId            ( "inCodOrgaoSuperiorTxt" );
    $obTxtOrgaoSuperior->setValue         ( $inCodOrgaoSuperior     );
    $obTxtOrgaoSuperior->setSize          ( 5 );
    $obTxtOrgaoSuperior->setMaxLength     ( 5 );
    $obTxtOrgaoSuperior->setInteiro       ( true  );

    $obCmbOrgaoSuperior = new Select;
    $obCmbOrgaoSuperior->setRotulo        ( "*Órgão Superior" );
    $obCmbOrgaoSuperior->setId            ( "inCodOrgaoSuperior" );
    $obCmbOrgaoSuperior->setName          ( "inCodOrgaoSuperior" );
    $obCmbOrgaoSuperior->setValue         ( $inCodOrgaoSuperior  );
    $obCmbOrgaoSuperior->setStyle         ( "width: 200px");
    $obCmbOrgaoSuperior->setCampoID       ( "cod_orgao" );
    $obCmbOrgaoSuperior->setCampoDesc     ( "descricao" );
    $obCmbOrgaoSuperior->addOption        ( "", "Selecione" );
} else {
    //OrgaoSuperior
    $obLblOrgaoSuperior = new Label;
    $obLblOrgaoSuperior->setRotulo  ( "Orgão Superior" );
    $obLblOrgaoSuperior->setId      ( 'stOrgaoSuperior');
    $obLblOrgaoSuperior->setValue   ( $stOrgaoSuperior );
}

$obHdnOrgaoSuperior = new HiddenEval;
$obHdnOrgaoSuperior->setName( "stOrgaoSuperior" );

//DataCriacao
$obTxtDataCriacao = new Data;
$obTxtDataCriacao->setRotulo        ( "Data de Criação" );
$obTxtDataCriacao->setTitle         ( "Data de criação do órgão" );
$obTxtDataCriacao->setName          ( "stDataCriacao" );
$obTxtDataCriacao->setValue         ( $stDataCriacao  );
$obTxtDataCriacao->setNull          ( false );

//Calendario
$obTxtCalendario = new TextBox;
$obTxtCalendario->setRotulo        ( "Calendário" );
$obTxtCalendario->setTitle         ( "Calendário vinculado ao órgão");
$obTxtCalendario->setName          ( "inCodCalendarioTxt" );
$obTxtCalendario->setValue         ( $inCodCalendario     );
$obTxtCalendario->setSize          ( 5 );
$obTxtCalendario->setMaxLength     ( 5 );
$obTxtCalendario->setInteiro       ( true  );
$obTxtCalendario->setNull          ( false );

$obCmbCalendario = new Select;
$obCmbCalendario->setRotulo        ( "Calendário" );
$obCmbCalendario->setName          ( "inCodCalendario" );
$obCmbCalendario->setValue         ( $inCodCalendario  );
$obCmbCalendario->setStyle         ( "width: 200px");
$obCmbCalendario->setCampoID       ( "cod_calendar" );
$obCmbCalendario->setCampoDesc     ( "descricao" );
$obCmbCalendario->addOption        ( "", "Selecione" );
$obCmbCalendario->setNull          ( false );
$obCmbCalendario->preencheCombo    ( $rsCalendario );

//TipoNorma
$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo        ( "Tipo Norma" );
$obTxtTipoNorma->setTitle         ( "Tipo de norma vinculada ao órgão" );
$obTxtTipoNorma->setName          ( "inCodTipoNormaTxt" );
$obTxtTipoNorma->setValue         ( $inCodTipoNorma     );
$obTxtTipoNorma->setSize          ( 5 );
$obTxtTipoNorma->setMaxLength     ( 5 );
$obTxtTipoNorma->setInteiro       ( true  );
$obTxtTipoNorma->setNull          ( false );
$obTxtTipoNorma->obEvento->setOnChange("montaParametrosGET('MontaNorma');");

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo        ( "Tipo Norma" );
$obCmbTipoNorma->setName          ( "inCodTipoNorma" );
$obCmbTipoNorma->setValue         ( $inCodTipoNorma );
$obCmbTipoNorma->setStyle         ( "width: 200px");
$obCmbTipoNorma->setCampoID       ( "[cod_tipo_norma]" );
$obCmbTipoNorma->setCampoDesc     ( "nom_tipo_norma" );
$obCmbTipoNorma->addOption        ( "", "Selecione" );
$obCmbTipoNorma->setNull          ( false );
$obCmbTipoNorma->preencheCombo    ( $rsTipoNorma );
$obCmbTipoNorma->obEvento->setOnChange("montaParametrosGET('MontaNorma');");

$obHdbTipoNormaAux = new Hidden;
$obHdbTipoNormaAux->setId    ('inCodTipoNormaAux');
$obHdbTipoNormaAux->setName  ('inCodTipoNormaAux');
$obHdbTipoNormaAux->setValue ($inCodTipoNorma);

//Norma
$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo        ( "Norma" );
$obTxtNorma->setTitle         ( "Norma vinculada ao órgão" );
$obTxtNorma->setId            ( "inCodNormaTxt" );
$obTxtNorma->setName          ( "inCodNormaTxt" );
$obTxtNorma->setValue         ( $inCodNorma     );
$obTxtNorma->setSize          ( 5 );
$obTxtNorma->setMaxLength     ( 5 );
$obTxtNorma->setInteiro       ( true  );
$obTxtNorma->setNull          ( false );

$obCmbNorma = new Select;
$obCmbNorma->setRotulo        ( "Norma" );
$obCmbNorma->setName          ( "inCodNorma" );
$obCmbNorma->setValue         ( $inCodNorma );
$obCmbNorma->setStyle         ( "width: 300px");
$obCmbNorma->setCampoID       ( "cod_norma" );
$obCmbNorma->setCampoDesc     ( "nom_norma" );
$obCmbNorma->addOption        ( "", "Selecione" );
$obCmbNorma->setNull          ( false );
$obCmbNorma->preencheCombo    ( $rsNorma );

/////////////////////////////////////////////////////////
//    Reponsável pelo Órgão
/////////////////////////////////////////////////////////

//CGM do responsável
$obBscCgmResponsavel = new BuscaInner;
$obBscCgmResponsavel->setRotulo( "Cgm do Responsável" );
$obBscCgmResponsavel->setTitle( "Informe o código do CGM" );
$obBscCgmResponsavel->setNull( false );
$obBscCgmResponsavel->setId( "inNomCGMResponsavel" );
$obBscCgmResponsavel->obCampoCod->setName("inNumCGMResponsavel");
$obBscCgmResponsavel->obCampoCod->setValue( $inNumCGMResponsavel );
$obBscCgmResponsavel->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumCGMResponsavel='+this.value,'buscaCGMResponsavel');");
$obBscCgmResponsavel->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMResponsavel','inNomCGMResponsavel','fisica','".Sessao::getId()."&stCtrl=manterOrgao','800','550');" );

//Telefone Comercial
$obLblTelefoneComercial = new Label;
$obLblTelefoneComercial->setRotulo        ( "Telefone Comercial"  );
$obLblTelefoneComercial->setId            ( "inTelefoneComercial" );
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
$obLblEmailResponsavel->setId            ( "stEmailResponsavel" );
$obLblEmailResponsavel->setValue         ( $stEmailResponsavel  );

/////////////////////////////////////////////////////////
//    Dados de CGM
/////////////////////////////////////////////////////////

//CGM do Órgão
$obBscCgmOrgao = new BuscaInner;
$obBscCgmOrgao->setRotulo( "Cgm do Órgão" );
$obBscCgmOrgao->setTitle( "Informe o código do CGM" );
$obBscCgmOrgao->setId( "inNomCGMOrgao" );
$obBscCgmOrgao->obCampoCod->setName("inNumCGMOrgao");
$obBscCgmOrgao->obCampoCod->setValue( $inNumCGMOrgao );
$obBscCgmOrgao->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumCGMOrgao='+this.value,'buscaCGMOrgao');");
$obBscCgmOrgao->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMOrgao','inNomCGMOrgao','juridica','".Sessao::getId()."','800','550');" );

//Telefone
$obLblTelefone = new Label;
$obLblTelefone->setRotulo        ( "Telefone" );
$obLblTelefone->setId            ( "inTelefone" );
$obLblTelefone->setValue         ( $inTelefone  );

//Ramal
$obLblRamal = new Label;
$obLblRamal->setRotulo        ( "Ramal" );
$obLblRamal->setId            ( "inRamal" );
$obLblRamal->setValue         ( $inRamal  );

//Endereço
$obLblEndereco = new Label;
$obLblEndereco->setRotulo        ( "Endereço"  );
$obLblEndereco->setId            ( "stEndereco" );
$obLblEndereco->setValue         ( $stEndereco  );

//Numero
$obLblNumero = new Label;
$obLblNumero->setRotulo        ( "Número"  );
$obLblNumero->setId            ( "inNumero" );
$obLblNumero->setValue         ( $inNumero  );

//Email
$obLblEmailOrgao = new Label;
$obLblEmailOrgao->setRotulo        ( "Email "   );
$obLblEmailOrgao->setId            ( "stEmailOrgao" );
$obLblEmailOrgao->setValue         ( $stEmailOrgao  );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( 'UC-01.05.02' );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnOrgaoSuperior , true);

if ($stAcao == "incluir") {
    $obFormulario->addHidden ( $obHdnCodOrganogramaAux );
    $obFormulario->addHidden ( $obHdnCodNivelAux );
} elseif ($stAcao == 'alterar') {
    $obFormulario->addHidden ( $obHdnCodOrgao );
    $obFormulario->addHidden ( $obHdnCodOrganograma );
    $obFormulario->addHidden ( $obHdnCodNivel );
    $obFormulario->addHidden ( $obHdnCodNivelTxt );
}

$obFormulario->addHidden ( $obHdbTipoNormaAux );

$obFormulario->addTitulo ( "Dados do Órgão" );

if ($stAcao != 'incluir') {
    $obFormulario->addComponente ( $obLblCodOrgao );
}

$obFormulario->addComponente ( $obTxtSigla );

$obFormulario->addComponente ( $obTxtDescricao );
$obFormulario->addHidden     ( $obHdnDescricao );

if ($stAcao == 'incluir') {
    $obFormulario->addComponenteComposto( $obTxtOrganograma , $obCmbOrganograma );
    $obFormulario->addComponenteComposto( $obTxtNivel , $obCmbNivel );
    $obFormulario->addComponenteComposto( $obTxtOrgaoSuperior , $obCmbOrgaoSuperior );
} else {
    # $obFormulario->addComponente        ( $obLblDescricao );
    $obFormulario->addComponente        ( $obLblOrganograma );
    $obFormulario->addComponente        ( $obLblNivel );
    $obFormulario->addComponente        ( $obLblOrgaoSuperior );
}

$obFormulario->addComponente        ( $obTxtDataCriacao );
$obFormulario->addComponenteComposto( $obTxtCalendario , $obCmbCalendario );
$obFormulario->addComponenteComposto( $obTxtTipoNorma , $obCmbTipoNorma );
$obFormulario->addComponenteComposto( $obTxtNorma , $obCmbNorma );

$obFormulario->addTitulo            ( "Responsável pelo Órgão" );
$obFormulario->addComponente        ( $obBscCgmResponsavel );
$obFormulario->addComponente        ( $obLblTelefoneComercial );
$obFormulario->addComponente        ( $obLblRamalComercial );
$obFormulario->addComponente        ( $obLblTelefoneCelular );
$obFormulario->addComponente        ( $obLblEmailResponsavel );

$obFormulario->addTitulo            ( "Dados de CGM" );
$obFormulario->addComponente        ( $obBscCgmOrgao );
$obFormulario->addComponente        ( $obLblTelefone );
$obFormulario->addComponente        ( $obLblRamal );
$obFormulario->addComponente        ( $obLblEndereco );
$obFormulario->addComponente        ( $obLblNumero );
$obFormulario->addComponente        ( $obLblEmailOrgao );

if ( $stAcao == "incluir" )
    $obFormulario->OK();
else
    $obFormulario->Cancelar($stLocation);

$obFormulario->show();

if ($inCodNorma) {
   include_once $pgOcul;
}

# Recurso para preencher os campos com os dados do último órgão cadastrado.

if ($stAcao == "incluir") {

    # Busca os níveis relacionados ao organograma.
    if (isset($inCodOrganograma)) {
        $jsOnLoad .= "montaParametrosGET('MontaNivel', 'inCodOrganograma, inCodOrganogramaAux, inCodNivelTxt'); \n";
    }

    # Busca o órgão superior.
    if (isset($inCodNivel)) {
        $jsOnLoad .= "montaParametrosGET('MontaOrgaoSuperior'); \n";
    }

    # Busca o norma relacionada ao tipo da norma.
    if (isset($inCodTipoNorma)) {
        $jsOnLoad .= "montaParametrosGET('MontaNorma'); \n";
    }

    # Busca o CGM responsável pelo órgão.
    if (!empty($inNumCGMResponsavel)) {
        $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumCGMResponsavel=".$inNumCGMResponsavel."','buscaCGMResponsavel'); \n";
    }
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
