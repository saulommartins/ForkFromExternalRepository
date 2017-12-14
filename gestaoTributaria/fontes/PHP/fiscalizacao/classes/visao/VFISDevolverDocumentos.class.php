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
    * @author Desenvolvedor : Aldo Jean Soares Silva

    * @package URBEM
    * @subpackage Visao

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/componentes/ITextBoxSelectDocumento.class.php';
require_once( CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php" );
require_once( CAM_GT_FIS_MAPEAMENTO."TFISInicioFiscalizacao.class.php" );

final class VFISDevolverDocumentos
{

    private $controller;
    private $visaoProcessoFiscal;
    private $visaoIniciarProcessoFiscal;
    private $obRsProcesso;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->visaoProcessoFiscal = new VFISProcessoFiscal($this->controller);
        $this->visaoIniciarProcessoFiscal = new VFISIniciarProcessoFiscal($this->controller);

    }

    public function montaForm($param)
    {
        return $this->visaoIniciarProcessoFiscal->montaForm( $param );
    }

    public function recuperarListaInicioFiscalizacaoEconomica($stFiltro)
    {
        if ($stFiltro) {
            $rsFiscal = $this->dadosFiscal();
            if ($rsFiscal->getCampo('adm') == 't') {
                $arParam['boSituacao'] = true;
                $stNewFiltro = $this->filtrosDocumentos($arParam);
            }

            $stFiltro .= " AND fc.numcgm = ".Sessao::read('numCgm');
            if ( $stNewFiltro )
                $stFiltro .= $stNewFiltro;

            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoEconomica();
    }

    public function recuperarListaInicioFiscalizacaoObra($stFiltro)
    {
        if ($stFiltro) {
            $rsFiscal = $this->dadosFiscal();
            if ($rsFiscal->getCampo('adm') == 't') {
                $arParam['boSituacao'] = true;
                $stNewFiltro = $this->filtrosDocumentos($arParam);
            }

            $stFiltro .= " AND fc.numcgm = ".Sessao::read('numCgm');
            if ( $stNewFiltro )
                $stFiltro .= $stNewFiltro;

            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoObra();
    }

    public function recuperarListaInicioFiscalizacaoEconomicaObra($stFiltro)
    {
        if ($stFiltro) {
            $rsFiscal = $this->dadosFiscal();
            if ($rsFiscal->getCampo('adm') == 't') {
                $arParam['boSituacao'] = true;
                $stNewFiltro = $this->filtrosDocumentos($arParam);
            }

            $stFiltro .= " AND fc.numcgm = ".Sessao::read('numCgm');
            if ( $stNewFiltro )
                $stFiltro .= $stNewFiltro;

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

    public function buscaDocumentos($inCodProcesso)
    {
        return $this->controller->buscaDocumentos($inCodProcesso);
    }

    // Valida cod_processo e executa buscaDocumentos em RFISDevolverDocumentos.
    public function buscaDocumentosD($inCodProcesso)
    {
        return $this->controller->buscaDocumentosD($inCodProcesso);
    }

    public function dadosFiscal()
    {
        $numcgm = Sessao::read('numCgm');

        return $this->controller->isFiscal($numcgm);
    }

    // Captura valor de inscrição de acordo com o Tipo de Fiscalização. Retorna um tipo inteiro.
    public function getValorTipoInscricao($arParam)
    {
        switch ($arParam['inTipoFiscalizacao']) {
            case 1:
                $arParam['inInscricaoEconomica'] = $arParam['inInscricao'];
                //Filtros da pesquisa.
                $where = $this->filtrosDocumentos( $arParam );
                $this->obRsProcesso = $this->iniciarInicioFiscalizacaoEconomica( $where );
                $inInscricaoEconomica = $this->obRsProcesso->arElementos[0]['inscricao_economica'];

                return $inInscricaoEconomica;

            break;

            case 2:
                $arParam['inCodImovel'] = $arParam['inInscricao'];
                //Filtros da pesquisa.
                $where = $this->filtrosDocumentos($arParam);
                $this->obRsProcesso = $this->iniciarInicioFiscalizacaoObra( $where );
                $inInscricaoMunicipal = $this->obRsProcesso->arElementos[0]['inscricao_municipal'];

                return $inInscricaoMunicipal;

            break;
        }

    }

    // Retorna array com resultado de consultas.
    public function getObRsProcesso()
    {
        return $this->obRsProcesso ;
    }

    // Adiciona Hidden ao formulário de acordo com o tipo de inscrição. Retorna um objeto Hidden.
    public function getHiddenInscricao($inTipoFiscalicacaoPar, $inInscricaoPar)
    {
        switch ($inTipoFiscalicacaoPar) {
            case "1"://Econômica
                $obHdnInInscricaoEconomica = new Hidden() ;
                $obHdnInInscricaoEconomica->setName("inIncricaoEconomica");
                $obHdnInInscricaoEconomica->setId("inIncricaoEconomica");
                $obHdnInInscricaoEconomica->setValue($inInscricaoPar);

                return $obHdnInInscricaoEconomica;

            break;

            case "2"://Municipal
                $obHdnInInscricaoImobiliaria = new Hidden();
                $obHdnInInscricaoImobiliaria->setName("inInscricaoImobiliaria");
                $obHdnInInscricaoImobiliaria->setId("inInscricaoImobiliaria");
                $obHdnInInscricaoImobiliaria->setValue($inInscricaoPar);

                return $obHdnInInscricaoImobiliaria;

            break ;
        }
    }

    // Adiciona o componente label ao formulário de acordo com o tipo de fiscalização. Retorna um objeto Label.
    public function getLabelinscricao($inTipoFiscalizacaoPar, $obInscricaoEconomica, $obInscricaoImobiliaria)
    {
        switch ($inTipoFiscalizacaoPar) {
            case "1":// Econômica

                return $obInscricaoEconomica;
            break;

            case "2":// Imobiliária

                return $obInscricaoImobiliaria;
            break;
        }

    }

    // Retorna lista de documentos por meio de buscaDocumentos e seta html de um chekbox para o campo situação.
    public function getListaDocumentos($inCodProcessoPar, $obChkStatusSituacao)
    {
        $listaDocumentos = $this->buscaDocumentos($inCodProcessoPar);
        $this->validaCheckBoxDevolucao($listaDocumentos, $inCodProcessoPar, $obChkStatusSituacao);

        return $listaDocumentos;

    }

    // Requisita o início da transação de alteração em fiscalização.documentos_entrega.
    public function devolver($dadosRequestPar)
    {
        if ($dadosRequestPar!= "") {
            $return  = $this->controller->devolver($dadosRequestPar);

            return $return;
        }
    }

    // Escreve os checkBoxes na tabela de documentos que foram e que serão devolvidos.
    private function validaCheckBoxDevolucao($listaDocumentos, $inCodProcessoPar, $obChkStatusSituacao)
    {
        $count = count($listaDocumentos->arElementos);
        $inNewCodDocumentos = '';
        $inNumLinhas = 0;
        for ($i = 0; $i < $count; $i++) {

            $inCodDocumentos = $listaDocumentos->arElementos[$i]["cod_documento"];
            $stSituacao = $listaDocumentos->arElementos[$i]["situacao"];

            if ($inNewCodDocumentos != $inCodDocumentos) {
                foreach ($listaDocumentos->arElementos[$i] as $ch => $vlr) {
                    if ($ch == 'situacao') {
                        switch ($vlr) {
                            case "R":
                                $obChkStatusSituacao->setName("stSituacao[{$inCodDocumentos}]");
                                $obChkStatusSituacao->setChecked(false);
                                $obChkStatusSituacao->setDisabled(false);
                                $obChkStatusSituacao->setValue("D");
                            break;
                            case "D":
                                $obChkStatusSituacao->setChecked(true);
                                $obChkStatusSituacao->setDisabled(true);
                                $obChkStatusSituacao->setTitle("Devolvido");
                                $obChkStatusSituacao->setValue("");
                            break;
                            default:
                                $obChkStatusSituacao->setChecked(false);
                                $obChkStatusSituacao->setDisabled(true);
                                $obChkStatusSituacao->setTitle("A Receber" );
                                $obChkStatusSituacao->setValue("");
                            break;
                        }

                        if ($vlr == '') {
                            $listaDocumentos->arElementos[$i]['check'] = "Não recebido!";
                        } else {
                            $obChkStatusSituacao->montaHtml();
                            $listaDocumentos->arElementos[$i]['check'] = $obChkStatusSituacao->getHtml() ;
                        }
                    }
                }
                $inNewCodDocumentos = $inCodDocumentos;
            } else {
                $listaDocumentos->arElementos[$i] = null;
            }
        }
        $listaDocumentos->ordena('cod_documento');
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
            $stFiltro[] = " fifde.situacao = 'R' \n";
            $stFiltro[] = " fifded.situacao is null \n";
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
