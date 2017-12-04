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
    * Data de Criação   : 23/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Revision: 31583 $
    $Name$
    $Author: melo $
    $Date: 2008-03-13 16:31:49 -0300 (Qui, 13 Mar 2008) $

    * Casos de uso : uc-02.03.09
*/

/*
$Log$
Revision 1.8  2006/08/09 18:13:58  jose.eduardo
Bug #6737#

Revision 1.7  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO    );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"             );

/**
    * Classe de Regra de Negócios Empenho Empenhado, Pago ou Liquidado
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioRPPagEst extends PersistenteRelatorio
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
function setCodCredor($valor) { $this->inCodCredor      = $valor; }
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
     * @access Public
     * @return Object
*/
function getCodCredor() { return $this->inCodCredor;                     }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioRPPagEst()
{
    $this->setREntidade                     ( new ROrcamentoEntidade          );
    $this->obREntidade->obRCGM->setNumCGM   ( Sessao::read('numCgm')                 );
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
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoRPPagEstCredor.class.php" );
    $FEmpenhoRPPagEstCredor  = new FEmpenhoRPPagEstCredor;

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

    $FEmpenhoRPPagEstCredor->setDado("exercicio"			, $this->getExercicio()		);
    $FEmpenhoRPPagEstCredor->setDado("stFiltro"				, $this->getFiltro()		);
    $FEmpenhoRPPagEstCredor->setDado("stEntidade"			, $this->getCodEntidade()	);
    $FEmpenhoRPPagEstCredor->setDado("stDataInicial"		, $this->getDataInicial()	);
    $FEmpenhoRPPagEstCredor->setDado("stDataFinal"			, $this->getDataFinal()		);
    $FEmpenhoRPPagEstCredor->setDado("inOrgao"				, $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()	);
    $FEmpenhoRPPagEstCredor->setDado("inUnidade"			, $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade()	);
    $FEmpenhoRPPagEstCredor->setDado("stElementoDespesa"	, $this->obROrcamentoClassificacaoDespesa->getCodEstrutural() 	);
    $FEmpenhoRPPagEstCredor->setDado("inRecurso"			, $this->obROrcamentoRecurso->getCodRecurso()					);
    $FEmpenhoRPPagEstCredor->setDado("stDestinacaoRecurso"  , $this->obROrcamentoRecurso->getDestinacaoRecurso()			);
    $FEmpenhoRPPagEstCredor->setDado("inCodDetalhamento"	, $this->obROrcamentoRecurso->getCodDetalhamento()				);
    $FEmpenhoRPPagEstCredor->setDado("inSituacao"			, $this->getSituacao()		);
    $FEmpenhoRPPagEstCredor->setDado("inCodCredor"			, $this->getCodCredor()		);

    $obErro = $FEmpenhoRPPagEstCredor->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

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
            $arRecord[$inCount]['nivel']              = 2;
            $arRecord[$inCount]['empenho']            = "";
            $arRecord[$inCount]['cod_nota']           = "";
            $arRecord[$inCount]['conta']              = "";
            $arRecord[$inCount]['banco']              = "TOTAL DO DIA";
            $arRecord[$inCount]['valor']              = number_format( $inTotal, 2, ',', '.' );
            $arRecord[$inCount]['data']               = "";

            $inCount++;

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = "";
            $arRecord[$inCount]['cod_nota']          = "";
            $arRecord[$inCount]['conta']             = "";
            $arRecord[$inCount]['banco']             = "";
            $arRecord[$inCount]['valor']             = "";
            $arRecord[$inCount]['data']              = "";

            $inCount++;

            $inTotalGeral = $inTotalGeral + $inTotal;
            $inTotal    = 0;
            $mostra     = true;
        }

        $arRecord[$inCount]['nivel']            = 1;
        $arRecord[$inCount]['empenho']          = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
        if($rsRecordSet->getCampo('cod_nota') <> 0)
           $arRecord[$inCount]['cod_nota']      = $rsRecordSet->getCampo('cod_nota');
        else
           $arRecord[$inCount]['cod_nota']      = "";
        $arRecord[$inCount]['cod_estrutural']   = $rsRecordSet->getCampo('cod_estrutural');
        $arRecord[$inCount]['credor']           = $rsRecordSet->getCampo('credor');
        $arRecord[$inCount]['conta']            = $rsRecordSet->getCampo('conta');
        $arRecord[$inCount]['banco']            = $rsRecordSet->getCampo('banco');
        $arRecord[$inCount]['valor']            = number_format( $rsRecordSet->getCampo('valor'), 2, ',', '.' );
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
        $arRecord[$inCount]['conta']             = "";
        $arRecord[$inCount]['banco']             = "TOTAL DO DIA";
        $arRecord[$inCount]['valor']             = number_format( $inTotal, 2, ',', '.' );
        $arRecord[$inCount]['data']              = "";
        $inTotalGeral = $inTotalGeral + $inTotal;

        $inCount++;

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cod_nota']          = "";
        $arRecord[$inCount]['conta']             = "";
        $arRecord[$inCount]['banco']             = "";
        $arRecord[$inCount]['valor']             = "";
        $arRecord[$inCount]['data']              = "";

        $inCount++;

        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']             = 2;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cod_nota']          = "";
        $arRecord[$inCount]['conta']             = "";
        $arRecord[$inCount]['banco']             = "TOTAL DO PERÍODO";
        $arRecord[$inCount]['valor']             = number_format( $inTotalGeral, 2, ',', '.' );
        $arRecord[$inCount]['data']              = "";

        $inCount++;
    }

    $arRecord[$inCount]['nivel']             = 2;
    $arRecord[$inCount]['empenho']           = " ";
    $arRecord[$inCount]['cod_nota']          = "";
    $arRecord[$inCount]['conta']             = " ";
    $arRecord[$inCount]['banco']             = "- ENTIDADES RELACIONADAS";
    $arRecord[$inCount]['valor']             = " ";
    $arRecord[$inCount]['data']              = " ";

    $this->obREntidade->setExercicio( $this->getExercicio() );
    $inEntidades = str_replace("'","",$this->getCodEntidade() );
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inCount++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arRecord[$inCount]['banco'] = $rsLista->getCampo("entidade");
    }

    if ($this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()) {
        $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio($this->getExercicio());
        $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

        $inCount++;
        $arRecord[$inCount]['banco'] = "";
        $inCount++;
        $arRecord[$inCount]['banco'] = "- ORGÃO";
        $inCount++;
        $arRecord[$inCount]['banco'] = $rsOrgao->getCampo("nom_orgao") ? $rsOrgao->getCampo("nom_orgao") : $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
    }

    if ($this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade()) {
        $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
        $this->obROrcamentoUnidadeOrcamentaria->setExercicio($this->getExercicio());
        $this->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

        $inCount++;
        $arRecord[$inCount]['banco'] = "";
        $inCount++;
        $arRecord[$inCount]['banco'] = "- UNIDADE";
        $inCount++;
        $arRecord[$inCount]['banco'] = $rsCombo->getCampo("nom_unidade") ? $rsCombo->getCampo("nom_unidade") : $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();
    }

    if ($this->obROrcamentoClassificacaoDespesa->getCodEstrutural()) {
        $inCount++;
        $arRecord[$inCount]['banco'] = "";
        $inCount++;
        $arRecord[$inCount]['banco'] = "- ELEMENTO DE DESPESA";
        $inCount++;
        $arRecord[$inCount]['banco'] = $this->obROrcamentoClassificacaoDespesa->getCodEstrutural();
    }

    if ($this->obROrcamentoRecurso->getCodRecurso()) {
        $this->obROrcamentoRecurso->listar( $rsRecurso );

        $inCount++;
        $arRecord[$inCount]['banco'] = "";
        $inCount++;
        $arRecord[$inCount]['banco'] = "- RECURSO";
        $inCount++;
        $arRecord[$inCount]['banco'] = $rsRecurso->getCampo("nom_recurso");
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
