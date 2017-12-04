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

 * Classe de mapeamento

 * @package Urbem
 * @subpackage Mapeamento

 $Id:$
 
 * @author Lisiane Morais

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCETOTransferencias extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCETOTransferencias()
    {
        parent::Persistente();
        
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaTransferencias(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem)){
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        }
        
        $stSql = $this->montaRecuperaTransferencias().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTransferencias()
    {	
        $stSql = "
            SELECT cod_und_gestora
                 , ".$this->getDado('bimestre')." AS bimestre
                 , '".$this->getDado('exercicio')."' AS exercicio
                 , id_rec_vinculado
                 , RPAD(conta_contabil,17, '0') AS conta_contabil
                 , debito_credito
                 , LPAD(banco,3,'0') AS banco
                 , LPAD(REPLACE(agencia_banco,'-',''),5,'0') AS agencia_banco
                 , num_conta_corrente
                 , LPAD(cod_tipo_transferencia::VARCHAR,2,'0') AS cod_tipo_transferencia
                 , dt_transferencia
                 , numero_registro
                 , valor
                 , RPAD(descricao,255,'') AS descricao

              FROM ( SELECT ( SELECT sw_cgm_pj.cnpj
                                FROM orcamento.entidade
                          INNER JOIN sw_cgm
                                  ON sw_cgm.numcgm = entidade.numcgm
                          INNER JOIN sw_cgm_pessoa_juridica AS sw_cgm_pj
                                  ON sw_cgm.numcgm = sw_cgm_pj.numcgm
                               WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                 AND entidade.cod_entidade = '".$this->getDado('und_gestora')."'
                          ) AS cod_und_gestora
		          , recurso.cod_recurso as id_rec_vinculado
		          , REPLACE(plano_conta_debito.cod_estrutural,'.','') AS conta_contabil
		          , 'D' AS debito_credito
                          , (SELECT num_banco FROM monetario.banco WHERE banco.cod_banco = plano_banco_debito.cod_banco) AS banco
                          , (SELECT num_agencia FROM monetario.agencia WHERE agencia.cod_banco = plano_banco_debito.cod_banco 
		                AND agencia.cod_agencia = plano_banco_debito.cod_agencia) AS agencia_banco
                          , (SELECT num_conta_corrente FROM monetario.conta_corrente cc WHERE cc.cod_banco = plano_banco_debito.cod_banco 
		                AND cc.cod_agencia = plano_banco_debito.cod_agencia AND cc.cod_conta_corrente = plano_banco_debito.cod_conta_corrente) AS num_conta_corrente
		          , TO_CHAR(transferencia.dt_autenticacao, 'dd/mm/yyyy' ) AS dt_transferencia
		          , transferencia_tipo_transferencia.exercicio_empenho||(LPAD(transferencia_tipo_transferencia.cod_empenho::VARCHAR, 9,'0')) AS numero_registro
		          , transferencia_tipo_transferencia.cod_tipo_transferencia
		          , ABS(transferencia.valor)::VARCHAR AS valor
                          , transferencia.observacao as descricao
                       FROM tesouraria.transferencia

                 INNER JOIN contabilidade.lote
                         ON lote.exercicio    = transferencia.exercicio
                        AND lote.cod_entidade = transferencia.cod_entidade
                        AND lote.tipo         = transferencia.tipo
                        AND lote.cod_lote     = transferencia.cod_lote
                 
                 INNER JOIN contabilidade.valor_lancamento
                         ON valor_lancamento.exercicio    = lote.exercicio
                        AND valor_lancamento.cod_entidade = lote.cod_entidade
                        AND valor_lancamento.tipo         = lote.tipo
                        AND valor_lancamento.cod_lote     = lote.cod_lote
                 
                 INNER JOIN contabilidade.plano_analitica as plano_debito
                         ON plano_debito.cod_plano = transferencia.cod_plano_debito
                        AND plano_debito.exercicio = transferencia.exercicio
                 
                 INNER JOIN contabilidade.plano_banco as plano_banco_debito
                         ON plano_banco_debito.exercicio = plano_debito.exercicio 
                        AND plano_banco_debito.cod_plano = plano_debito.cod_plano
                 
                 INNER JOIN contabilidade.plano_conta as plano_conta_debito
                         ON plano_conta_debito.exercicio = plano_debito.exercicio 
                        AND plano_conta_debito.cod_conta = plano_debito.cod_conta
                     
                 INNER JOIN contabilidade.plano_recurso
                         ON (plano_recurso.exercicio = plano_debito.exercicio)
                        AND (plano_recurso.cod_plano = plano_debito.cod_plano) 
                 
                 INNER JOIN orcamento.recurso
                         ON recurso.exercicio   = plano_recurso.exercicio
                        AND recurso.cod_recurso = plano_recurso.cod_recurso
                        
                  LEFT JOIN tceto.transferencia_tipo_transferencia
	                 ON transferencia_tipo_transferencia.tipo         = transferencia.tipo
                        AND transferencia_tipo_transferencia.cod_lote     = transferencia.cod_lote
                        AND transferencia_tipo_transferencia.cod_entidade = transferencia.cod_entidade
                        AND transferencia_tipo_transferencia.exercicio    = transferencia.exercicio
                  
                 INNER JOIN orcamento.entidade
                         ON entidade.exercicio = transferencia.exercicio 
                        AND entidade.cod_entidade = transferencia.cod_entidade
                 
                  LEFT JOIN sw_cgm_pessoa_juridica as PJ
                         ON PJ.numcgm = entidade.numcgm
                 
                      WHERE transferencia.exercicio = '".$this->getDado('exercicio')."'
                        AND transferencia.cod_entidade IN (".$this->getDado('cod_entidade').")
                        AND valor_lancamento.tipo_valor = 'D'
                        AND transferencia.cod_tipo IN (3,4,5)
                        AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                            BETWEEN to_date('".$this->getDado('dtInicial')."', 'dd/mm/yyyy')
                                AND to_date('".$this->getDado('dtFinal')."', 'dd/mm/yyyy')

                  UNION ALL


                     SELECT ( SELECT sw_cgm_pj.cnpj
                                FROM orcamento.entidade
                          INNER JOIN sw_cgm
                                  ON sw_cgm.numcgm = entidade.numcgm
                          INNER JOIN sw_cgm_pessoa_juridica AS sw_cgm_pj
                                  ON sw_cgm.numcgm = sw_cgm_pj.numcgm
                               WHERE entidade.exercicio   = '".$this->getDado('exercicio')."'
                                 AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                          ) AS cod_und_gestora
                          , recurso.cod_recurso as id_rec_vinculado
                          , REPLACE(plano_conta_credito.cod_estrutural,'.','') AS conta_contabil
                          , 'C' AS debito_credito
                          , (SELECT num_banco FROM monetario.banco  WHERE banco.cod_banco = plano_banco_credito.cod_banco) AS banco
                          , (SELECT num_agencia FROM monetario.agencia WHERE agencia.cod_banco = plano_banco_credito.cod_banco 
                                  AND agencia.cod_agencia = plano_banco_credito.cod_agencia) AS agencia_banco
                          , (SELECT num_conta_corrente FROM monetario.conta_corrente cc WHERE cc.cod_banco = plano_banco_credito.cod_banco 
                                  AND cc.cod_agencia = plano_banco_credito.cod_agencia AND cc.cod_conta_corrente = plano_banco_credito.cod_conta_corrente)AS num_conta_corrente
                          , TO_CHAR(transferencia.dt_autenticacao, 'dd/mm/yyyy' ) AS dt_transferencia
                          , transferencia_tipo_transferencia.exercicio_empenho||(LPAD(transferencia_tipo_transferencia.cod_empenho::VARCHAR, 9,'0')) AS numero_registro
                          , transferencia_tipo_transferencia.cod_tipo_transferencia
                          , ABS(transferencia.valor)::VARCHAR AS valor
                          , transferencia.observacao as descricao
	
                       FROM tesouraria.transferencia

                 INNER JOIN contabilidade.lote
                         ON lote.exercicio    = transferencia.exercicio
                        AND lote.cod_entidade = transferencia.cod_entidade
                        AND lote.tipo         = transferencia.tipo
                        AND lote.cod_lote     = transferencia.cod_lote
                 
                 INNER JOIN contabilidade.valor_lancamento
                         ON valor_lancamento.exercicio    = lote.exercicio
                        AND valor_lancamento.cod_entidade = lote.cod_entidade
                        AND valor_lancamento.tipo         = lote.tipo
                        AND valor_lancamento.cod_lote     = lote.cod_lote
                 
                 INNER JOIN contabilidade.plano_analitica as plano_credito
                         ON plano_credito.cod_plano = transferencia.cod_plano_credito
                        AND plano_credito.exercicio = transferencia.exercicio
                 
                 INNER JOIN contabilidade.plano_banco as plano_banco_credito
                         ON plano_banco_credito.exercicio = plano_credito.exercicio 
                        AND plano_banco_credito.cod_plano = plano_credito.cod_plano
                 
                 INNER JOIN contabilidade.plano_conta as plano_conta_credito
                         ON plano_conta_credito.exercicio = plano_credito.exercicio 
                        AND plano_conta_credito.cod_conta = plano_credito.cod_conta
                        
                 INNER JOIN contabilidade.plano_recurso
                         ON (plano_recurso.exercicio = plano_credito.exercicio)
                        AND (plano_recurso.cod_plano = plano_credito.cod_plano) 
                 
                 INNER JOIN orcamento.recurso
                         ON recurso.exercicio   = plano_recurso.exercicio
                        AND recurso.cod_recurso = plano_recurso.cod_recurso
                 
                  LEFT JOIN tceto.transferencia_tipo_transferencia
	                 ON transferencia_tipo_transferencia.tipo         = transferencia.tipo
                        AND transferencia_tipo_transferencia.cod_lote     = transferencia.cod_lote
                        AND transferencia_tipo_transferencia.cod_entidade = transferencia.cod_entidade
                        AND transferencia_tipo_transferencia.exercicio    = transferencia.exercicio
        
                 INNER JOIN orcamento.entidade
                         ON entidade.exercicio = transferencia.exercicio 
                        AND entidade.cod_entidade = transferencia.cod_entidade
                 
                  LEFT JOIN sw_cgm_pessoa_juridica as PJ
                         ON PJ.numcgm = entidade.numcgm
                 
                      WHERE transferencia.exercicio = '".$this->getDado('exercicio')."'
                        AND transferencia.cod_entidade IN (".$this->getDado('cod_entidade').")
                        AND valor_lancamento.tipo_valor = 'D'
                        AND transferencia.cod_tipo IN (3,4,5)
                 
                        AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                            BETWEEN to_date('".$this->getDado('dtInicial')."', 'dd/mm/yyyy')
                                AND to_date('".$this->getDado('dtFinal')."', 'dd/mm/yyyy')  
                ) as tabela ";

        return $stSql;
	
    }

}
