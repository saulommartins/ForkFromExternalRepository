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
    * Extensão da Classe de Mapeamento TTCEALORGAO
    *
    * Data de Criação: 30/05/2014
    *
    * @author: Lisiane Morais
    * $Id: TTCEALOrgao.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
*/
class TTCEALOrgao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALOrgao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaOrgao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaOrgao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaOrgao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaOrgao()
    {
        $stSql  = "
               SELECT (SELECT PJ.cnpj
                         FROM orcamento.entidade
                   INNER JOIN sw_cgm
                           ON sw_cgm.numcgm = entidade.numcgm
                   INNER JOIN sw_cgm_pessoa_juridica AS PJ
                           ON sw_cgm.numcgm = PJ.numcgm
                        WHERE entidade.exercicio    =  '".$this->getDado('exercicio')."'
                          AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                    ) AS cod_und_gestora
                    , (SELECT LPAD(configuracao_entidade.valor,4,'0') AS valor
                         FROM administracao.configuracao_entidade 
                        WHERE configuracao_entidade.cod_modulo = 62
                          AND configuracao_entidade.exercicio ='".$this->getDado('exercicio')."'
                          AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                          AND configuracao_entidade.cod_entidade = ".$this->getDado('und_gestora')."
                    ) AS codigo_ua 
                    , ".$this->getDado('bimestre')." AS bimestre
		    , '".$this->getDado('exercicio')."' AS exercicio
                    , LPAD(num_orgao::VARCHAR, 2, '0') as cod_orgao
                    , nom_orgao as nome
                 FROM orcamento.orgao                                                                                           
                WHERE exercicio =  '".$this->getDado('exercicio')."'
             ORDER BY num_orgao ";
        return $stSql;
    }
}
?>
