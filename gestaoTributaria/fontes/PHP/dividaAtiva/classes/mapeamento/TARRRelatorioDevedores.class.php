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
    * Classe de mapeamento da tabela ARRECADACAO.CALCULO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRRelatorioDevedores.class.php 63959 2015-11-11 17:07:46Z evandro $

* Casos de uso: uc-05.03.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ARRECADACAO.CALCULO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRRelatorioDevedores extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TARRRelatorioDevedores() {
        parent::Persistente();
        $this->setTabela('');
    
        $this->setCampoCod('cod_calculo');
        $this->setComplementoChave('');
        
        $this->AddCampo('numcgm'       , 'integer', true, ''    , true,  false);
        $this->AddCampo('nom_cgm'      , 'char'   , true, '200' , false, true);
        $this->AddCampo('divida_ativa' , 'integer', true, ''    , false, true);
        $this->AddCampo('ano_exercicio', 'char'   , true, '4'   , false, false);
        $this->AddCampo('valor'        , 'numeric', true, '14,2', false, false);
    }
    
    function montaConsultaPorGrupo() {
        $stSql = "
            SELECT final.*
                FROM (
                    SELECT cgm.numcgm 
                         , cgm.nom_cgm                 
                         , divida_ativa.inscricao
                         , divida_ativa.descricao
                         , '".$this->getDado('cod_grupo')."/".$this->getDado('exercicio')."' AS codigo
                         , divida_ativa.ano_exercicio
                         , SUM(valor) AS valor
                         
                      FROM sw_cgm AS cgm
                INNER JOIN ( SELECT divida_ativa.cod_inscricao
                                  , divida_ativa.exercicio
                                  , divida_cgm.numcgm
                                  , divida_parcelamento_minimo.num_parcelamento AS MINparc
                                  , divida_parcelamento_maximo.num_parcelamento AS MAXparc
                                  , SUM(divida_parcela_origem.valor)            AS valor
                                  , origem.descricao	
                                  , origem.ano_exercicio
                                  , COALESCE( ddi.inscricao_municipal, dde.inscricao_economica ) AS inscricao
                               FROM divida.divida_ativa
       
                         INNER JOIN divida.divida_cgm
                                 ON divida_cgm.cod_inscricao  = divida_ativa.cod_inscricao
                                AND divida_cgm.exercicio      = divida_ativa.exercicio
                                    
                         INNER JOIN ( SELECT cod_inscricao
                                           , exercicio
                                           , MIN(num_parcelamento) AS num_parcelamento
                                        FROM divida.divida_parcelamento
                                    GROUP BY cod_inscricao
                                           , exercicio
                                     ) AS divida_parcelamento_minimo
                                  ON divida_parcelamento_minimo.cod_inscricao = divida_ativa.cod_inscricao
                                 AND divida_parcelamento_minimo.exercicio     = divida_ativa.exercicio
       
                          INNER JOIN ( SELECT num_parcelamento
                                            , cod_parcela
                                            , valor
                                         FROM divida.parcela_origem
                                     ) AS divida_parcela_origem
                                  ON divida_parcela_origem.num_parcelamento = divida_parcelamento_minimo.num_parcelamento
       
                          INNER JOIN ( SELECT cod_inscricao
                                            , exercicio
                                            , MAX(num_parcelamento) AS num_parcelamento
                                         FROM divida.divida_parcelamento
                                     GROUP BY cod_inscricao
                                            , exercicio
                                        ) AS divida_parcelamento_maximo
                                  ON divida_parcelamento_maximo.cod_inscricao = divida_ativa.cod_inscricao
                                 AND divida_parcelamento_maximo.exercicio     = divida_ativa.exercicio
                                 
                           LEFT JOIN divida.divida_imovel AS ddi
                                  ON ddi.cod_inscricao = divida_ativa.cod_inscricao
                                 AND ddi.exercicio = divida_ativa.exercicio

                           LEFT JOIN divida.divida_empresa AS dde
                                  ON dde.cod_inscricao = divida_ativa.cod_inscricao
                                 AND dde.exercicio = divida_ativa.exercicio
                                 
                          INNER JOIN ( SELECT parcela.cod_parcela
                                            , grupo_credito.descricao
                                            , grupo_credito.ano_exercicio
                                         FROM arrecadacao.parcela
                                         
                                   INNER JOIN arrecadacao.lancamento_calculo
                                           ON lancamento_calculo.cod_lancamento = parcela.cod_lancamento
                                       
                                   INNER JOIN arrecadacao.calculo_grupo_credito
                                           ON calculo_grupo_credito.cod_calculo   = lancamento_calculo.cod_calculo
       
                                   INNER JOIN arrecadacao.grupo_credito
                                           ON grupo_credito.cod_grupo     = calculo_grupo_credito.cod_grupo
                                          AND grupo_credito.ano_exercicio = calculo_grupo_credito.ano_exercicio 
       
                                        WHERE calculo_grupo_credito.cod_grupo     = '".$this->getDado('cod_grupo')."'
                                          AND calculo_grupo_credito.ano_exercicio = '".$this->getDado('exercicio')."'
                                          

                                     GROUP BY parcela.cod_parcela
                                            , grupo_credito.descricao
                                            , grupo_credito.ano_exercicio
       
                                     ) AS origem
                                  ON origem.cod_parcela = divida_parcela_origem.cod_parcela
                               WHERE 1 = 1
                               
                                 AND NOT EXISTS ( SELECT 1
                                                    FROM divida.parcela
                                                   WHERE divida_parcelamento_maximo.num_parcelamento = parcela.num_parcelamento
                                                     AND paga = TRUE
                                                )
                                                 
                           GROUP BY divida_ativa.cod_inscricao
                                  , divida_ativa.exercicio
                                  , divida_cgm.numcgm
                                  , divida_parcelamento_minimo.num_parcelamento
                                  , divida_parcelamento_maximo.num_parcelamento
                                  , origem.descricao
                                  , origem.ano_exercicio
                                  , inscricao
                        ) AS divida_ativa
                      ON divida_ativa.numcgm = cgm.numcgm
       
                     WHERE 1 = 1
                       AND NOT EXISTS ( SELECT 1
                                          FROM divida.divida_cancelada
                                         WHERE cod_inscricao = divida_ativa.cod_inscricao
                                           AND exercicio     = divida_ativa.exercicio )
                                           
                       AND NOT EXISTS ( SELECT 1
                                          FROM divida.divida_estorno
                                         WHERE cod_inscricao = divida_ativa.cod_inscricao
                                           AND exercicio     = divida_ativa.exercicio )
       
                       AND NOT EXISTS ( SELECT 1
                                          FROM divida.divida_remissao
                                         WHERE cod_inscricao = divida_ativa.cod_inscricao
                                           AND exercicio     = divida_ativa.exercicio
                                      )
       
                  GROUP BY cgm.numcgm
                         , cgm.nom_cgm                       
                         , divida_ativa.descricao
                         , codigo
                         , divida_ativa.ano_exercicio
                         , divida_ativa.inscricao
              ) AS final
                 
        ORDER BY final.inscricao";
        
        if($this->getDado('limite') != 0) {
            $stSql.= " LIMIT ".$this->getDado('limite');
        }
    
        return $stSql;
    }
    
    function consultaPorGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "") {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaConsultaPorGrupo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaConsultaPorCredito() {
        $stSql = "
            SELECT final.*
                FROM (
                    SELECT cgm.numcgm 
                         , cgm.nom_cgm
                         , divida_ativa.cod_inscricao
                         , divida_ativa.inscricao
                         , divida_ativa.descricao
                         , '".$this->getDado('cod_credito').".".$this->getDado('cod_especie').".".$this->getDado('cod_genero').".".$this->getDado('cod_natureza')."' AS codigo
                         , divida_ativa.ano_exercicio
                         , SUM(valor) AS valor
                                
                    FROM sw_cgm AS cgm
                    
              INNER JOIN ( SELECT divida_ativa.cod_inscricao
                                , divida_ativa.exercicio
                                , divida_cgm.numcgm
                                , divida_parcelamento_minimo.num_parcelamento AS MINparc
                                , divida_parcelamento_maximo.num_parcelamento AS MAXparc
                                , SUM(divida_parcela_origem.valor)            AS valor
                                , origem.descricao	
                                , origem.ano_exercicio
                                , COALESCE( ddi.inscricao_municipal, dde.inscricao_economica ) AS inscricao
                             FROM divida.divida_ativa
                               
                       INNER JOIN divida.divida_cgm
                               ON divida_cgm.cod_inscricao  = divida_ativa.cod_inscricao
                              AND divida_cgm.exercicio      = divida_ativa.exercicio
                                  
                       INNER JOIN ( SELECT cod_inscricao
                                         , exercicio
                                         , MIN(num_parcelamento) AS num_parcelamento
                                      FROM divida.divida_parcelamento
                                  GROUP BY cod_inscricao
                                         , exercicio
                                   ) AS divida_parcelamento_minimo
                                ON divida_parcelamento_minimo.cod_inscricao = divida_ativa.cod_inscricao
                               AND divida_parcelamento_minimo.exercicio     = divida_ativa.exercicio
     
                        INNER JOIN ( SELECT num_parcelamento
                                          , cod_parcela
                                          , valor
                                       FROM divida.parcela_origem
                                   ) AS divida_parcela_origem
                                ON divida_parcela_origem.num_parcelamento = divida_parcelamento_minimo.num_parcelamento
     
                        INNER JOIN ( SELECT cod_inscricao
                                          , exercicio
                                          , MAX(num_parcelamento) AS num_parcelamento
                                       FROM divida.divida_parcelamento
                                   GROUP BY cod_inscricao
                                          , exercicio
                                      ) AS divida_parcelamento_maximo
                                ON divida_parcelamento_maximo.cod_inscricao = divida_ativa.cod_inscricao
                               AND divida_parcelamento_maximo.exercicio     = divida_ativa.exercicio
                               
                        LEFT JOIN divida.divida_imovel AS ddi
                               ON ddi.cod_inscricao = divida_ativa.cod_inscricao
                              AND ddi.exercicio = divida_ativa.exercicio

                          LEFT JOIN divida.divida_empresa AS dde
                                 ON dde.cod_inscricao = divida_ativa.cod_inscricao
                                AND dde.exercicio = divida_ativa.exercicio         
                        
                        INNER JOIN ( SELECT parcela.cod_parcela
                                          , credito.descricao_credito AS descricao
                                          , '".$this->getDado('exercicio')."'::VARCHAR AS ano_exercicio
                                       FROM arrecadacao.parcela
                                       
                                 INNER JOIN arrecadacao.lancamento_calculo
                                         ON lancamento_calculo.cod_lancamento = parcela.cod_lancamento
                                
                                 INNER JOIN arrecadacao.calculo_grupo_credito
                                         ON calculo_grupo_credito.cod_calculo   = lancamento_calculo.cod_calculo
     
                                 INNER JOIN arrecadacao.calculo 
                                         ON calculo.cod_calculo  = lancamento_calculo.cod_calculo
                       
                                 INNER JOIN monetario.credito
                                         ON credito.cod_credito  = calculo.cod_credito  
                                        AND credito.cod_especie  = calculo.cod_especie  
                                        AND credito.cod_genero   = calculo.cod_genero   
                                        AND credito.cod_natureza = calculo.cod_natureza 
                       
                                      WHERE calculo.cod_credito  = '".$this->getDado('cod_credito')."'
                                        AND calculo.cod_especie  = '".$this->getDado('cod_especie')."'
                                        AND calculo.cod_genero   = '".$this->getDado('cod_genero')."'
                                        AND calculo.cod_natureza = '".$this->getDado('cod_natureza')."'
                                        AND calculo.exercicio    = '".$this->getDado('exercicio')."'
     
                                    ) AS origem
                                ON origem.cod_parcela = divida_parcela_origem.cod_parcela
                             WHERE 1 = 1
                             
                               AND NOT EXISTS ( SELECT 1
                                                  FROM divida.parcela
                                                 WHERE divida_parcelamento_maximo.num_parcelamento = parcela.num_parcelamento
                                                   AND paga = TRUE
                                               )
                                               
                         GROUP BY divida_ativa.cod_inscricao
                                , divida_ativa.exercicio
                                , divida_cgm.numcgm
                                , divida_parcelamento_minimo.num_parcelamento
                                , divida_parcelamento_maximo.num_parcelamento
                                , origem.descricao
                                , origem.ano_exercicio
                                , inscricao
                      ) AS divida_ativa
                      ON divida_ativa.numcgm = cgm.numcgm
                       
                   WHERE 1 = 1
                     AND NOT EXISTS ( SELECT 1
                                        FROM divida.divida_cancelada
                                       WHERE cod_inscricao = divida_ativa.cod_inscricao
                                         AND exercicio     = divida_ativa.exercicio )
                                         
                     AND NOT EXISTS ( SELECT 1
                                        FROM divida.divida_estorno
                                       WHERE cod_inscricao = divida_ativa.cod_inscricao
                                         AND exercicio     = divida_ativa.exercicio )
                                           
                     AND NOT EXISTS ( SELECT 1
                                        FROM divida.divida_remissao
                                       WHERE cod_inscricao = divida_ativa.cod_inscricao
                                         AND exercicio     = divida_ativa.exercicio
                                    )   
                                                
                         GROUP BY cgm.numcgm
                                , cgm.nom_cgm
                                , divida_ativa.cod_inscricao
                                , divida_ativa.descricao
                                , codigo
                                , divida_ativa.ano_exercicio
                                , divida_ativa.inscricao
                     ) AS final
                     
            ORDER BY final.inscricao DESC";
        
        if($this->getDado('limite') != 0) {
            $stSql.= " LIMIT ".$this->getDado('limite');
        }
        
        return $stSql;
    }
    
    function consultaPorCredito(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "") {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaConsultaPorCredito().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
}