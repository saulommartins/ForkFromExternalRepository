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
 * Página de processamento oculto para o cadastro de construção
 * Data de Criação   : 21/03/2005

 * @author Analista: Ricardo Lopes de Alencar
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Desenvolvedor: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezzio Paulino

 * @ignore

 * $Id: OCManterConstrucao.php 63161 2015-07-30 19:45:43Z arthur $

 * Casos de uso: uc-05.01.12
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php";
include_once CAM_GA_PROT_NEGOCIO."RProcesso.class.php";

$stJs = "";
switch ($request->get("stCtrl")) {
    case "buscaCondominio":

        $obRCIMCondominio  = new RCIMCondominio;
        if ($_POST['inCodigoCondominio'] != '') {
            $obRCIMCondominio->setCodigoCondominio( $_POST['inCodigoCondominio']  );
            $obErro = $obRCIMCondominio->consultarCondominio( $rsCondominio );
            if ( $obErro->ocorreu() or $rsCondominio->eof() ) {
                $stJs .= 'f.inCodigoCondominio.value = "";';
                $stJs .= 'f.inCodigoCondominio.focus();';
                $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Condomínio não encontrado. (".$_POST["inCodigoCondominio"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs  = 'd.getElementById("campoInner").innerHTML = "'.$rsCondominio->getCampo('nom_condominio').'";';
            }
        }
    break;

    case "buscaProcesso":
        $obRProcesso  = new RProcesso;
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;

    case "habilitaSpnImovelCond":
        $obFormulario = new Formulario;
        if ($_REQUEST["boVinculoConstrucao"] == "Imóvel") {
            $obBscInscricaoMunicipal = new BuscaInner;
            $obBscInscricaoMunicipal->setRotulo           ( "Inscrição Imobiliária"     );
            $obBscInscricaoMunicipal->setTitle            ( "Inscrição imobiliária com a qual a edificação está vinculada" );
            $obBscInscricaoMunicipal->obCampoCod->setName ( "inNumeroInscricao"         );
            $obBscInscricaoMunicipal->obCampoCod->setId   ( "inNumeroInscricao"         );
            $obBscInscricaoMunicipal->setFuncaoBusca      ( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumeroInscricao','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');");
            $obFormulario->addComponente                  ( $obBscInscricaoMunicipal    );
        } elseif ($_REQUEST["boVinculoConstrucao"] == "Condomínio") {
            $obBscCondominio = new BuscaInner;
            $obBscCondominio->setRotulo              ( "Condomínio"                           );
            $obBscCondominio->setTitle               ( "Condomínio com o qual a construção está vinculada" );
            $obBscCondominio->setNull                ( true                                   );
            $obBscCondominio->setId                  ( "campoInner"                           );
            $obBscCondominio->obCampoCod->setName    ( "inCodigoCondominio"                   );
            $obBscCondominio->obCampoCod->setId      ( "inCodigoCondominio"                   );
            $obBscCondominio->obCampoCod->setValue   ( $_REQUEST["inCodigoCondominio"]        );
            $obBscCondominio->obCampoCod->obEvento->setOnChange("buscaDado('buscaCondominio');" );
            $obBscCondominio->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."condominio/FLProcurarCondominio.php','frm','inCodigoCondominio','campoInner','','".Sessao::getId()."','800','550')" );
            $obFormulario->addComponente           ( $obBscCondominio                         );
            }
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stJs .= "d.getElementById('spnImovelCond').innerHTML = '".$stHtml."';\n";
    break;

    case "visualizarProcesso":
        $obRCIMConstrucao = new RCIMConstrucaoOutros;

        $arChaveAtributoConstrucao =  array( "cod_construcao" => $request->get('cod_construcao'),"timestamp" => $request->get('timestamp'), "cod_processo" => $request->get('cod_processo') );
        $obRCIMConstrucao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucao );
        $obRCIMConstrucao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosConstrucao );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo" );
        $obLblProcesso->setValue     ( str_pad($request->get('cod_processo'),5,"0",STR_PAD_LEFT) . "/" . $request->get('ano_exercicio')  );

        $obMontaAtributosConstrucao = new MontaAtributos;
        $obMontaAtributosConstrucao->setTitulo     ( "Atributos"        );
        $obMontaAtributosConstrucao->setName       ( "Atributo_"  );
        $obMontaAtributosConstrucao->setLabel       ( true  );
        $obMontaAtributosConstrucao->setRecordSet  ( $rsAtributosConstrucao );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obMontaAtributosConstrucao->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcesso').innerHTML = '".$stHtml."';";
    break;
}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

?>
