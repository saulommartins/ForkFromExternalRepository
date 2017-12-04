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
* Página de Oculto
* Data de criação : 16/12/2004

* @author Analista: Leandro Oliveira
* @author Programador: Rafael Almeida

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                                 );

function buscaCGM()
{
    $obRPessoalServidor = new RPessoalServidor;
    $rsContrato = new Recordset;
    $inNumCGM = $_POST["inNumCGM"];
    if ($inNumCGM  != "") {
        $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $inNumCGM  );
        $obRPessoalServidor->obRCGMPessoaFisica->consultarCGM( $rsCGM );
        if ( $rsCGM->getCampo('nom_cgm') ) {
            $stJs .= "d.getElementById('inNomCGM').innerHTML             = '".  addslashes($rsCGM->getCampo('nom_cgm')) ."';\n";
            $stJs .= "f.inNomCGM.value = '" . addslashes($rsCGM->getCampo('nom_cgm')) ."';\n";
        } else {
            $stJs .= "d.getElementById('inNomCGM').innerHTML  = '&nbsp'  ;\n";
            $stJs .= "f.inNomCGM.value = '';\n";
            $stJs .= "f.inNumCGM.value = '';\n";
            $stJs .= "alertaAviso( 'Número do CGM (".$inNumCGM.") não encontrado no cadastro de Pessoa fisica','form','erro','".Sessao::getId()."');";

        }
    } else {
        $stJs .= "d.getElementById('inNomCGM').innerHTML  = '&nbsp'  ;\n";
        $stJs .= "f.inNomCGM.value = '';\n";
    }

    return $stJs;
}

function processarForm($boExecuta=false,$stAcao,$inAba=0)
{  
    gerarSpanPrevidencia(true);
    
    ####################################
    #        ABA IDENTIFICAÇÃO         #
    ####################################
    global $inCodUF,$inCodMunicipio;
    $stJs .= preencheMunicipioOrigem($inCodUF,$inCodMunicipio);

    ####################################
    #        ABA DOCUMENTAÇÃO          #
    ####################################
    if ($stAcao == "alterar" or $stAcao == "alterar_servidor") {
        $stJs .= listarAlterarCTPS();
    }

    ####################################
    #        ABA DEPENDENTE            #
    ####################################
    if ($stAcao == "alterar" or $stAcao == "alterar_servidor") {
        $stJs .= listarAlterarDependente();
    }
    ####################################
    #        ABA CONTRATO              #
    ####################################

    if ($stAcao == "alterar") {
        $stJs .= habilita();
        $stJs .= preencheSubDivisaoAlterar();
        $stJs .= preencheCargoAlterar();
        $stJs .= preencheEspecialidadeAlterar();
        $stJs .= preencheSubDivisaoFuncaoAlterar();
        $stJs .= preencheFuncaoAlterar();
        $stJs .= preencheEspecialidadeFuncaoAlterar();
        $stJs .= preencheProgressaoAlterar();
        $stJs .= preencheAgenciaBancaria();
        $stJs .= preencheAgenciaBancariaSalario();
        $stJs .= preenchePortariaNomeacao();
        $stJs .= preencheTurnos();
        $stJs .= buscaSindicato();
        $stJs .= buscaLocal();
        $stJs .= preencherSpanAposentadoria();
        $stJs .= preencherSpanCedencia();
        $stJs .= preencherSpanRescisao();
    }
    if ($inAba) {
        $stJs .= bloqueiaAbas($inAba);
    }
    $stJs .= 'LiberaFrames(true,false);';
    $stJs .= "d.getElementById('btnAlterarDependente').disabled = true; \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function bloqueiaAbas($inAba)
{
    switch ($inAba) {
        case 1:
            $stJs .= "document.links['id_layer_1'].href = \"javascript:HabilitaLayer('layer_1');\";\n";
            $stJs .= "document.links['id_layer_2'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_3'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_4'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_5'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_6'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "HabilitaLayer('layer_1');\n";
        break;
        case 2:
            $stJs .= "document.links['id_layer_1'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_2'].href = \"javascript:HabilitaLayer('layer_2');\";\n";
            $stJs .= "document.links['id_layer_3'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_4'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_5'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_6'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "HabilitaLayer('layer_2');\n";
        break;
        case 3:
            $stJs .= "document.links['id_layer_1'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_2'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_3'].href = \"javascript:HabilitaLayer('layer_3');\";\n";
            $stJs .= "document.links['id_layer_4'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_5'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_6'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "HabilitaLayer('layer_3');\n";
        break;
        case 4:
            $stJs .= "document.links['id_layer_1'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_2'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_3'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_4'].href = \"javascript:HabilitaLayer('layer_4');\";\n";
            $stJs .= "document.links['id_layer_5'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_6'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "HabilitaLayer('layer_4');\n";
        break;
        case 5:
            $stJs .= "document.links['id_layer_1'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_2'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_3'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_4'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_5'].href = \"javascript:HabilitaLayer('layer_5');\";\n";
            $stJs .= "document.links['id_layer_6'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "HabilitaLayer('layer_5');\n";
        break;
        case 6:
            $stJs .= "document.links['id_layer_1'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_2'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_3'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_4'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_5'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            $stJs .= "document.links['id_layer_6'].href = \"javascript:HabilitaLayer('layer_6');\";\n";
            $stJs .= "HabilitaLayer('layer_6');\n";
        break;
    }

    return $stJs;
}

function exibeAviso()
{
    $stMensagem = "Usuário não possui permissão para acessar esta aba.";
    $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');";

    return $stJs;
}

switch ($_POST["stCtrl"]) {
    case "buscaCGM":
        $stJs .= buscaCGM();
    break;
    case "exibeAviso":
        $stJs .= exibeAviso();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
