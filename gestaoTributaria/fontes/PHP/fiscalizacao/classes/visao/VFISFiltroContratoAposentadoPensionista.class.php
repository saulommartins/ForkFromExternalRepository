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
    * Classe de Visao do Componente IFiltroContratoAposentadoPensionista.class.php
    * Data de Criação   : 29/12/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Visao
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");

final class VFISFiltroContratoAposentadoPensionista
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    private function recuperaNomeCampos(&$stCampoContrato, &$stCampoDigito)
    {
        $stCampoContrato = "inContrato";
        $stCampoDigito = "inDigitoVerificador";

        foreach ($_GET as $stCampo => $stValor) {
            if (substr($stCampo,0,10) == "inContrato") {

                $stCampoContrato = $stCampo;
                $stCampoDigito .= substr($stCampo,10,strlen($stCampo));
                break;
            }
        }
    }

    public function validaRegistroContrato()
    {
        $obRPessoalServidor = new RPessoalServidor;
        $obRPessoalServidor->addContratoServidor();
        $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
        $obRConfiguracaoPessoal->Consultar();
        $stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
        $arMascaraRegistro = explode("-",$stMascaraRegistro);
        $boMascaraRegistro = (count($arMascaraRegistro) >= 2) ? true : false;

        $this->recuperaNomeCampos($stCampoContrato, $stCampoDigito);

        $inRegistro = $_GET[$stCampoContrato];

        if ($inRegistro != "") {

            $obRPessoalServidor->roUltimoContratoServidor->setRegistro($inRegistro);

            $stFiltro = " pcscc.cod_contrato isnull AND pcp.cod_contrato  isnull AND pa.cod_contrato isnull AND pae.cod_contrato isnull AND pc.registro = ".$inRegistro." \n";
            $rsContrato = $this->recuperarContratoFiscais($stFiltro);

            if ($rsContrato->getNumLinhas() == 1) {

                $stFiltro = " cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                $rsRescisao = $this->recuperarTodosServidorCasoCausa($stFiltro);

                if ($rsRescisao->getNumLinhas() > 0) {

                    $stJs.= "f.".$stCampoContrato.".value = '';\n";
                    $stJs.= "d.getElementById('".$stCampoContrato."').focus();\n";
                    $stJs.= "alertaAviso('@Matrícula informada inválida para ser incluída como Fiscal(".$inRegistro.").','form','erro','".Sessao::getId()."');";
                    $stJs.= ($boMascaraRegistro) ? "f.".$stCampoDigito.".value = '';\n" : "";
                } else {

                    $stNomCGM = $rsContrato->getCampo('numcgm') ." - ". $rsContrato->getCampo('nom_cgm');
                    $stJs.= "d.getElementById('inNomCGM').innerHTML = '".addslashes($stNomCGM)."';\n";
                    $stJs.= "f.hdnCGM.value = '".addslashes($stNomCGM)."';\n";

                    if ($_GET['boFuncao']) {
                        $stJs.= $this->preencheInformacoesFuncao($_GET['inContrato']);
                    }
                }
            } else {

                $stJs.= "d.getElementById('inContrato').value = '';\n";
                $stJs.= "alertaAviso('@Matrícula informada inválida para ser incluída como Fiscal.(".$_GET['inContrato'].")','form','erro','".Sessao::getId()."');\n";
                $stJs.= "d.getElementById('inContrato').focus();\n";
                $stJs.= "d.getElementById('inNomCGM').innerHTML = '&nbsp;'; \n";
                if ($_GET['boFuncao']) {
                    $stJs.= "d.getElementById('stInformacoesFuncao').innerHTML = '&nbsp;'; \n";
                }
            }
        }

        return $stJs;
    }

    public function preencheInformacoesFuncao($inContrato)
    {
        $obRPessoalServidor = new RPessoalServidor;
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->listarFuncaoDoRegistro($rsFuncao,$inContrato,$boTransacao);
        $stInformacoesFuncao = $rsFuncao->getCampo('descricao') . " - " . $rsFuncao->getCampo('dt_posse');

        if ($inContrato != "" && $rsFuncao->getNumLinhas() > 0) {
            $stJs .= "d.getElementById('stInformacoesFuncao').innerHTML = '".$stInformacoesFuncao."';\n";
        } else {
            $stJs .= "d.getElementById('stInformacoesFuncao').innerHTML = '&nbsp;';\n";
        }

        return $stJs;
    }

    private function recuperarContratoFiscais($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaContratoFiscais();
    }

    private function recuperarTodosServidorCasoCausa($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getRecuperaContratoServidorCasoCausa();
    }
}
?>
