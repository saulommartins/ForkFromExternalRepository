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
    * Extensão da Classe de Mapeamento TTCETORecursoVinculado
    *
    * Data de Criação: 10/11/2014
    *
    * $Id: TTCETORecursoVinculado.class.php 60695 2014-11-10 18:00:29Z evandro $
    *
    * @author: Evandro Melos
    *
*/
class TTCETORecursoVinculado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETORecursoVinculado()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaRecursoVinculado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecursoVinculado().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecursoVinculado()
    {
        $stSql  = " SELECT  (SELECT PJ.cnpj
                                FROM orcamento.entidade
                                JOIN sw_cgm
                                    ON sw_cgm.numcgm = entidade.numcgm
                                JOIN sw_cgm_pessoa_juridica AS PJ
                                    ON sw_cgm.numcgm = PJ.numcgm
                                WHERE entidade.exercicio  = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                            ) AS cod_und_gestora
                            ,'".$this->getDado('exercicio')."' AS exercicio
                            ,recurso.cod_recurso as codRecVinculado
                            ,recurso.nom_recurso as nome
                            ,recurso_direto.finalidade
                            ,recurso_direto.cod_tipo_esfera
                    FROM orcamento.recurso
                    JOIN orcamento.recurso_direto
                         ON recurso.exercicio   = recurso_direto.exercicio
                        AND recurso.cod_recurso = recurso_direto.cod_recurso
                                                                                      
             WHERE recurso.exercicio = '".$this->getDado('exercicio')."' ";
                 
        return $stSql;
    }
}
?>
