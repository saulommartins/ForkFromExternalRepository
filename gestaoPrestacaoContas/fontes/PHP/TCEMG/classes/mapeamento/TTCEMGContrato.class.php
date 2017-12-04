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
	* Classe de mapeamento da tabela tcemg.contrato
	* Data de Criação   : 06/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGContrato.class.php 62302 2015-04-20 17:54:18Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGContrato extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGContrato()
    {
        parent::Persistente();
        $this->setTabela('tcemg.contrato');
        
        $this->setCampoCod('cod_contrato');
        $this->setComplementoChave('exercicio,cod_entidade');
        
        $this->AddCampo( 'cod_contrato'             , 'integer' , true  , ''    , true  , false );
        $this->AddCampo( 'cod_entidade'             , 'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'num_orgao'                , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'num_unidade'              , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'nro_contrato'             , 'integer' , true  , ''    , false , false );
        $this->AddCampo( 'exercicio'                , 'char'    , true  , '4'   , true  , true  );
        $this->AddCampo( 'data_assinatura'          , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'cod_modalidade_licitacao' , 'char'    , true  , '1'   , false , true  );
        $this->AddCampo( 'cod_entidade_modalidade'  , 'integer' , false , ''    , false , false );
        $this->AddCampo( 'num_orgao_modalidade'     , 'integer' , false , ''    , false , false );
        $this->AddCampo( 'num_unidade_modalidade'   , 'integer' , false , ''    , false , false );
        $this->AddCampo( 'nro_processo'             , 'numeric' , false , '5,0' , false , false );
        $this->AddCampo( 'exercicio_processo'       , 'char'    , false , '4'   , false , false );
        $this->AddCampo( 'cod_tipo_processo'        , 'char'    , false , '1'   , false , true  );
        $this->AddCampo( 'cod_objeto'               , 'char'    , true  , '1'   , false , true  );
        $this->AddCampo( 'objeto_contrato'          , 'varchar' , true  , '500' , false , false );
        $this->AddCampo( 'cod_instrumento'          , 'char'    , true  , '1'   , false , true  );
        $this->AddCampo( 'data_inicio'              , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'data_final'               , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'vl_contrato'              , 'numeric' , true  , '14,2', false , false );
        $this->AddCampo( 'fornecimento'             , 'varchar' , false , '50'  , false , false );
        $this->AddCampo( 'pagamento'                , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'execucao'                 , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'multa'                    , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'multa_inadimplemento'     , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'cod_garantia'             , 'char'    , false , '1'   , false , true  );
        $this->AddCampo( 'numcgm_contratante'       , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'data_publicacao'          , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'numcgm_publicidade'       , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'cgm_signatario'           , 'integer' , true  , ''    , false , true  );
    }

    function recuperaProximoContrato(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        $stSql = $this->montaRecuperaProximoContrato();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
        
        return $obErro;
    }

    function montaRecuperaProximoContrato()
    {
        $stSql  = " SELECT max(cod_contrato) + 1 as cod_contrato    \n";
        $stSql .= " FROM tcemg.contrato                             \n";

        return $stSql;
    }
    
    public function recuperaContrato(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaContrato()
    {
        $stSql = "SELECT 
            (''||contrato.cod_entidade||' - '||(SELECT sw_cgm.nom_cgm FROM sw_cgm WHERE sw_cgm.numcgm=entidade.numcgm))
            AS nom_entidade,
            Modalidade.descricao AS st_modalidade,
            Natureza.descricao AS st_natureza,
            Instrumento.descricao AS st_instrumento,
            contrato.*
            FROM tcemg.contrato
            INNER JOIN orcamento.entidade
            ON entidade.exercicio=contrato.exercicio
            AND entidade.cod_entidade=contrato.cod_entidade
            INNER JOIN tcemg.contrato_modalidade_licitacao AS Modalidade
            ON Modalidade.cod_modalidade_licitacao=contrato.cod_modalidade_licitacao
            INNER JOIN tcemg.contrato_objeto AS Natureza
            ON Natureza.cod_objeto=contrato.cod_objeto
            INNER JOIN tcemg.contrato_instrumento AS Instrumento 
            ON Instrumento.cod_instrumento=contrato.cod_instrumento
        ";

        return $stSql;
    }
    
    public function recuperaContratoRescisao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContratoRescisao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaContratoRescisao()
    {
        $stSql = "SELECT 
	    contrato.cod_entidade,
	    contrato.nro_contrato,
	    contrato.exercicio,
	    contrato.vl_contrato,
	    contrato.objeto_contrato,
	    contrato.cod_contrato,
	    to_char(contrato.data_assinatura, 'dd/mm/yyyy') AS data_assinatura,
	    to_char(contrato.data_inicio, 'dd/mm/yyyy') AS data_inicio,
	    to_char(contrato.data_final, 'dd/mm/yyyy') AS data_final,
	    to_char(contrato_rescisao.data_rescisao, 'dd/mm/yyyy') AS data_rescisao,
	    contrato_rescisao.valor_rescisao
            FROM tcemg.contrato
	    LEFT JOIN tcemg.contrato_rescisao
	    ON contrato_rescisao.cod_contrato=contrato.cod_contrato
	    AND contrato_rescisao.exercicio=contrato.exercicio
	    AND contrato_rescisao.cod_entidade=contrato.cod_entidade
        ";

        return $stSql;
    }
    
    

    function alteraContrato($boTransacao = false)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
    
        $stSql = $this->montaAlteraContrato();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaDML( $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaAlteraContrato()
    {
        $cod_entidade_modalidade    = ($this->getDado( 'cod_entidade_modalidade')!=''   ) ? $this->getDado('cod_entidade_modalidade')     : "NULL";
        $cod_tipo_processo          = ($this->getDado( 'cod_tipo_processo')!=''         ) ? $this->getDado('cod_tipo_processo')           : "NULL";
        $cod_garantia               = ($this->getDado( 'cod_garantia')!=''              ) ? $this->getDado('cod_garantia')                : "NULL";
        $num_orgao_modalidade       = ($this->getDado( 'num_orgao_modalidade')!=''      ) ? $this->getDado('num_orgao_modalidade')        : "NULL";
        $num_unidade_modalidade     = ($this->getDado( 'num_unidade_modalidade')!=''    ) ? $this->getDado('num_unidade_modalidade')      : "NULL";
        $nro_processo               = ($this->getDado( 'nro_processo')!=''              ) ? $this->getDado('nro_processo')                : "NULL";
        $exercicio_processo         = ($this->getDado( 'exercicio_processo')!=''        ) ? "'".$this->getDado('exercicio_processo')."'"  : "NULL";
        $fornecimento               = ($this->getDado( 'fornecimento')!=''              ) ? "'".$this->getDado('fornecimento')."'"        : "NULL";
        $pagamento                  = ($this->getDado( 'pagamento')!=''                 ) ? "'".$this->getDado('pagamento')."'"           : "NULL";
        $execucao                   = ($this->getDado( 'execucao')!=''                  ) ? "'".$this->getDado('execucao')."'"            : "NULL";
        $multa                      = ($this->getDado( 'multa')!=''                     ) ? "'".$this->getDado('multa')."'"               : "NULL";
        $multa_inadimplemento       = ($this->getDado( 'multa_inadimplemento')!=''      ) ? "'".$this->getDado('multa_inadimplemento')."'": "NULL";
        
        
        $stSql  = " UPDATE tcemg.contrato\n";
        $stSql .= " SET cod_entidade_modalidade = ".$cod_entidade_modalidade.", \n";
        $stSql .= " cod_tipo_processo = ".$cod_tipo_processo.",                 \n";
        $stSql .= " cod_garantia = ".$cod_garantia.",                           \n";
        $stSql .= " num_orgao_modalidade = ".$num_orgao_modalidade.",           \n";
        $stSql .= " num_unidade_modalidade = ".$num_unidade_modalidade.",       \n";
        $stSql .= " nro_processo = ".$nro_processo.",                           \n";
        $stSql .= " exercicio_processo = ".$exercicio_processo.",               \n";
        $stSql .= " fornecimento = ".$fornecimento.",                           \n";
        $stSql .= " pagamento = ".$pagamento.",                                 \n";
        $stSql .= " execucao = ".$execucao.",                                   \n";
        $stSql .= " multa = ".$multa.",                                         \n";
        $stSql .= " multa_inadimplemento = ".$multa_inadimplemento."            \n";
        $stSql .= " WHERE cod_contrato = ".$this->getDado('cod_contrato')."     \n";
        $stSql .= " AND exercicio = '".$this->getDado('exercicio')."'           \n";
        $stSql .= " AND cod_entidade = '".$this->getDado('cod_entidade')."'     \n";
        
        return $stSql;
    }
    
	public function __destruct(){}

}
?>
