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
    * Classe de mapeamento da tabela CONTABILIDADE.LANCAMENTO_DEPRECIACAO
    * Data de Criação: 09/09/2013
    * @author Analista: Gelson
    * @author Desenvolvedor: Evandro N. Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeLancamentoDepreciacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela('contabilidade.lancamento_depreciacao');

        $this->setCampoCod('id');

        $this->AddCampo('id'                    ,'integer'  ,true,''    ,true,false);
        $this->AddCampo('exercicio'             ,'char'     ,true,'04'  ,false,true);
        $this->AddCampo('cod_entidade'          ,'integer'  ,true,''    ,false,true);
        $this->AddCampo('tipo'                  ,'char'     ,true,'01'  ,false,true);
        $this->AddCampo('cod_lote'              ,'integer'  ,true,''    ,false,true);
        $this->AddCampo('sequencia'             ,'integer'  ,true,''    ,false,true);
        $this->AddCampo('cod_depreciacao'       ,'integer'  ,true,''    ,false,true);
        $this->AddCampo('cod_bem'               ,'integer'  ,true,''    ,false,true);
        $this->AddCampo('timestamp_depreciacao' ,'timestamp',true,''    ,false,true);
        $this->AddCampo('estorno'               ,'boolean'  ,true,''    ,false,false);

    }

    public function insereLancamentoDepreciacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaInsereLancamentoDepreciacao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaInsereLancamentoDepreciacao()
    {
        
        $stSql  = " SELECT contabilidade.fn_insere_lancamentos_depreciacao(  '".$this->getDado("exercicio")."'
                                                                           , '".$this->getDado("competencia")."'
                                                                           , '".$this->getDado("cod_entidade")."'
                                                                           , ".$this->getDado("cod_historico")."
                                                                           , '".$this->getDado("tipo")."'
                                                                           , '".$this->getDado("complemento")."'
                                                                           , ".$this->getDado("estorno")."
                                                                          ); ";
        return $stSql;
    }

    public function verificaDepreciacoesAnteriores(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaverificaDepreciacoesAnteriores().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaverificaDepreciacoesAnteriores()
    {
        $stSql  = " SELECT *
                      FROM contabilidade.lancamento_depreciacao
                      
                      JOIN patrimonio.depreciacao
                        ON lancamento_depreciacao.cod_depreciacao       = depreciacao.cod_depreciacao
                       AND lancamento_depreciacao.cod_bem               = depreciacao.cod_bem
                       AND lancamento_depreciacao.timestamp_depreciacao = depreciacao.timestamp \n";
        return $stSql;
    }
    
    public function recuperaMinCompetenciaLancada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        
        $stGroup = " \n GROUP BY lancamento_depreciacao.estorno ";
        
        $stSql = $this->montaRecuperaMinCompetenciaLancada().$stFiltro.$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMinCompetenciaLancada()
    {                
        $stSql  = " SELECT MIN(depreciacao.competencia) AS min_competencia
                         , TO_CHAR(TO_DATE(MIN(depreciacao.competencia), 'YYYYMM'), 'MM/YYYY') AS min_competencia_formatada
                         , lancamento_depreciacao.estorno
                     FROM contabilidade.lancamento_depreciacao

               INNER JOIN patrimonio.depreciacao
		       ON lancamento_depreciacao.cod_depreciacao       = depreciacao.cod_depreciacao
		      AND lancamento_depreciacao.cod_bem               = depreciacao.cod_bem               
		      AND lancamento_depreciacao.timestamp_depreciacao = depreciacao.timestamp    
		";
        
        return $stSql;
    }
    
    public function recuperaMaxCompetenciaLancada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        
        $stGroup = " \n GROUP BY lancamento_depreciacao.estorno ";
        
        $stSql = $this->montaRecuperaMaxCompetenciaLancada().$stFiltro.$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMaxCompetenciaLancada()
    {

        $stSql  = " SELECT MAX(depreciacao.competencia) AS max_competencia
                         , TO_CHAR(TO_DATE(MAX(depreciacao.competencia), 'YYYYMM'), 'MM/YYYY') AS max_competencia_formatada
                         , lancamento_depreciacao.estorno
                     FROM contabilidade.lancamento_depreciacao

               INNER JOIN patrimonio.depreciacao
		       ON lancamento_depreciacao.cod_depreciacao       = depreciacao.cod_depreciacao
		      AND lancamento_depreciacao.cod_bem               = depreciacao.cod_bem               
		      AND lancamento_depreciacao.timestamp_depreciacao = depreciacao.timestamp    
		";
        
        return $stSql;
    }
    
}

?>