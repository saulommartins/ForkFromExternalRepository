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
    * Data de Criação: 01/07/2015

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

class TTBAProgramaPPA extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTBAProgramaPPA()
    {
        parent::Persistente();
    }
    
    
    function recuperaProgramaPPA(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaProgramaPPA().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaProgramaPPA()
    {
        $stSql = "
                   SELECT 1 AS tipo_registro
                        , '".$this->getDado('stExercicio')."' AS exercicio
                        , '".$this->getDado('inCodUnidadeGestora')."' AS unidade_gestora
                        , programa.num_programa  AS cod_programa
                        , programa_dados.identificacao AS descricao
                        , programa_dados.objetivo
                        , COALESCE(SUM(acao_recurso.valor),0.00) AS valor
                        , (SELECT ano_inicio FROM ppa.ppa WHERE '".$this->getDado('stExercicio')."' BETWEEN ano_inicio AND ano_final) AS ano_inicio
                        , (SELECT ano_final  FROM ppa.ppa WHERE '".$this->getDado('stExercicio')."' BETWEEN ano_inicio AND ano_final) AS ano_final
                        
                     FROM ppa.programa
        
               INNER JOIN ppa.acao
                       ON programa.cod_programa = acao.cod_programa
        
               INNER JOIN ppa.acao_dados
                       ON acao_dados.cod_acao = acao.cod_acao
                      AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
        
               INNER JOIN ppa.programa_dados
                       ON programa_dados.cod_programa = programa.cod_programa
                      AND programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados

               INNER JOIN ppa.acao_recurso
                       ON acao_recurso.cod_acao = acao_dados.cod_acao
                      AND acao_recurso.timestamp_acao_dados = acao_dados.timestamp_acao_dados

                    WHERE programa.ativo = TRUE
                      AND TO_CHAR(acao_dados.timestamp_acao_dados,'yyyy')
                                BETWEEN (SELECT ano_inicio FROM ppa.ppa WHERE ano_inicio <= '".$this->getDado('stExercicio')."' AND ano_final >= '".$this->getDado('stExercicio')."')
                                AND (SELECT ano_final  FROM ppa.ppa WHERE ano_inicio <= '".$this->getDado('stExercicio')."' AND ano_final >= '".$this->getDado('stExercicio')."')
                      
                 GROUP BY programa.num_programa
                         ,programa_dados.identificacao
                         ,programa_dados.objetivo
                        
                 ORDER BY programa.num_programa";
                 
        return $stSql;
    }
}
