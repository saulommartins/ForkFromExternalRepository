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
    * Classe de Visao do Prorrogar Recebimento de Documentos
    * Data de Criação   : 13/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Zainer Cruz dos Santos Silva

    * @package URBEM
    * @subpackage Visao

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/TextBox.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Select.class.php';
require_once( CAM_GA_ADM_COMPONENTES.'ITextBoxSelectDocumento.class.php' );
require_once( CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php" );
require_once( CAM_GT_FIS_MAPEAMENTO."TFISInicioFiscalizacao.class.php" );

final class VFISReceberDocumentos
{

    private $controller;
    private $visaoProcessoFiscal;
    private $visaoIniciarProcessoFiscal;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->visaoProcessoFiscal = new VFISProcessoFiscal($this->controller);
        $this->visaoIniciarProcessoFiscal = new VFISIniciarProcessoFiscal($this->controller);
    }

    public function receberDocumentos($cod_documento, $stAcao)
    {
        $opt = array(
                "cabecalho" => "Lista de Documentos",
                "span"      => "spanListaDocumentos",
                "desc"      => "txtDocumentos",
                "alvo"      => "cmbDocumentos",
                "codigo"    => "cod_documento",
                "container" => "arDocumentos"
            );
        $obDocumento =  $this->controller->buscaDocumentos( $cod_documento );
        $lista = $this->montaLista( $obDocumento,$stAcao,$opt );

        return $lista;
    }

    public function montaForm($param)
    {
        return $this->visaoIniciarProcessoFiscal->montaForm( $param );
    }

    public function recuperarListaInicioFiscalizacaoEconomica($stFiltro)
    {
        if ($stFiltro) {
            $rsFiscal = $this->controller->getFiscais();
            $stFiltro .= " AND fc.numcgm = ".Sessao::read('numCgm');
            if ($rsFiscal->getCampo('adm') == 't') {
                $arParam['boSituacao'] = true;
                $stNewFiltro = $this->filtrosDocumentos($arParam);
                if ($stNewFiltro) {
                    $stFiltro .= " AND ".$stNewFiltro;
                }
            }

            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoEconomica();
    }

    public function recuperarListaInicioFiscalizacaoObra($stFiltro)
    {
        if ($stFiltro) {
            $rsFiscal = $this->controller->getFiscais();
            $stFiltro .= " AND fc.numcgm = ".Sessao::read('numCgm');
            if ($rsFiscal->getCampo('adm') == 't') {
                $arParam['boSituacao'] = true;
                $stNewFiltro = $this->filtrosDocumentos($arParam);
                if ($stNewFiltro) {
                    $stFiltro .= " AND ".$stNewFiltro;
                }
            }

            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoObra();
    }

    public function recuperarListaInicioFiscalizacaoEconomicaObra($stFiltro)
    {
        if ($stFiltro) {
            $rsFiscal = $this->controller->getFiscais();
            $stFiltro .= " AND fc.numcgm = ".Sessao::read('numCgm');
            if ($rsFiscal->getCampo('adm') == 't') {
                $arParam['boSituacao'] = true;
                $stNewFiltro = $this->filtrosDocumentos($arParam);
                if ($stNewFiltro) {
                    $stFiltro .= " AND ".$stNewFiltro;
                }
            }

            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoEconomicaObra();
    }

    public function iniciarInicioFiscalizacaoEconomica($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getInicioFiscalizacaoEconomica();
    }

    public function iniciarInicioFiscalizacaoObra($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getInicioFiscalizacaoObra();
    }

    public function montaLista($arValores, $stAcao, $opt)
    {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche($arValores);
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo($opt["cabecalho"]);

        $obLista->setRecordSet($rsRecordSet);
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código");
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nome");
        $obLista->ultimoCabecalho->setWidth(80);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Situação");
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        ////dados

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo("cod_documento");
        $obLista->commitDado();

        $obLista->montaHTML();

        $html = $obLista->getHTML();
        $html = str_replace("\n", "", $html);
        $html = str_replace("  ", "", $html);
        $html = str_replace("'", "\\'", $html);

        return $html;
    } //  fim montalista

    public function receberDocumentosFiscal($param)
    {
        return $this->controller->receberDocumentos($param);
    }

    public function getListaDocumentos($inCodProcessoPar)
    {
        $listaDocumentos = $this->buscaDocumentos($inCodProcessoPar);
        $count = count($listaDocumentos->arElementos);

        for ($i = 0; $i < $count; $i++) {

            $inCodDocumento = $listaDocumentos->arElementos[$i]['cod_documento'];
            $inCodProcesso = $listaDocumentos->arElementos[$i]['cod_processo'];
            $stSituacao = $listaDocumentos->arElementos[$i]['situacao'];

            foreach ($listaDocumentos->arElementos[$i] as $ch => $vlr) {
                if ($ch == 'cod_documento_entrega') {
                    $checkBox = new CheckBox();
                    if ($vlr != "") {
                        $checkBox->setName("checkDesabilitado[]");
                        $checkBox->setValue("");
                        $checkBox->setChecked(true);
                        $checkBox->setDisabled(true);
                    } else {
                        $checkBox->setName("checkAbilitado[{$inCodDocumento}]");
                        $checkBox->setValue("R");
                        $checkBox->setChecked(false);
                        $checkBox->setDisabled(false);
                    }
                    $checkBox->montaHtml();
                    $listaDocumentos->arElementos[$i]['check'] = $checkBox->getHtml();
                    unset($checkBox);
                } // fim foreach.
            } // fim if.
        }

        return $listaDocumentos;
    }

    public function buscaDocumentos($cod_processo)
    {
        return $this->controller->buscaDocumentos($cod_processo);
    }

    public function filtrosDocumentos($param)
    {
        if ($param['inTipoFiscalizacao'] != "") {
            $stFiltro[] = " pf.cod_tipo = " .$param['inTipoFiscalizacao']. "\n";
        }

        if ($param['inCodProcesso'] != "") {
            $stFiltro[] = " pf.cod_processo = " .$param['inCodProcesso']. "\n";
        }

        if ($param['inInscricaoEconomica'] != "") {
            $stFiltro[] = " pfe.inscricao_economica = " .$param['inInscricaoEconomica']. "\n";
        }

        if ($param['inCodImovel'] != "") {
            $stFiltro[] = " pfo.inscricao_municipal = " .$param['inCodImovel']. "\n";
        }

        if ($param['stCodDocumento'] != "") {
            $stFiltro[] = " fif.cod_documento = " .$param['stCodDocumento']. "\n";

        }

        if ($param['boSituacao']) {
            $stFiltro[] = " fifde.situacao is null \n";
            $stFiltro[] = " ftf.cod_processo is null \n";
        }

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
?>
