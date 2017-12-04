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
    * Oculto de processamento do componente IContratoDigitoVerificador
    * Data de Criação: 06/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                     );

function recuperaNomeCampos(&$stCampoContrato,&$stCampoDigito)
{
    $stCampoContrato = "inContrato";
    $stCampoDigito   = "inDigitoVerificador";
    foreach ($_GET as $stCampo=>$stValor) {
        if ( substr($stCampo,0,10) == "inContrato" ) {
            $stCampoContrato  = $stCampo;
            $stCampoDigito   .= substr($stCampo,10,strlen($stCampo));
            break;
        }
    }
}

function validaRegistroContrato()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
    $obRConfiguracaoPessoal->Consultar();
    $stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
    $arMascaraRegistro = explode("-",$stMascaraRegistro);
    $boMascaraRegistro = ( count($arMascaraRegistro) >= 2 ) ? true : false;
    recuperaNomeCampos($stCampoContrato,$stCampoDigito);
    $inRegistro = $_GET[$stCampoContrato];
    $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $inRegistro );
    if ($_GET['stAcao'] == 'incluir' or $_GET['stAcao'] == 'alterar_servidor') {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " WHERE registro = ".$inRegistro;
        $obTPessoalContrato->recuperaTodosComTipoContrato($rsContrato,$stFiltro);
        if ( $rsContrato->getNumLinhas() > 0 ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
            $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
            $stFiltro = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
            $obTPessoalContratoServidorCasoCausa->recuperaTodos($rsRescisao,$stFiltro);
            $stMensagem = "@O número de matrícula ".$inRegistro." já está sendo utilizado por um ".$rsContrato->getCampo("tipo");
            if ( $rsRescisao->getNumLinhas() > 0 ) {
                $stMensagem .= ", e está rescindida";
            }
            $stJs .= "f.".$stCampoContrato.".value = '';                      \n";
            $stJs .= "d.getElementById('".$stCampoContrato."').focus();                         \n";
            $stJs .= "alertaAviso('$stMensagem.','form','erro','".Sessao::getId()."');";
            $stJs .= ( $boMascaraRegistro ) ? "f.".$stCampoDigito.".value = '';\n" : "";
            $boMascaraRegistro = false;
        }
    }

    if ($boMascaraRegistro) {
        $stJs .= geraDigitoVerificador($obRPessoalServidor);
    }

    return $stJs;
}

function validaRegistroContrato2()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
    $obRConfiguracaoPessoal->Consultar();
    $stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
    $arMascaraRegistro = explode("-",$stMascaraRegistro);
    $boMascaraRegistro = ( count($arMascaraRegistro) >= 2 ) ? true : false;
    recuperaNomeCampos($stCampoContrato,$stCampoDigito);
    $inRegistro = $_GET[$stCampoContrato];
    if ($inRegistro!="") {
        $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $inRegistro );
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " WHERE registro = ".$inRegistro;
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        if ( $rsContrato->getNumLinhas() == 1 ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
            $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
            $stFiltro = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
            $obTPessoalContratoServidorCasoCausa->recuperaTodos($rsRescisao,$stFiltro);
            if ( $rsRescisao->getNumLinhas() > 0 ) {
                $stJs .= "f.".$stCampoContrato.".value = '';                      \n";
                $stJs .= "d.getElementById('".$stCampoContrato."').focus();                         \n";
                $stJs .= "alertaAviso('@ A Matrícula está rescindida (".$inRegistro.").','form','erro','".Sessao::getId()."');";
                $stJs .= ( $boMascaraRegistro ) ? "f.".$stCampoDigito.".value = '';\n" : "";
            }
        }
        if ($boMascaraRegistro) {
            $stJs .= geraDigitoVerificador($obRPessoalServidor);
        }
    }

    return $stJs;
}

function geraDigitoVerificador(&$obRPessoalServidor)
{
    $obRPessoalServidor->roUltimoContratoServidor->calculaDigito( $boTransacao );
    $inDigito = $obRPessoalServidor->roUltimoContratoServidor->getDigito();
    recuperaNomeCampos($stCampoContrato,$stCampoDigito);
    $stJs .= "f.".$stCampoDigito.".value = '$inDigito';    \n";

    return $stJs;
}

function verificaDigitoVerificador()
{
    recuperaNomeCampos($stCampoContrato,$stCampoDigito);
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $_GET[$stCampoContrato] );
    $obRPessoalServidor->roUltimoContratoServidor->calculaDigito( $boTransacao );
    if ( !($obRPessoalServidor->roUltimoContratoServidor->getDigito() == $_GET[$stCampoDigito]) ) {
        $stJs .= "f.".$stCampoDigito.".value = '';                      \n";
        $stJs .= "d.getElementById('".$stCampoDigito."').focus();       \n";
        $stJs .= "alertaAviso('@Campo Dígito Verificador inválido!(".$_GET[$stCampoDigito].").','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

//verifica o digito verificador pelo numero da matricula(contrato) que foi selecionado na janela de popUp
function verificaDigitoVerificador2()
{
    recuperaNomeCampos($stCampoContrato,$stCampoDigito);
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $_GET[$stCampoContrato] );
    $obRPessoalServidor->roUltimoContratoServidor->calculaDigito( $boTransacao );
    
    $inDigito = $obRPessoalServidor->roUltimoContratoServidor->getDigito();

    $stJs .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$stCampoDigito.".value = ".$inDigito."; \n";
    $stJs .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$stCampoDigito.".focus(); \n";
    $stJs .= " window.close(); ";

    return $stJs;
}

function limpaDigitoVerificador()
{
    recuperaNomeCampos($stCampoContrato,$stCampoDigito);
    $stJs .= "f.".$stCampoDigito.".value = '';\n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "limpaDigitoVerificador":
        $stJs = limpaDigitoVerificador();
        break;
    case "verificaDigitoVerificador":
        $stJs = verificaDigitoVerificador();
        break;
    case "validaRegistroContrato":
        $stJs = validaRegistroContrato();
        break;
    case "validaRegistroContrato2":
        $stJs = validaRegistroContrato2();
        break;
    case "verificaDigitoVerificador2":
        $stJs = verificaDigitoVerificador2();
        break;
}

if ($stJs) {
    echo $stJs;
}
?>
