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
    * Página de Regra de Negócio para vinculação de atividades
    * Data de Criacao: 31/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

*/

include_once (CAM_GT_FIS_NEGOCIO.'/RFISDocumento.class.php');
include_once (CAM_GT_CEM_MAPEAMENTO.'/TCEMAtividade.class.php');
include_once (CAM_GT_FIS_MAPEAMENTO.'/TFISVinculo.class.php');

$stPrograma = "ManterVinculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

class RFISVinculo
{
    public $documento;
    public $CriterioSql = null;

    public function __construct()
    {
        $this->documento =  new RFISDocumento();
    }

    protected function CallMap($mapeamento,$metodo,$criterio)
    {
        $where = " where ";
        if ($criterio) {
            $criterio = $where.$criterio;
        }

        $obRs = new RecordSet();
        $ob = new $mapeamento;
        $ob->$metodo($obRs,$criterio);

        return $obRs;
    }

    //retorna o codígo da atividade vinculada ao codigo estrutural
    public function getAtividade($cod_estrutural)
    {
        $obRSCEMAtividade = new RecordSet();
        $obTCEMAtividade = new TCEMAtividade();
        $condicao = "   WHERE cod_estrutural LIKE '%".$cod_estrutural."%' \n";
        $condicao.= "     AND cod_vigencia = ( SELECT MAX(cod_vigencia) AS nivel \n";
        $condicao.= "                            FROM economico.vigencia_atividade) \n";
        $condicao.= "ORDER BY cod_nivel DESC LIMIT 1; \n";

        $obTCEMAtividade->recuperaTodos($obRSCEMAtividade, $condicao);

        if ($obRSCEMAtividade->arElementos[0]['cod_estrutural'] == $cod_estrutural) {
            $inCodAtividade = $obRSCEMAtividade->arElementos[0]['cod_atividade'];
        } else {
            $inCodAtividade = 0;
        }

        return $inCodAtividade;
    }

    public function getDocumento($inCodAtividade)
    {
        $obRSDocumento = new RecordSet();
        $obTDocumento = new TFISVinculo();
        $condicao = $inCodAtividade;
        $obTDocumento->recuperarDocumento($obRSDocumento, $condicao);

        return $obRSDocumento;
    }

    public function setVinculo($inCodAtividade, $arValores)
    {
        global $pgForm;
        $obTFISVinculo = new TFISVinculo;
        $obRCEMAtividade = new RCEMAtividade();
        $obRSTFISVinculo = new RecordSet;
        $documentos = $arValores["cod_documento"];

        $arExcluir = Sessao::read('exclusao');
        if (count($arExcluir) > 0) {
            $this->excluirDocumentoVinculado($inCodAtividade,$arExcluir);
        }

        $inNivel = $arValores["inNumNiveis"] - 1;

        if (empty($arValores["inCodAtividade_".$inNivel])) {
            return sistemaLegado::exibeAviso("Todos os Níveis da Atividade devem ser preenchidos.", "aviso", "aviso");
        }

        if (count($arValores) > 0) {
           if (count($documentos) > 0) {
                # Inicia nova transação
                $obTransacao = new Transacao();
                $boFlagTransacao = false;
                   $boTransacao = "";

                   $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

                foreach ($documentos as $chave => $doc) {

                    $stFiltro = " WHERE cod_atividade = ".$inCodAtividade." \n";
                    $stFiltro.= "   AND cod_documento = ".$doc." \n";

                    $obTFISVinculo->recuperaTodos($obRSTFISVinculo,$stFiltro);

                    if ($obRSTFISVinculo->Eof()) {

                        $obTFISVinculo->setDado("cod_atividade", $inCodAtividade);
                        $obTFISVinculo->setDado("cod_documento", $doc);

                        $obErro = $obTFISVinculo->inclusao($boTransacao);

                        if ($obErro->ocorreu()) {
                            return sistemaLegado::exibeAviso("Erro ao incluir documento", "n_incluir", "erro");
                        }
                    }
                }//foreach

                # Termina transação
                Sessao::remove('arDocumentos');
                   $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTFISVinculo);

                return sistemaLegado::alertaAviso($pgForm, $inCodAtividade , "incluir", "aviso", Sessao::getId(), "../");

            } else {
                return sistemaLegado::exibeAviso("Todos os Níveis da Atividade devem ser preenchidos.", "n_incluir", "erro");
            }
        } else {
            return sistemaLegado::exibeAviso("Todos os Níveis da Atividade devem ser preenchidos.", "n_incluir", "erro");
        }
    } //fim funcao

    public function excluirDocumentoVinculado($inCodAtividade,$arExcluir)
    {
        $obTFISVinculoexcluir = new TFISVinculo;
        $obRCEMAtividade = new RCEMAtividade();
        $obRSTFISVinculo = new RecordSet;
        foreach ($arExcluir as $chave => $doc) {

            $obTFISVinculoexcluir->setDado( "cod_documento"  , $doc['excluir'] ." AND cod_atividade =".$inCodAtividade );
            $obTFISVinculoexcluir->exclusao();
        }
    }

    public function setCriterio($vlr)
    {
        $this->CriterioSql = $vlr;
    }
}
?>
