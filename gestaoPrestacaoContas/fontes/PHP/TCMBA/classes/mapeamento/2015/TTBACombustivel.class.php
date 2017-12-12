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
    * Extensão da Classe de mapeamento
    * Data de Criação: 26/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 64003 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.3  2007/10/03 02:50:44  diego
Corrigindo formatação

Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/27 03:14:30  diego
Adicionado Patrimonio

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 26/09/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBACombustivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio() );
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
  $stSql = " SELECT tipo_registro
                  , placa
                  , tipo_combustivel
                  , SUM(km_ltr) AS km_ltr
                  , SUM(custo_mensal) AS custo_mensal
                  , competencia
                  , unidade_gestora
                  , cod_tipo

               FROM
                  (
                    SELECT 1 AS tipo_registro
                         , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                         , veiculo.placa
                         , CASE WHEN combustivel.cod_combustivel = 1 THEN 3
                                WHEN combustivel.cod_combustivel = 2 THEN 1
                                WHEN combustivel.cod_combustivel = 3 THEN 2
                            END AS tipo_combustivel
                         , COALESCE(manutencao_item.quantidade,0.00) AS km_ltr
                         , COALESCE(manutencao_item.valor,0.00) AS custo_mensal
                         , TO_CHAR(manutencao.dt_manutencao, 'yyyymm') AS competencia
                         , item.cod_tipo
                         
                     FROM frota.manutencao

               INNER JOIN frota.manutencao_item
                       ON manutencao_item.cod_manutencao = manutencao.cod_manutencao
                      AND manutencao_item.exercicio = manutencao.exercicio

               INNER JOIN frota.item
                       ON item.cod_item = manutencao_item.cod_item

               INNER JOIN frota.tipo_item
                       ON tipo_item.cod_tipo = item.cod_tipo

               INNER JOIN frota.combustivel_item
		               ON item.cod_item = combustivel_item.cod_item
              
               INNER JOIN frota.combustivel
                       ON combustivel.cod_combustivel = combustivel_item.cod_combustivel

               INNER JOIN frota.veiculo
                       ON veiculo.cod_veiculo = manutencao.cod_veiculo

               INNER JOIN frota.tipo_veiculo
                       ON tipo_veiculo.cod_tipo = veiculo.cod_tipo_veiculo

                LEFT JOIN frota.abastecimento
                       ON veiculo.cod_veiculo = abastecimento.cod_veiculo

                LEFT JOIN frota.veiculo_combustivel
                       ON veiculo_combustivel.cod_veiculo = veiculo.cod_veiculo

                    WHERE manutencao.exercicio = '".$this->getDado('exercicio')."'
                      AND manutencao.dt_manutencao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                       AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                      AND EXISTS(SELECT veiculo_propriedade.cod_veiculo
                                      , MAX(veiculo_propriedade.timestamp) AS timestamp
                                   FROM frota.veiculo_propriedade
                             INNER JOIN frota.proprio
                                     ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                    AND proprio.timestamp = veiculo_propriedade.timestamp
                              LEFT JOIN patrimonio.bem_comprado
                                     ON bem_comprado.cod_bem = proprio.cod_bem
                                  WHERE veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                    AND bem_comprado.cod_entidade IN (2)
                               GROUP BY veiculo_propriedade.cod_veiculo
                                )
                     AND item.cod_tipo = 1        
                        
                    GROUP BY veiculo.placa
                           , tipo_combustivel
                           , manutencao_item.valor
                           , manutencao.dt_manutencao
                           , item.cod_tipo
                           , manutencao_item.quantidade
                  ) AS retorno

          WHERE custo_mensal > 0.00
          GROUP BY placa
                 , tipo_registro
                 , tipo_combustivel
                 , competencia
                 , unidade_gestora
                 , cod_tipo ";

    return $stSql;
}

}
