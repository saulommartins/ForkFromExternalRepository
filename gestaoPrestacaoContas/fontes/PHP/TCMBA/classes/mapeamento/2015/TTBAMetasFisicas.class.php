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
    * Data de Criação: 02/07/2015

    * @author Analista: Ane Pereira
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: diego $
    $Date: 2007-10-16 01:38:47 +0000 (Ter, 16 Out 2007) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBAMetasFisicas extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTBAMetasFisicas()
    {
        parent::Persistente();
    }
    
    
    function recuperaMetasFisicas(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaMetasFisicas().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaMetasFisicas()
    {
        $stSql = "
            SELECT 1 AS tipo_registro
                 , '".$this->getDado('inCodUnidadeGestora')."' AS unidade_gestora
                 , CASE WHEN (SUBSTR(LPAD(acao.cod_acao::VARCHAR,4,'0'), 1,1 )) = '0' THEN 3
                        WHEN (SUBSTR(LPAD(acao.cod_acao::VARCHAR,4,'0'), 1,1 )) = '1' THEN 1
                        WHEN (SUBSTR(LPAD(acao.cod_acao::VARCHAR,4,'0'), 1,1 )) = '2' THEN 2
                        END AS tipo_projeto 
                 , LPAD(acao.cod_acao::VARCHAR,4,'0') AS cod_acao
                 , LPAD(acao.num_acao::VARCHAR,4,'0') AS num_acao
                 , LPAD(programa.cod_programa::VARCHAR,4,'0') AS cod_programa
                 , programa_dados.identificacao AS nom_programa
                 , programa_dados.cod_tipo_programa
                 , acao_dados.titulo
                 , acao_dados.descricao AS descricao_acao
                 , produto.cod_produto
                 , produto.descricao AS descricao_produto
                 , funcao.cod_funcao
                 , funcao.descricao AS nom_funcao
                 , subfuncao.descricao AS nom_subfuncao
                 , acao_dados.cod_unidade_medida
                 , tipo_acao.descricao AS nom_tipo_acao
                 , unidade_medida.nom_unidade AS descricao_unidade
                 , acao_dados.exercicio
    
              FROM ppa.acao
              
        INNER JOIN ppa.programa
                ON acao.cod_programa = programa.cod_programa
                
        INNER JOIN ppa.programa_dados
                ON programa.cod_programa = programa_dados.cod_programa
               AND programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados
               
        INNER JOIN ppa.programa_setorial
                ON programa.cod_setorial = programa_setorial.cod_setorial
                
        INNER JOIN ppa.macro_objetivo
                ON programa_setorial.cod_macro = macro_objetivo.cod_macro
                
        INNER JOIN ppa.ppa
                ON macro_objetivo.cod_ppa = ppa.cod_ppa
                
        INNER JOIN ppa.acao_dados
                ON acao.cod_acao                    = acao_dados.cod_acao
               AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
    
         LEFT JOIN ppa.produto
                ON acao_dados.cod_produto = produto.cod_produto
                
        INNER JOIN ppa.tipo_acao
                ON tipo_acao.cod_tipo = acao_dados.cod_tipo
                
         LEFT JOIN ppa.acao_norma
                ON acao.cod_acao = acao_norma.cod_acao
               AND acao.ultimo_timestamp_acao_dados = acao_norma.timestamp_acao_dados
               
         LEFT JOIN orcamento.funcao
                ON acao_dados.exercicio  = funcao.exercicio
               AND acao_dados.cod_funcao = funcao.cod_funcao
               
         LEFT JOIN orcamento.subfuncao
                ON acao_dados.exercicio     = subfuncao.exercicio
               AND acao_dados.cod_subfuncao = subfuncao.cod_subfuncao
               
        INNER JOIN ppa.acao_unidade_executora
                ON acao_dados.cod_acao             = acao_unidade_executora.cod_acao
               AND acao_dados.timestamp_acao_dados = acao_unidade_executora.timestamp_acao_dados
               
         LEFT JOIN ppa.acao_quantidade
                ON acao_quantidade.cod_acao             = acao_dados.cod_acao
               AND acao_quantidade.timestamp_acao_dados = acao_dados.timestamp_acao_dados
            
        INNER JOIN administracao.unidade_medida
                ON unidade_medida.cod_unidade  = acao_dados.cod_unidade_medida 
               AND unidade_medida.cod_grandeza = acao_dados.cod_grandeza
               
             WHERE '".$this->getDado('stExercicio')."' BETWEEN ppa.ano_inicio
                                                           AND ppa.ano_final
    
          GROUP BY acao.num_acao
                 , acao.cod_acao
                 , programa.cod_programa
                 , programa_dados.identificacao
                 , programa_dados.cod_tipo_programa
                 , programa_setorial.cod_setorial
                 , macro_objetivo.cod_macro
                 , acao_dados.titulo
                 , acao_dados.descricao
                 , produto.cod_produto
                 , produto.descricao 
                 , funcao.cod_funcao
                 , funcao.descricao 
                 , subfuncao.descricao
                 , tipo_acao.descricao  
                 , unidade_medida.nom_unidade
                 , acao_dados.cod_unidade_medida
                 , acao_dados.exercicio
                 , unidade_gestora ";

        return $stSql;
    }
}
