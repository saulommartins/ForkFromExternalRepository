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
    * Classe de Visao do Iniciar Processo Fiscal
    * Data de Criação   : 28/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Visao

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/componentes/ITextBoxSelectDocumento.class.php';
require_once(CAM_GT_FIS_COMPONENTES."IFISTextBoxSelectDocumento.class.php");
require_once(CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php");
require_once(CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php");
require_once(CAM_GT_FIS_VISAO."VFISProcessoFiscal.class.php");
require_once(CAM_GT_FIS_NEGOCIO."RFISVinculo.class.php");

final class VFISIniciarProcessoFiscal
{
    private $controller;
    private $visaoProcessoFiscal;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->visaoProcessoFiscal = new VFISProcessoFiscal($this->controller);
    }

    public function verificaAtribuicaoFiscal($arParam)
    {
        return $this->visaoProcessoFiscal->verificaAtribuicaoFiscal($arParam);
    }

    public function montaListaDocumentosVinculados($arParam)
    {
        $obRFISVinculo = new RFISVinculo;
        $rsDocumentosVinculados = $obRFISVinculo->getDocumento($arParam['inCodAtividade']);
        $inCount = count($rsDocumentosVinculados->arElementos);

        //Sessão para os Documentos
        $this->limparSession("");

        //echo $inCount;

        if ($inCount > 0) {
            for ($i = 0; $i < $inCount; $i++) {
                $arDocumentosVinculados[$i]['codigo'] = $rsDocumentosVinculados->arElementos[$i]['cod_documento'];
                $arDocumentosVinculados[$i]['nome'] = $rsDocumentosVinculados->arElementos[$i]['nom_documento'];
                $obHdn = $this->visaoProcessoFiscal->GeraHidden("documento",$rsDocumentosVinculados->arElementos[$i]['cod_documento']);
                $arDocumentosVinculados[$i]['hidden'] = $obHdn;

                $j = $i + 1;

                //Sessão para os Documentos
                Sessao::write('arDocumentos', $arDocumentosVinculados);
                if (!$rsDocumentosVinculados->arElementos[$j]) {
                    $opt = array(
                            "cabecalho" => "Lista de Documentos",
                            "span"      => "spnDocumentos",
                               "desc"      => "txtDocumentos",
                            "alvo"      => "cmbDocumentos",
                            "codigo"    => $arDocumentosVinculados[$i]['codigo'],
                            "container" => "arDocumentos"
                           );

                    $lista = $this->visaoProcessoFiscal->montaLista($arDocumentosVinculados, true, $opt);
                    $result = $lista;

                    return $result;
                }
            }
        }
    }

    public function incluirDocumento($arParam)
    {
        $opt = array(
            "cabecalho" => "Lista de Documentos",
            "span"      => "spnDocumentos",
            "desc"      => "txtDocumentos",
            "alvo"      => "cmbDocumentos",
            "codigo"    => $arParam["inDocumento"],
            "container" => "arDocumentos"
            );

        $arValores = Sessao::read($opt['container']);

        if ($this->visaoProcessoFiscal->PodeIncluirItemLista($opt)) {

            $this->controller->setCriterio(" documento.cod_documento = ".$opt["codigo"]);
            $obDocumento = $this->controller->getDocumentoEmpresa();

            if ($obDocumento) {
                $k = count($arValores);
                $arValores[$k]['codigo'] = $obDocumento->arElementos[0]['cod_documento'];
                $arValores[$k]['nome'] = $obDocumento->arElementos[0]['nom_documento'];
                $Hdn =  $this->visaoProcessoFiscal->GeraHidden("documento",$obDocumento->arElementos[0]['cod_documento']);
                $arValores[$k]['hidden'] = $Hdn;
                Sessao::write($opt['container'], $arValores);

                $lista = $this->visaoProcessoFiscal->montaLista($arValores, true, $opt);
                $result = $lista;

            } else {
                $stMensagem = "@Documento inválido (".$arParam["inDocumento"].")   ";
                $js.= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
                $result = $js;
            }

        } else {
            $stMensagem = "@Documento já informado.(".$arParam["inDocumento"].")   ";
            $js.= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."'); \n";
            $result = $js;
        }

        $result.= " setTimeout('limparDocumento()',500);\n";

        return $result;
    }

    public function ExcluirItemLista($arParam)
    {
        $return = $this->visaoProcessoFiscal->ExcluirItemLista($arParam);

        return $return;
    }

    public function montaForm($arParam)
    {
        //Define o formulario
        $obFormulario = new Formulario;
        switch ($arParam['cmbTipoFiscalizacao']) {
            case '1':
                //Inscricao Economica
                $obIPopUpEmpresa = new IPopUpEmpresa;
                $obIPopUpEmpresa->obInnerEmpresa->setNull(true);
                $obIPopUpEmpresa->obInnerEmpresa->setTitle("Informe o código da Inscrição Econômica.");
                $obIPopUpEmpresa->geraFormulario($obFormulario);

                $obFormulario->montaInnerHTML();
                $stJs = "$('spnForm').innerHTML = '".$obFormulario->getHTML()."';";

            break;

            case '2':
                //Inscrição Imobiliária
                $obIPopUpImovel = new IPopUpImovel;
                $obIPopUpImovel->obInnerImovel->setNull (true);
                $obIPopUpImovel->obInnerImovel->setTitle("Informe o código da Inscrição Imobiliária.");
                $obIPopUpImovel->geraFormulario($obFormulario);
                $obFormulario->montaInnerHTML();
                $stJs = "$('spnForm').innerHTML = '".$obFormulario->getHTML()."';";
            break;

            default:
                $stJs = "$('spnForm').innerHTML = '';";
            break;
        }

        return $stJs;
    }

    public function iniciarProcessoFiscalEconomica($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getProcessoFiscalEconomica();
    }

    public function iniciarProcessoFiscalObra($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getProcessoFiscalObra();
    }

    public function iniciarProcessoFiscalDocumento($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getProcessoFiscalCreditoGrupo();
    }

    public function recuperarListaProcessoFiscalEconomica($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaProcessoFiscalEconomica();
    }

    public function recuperarListaProcessoFiscalObra($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaProcessoFiscalObra();
    }

    public function recuperarListaProcessoFiscaEconomicaObra($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaProcessoFiscalEconomicaObra();
    }

    public function recuperarListaProcessoFiscalDocumentosVinculados($arParam)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getDocumentoVinculado();
    }

    public function iniciarProcessoFiscal($arParam)
    {
        return $this->controller->iniciarProcesso($arParam);
    }

    public function limparSession($arParam)
    {
        Sessao::write('arDocumentos', array());
    }

    public function filtrosProcessoFiscal($arParam)
    {
        if ($arParam['inTipoFiscalizacao'] != "") {
                $stFiltro[] = " pf.cod_tipo = " .$arParam['inTipoFiscalizacao']. "\n";
        }

        if ($arParam['inCodProcesso'] != "") {
            $stFiltro[] = " pf.cod_processo = " .$arParam['inCodProcesso']. "\n";
        }

        if ($arParam['inInscricaoEconomica'] != "") {
            $stFiltro[] = " pfe.inscricao_economica = " .$arParam['inInscricaoEconomica']. "\n";
        }

        if ($arParam['inCodImovel'] != "") {
            $stFiltro[] = " pfo.inscricao_municipal = " .$arParam['inCodImovel']. "\n";
        }

        if ($arParam['numcgm'] != "") {
            $stFiltro[] = " fc.numcgm = " .$arParam['numcgm']. "\n";
        }

        if ($arParam['boInicio']) {
            $stFiltro[] = " fif.cod_processo is null \n";
            $stFiltro[] = " pfc.cod_processo is null \n";
            $stFiltro[] = " ftf.cod_processo is null \n";
        }

        $return = " ";

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }
        }

        return $return;
    }
}
