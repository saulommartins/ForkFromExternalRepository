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

    * Classe de mapeamento da tabela tcemg.conta_bancaria
    * Data de Criação   : 14/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: TTCEMGContaBancaria.class.php 59834 2014-09-15 14:22:10Z lisiane $
    *
    * $Revision: 59834 $
    * $Author: lisiane $
    * $Date: 2014-09-15 11:22:10 -0300 (Mon, 15 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

class TTCEMGContaBancaria extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGContaBancaria()
    {
        parent::Persistente();
        $this->setTabela('tcemg.conta_bancaria');
        $this->setComplementoChave('cod_conta, exercicio');

        $this->AddCampo('cod_conta','integer',true,'',true,true);
        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('cod_entidade','varchar',true,'',false,false);
        $this->AddCampo('sequencia','varchar',true,'',false,false);
        $this->AddCampo('cod_tipo_aplicacao','integer',true,'',false,false);
        $this->AddCampo('cod_ctb_anterior','integer',true,'',false,false);
    }

        function recuperaPlanoContaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
        {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            if(trim($stOrdem))
                $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            $stSql = $this->montaRecuperaPlanoContaAnalitica().$stCondicao.$stOrdem;
            $this->setDebug( $stSql );
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
        }

        /**
    * Seta dados para fazer o recuperaRelacionamentoPlanoContaAnalitica
    * @access Public
    * @return String $stSql
*/
    public function montaRecuperaPlanoContaAnalitica()
    {
        $stSQL  = "
            SELECT    pc.cod_estrutural
                    , pc.exercicio
                    , pc.nom_conta
                    , pc.cod_conta
                    , publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido
                    , tabela.vl_lancamento
                    , pa.cod_plano
                    , tabela.sequencia
                    , pa.natureza_saldo
                    , cb.cod_tipo_aplicacao
                    , cb.cod_ctb_anterior
                    , banco.num_banco AS num_banco
                    , agencia.num_agencia 
                    , conta_corrente.num_conta_corrente 
                      
               FROM contabilidade.plano_conta as pc

         INNER JOIN contabilidade.plano_analitica as pa
                 ON pc.cod_conta = pa.cod_conta
                AND pc.exercicio = pa.exercicio

         INNER JOIN contabilidade.plano_banco as pb
                 ON pb.cod_plano = pa.cod_plano
                AND pb.exercicio = pa.exercicio
                
               JOIN monetario.banco
                 ON banco.cod_banco = pb.cod_banco
                  
               JOIN monetario.agencia
                 ON agencia.cod_agencia = pb.cod_agencia
                AND agencia.cod_banco = pb.cod_banco
                  
               JOIN monetario.conta_corrente
                 ON conta_corrente.cod_banco =pb.cod_banco
                AND conta_corrente.cod_conta_corrente = pb.cod_conta_corrente
               
          LEFT JOIN tcemg.conta_bancaria as cb
                 ON  cb.cod_conta = pc.cod_conta
                AND cb.exercicio = pc.exercicio
                
          LEFT JOIN (
                      SELECT CASE WHEN pac.cod_plano IS NOT NULL  THEN pac.cod_plano  ELSE pad.cod_plano END AS cod_plano
                              , CASE WHEN cc.cod_entidade IS NOT NULL THEN cc.cod_entidade ELSE cd.cod_entidade END AS cod_entidade
                              , CASE WHEN vlc.vl_lancamento IS NOT NULL  THEN vlc.vl_lancamento ELSE vld.vl_lancamento  END AS vl_lancamento
                              , CASE WHEN vlc.sequencia IS NOT NULL THEN vlc.sequencia ELSE vld.sequencia END AS sequencia
                              , CASE WHEN cc.cod_entidade IS NOT NULL THEN cc.cod_entidade ELSE cd.cod_entidade END AS cod_entidade
                              , CASE WHEN cc.exercicio IS NOT NULL THEN cc.exercicio  ELSE cd.exercicio END AS exercicio
                        FROM contabilidade.plano_analitica as pad

                   LEFT JOIN contabilidade.conta_debito as cd
                          ON (     pad.cod_plano = cd.cod_plano
                               AND pad.exercicio = cd.exercicio
                               AND cd.tipo = 'I'
                               AND cd.cod_lote = 1
                               AND cd.cod_entidade = ".$this->getDado('cod_entidade')."
                            )

                   LEFT JOIN  contabilidade.valor_lancamento as vld
                          ON (     cd.cod_lote = vld.cod_lote
                               AND cd.tipo = vld.tipo
                               AND cd.sequencia = vld.sequencia
                               AND cd.exercicio = vld.exercicio
                               AND cd.tipo_valor = vld.tipo_valor
                               AND cd.cod_entidade = vld.cod_entidade
                            )
                               , contabilidade.plano_analitica as pac
                   LEFT JOIN contabilidade.conta_credito as cc
                          ON (     pac.cod_plano = cc.cod_plano
                               AND pac.exercicio = cc.exercicio
                               AND cc.tipo = 'I'
                               AND cc.cod_lote = 1
                               AND cc.cod_entidade = ".$this->getDado('cod_entidade')."
                            )

                   LEFT JOIN contabilidade.valor_lancamento as vlc
                          ON (     cc.cod_lote = vlc.cod_lote
                               AND cc.tipo = vlc.tipo
                               AND cc.sequencia = vlc.sequencia
                               AND cc.exercicio = vlc.exercicio
                               AND cc.tipo_valor = vlc.tipo_valor
                               AND cc.cod_entidade = vlc.cod_entidade
                           )

                        WHERE   pad.cod_plano = pac.cod_plano
                          AND     pad.exercicio = pac.exercicio
                          AND     ( cc.cod_entidade = ".$this->getDado('cod_entidade')." OR cd.cod_entidade = ".$this->getDado('cod_entidade').")
            ) AS tabela
            
            ON (  pa.cod_plano = tabela.cod_plano AND pa.exercicio = tabela.exercicio
            )

        WHERE pc.exercicio    = '".$this->getDado('exercicio')."'
          AND pb.cod_entidade = ".$this->getDado('cod_entidade');
        
        if($this->getDado('cod_entidade')!=3){
            $stSQL .= " AND (pc.cod_estrutural like '1.1.1.1.1%' OR pc.cod_estrutural like '1.1.4%')";
        }else{
            $stSQL .= " AND (pc.cod_estrutural like '1.1.1.1.1.50%' OR pc.cod_estrutural like '1.1.4%')";    
        }
        
        return $stSQL;
    }
    
    public function __destruct(){}

}
