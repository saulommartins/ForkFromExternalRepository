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
    * Data de Criação: 03/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63479 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php";

class TTBAPAO extends TOrcamentoProjetoAtividade
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::__construct();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaDados()
{
    $stSql .= " 
              SELECT * FROM (

              SELECT 1 AS tipo_registro
                     , pao.exercicio                                                      
                     , ( SELECT valor
                              FROM administracao.configuracao_entidade
                             WHERE cod_modulo = 45
                               AND parametro = 'tceba_codigo_unidade_gestora'
                               AND cod_entidade = '".$this->getDado('entidade')."'
                        ) AS unidade_gestora
                     , orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao) as tipo
                     , acao.num_acao AS num_projatv
                     , pao.nom_pao AS descricao
                     , produto.descricao AS produto
                     , 0 AS reservado_tcm
                     , acao_dados.cod_funcao
                     , acao_dados.cod_subfuncao
                     , acao.cod_programa
                     , unidade_medida.nom_unidade AS unidade_medida
                     , REPLACE(COALESCE(meta_estimada,0.00)::VARCHAR,'.',',') AS meta

                  FROM orcamento.pao
            INNER JOIN orcamento.pao_ppa_acao
                    ON pao_ppa_acao.exercicio = pao.exercicio
                   AND pao_ppa_acao.num_pao = pao.num_pao
            INNER JOIN ppa.acao
                    ON acao.cod_acao = pao_ppa_acao.cod_acao
            INNER JOIN ppa.acao_dados
                    ON acao_dados.cod_acao = acao.cod_acao
                   AND acao_dados.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados
            INNER JOIN ppa.produto
                    ON produto.cod_produto = acao_dados.cod_produto
            INNER JOIN administracao.unidade_medida
                    ON unidade_medida.cod_unidade = acao_dados.cod_unidade_medida
                   AND unidade_medida.cod_grandeza = acao_dados.cod_grandeza

                 WHERE pao.exercicio = '".$this->getDado('exercicio')."'

              GROUP BY tipo_registro                                                    
                      ,pao.exercicio                                                   
                      ,unidade_gestora                                                        
                      ,pao.num_pao                                                        
                      ,pao.nom_pao                                                    
                      ,produto.descricao                                                 
                      ,reservado_tcm                                             
                      ,acao_dados.cod_funcao
                      ,acao_dados.cod_subfuncao
                      ,acao.cod_programa
                      ,unidade_medida.nom_unidade
                      ,meta
                      ,acao.num_acao
            ) AS tabela
        WHERE tipo <> 4 ";
    
    return $stSql;
}

}

?>