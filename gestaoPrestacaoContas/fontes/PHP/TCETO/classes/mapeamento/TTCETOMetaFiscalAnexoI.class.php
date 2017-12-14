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
    * Extensão da Classe de Mapeamento TTCETOMetaFiscalAnexoI
    *
    * Data de Criação: 18/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCETOMetaFiscalAnexoI.class.php 60855 2014-11-19 12:58:51Z carlos.silva $
    *
    * @ignore
    *
*/
class TTCETOMetaFiscalAnexoI extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOMetaFiscalAnexoI()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCredor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaUndGestora(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaUndGestora().$stCondicao.$stOrdem;        
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaUndGestora()
    {
        $stSql  = " SELECT PJ.cnpj AS cod_und_gestora
                      FROM orcamento.entidade
                      JOIN sw_cgm
                        ON sw_cgm.numcgm = entidade.numcgm
                      JOIN sw_cgm_pessoa_juridica AS PJ
                        ON sw_cgm.numcgm = PJ.numcgm
                     WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                       AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."";
        
        return $stSql;
    }
}
?>
