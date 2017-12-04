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
    * Classe de mapeamento da tabela folhapagamento.desconto_externo_IRRF
    * Data de Criação: 25/07/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31094 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-13 13:11:28 -0300 (Qui, 13 Set 2007) $

    * Casos de uso: uc-04.05.60
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.desconto_externo_IRRF
  * Data de Criação: 25/07/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Tiago Finger

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoDescontoExternoIRRF extends Persistente
{
/**
    * @var Date
    * @access Private
*/
var $vigenciaIni;

/**
    * @var Date
    * @access Private
*/
var $vigenciaFinal;

/**
    * @access Public
    * @param String $valor => $vigenciaIni
*/
function setVigenciaIni($valor) { $vigenciaIni           = $valor; }

/**
    * @access Public
    * @param String $valor => $vigenciaFinal
*/
function setVigenciaFinal($valor) { $vigenciaFinal         = $valor; }

/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoDescontoExternoIRRF()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.desconto_externo_irrf");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,timestamp');

    $this->AddCampo('cod_contrato','integer'      ,true  ,''      ,true,'TPessoalContrato');
    $this->AddCampo('timestamp'   ,'timestamp_now',true  ,''      ,true,false);
    $this->AddCampo('vl_base_IRRF','numeric'      ,true  ,'14,2'  ,false,false);
    $this->AddCampo('vigencia'    ,'date'         ,true  ,''      ,false,false);

}

function montaRecuperaMaxDescontoExternoIRRF()
{
    ;

    $stSql  = "SELECT cod_contrato, max(timestamp) FROM folhapagamento.desconto_externo_irrf  \n";
    if ( $this->getDado("cod_contrato") ) {
        $stSql .= "WHERE cod_contrato = ".$this->getDado("cod_contrato")."							 					 \n";
    }
    $stSql .= "GROUP BY cod_contrato 																					 \n";

    return $stSql;
}

function recuperaMaxDescontoExternoIRRF(&$rsRecorSet, $stFiltro = '')
{
    ;

    $obErro = $this->executaRecupera( 'montaRecuperaMaxDescontoExternoIRRF', $rsRecorSet, $stFiltro );

    return  $obErro;
}

function montaRecuperaUltimavigencia()
{
    ;

    $stSql  = "SELECT vigencia, desconto_externo_IRRF.timestamp AS timestamp FROM 										  \n";
    $stSql .= "		folhapagamento.desconto_externo_irrf   									  \n";
    $stSql .= "INNER JOIN 																								  \n";
    $stSql .= "		( SELECT cod_contrato, max(timestamp) AS timestamp FROM folhapagamento.desconto_externo_irrf GROUP BY cod_contrato ) AS max_desconto_IRRF \n";
    $stSql .= "ON desconto_externo_IRRF.cod_contrato = max_desconto_IRRF.cod_contrato 								  	  \n";
    $stSql .= "AND desconto_externo_IRRF.timestamp =  max_desconto_IRRF.timestamp       						          \n";
    $stSql .= " AND NOT EXISTS (                                                                                        \n";
    $stSql .= "     SELECT cod_contrato, max(timestamp) as timestamp FROM folhapagamento.desconto_externo_irrf_anulado       \n";
    $stSql .= "         WHERE cod_contrato = max_desconto_IRRF.cod_contrato AND timestamp = max_desconto_IRRF.timestamp \n";
    $stSql .= "         GROUP BY cod_contrato                                                                           \n";
    $stSql .= " )                                                                                                       \n";

    if ( $this->getDado("cod_contrato") ) {
        $stSql .= " AND desconto_externo_IRRF.cod_contrato = ".$this->getDado("cod_contrato");
    }

    return $stSql;
}

function recuperaUltimavigencia(&$rsRecorSet, $stFiltro = '')
{
    ;

    $obErro = $this->executaRecupera( 'montaRecuperaUltimavigencia', $rsRecorSet, $stFiltro );

    return  $obErro;
}

function montaRecuperaRelacionamento()
{
    ;
    $stSql .= "SELECT desconto_externo_irrf.*  \n";
    $stSql .= "     , to_char(desconto_externo_irrf.vigencia,'dd/mm/yyyy') as vigencia_formatado  \n";
    $stSql .= "     , to_real(desconto_externo_irrf.vl_base_irrf) as vl_base_irrf_formatado  \n";
    $stSql .= "     , to_real(desconto_externo_irrf_valor.valor_irrf) as valor_irrf  \n";
    $stSql .= "     , servidor.numcgm \n";
    $stSql .= "     , (SELECT sw_cgm.nom_cgm FROM sw_cgm where numcgm = servidor.numcgm) as nom_cgm  \n";
    $stSql .= "     , contrato.registro   \n";
    $stSql .= "  FROM folhapagamento.desconto_externo_irrf \n";
    $stSql .= "     , (SELECT max(timestamp) as timestamp \n";
    $stSql .= "             , cod_contrato \n";
    $stSql .= "          FROM folhapagamento.desconto_externo_irrf \n";
    $stSql .= "         WHERE NOT EXISTS (SELECT 1  \n";
    $stSql .= "                    FROM folhapagamento.desconto_externo_irrf_anulado \n";
    $stSql .= "                   WHERE desconto_externo_irrf_anulado.cod_contrato = desconto_externo_irrf.cod_contrato \n";
    $stSql .= "                     AND desconto_externo_irrf_anulado.timestamp = desconto_externo_irrf.timestamp) \n";

    if ($vigenciaIni!='') {
        $stSql .= "           AND vigencia >= to_date('".$this->getDado( "vigenciaIni" )."','dd/mm/yyyy') \n";
    }

    if ($vigenciaFinal!='') {
        $stSql .= "           AND vigencia <= to_date('".$this->getDado( "vigenciaFinal" )."','dd/mm/yyyy') \n";
    }
    $stSql .= "         GROUP BY cod_contrato) as max_desconto \n";
    $stSql .= "       LEFT JOIN folhapagamento.desconto_externo_irrf_valor \n";
    $stSql .= "         ON desconto_externo_irrf_valor.timestamp = max_desconto.timestamp \n";
    $stSql .= "        AND desconto_externo_irrf_valor.cod_contrato = max_desconto.cod_contrato  \n";
    $stSql .= "       LEFT JOIN pessoal.contrato \n";
    $stSql .= "         ON contrato.cod_contrato = max_desconto.cod_contrato \n";
    $stSql .= "       LEFT JOIN pessoal.servidor_contrato_servidor \n";
    $stSql .= "         ON servidor_contrato_servidor.cod_contrato = max_desconto.cod_contrato \n";
    $stSql .= "       LEFT JOIN pessoal.servidor \n";
    $stSql .= "         ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor \n";
    $stSql .= "WHERE desconto_externo_irrf.timestamp = max_desconto.timestamp \n";
    $stSql .= "  AND desconto_externo_irrf.cod_contrato = max_desconto.cod_contrato \n";
    if ( $this->getDado( "cod_contrato" )) {
        $stSql .= "    AND desconto_externo_irrf.cod_contrato = ".$this->getDado( "cod_contrato" )." \n";
    }

    return $stSql;
}

function montaRecuperaDescontoIRRF()
{
    ;

    $stSql  = "SELECT to_char(desconto_externo_irrf.vigencia,'DD/MM/YYYY') AS vigencia									\n";
    $stSql .= "		, desconto_externo_irrf.cod_contrato 																\n";
    $stSql .= "		, to_real(desconto_externo_irrf.vl_base_irrf) AS vl_base_irrf										\n";
    $stSql .= "		, to_real(desconto_externo_irrf_valor.valor_irrf) AS valor_irrf 									\n";
    $stSql .= "     , desconto_externo_irrf.timestamp                                                                   \n";
    $stSql .= "     , servidor.numcgm 																					\n";
    $stSql .= "     , (SELECT sw_cgm.nom_cgm FROM sw_cgm where numcgm = servidor.numcgm) as nom_cgm   					\n";
    $stSql .= "     , contrato.registro   																				\n";
    $stSql .= "FROM folhapagamento.desconto_externo_irrf 										\n";
    $stSql .= "		, (SELECT max(timestamp) as timestamp 																\n";
    $stSql .= "     , cod_contrato 																						\n";
    $stSql .= "		FROM folhapagamento.desconto_externo_irrf 								\n";
    $stSql .= "		  GROUP BY cod_contrato) as max_desconto 															\n";
    $stSql .= "       LEFT JOIN folhapagamento.desconto_externo_irrf_valor 					\n";
    $stSql .= "       	ON desconto_externo_irrf_valor.timestamp = max_desconto.timestamp 								\n";
    $stSql .= "       	AND desconto_externo_irrf_valor.cod_contrato = max_desconto.cod_contrato 						\n";
    $stSql .= "       LEFT JOIN pessoal.contrato 												\n";
    $stSql .= "       	ON contrato.cod_contrato = max_desconto.cod_contrato 											\n";
    $stSql .= "       LEFT JOIN pessoal.servidor_contrato_servidor 							\n";
    $stSql .= "         ON servidor_contrato_servidor.cod_contrato = max_desconto.cod_contrato 							\n";
    $stSql .= "		  LEFT JOIN pessoal.servidor 												\n";
    $stSql .= "         ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor 								\n";
    $stSql .= "WHERE desconto_externo_irrf.timestamp = max_desconto.timestamp 											\n";
    $stSql .= "	AND desconto_externo_irrf.cod_contrato = max_desconto.cod_contrato 										\n";
    $stSql .= " AND NOT EXISTS (                                                                                        \n";
    $stSql .= "     SELECT cod_contrato, max(timestamp) AS timestamp FROM folhapagamento.desconto_externo_irrf_anulado \n";
    $stSql .= "         WHERE cod_contrato = max_desconto.cod_contrato AND timestamp = max_desconto.timestamp           \n";
    $stSql .= "         GROUP BY cod_contrato                                                                           \n";
    $stSql .= " )                                                                                                       \n";

    return $stSql;
}

function recuperaDescontoIRRF(&$rsRecorSet, $stFiltro, $stOrdem)
{
    $obErro = $this->executaRecupera( 'montaRecuperaDescontoIRRF', $rsRecorSet, $stFiltro, $stOrdem );

    return  $obErro;
}

function recuperaParaExclusao(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaParaExclusao",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaParaExclusao()
{
    $stSql = "SELECT desconto_externo_irrf.*  \n";
    $stSql .= "  FROM folhapagamento.desconto_externo_irrf \n";
    $stSql .= " WHERE NOT EXISTS (SELECT 1  \n";
    $stSql .= "                     FROM folhapagamento.desconto_externo_irrf_anulado \n";
    $stSql .= "                    WHERE desconto_externo_irrf_anulado.cod_contrato = desconto_externo_irrf.cod_contrato \n";
    $stSql .= "                      AND desconto_externo_irrf_anulado.timestamp = desconto_externo_irrf.timestamp) \n";

    return $stSql;
}

}

?>
