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
    * Classe de mapeamento da tabela
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoLancamentoMaterialEstorno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoLancamentoMaterialEstorno()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.lancamento_material_estorno');

    $this->setCampoCod('cod_lancamento_estorno');
    $this->setComplementoChave('cod_lancamento,cod_item,cod_marca,cod_almoxarifado,cod_centro');

    $this->AddCampo('cod_lancamento_estorno','sequence',true,'',true,false);
    $this->AddCampo('cod_lancamento'        ,'integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_almoxarifado'      ,'integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_item'              ,'integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_marca'             ,'integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_centro'            ,'integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');

}

function recuperaLancamentos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaLancamentos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaLancamentos()
{
    $stSql = "
       SELECT
                 natureza_lancamento.exercicio_lancamento
               , natureza_lancamento.num_lancamento
               , to_char(natureza_lancamento.timestamp,'dd/mm/yyyy') as dt_lancamento
               , natureza_lancamento.cod_natureza
               , natureza_lancamento.tipo_natureza
               , natureza_lancamento.cgm_almoxarife

               , ( SELECT  nom_cgm
                     FROM  sw_cgm
                    WHERE  sw_cgm.numcgm = natureza_lancamento.cgm_almoxarife
                 ) as nom_almoxarife

               , almoxarifado.cod_almoxarifado
               , almoxarifado.cgm_almoxarifado

               , ( SELECT  nom_cgm
                     FROM  sw_cgm
                    WHERE  sw_cgm.numcgm = almoxarifado.cgm_almoxarifado
                 ) as nom_almoxarifado

               , nota_fiscal_fornecedor_ordem.exercicio
               , nota_fiscal_fornecedor_ordem.cod_entidade
               , nota_fiscal_fornecedor_ordem.cod_ordem
               , ordem.exercicio_empenho
               , ordem.cod_empenho
               , nota_fiscal_fornecedor_ordem.cod_ordem||'/'||nota_fiscal_fornecedor_ordem.exercicio as cod_exercicio_ordem
               , ordem.cod_empenho||'/'||ordem.exercicio_empenho as cod_exercicio_empenho

         FROM  almoxarifado.natureza_lancamento

   INNER JOIN  compras.nota_fiscal_fornecedor
           ON  natureza_lancamento.exercicio_lancamento = nota_fiscal_fornecedor.exercicio_lancamento
          AND  natureza_lancamento.num_lancamento       = nota_fiscal_fornecedor.num_lancamento
          AND  natureza_lancamento.cod_natureza         = nota_fiscal_fornecedor.cod_natureza
          AND  natureza_lancamento.tipo_natureza        = nota_fiscal_fornecedor.tipo_natureza

    LEFT JOIN  compras.nota_fiscal_fornecedor_ordem
           ON  nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor
          AND  nota_fiscal_fornecedor_ordem.cod_nota       = nota_fiscal_fornecedor.cod_nota

    LEFT JOIN  compras.ordem
           ON  nota_fiscal_fornecedor_ordem.exercicio     = ordem.exercicio
          AND  nota_fiscal_fornecedor_ordem.cod_entidade  = ordem.cod_entidade
          AND  nota_fiscal_fornecedor_ordem.cod_ordem     = ordem.cod_ordem
          AND  nota_fiscal_fornecedor_ordem.tipo          = ordem.tipo
          AND  nota_fiscal_fornecedor_ordem.tipo          = 'C'

   INNER JOIN  almoxarifado.lancamento_material
           ON  lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento
          AND  lancamento_material.num_lancamento       = natureza_lancamento.num_lancamento
          AND  lancamento_material.cod_natureza         = natureza_lancamento.cod_natureza
          AND  lancamento_material.tipo_natureza        = natureza_lancamento.tipo_natureza

   INNER JOIN  almoxarifado.almoxarifado
           ON  almoxarifado.cod_almoxarifado = lancamento_material.cod_almoxarifado

".( $this->getDado('stExercicio')       ? " AND natureza_lancamento.exercicio_lancamento = '".$this->getDado('stExercicio')."'"  :"" )."
".( $this->getDado('stCodAlmoxarifado') ? " AND lancamento_material.cod_almoxarifado in (".$this->getDado('stCodAlmoxarifado').")"  :"" )."
".( $this->getDado('inCodItem')         ? " AND lancamento_material.cod_item = ".$this->getDado('inCodItem')  :"" )."
".( $this->getDado('inCodMarca')        ? " AND lancamento_material.cod_marca = ".$this->getDado('inCodMarca')  :"" )."
".( $this->getDado('inCodCentro')       ? " AND lancamento_material.cod_centro = ".$this->getDado('inCodCentro')  :"" )."
".( $this->getDado('stDataInicial')     ? " AND natureza_lancamento.timestamp::date >= to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy') "  :"" )."
".( $this->getDado('stDataFinal')       ? " AND natureza_lancamento.timestamp::date <= to_date('".$this->getDado('stDataFinal')."','dd/mm/yyyy') "  :"" )."
".( $this->getDado('inCodEmpenhoInicial') ? " AND ordem.cod_empenho BETWEEN ".$this->getDado('inCodEmpenhoInicial')." AND ".$this->getDado('inCodEmpenhoFinal')  :"" )."
".( $this->getDado('inCodOrdemInicial')   ? " AND ordem.cod_ordem BETWEEN ".$this->getDado('inCodOrdemInicial')." AND ".$this->getDado('inCodOrdemFinal')  :"" )."
".( $this->getDado('inNroEntradaInicial') ? " AND lancamento_material.num_lancamento >= ".$this->getDado('inNroEntradaInicial') :"")."
".( $this->getDado('inNroEntradaFinal') ? " AND lancamento_material.num_lancamento <= ".$this->getDado('inNroEntradaFinal') :"")."

          AND  (
                 (natureza_lancamento.cod_natureza = 9 AND natureza_lancamento.tipo_natureza = 'E') OR
                 (natureza_lancamento.cod_natureza = 1 AND natureza_lancamento.tipo_natureza = 'E')
               )

   WHERE (
           (
                SELECT  abs(sum(lancamento_material2.quantidade)) as soma
                FROM     almoxarifado.lancamento_material_estorno
                        ,almoxarifado.lancamento_material as lancamento_material2
                WHERE   lancamento_material2.cod_lancamento  = lancamento_material_estorno.cod_lancamento_estorno
                AND     lancamento_material2.cod_almoxarifado= lancamento_material_estorno.cod_almoxarifado
                AND     lancamento_material2.cod_item        = lancamento_material_estorno.cod_item
                AND     lancamento_material2.cod_marca       = lancamento_material_estorno.cod_marca
                AND     lancamento_material2.cod_centro      = lancamento_material_estorno.cod_centro

                AND     lancamento_material.cod_lancamento  = lancamento_material_estorno.cod_lancamento
                AND     lancamento_material.cod_almoxarifado= lancamento_material_estorno.cod_almoxarifado
                AND     lancamento_material.cod_item        = lancamento_material_estorno.cod_item
                AND     lancamento_material.cod_marca       = lancamento_material_estorno.cod_marca
                AND     lancamento_material.cod_centro      = lancamento_material_estorno.cod_centro

".( $this->getDado('stCodAlmoxarifado') ? " AND lancamento_material2.cod_almoxarifado in (".$this->getDado('stCodAlmoxarifado').")"  :"" )."
".( $this->getDado('inCodItem')         ? " AND lancamento_material2.cod_item = ".$this->getDado('inCodItem')  :"" )."
".( $this->getDado('inCodMarca')        ? " AND lancamento_material2.cod_marca = ".$this->getDado('inCodMarca')  :"" )."
".( $this->getDado('inCodCentro')       ? " AND lancamento_material2.cod_centro = ".$this->getDado('inCodCentro')  :"" )."
            ) < lancamento_material.quantidade

        OR
            NOT EXISTS (
            SELECT  1
            FROM    almoxarifado.lancamento_material_estorno
            WHERE   lancamento_material.cod_lancamento  = lancamento_material_estorno.cod_lancamento
            AND     lancamento_material.cod_almoxarifado= lancamento_material_estorno.cod_almoxarifado
            AND     lancamento_material.cod_item        = lancamento_material_estorno.cod_item
            AND     lancamento_material.cod_marca       = lancamento_material_estorno.cod_marca
            AND     lancamento_material.cod_centro      = lancamento_material_estorno.cod_centro
            )
        )
        AND
            (
              SELECT  sum(lancamento_material2.quantidade)
                FROM  almoxarifado.lancamento_material as lancamento_material2
               WHERE  lancamento_material.cod_almoxarifado = lancamento_material2.cod_almoxarifado
                 AND  lancamento_material.cod_item         = lancamento_material2.cod_item
                 AND  lancamento_material.cod_marca        = lancamento_material2.cod_marca
                 AND  lancamento_material.cod_centro       = lancamento_material2.cod_centro

".( $this->getDado('stCodAlmoxarifado') ? " AND lancamento_material2.cod_almoxarifado in (".$this->getDado('stCodAlmoxarifado').")"  :"" )."
".( $this->getDado('inCodItem')         ? " AND lancamento_material2.cod_item = ".$this->getDado('inCodItem')  :"" )."
".( $this->getDado('inCodMarca')        ? " AND lancamento_material2.cod_marca = ".$this->getDado('inCodMarca')  :"" )."
".( $this->getDado('inCodCentro')       ? " AND lancamento_material2.cod_centro = ".$this->getDado('inCodCentro')  :"" )."
            ) > 0



     GROUP BY
                  natureza_lancamento.exercicio_lancamento
               ,  natureza_lancamento.num_lancamento
               ,  to_char(natureza_lancamento.timestamp,'dd/mm/yyyy')
               ,  natureza_lancamento.cod_natureza
               ,  natureza_lancamento.tipo_natureza
               ,  natureza_lancamento.cgm_almoxarife
               --,  nom_cgm
               ,  almoxarifado.cod_almoxarifado
               ,  almoxarifado.cgm_almoxarifado
               --,  nom_cgm
               ,  nota_fiscal_fornecedor_ordem.exercicio
               ,  nota_fiscal_fornecedor_ordem.cod_entidade
               ,  nota_fiscal_fornecedor_ordem.cod_ordem
               ,  ordem.exercicio_empenho
               ,  ordem.cod_empenho

     ORDER BY  to_char(natureza_lancamento.timestamp,'dd/mm/yyyy') DESC ";

    return $stSql;
}

function listarItens(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaListarItens",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaListarItens()
{
    $stSql = "
SELECT   lm.cod_almoxarifado
        ,lm.cod_item
        ,lm.cod_marca
        ,lm.cod_centro
        ,ci.descricao_resumida
        ,case when ci.cod_tipo = 2 then true else false end as perecivel
        ,um.nom_unidade
        ,sum(lm.quantidade) as quantidade
        ,lm.quantidade * (lm.valor_mercado / lm.quantidade) as valor
        ,lm.valor_mercado / lm.quantidade as valor_unitario
        ,(
            SELECT  sum(lm2.quantidade)
              FROM  almoxarifado.lancamento_material as lm2
             WHERE  lm.cod_item         = lm2.cod_item
               AND  lm.cod_almoxarifado = lm2.cod_almoxarifado
               AND  lm.cod_marca        = lm2.cod_marca
               AND  lm.cod_centro       = lm2.cod_centro
               AND  lm2.exercicio_lancamento = '".$this->getDado('stExercicio')."'
        ) as saldo
        ,(
            SELECT  sum(lm2.quantidade)
              FROM  almoxarifado.lancamento_material as lm2
             WHERE  lm.cod_item         = lm2.cod_item
               AND  lm.cod_almoxarifado = lm2.cod_almoxarifado
               AND  lm.cod_marca        = lm2.cod_marca
               AND  lm.cod_centro       = lm2.cod_centro
               AND  lm2.exercicio_lancamento = '".$this->getDado('stExercicio')."'
               AND
                (
                    (       lm2.cod_natureza         = ".$this->getDado('inCodNatureza')."
                        AND lm2.tipo_natureza        = '".$this->getDado('stTipoNatureza')."'
                    )
                    OR
                    (       lm2.cod_natureza         = 10
                        AND lm2.tipo_natureza        = 'S'
                    )
                )
        ) as saldo_lancamento
FROM     almoxarifado.lancamento_material   as lm
        ,almoxarifado.catalogo_item         as ci
        ,administracao.unidade_medida       as um

WHERE  lm.cod_item             = ci.cod_item
  AND  ci.cod_grandeza         = um.cod_grandeza
  AND  ci.cod_unidade          = um.cod_unidade
  AND  lm.cod_almoxarifado     = ".$this->getDado('inCodAlmoxarifado')."
  AND  lm.exercicio_lancamento = '".$this->getDado('stExercicio')."'
  AND  lm.num_lancamento       = ".$this->getDado('inNumLancamento')."
  AND  lm.cod_natureza         = ".$this->getDado('inCodNatureza')."
  AND  lm.tipo_natureza        = '".$this->getDado('stTipoNatureza')."'

--  Implementado o teste usando Having para melhorar a performace.

--  AND
--  (
--    SELECT  sum(lm2.quantidade)
--      FROM  almoxarifado.lancamento_material as lm2
--     WHERE  lm.cod_almoxarifado = lm2.cod_almoxarifado
--       AND  lm.cod_item         = lm2.cod_item
--       AND  lm.cod_marca        = lm2.cod_marca
--       AND  lm.cod_centro       = lm2.cod_centro
--  ) > 0

GROUP BY  lm.cod_almoxarifado
       ,  lm.cod_item
       ,  ci.descricao_resumida
       ,  ci.cod_tipo
       ,  um.nom_unidade
       ,  lm.quantidade
       ,  lm.valor_mercado
       ,  lm.cod_marca
       ,  lm.cod_centro

  HAVING
  (
    SELECT  sum(lm2.quantidade)
      FROM  almoxarifado.lancamento_material as lm2
     WHERE  lm.cod_almoxarifado = lm2.cod_almoxarifado
       AND  lm.cod_item         = lm2.cod_item
       AND  lm.cod_marca        = lm2.cod_marca
       AND  lm.cod_centro       = lm2.cod_centro
  ) > 0

ORDER BY lm.cod_item

";

    return $stSql;
}

function listarSaldoEstornoLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaListarSaldoEstornoLancamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaListarSaldoEstornoLancamento()
{
    $stSql  = "    SELECT SUM(quantidade) * -1 AS saldo_estornado                                                                \n";
    $stSql .= "      FROM almoxarifado.lancamento_material                                                                       \n";
    $stSql .= "INNER JOIN (                                                                                                      \n";
    $stSql .= "                SELECT DISTINCT lancamento_material_estorno.cod_lancamento_estorno                                \n";
    $stSql .= "                  FROM almoxarifado.lancamento_material                                                           \n";
    $stSql .= "            INNER JOIN almoxarifado.lancamento_material_estorno                                                   \n";
    $stSql .= "                    ON lancamento_material.cod_item         = lancamento_material_estorno.cod_item                \n";
    $stSql .= "                   AND lancamento_material.cod_almoxarifado = lancamento_material_estorno.cod_almoxarifado        \n";
    $stSql .= "                   AND lancamento_material.cod_marca        = lancamento_material_estorno.cod_marca               \n";
    $stSql .= "                   AND lancamento_material.cod_centro       = lancamento_material_estorno.cod_centro              \n";
    $stSql .= "                   AND lancamento_material.cod_lancamento   = lancamento_material_estorno.cod_lancamento          \n";
    $stSql .= "                 WHERE 1 = 1                                                                                      \n";
    if ( $this->getDado('inNumLancamento') ) {
        $stSql .= "                   AND lancamento_material.num_lancamento   = ".$this->getDado('inNumLancamento')."               \n";
    }
    if ( $this->getDado('inCodItem') ) {
        $stSql .= "                   AND lancamento_material.cod_item         = ".$this->getDado('inCodItem')."                     \n";
    }
    if ( $this->getDado('inCodAlmoxarifado') ) {
        $stSql .= "                   AND lancamento_material.cod_almoxarifado = ".$this->getDado('inCodAlmoxarifado')."             \n";
    }
    if ( $this->getDado('inCodMarca') ) {
        $stSql .= "                   AND lancamento_material.cod_marca        = ".$this->getDado('inCodMarca')."                    \n";
    }
    if ( $this->getDado('inCodCentro') ) {
        $stSql .= "                   AND lancamento_material.cod_centro       = ".$this->getDado('inCodCentro')."                    \n";
    }
    $stSql .= "           ) as lm                                                                                            \n";
    $stSql .= "        ON lm.cod_lancamento_estorno         = lancamento_material.cod_lancamento                             \n";
    $stSql .= "     WHERE lancamento_material.tipo_natureza = 'S'                                                            \n";
    $stSql .= "       AND lancamento_material.cod_natureza  = 10                                                             \n";

    return $stSql;
}

function listarMarcaCentro(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaListarMarcaCentro",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaListarMarcaCentro()
{
    $stSql = "
       SELECT   lm.cod_marca
                ,ma.descricao as desc_marca
        ".( !$this->getDado('inCodMarca')?"":"
                ,lm.cod_centro
                ,ce.descricao as desc_centro
        " )."
                ,sum(lm.quantidade) as quantidade
                ,
                 COALESCE
                 (
                     SUM
                     (
                         (
                             SELECT  lm.quantidade * -1
                               FROM  almoxarifado.lancamento_material_estorno
                              WHERE  lancamento_material_estorno.cod_almoxarifado       = lm.cod_almoxarifado
                                AND  lancamento_material_estorno.cod_item               = lm.cod_item
                                AND  lancamento_material_estorno.cod_marca              = lm.cod_marca
                                AND  lancamento_material_estorno.cod_centro             = lm.cod_centro
                                AND  lancamento_material_estorno.cod_lancamento_estorno = lm.cod_lancamento
                                AND  lm.cod_natureza								    = 10
                                AND  lm.tipo_natureza							        = 'S'
                         )
                     ) , 0
                 ) as saldo_estornado

        FROM     almoxarifado.lancamento_material   as lm
                ,almoxarifado.marca  as ma
        ".( !$this->getDado('inCodMarca')?"":"
                ,almoxarifado.centro_custo as ce
        " )."

       WHERE   lm.cod_marca = ma.cod_marca
        ".( !$this->getDado('inCodMarca') ? "" : " AND lm.cod_centro = ce.cod_centro " )."
         AND   lm.cod_almoxarifado = ".$this->getDado('inCodAlmoxarifado')."
         AND   lm.cod_item         = ".$this->getDado('inCodItem')."
        ".( $this->getDado('inCodMarca')      ? " AND  lm.cod_marca = ".$this->getDado('inCodMarca')   : "" )."
        ".( $this->getDado('inCodCentro')     ? " AND  lm.cod_centro = ".$this->getDado('inCodCentro') : "" )."
        ".( $this->getDado('inNumLancamento') ? " AND lm.num_lancamento = ".$this->getDado('inNumLancamento') : "")."

    --	 AND
    --	   (
    --	       SELECT  sum(lm2.quantidade)
    --			 FROM  almoxarifado.lancamento_material as lm2
    --			WHERE  lm.cod_almoxarifado = lm2.cod_almoxarifado
    --			  AND  lm.cod_item         = lm2.cod_item
    --			  AND  lm.cod_marca        = lm2.cod_marca
    --			  AND  lm.cod_centro       = lm2.cod_centro
    --	    ) > 0

     GROUP BY  lm.cod_marca
            ,  ma.descricao
            ".( !$this->getDado('inCodMarca') ? "" : "
            ,  lm.cod_centro
            ,  ce.descricao " )."
            ,  lm.cod_almoxarifado
            ,  lm.cod_item
            ,  lm.cod_marca
            ,  lm.cod_centro

       HAVING
            (
              SELECT  sum(lm2.quantidade)
                FROM  almoxarifado.lancamento_material as lm2
               WHERE  lm.cod_almoxarifado = lm2.cod_almoxarifado
                 AND  lm.cod_item         = lm2.cod_item
                 AND  lm.cod_marca        = lm2.cod_marca
                 AND  lm.cod_centro       = lm2.cod_centro
            ) > 0

     ORDER BY  lm.cod_marca ";

    return $stSql;
}

function listarAtributosValores(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaListarAtributosValores",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaListarAtributosValores()
{
    $stSql = "
       SELECT AD.nao_nulo
                  , AD.nom_atributo
                  , AD.cod_atributo
                  , AD.cod_cadastro
                  , AD.cod_modulo
                  , administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'') as valor_padrao
                  , CASE TA.cod_tipo
                               WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))
                               WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))
                               ELSE null
                    END AS valor_padrao_desc
                  , CASE TA.cod_tipo
                               WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'')
                               ELSE  null
                     END AS valor_desc
                  , AD.ajuda
                  , AD.mascara
                  , TA.nom_tipo
                  , TA.cod_tipo
                  , AV.valor
               FROM administracao.atributo_dinamico AS AD
                  , administracao.tipo_atributo AS TA
                  , almoxarifado.atributo_catalogo_item AS AC
                  , almoxarifado.atributo_estoque_material_valor as AV
                  , almoxarifado.lancamento_material as LM
              WHERE AD.cod_tipo = TA.cod_tipo
                AND AD.cod_modulo   = 29
                AND AD.ativo = 't'
                AND AC.ativo = 't'
                AND AD.cod_atributo = ac.cod_atributo
                AND AD.cod_cadastro = ac.cod_cadastro
                AND AD.cod_modulo = ac.cod_modulo
                AND AC.cod_modulo   = AV.cod_modulo
                AND AC.cod_cadastro = AV.cod_cadastro
                AND AC.cod_atributo = AV.cod_atributo
                AND AC.cod_item     = AV.cod_item
                AND AV.cod_lancamento   = LM.cod_lancamento
                AND AV.cod_almoxarifado = LM.cod_almoxarifado
                AND AV.cod_item         = LM.cod_item
                AND AV.cod_marca        = LM.cod_marca
                AND AV.cod_centro       = LM.cod_centro

                AND AV.cod_centro   = ".$this->getDado('inCodCentro')."
                AND AV.cod_marca    = ".$this->getDado('inCodMarca')."
                AND AV.cod_item     = ".$this->getDado('inCodItem')."
                AND AV.cod_almoxarifado = ".$this->getDado('inCodAlmoxarifado')."

                AND LM.exercicio_lancamento = '".$this->getDado('stExercicio')."'
                AND LM.num_lancamento       = ".$this->getDado('inNumLancamento')."
                AND LM.tipo_natureza        = '".$this->getDado('stTipoNatureza')."'
                AND LM.cod_natureza         = ".$this->getDado('inCodNatureza')."
";

    return $stSql;
}

function listarPereciveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaListarPereciveis",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaListarPereciveis()
{
    $stSql = "
SELECT   lm.*
        ,lp.lote
        ,to_char(pe.dt_fabricacao,'dd/mm/yyyy') as dt_fabricacao
        ,to_char(pe.dt_validade,'dd/mm/yyyy') as dt_validade
        , ( SELECT  sum(lm2.quantidade)
            FROM     almoxarifado.lancamento_material   as lm2
                    ,almoxarifado.lancamento_perecivel  as lp2
            WHERE   lm2.cod_lancamento   = lp2.cod_lancamento
            AND     lm2.cod_almoxarifado = lp2.cod_almoxarifado
            AND     lm2.cod_item         = lp2.cod_item
            AND     lm2.cod_marca        = lp2.cod_marca
            AND     lm2.cod_centro       = lp2.cod_centro
            AND     lp2.lote             = lp.lote
            AND     lp2.cod_almoxarifado = lp.cod_almoxarifado
            AND     lp2.cod_item         = lp.cod_item
            AND     lp2.cod_marca        = lp.cod_marca
            AND     lp2.cod_centro       = lp.cod_centro
            ) as saldo
FROM     almoxarifado.lancamento_material   as lm
        ,almoxarifado.lancamento_perecivel  as lp
        ,almoxarifado.perecivel             as pe
WHERE   lm.cod_lancamento   = lp.cod_lancamento
AND     lm.cod_almoxarifado = lp.cod_almoxarifado
AND     lm.cod_item         = lp.cod_item
AND     lm.cod_marca        = lp.cod_marca
AND     lm.cod_centro       = lp.cod_centro
AND     lp.lote             = pe.lote
AND     lp.cod_almoxarifado = pe.cod_almoxarifado
AND     lp.cod_item         = pe.cod_item
AND     lp.cod_marca        = pe.cod_marca
AND     lp.cod_centro       = pe.cod_centro

AND     lm.exercicio_lancamento = '".$this->getDado('stExercicio')."'
AND     lm.num_lancamento = ".$this->getDado('inNumLancamento')."
AND     lm.cod_natureza = ".$this->getDado('inCodNatureza')."
AND     lm.tipo_natureza = '".$this->getDado('stTipoNatureza')."'
AND     lm.cod_almoxarifado = ".$this->getDado('inCodAlmoxarifado')."
AND     lm.cod_item = ".$this->getDado('inCodItem')."
AND     lm.cod_marca = ".$this->getDado('inCodMarca')."
AND     lm.cod_centro = ".$this->getDado('inCodCentro')."
";

    return $stSql;
}

}
