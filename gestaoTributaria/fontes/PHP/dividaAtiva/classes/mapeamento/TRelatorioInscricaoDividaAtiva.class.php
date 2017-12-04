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
    * Classe de mapeamento do Relatorio Inscricao da Divida Ativa
    * Data de Criação: 12/09/2014
    * @author Desenvolvedor: Evandro Melos
    $Id: TRelatorioInscricaoDividaAtiva.class.php 61352 2015-01-09 18:14:18Z evandro $
*/

include_once ( CLA_PERSISTENTE );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';


class TRelatorioInscricaoDividaAtiva extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TRelatorioInscricaoDividaAtiva()
    {
        parent::Persistente();

    }

    function recuperaRelatorioInscricaoDividaAtiva(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelatorioInscricaoDividaAtiva().$stCondicao.$stOrdem;
        
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaRelatorioInscricaoDividaAtiva()
    {
        if ( $this->getDado('mostrar_cgm') ) {
            $stSelect = "divida_cgm.numcgm ";
        }else{
            $stSelect = "CASE WHEN divida_imovel.inscricao_municipal > 0 THEN 
                            divida_imovel.inscricao_municipal
                        ELSE
                            divida_empresa.inscricao_economica
                        END ";
        }

        $stSql = " SELECT DISTINCT
                    ".$stSelect." AS inscricao_origem
                    , divida_ativa.exercicio
                    , credito.descricao_credito || ' / ' || COALESCE(grupo_credito.descricao, '') AS imposto
                    , divida_ativa.num_livro    AS livro
                    , divida_ativa.num_folha    AS folha
                    , divida_ativa.cod_inscricao || '/' || divida_ativa.exercicio AS ida
                    , SUM(parcela_origem.valor) AS valor_origem
                  
                     FROM divida.divida_ativa 
                      
               JOIN (SELECT MIN(divida_parcelamento.num_parcelamento) as num_parcelamento
                               , divida_parcelamento.exercicio 
                               , divida_parcelamento.cod_inscricao 
                            FROM divida.divida_parcelamento 
                        GROUP BY divida_parcelamento.exercicio 
                               , divida_parcelamento.cod_inscricao 
                         ) AS divida_parcelamento
                   ON divida_parcelamento.exercicio     = divida_ativa.exercicio
                 AND divida_parcelamento.cod_inscricao = divida_ativa.cod_inscricao
                      
                 JOIN divida.divida_cgm
                   ON divida_cgm.exercicio     = divida_ativa.exercicio
                 AND divida_cgm.cod_inscricao = divida_ativa.cod_inscricao
                      
                    JOIN divida.parcelamento
                      ON parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
                       
                    JOIN divida.parcela_origem
                      ON parcela_origem.num_parcelamento = parcelamento.num_parcelamento
                      
                LEFT JOIN monetario.credito
                       ON credito.cod_credito  = parcela_origem.cod_credito
                      AND credito.cod_natureza = parcela_origem.cod_natureza
                      AND credito.cod_genero   = parcela_origem.cod_genero
                      AND credito.cod_especie  = parcela_origem.cod_especie

                    JOIN arrecadacao.parcela
                      ON parcela.cod_parcela = parcela_origem.cod_parcela

                    JOIN arrecadacao.lancamento
                      ON lancamento.cod_lancamento = parcela.cod_lancamento
                      
               INNER JOIN arrecadacao.lancamento_calculo
                       ON lancamento_calculo.cod_lancamento = lancamento.cod_lancamento

               INNER JOIN arrecadacao.calculo
                       ON calculo.cod_calculo  = lancamento_calculo.cod_calculo

	       LEFT JOIN arrecadacao.calculo_grupo_credito
                       ON calculo.cod_calculo = calculo_grupo_credito.cod_calculo

	        LEFT JOIN arrecadacao.grupo_credito
                       ON calculo_grupo_credito.cod_grupo     = grupo_credito.cod_grupo
                      AND calculo_grupo_credito.ano_exercicio = grupo_credito.ano_exercicio
                
                LEFT JOIN divida.divida_empresa
                       ON divida_empresa.exercicio     = divida_ativa.exercicio
                      AND divida_empresa.cod_inscricao = divida_ativa.cod_inscricao
                
                LEFT JOIN divida.divida_imovel
                       ON divida_imovel.exercicio     = divida_ativa.exercicio
                      AND divida_imovel.cod_inscricao = divida_ativa.cod_inscricao
                      
               INNER JOIN divida.modalidade_vigencia
                       ON modalidade_vigencia.cod_modalidade  = parcelamento.cod_modalidade   
                      AND modalidade_vigencia.timestamp       = parcelamento.timestamp_modalidade

               INNER JOIN divida.modalidade
                       ON modalidade.cod_modalidade = modalidade_vigencia.cod_modalidade ";
                      
        return $stSql;
    }

}