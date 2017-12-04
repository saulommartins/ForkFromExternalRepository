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
    * Classe de Regra do Relatório de Balancete de Receita
    * Data de Criação   : 18/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Revision: 30805 $
    $Name$
    $Author: cako $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso : uc-02.03.08
*/

/*
$Log$
Revision 1.8  2006/08/09 18:13:58  jose.eduardo
Bug #6737#

Revision 1.7  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"             );

/**
    * Classe de Regra de Negócios Empenho Empenhado, Pago ou Liquidado
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioRPAnuLiqEstLiq extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obREntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $inOrgao;
/**
    * @var Integer
    * @access Private
*/
var $inUnidade;
/**
    * @var Integer
    * @access Private
*/
var $stCodElementoDespesa;
/**
    * @var String
    * @access Private
*/
var $inRecurso;
/**
    * @var Integer
    * @access Private
*/
var $inSituacao;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var Integer
    * @access Private
*/
var $stFiltro;
/**
     * @access Public
     * @param Object $valor
*/
function setREntidade($valor) { $this->obREntidade     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setOrgao($valor) { $this->inOrgao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setUnidade($valor) { $this->inUnidade        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodElementoDespesa($valor) { $this->stCodElementoDespesa      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRecurso($valor) { $this->inRecurso        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setSituacao($valor) { $this->inSituacao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getREntidade() { return $this->obREntidade;               }
/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return Object
*/
function getDataInicial() { return $this->stDataInicial;                }
/**
     * @access Public
     * @return Object
*/
function getDataFinal() { return $this->stDataFinal;                }
/**
     * @access Public
     * @return Object
*/
function getOrgao() { return $this->inOrgao;                  }
/**
     * @access Public
     * @return Object
*/
function getUnidade() { return $this->inUnidade;                  }
/**
     * @access Public
     * @return Object
*/
function getCodElementoDespesa() { return $this->stCodElementoDespesa;                }
/**
     * @access Public
     * @return Object
*/
function getRecurso() { return $this->inRecurso;                  }
/**
     * @access Public
     * @return Object
*/
function getSituacao() { return $this->inSituacao;                  }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                  }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                     }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioRPAnuLiqEstLiq()
{
    $this->setREntidade                     ( new ROrcamentoEntidade                );
    $this->obREntidade->obRCGM->setNumCGM   ( Sessao::read('numCgm')                       );
    $this->obROrcamentoUnidadeOrcamentaria       = new ROrcamentoUnidadeOrcamentaria;
    $this->obROrcamentoClassificacaoDespesa      = new ROrcamentoClassificacaoDespesa;
    $this->obROrcamentoRecurso                   = new ROrcamentoRecurso;

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoRPAnuLiqEstLiq.class.php" );
    $obFEmpenhoRPAnuLiqEstLiq        = new FEmpenhoRPAnuLiqEstLiq;

    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obREntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

    $obFEmpenhoRPAnuLiqEstLiq->setDado("exercicio",$this->getExercicio());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("stFiltro",$this->getFiltro());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("stEntidade",$this->getCodEntidade());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("stDataInicial",$this->getDataInicial());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("stDataFinal",$this->getDataFinal());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("inOrgao", $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("inUnidade",$this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("stElementoDespesa",$this->obROrcamentoClassificacaoDespesa->getCodEstrutural() );
    $obFEmpenhoRPAnuLiqEstLiq->setDado("inRecurso",$this->obROrcamentoRecurso->getCodRecurso());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("stDestinacaoRecurso",$this->obROrcamentoRecurso->getDestinacaoRecurso());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("inCodDetalhamento",$this->obROrcamentoRecurso->getCodDetalhamento());
    $obFEmpenhoRPAnuLiqEstLiq->setDado("inSituacao",$this->getSituacao());
    $obErro = $obFEmpenhoRPAnuLiqEstLiq->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotal            = 0;
    $inTotalGeral       = 0;
    $arRecord           = array();
    $dtAtual            = "";
    $mostra             = true;

    while ( !$rsRecordSet->eof() ) {
        $data = $rsRecordSet->getCampo('data');

        if (($dtAtual <> $data) and $inCount>0) {
            $dtAtual = $data;

            //MONTA TOTALIZADOR GERAL
            $arRecord[$inCount]['nivel']             = 2;
            $arRecord[$inCount]['empenho']           = "";
            $arRecord[$inCount]['cod_nota']          = "";
            $arRecord[$inCount]['cgm']               = "";
            $arRecord[$inCount]['razao_social']      = "TOTAL DO DIA";
            $arRecord[$inCount]['valor']              = number_format( $inTotal, 2, ',', '.' );
            $arRecord[$inCount]['data']               = "";

            $inCount++;

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = "";
            $arRecord[$inCount]['cod_nota']          = "";
            $arRecord[$inCount]['cgm']               = "";
            $arRecord[$inCount]['razao_social']      = "";
            $arRecord[$inCount]['valor']             = "";
            $arRecord[$inCount]['data']              = "";

            $inCount++;

            $inTotalGeral = $inTotalGeral + $inTotal;
            $inTotal    = 0;
            $mostra     = true;
        }

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['empenho']           = $rsRecordSet->getCampo('entidade') . " - " .$rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
        if($rsRecordSet->getCampo('cod_nota') <> 0)
           $arRecord[$inCount]['cod_nota']      = $rsRecordSet->getCampo('cod_nota');
        else
           $arRecord[$inCount]['cod_nota']      = "";
        $arRecord[$inCount]['cgm']               = $rsRecordSet->getCampo('cgm');
        $arRecord[$inCount]['razao_social']      = $rsRecordSet->getCampo('razao_social');
        $arRecord[$inCount]['valor']             = number_format( $rsRecordSet->getCampo('valor'), 2, ',', '.' );
        if($mostra)
            $arRecord[$inCount]['data']              = $data;
        else
            $arRecord[$inCount]['data']              = "";

        if($inCount == 0)
            $dtAtual = $data;

        $inCount++;
        $inTotal          = $inTotal + $rsRecordSet->getCampo('valor');
        $rsRecordSet->proximo();

        $mostra = false;
    }

    if ($inCount>0) {
        $arRecord[$inCount]['nivel']             = 2;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cod_nota']          = "";
        $arRecord[$inCount]['cgm']               = "";
        $arRecord[$inCount]['razao_social']      = "TOTAL DO DIA";
        $arRecord[$inCount]['valor']              = number_format( $inTotal, 2, ',', '.' );
        $arRecord[$inCount]['data']               = "";
        $inTotalGeral = $inTotalGeral + $inTotal;

        $inCount++;

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cod_nota']          = "";
        $arRecord[$inCount]['cgm']               = "";
        $arRecord[$inCount]['razao_social']      = "";
        $arRecord[$inCount]['valor']             = "";
        $arRecord[$inCount]['data']              = "";

        $inCount++;

        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']             = 2;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cgm']               = "";
        $arRecord[$inCount]['razao_social']      = "TOTAL DO PERÍODO";
        $arRecord[$inCount]['valor']              = number_format( $inTotalGeral, 2, ',', '.' );
        $arRecord[$inCount]['data']               = "";

        $inCount++;
    }

    $arRecord[$inCount]['nivel']             = 2;
    $arRecord[$inCount]['empenho']           = " ";
    $arRecord[$inCount]['cod_nota']          = "";
    $arRecord[$inCount]['cgm']               = " ";
    $arRecord[$inCount]['razao_social']      = "- ENTIDADES RELACIONADAS";
    $arRecord[$inCount]['valor']             = " ";
    $arRecord[$inCount]['data']              = " ";

    $this->obREntidade->setExercicio( $this->getExercicio() );
    $inEntidades = str_replace("'","",$this->getCodEntidade() );
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inCount++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arRecord[$inCount]['razao_social'] = $rsLista->getCampo("entidade");
    }

    if ($this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()) {
        $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio($this->getExercicio());
        $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- ORGÃO";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $rsOrgao->getCampo("nom_orgao") ? $rsOrgao->getCampo("nom_orgao") : $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
    }

    if ($this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade()) {
        $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
        $this->obROrcamentoUnidadeOrcamentaria->setExercicio($this->getExercicio());
        $this->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- UNIDADE";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $rsCombo->getCampo("nom_unidade") ? $rsCombo->getCampo("nom_unidade") : $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();
    }

    if ($this->obROrcamentoClassificacaoDespesa->getCodEstrutural()) {
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- ELEMENTO DE DESPESA";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $this->obROrcamentoClassificacaoDespesa->getCodEstrutural();
    }

    if ($this->obROrcamentoRecurso->getCodRecurso()) {
        $this->obROrcamentoRecurso->listar( $rsRecurso );

        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- RECURSO";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $rsRecurso->getCampo("nom_recurso");
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
