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
    * Classe de regra de negócio para Prorrogar Recebimento de Documentos
    * Data de Criação: 08/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacao.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacaoDocumentos.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProrrogacaoEntrega.class.php' );
require_once( CAM_GT_FIS_NEGOCIO.'RFISProcessoFiscal.class.php' );

class RFISProrrogarRecebimentoDocumentos extends RFISProcessoFiscal
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getListaInicioFiscalizacaoEconomica()
    {
        $mapeamento = "TFISInicioFiscalizacao";
        $metodo = "recuperaListaInicioFiscalizacaoEconomica";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getListaInicioFiscalizacaoObra()
    {
        $mapeamento = "TFISInicioFiscalizacao";
        $metodo = "recuperaListaInicioFiscalizacaoObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getListaInicioFiscalizacaoEconomicaObra()
    {
        $mapeamento = "TFISInicioFiscalizacao";
        $metodo = "recuperaListaInicioFiscalizacaoEconomicaObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getInicioFiscalizacaoEconomica()
    {
        $mapeamento = "TFISInicioFiscalizacao";
        $metodo = "recuperarInicioFiscalizacaoEconomica";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getInicioFiscalizacaoObra()
    {
        $mapeamento = "TFISInicioFiscalizacao";
        $metodo = "recuperarInicioFiscalizacaoObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function prorrogarRecebimento($parametros)
    {
        $pgList   = "FLProrrogarRecebimentoDocumentos.php";
        $pgForm   = "FMProrrogarRecebimentoDocumentos.php";

        $obTFISProrrogacaoEntrega = new TFISProrrogacaoEntrega();
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        # Inicia nova transação
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $inCodProcesso = $parametros['inCodProcesso'];
        $stProrrogacaoEntrega = $parametros['dtProrrogacaoEntrega'];
        $stAnterior = $parametros['inDtAnterior'];

        $dtNew = explode("/",$stProrrogacaoEntrega);
        $dtProrrogacaoEntrega = strtotime($dtNew[2]."-".$dtNew[1]."-".$dtNew[0]);

        $dtOld = explode("/",$stAnterior);
        $dtAnterior = strtotime($dtOld[2]."-".$dtOld[1]."-".$dtOld[0]);

        $rsRecordSet = new RecordSet();

        if ($dtProrrogacaoEntrega > $dtAnterior) {

            if ($rsRecordSet->Eof()) {
                    $obTFISProrrogacaoEntrega->setDado( "cod_processo", $inCodProcesso );

                $obTFISProrrogacaoEntrega->setDado( "dt_prorrogacao", $stProrrogacaoEntrega );

                $obErro = $obTFISProrrogacaoEntrega->inclusao( $boTransacao );
                # Ocorreu erro?
                       if (!$obErro->ocorreu() ) {
                    $return = sistemaLegado::alertaAviso($pgList , $inCodProcesso ,"incluir","aviso", Sessao::getId(), "../");
                    $boTermina = true;
                } else {
                    $return = sistemaLegado::exibeAviso( "Houve um erro ao Prorrogar Recebimento de Documentos.(".$inCodProcesso.")","n_incluir","erro" );
                    $boTermina = true;
                }
            } else {
                    $return = sistemaLegado::exibeAviso( "Houve um erro ao Prorrogar Recebimento de Documentos.(".$inCodProcesso.")","n_incluir","erro" );
                $boTermina = true;
            }
        } else {
            $return = sistemaLegado::exibeAviso( "Data Inválida para Prorrogar Recebimento de Documentos..(".$inCodProcesso.")","n_incluir","erro" );
        }

        if ($boTermina) {
            # Termina transação
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFISInicioFiscalizacao );
        }

        return $return;
    }

}
?>
