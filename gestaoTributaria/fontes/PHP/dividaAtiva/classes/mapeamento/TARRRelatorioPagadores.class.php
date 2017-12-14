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
    * Classe de mapeamento do relatorio de Pagadores
    * Data de criação : 10/11/2015
    * @author Analista: Luciana Dellay
    * @author Programador: Evandro Melos
    * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRRelatorioPagadores extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function __construct() {
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
    
    function consultaPorGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "") 
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaConsultaPorGrupo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaConsultaPorGrupo() 
    {
        $stSql = "
            SELECT  divida.numcgm
                    , divida.nom_cgm
                    , inscricao
                    , descricao        
                    , codigo
                    , ano_exercicio
                    , SUM(parcela.vlr_parcela) as valor
             FROM divida.parcela
             INNER JOIN (
                       SELECT 
                              divida_parcelamento.num_parcelamento
                              , sw_cgm.numcgm
                              , sw_cgm.nom_cgm 
                              , COALESCE( divida_imovel.inscricao_municipal, divida_empresa.inscricao_economica ) AS inscricao
                              , origem.descricao                   
                              , origem.ano_exercicio                   
                              , origem.cod_grupo||'/'||origem.ano_exercicio as codigo
                         FROM divida.divida_parcelamento
               
                   INNER JOIN divida.divida_ativa
                           ON divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                          AND divida_ativa.exercicio     = divida_parcelamento.exercicio
                    LEFT JOIN divida.divida_imovel
                           ON divida_imovel.cod_inscricao = divida_ativa.cod_inscricao
                          AND divida_imovel.exercicio     = divida_ativa.exercicio        
                    LEFT JOIN divida.divida_empresa
                           ON divida_empresa.cod_inscricao = divida_ativa.cod_inscricao
                          AND divida_empresa.exercicio = divida_ativa.exercicio
                   INNER JOIN divida.parcelamento
                           ON parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
                   INNER JOIN divida.parcela
                           ON parcela.num_parcelamento = parcelamento.num_parcelamento
                   INNER JOIN divida.divida_cgm
                           ON divida_cgm.cod_inscricao  = divida_ativa.cod_inscricao
                          AND divida_cgm.exercicio      = divida_ativa.exercicio
                   INNER JOIN sw_cgm 
                           ON sw_cgm.numcgm = divida_cgm.numcgm
                   INNER JOIN divida.parcela_origem
                           ON parcela_origem.num_parcelamento = parcelamento.num_parcelamento
                   INNER JOIN ( SELECT  parcela.cod_parcela
                                        , grupo_credito.descricao
                                        , grupo_credito.ano_exercicio
                                        , grupo_credito.cod_grupo
                                    FROM arrecadacao.parcela
                                    
                                    INNER JOIN arrecadacao.lancamento_calculo
                                        ON lancamento_calculo.cod_lancamento = parcela.cod_lancamento
                                    
                                    INNER JOIN arrecadacao.calculo_grupo_credito
                                        ON calculo_grupo_credito.cod_calculo   = lancamento_calculo.cod_calculo
                                    
                                    INNER JOIN arrecadacao.grupo_credito
                                        ON grupo_credito.cod_grupo     = calculo_grupo_credito.cod_grupo
                                       AND grupo_credito.ano_exercicio = calculo_grupo_credito.ano_exercicio 
                                    
                                    WHERE calculo_grupo_credito.cod_grupo     = ".$this->getDado('cod_grupo')."
                                      AND calculo_grupo_credito.ano_exercicio = '".$this->getDado('exercicio')."'
                                    
                                    GROUP BY parcela.cod_parcela
                                             ,grupo_credito.descricao
                                             ,grupo_credito.ano_exercicio
                                             ,grupo_credito.cod_grupo
                        ) AS origem
                        ON origem.cod_parcela = parcela_origem.cod_parcela
                        
                        WHERE parcela.paga = TRUE
                        
                        GROUP BY divida_parcelamento.num_parcelamento
                                 , sw_cgm.numcgm
                                 , sw_cgm.nom_cgm          
                                 , divida_imovel.inscricao_municipal
                                 , divida_empresa.inscricao_economica                 
                                 , origem.descricao      
                                 , origem.ano_exercicio
                                 , origem.cod_grupo   
            ) AS divida
               ON divida.num_parcelamento = parcela.num_parcelamento
            
            WHERE parcela.paga = TRUE
            
            GROUP BY divida.numcgm
                     , divida.nom_cgm
                     , descricao
                     , inscricao
                     , codigo
                     , ano_exercicio
            ORDER BY inscricao
            ";
        
        if($this->getDado('limite') != 0) {
            $stSql.= " LIMIT ".$this->getDado('limite');
        }
    
        return $stSql;
    }
    
    
    
    function consultaPorCredito(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "") 
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaConsultaPorCredito().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }


    function montaConsultaPorCredito() 
    {
        $stSql = "
            SELECT  divida.numcgm
                    , divida.nom_cgm
                    , inscricao
                    , descricao        
                    , codigo
                    , ano_exercicio
                    , SUM(parcela.vlr_parcela) as valor
             FROM divida.parcela
             INNER JOIN (
                       SELECT 
                              divida_parcelamento.num_parcelamento
                              , sw_cgm.numcgm
                              , sw_cgm.nom_cgm 
                              , COALESCE( divida_imovel.inscricao_municipal, divida_empresa.inscricao_economica ) AS inscricao
                              , origem.descricao                   
                              , origem.ano_exercicio                   
                              , origem.cod_grupo||'/'||origem.ano_exercicio as codigo
                         FROM divida.divida_parcelamento
               
                   INNER JOIN divida.divida_ativa
                           ON divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                          AND divida_ativa.exercicio     = divida_parcelamento.exercicio
                    LEFT JOIN divida.divida_imovel
                           ON divida_imovel.cod_inscricao = divida_ativa.cod_inscricao
                          AND divida_imovel.exercicio     = divida_ativa.exercicio        
                    LEFT JOIN divida.divida_empresa
                           ON divida_empresa.cod_inscricao = divida_ativa.cod_inscricao
                          AND divida_empresa.exercicio = divida_ativa.exercicio
                   INNER JOIN divida.parcelamento
                           ON parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
                   INNER JOIN divida.parcela
                           ON parcela.num_parcelamento = parcelamento.num_parcelamento
                   INNER JOIN divida.divida_cgm
                           ON divida_cgm.cod_inscricao  = divida_ativa.cod_inscricao
                          AND divida_cgm.exercicio      = divida_ativa.exercicio
                   INNER JOIN sw_cgm 
                           ON sw_cgm.numcgm = divida_cgm.numcgm
                   INNER JOIN divida.parcela_origem
                           ON parcela_origem.num_parcelamento = parcelamento.num_parcelamento
                   INNER JOIN ( SELECT    parcela.cod_parcela
                                          , credito.descricao_credito AS descricao
                                          , calculo_grupo_credito.ano_exercicio
                                          , calculo_grupo_credito.cod_grupo
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
                        ON origem.cod_parcela = parcela_origem.cod_parcela
                        
                        WHERE parcela.paga = TRUE
                        
                        GROUP BY divida_parcelamento.num_parcelamento
                                 , sw_cgm.numcgm
                                 , sw_cgm.nom_cgm          
                                 , divida_imovel.inscricao_municipal
                                 , divida_empresa.inscricao_economica                 
                                 , origem.descricao      
                                 , origem.ano_exercicio
                                 , origem.cod_grupo   
            ) AS divida
               ON divida.num_parcelamento = parcela.num_parcelamento
            
            WHERE parcela.paga = TRUE
            
            GROUP BY divida.numcgm
                     , divida.nom_cgm
                     , descricao
                     , inscricao
                     , codigo
                     , ano_exercicio
            ORDER BY inscricao
        ";
        
        if($this->getDado('limite') != 0) {
            $stSql.= " LIMIT ".$this->getDado('limite');
        }
        
        return $stSql;
    }
  
}//End of Class