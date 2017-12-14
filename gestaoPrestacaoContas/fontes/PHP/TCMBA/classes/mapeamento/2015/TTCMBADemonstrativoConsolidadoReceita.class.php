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
    * Classe de mapeamento da tabela fn_demonstrativo_consolidado_receita
    * Data de Criação: 24/09/2004

    * @author Analista: Valtair
    * @author Desenvolvedor: Lisiane Morais
    * $id:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBADemonstrativoConsolidadoReceita extends Persistente 
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    
    parent::Persistente();
    $this->setTabela('tcmba.fn_demonstrativo_consolidado_receita');

    $this->AddCampo('tipo_registro'             , 'varchar', false, ''    , false, false);
    $this->AddCampo('unidade_gestora'           , 'varchar', false, ''    , false, false);
    $this->AddCampo('competencia'               , 'varchar', false, ''    , false, false);
  
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}




function montaRecuperaDadosTribunal()
{
    $stSql  = "
        SELECT 1  AS tipo_registro
             , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
             , '".$this->getDado('exercicio').$this->getDado('mes')."' AS competencia
             , tabela.item_receita
             , SUM(tabela.valor_previsto) AS valor_previsto
             , SUM(tabela.arrecadado_mes)AS arrecadado_mes
             , SUM(tabela.arrecadado_ate_periodo) AS arrecadado_ate_periodo
             , SUM(tabela.anulado_mes) AS anulado_mes
             , SUM(tabela.anulado_ate_periodo) AS anulado_ate_periodo
             , SUM(tabela.vl_diferenca_mais) AS vl_diferenca_mais
             , SUM(tabela.vl_diferenca_menos) AS vl_diferenca_menos
         FROM ( SELECT SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,8) AS item_receita
                    , CASE WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) = '9' THEN
                               CASE WHEN (retorno.valor_previsto > 0) THEN retorno.valor_previsto * -1
                                    ELSE retorno.valor_previsto
                               END
                           WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) != '9' THEN
                               CASE WHEN (retorno.valor_previsto < 0) THEN retorno.valor_previsto * -1
                                    ELSE retorno.valor_previsto
                               END
                       END AS valor_previsto
                    , CASE WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) = '9' THEN
                               CASE WHEN (retorno.arrecadado_mes > 0) THEN retorno.arrecadado_mes * -1
                                    ELSE retorno.arrecadado_mes
                               END
                           WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) != '9' THEN
                               CASE WHEN (retorno.arrecadado_mes < 0) THEN retorno.arrecadado_mes * -1
                                   ELSE retorno.arrecadado_mes
                               END
                      END AS arrecadado_mes
                    , CASE WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) = '9' THEN
                               CASE WHEN (retorno.arrecadado_ate_periodo > 0) THEN retorno.arrecadado_ate_periodo * -1
                                    ELSE retorno.arrecadado_ate_periodo
                               END
                           WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) != '9' THEN
                               CASE WHEN (retorno.arrecadado_ate_periodo < 0) THEN retorno.arrecadado_ate_periodo * -1
                                    ELSE retorno.arrecadado_ate_periodo
                               END
                       END AS arrecadado_ate_periodo
                     , CASE WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) != '9' THEN retorno.anulado_mes * -1
                            ELSE retorno.anulado_mes
                       END AS anulado_mes
                    , CASE WHEN SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,1) != '9' THEN retorno.anulado_ate_periodo * -1
		                   ELSE retorno.anulado_ate_periodo
                       END AS anulado_ate_periodo
                    , retorno.valor_diferenca AS vl_diferenca_mais
                    , ( retorno.valor_previsto - retorno.valor_diferenca ) AS vl_diferenca_menos
                   
                 FROM ".$this->getTabela()." ( '".$this->getDado("exercicio")."'
                                              ,'".$this->getDado("data_inicio")."'
                                              ,'".$this->getDado("data_fim")."'
                                              ,'".$this->getDado("entidades")." ') AS retorno
             ORDER BY item_receita
            ) AS tabela
             
     GROUP BY tabela.item_receita ";
    
    return $stSql;
}

}

?>