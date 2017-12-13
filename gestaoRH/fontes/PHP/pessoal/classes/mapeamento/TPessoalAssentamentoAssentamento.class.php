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
    * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_ASSENTAMENTO
    * Data de Criacão: 30/09/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TPessoalAssentamentoAssentamento.class.php 66365 2016-08-18 14:39:09Z evandro $

    * Casos de uso: uc-04.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_ASSENTAMENTO
  * Data de Criacão: 30/09/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoAssentamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoAssentamento()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_assentamento');

    $this->setCampoCod('cod_assentamento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_assentamento'      ,'integer'  ,true   ,''     ,true   ,false);
    $this->AddCampo('cod_classificacao'     ,'integer'  ,true   ,''     ,false  ,true);
    $this->AddCampo('cod_motivo'            ,'integer'  ,true   ,''     ,false  ,true);
    $this->AddCampo('cod_norma'             ,'integer'  ,true   ,''     ,false  ,true);
    $this->AddCampo('cod_operador'          ,'integer'  ,true   ,''     ,false  ,true);
    $this->AddCampo('descricao'             ,'char'     ,true   ,'80'   ,false  ,false);
    $this->AddCampo('sigla'                 ,'char'     ,true   ,'10'   ,false  ,false);
    $this->AddCampo('abreviacao'            ,'char'     ,false  ,'3'    ,false  ,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT assentamento_assentamento.*                                                                      \n";
    $stSql .= "  FROM pessoal.assentamento                                                                             \n";
    $stSql .= "     , (SELECT cod_assentamento                                                                         \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.assentamento                                                                     \n";
    $stSql .= "        GROUP BY cod_assentamento) as max_assentamento                                                  \n";
    $stSql .= "     , pessoal.assentamento_assentamento                                                                \n";
    $stSql .= "     , pessoal.classificacao_assentamento                                                               \n";
    $stSql .= "     , folhapagamento.previdencia                                                                       \n";
    $stSql .= "     , pessoal.contrato_servidor_previdencia                                                            \n";
    $stSql .= "     , (SELECT cod_contrato                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.contrato_servidor_previdencia                                                    \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                     \n";
    $stSql .= " WHERE assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                       \n";
    $stSql .= "   AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                \n";
    $stSql .= "   AND assentamento.timestamp        = max_assentamento.timestamp                                       \n";
    $stSql .= "   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao       \n";
    $stSql .= "   AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                      \n";
    $stSql .= "   AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato      \n";
    $stSql .= "   AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp         \n";

    if (trim($this->getDado("dtInicial")) != "" && trim($this->getDado("dtFinal"))!="") {
        $stSql .= "  AND EXISTS (  SELECT 1                                                                                                                                                     \n";
        $stSql .= "                  FROM pessoal.assentamento_gerado_contrato_servidor                                                                                                         \n";
        $stSql .= "            INNER JOIN pessoal.assentamento_gerado                                                                                                                           \n";
        $stSql .= "                    ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                                           \n";
        $stSql .= "            INNER JOIN pessoal.assentamento_assentamento as assentamento_assentamento_interno                                                                                \n";
        $stSql .= "                    ON assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento                                                                     \n";
        $stSql .= "            INNER JOIN ( SELECT cod_assentamento_gerado                                                                                                                      \n";
        $stSql .= "                              , max(timestamp) as timestamp                                                                                                                  \n";
        $stSql .= "                           FROM pessoal.assentamento_gerado                                                                                                                  \n";
        $stSql .= "                       GROUP BY cod_assentamento_gerado) as max_assentamento_gerado                                                                                          \n";
        $stSql .= "                    ON assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                                         \n";
        $stSql .= "                   AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                                     \n";
        $stSql .= "                 WHERE assentamento_gerado_contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                       \n";
        $stSql .= "                   AND to_date('".$this->getDado("dtInicial")."', 'dd/mm/yyyy') BETWEEN assentamento_gerado.periodo_inicial AND assentamento_gerado.periodo_final            \n";
        $stSql .= "                    OR to_date('".$this->getDado("dtFinal")."', 'dd/mm/yyyy') BETWEEN assentamento_gerado.periodo_inicial AND assentamento_gerado.periodo_final              \n";
        $stSql .= "                    OR ( assentamento_gerado.periodo_inicial >= to_date('".$this->getDado("dtInicial")."', 'dd/mm/yyyy') AND assentamento_gerado.periodo_final <= to_date('".$this->getDado("dtFinal")."', 'dd/mm/yyyy'))  \n";
        $stSql .= "                   AND NOT EXISTS (SELECT 1                                                                                                                                  \n";
        $stSql .= "                                     FROM pessoal.assentamento_gerado_excluido                                                                                               \n";
        $stSql .= "                                    WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                                 \n";
        $stSql .= "                                      AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp))                                                           \n";
    }

    return $stSql;
}

function recuperaAssentamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAssentamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAssentamento()
{
    $stSql .= "SELECT assentamento_assentamento.*                                                                      \n";
    $stSql .= "     , assentamento.assentamento_automatico                                                                      \n";
    $stSql .= "  FROM pessoal.assentamento                                                                             \n";
    $stSql .= "     , (SELECT cod_assentamento                                                                         \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.assentamento                                                                     \n";
    $stSql .= "        GROUP BY cod_assentamento) as max_assentamento                                                  \n";
    $stSql .= "     , pessoal.assentamento_assentamento                                                                \n";
    $stSql .= "     , pessoal.classificacao_assentamento                                                               \n";
    $stSql .= " WHERE assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                       \n";
    $stSql .= "   AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                \n";
    $stSql .= "   AND assentamento.timestamp        = max_assentamento.timestamp                                       \n";
    $stSql .= "   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao       \n";

    return $stSql;
}

function recuperaAssentamentoSubDivisao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAssentamentoSubDivisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAssentamentoSubDivisao()
{
    $stSql .= "SELECT assentamento_assentamento.*                                                                      \n";
    $stSql .= "     , assentamento.assentamento_automatico                                                             \n";
    $stSql .= "  FROM pessoal.assentamento                                                   \n";
    $stSql .= "     , (SELECT cod_assentamento                                                                         \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.assentamento                                           \n";
    $stSql .= "        GROUP BY cod_assentamento) as max_assentamento                                                  \n";
    $stSql .= "     , pessoal.assentamento_assentamento                                      \n";
    $stSql .= "     , pessoal.classificacao_assentamento                                     \n";
    $stSql .= "     , pessoal.assentamento_sub_divisao                                       \n";
    $stSql .= "     , (  SELECT cod_assentamento                                                                       \n";
    $stSql .= "               , max(timestamp) as timestamp                                                            \n";
    $stSql .= "            FROM pessoal.assentamento_sub_divisao                             \n";
    $stSql .= "        GROUP BY cod_assentamento) as max_assentamento_sub_divisao                                      \n";
    $stSql .= " WHERE assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                       \n";
    $stSql .= "   AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                \n";
    $stSql .= "   AND assentamento.timestamp        = max_assentamento.timestamp                                       \n";
    $stSql .= "   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao       \n";
    $stSql .= "   AND assentamento.cod_assentamento = assentamento_sub_divisao.cod_assentamento                        \n";
    $stSql .= "   AND assentamento_sub_divisao.cod_assentamento = max_assentamento_sub_divisao.cod_assentamento        \n";
    $stSql .= "   AND assentamento_sub_divisao.timestamp = max_assentamento_sub_divisao.timestamp                      \n";

    return $stSql;
}

function recuperaContratoAssentamentoSubDivisao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratoAssentamentoSubDivisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratoAssentamentoSubDivisao()
{
    $stSQL = "     SELECT assentamento_assentamento.*                                                                               \n";
    $stSQL .= "       FROM pessoal.assentamento                                                                                      \n";
    $stSQL .= " INNER JOIN pessoal.assentamento_assentamento                                                                         \n";
    $stSQL .= "         ON assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                                \n";
    $stSQL .= " INNER JOIN (  SELECT cod_assentamento                                                                                \n";
    $stSQL .= "                    , max(timestamp) as timestamp                                                                     \n";
    $stSQL .= "                 FROM pessoal.assentamento                                                                            \n";
    $stSQL .= "             GROUP BY cod_assentamento) as max_assentamento                                                           \n";
    $stSQL .= "         ON assentamento.cod_assentamento = max_assentamento.cod_assentamento                                         \n";
    $stSQL .= "        AND assentamento.timestamp        = max_assentamento.timestamp                                                \n";
    $stSQL .= " INNER JOIN pessoal.classificacao_assentamento                                                                        \n";
    $stSQL .= "         ON assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                \n";
    $stSQL .= " INNER JOIN folhapagamento.previdencia                                                                                \n";
    $stSQL .= "         ON previdencia.cod_regime_previdencia = assentamento_assentamento.cod_regime_previdencia                     \n";
    $stSQL .= " INNER JOIN pessoal.contrato_servidor_previdencia                                                                     \n";
    $stSQL .= "         ON previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                               \n";
    $stSQL .= " INNER JOIN (  SELECT cod_contrato                                                                                    \n";
    $stSQL .= "                    , max(timestamp) as timestamp                                                                     \n";
    $stSQL .= "                 FROM pessoal.contrato_servidor_previdencia                                                           \n";
    $stSQL .= "             GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                              \n";
    $stSQL .= "         ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato               \n";
    $stSQL .= "        AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp                  \n";
    $stSQL .= "                                                                                                                      \n";
    $stSQL .= " INNER JOIN (  SELECT cod_contrato                                                                                    \n";
    $stSQL .= "                    , max(timestamp) as timestamp                                                                     \n";
    $stSQL .= "                 FROM pessoal.contrato_servidor_sub_divisao_funcao                                                    \n";
    $stSQL .= "             GROUP BY cod_contrato ) AS max_contrato_servidor_sub_divisao_funcao                                      \n";
    $stSQL .= "         ON max_contrato_servidor_previdencia.cod_contrato =max_contrato_servidor_sub_divisao_funcao.cod_contrato     \n";
    $stSQL .= " INNER JOIN pessoal.contrato_servidor_sub_divisao_funcao                                                              \n";
    $stSQL .= "         ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato \n";
    $stSQL .= "        AND max_contrato_servidor_sub_divisao_funcao.timestamp    = contrato_servidor_sub_divisao_funcao.timestamp    \n";
    $stSQL .= "                                                                                                                      \n";
    $stSQL .= " INNER JOIN pessoal.assentamento_sub_divisao                                                                          \n";
    $stSQL .= "         ON assentamento_sub_divisao.cod_assentamento = assentamento.cod_assentamento                                 \n";
    $stSQL .= " INNER JOIN ( SELECT cod_assentamento                                                                                 \n";
    $stSQL .= "                   , max(timestamp) AS timestamp                                                                      \n";
    $stSQL .= "                FROM pessoal.assentamento_sub_divisao                                                                 \n";
    $stSQL .= "            GROUP BY cod_assentamento                                                                                 \n";
    $stSQL .= "            ORDER BY cod_assentamento ) AS max_assentamento_sub_divisao                                               \n";
    $stSQL .= "         ON assentamento_sub_divisao.cod_assentamento = max_assentamento_sub_divisao.cod_assentamento                 \n";
    $stSQL .= "        AND assentamento_sub_divisao.timestamp        = max_assentamento_sub_divisao.timestamp                        \n";
    $stSQL .= "        AND assentamento_sub_divisao.cod_sub_divisao  = contrato_servidor_sub_divisao_funcao.cod_sub_divisao          \n";

    return $stSQL;
}

function recuperaAssentamentosPE(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAssentamentosPE",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAssentamentosPE()
{
    
    $stSql = "SELECT assentamento_assentamento.*, max_assentamento.timestamp \n";
    
    if ($this->getDado('cod_entidade') != 2) {
        
        $stSql .= " FROM pessoal_".$this->getDado('cod_entidade').".assentamento_assentamento
                    JOIN ( SELECT cod_assentamento
                                , max(timestamp) as timestamp
                             FROM pessoal_".$this->getDado('cod_entidade').".assentamento
                         GROUP BY cod_assentamento) as max_assentamento
                      ON max_assentamento.cod_assentamento=assentamento_assentamento.cod_assentamento
                  ";
    } else {
        $stSql .= " FROM pessoal.assentamento_assentamento
                    JOIN ( SELECT cod_assentamento
                                , max(timestamp) as timestamp
                             FROM pessoal.assentamento
                         GROUP BY cod_assentamento) as max_assentamento
                      ON max_assentamento.cod_assentamento=assentamento_assentamento.cod_assentamento
                  ";
    }
    
    return $stSql;
}



}
