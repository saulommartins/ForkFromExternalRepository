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
    * Extensão da Classe de Mapeamento TTCETORubricaDespesa
    *
    * Data de Criação: 29/05/2014
    *
    * @author: Evandro Melos
    *
*/
class TTCETORubricaDespesa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETORubricaDespesa()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function recuperaRubricaDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false) ? " ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRubricaDespesa().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRubricaDespesa()
    {
        $stSql  = " SELECT ( SELECT PJ.cnpj
                                FROM orcamento.entidade
                                JOIN sw_cgm
                                    ON sw_cgm.numcgm = entidade.numcgm
                                JOIN sw_cgm_pessoa_juridica AS PJ
                                    ON sw_cgm.numcgm = PJ.numcgm
                                WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = ".$this->getDado('und_gestora')." )
                            AS cod_und_gestora
                            , conta_despesa.exercicio                            
                            , REPLACE(conta_despesa.cod_estrutural,'.','') as cod_rubrica
                            , conta_despesa.descricao as especificacao
                            , orcamento.fn_tipo_conta_despesa('".$this->getDado('exercicio')."', conta_despesa.cod_estrutural) as tipo_nivel_conta
                            , publico.fn_nivel(conta_despesa.cod_estrutural) as num_nivel_conta    
                    
                    FROM orcamento.conta_despesa

                    WHERE conta_despesa.exercicio = '".$this->getDado('exercicio')."'

                    GROUP BY  cod_und_gestora                            
                            , conta_despesa.exercicio                            
                            , conta_despesa.cod_estrutural
                            , especificacao
                            , tipo_nivel_conta
                            , num_nivel_conta
                ";
        return $stSql;
    }

}
?>
